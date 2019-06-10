from django.db import models
# Create your models here.
from account.models import User


class Commodity(models.Model):
    price = models.PositiveIntegerField(default=0)
    desc = models.CharField(max_length=64, blank=True)
    name = models.CharField(max_length=16)
    amount = models.PositiveIntegerField(default=0)

    class Meta:
        db_table = 'commodity'


class ShopCar(models.Model):
    user = models.OneToOneField(User, related_name='shop_car_user', on_delete=models.DO_NOTHING)
    commodity = models.ForeignKey(Commodity, related_name='commodity_shop_car', on_delete=models.DO_NOTHING, null=True)

    class Meta:
        db_table = 'shop_car'


class Order(models.Model):
    user = models.ForeignKey(User, related_name='order_user', on_delete=models.DO_NOTHING)
    commodity = models.ManyToManyField(Commodity)
    created_time = models.DateTimeField(auto_created=True)
    total_price = models.PositiveIntegerField()

    class Meta:
        db_table = 'order'
