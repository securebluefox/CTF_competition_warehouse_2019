package minibtc

import (
	"bytes"
	"crypto/ecdsa"
	"crypto/elliptic"
	"encoding/base32"
	"encoding/hex"
	"fmt"
	"math/big"
)

type Model struct {
}

func (self *Model) CreateWallet() string {
	wallet := NewWallet()

	privKey := wallet.PrivateKey.D.Bytes()
	privKeyStr := base32.StdEncoding.EncodeToString(privKey)
	address := wallet.GetAddress()
	return "{\"privkey\":\"" + privKeyStr + "\",\"address\":\"" + address + "\"}"
}
func (self *Model) DecodeWallet(address string, privKeyStr string) *Wallet {
	PubKey := DecodeAddress(address)
	x := big.Int{}
	y := big.Int{}
	curve := elliptic.P256()
	keyLen := len(PubKey)
	x.SetBytes(PubKey[:(keyLen / 2)])
	y.SetBytes(PubKey[(keyLen / 2):])
	rawPubKey := ecdsa.PublicKey{curve, &x, &y}
	b, err := base32.StdEncoding.DecodeString(privKeyStr)
	checkError(err)
	D := big.Int{}
	D.SetBytes(b)
	PrivKey := ecdsa.PrivateKey{rawPubKey, &D}
	return &Wallet{PrivKey, PubKey, ""}
}
func (self *Model) GetBalance(address string) int {
	bc := NewBlockChain()
	defer bc.Close()
	pubKeyHash := DecodeAddress(address)
	utxos := bc.FindUTXO(pubKeyHash)
	var ret = 0
	for _, utxo := range utxos {
		ret += utxo.Value
	}
	return ret
}
func (self *Model) SendTx(from, to string, cost int, msg string, privkeyStr string) string {
	bc := NewBlockChain()
	defer bc.Close()
	if ValidateAddress(from) && ValidateAddress(to) && cost > 0 {
		wallet := self.DecodeWallet(from, privkeyStr)
		tx := NewUTXOTransaction(from, to, cost, msg, wallet, bc)
		bc.MineBlock([]*Transaction{tx})
		return fmt.Sprintf("%x", tx.ID)
	} else {
		panic("error")
	}
}
func (self *Model) ValidateTx(to string, cost int, msg string, txid string) int {
	bc := NewBlockChain()
	defer bc.Close()
	pubkeyHash := DecodeAddress(to)
	i := bc.Iterator()
	var tx *Transaction = nil
	btxid, err := hex.DecodeString(txid)
	checkError(err)
	for len(i.currentHash) > 0 {
		block := i.Next()
		if bytes.Compare(block.Transactions[0].ID, btxid) == 0 {
			tx = block.Transactions[0]
			break
		}
	}
	if string(tx.Data) != msg {
		return 0
	}
	var sum = 0
	for _, txo := range tx.Vout {
		if txo.IsLockedWithKey(pubkeyHash) {
			sum += txo.Value
		}
	}
	if sum < cost {
		return 0
	}
	return 1
}
func (self *Model) Init() {
	bc := NewBlockChain()
	bc.Close()
}
