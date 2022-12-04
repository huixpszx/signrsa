1.test，用于测试接口是否正常，正常返回[test-ok]

2.send_post_from，[提交地址，提交的数组，连接超时秒数（不传则默认10秒）]，例子['https://www.baidu.com',[0=>'a',1=>'b'],3]

3.httpGet，[提交地址]，可带参数，例子：https://www.baidu.com?test=12345&num=10

4.rsa_sign,rsa签名方式，返回签名后的字符串。注意，签名的目的是用于[防止伪造]，并不是防止被人看到明文，如果需要字符串加密，则使用6，rsa加密

5.rsa_verify_sign，用于[验证]上方的rsa_sign结果，返回true 或者 false

6.rsa_encryp，rsa加密，将字符串用[对方的公钥]加密，用于通讯中不想被人看到明文

7.rsa_decrypt，rsa解密，对方传来的加密字符串，使用[自己的私钥]，解密成明文