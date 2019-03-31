Alice和Bob正在进行通信，作为中间人的Eve一直在窃听他们两人的通信。

Eve窃听到这样一段内容，主要内容如下：
p = 37
A = 17
B = 31

U2FsdGVkX1+mrbv3nUfzAjMY1kzM5P7ok/TzFCTFGs7ivutKLBLGbZxOfFebNdb2
l7V38e7I2ywU+BW/2dOTWIWnubAzhMN+jzlqbX6dD1rmGEd21sEAp40IQXmN/Y0O
K4nCu4xEuJsNsTJZhk50NaPTDk7J7J+wBsScdV0fIfe23pRg58qzdVljCOzosb62
7oPwxidBEPuxs4WYehm+15zjw2cw03qeOyaXnH/yeqytKUxKqe2L5fytlr6FybZw
HkYlPZ7JarNOIhO2OP3n53OZ1zFhwzTvjf7MVPsTAnZYc+OF2tqJS5mgWkWXnPal
+A2lWQgmVxCsjl1DLkQiWy+bFY3W/X59QZ1GEQFY1xqUFA4xCPkUgB+G6AC8DTpK
ix5+Grt91ie09Ye/SgBliKdt5BdPZplp0oJWdS8Iy0bqfF7voKX3VgTwRaCENgXl
VwhPEOslBJRh6Pk0cA0kUzyOQ+xFh82YTrNBX6xtucMhfoenc2XDCLp+qGVW9Kj6
m5lSYiFFd0E=

分析得知，他们是在公共信道上交换加密密钥，共同建立共享密钥。

而上面这段密文是Alice和Bob使用自己的密值和共享秘钥，组成一串字符的md5值的前16位字符作为密码使用另外一种加密算法加密明文得到的。

例如Alice的密值为3，Bob的密值为6，共享秘钥为35，那么密码为：

password = hashlib.md5("(3,6,35)").hexdigest()[0:16]


