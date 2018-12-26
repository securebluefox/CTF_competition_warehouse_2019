import os
import views
import tornado.web
from settings import debug, cookie_secret


class Application(tornado.web.Application):
    def __init__(self):
        self.root_path = os.path.dirname(__file__)
        self.ans_path = os.path.join(self.root_path, 'captcha/ans')
        self.jpgs_path = os.path.join(self.root_path, 'captcha/jpgs')
        self.files = self._get_files(self.jpgs_path)
        self.uuid = ''
        self.question = ''
        handlers = views.handlers
        settings = dict(
            static_path=os.path.join(self.root_path, 'template/assets'),
            template_path=os.path.join(self.root_path, 'template'),
            login_url='/login',
            cookie_secret=cookie_secret,
            debug=debug,
            xsrf_cookies=True
        )
        super(Application, self).__init__(handlers, **settings)

    def _get_files(self, file_path):
        for root, dirs, files in os.walk(file_path):
            return files

    def _get_ans(self, uuid):
        answer = {}
        with open(os.path.join(self.ans_path, 'ans%s.txt' % uuid), 'r') as f:
            for line in f.readlines():
                if line != '\n':
                    ans = line.strip().split('=')
                    answer[ans[0].strip()] = ans[1].strip()
        return answer

    def _generate_captcha(self):
        uuids = []
        for file in self.files:
            uuids.append(file.replace('ques', '').replace('.jpg', ''))
        from random import choice
        uuid = choice(uuids)
        ans = self._get_ans(uuid)
        self.uuid = uuid
        self.question = ans['vtt_ques']
