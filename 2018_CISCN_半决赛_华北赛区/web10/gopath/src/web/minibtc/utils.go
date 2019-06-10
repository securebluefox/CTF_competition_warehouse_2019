package minibtc

import (
	"bytes"
	"encoding/binary"
)

func checkError(err error) {
	if err != nil {
		panic(err)
	}
}

func IntToHex(x int64) []byte {
	buff := new(bytes.Buffer)
	err := binary.Write(buff, binary.BigEndian, x)
	if err != nil {
		panic(err)
	}
	return buff.Bytes()
}
