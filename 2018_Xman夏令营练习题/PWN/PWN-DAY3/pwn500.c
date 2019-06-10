/*
2017.5.13 by:Ox9A82
House of lemon

1.stack leak--->leak libc
2.unlink write-anything-anywhere
3.alloc big--->arena overflow
4.fake vtable
*/

#include <stdio.h>
#include <stdlib.h>

typedef struct _LEMON
{
char text[16];
struct _LEMON *fd;
struct _LEMON *bk;
} LEMON;


int  data_money=10;
int junk1=0x1;
LEMON data_start={1};
int junk2=0x1;
int data_advise_size=1;
int junk3=0x1;
void *data_advise_ptr=1;
int junk4=0x1;
int leave_flag=1;
int junk5=0x1;
int delete_flag=1;
int junk6=0x1;
int edit_flag=1;
int junk7=0x1;
int sub_flag=1;

void init(void)
{
    alarm(100);
    setbuf(stdin,0);
    setbuf(stdout,0);
    CleanZero();
}

void CleanZero(void)
{
    data_start.fd=&data_start;
    data_start.bk=&data_start;
    data_advise_size=200;
    data_advise_ptr=0;
    leave_flag=0;
    delete_flag=0;
    edit_flag=0;
    sub_flag=0;
}


int GetContent(char *buf,int number)
{
    char temp=0;
    int i=0;

    for(;i<number;i++)
    {
        read(0,&temp,1);
        if(temp!='\n')
        {
            buf[i]=temp;
        }
        else
        {
            temp='\x00';
            buf[i]=temp;
            break;
        }
    }
    if(i==number)
    {
        buf[i-1]='\x00';
    }
    return i;
}


int GetContent_leak(char *buf,int number)
{
    char temp=0;
    int i=0;

    for(;i<number;i++)
    {
        read(0,&temp,1);
        if(temp!='\n')
        {
            buf[i]=temp;
        }
        else
        {
            break;
        }
    }
   
    return i;
}

int GetNumber(void)
{
    int size=128;
    int i=0;
    char temp=0;
    int number=0;
    char data_NumBuf[15]="";
    for(;i<12;i++)
    {
        read(0,&temp,1);
        if(temp!='\n')
        {
            data_NumBuf[i]=temp;
        }
        else
        {
            temp='\x00';
            data_NumBuf[i]=temp;
            break;
        }
    }
    number=atoi(data_NumBuf);
    return number;
}

void welcome(void)
{
    if(data_money<0||data_money>10)
     {
		exit(0);
     }

     if(data_advise_size<200||data_advise_size>8000)
    {
        exit(0);
    }
    puts("\nwelcome to House of lemon");
    puts("This is a lemon store.\n");
    puts("Here is our lemon types list:");
    puts("1.Meyer lemon");
    puts("2.Ponderosa lemon");
    puts("3.Leave advise");
    puts("4.Submit");
    printf("\nNow you have %d$\n",data_money);
    puts("Pls input your choice:");

}

int CheckMoney(int price)
{
    int money=0;
    if(data_money<0||data_money>10)
    {
        exit(0);
    }
    money=data_money-price;
    if(money>=0&&money<=10)
    {
        printf("You have pay %d$\n",price);
        data_money=money;
        return 1;
    }
    else
    {
        puts("You don't have enough money");
        return 0;
    }

}

void Remove(void)
{
     LEMON *ul=0;
     LEMON *bck=0;

     ul=data_start.bk;
     bck=ul->bk;
     data_start.bk=bck;
     bck->fd=&data_start;
     puts("Remove successed");
}

