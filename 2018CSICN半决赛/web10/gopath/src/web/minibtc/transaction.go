package minibtc

import (
	"bytes"
	"crypto/ecdsa"
	"crypto/elliptic"
	"crypto/rand"
	"crypto/sha256"
	"encoding/gob"
	"encoding/hex"
	"fmt"
	"log"
	"math/big"
)

const subsidy = 999999999

type Transaction struct {
	ID   []byte // 交易id
	Vin  []TxInput
	Vout []TxOutput
	Data []byte
}

func (tx *Transaction) Serialize() []byte {
	var encoded bytes.Buffer
	enc := gob.NewEncoder(&encoded)
	err := enc.Encode(tx)
	if err != nil {
		panic(err)
	}
	return encoded.Bytes()
}

func (tx *Transaction) Hash() []byte {
	var hash [32]byte
	txCopy := *tx
	txCopy.ID = []byte{}
	hash = sha256.Sum256(txCopy.Serialize())
	return hash[:]
}

func (tx *Transaction) String() string {
	var buff bytes.Buffer
	fmt.Fprintf(&buff, "Transaction %x:\n", tx.ID)
	for i, input := range tx.Vin {
		fmt.Fprintf(&buff, "  Input %d:\n", i)
		fmt.Fprintf(&buff, "    TXID:    %x\n", input.Txid)
		fmt.Fprintf(&buff, "    Out:     %d\n", input.Vout)
		fmt.Fprintf(&buff, "    Signature:  %s\n", input.Signature)
		fmt.Fprintf(&buff, "    PubKey:  %s\n", input.PubKey)
	}
	for i, output := range tx.Vout {
		fmt.Fprintf(&buff, "  Output %d:\n", i)
		fmt.Fprintf(&buff, "    Value:  %d\n", output.Value)
		fmt.Fprintf(&buff, "    PubKeyHash: %s\n", output.PubKeyHash)
	}
	fmt.Fprintf(&buff, "  Data: %s\n", tx.Data)
	return buff.String()
}

func (tx *Transaction) IsCoinbase() bool {
	return len(tx.Vin) == 1 && len(tx.Vin[0].Txid) == 0 && tx.Vin[0].Vout == -1
}

func NewCoinbaseTX(to, data string) *Transaction {
	if data == "" {
		data = fmt.Sprintf("Reward to '%s'", to)
	}
	txin := TxInput{[]byte{}, -1, nil, nil}
	txout := NewTxOutput(subsidy, to)
	tx := Transaction{[]byte{},
		[]TxInput{txin},
		[]TxOutput{*txout},
		[]byte(data),
	}
	tx.ID = tx.Hash()
	return &tx
}

func NewUTXOTransaction(from, to string, amount int, msg string, wallet *Wallet, bc *BlockChain) *Transaction {
	var inputs []TxInput
	var outputs []TxOutput
	pubKeyHash := DecodeAddress(from)

	acc, validOutputs := bc.FindSpendableOutputs(pubKeyHash, amount)
	if acc < amount {
		panic("not enough balance")
	}
	for txid, outs := range validOutputs {
		txID, err := hex.DecodeString(txid)
		if err != nil {
			panic(err)
		}
		for _, out := range outs {
			input := TxInput{txID, out, nil, wallet.PublicKey}
			inputs = append(inputs, input)
		}
	}
	outputs = append(outputs, *NewTxOutput(amount, to))
	if acc > amount {
		outputs = append(outputs, *NewTxOutput(acc-amount, from))
	}
	tx := Transaction{[]byte{}, inputs, outputs, []byte(msg)}
	tx.ID = tx.Hash()
	bc.SignTransaction(&tx, wallet.PrivateKey)
	return &tx
}

// TrimmedCopy creates a trimmed copy of Transaction to be used in signing
func (tx *Transaction) TrimmedCopy() Transaction {
	var inputs []TxInput
	var outputs []TxOutput

	for _, vin := range tx.Vin {
		inputs = append(inputs, TxInput{vin.Txid, vin.Vout, nil, nil})
	}

	for _, vout := range tx.Vout {
		outputs = append(outputs, TxOutput{vout.Value, vout.PubKeyHash})
	}

	txCopy := Transaction{tx.ID, inputs, outputs, tx.Data}

	return txCopy
}

// Sign signs each input of a Transaction
func (tx *Transaction) Sign(privKey ecdsa.PrivateKey, prevTXs map[string]Transaction) {
	if tx.IsCoinbase() {
		return
	}

	for _, vin := range tx.Vin {
		if prevTXs[hex.EncodeToString(vin.Txid)].ID == nil {
			log.Panic("ERROR: Previous transaction is not correct")
		}
	}

	txCopy := tx.TrimmedCopy()

	for inID, vin := range txCopy.Vin {
		prevTx := prevTXs[hex.EncodeToString(vin.Txid)]
		txCopy.Vin[inID].Signature = nil
		txCopy.Vin[inID].PubKey = prevTx.Vout[vin.Vout].PubKeyHash
		txCopy.ID = txCopy.Hash()
		txCopy.Vin[inID].PubKey = nil

		r, s, err := ecdsa.Sign(rand.Reader, &privKey, txCopy.ID)
		if err != nil {
			log.Panic(err)
		}
		signature := append(r.Bytes(), s.Bytes()...)

		tx.Vin[inID].Signature = signature
	}
}

// Verify verifies signatures of Transaction inputs
func (tx *Transaction) Verify(prevTXs map[string]Transaction) bool {
	if tx.IsCoinbase() {
		return true
	}

	for _, vin := range tx.Vin {
		if prevTXs[hex.EncodeToString(vin.Txid)].ID == nil {
			log.Panic("ERROR: Previous transaction is not correct")
		}
	}

	txCopy := tx.TrimmedCopy()
	curve := elliptic.P256()

	for inID, vin := range tx.Vin {
		prevTx := prevTXs[hex.EncodeToString(vin.Txid)]
		txCopy.Vin[inID].Signature = nil
		txCopy.Vin[inID].PubKey = prevTx.Vout[vin.Vout].PubKeyHash
		txCopy.ID = txCopy.Hash()
		txCopy.Vin[inID].PubKey = nil

		r := big.Int{}
		s := big.Int{}
		sigLen := len(vin.Signature)
		r.SetBytes(vin.Signature[:(sigLen / 2)])
		s.SetBytes(vin.Signature[(sigLen / 2):])

		x := big.Int{}
		y := big.Int{}
		keyLen := len(vin.PubKey)
		x.SetBytes(vin.PubKey[:(keyLen / 2)])
		y.SetBytes(vin.PubKey[(keyLen / 2):])

		rawPubKey := ecdsa.PublicKey{curve, &x, &y}
		if ecdsa.Verify(&rawPubKey, txCopy.ID, &r, &s) == false {
			return false
		}
	}

	return true
}
