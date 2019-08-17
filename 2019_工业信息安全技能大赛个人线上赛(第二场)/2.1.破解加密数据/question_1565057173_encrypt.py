m = "unknown"
e = 2
n = 0x6FBD096744B2B34B423D70C8FB19B541

assert(int(m.encode('hex'), 16) < n)
c = pow(int(m.encode('hex'), 16),e,n)
print c
#109930883401687215730636522935643539707
