#include <stdint.h>
#include <stdlib.h>
#include <stdio.h>
#include <unistd.h>

#define GPS_ACCURACY 1337

typedef void (fn_t)(void);

void initialize() {
    printf("GPS Initializing");
    for (int i = 0; i < 10; ++i) {
        usleep(300000);
        printf(".");
    }
    printf("Done\n");
}

void acquire_satellites() {
    printf("Acquiring satellites.");
    for (int i = 0; i < 3; ++i) {
        printf("Satellite %d", i);
        for (int j = 0; j < rand() % 10; ++j) {
            usleep(133700);
            printf(".");
        }
        if (i != 3) {
            printf("Done\n");
        } else {
            printf("Weak signal.\n");
        }
    }

    printf("\nGPS Initialized.\n");
    printf("Warning: Weak signal causing low measurement accuracy\n\n");
}

void *query_position() {
  char stk;
  int offset = rand() % GPS_ACCURACY - (GPS_ACCURACY / 2);
  void *ret = &stk + offset;
  return ret;
}


int main() {
    setbuf(stdout, NULL);

    char buffer[0x1000];
    srand((unsigned) (uintptr_t) buffer);

    initialize();
    acquire_satellites();

    printf("We need to access flag.txt.\nCurrent position: %p\n", query_position());

    printf("What's your plan?\n> ");
    fgets(buffer, sizeof(buffer), stdin);

    fn_t *location;

    printf("Where do we start?\n> ");
    scanf("%p", (void**) &location);

    location();
    return 0;
}
