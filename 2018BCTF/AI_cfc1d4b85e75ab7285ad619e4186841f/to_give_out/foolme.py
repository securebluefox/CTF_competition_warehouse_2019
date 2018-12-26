from PIL import Image
import sys
import os
import numpy as np
import random
import time
import base64
import inception
import string
import hashlib
SALT_LEN = 10
HEX_LEN = 4


std_image_name="/home/ctf/images/image.jpg"
input_image_name=''
os.environ['TF_CPP_MIN_LOG_LEVEL']='2'

def base_str():
    return "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"   

def random_string(length):
    string = [random.choice(base_str()) for i in range(length)]
    return ("".join(string))

def tofile(data):
    try:
        data=base64.b64decode(data)
    except:
        return ""
    filename="/backup/"+str(time.time())+".jpg"
    fd=open(filename,"wb")
    fd.write(data)
    fd.close()
    filename="/home/ctf/images/"+str(time.time())+".jpg"
    fd=open(filename,"wb")
    fd.write(data)
    fd.close()
    return filename

def check():
    global input_image_name
    try:
        input_image=Image.open(input_image_name)
        std_image=Image.open(std_image_name)
    except:
        print("[-]give me a real image!!")
        sys.stdout.flush()
        return False
    input_image_np=np.array(input_image)
    std_image_np=np.array(std_image)
    input_x=len(input_image_np)
    input_y=len(input_image_np[0])
    input_z=len(input_image_np[0][0])
    std_x=len(std_image_np)
    std_y=len(std_image_np[0])
    std_z=len(std_image_np[0][0])
    if std_x!=input_x or std_y!=input_y or std_z!=input_z:
        return False
    diff=0
    for i in range(input_x):
        for j in range(input_y):
            for k in range(input_z):
                if input_image_np[i][j][k]>std_image_np[i][j][k]:
                    diff+=input_image_np[i][j][k]-std_image_np[i][j][k]
                else:
                    diff+=std_image_np[i][j][k]-input_image_np[i][j][k]
    diff=diff/(input_x*input_y*input_z) 
    if diff>2:
        return False
    return True

def classify(image_path):
    model = inception.Inception()
    pred = model.classify(image_path=image_path)
    return (pred.argmax(),model.name_lookup.cls_to_name(pred.argmax(),only_first_name=True))
def getflag():
    fd=open("/home/ctf/flag")
    flag=fd.readline()
    fd.close()
    return flag
def main():
    salt=random_string(SALT_LEN)
    tmpvalue=random_string(20)+salt
    md5=hashlib.md5()
    md5.update(tmpvalue.encode("utf-8"))
    submd5=md5.hexdigest()[:4]
    print ("[*]Proof of work:")
    print ("\tMD5(key+\"%s\")[:4]==%s"%(salt,submd5))
    print ("[+]Give me the key:")
    sys.stdout.flush()
    value=sys.stdin.readline()[:-1]
    value=value+salt
    md5=hashlib.md5()
    md5.update(value.encode("utf-8"))
    md5value=md5.hexdigest()
    if(md5value[:HEX_LEN]!=submd5):
        print ("[-]Access Failed")
        return;
    print ("[*]I am the world smartest CV system!")
    print ("[+]Give me a wing to fly?")
    sys.stdout.flush()
    global input_image_name
    image=sys.stdin.readline()[:-1]
    if(len(image)>200000):
        print("[-]input too long!")
        return;
    input_image_name=tofile(image)
    if input_image_name=="":
        print ("[-]base64 please!")
        sys.stdout.flush()
        return
    if not check():
        print ("[-]You cannot fool me!")
        sys.stdout.flush()
        return
    (input_image_class,input_image_classname)=classify(input_image_name)
    (std_image_class,std_image_classname)=classify(std_image_name)
    if input_image_class!=std_image_class and input_image_class==503:
        print("[*]Wow I get the wing")
        print("[*]Give you the flag")

        print(getflag())
        sys.stdout.flush()
        return
    else:
        print("[*]Give me the wing!")
        sys.stdout.flush()
        return
    
main()
