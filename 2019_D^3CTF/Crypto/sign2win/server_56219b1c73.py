import os
import SocketServer
import signal,random,string
import hashlib
import ecdsa
from ecdsa import SECP256k1

FLAG = open("flag.txt","r").read()



class signandverify(SocketServer.StreamRequestHandler):
    def proof_of_work(self):
        random.seed(os.urandom(8))
        proof = ''.join([random.choice(string.ascii_letters+string.digits) for _ in range(20)])
        digest = hashlib.sha256(proof.encode('utf-8')).hexdigest()
        self.request.sendall("sha256(XXXX+%s) == %s\n" % (proof[4:],digest))
        self.request.sendall('Give me XXXX:')
        x = self.rfile.readline().strip()
        if len(x) != 4 or hashlib.sha256((x+proof[4:])).hexdigest() != digest: 
            return False
        return True

    def handle(self):
        signal.alarm(500)
        if not self.proof_of_work():
            return
        req = self.request
        sk=None
        vk=None
        while True:
                req.sendall('''

    Please select your options:

    1. Generate a key  to sign and verify messages.
    2. Provide a pubkey to verify signature.
    3. Sign message.
    4. Verify message.
    5. Get flag.
    ''')
                msg = self.rfile.readline().strip()
                gsk = ecdsa.SigningKey.generate(curve=SECP256k1)
                gvk = gsk.get_verifying_key()
                if msg[0] == '1':
                    sk = ecdsa.SigningKey.generate(curve=SECP256k1)
                    vk = sk.get_verifying_key()
                    req.sendall("your pubkey is (hex encode):%s" % vk.to_string().encode('hex'))
                elif msg[0] == '2':
                    req.sendall("Please send your pubkey(hex encode)\n")
                    msg = self.rfile.readline().strip().decode('hex')
                    try:
                        vk = gvk.from_string(msg,curve=SECP256k1)
                    except:
                        req.sendall("err msg")
                        req.close()
                        return
                    req.sendall("now your pubkey is update to :%s" % vk.to_string().encode('hex'))

                elif msg[0] == '3':
                    if sk==None:
                        req.sendall("you should generate a key first\n")
                        break
                    req.sendall("please send the message you want to sign(hex encode)\n")
                    msg = self.rfile.readline().strip().decode('hex')
                    signature = sk.sign(msg,hashfunc=hashlib.sha256)
                    req.sendall("signature for your message(hex encode):%s" % signature.encode('hex'))
                elif msg[0] == '4':
                    if vk==None:
                        req.sendall("you should have a pubkey first\n")
                        break
                    req.sendall("Please send the signature you want to verify(hex encode)\n")
                    sig = self.rfile.readline().strip().decode('hex')
                    req.sendall("please send the message of this signature(hex encode)\n")
                    m=self.rfile.readline().strip().decode('hex')
                    try:
                        if vk.verify(sig,m,hashfunc=hashlib.sha256):
                            req.sendall("verify success\n")
                        else:
                            req.sendall("wrong signature\n")
                    except:
                        req.sendall("err msg\n")
                        req.close()
                        return
                elif msg[0] == '5':
                    m1="I want the flag"
                    m2="I hate the flag"
                    req.sendall("sign a message '%s' and send the signature\n" % m1)
                    sig1 = self.rfile.readline().strip().decode('hex')
                    req.sendall("sign a message '%s' and send the signature\n" % m2)
                    sig2 = self.rfile.readline().strip().decode('hex')
                    if sig1!=sig2:
                        req.sendall("sorry,check failed")
                        req.close()
                        return
                    if vk.verify(sig1,m1,hashfunc=hashlib.sha256) and vk.verify(sig2,m2,hashfunc=hashlib.sha256):
                        req.sendall("Here is your flag:%s" % FLAG)
                        return
                    else:
                        req.sendall("sorry,verify failed")
                        req.close()
                        return

class ThreadedServer(SocketServer.ThreadingMixIn, SocketServer.TCPServer):
    pass


if __name__ == "__main__":
    HOST, PORT = "0.0.0.0", 12233
    server = ThreadedServer((HOST, PORT), signandverify)
    server.allow_reuse_address = True
    server.serve_forever()

