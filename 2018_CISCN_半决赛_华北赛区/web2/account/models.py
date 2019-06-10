from django.contrib.auth.models import AbstractUser
from django.db import models

# Create your models here.


class User(AbstractUser):
    name = models.CharField(max_length=32, blank=True)
    integral = models.PositiveIntegerField(default=1000)
    is_seckilled = models.BooleanField(default=False)
    is_invited = models.BooleanField(default=False)
    ques_str = models.CharField(max_length=16, blank=True)
    ans_str = models.CharField(max_length=8, blank=True)

    class Meta:
        db_table = 'system_user'

