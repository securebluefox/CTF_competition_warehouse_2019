import os

limit = 10
username = 'hao123'
email = 'hao123@qvq.im'
debug = True

#os.getcwd()
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
connect_str = 'sqlite:///%s' % os.path.join(BASE_DIR, 'sshop.db3')
cookie_secret = 'JDIOtOQQjLXklJT/N4aJE.tmYZ.IoK9M0_IHZW448b6exe7p1pysO'
