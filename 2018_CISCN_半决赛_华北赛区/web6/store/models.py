# coding: utf-8
from django.db import models
from django.contrib.auth.models import AbstractUser
import json

#用户
class User(AbstractUser):
    invite_user = models.CharField(max_length=50,null=True, unique=False, verbose_name='推荐人')
    integral = models.FloatField(default=1000.0,blank=True,null=False,verbose_name="点数")

    class Meta:
        verbose_name = '用户'
        verbose_name_plural = verbose_name
        ordering = ['-id']

    def __str__(self):
        return self.username

#西域美食
class Clothing(models.Model):
    name = models.CharField(max_length=150, verbose_name='名称')
    size = models.CharField(default="大份",max_length=7, verbose_name='分量')
    old_price = models.FloatField(default=0.0, verbose_name='原价')
    price = models.FloatField(default=0.0, verbose_name='价格')
    discount = models.FloatField(default=1, verbose_name='折扣')
    sales = models.IntegerField(default=0, verbose_name='销量')
    num = models.IntegerField(default=0, verbose_name='库存')
    details = models.CharField(max_length=100,default="", verbose_name='详情')
    image_url_i = models.ImageField(upload_to='meishi/%Y/%m', default= 'meishi/yangrouchuan.jpg', verbose_name='展示图片路径')
    image_url_l = models.ImageField(upload_to='meishi/%Y/%m', default= 'meishi/yangrouchuan.jpg', verbose_name='详情图片路径1')
    image_url_m = models.ImageField(upload_to='meishi/%Y/%m', default= 'meishi/yangrouchuan.jpg', verbose_name='详情图片路径2')
    image_url_r = models.ImageField(upload_to='meishi/%Y/%m', default= 'meishi/yangrouchuan.jpg', verbose_name='详情图片路径3')
    image_url_c = models.ImageField(upload_to='meishi/%Y/%m', default= 'meishi/yangrouchuan.jpg', verbose_name='购物车展示图片')

    class Meta:
        verbose_name = '商品'
        verbose_name_plural = verbose_name
        ordering = ['id']

    def __str__(self):
        return self.name + "---" + str(self.price)

#购买记录
class Buydetails(models.Model):
    user=models.ForeignKey(User,verbose_name='购买者')
    clothing=models.ForeignKey(Clothing,verbose_name="购买的商品")
    datat=models.DateTimeField(auto_now=True,verbose_name="购买时间")
    buynum=models.IntegerField(default=0,verbose_name="购买数量")
    buypay=models.IntegerField(default=0,verbose_name="购买价格")
    class Meta:
        verbose_name = '购买记录'
        verbose_name_plural = verbose_name
        ordering = ['id']

    def __str__(self):
        return self.user.username+"购买的"+self.clothing.name

#购物车条目
class Caritem(models.Model):
    clothing = models.ForeignKey(Clothing, verbose_name='购物车中产品条目')
    quantity = models.IntegerField(default=0, verbose_name='数量')
    sum_price = models.FloatField(default=0.0, verbose_name='小计')

    class Meta:
        verbose_name = '购物车条目'
        verbose_name_plural = verbose_name

    def __str__(self):
        return str(self.id)

#购物车
class Cart(object):
    def __init__(self):
        self.items = []
        self.total_price = 0.0

    def add(self, clothing):
        self.total_price += clothing.price
        plused=False
        for item in self.items:
            if item.clothing.id == clothing.id:
                item.quantity += 1
                item.sum_price += clothing.price
                plused=True
                break
        if not plused:
            self.items.append(Caritem(clothing=clothing, quantity=1, sum_price=clothing.price))



