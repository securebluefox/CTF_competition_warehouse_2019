import time
import random
import requests
import base64

def getrandom():
    while 1:
        RANDOMLIST=[]
        for i in range(3):
            RANDOMLIST.append(random.randint(1, 49))
        requests.get("http://127.0.0.1:8233/getrand/"+base64.b64encode(str(RANDOMLIST)))
        time.sleep(600)

getrandom()
