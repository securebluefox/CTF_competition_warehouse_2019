import binascii
from lfsr_parameters import *


class LFSR:
    def __init__(self, register, branches):
        self.register = register
        self.branches = branches
        self.n = len(register)

    def next_bit(self):
        ret = self.register[self.n - 1]
        new = 0
        for i in self.branches:
            new ^= self.register[i - 1]
        self.register = [new] + self.register[:-1]

        return ret


f = open('flag.html', 'r')
flag_text = f.read()
f.close()

flag_bin_text = bin(int(binascii.hexlify(flag_text), 16))[2:]
print flag_bin_text
flag_bits = [int(i) for i in flag_bin_text]
generator = LFSR(register, branches)
ctext = []
for i in range(len(flag_bits)):
    ctext.append(flag_bits[i] ^ generator.next_bit())

ciphertext = '0b' + ''.join(map(str, ctext))
n = int(ciphertext, 2)
print binascii.unhexlify('%x' % n).encode('base64')