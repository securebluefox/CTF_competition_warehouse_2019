#include <stdio.h>
char buf[0x100]="";
FILE *fp ;
void shell(){
    system("/bin/sh");
}
int main(){
    fp = fopen("test.txt","r");
    gets(buf);
    fclose(fp);
}
