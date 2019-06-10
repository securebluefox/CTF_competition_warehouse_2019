# coding: utf-8
from django.contrib import admin
from store.models import *

class ClothingAdmin(admin.ModelAdmin):
    list_display = ('name','price','num',)
    fieldsets = (
        ('None',{'fields':('name','size','old_price',
                           'price','sales','details','num','image_url_i',
                           'image_url_l','image_url_m','image_url_r','image_url_c',)}),
    )
# admin.site.register(User)
# admin.site.register(Buydetails)
# admin.site.register(Clothing,ClothingAdmin)