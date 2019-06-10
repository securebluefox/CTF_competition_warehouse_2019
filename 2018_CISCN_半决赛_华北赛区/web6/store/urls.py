from django.conf.urls import include, url
from store.views import *


urlpatterns = [
    #url(r'^$', index, name='index'),
    url(r'^$', index, name='index'),
    url(r'^products/$', products, name='products'),
    url(r'^info/(?P<cloid>[0-9]+)$', info, name='info'),
    url(r'^register$', do_reg, name='register'),
    url(r'^login$', do_login, name='login'),
    url(r'^login/$', do_login, name='login'),
    url(r'^logout/$', do_logout, name='logout'),
    url(r'^shopcar$', pay_car, name='view_cart'),
    url(r'^shopcar/add$', add_cart, name='add_cart'),
    url(r'^clean_cart/$', cleanCart, name='clean_cart'),
    url(r'^user$', userinfo, name='user'),
    url(r'^shop$', shoplist, name='shop'),
    url(r'^paycar$', pay_car, name='paycar'),
    url(r'^buydetails$', tobuydetails, name='buydetails'),
    url(r'^pay$', pay, name='pay'),
    url(r'^user/change$',changepass,name='changepass'),
    url(r'^pass/reset$',resetpass,name='resetpass'),
    url(r'^seckill$',seckill,name='seckill'),
    url(r'^search$',search,name='search'),
]
