# coding: utf-8
from django.shortcuts import render,redirect,HttpResponseRedirect,HttpResponse
from store.models import *
from django.conf import settings
import logging
from django.core.paginator import Paginator,InvalidPage,EmptyPage,PageNotAnInteger
from store.forms import *
from django.contrib.auth import logout,login,authenticate
from django.contrib.auth.hashers import make_password
from django.contrib import messages
import random
import string
logger = logging.getLogger('store.views')
from .captcha import *

def authenticated_view(function):
  def wrap(request, *args, **kwargs):
      if request.user.is_authenticated():
          return function(request)
      else:
          cap = Captcha()
          cap.generate_captcha()
          request.session["uuidl"] = cap.uuid
          login_form = LoginForm()
          return render(request, 'login.html', locals())
  wrap.__doc__=function.__doc__
  wrap.__name__=function.__name__
  return wrap

def global_setting(request):
    #站点信息
    MEDIA_URL = settings.MEDIA_URL
    #热销榜
    hot_list = Clothing.objects.all().order_by('-sales')[:4]
    #购物车
    cart = request.session.get(request.user.id, None)
    return locals()

#单件购买
def pay(request):
    if request.method == 'POST':
        price=request.POST.get('price', 10000)
        cloid=request.session.get("cloid")
        clo = Clothing.objects.get(pk=cloid)
        user = request.user
        if clo.num>0 and user.integral>=clo.price:
            clo.num-=1
            clo.sales += 1
            user.integral -=clo.price
            buydetails = Buydetails.objects.create(
                user=user,
                clothing=clo,
                buynum=1,
                buypay=clo.price,
            )
            buydetails.save()
            clo.save()
            user.save()
    info(request, cloid)
    return render(request, 'single.html', locals())

#秒杀
def seckill(request):
    if request.method == 'POST':
        try:
            cloid=request.POST.get('id')
            clo = Clothing.objects.get(pk=cloid)
            print(cloid,clo.name,clo.num,clo.price)
            user = request.user
            if clo.num > 0 and user.integral>=clo.price:
                clo.num -= 1
                clo.sales+=1
                user.integral -= clo.price
                buydetails = Buydetails.objects.create(
                    user=user,
                    clothing=clo,
                    buynum=1,
                    buypay=clo.price,
                )
                buydetails.save()
                clo.save()
                user.save()
        except:
            messages.error(request, "秒杀失败")
    clo_list = Clothing.objects.all().filter(num__gt=0)
    clo_list = getPage(request, clo_list)
    return render(request, 'list.html', locals())

#重置密码
def resetpass(request):
    if request.method == 'POST':
        try:
            email=request.POST.get('mail')
            try:
                user=User.objects.get(email=email)
            except:
                user = None
            print("user:",user)
            cap2 = Captcha()
            uuid = request.session.get("resetpassuuid", None)
            x = request.POST.get('captcha_x')
            y = request.POST.get('captcha_y')
            print("uuid:", uuid, "x:", x, "y:", y)
            cap2.set_uuid(uuid)
            print("ch:", cap2.check(x,y))
            password = ''.join(random.sample(string.ascii_letters, 10))
            print("uuid:", uuid,"x:",x,"y:",y,"user:",user,"email:",email)
            if user is not None and cap2.check(x,y):
                user.password=make_password(password)
                user.save()
                messages.success(request, "%s密码已经重置为：%s"%(user.username,password))
                cap = Captcha()
                cap.generate_captcha()
                if not user.is_superuser:
                    user.backend = 'django.contrib.auth.backends.ModelBackend'  # 指定默认的登录验证方式
                    login(request, user)
                print(user.username,"的密码已经修改！:",password)
                return render(request, 'resetpass.html', locals())
            else:
                messages.error(request, "密码重置失败2")
        except:
            messages.error(request, "密码重置失败")
    else:
        pass
    cap = Captcha()
    cap.generate_captcha()
    request.session["resetpassuuid"]=cap.uuid
    x=request.session
    return render(request, 'resetpass.html', locals())

#修改密码
def changepass(request):
    changePass_form=ChangePassForm()
    if request.method == 'POST':
        changePass_form = ChangePassForm(request.POST)
        if changePass_form.is_valid():
            user = request.user
            user.password=make_password(changePass_form.cleaned_data["password"])
            user.save()
            messages.success(request, "密码已经修改！")
            print("密码已经修改！:",changePass_form.cleaned_data["password"])
        else:
            messages.error(request, "用户名已存在或验证码错误!")
            return render(request, 'changepass.html', locals())
    else:
        return render(request, 'changepass.html', locals())
    return render(request, 'resetpass.html', locals())

