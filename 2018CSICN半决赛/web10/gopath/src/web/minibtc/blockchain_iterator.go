package minibtc

import (
	"errors"

	"github.com/boltdb/bolt"
)

type BlockChainIterator struct {
	currentHash []byte
	db          *bolt.DB
}

func (i *BlockChainIterator) Next() *Block {
	var block *Block
	err := i.db.View(func(tx *bolt.Tx) error {
		b := tx.Bucket([]byte(blocksBucket))
		encodedBlock := b.Get(i.currentHash)
		if encodedBlock == nil {
			return errors.New("cannot get block")
		}
		block = DeserializeBlock(b.Get(i.currentHash))
		return nil
	})
	if err != nil {
		panic(err)
	}
	i.currentHash = block.PrevBlockHash
	return block
}
