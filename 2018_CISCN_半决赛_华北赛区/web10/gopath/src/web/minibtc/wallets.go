package minibtc

import (
	"bytes"
	"crypto/elliptic"
	"encoding/gob"
	"fmt"
	"io/ioutil"
	"log"
	"os"
)

const walletFile = "wallet.dat"

type Wallets struct {
	Wallets map[string]*Wallet
}

func NewWallets() *Wallets {
	wallets := Wallets{}
	wallets.Wallets = make(map[string]*Wallet)

	wallets.LoadFromFile()

	return &wallets
}

func (ws *Wallets) LoadFromFile() {
	gob.Register(elliptic.P256())

	if _, err := os.Stat(walletFile); os.IsNotExist(err) {
		return
	}
	f, err := os.Open(walletFile)
	if err != nil {
		panic(err)
	}
	decoder := gob.NewDecoder(f)
	err = decoder.Decode(ws)
	if err != nil {
		panic(err)
	}
}

func (ws *Wallets) GetWallet(address string) *Wallet {
	fmt.Println("Plz input your password")
	var password = ""
	fmt.Scanln(&password)
	wallet := ws.Wallets[address]
	if wallet == nil {
		panic("wallet not exist")
	}
	if wallet.Password != password {
		panic("Password not correct ")
	}
	return wallet
}

func (ws *Wallets) CreateWallet() {
	wallet := NewWallet()
	address := wallet.GetAddress()
	fmt.Println("Create wallet successfully! your address is ", address)
	ws.Wallets[wallet.GetAddress()] = wallet
}

// SaveToFile saves wallets to a file
func (ws Wallets) SaveToFile() {
	var content bytes.Buffer

	gob.Register(elliptic.P256())

	encoder := gob.NewEncoder(&content)
	err := encoder.Encode(ws)
	if err != nil {
		log.Panic(err)
	}

	err = ioutil.WriteFile(walletFile, content.Bytes(), 0644)
	if err != nil {
		log.Panic(err)
	}
}
