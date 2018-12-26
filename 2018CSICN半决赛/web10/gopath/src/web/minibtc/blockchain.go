package minibtc

import (
	"bytes"
	"crypto/ecdsa"
	"encoding/hex"
	"errors"
	"fmt"
	"log"
	"os"

	"github.com/boltdb/bolt"
)

const (
	dbFile               = "data.bolt"
	blocksBucket         = "blocksBucket"
	genesisCoinnbaseData = "Decentralization make more people have more right"
	genesisAddress       = "ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ="
)

type BlockChain struct {
	db  *bolt.DB
	tip []byte
}

func (bc *BlockChain) Close() {
	bc.db.Close()
}
func dbExist() bool {
	_, err := os.Stat(dbFile)
	if os.IsNotExist(err) {
		return false
	}
	return true
}

func (bc *BlockChain) MineBlock(transactions []*Transaction) {
	var lastHash []byte

	for _, tx := range transactions {
		if bc.VerifyTransaction(tx) == false {
			panic("Error: invalid transaction")
		}
	}
	err := bc.db.View(func(tx *bolt.Tx) error {
		b := tx.Bucket([]byte(blocksBucket))
		lastHash = b.Get([]byte("l"))
		return nil
	})
	if err != nil {
		panic(err)
	}
	newBlock := NewBlock(transactions, lastHash)
	bc.tip = newBlock.Hash
	err = bc.db.Update(func(tx *bolt.Tx) error {
		b := tx.Bucket([]byte(blocksBucket))
		err := b.Put([]byte(newBlock.Hash), newBlock.Serialize())
		if err != nil {
			return err
		}
		err = b.Put([]byte("l"), newBlock.Hash)
		return err
	})
	if err != nil {
		panic(err)
	}
}

func CreateBlockChain(address string) *BlockChain {
	if dbExist() {
		fmt.Println("BlockChain already exists.")
		return NewBlockChain()
	}
	var tip []byte
	cbtx := NewCoinbaseTX(address, genesisCoinnbaseData)
	genesis := NewGenesisBlock(cbtx)
	tip = genesis.Hash
	db, err := bolt.Open(dbFile, 0600, nil)
	if err != nil {
		panic(err)
	}
	err = db.Update(func(tx *bolt.Tx) error {
		b, err := tx.CreateBucket([]byte(blocksBucket))
		if err != nil {
			return err
		}
		err = b.Put(genesis.Hash, genesis.Serialize())
		if err != nil {
			return err
		}
		err = b.Put([]byte("l"), genesis.Hash)
		return err
	})
	if err != nil {
		panic(err)
	}
	return &BlockChain{db, tip}
}

func NewBlockChain() *BlockChain {
	if dbExist() == false {
		return CreateBlockChain(genesisAddress)
	}

	var tip []byte
	fmt.Println("start open")
	db, err := bolt.Open("data.bolt", 0600, nil)
	fmt.Println("open success")
	if err != nil {
		panic(err)
	}
	err = db.Update(func(tx *bolt.Tx) error {
		b := tx.Bucket([]byte(blocksBucket))
		tip = b.Get([]byte("l"))
		return nil
	})
	fmt.Println("updated success")
	if err != nil {
		panic(err)
	}
	return &BlockChain{db, tip}
}

func (bc *BlockChain) Iterator() *BlockChainIterator {
	return &BlockChainIterator{bc.tip, bc.db}
}

func (bc *BlockChain) Browse() {
	i := bc.Iterator()
	for len(i.currentHash) > 0 {
		block := i.Next()
		fmt.Println(block)
	}
}

func (bc *BlockChain) FindUnspentTransactions(pubKeyHash []byte) []Transaction {
	var unspentTXs []Transaction
	spentTXOs := make(map[string][]int)
	bci := bc.Iterator()
	for len(bci.currentHash) > 0 {
		block := bci.Next()

		for _, tx := range block.Transactions {
			txID := hex.EncodeToString(tx.ID)
		Output:
			for outIdx, out := range tx.Vout {
				if spentTXOs[txID] != nil {
					for _, spentOut := range spentTXOs[txID] {
						if spentOut == outIdx {
							continue Output
						}
					}
				}
				if out.IsLockedWithKey(pubKeyHash) {
					unspentTXs = append(unspentTXs, *tx)
				}
			}
			if tx.IsCoinbase() == false {
				for _, in := range tx.Vin {
					inTxID := hex.EncodeToString(in.Txid)
					if in.UseKey(pubKeyHash) {
						spentTXOs[inTxID] = append(spentTXOs[inTxID], in.Vout)
					}
				}
			}
		}
	}
	return unspentTXs
}

func (bc *BlockChain) FindUTXO(pubKeyHash []byte) []TxOutput {
	var UTXOs []TxOutput
	unspentTXs := bc.FindUnspentTransactions(pubKeyHash)
	for _, tx := range unspentTXs {
		for _, out := range tx.Vout {
			if out.IsLockedWithKey(pubKeyHash) {
				UTXOs = append(UTXOs, out)
			}
		}
	}
	return UTXOs
}

func (bc *BlockChain) FindSpendableOutputs(pubKeyHash []byte, amount int) (int, map[string][]int) {
	var unspentOutputs = make(map[string][]int)
	unspentTXs := bc.FindUnspentTransactions(pubKeyHash)
	accumulated := 0
Work:
	for _, tx := range unspentTXs {
		txID := hex.EncodeToString(tx.ID)
		for outIdx, out := range tx.Vout {
			if out.IsLockedWithKey(pubKeyHash) && accumulated < amount {
				accumulated += out.Value
				unspentOutputs[txID] = append(unspentOutputs[txID], outIdx)
				if accumulated >= amount {
					break Work
				}
			}
		}
	}
	return accumulated, unspentOutputs
}

// FindTransaction finds a transaction by its ID
func (bc *BlockChain) FindTransaction(ID []byte) (Transaction, error) {
	bci := bc.Iterator()

	for {
		block := bci.Next()

		for _, tx := range block.Transactions {
			if bytes.Compare(tx.ID, ID) == 0 {
				return *tx, nil
			}
		}

		if len(block.PrevBlockHash) == 0 {
			break
		}
	}

	return Transaction{}, errors.New("Transaction is not found")
}

// SignTransaction signs inputs of a Transaction
func (bc *BlockChain) SignTransaction(tx *Transaction, privKey ecdsa.PrivateKey) {
	prevTXs := make(map[string]Transaction)

	for _, vin := range tx.Vin {
		prevTX, err := bc.FindTransaction(vin.Txid)
		if err != nil {
			log.Panic(err)
		}
		prevTXs[hex.EncodeToString(prevTX.ID)] = prevTX
	}

	tx.Sign(privKey, prevTXs)
}

// VerifyTransaction verifies transaction input signatures
func (bc *BlockChain) VerifyTransaction(tx *Transaction) bool {
	if tx.IsCoinbase() {
		return true
	}

	prevTXs := make(map[string]Transaction)

	for _, vin := range tx.Vin {
		prevTX, err := bc.FindTransaction(vin.Txid)
		if err != nil {
			log.Panic(err)
		}
		prevTXs[hex.EncodeToString(prevTX.ID)] = prevTX
	}

	return tx.Verify(prevTXs)
}
