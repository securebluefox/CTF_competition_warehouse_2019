import requests
import json

headers = {'Content-Type': 'application/x-www-form-urlencoded',
           'User-Agent': 'Pig-Peggy Shop'}


def create_tx(from_addr, to_addr, commodity_id, msg, private_key):
    try:
        data = 'from=%s&to=%s&commodity_id=%s&msg=%s&privkey=%s' % (from_addr, to_addr, commodity_id, msg, private_key)
        requests.post(url='http://127.0.0.1:8081/createTx', headers=headers, data=data)
    except:
        pass


def get_balance(addr):
    try:
        response = requests.get(url='http://127.0.0.1:8081/getBalance?addr=' + addr, headers=headers)
        return float(response.content)
    except:
        return float(-1)
