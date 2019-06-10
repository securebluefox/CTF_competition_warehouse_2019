#!/usr/bin/env python2

from gmpy2 import powmod, is_prime
from flag import FLAG 

pow = powmod 

# -------------------------------------------------------------
def rabmil(n):
    if n & 1 == 0:
        return False
    else:
        s, d = 0, n - 1
        while d & 1 == 0:
            s, d = s + 1, d >> 1
        for a in (2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 51, 53, 59, 61, 67):
            x = pow(a, d, n)
            if x != 1 and x + 1 != n:
                for r in xrange(1, s):
                    x = pow(x, 2, n)
                    if x == 1:
                        return False 
                    elif x == n - 1:
                        a = 0  
                        break  
                if a:
                    return False
        return True  
# -------------------------------------------------------------

# Now it's time to screw you up :))

if __name__ == '__main__':
    introduction = """
     .--.     .-------------------.
    | _|_    |                   |
    | O O   <  Don't bypass papa |
    |  ||    |                   |
    | _:|    `-------------------'
    |   |
    `---'
    If you take closer look, It's just pixel.
    """
    print introduction
    try:
        num = int(raw_input('p : '))
        if rabmil(num) == True and is_prime(num) == False:
            print '\nHOLY. You hacked papa, mama gonna beat you up. \n'
            print FLAG
        else:
            print "\nWrong. To be smarter, listen to papa: Eat 73686974."

    except:
        print "Take your time to think of the inputs."
        pass