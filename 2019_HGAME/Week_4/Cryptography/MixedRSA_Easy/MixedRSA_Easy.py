#!/usr/bin/python3
from Crypto.Util.number import getPrime
from signal import alarm


def gcd(a, b):
    while b != 0:
        a, b = b, a % b
    return a


def ex_gcd(m, n):
    x, y, x1, y1 = 0, 1, 1, 0
    while m % n:
        x, x1 = x1 - m // n * x, x
        y, y1 = y1 - m // n * y, y
        m, n = n, m % n
    return n, x, y


def inv(x, p):
    g, y, k = ex_gcd(x, p)
    if y < p:
        y += p
    return y


def xor(a, b):
    return bytes(x ^ y for x, y in zip(a, b))


class MixedRSA:
    n = e = d = iv = BLOCK = 0

    def __init__(self, iv, block=256):
        self.BLOCK = block
        p = getPrime(block * 4)
        q = getPrime(block * 4)
        self.n = p * q
        phi = (p - 1) * (q - 1)
        self.e = getPrime(64)
        self.d = inv(self.e, phi)
        iv *= block // len(iv)
        self.iv = iv.rjust(block, b'\x00')

    def padding(self, s):
        return b'\x00' * (-len(s) % self.BLOCK) + s

    def rsa_encrypt(self, m):
        c = pow(int(m.hex(), 16), self.e, self.n)
        c = hex(c)[2:].rjust(self.BLOCK * 2, '0')
        return bytes.fromhex(c)

    def rsa_decrypt(self, c):
        m = pow(int(c.hex(), 16), self.d, self.n)
        m = hex(m)[2:].rjust(self.BLOCK * 2, '0')
        return bytes.fromhex(m)

    def encrypt(self, plaintext):
        plaintext = self.padding(plaintext)
        imd = self.iv
        for i in range(0, len(plaintext), self.BLOCK):
            m = xor(imd[-self.BLOCK:], plaintext[i: i + self.BLOCK])
            imd += self.rsa_encrypt(m)
        imd = imd[self.BLOCK:]
        cipher = self.iv
        for i in range(0, len(imd), self.BLOCK):
            c = self.rsa_encrypt(cipher[-self.BLOCK:])
            cipher += xor(c, imd[i: i + self.BLOCK])
        return cipher[self.BLOCK:]

    def decrypt(self, cipher):
        cipher = self.iv + cipher
        imd = b''
        for i in range(self.BLOCK, len(cipher), self.BLOCK):
            c = self.rsa_encrypt(cipher[i - self.BLOCK: i])
            imd += xor(c, cipher[i: i + self.BLOCK])
        imd = self.iv + imd
        plaintext = b''
        for i in range(self.BLOCK, len(imd), self.BLOCK):
            m = self.rsa_decrypt(imd[i: i + self.BLOCK])
            plaintext += xor(m, imd[i - self.BLOCK: i])
        return plaintext


if __name__ == '__main__':
    alarm(60)
    mix = MixedRSA(FLAG)
    while True:
        print("Choose:\n[1] Encrypt (hex)\n[2] Decrypt (hex)")
        op = input()
        if op == '1':
            msg = bytes.fromhex(input())
            print(mix.encrypt(msg).hex())
        elif op == '2':
            msg = bytes.fromhex(input())
            print(mix.decrypt(msg).hex())
        else:
            print('Bye')
            break
