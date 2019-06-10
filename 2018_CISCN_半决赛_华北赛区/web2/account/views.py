import os

from django.contrib import auth
from django.contrib.auth.decorators import login_required
from django.db import transaction
from django.forms import ALL_FIELDS
from django.http import HttpResponseRedirect
from django.shortcuts import render, render_to_response
from rest_framework import serializers
from rest_framework.decorators import api_view
from rest_framework.views import APIView

from account.models import User
from sshop.models import Commodity, ShopCar


class Captcha:
    def __init__(self):
        self.root_path = os.path.dirname(os.path.abspath('__file__'))
        self.ans_path = os.path.join(self.root_path, 'static/captcha/ans')
        self.jpeg_path = os.path.join(self.root_path, 'static/captcha/jpgs')
        self.files = self._get_files()
        self.uuid = ''
        self.question = ''

    def _get_files(self):
        return os.listdir(self.jpeg_path)

    def _get_ans(self, uuid):
        answer = {}
        with open(os.path.join(self.ans_path, 'ans%s.txt' % uuid), 'r', encoding='utf8') as f:
            for line in f.readlines():
                if line != '\n':
                    ans = line.strip().split('=')
                    answer[ans[0].strip()] = ans[1].strip()
        return answer

    def generate_captcha(self):
        uuids = []
        for file in self.files:
            uuids.append(file.replace('ques', '').replace('.jpg', ''))
        from random import choice
        uuid = choice(uuids)
        ans = self._get_ans(uuid)
        self.uuid = uuid
        self.question = ans['vtt_ques']

    def check_captcha(self, captcha_x, captcha_y, uuid):
        try:
            x = float(captcha_x)
            y = float(captcha_y)
            if x and y:
                uuid = uuid
                answer = self._get_ans(uuid)
                if float(answer['ans_pos_x_1']) <= x <= (float(answer['ans_width_x_1']) + float(answer['ans_pos_x_1'])):
                    if float(answer['ans_pos_y_1']) <= y <= (
                            float(answer['ans_height_y_1']) + float(answer['ans_pos_y_1'])):
                        return True
                return False
        except Exception as ex:
            print(str(ex))
            return False


def index(request):
    return HttpResponseRedirect('/shop')


@api_view(['GET'])
@login_required(login_url='/login')
def logout(request):
    auth.logout(request)
    return HttpResponseRedirect('/shop')


class ChangeView(APIView):
    @staticmethod
    @login_required(login_url='/login')
    def get(request):
        return render(request, './change.html')

    @staticmethod
    @login_required(login_url='/login')
    def post(request):
        data = request.data.copy()
        old_password = data['old_password']
        user = auth.authenticate(username=request.user.username, password=old_password)
        if user is None:
            return render(request, './change.html', {'danger': True, 'msg': '原始密码错误'})
        if data['password'] != data['password_confirm']:
            return render(request, './change.html', {'danger': True, 'msg': '两次密码输入不一致'})
        password = data['password']
        user.set_password(password)
        password = user.password
        User.objects.filter(id=user.id).update(password=password)
        c = Captcha()
        c.generate_captcha()
        return render(request, './change.html', {'success': True, 'msg': '修改密码成功'})


class RegisterSer(serializers.ModelSerializer):
    class Meta:
        model = User
        fields = '__all__'


