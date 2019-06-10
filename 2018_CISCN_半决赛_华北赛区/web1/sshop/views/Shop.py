import tornado.web
from sqlalchemy.orm.exc import NoResultFound
from sshop.base import BaseHandler
from sshop.models import Commodity, User
from sshop.settings import limit
import pickle
from cStringIO import StringIO
import base64
import pickle
import platform
from base64 import b64encode as b64e
import string
from hashlib import sha256
import os

class ShopIndexHandler(BaseHandler):
    def get(self, *args, **kwargs):
        return self.redirect('/shop')


class ShopListHandler(BaseHandler):
    def get(self):
        page = self.get_argument('page', 1)
        page = int(page) if int(page) else 1
        commoditys = self.orm.query(Commodity) \
            .filter(Commodity.amount > 0) \
            .order_by(Commodity.price.desc()) \
            .limit(limit).offset((page - 1) * limit).all()
        return self.render('index.html', commoditys=commoditys, preview=page - 1, next=page + 1, limit=limit)

class ShopDetailHandler(BaseHandler):
    def get(self, id=1):
        try:
            commodity = self.orm.query(Commodity) \
                .filter(Commodity.id == int(id)).one()
        except NoResultFound:
            return self.redirect('/')
        return self.render('info.html', commodity=commodity)


def loads(strs):
    reload(pickle)
    files = StringIO(strs)
    unpkler = pickle.Unpickler(files)
    return unpkler.load()

class ShopPayHandler(BaseHandler):
    @tornado.web.authenticated
    def post(self):
        try:
            price = self.get_argument('price')
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            user.integral = user.pay(float(price))
            self.orm.commit()
            try:
                id = self.get_argument("id")
                commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
                commodity.amount -= 1
                self.orm.commit()
            except Exception as e:
                pass
            return self.render('pay.html', success=1)
        except:
            return self.render('pay.html', danger=1)

class ShopCarHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        id = self.get_secure_cookie('commodity_id')
        if id:
            commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            return self.render('shopcar.html', commodity=commodity)
        return self.render('shopcar.html')

    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        try:
            price = self.get_argument('price')
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            res = user.pay(float(price))
            if res:
                user.integral = res
                self.orm.commit()
                name = self.get_cookie("name")
                name = base64.b64decode(name)
                name = loads(name)
                commodity = self.orm.query(Commodity).filter(Commodity.name == name).one()
                commodity.amount = commodity.amount - 1
                self.orm.commit()
                self.clear_cookie('commodity_id')
                self.clear_cookie('name')
                return self.render('shopcar.html', success=1)
        except Exception as ex:
            print str(ex)
        return self.redirect('/shopcar')


class ShopCarAddHandler(BaseHandler):
    def post(self, *args, **kwargs):
        id = self.get_argument('id')
        commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
        self.set_secure_cookie('commodity_id', id)
        name = commodity.name
        name = pickle.dumps(name)
        self.set_cookie('name',base64.b64encode(name))
        return self.redirect('/shopcar')

class SecKillHandler(BaseHandler):
    def get(self, *args, **kwargs):
        return self.render('seckill.html')

    def post(self, *args, **kwargs):
        try:
            id = self.get_argument('id')
            commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            commodity.amount -= 1
            if commodity.amount <0:
                return self.render('seckill.html', danger=1)
            self.orm.commit()
            return self.render('seckill.html', success=1)
        except:
            return self.render('seckill.html', danger=1)
