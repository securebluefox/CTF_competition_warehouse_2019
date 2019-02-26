#!/usr/bin/python3
from Crypto.Cipher import AES
from hashlib import md5
from os import urandom
import string
from signal import alarm


class Sign:
    block = T = 0
    key = salt = ""

    def __init__(self, key, salt):
        self.key = key
        self.salt = salt
        self.block = len(key)

    def register(self, username):
        if b'admin' in username:
            return None
        sig = md5(self.salt + username).digest()
        padlen = self.block - len(username) % self.block
        username += bytes([padlen] * padlen)
        iv = urandom(self.block)
        aes = AES.new(self.key, AES.MODE_CBC, iv)
        c = aes.encrypt(username)
        return iv + c + sig

    def login(self, cipher):
        if len(cipher) % self.block != 0:
            return None
        self.T -= 1
        iv = cipher[:self.block]
        sig = cipher[-self.block:]
        cipher = cipher[self.block:-self.block]
        aes = AES.new(self.key, AES.MODE_CBC, iv)
        p = aes.decrypt(cipher)
        p = p[:-p[-1]]
        return [p, md5(self.salt + p).digest() == sig]


if __name__ == '__main__':
    unprintable = b""
    for i in range(256):
        if chr(i) not in string.printable:
            unprintable += bytes([i])
    alarm(60)
    s = Sign(urandom(16), urandom(16))
    while True:
        print("Choose:\n[1] Register\n[2] Login")
        op = input()
        if op == '1':
            user = input("Input your username(hex): ")
            token = s.register(bytes.fromhex(user))
            if not token:
                print("Sorry, invalid username.")
            else:
                print("Your token is: %s" % token.hex())
        elif op == '2':
            token = input("Input your token: ")
            res = s.login(bytes.fromhex(token))
            if not res:
                print("Sorry, invalid token.")
            elif not res[1]:
                user = res[0].hex()
                print("Sorry, your username(hex) %s is inconsistent with given signature." % user)
            else:
                user = res[0].strip(unprintable).decode("Latin1")
                print("Login success. Welcome, %s!" % user)
                if user == "admin":
                    print("I have a gift for you: %s" % FLAG)
        else:
            print("See you")
            break