void Meyer_lemon(void) //1
{
    int choice=0;
    LEMON *ptr=0;
    LEMON *bck=0;
    LEMON *ul=0;
    while(1)
    {
        puts("\nYou choice the Meyer lemon!");
        puts("1.Information about Meyer lemon");
        puts("2.Add to my cart");
        puts("3.Remove from my cart");
        puts("4.Leave Message");
        puts("5.back..");
        puts("Pls Input your choice:");
        choice=GetNumber();
        if(choice==1)
        {
            puts("Meyer lemon is 6$");
        }
        else if(choice==2)
        {
            if(!CheckMoney(6))
            {
                return;
            }
            ptr=malloc(sizeof(LEMON));
            ptr->fd=0;
            ptr->bk=0;
           
            bck=data_start.bk;
            data_start.bk=ptr;
            ptr->fd=&data_start;
            ptr->bk=bck;
            bck->fd=ptr;
            puts("successed!");
        }
        else if(choice==3)
        {
           Remove();
        }
        else if(choice==4)
        {
            ul=data_start.bk;
            puts("Get Input:");
	        GetContent(ul->text,32);
        }
        else if(choice==5)
        {
            return;
        }
    }
}

void Ponderosa_lemon(void) //2
{
    int choice=0;
    LEMON *ptr=0;
    LEMON *bck=0;
    LEMON *ul=0;
    while(1)
    {
        puts("\nYou choice the Ponderosa_lemon");
        puts("1.Information about Ponderosa_lemon");
        puts("2.Add to my cart");
        puts("3.Remove from my cart");
        puts("4.Leave Message");
        puts("5.back..");
        puts("Pls Input your choice:");
        choice=GetNumber();
        if(choice==1)
        {
            puts("Meyer lemon is 4$");
        }
        else if(choice==2)
        {
            if(!CheckMoney(4))
            {
                return;
            }
            ptr=malloc(sizeof(LEMON));
            ptr->fd=0;
            ptr->bk=0;
           
            bck=data_start.bk;
            data_start.bk=ptr;
            ptr->fd=&data_start;
            ptr->bk=bck;
            bck->fd=ptr;
            puts("successed!");
        }
        else if(choice==3)
        {
          Remove();
        }
        else if(choice==4)
        {
            ul=data_start.bk;
            puts("Get Input:");
  	        GetContent(ul->text,32);
        }
        else if(choice==5)
        {
            return;
        }
    }
}


void Submit(void)
{   
    if(sub_flag)
    {
 	return;
    }
    char phone[15];
    char buf[100];
    int vip=1056;
    printf("Hello Vip");
    puts("Leave your information");
    puts("Pls input your phone number first:");
    GetContent_leak(phone,15);
    puts("Ok,Pls input your home address");
    GetContent_leak(buf,95);
    printf("OK,your input is:%s",buf);
    sub_flag=1;
    return;
}


void Advise(void)
{
   
    int choice=0;
    while(1)
    {
        puts("1.leave advise");
        puts("2.edit advise");
        puts("3.delete advise");
        puts("4.return");
        choice=GetNumber();
        switch(choice)
        {
            case 1:
                if(leave_flag)
                {
                    return;
                }
                puts("Input size(200~8000):");
                data_advise_size=GetNumber();
                if(data_advise_size<200||data_advise_size>8000)
                {
                    puts("wrong size");
                    return;
                }
                data_advise_ptr=malloc(data_advise_size);
                if(!data_advise_ptr)
                {
                    exit(0);
                }
                puts("OK");
                leave_flag=1;
                break;
            case 2:
                if(edit_flag)
                {
                    return;
                }
                if(data_advise_size<200||data_advise_size>8000)
                {
                   exit(0);
                }
                if(!data_advise_ptr)
                {
                    return;
                }
                puts("Input your advise");
                GetContent(data_advise_ptr,data_advise_size);
                edit_flag=1;
                break;
            case 3:
                if(delete_flag)
                {
                    return;
                }
                if(!data_advise_ptr)
                {
                    puts("nothing to delete");
                    continue;
                }
                free(data_advise_ptr);
                delete_flag=1;
                break;
            case 4:
                return;
            default:
                puts("You input is wrong");
                break;
        }
    }
}

int main()
{
    int choice=0;
    init();
    while(1)
    {
        welcome();
        choice=GetNumber();
        if(choice==1)
        {
            Meyer_lemon();
            continue;
        }
        else if(choice==2)
        {
            Ponderosa_lemon();
            continue;
        }
        else if(choice==3)
        {
            Advise();
            continue;
        }
        else if(choice==4)
        {
            Submit();
            continue;
        }
        else
        {
            puts("Error!please input 1~5");
            continue;
        }
    }
}

