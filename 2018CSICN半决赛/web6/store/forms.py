#coding:utf-8
from django import forms

#登录表单
class LoginForm(forms.Form):
    username = forms.CharField(widget=forms.TextInput(attrs={"placeholder": "用户名", "required": "required",}),
                              max_length=50,error_messages={"required": "username不能为空",})
    password = forms.CharField(widget=forms.PasswordInput(attrs={"placeholder": "密码", "required": "required",}),
                              max_length=20,error_messages={"required": "password不能为空",})

#修改密码表单
class ChangePassForm(forms.Form):
    old_password = forms.CharField(widget=forms.PasswordInput(attrs={"placeholder": "旧密码", "required": "required",}),
                              max_length=20,error_messages={"required": "password不能为空",})
    password = forms.CharField(widget=forms.PasswordInput(attrs={"placeholder": "密码", "required": "required",}),
                              max_length=20,error_messages={"required": "password不能为空",})
    password_confirm = forms.CharField(widget=forms.PasswordInput(attrs={"placeholder": "确认密码", "required": "required",}),
                              max_length=20,error_messages={"required": "password不能为空",})
    def clean(self):
        if self.cleaned_data['password_confirm'] != self.cleaned_data['password']:
            raise forms.ValidationError('两次输入密码不一致')
        else:
            cleaned_data = super(ChangePassForm,self).clean()
        return cleaned_data

#注册表单
class RegForm(forms.Form):
    username = forms.CharField(widget=forms.TextInput(attrs={"placeholder": "用户名", "required": "required",}),
                              max_length=50,error_messages={"required": "username不能为空",})
    mail = forms.EmailField(widget=forms.TextInput(attrs={"placeholder": "邮箱", "required": "required",}),
                              max_length=50,error_messages={"required": "email不能为空",})
    #invite_user = forms.CharField(widget=forms.TextInput(attrs={"placeholder": "推荐人", }),
    #                           max_length=20,)
    password = forms.CharField(widget=forms.PasswordInput(attrs={"placeholder": "密码", "required": "required",}),
                              max_length=20,error_messages={"required": "password不能为空",})
    password_confirm = forms.CharField(widget=forms.PasswordInput(attrs={"placeholder": "确认密码", "required": "required",}),
                              max_length=20,error_messages={"required": "password不能为空",})

    def clean(self):
        if self.cleaned_data['password_confirm'] != self.cleaned_data['password']:
            raise forms.ValidationError('两次输入密码不一致')
        else:
            cleaned_data = super(RegForm,self).clean()
        return cleaned_data

#评论表单
class CommentForm(forms.Form):
    author = forms.CharField(widget=forms.TextInput(attrs={"id":"author","class":"comment_input",
                                                           "required":"required","size":"25",
                                                           "tabindex":"1"}),
                             max_length=50,error_messages={"required":"username不能为空",})

    email = forms.EmailField(widget=forms.TextInput(attrs={"id":"email","type":"email",
                                                           "class": "comment_input",
                                                           "required":"required","size":"25",
                                                           "tabindex":"2"}),
                                 max_length=50, error_messages={"required":"email不能为空",})

    url = forms.URLField(widget=forms.TextInput(attrs={"id":"url","type":"url","class": "comment_input",
                                                       "size":"25", "tabindex":"3"}),
                              max_length=100, required=False)

    comment = forms.CharField(widget=forms.Textarea(attrs={"id":"comment","class": "message_input",
                                                           "required": "required", "cols": "25",
                                                           "rows": "5", "tabindex": "4"}),
                                                    error_messages={"required":"评论不能为空",})

    article = forms.CharField(widget=forms.HiddenInput())


