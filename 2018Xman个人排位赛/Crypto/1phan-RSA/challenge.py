from Crypto.PublicKey import RSA
from Crypto.Util.number import bytes_to_long, long_to_bytes
import socketserver

class PUB():
        def __init__(self):
                self.rsa = RSA.generate(2048)
        
        def get_n(self):
                return self.rsa.n
                
        def get_e(self):
                return self.rsa.e

        def encrypt(self, plaintext):
                return self.rsa.encrypt(plaintext, None)[0]

        def decrypt(self, ciphertext):
                return (self.rsa.decrypt(ciphertext) % 2 == 0)

class process(socketserver.BaseRequestHandler):
    def handle(self):
        #self.justWaite()    
        pub = PUB()
        e, n = pub.get_e(), pub.get_n()
        self.request.send(bytes(hex(e), 'utf-8'))
        self.request.send(b'\n\n')
        self.request.send(bytes(hex(n), 'utf-8'))

        while True:
            self.request.send(b"\n'f'lag or 'e'ncrypt or 'd'ecrypt_detect\n")
            c = self.request.recv(2)[:-1]
            if c == b'f':
                flag = b'xman{*********************}'
                flag = bytes_to_long(flag)
                
                self.request.send(long_to_bytes(pub.encrypt(flag)))

            elif c == b'd':
                c = self.request.recv(2048)[:-1]
                c = bytes_to_long(c)
                self.request.send(bytes(str(pub.decrypt(c)), 'utf-8'))

        self.request.close()


class ThreadedServer(socketserver.ThreadingMixIn, socketserver.TCPServer):
    pass

if __name__ == "__main__":
    HOST, PORT = '0.0.0.0', 10093
    server = ThreadedServer((HOST, PORT), process)
    server.allow_reuse_address = True
    server.serve_forever()
