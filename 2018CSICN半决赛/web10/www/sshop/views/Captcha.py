import tornado.web


class CaptchaHandler(tornado.web.RequestHandler):
    def get(self, *args, **kwargs):
        file = open(self.application.jpgs_path + '/ques%s.jpg' % self.application.uuid,'r')
        self.write(file.read())
        file.close()
        self.set_header('Content-Type', 'image/jpeg')