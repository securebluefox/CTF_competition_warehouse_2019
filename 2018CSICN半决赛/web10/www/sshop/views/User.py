import tornado.web
from sqlalchemy.orm.exc import NoResultFound
from sshop.base import BaseHandler
from sshop.models import User
from sshop.views.Tx import create_tx, get_balance
import bcrypt
import requests
import json


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
            return self.render('register.html', danger=1,  ques=self.application.question, uuid=self.application.uuid)
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
                wallet_addr = ''
                private_key = ''
                try:
                    response = requests.get(url="http://127.0.0.1:8081/createWallet")
                    data = json.loads(response.content)
                    # print data
                    wallet_addr = data['address']
                    private_key = data['privkey']
                    super_user_key = 'BOLVUKGDUBELY6YOHGLJBRUAHQUGYCAOWS7ECGDUALXUFA4SCPTA===='
                    addr = wallet_addr
                    msg = 'register'
                    super_user_addr = 'ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ='
                    create_tx(super_user_addr, addr, 1, msg, super_user_key)
                    create_tx(super_user_addr, addr, 1, msg, super_user_key)
                except:
                    return self.render('register.html', danger=1, ques=self.application.question,
                                       uuid=self.application.uuid)

                self.orm.add(User(username=username, mail=mail, wallet_addr=wallet_addr, private_key=private_key,
                                  password=bcrypt.hashpw(password.encode('utf8'), bcrypt.gensalt())))

                self.orm.commit()
                try:
                    inviteUser = self.orm.query(User).filter(User.username == invite_user).one()
                    headers = self.request.headers
                    UA = headers['User-Agent'] if 'User-Agent' in headers else ''
                    XFF = headers['X-Forwarded-For'] if 'X-Forwarded-For' in headers else ''
                    tmp_diff_hash = str(hash(XFF + UA))
                    if tmp_diff_hash != inviteUser.diff_hash:
                        inviteUser.integral += 10
                        inviteUser.invite_num += 1
                        inviteUser.diff_hash = tmp_diff_hash
                        try:
                            super_user_key = 'BOLVUKGDUBELY6YOHGLJBRUAHQUGYCAOWS7ECGDUALXUFA4SCPTA===='
                            addr = inviteUser.wallet_addr
                            msg = inviteUser.username
                            super_user_addr = 'ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ='
                            create_tx(super_user_addr, addr, 0, msg, super_user_key)
                        except:
                            pass
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
        mail = self.get_argument('mail')
        try:
            user = self.orm.query(User).filter(User.mail == mail).one()
            user.password = bcrypt.hashpw('654321'.encode('utf8'), bcrypt.gensalt())
            self.orm.commit()
        except NoResultFound:
            return self.render('reset.html', ques=self.application.question, uuid=self.application.uuid)
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
        if 'python' in self.request.headers['User-Agent']:
            return self.render('user.html', user=user)
        else:
            balance = get_balance(user.wallet_addr)
            return self.render('user.html', user=user, balance=balance)


class UserLogoutHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        self.clear_cookie('username')
        self.redirect('/login')
