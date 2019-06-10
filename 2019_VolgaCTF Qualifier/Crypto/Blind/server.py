#!/usr/bin/env python
from __future__ import print_function
import os
import sys
import shlex
import subprocess
from private_key import d


"""
    Utils
"""


def run_cmd(cmd):
    try:
        args = shlex.split(cmd)
        return subprocess.check_output(args)
    except Exception as ex:
        return str(ex)


"""
    Signature
"""

class RSA:
    def __init__(self, e, d, n):
        self.e = e
        self.d = d
        self.n = n

    def sign(self, message):
        message = int(message.encode('hex'), 16)
        return pow(message, self.d, self.n)

    def verify(self, message, signature):
        message = int(message.encode('hex'), 16)
        verify = pow(signature, self.e, self.n)
        return message == verify


"""
	Keys
"""

n = 26507591511689883990023896389022361811173033984051016489514421457013639621509962613332324662222154683066173937658495362448733162728817642341239457485221865493926211958117034923747221236176204216845182311004742474549095130306550623190917480615151093941494688906907516349433681015204941620716162038586590895058816430264415335805881575305773073358135217732591500750773744464142282514963376379623449776844046465746330691788777566563856886778143019387464133144867446731438967247646981498812182658347753229511846953659235528803754112114516623201792727787856347729085966824435377279429992530935232902223909659507613583396967
e = 65537


"""
    Communication utils
"""

def read_message():
    return sys.stdin.readline()


def send_message(message):
    sys.stdout.write('{0}\r\n'.format(message))
    sys.stdout.flush()


def eprint(*args, **kwargs):
    print(*args, file=sys.stderr, **kwargs)


"""
    Main
"""

def check_cmd_signatures(signature):
    cmd1 = 'exit'
    cmd2 = 'leave'
    assert (signature.verify(cmd1, signature.sign(cmd1)))
    assert (signature.verify(cmd2, signature.sign(cmd2)))


class SignatureException(Exception):
    pass


if __name__ == '__main__':
    signature = RSA(e, d, n)
    check_cmd_signatures(signature)
    try:
        while True:
            send_message('Enter your command:')
            message = read_message().strip()
            (sgn, cmd_exp) = message.split(' ', 1)
            eprint('Accepting command {0}'.format(cmd_exp))
            eprint('Accepting command signature: {0}'.format(sgn))

            cmd_l = shlex.split(cmd_exp)
            cmd = cmd_l[0]
            if cmd == 'ls' or cmd == 'dir':
                ret_str = run_cmd(cmd_exp)
                send_message(ret_str)

            elif cmd == 'cd':
                try:
                    sgn = int(sgn)
                    if not signature.verify(cmd_exp, sgn):
                        raise SignatureException('Signature verification check failed')
                    os.chdir(cmd_l[1])
                    send_message('')
                except Exception as ex:
                    send_message(str(ex))

            elif cmd == 'cat':
                try:
                    sgn = int(sgn)
                    if not signature.verify(cmd_exp, sgn):
                        raise SignatureException('Signature verification check failed')
                    if len(cmd_l) == 1:
                        raise Exception('Nothing to cat')
                    ret_str = run_cmd(cmd_exp)
                    send_message(ret_str)
                except Exception as ex:
                    send_message(str(ex))

            elif cmd == 'sign':
                try:
                    send_message('Enter your command to sign:')
                    message = read_message().strip()
                    message = message.decode('base64')
                    cmd_l = shlex.split(message)
                    sign_cmd = cmd_l[0]
                    if sign_cmd not in ['cat', 'cd']:
                        sgn = signature.sign(sign_cmd)
                        send_message(str(sgn))
                    else:
                        send_message('Invalid command')
                except Exception as ex:
                    send_message(str(ex))

            elif cmd == 'exit' or cmd == 'leave':
                sgn = int(sgn)
                if not signature.verify(cmd_exp, sgn):
                    raise SignatureException('Signature verification check failed')
                break

            else:
                send_message('Unknown command {0}'.format(cmd))
                break

    except SignatureException as ex:
        send_message(str(ex))
        eprint(str(ex))

    except Exception as ex:
        send_message('Something must have gone very, very wrong...')
        eprint(str(ex))

    finally:
        pass