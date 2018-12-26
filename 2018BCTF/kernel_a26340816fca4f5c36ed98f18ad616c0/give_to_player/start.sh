#!/bin/sh
GDB_PORT=1234
qemu-system-x86_64 \
-m 256M \
-kernel ./bzImage \
-initrd  ./1.cpio \
-append "root=/dev/ram rw console=ttyS0 oops=panic quiet nokaslr" \
-cpu qemu64,+smep,+smap \
-netdev user,id=t0, -device e1000,netdev=t0,id=nic0 \
-gdb tcp::${GDB_PORT} \
-monitor /dev/null -nographic 2>/dev/null