#购买记录
def tobuydetails(request):
    userbuy=request.user
    mybuydetails=userbuy.buydetails_set.all()
    clo_list = getPage(request, mybuydetails)
    return render(request, 'buydetails.html', locals())

#购物车付款
@authenticated_view
def pay_car(request):
    if request.method == 'POST':
        cart = request.session.get(request.user.id, None)
        user = request.user
        if(user.integral>cart.total_price):
            cartnew = Cart()
            for item in cart.items:
                clo=Clothing.objects.get(pk=item.clothing.id)
                if(clo.num>=item.quantity):
                    user.integral -= (clo.price*item.quantity)
                    clo.num-=item.quantity
                    clo.sales+=item.quantity
                    buydetails = Buydetails.objects.create(
                        user=user,
                        clothing=clo,
                        buynum=item.quantity,
                        buypay=clo.price*item.quantity,
                    )
                    buydetails.save()
                    clo.save()
                else:
                    messages.error(request, "商品库存不足!")
            user.save()
            request.session[request.user.id] = cartnew
            cart=cartnew
        else:
            messages.error(request, "用户金额不足!")
    else:
        cart = request.session.get(request.user.id, None)
    return render(request, "checkout.html", locals())

#搜索商品
def search(request):
    keyword=request.GET.get("searchstr")
    coutstr= "Hello {user}, This is your search: "+request.GET.get("searchstr")
    coutstr=coutstr.format(user=request.user)
    clo_list=Clothing.objects.filter(name__icontains=keyword)
    clo_list = getPage(request, clo_list)
    return render(request, "list.html", locals())

#主页
def index(request):
    clo_list = Clothing.objects.all()[:15]
    clo_list = getPage(request,clo_list)
    return render(request,"index.html",locals())

#商品列表
def shoplist(request):
    clo_list = Clothing.objects.all().filter(num__gt = 0 )
    clo_list = getPage(request, clo_list)
    return render(request, "list.html", locals())

#用户信息
def userinfo(request):
    return render(request, "userinfo.html", locals())

#产品列表页
def products(request):
    try:
        clo_list = Clothing.objects.all().order_by("price")[:10]
        clo_list = getPage(request,clo_list)
    except Exception as e:
        logger.error(e)
    return render(request, 'products.html', locals())

#商品详情页
def info(request,cloid):
    try:
        did = cloid
        try:
            clo = Clothing.objects.get(pk=did)
            request.session["cloid"]=cloid
        except Clothing.DoesNotExist:
            return render(request, 'error.html', {"reason": "商品不存在"})
    except Exception as e:
        logger.error(e)
    return render(request, 'single.html', locals())

#注册
def do_reg(request):
    cap = Captcha()
    cap.generate_captcha()
    try:
        if request.method == 'POST':
            reg_form = RegForm(request.POST)
            if reg_form.is_valid():
                uuid = request.session.get("uuidl",None)
                uuid2 = request.session.get("uuidR", None)
                invite_user = request.POST.get('invite_user', None)
                cap2=Captcha()
                cap2.set_uuid(uuid)
                cap3 = Captcha()
                cap3.set_uuid(uuid2)
                x = request.POST.get('captcha_x')
                y = request.POST.get('captcha_y')
                try:
                    user1 = User.objects.get(username = reg_form.cleaned_data["username"])
                    messages.error(request, "用户名已存在!")
                except:
                    user1=None
                try:
                    user2 = User.objects.get(email = reg_form.cleaned_data["mail"])#邮箱不可重复，找回密码使用
                    messages.error(request, "邮箱已存在!")
                except:
                    user2=None
                if user1 is None and user2 is None and( cap2.check(x,y) or cap3.check(x,y)):
                    user = User.objects.create(username=reg_form.cleaned_data["username"],
                                    email=reg_form.cleaned_data["mail"],
                                    password=make_password(reg_form.cleaned_data["password"]),
                                    invite_user=invite_user,
                                               )
                    user.save()
                    try:
                        user3 = User.objects.get(username=invite_user)
                    except:
                        user3 = None
                    if user3 is not None:
                        user3.integral=user3.integral+10
                        user3.save()
                    clothingcount=Clothing.objects.count()
                    print(clothingcount)
                    if clothingcount>15:#商品多于15个后随机2%概率添加商品
                        randomnum=random.randint(0,50)
                        if randomnum==5:#如果随机数等于5添加商品
                            clothing=Clothing.objects.create(name = "商品："+''.join(random.sample(string.ascii_letters, 16)),
                                        old_price = 100.0,
                                        price =90.0,
                                        discount = 0.9,
                                        num = 5,
                                        )
                            clothing.save()
                        else:
                            pass
                    else:
                        clothing = Clothing.objects.create(
                            name="商品：" + ''.join(random.sample(string.ascii_letters, 16)),
                            old_price=100.0,
                            price=90.0,
                            discount=0.9,
                            num=5,
                            )
                        clothing.save()
                    reg_form = RegForm()
                    return redirect(request.POST.get('source_url'))
                else: #出现错误的时候，重新加载验证
                    messages.error(request, "用户名已存在或邮箱已存在或验证码错误!")
            else:
                return render(request,'register.html',locals())
    except Exception as e:
        logger.error(e)
    request.session["uuidR"] = cap.uuid
    reg_form = RegForm()
    return render(request,'register.html',locals())

