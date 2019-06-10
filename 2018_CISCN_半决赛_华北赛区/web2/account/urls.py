from django.conf.urls import url
from django.urls import path

from . import views

urlpatterns = [
    url(r'^login?$', views.ClientLoginView.as_view(), name='login'),
    url(r'^/?$', views.index, name='index'),
    url(r'^logout?$', views.logout, name='logout'),
    url(r'^register?$', views.RegisterView.as_view(), name='register'),
    url(r'^user/change?$', views.ChangeView.as_view(), name='change'),
    url(r'^shop?$', views.shop, name='shop'),
    url(r'^shopcar?$', views.ShopCarView.as_view(), name='shopcar'),
    url(r'^pay?$', views.pay, name='pay'),
    url(r'^shopcar/add?$', views.add_shop_car, name='shopcar_add'),
    url(r'^user?$', views.UserView.as_view(), name='user'),
    url(r'^seckill?$', views.SecKillView.as_view(), name='seckill'),
    path('info/<int:commodity_id>', views.ShopDetailView.as_view(), name='shop_detail'),
    url(r'^pass/reset?$', views.PassResetView.as_view(), name='pass_reset_view'),
    url(r'^suggestion?$', views.SuggestionView.as_view(), name='suggestion_view')
]
