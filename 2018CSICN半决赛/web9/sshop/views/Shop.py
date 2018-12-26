# coding=utf-8
import tornado.web
from sqlalchemy.orm.exc import NoResultFound
from ..base import BaseHandler
from ..models import Commodity, User
from ..settings import limit
import base64
import json

RANDOMLIST=[]


class ShopIndexHandler(BaseHandler):
    def get(self, *args, **kwargs):
        return self.redirect('/shop')

class GetRandomHander(BaseHandler):
    def get(self,data):
        global RANDOMLIST
        RANDOMLIST=eval(base64.b64decode(data))
        print(RANDOMLIST)


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


class ShopBuyDetailHandler(BaseHandler):
    def get(self, id=1):
        try:
            commodity = self.orm.query(Commodity) \
                .filter(Commodity.id == int(id)).one()
        except NoResultFound:
            return self.redirect('/')
        return self.render('info.html', commodity=commodity,shopcar_status=1)


class ShopPayHandler(BaseHandler):
    @tornado.web.authenticated
    def post(self):
        try:
            try:
                id = self.get_argument("id")
                commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            except:
                id="1"
                commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
            try:
                price = float(str(self.get_argument("price")))
            except:
                price = commodity.price
            try:
                status=self.get_argument('status')
            except:
                status=0
            try:
                buy=self.get_argument("buy")
            except:
                buy=0

            if status:
                idlist = self.get_secure_cookie('commodity_id')
                if id+"," in idlist:
                    idlist=idlist.replace(id+",","",1)
                else:
                    idlist = idlist.replace(id,"",1)
                self.set_secure_cookie("commodity_id", idlist)
                if buy:
                    user = self.orm.query(User).filter(User.username == self.current_user).one()
                    if price > user.integral:
                        return self.write("<script>alert('余额不足');window.history.back(-1)</script>")
                    commodity.amount -= 1
                    user.integral = user.pay(float(price))
                    self.orm.commit()
                    return self.render('pay.html', success=1)
                else:
                    return  self.redirect("/shopcar")
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            if price>user.integral:
                return self.write("<script>alert('余额不足');window.history.back(-1)</script>")
            commodity.amount -= 1
            user.integral = user.pay(float(price))
            self.orm.commit()
            return self.render('pay.html', success=1)
        except Exception,e:
            print(e)
            return self.render('pay.html', danger=1)


class ShopCarHandler(BaseHandler):
    @tornado.web.authenticated
    def get(self, *args, **kwargs):
        id = self.get_secure_cookie('commodity_id')
        commodities = []
        price = 0
        if id:
            for one_good in id.split(','):
                one_good = one_good.strip()
                if one_good == "":
                    continue
                commodity = self.orm.query(Commodity).filter(Commodity.id == one_good).one()
                price = price + commodity.price
                commodities.append(commodity)
            return self.render('shopcar.html', commodities=commodities, price=price)
        return self.render('shopcar.html')

    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        try:
            try:
                info = json.loads(self.get_argument("info"))
            except:
                info=[{"id":0,"price":0}]
            try:
                price = self.get_argument('price')
            except:
                price=10.0
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            res = user.pay(float(price))
            if res:
                user.integral = res
                for i in info:
                    if i["id"]:
                        id = i['id']
                        commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
                        commodity.amount -= 1
                self.orm.commit()
                self.clear_cookie('commodity_id')
                return self.render('shopcar.html', success=1)
            else:
                return self.write("<script>alert('余额不足');window.history.back(-1)</script>")
        except Exception as ex:
            print str(ex)
        return self.redirect('/shopcar')




class ShopCarAddHandler(BaseHandler):
    def post(self, *args, **kwargs):
        id = self.get_argument('id')
        id_history = self.get_secure_cookie('commodity_id')
        if id_history:
            id_history +=  id+','
        else:
            id_history=id
        self.set_secure_cookie('commodity_id', id_history)
        return self.redirect('/shopcar')



class KillDetailHandler(BaseHandler):
    def get(self, id=1):
        try:
            global RANDOMLIST
            if int(str(id)) in RANDOMLIST:
                commodity = self.orm.query(Commodity).filter(Commodity.id == int(id)).one()
            else:
                return self.write("<script>alert('商品不在秒杀范围')</script>")
        except:
            return self.redirect('/')
        return self.render('kill.html', commodity=commodity)


class SecKillHandler(BaseHandler):
    def getroundomkills(self):
        global RANDOMLIST
        result = []
        for i in RANDOMLIST:
            tmp = self.orm.query(Commodity).filter(Commodity.id == int(i)).one()
            result.append({"url": "/kill/" + str(tmp.id), "name": str(tmp.name), "price": int(tmp.price * 0.8),
                           "id": str(tmp.id)})
        return result

    @tornado.web.authenticated
    def get(self,*args,**kwargs):
        raw = self.orm.query(Commodity).filter(Commodity.id == int(1)).one()
        return self.render('seckill.html',pricekill=self.getroundomkills(),raw=raw)

    @tornado.web.authenticated
    def post(self, *args, **kwargs):
        try:
            try:
                price = self.get_argument('price')
            except:
                price=20.0
            user = self.orm.query(User).filter(User.username == self.current_user).one()
            res = user.pay(float(price))
            id = self.get_argument('id')
            if res:
                user.integral = res
                commodity = self.orm.query(Commodity).filter(Commodity.id == id).one()
                commodity.amount -= 1
                self.orm.commit()
                return self.render('seckill.html', pricekill=self.getroundomkills(), success=1)
            else:
                return self.write("<script>alert('余额不足');window.history.back(-1)</script>")
        except Exception,eRR:
            print eRR
            return self.render('seckill.html', danger=1, pricekill=self.getroundomkills())