#登录
def do_login(request):
    try:
        cap=Captcha()
        cap.generate_captcha()
        if request.method == 'POST':
            login_form = LoginForm(request.POST)
            if login_form.is_valid():
                username = login_form.cleaned_data["username"]
                password = login_form.cleaned_data["password"]
                cap2 = Captcha()
                uuid = request.session.get("uuidl", None)
                cap2.set_uuid(uuid)
                x=request.POST.get('captcha_x')
                y=request.POST.get('captcha_y')
                user = authenticate(username=username, password=password)#验证用户
                if user is not None and cap2.check(x,y):
                    if not user.is_superuser:#前端不允许管理员登录
                        user.backend = 'django.contrib.auth.backends.ModelBackend'  # 指定默认的登录验证方式
                        login(request, user)
                    else:
                        messages.error(request, "此用户不允许在此登陆!")
                    #return render(request, 'login.html', locals())
                    #return redirect(request, "/")
                else:
                    messages.error(request, "用户名或密码错误或验证码错误!")
    except Exception as e:
        logger.error(e)
    login_form = LoginForm()
    request.session["uuidl"] = cap.uuid
    request.session["uuidR"] = cap.uuid
    print("uuidl A:", cap.uuid)
    return render(request, 'login.html', locals())

#退出
def do_logout(request):
    try:
        logout(request)
    except Exception as e:
        logger.error(e)
    cap = Captcha()
    cap.generate_captcha()
    login_form = LoginForm()
    return render(request, 'login.html', locals())

#查看购物车
@authenticated_view
def view_cart(request):
    cart = request.session.get(request.user.id, None)
    return render(request, 'checkout.html', locals())

#添加购物车
@authenticated_view
def add_cart(request):
    try:
        chid = request.POST.get('id',None)
        try:
            clothing = Clothing.objects.get(pk=chid)
        except Clothing.DoesNotExist:
            return render(request, 'error.html', {'reason':'商品不存在'})
        cart = request.session.get(request.user.id,None)
        if not cart:
            cart = Cart()
            cart.add(clothing)
            request.session[request.user.id] = cart
        else:
            cart.add(clothing)
            request.session[request.user.id] = cart
    except Exception as e:
        logger.error(e)
    return render(request, 'checkout.html', locals())

#清空购物车
@authenticated_view
def cleanCart(request):
    cart = Cart()
    request.session[request.user.id] = cart
    return render(request, 'checkout.html', locals())

@authenticated_view
def clean_one_item(request, id):
    item = None
    try:
     item = Clothing.objects.get(pk=id)
    except Clothing.DoesNotExist:
        pass
    if item:
        item.delete()
    cart = request.session.get(request.user.id, None)
    return render(request, 'checkout.html', {'cart':cart})


#分页
def getPage(request,clo_list):
    paginator = Paginator(clo_list,8)
    try:
        page = int(request.GET.get('page',1))
        clo_list = paginator.page(page)
    except (EmptyPage,InvalidPage,PageNotAnInteger):
        clo_list = paginator.page(1)
    return clo_list
