#!/usr/bin/env python3
from os import urandom
import signal


def getrandbits(bit):
    return int.from_bytes(urandom(bit >> 3), "big")


def main():
    signal.alarm(60)
    secret = getrandbits(1024)
    print("Listen...The secret is...M2@...f*#...z()I!(...3;J..."
          "Hello?...really noisy here...God bless you get it...")
    for i in range(50):
        op = input().strip()
        num = input().strip()
        if not str.isnumeric(num):
            print("INVALID NUMBER")
            continue
        num = int(num)
        if op == 'god':
            print((num + getrandbits(1000)) % secret)
        elif op == 'bless':
            if num == secret:
                print("CONGRATULATIONS", FLAG)
                return
            print("WRONG SECRET")


main()
