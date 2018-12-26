import tornado.web
from models import db


class BaseHandler(tornado.web.RequestHandler):
    @property
    def orm(self):
        return db()

    def on_finish(self):
        db.remove()

    def get_current_user(self):
        return self.get_secure_cookie("username")

    def check_captcha(self):
        try:
            x = float(self.get_argument('captcha_x'))
            y = float(self.get_argument('captcha_y'))
            if x and y:
                uuid = self.application.uuid
                answer = self.application._get_ans(uuid)
                print x,y,uuid, answer
                if float(answer['ans_pos_x_1']) <= x <= (float(answer['ans_width_x_1']) + float(answer['ans_pos_x_1'])):
                    if float(answer['ans_pos_y_1']) <= y <= (
                            float(answer['ans_height_y_1']) + float(answer['ans_pos_y_1'])):
                        return True
                return False
        except Exception as ex:
            print str(ex)
            return False
