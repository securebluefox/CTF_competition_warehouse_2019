import tornado.web
import tornado.ioloop
from tornado.options import define, options
from sqlalchemy.orm.exc import NoResultFound
from sshop.base import BaseHandler
from sshop.models import User
import bcrypt
import shutil
import os
import yaml


class UserLoginHanlder(BaseHandler):
    def get(self, *args, **kwargs):
        self.application._generate_captcha()
        return self.render('login.html', ques=self.application.question, uuid=self.application.uuid)

    def post(self):
        if not self.check_captcha():
            return self.render('login.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        username = self.get_argument('username')
        password = self.get_argument('password')
        if username and password:
            try:
                user = self.orm.query(User).filter(User.username == username).one()
            except NoResultFound:
                return self.render('login.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
            if user.check(password):
                self.set_secure_cookie('username', user.username)
                self.redirect('/user')
            else:
                return self.render('login.html', danger=1, ques=self.application.question, uuid=self.application.uuid)


class RegisterHandler(BaseHandler):
    def get(self, *args, **kwargs):
        self.application._generate_captcha()
        return self.render('register.html', ques=self.application.question, uuid=self.application.uuid)

    def post(self, *args, **kwargs):
        if not self.check_captcha():
            return self.render('register.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        username = self.get_argument('username')
        mail = self.get_argument('mail')
        password = self.get_argument('password')
        password_confirm = self.get_argument('password_confirm')
        invite_user = self.get_argument('invite_user')

        if password != password_confirm:
            return self.render('register.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        if mail and username and password:
            try:
                user = self.orm.query(User).filter(User.username == username).one()
            except NoResultFound:
                self.orm.add(User(username=username, mail=mail,
                                  password=bcrypt.hashpw(password.encode('utf8'), bcrypt.gensalt())))
                self.orm.commit()
                try:
                    inviteUser = self.orm.query(User).filter(User.username == invite_user).one()
                    inviteUser.integral += 10
                    self.orm.commit()
                except NoResultFound:
                    pass
                self.redirect('/login')
        else:
            return self.render('register.html', danger=1, ques=self.application.question, uuid=self.application.uuid)


class ResetPasswordHanlder(BaseHandler):
    def get(self, *args, **kwargs):
        self.application._generate_captcha()
        return self.render('reset.html', ques=self.application.question, uuid=self.application.uuid)

    def post(self, *args, **kwargs):
        if not self.check_captcha():
            return self.render('reset.html', danger=1, ques=self.application.question, uuid=self.application.uuid)
        return self.redirect('/login')


class changePasswordHandler(BaseHandler):
    def get(self):
        return self.render('change.html')

    def post(self, *args, **kwargs):
        old_password = self.get_argument('old_password')
        password = self.get_argument('password')
        password_confirm = self.get_argument('password_confirm')
        print old_password, password, password_confirm
        user = self.orm.query(User).filter(User.username == self.current_user).one()
        if password == password_confirm:
            if user.check(old_password):
                user.password = bcrypt.hashpw(password.encode('utf8'), bcrypt.gensalt())
                self.orm.commit()
                return self.render('change.html', success=1)
        return self.render('change.html', danger=1)


class UserInfoHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        user = self.orm.query(User).filter(User.username == self.current_user).one()
        return self.render('user.html', user=user)


class UserLogoutHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        self.clear_cookie('username')
        self.redirect('/login')


class UploadHandler(BaseHandler):
    def get(self):
        self.render('upload.html',flag="")

    def post(self):
        file_metas = self.request.files["file"]
        if not file_metas:
            self.finish()
        print(file_metas)
        for meta in file_metas:
            print meta
            file_name = meta['filename']

            print file_name
            with open(file_name, 'wb') as up:
                up.write(meta['body'])

            if (os.path.splitext(file_name)[1][1:] == 'yml'):
                f = os.path.abspath(file_name)
                flag=yaml.load(file(f,'r'))
                self.render('upload.html',flag=flag)
            else:
                print self.redirect('/user')
        print "OK, file uploaded successfully!"
        self.redirect('/user')
