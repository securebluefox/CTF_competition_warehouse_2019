import tornado.web
from tornado.escape import url_unescape
from tornado.template import Loader, Template
from tornado.httpclient import HTTPResponse

class CaptchaHandler(tornado.web.RequestHandler):
    def get(self, *args, **kwargs):
        file = open(self.application.jpgs_path + '/ques%s.jpg' % self.application.uuid,'r')
        self.write(file.read())
        file.close()
        self.set_header('Content-Type', 'image/jpeg')


class ErrorHandler(tornado.web.RequestHandler):
    def get(self):
        self.write_error(404)
    
    def write_error(self, status_code, **kwargs):
        if status_code == 404:
            url = url_unescape(self.request.path)
            # loader = Loader('./sshop/template')
            template_data = TEMPLATE.replace("FOO", url)
            t = tornado.template.Template(template_data)

            # self.render('404.html', url=self.request.path)
            self.write(t.generate())


TEMPLATE = '''
<html>
<head><title> 404 Error </title></head>
<body>URL FOO Not Found</body>
</html>
'''