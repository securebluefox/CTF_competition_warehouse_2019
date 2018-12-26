package minibtc

import (
	"bytes"
	"crypto/sha256"
	"encoding/gob"
	"fmt"
	"time"
)

type Block struct {
	TimeStamp     int64
	Transactions  []*Transaction
	PrevBlockHash []byte
	Hash          []byte
	Nonce         int
}

func NewBlock(transactions []*Transaction, prevBlockHash []byte) *Block {
	block := &Block{
		time.Now().Unix(),
		transactions,
		prevBlockHash,
		[]byte{},
		0,
	}
	pow := NewProofOfWork(block)
	block.Nonce, block.Hash = pow.Run()
	return block
}
func NewGenesisBlock(coinbase *Transaction) *Block {
	block := NewBlock([]*Transaction{coinbase}, []byte{})
	return block
}

func (b *Block) String() string {
	var buff bytes.Buffer
	fmt.Fprintf(&buff, "Timestamp: %d\n", b.TimeStamp)
	fmt.Fprintln(&buff, b.Transactions)
	fmt.Fprintf(&buff, "Prev: %x\n", b.PrevBlockHash)
	fmt.Fprintf(&buff, "Hash: %x\n", b.Hash)
	fmt.Fprintf(&buff, "Nonce: %d\n", b.Nonce)
	pow := NewProofOfWork(b)
	fmt.Fprintf(&buff, "Pow: %v\n", pow.Validate())
	return buff.String()
}

func (b *Block) Serialize() []byte {
	var result bytes.Buffer
	encoder := gob.NewEncoder(&result)
	encoder.Encode(b)
	return result.Bytes()
}

func DeserializeBlock(d []byte) *Block {
	var block Block

	decoder := gob.NewDecoder(bytes.NewReader(d))
	decoder.Decode(&block)
	return &block
}

func (b *Block) HashTransactions() []byte {
	var txHashes [][]byte
	var txHash [32]byte

	for _, tx := range b.Transactions {
		txHashes = append(txHashes, tx.ID)
	}
	txHash = sha256.Sum256(bytes.Join(txHashes, []byte{}))
	return txHash[:]
}
