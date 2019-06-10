import tornado.web
from sqlalchemy.orm.exc import NoResultFound
from sshop.base import BaseHandler
from sshop.models import Commodity, User
from sshop.settings import limit
import requests


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


class ShopPayHandler(BaseHandler):
    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        headers = {'Content-Type': 'application/x-www-form-urlencoded',
                   'User-Agent': 'Pig-Peggy Shop'}
        try:
            id = 1
            to_addr = "ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ="
            commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            price = commodity.price
            if id:
                user = self.orm.query(User).filter(User.username == self.current_user).one()
                from_addr = user.wallet_addr
                msg = user.username
                priv_key = user.private_key
                data = 'from=%s&to=%s&commodity_id=%s&msg=%s&privkey=%s' % (from_addr, to_addr, id, msg, priv_key)
                response = requests.post(url='http://127.0.0.1:8081/createTx', headers=headers,
                                         data=data, timeout=60)
                if '{"txid":"-1"}' not in response.content:
                    user.exp += price
                    user.integral -= price
                    self.orm.commit()
                    return self.render('pay.html', success=1)
                else:
                    self.orm.commit()
                    return self.render('pay.html', danger=1)
            else:
                return self.render('pay.html', danger=1)
        except Exception as e:
            return self.render('pay.html', danger=1)


class PayApiHandler(BaseHandler):
    def get(self, *args, **kwargs):
        try:
            id = self.get_argument('id')
            commodity = self.orm.query(Commodity).filter(Commodity.id == int(id)).one()
            return self.write(str(commodity.price) + ',' + str(commodity.amount))
        except Exception as e:
            return self.write('0,0')


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
        headers = {'Content-Type': 'application/x-www-form-urlencoded',
                   'User-Agent': 'Pig-Peggy Shop'}
        try:
            id = self.get_secure_cookie('commodity_id')
            to_addr = "ADGKJN2DH5OZ7EAU2WVS4RP3DPFH5U57MBW4LE6GRFORAVQBKEP7MD5TPWXFFNJTRYA6JLENJKRFHU3XDIGLPXXI4PTORDT3YSESCRWKLMQQRNQ="
            commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            price = commodity.price
            if id:
                user = self.orm.query(User).filter(User.username == self.current_user).one()
                from_addr = user.wallet_addr
                msg = user.username
                priv_key = user.private_key
                data = 'from=%s&to=%s&commodity_id=%s&msg=%s&privkey=%s' % (from_addr, to_addr, id, msg, priv_key)
                response = requests.post(url='http://127.0.0.1:8081/createTx', headers=headers,
                                         data=data, timeout=60)
                if '{"txid":"-1"}' not in response.content:
                    user.exp += price
                    user.integral -= price
                    self.orm.commit()
                    return self.render('shopcar.html', success=1)
                else:
                    self.orm.commit()
                    return self.redirect('/shopcar')
            else:
                return self.redirect('/shopcar')
        except Exception as e:
            return self.render('shopcar.html', danger=1)


class ShopCarAddHandler(BaseHandler):
    def post(self, *args, **kwargs):
        id = self.get_argument('id')
        self.set_secure_cookie('commodity_id', id)
        return self.redirect('/shopcar')


class SecKillHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        return self.render('seckill.html')

    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        try:
            id = self.get_argument('id')
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            if user.exp >= 10000:
                self.write('<html><h1 align="center">flag:' + open('/flag').read() + "</h1></html>")
            commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            commodity.amount -= 1
            self.orm.commit()
            return self.render('seckill.html', success=1)
        except:
            return self.render('seckill.html', danger=1)


class Source(BaseHandler):
    def get(self, *args, **kwargs):
        self.set_header('Content-Type', 'application/octet-stream')
        self.set_header('Content-Disposition', 'attachment; filename=backup.tar.gz')
        with open('/backup.tar.gz', 'rb') as fp:
            while True:
                data = fp.read(1024)
                if not data:
                    break
                self.write(data)
        self.flush()
