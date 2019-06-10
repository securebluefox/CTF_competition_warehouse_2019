from flask import Flask,request
import cPickle,json,re,M2Crypto
app = Flask(__name__)
class Phone(object):
	def __init__(self,makers='',model='',language='',Android='',IMEI=''):
		self.makers = makers
		self.model = model 
		self.language = language 
		self.Android = Android 
		self.IMEI = IMEI 
def public_decrypt(msg):
	sign_pub='''
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCqtXUIVoPUcBV1Wl3g8rGGNvMY
ImonQdMC1Y8USwIwf7Y0GcBP/h6fAJPAS9//qYZzy8ZfDKH1+ezifFFCUTCCa/8a
YFoms223okyzeTlUIRHbIkto1JxYOazbsE6+KmE+yJiij4839SYuC1KsLWT82uHE
A3Hau/DTzW4g4xhvzQIDAQAB
-----END PUBLIC KEY-----
'''
	bio = M2Crypto.BIO.MemoryBuffer(sign_pub)
	rsa_pub = M2Crypto.RSA.load_pub_key_bio(bio)
	ctxt_pri = msg.decode("base64")
	output = rsa_pub.public_decrypt(ctxt_pri, M2Crypto.RSA.pkcs1_padding)
	return output
@app.route("/",methods=['POST','GET'])
def hello():
	try:
		data  = public_decrypt(request.data)
		phone = json.loads(data)
		imei = phone["IMEI"]
		if re.match("^\d{15}$",imei):
			file = open("./phone/"+imei,'wb')
			newPhone = Phone(phone["makers"],phone["model"],phone["language"],phone["Android"],phone["IMEI"])
			phonestring = cPickle.dump(newPhone,file)
	except Exception as e:
		print e
	return ""
@app.route("/search",methods=['POST','GET'])
def search():
	try:
		print "xxxx"+request.form.get("imei")
		imei = public_decrypt(request.form.get("imei"))
		if re.match("^\d{15}$",imei):
			f = open("./phone/"+imei)
			phone = cPickle.load(f)
			return phone.makers+'\n'+phone.model+'\n'+phone.language+'\n'+phone.Android+'\n'+phone.IMEI
	except Exception as e:
		print e
		return ""
	return ""
@app.route("/upload",methods=['POST','GET'])
def upload():
	try:
		f = request.files['myfile']
		f.save("./image/"+f.filename)
	except Exception as e:
		print e
		return ""
	return ""
@app.route("/check",methods=['POST','GET'])
def check():
	try:
		name = request.form.get('myfile')
		if re.match("^\d{15}$",name):
			f = open("./image/"+name,"rb")
			return f.read()
	except Exception as e:
		print e
		return ""
	return ""
if __name__ == '__main__':
	app.run(debug=False, host='0.0.0.0')