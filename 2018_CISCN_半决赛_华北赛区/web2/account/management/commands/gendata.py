from django.core.management import BaseCommand

from account.models import User
from sshop.models import Commodity, ShopCar


class Command(BaseCommand):
    def handle(self, *args, **options):
        Commodity.objects.create(name='Test1', amount=1000, price=10, desc='22333')
        Commodity.objects.create(name='Test2', amount=1000, price=10, desc='33333')
        ShopCar.objects.all().delete()
        user = User.objects.all()
        user.delete()
        user = User.objects.create_user(username='admin', integral=500001)
        user.set_password('i2!kZk&l$q5i8PA')
        user.save()
        print("初始化数据结束!")
