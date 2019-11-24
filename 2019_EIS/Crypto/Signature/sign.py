#!/usr/bin/env python3
# coding=utf-8

import binascii
import os
import random
import signal

def b2n(b):
    res = 0
    for i in b:
        res *= 2
        res += i
    return res

def n2b(n, length):
    tmp = bin(n)[2:]
    tmp = '0'*(length-len(tmp)) + tmp
    return [int(i) for i in tmp]

def s2n(s):
    return int(binascii.hexlify(s), 16)

def sign(msg):
    msg = n2b(s2n(msg), len(msg)*8)
    msg += b1
    for shift in range(len(msg)-64):
        if msg[shift]:
            for i in range(65):
                msg[shift+i] ^= b2[i]
    res = msg[-64:]
    return b2n(res)

b1 = n2b(0xdeadbeeffeedcafe, 64)
b2  = n2b(0x10000000247f43cb7, 65)

if __name__ == '__main__':
    signal.alarm(60)
    with open('/home/ctf/flag', 'r') as f:
        flag = f.read()

    try:
        print("Welcome to the Signature Challenge!")
        raw = os.urandom(256)
        pos = random.randint(0, 248)
        raw_hex = bytearray(binascii.hexlify(raw))
        for i in range(8):
            raw_hex[(pos+i)*2] = ord('_')
            raw_hex[(pos+i)*2+1] = ord('_')
        raw_hex = bytes(raw_hex)
        print(f"Here is the message: {raw_hex.decode('ascii')}")
        ans = input("Please fill the blank: ")
        ans = bytes.fromhex(ans)
        assert len(ans) == 8

        raw = bytearray(raw)
        for i in range(8):
            raw[pos+i] = ans[i]
        raw = bytes(raw)
        if sign(raw) == 0x1337733173311337:
            print(f"Great! Here is your flag: {flag}")
        else:
            print(f"Wrong! Bye~")
    except Exception:
        print("Error!")
