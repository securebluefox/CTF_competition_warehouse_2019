#!/usr/bin/env python3
# coding=utf-8

import os
import signal
from Crypto.Cipher import AES
from Crypto.Util import Counter

def enc(msg, key):
    ctr = Counter.new(128,  initial_value=sum(msg))
    cipher = AES.new(key, AES.MODE_CTR, counter=ctr)
    return cipher.encrypt(msg)

if __name__ == '__main__':
    signal.alarm(60)
    key = os.urandom(16)
    with open('/home/ctf/flag', 'rb') as f:
        flag = f.read()
    assert len(flag) == 30
    enc_flag = enc(flag, key)

    print("Welcome to the our AES encryption system!")
    print(f"Here is your encrypted flag: {enc_flag}")
    for i in range(30):
        try:
            plaintext = input("Please input your plaintext: ")
            plaintext = bytes.fromhex(plaintext)
            ciphertext = enc(plaintext, key)
            print(f"Here is your ciphertext: {ciphertext}")
        except Exception:
            print('Error!')
            break
    print('Bye~')
