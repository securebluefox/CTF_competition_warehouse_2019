from Shop import *
from User import *
from Captcha import *
from ..base import  *

handlers = [

    (r'/', ShopIndexHandler),
    (r'/shop', ShopListHandler),
    (r'/info/(\d+)', ShopDetailHandler),
    (r'/seckill', SecKillHandler),
    (r'/shopcar', ShopCarHandler),
    (r'/shopcar/add', ShopCarAddHandler),
    (r'/pay', ShopPayHandler),
    (r'/kill/(.*?)', KillDetailHandler),
    (r'/captcha', CaptchaHandler),
    (r'/getrand/(.*?)', GetRandomHander),
    (r'/user', UserInfoHandler),
    (r'/user/change', changePasswordHandler),
    (r'/pass/reset', ResetPasswordHanlder),
    (r'/buy/(\d+)', ShopBuyDetailHandler),
    (r'/login', UserLoginHanlder),
    (r'/logout', UserLogoutHandler),
    (r'/register', RegisterHandler),
    (r".*", BaseHandler)
]