class RegisterView(APIView):
    @staticmethod
    def get(request):
        c = Captcha()
        c.generate_captcha()
        is_wrong_invite_user = request.GET.get('is_wrong_invite_user', False)
        is_wrong_captcha = request.GET.get('is_wrong_captcha', False)
        is_wrong_pwd = request.GET.get('is_wrong_pwd', False)
        is_wrong_params = request.GET.get('is_wrong_params', False)

        if is_wrong_captcha:
            response = render(request, './register.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '验证码错误'})
        elif is_wrong_pwd:
            response = render(request, './register.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '两次密码不一致'})
        elif is_wrong_params:
            response = render(request, './register.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '用户名已被注册或参数错误'})
        elif is_wrong_invite_user:
            response = render(request, './register.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '邀请人不存在或已经被填写过邀请人'})
        else:
            response = render(request, './register.html', {'uuid': c.uuid, 'ques': c.question})

        response.set_cookie('uuid', c.uuid)
        return response

    @staticmethod
    def post(request):
        data = request.data.copy()
        data['uuid'] = request.COOKIES['uuid']
        ser = RegisterSer(data=data)
        c = Captcha()
        c.generate_captcha()
        invite_user = data.get('invite_user', None)
        if invite_user is not None and len(invite_user):
            with transaction.atomic():
                try:
                    i_user = User.objects.select_for_update().get(username=invite_user)
                    if i_user.is_invited:
                        raise ZeroDivisionError
                    integral = i_user.integral + 100
                    User.objects.filter(id=i_user.id).update(integral=integral, is_invited=True)
                except User.DoesNotExist:
                    return HttpResponseRedirect('/register?is_wrong_invite_user?true')
                except ZeroDivisionError:
                    return HttpResponseRedirect('/register?is_wrong_invite_user?true')
        if data['password'] != data['password_confirm']:
            return HttpResponseRedirect('/register?is_wrong_pwd?true')
        if not c.check_captcha(data['captcha_x'], data['captcha_y'], data['uuid']):
            return HttpResponseRedirect('/register?is_wrong_captcha=true')
        if ser.is_valid():
            User.objects.create_user(username=ser.data['username'], password=ser.data['password'], email=request.data['mail'])
            return HttpResponseRedirect('/login?is_success=true')
        return HttpResponseRedirect('/register?is_wrong_params=true')


@api_view(['GET'])
def shop(request):
    queryset = Commodity.objects.all()
    return render(request, './index.html', {'commodities': queryset})


class ShopCarView(APIView):
    @staticmethod
    @login_required(login_url='/login')
    def get(request):
        try:
            shop_car = ShopCar.objects.get(user_id=request.user.id)
        except ShopCar.DoesNotExist:
            shop_car = ShopCar.objects.create(user_id=request.user.id)
        queryset = shop_car.commodity
        return render(request, './shopcar.html', {'commodity': queryset})

    @staticmethod
    @login_required(login_url='/login')
    def post(request):
        with transaction.atomic():
            shop_car = ShopCar.objects.select_for_update().get(user_id=request.user.id)
            if not shop_car.commodity:
                return render(request, './shopcar.html', {'commodity': None})
            commodity = Commodity.objects.select_for_update().get(id=shop_car.commodity.id)
            user = User.objects.select_for_update().get(id=request.user.id)
            if user.integral < commodity.price:
                return render(request, './shopcar.html', {'danger': True, 'msg': '积分不足, 购买失败'})
            if commodity.amount <= 0:
                return render(request, './shopcar.html', {'danger': True, 'msg': '产品数量不足，购买失败'})
            amount = commodity.amount - 1
            integral = user.integral - commodity.price
            Commodity.objects.filter(id=commodity.id).update(amount=amount)
            User.objects.filter(id=user.id).update(integral=integral)
            ShopCar.objects.filter(id=shop_car.id).update(commodity=None)
        try:
            shop_car = ShopCar.objects.get(user_id=request.user.id)
        except ShopCar.DoesNotExist:
            shop_car = ShopCar.objects.create(user_id=request.user.id)
        queryset = shop_car.commodity
        return render(request, './shopcar.html', {'success': True, 'msg': '购买成功', 'commodity': queryset})


@api_view(['POST'])
@login_required(login_url='/login')
def add_shop_car(request):
    data = request.data.copy()
    try:
        shop_car = ShopCar.objects.get(user_id=request.user.id)
    except ShopCar.DoesNotExist:
        shop_car = ShopCar.objects.create(user_id=request.user.id)
    shop_car.commodity_id = data['id']
    shop_car.save()
    return HttpResponseRedirect('/shopcar')


@api_view(['POST'])
@login_required(login_url='/login')
def pay(request):
    data = request.data.copy()
    price = data.get('price', None)
    if not price:
        price = 20
    i = request.user.integral - int(price.replace('.0', ''))
    print(i)
    if i >= 0:
        User.objects.filter(id=request.user.id).update(integral=i)
    commodity = request.data.get('id', None)
    if commodity:
        commodity = Commodity.objects.get(id=int(commodity))
        amount = commodity.amount - 1
        if amount >= 0:
            Commodity.objects.update(amount=amount)
    return render(request, './pay.html')


class UserLoginSerializer(serializers.Serializer):
    username = serializers.CharField(max_length=15)
    password = serializers.CharField(max_length=15)


class ClientLoginView(APIView):
    @staticmethod
    def get(request, **kwargs):
        c = Captcha()
        c.generate_captcha()
        is_wrong_captcha = request.GET.get('is_wrong_captcha', False)
        is_wrong_pwd = request.GET.get('is_wrong_pwd', False)
        is_wrong_params = request.GET.get('is_wrong_params', False)
        is_success = request.GET.get('is_success', False)

        if is_wrong_captcha:
            response = render(request, './login.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '验证码错误'})
        elif is_wrong_pwd:
            response = render(request, './login.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '用户名不存在或密码错误'})
        elif is_wrong_params:
            response = render(request, './login.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '参数错误'})
        elif is_success:
            response = render(request, './login.html', {'uuid': c.uuid, 'ques': c.question, 'success': True, 'msg': '注册成功'})
        else:
            response = render(request, './login.html', {'uuid': c.uuid, 'ques': c.question})

        response.set_cookie('uuid', c.uuid)
        return response

    @staticmethod
    def post(request, **kwargs):
        c = Captcha()
        c.generate_captcha()
        data = request.data.copy()
        data['uuid'] = request.COOKIES['uuid']
        if not c.check_captcha(data['captcha_x'], data['captcha_y'], data['uuid']):
            return HttpResponseRedirect('/login?is_wrong_captcha=true')
        ser = UserLoginSerializer(data=data)
        if ser.is_valid():
            username = ser.data['username']
            password = ser.data['password']
            user = auth.authenticate(username=username, password=password)
            print(username, password, user)
            if user is not None:
                request.session.set_expiry(60 * 60)
                auth.login(request, user)
                return HttpResponseRedirect('/user')
            else:
                return HttpResponseRedirect('/login?is_wrong_pwd=true')
        else:
            return HttpResponseRedirect('/login?is_wrong_params=true')


class UserView(APIView):
    @staticmethod
    @login_required(login_url='/login')
    def get(request, **kwargs):
        is_show_suggestion = False
        if request.user.integral >= 50000:
            is_show_suggestion = True
        return render(request, './user.html', {'is_show_suggestion': is_show_suggestion})


class ShopView(APIView):
    @staticmethod
    def get(request, **kwargs):
        queryset = Commodity.objects.all()
        return render(request, './index.html', {'commodities': queryset})


class ShopDetailView(APIView):
    @staticmethod
    def get(request, commodity_id, **kwargs):
        commodity = Commodity.objects.get(id=commodity_id)
        return render(request, './info.html', {'commodity': commodity})


class SecKillView(APIView):
    @staticmethod
    @login_required(login_url='/login')
    def get(request):
        if request.user.is_seckilled:
            import string
            import random
            import hashlib
            ques_str = ''.join(random.sample(string.ascii_letters + string.digits, 16))
            salt_str = ''.join(random.sample(string.ascii_letters + string.digits, 6))
            # print(salt_str)
            m = hashlib.sha256()
            m.update(f'{ques_str}{salt_str}'.encode('utf8'))
            ans_str = str(m.hexdigest())[:6]
            with transaction.atomic():
                user = User.objects.select_for_update().get(id=request.user.id)
                User.objects.filter(id=user.id).update(ques_str=ques_str, ans_str=ans_str)
            is_success = request.GET.get('success', False)
            is_fail = request.GET.get('fail', False)
            if is_success:
                return render(request, './rel_seckill.html', {'question': f'请输入{ques_str} + 6位字符串(包含数字区分大小写) s, 使得sha256(s) 前6位是{ans_str}', 'success': True, 'msg': '恭喜您秒杀成功获取1000积分奖励'})
            if is_fail:
                return render(request, './rel_seckill.html', {'question': f'请输入{ques_str} + 6位字符串(包含数字区分大小写) s, 使得sha256(s) 前6位是{ans_str}', 'danger': True, 'msg': '您输入的字符串有误'})
            return render(request, './rel_seckill.html', {'question': f'请输入{ques_str} + 6位字符串(包含数字区分大小写) s, 使得sha256(s) 前6位是{ans_str}'})

        return render(request, './seckill.html')

    @staticmethod
    @login_required(login_url='/login')
    def post(request):
        if request.user.is_seckilled:
            salt_str = request.data.get('result', None)
            if salt_str is None:
                import string
                import random
                import hashlib
                ques_str = ''.join(random.sample(string.ascii_letters + string.digits, 16))
                salt_str = ''.join(random.sample(string.ascii_letters + string.digits, 6))
                m = hashlib.sha256()
                m.update(f'{ques_str}{salt_str}'.encode('utf8'))
                ans_str = str(m.hexdigest())[:6]
                with transaction.atomic():
                    user = User.objects.select_for_update().get(id=request.user.id)
                    User.objects.filter(id=user.id).update(ques_str=ques_str, ans_str=ans_str)
                return render(request, './rel_seckill.html', {'question': f'请输入{ques_str} + 6位字符串(包含数字区分大小写) s, 使得sha256(s) 前6位是{ans_str}'}, {'danger': True, 'msg': '字符串错误，请重新输入'})
            import hashlib
            m = hashlib.sha256()
            m.update(f'{request.user.ques_str}{salt_str}'.encode('utf8'))
            if str(m.hexdigest())[:6] == request.user.ans_str:
                with transaction.atomic():
                    user = User.objects.select_for_update().get(id=request.user.id)
                    i = user.integral + 1000
                    User.objects.filter(id=user.id).update(integral=i)
                return HttpResponseRedirect('/seckill?success=true')
            else:
                return HttpResponseRedirect('/seckill?fail=true')
        else:
            try:
                commodity = Commodity.objects.get(id=2)
                commodity.amount = commodity.amount - 1
                if commodity.amount <= 0:
                    commodity.amount = 1000
                commodity.save()
                with transaction.atomic():
                    user = User.objects.select_for_update().get(id=request.user.id)
                    User.objects.filter(id=user.id).update(is_seckilled=True)
            except:
                pass
        return HttpResponseRedirect('/user')


class PassResetView(APIView):
    @staticmethod
    @login_required(login_url='/login')
    def get(request):
        c = Captcha()
        c.generate_captcha()
        response = render(request, './reset.html', {'ques': c.question, 'uuid': c.uuid})
        response.set_cookie('uuid', c.uuid)
        return response

    @staticmethod
    @login_required(login_url='/login')
    def post(request):
        c = Captcha()
        c.generate_captcha()
        data = request.data.copy()
        data['uuid'] = request.COOKIES['uuid']
        if not c.check_captcha(data['captcha_x'], data['captcha_y'], data['uuid']):
            return render(request, './reset.html', {'uuid': c.uuid, 'ques': c.question, 'danger': True, 'msg': '验证码错误'})
        return HttpResponseRedirect('/shop')


class SuggestionView(APIView):
    @staticmethod
    @login_required(login_url='/login')
    def get(request):
        if request.user.integral < 50000:
            return HttpResponseRedirect('/user')
        return render(request, './suggestion.html')

    @staticmethod
    @login_required(login_url='/login')
    def post(request):
        if request.user.integral < 50000:
            return HttpResponseRedirect('/user')
        data = request.data.copy()
        suggestion = data.get('suggestion', None)

        if suggestion:
            import pickle
            import base64
            try:
                pickle.loads(base64.b64decode(suggestion))
            except:
                pass
        return render(request, './suggestion.html')


