# Web方向出题模板

## 基本要求

- 实现能通过 `checker.py` 检查的web程序，确保基本功能完整，web程序实现示例在 `template` 文件夹下，使用 `docker-compose` 一键启动即可
- 必须包含预设的Web应用安全漏洞（创新性安全挑战即为对预设安全漏洞的某种检测和利用技巧），不限制Web应用安全漏洞类型
- 用户注册、登录和找回密码功能需要实现人机识别验证码的特性，其中人机识别验证码必须采用附件中的验证码数据集；
- 业务逻辑上设计防止“薅羊毛”的特性，植入创新性网络安全挑战，比如必须要进行某些绕过防止“薅羊毛”特性的攻击操作后，才能到达漏洞利用条件（如某些高等级用户才具备的功能特性，其中才包含网络安全挑战预设安全漏洞）
- 采用PHP/Python/Java等语言实现，运行在Apache或Nginx等Web服务器；后段必须有数据库，如mysql、sqlite、mongodb等
- 赛题环境提供`Dockerfile` + `docker-compose` 容器环境，主办方使用`docker-compose up -d`启动选手提供的环境（docker环境内初始**只包含源码，不要node_modules，mysql data之类的数据**，运行命令写入Dockerfile，由Docker自动编译和运行）
- web端口统一为 **80**，不要使用其他端口
- Flag可即时更新，不接受固定Flag的赛题，在文档中提供更新flag的命令，`sed -i 's/CISCN{xxxx}/CISCN{123456}/g' flag.php` 格式为 `CISCN{xxxxxxxflag}`

## 文件解释

- `template` 文件夹为题目格式模板
  - `deploy` 文件夹为部署环境，使用docker打包
    - `www` 文件夹下为完整源码，不需要包含数据文件
    - `requirement.pip` 模板程序运行所需要的依赖包
    - `Dockerfile` 与 `docker-compose.yml` 按照要求，使用docker打包
  - `writeup.md` 完整的解题步骤，如果需要使用exp则必须包含exp
  - `README.md` 题目的完整说明，包括功能介绍等
- `checker` 文件夹为功能检查脚本脚本
  - `ans` 目录为验证码答案文件
  - `checker.py` 为进行校验的检查脚本，通过通过则没有任何报错且打印 `xxx Success`，出现 `Failed` 或 `Exception` 则说明功能检查未通过

## checker检查过程

- checker主要检查以下功能（基本功能，必须实现）
  - 用户（登录 / 注册 / 修改密码 / 重置密码 / 邀请注册）
  - 商品（商品列表 / 购物车 / 结算 / 限时秒杀）
- checker 程序支持自定义csrf name
- checker 程序通过验证返回页面中是否存在 `class="alert alert-success" `与 `class="alert alert-danger"` 的 `div` 内容判断功能是否正常，数据操作功能则通过验证操作前后数据变化进行检查，如购买商品后检查用户积分是否减少或商品数量是否减少

## Web程序说明

在 checker 程序中提供了一份路由列表

```
登录:		/login
注册: 	/register
登出:     /logout
重置密码:  /pass/reset
修改密码:  /user/change
用户信息:  /user
商品列表:  /shop
商品信息:  /info/(id)
结账:  	/pay
秒杀活动:  /seckill
购物车: 	/shopcar
加入购物车: /shopcar/add
验证码:	/captcha
```

基本功能应遵循该路由，否则 checker 程序可能将检查失败

各个请求参数应遵循checker脚本中所提交的参数，可以进行功能与参数扩展，不允许删减功能与参数

程序进行功能性操作时，应显示操作成功与失败提示，遵循checker校验规则

资金或商品操作时，校验操作后的资金额度与商品数量是否减少

消息成功与失败应使用类为 `alert alert-sucess` 和 `alert alert-danger"` 的`div` 提示



## 模板程序说明

模板程序为 `tornado` + `sqlite` 的商城应用程序

模板程序启动时将填充一部分测试数据，启动后可直接使用 checker 程序进行功能校验

基本功能已经实现基本逻辑，可在此基础上进行完善

不限开发框架，语言，Web服务器，数据库，必须存在数据库交互



## 应用场景

基于以上要求，实现自己的电商应用场景



