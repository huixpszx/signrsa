安装方法：
composer require timespay/signrsa '@dev'



升级方法
composer update timespay/signrsa '@dev'



使用方法
1.test，用于测试接口是否正常，正常返回[test-ok]

2.send_post_from，传参[提交地址，提交的数组，连接超时秒数（不传则默认10秒）]，
例子['https://www.baidu.com',[0=>'a',1=>'b'],3]

3.httpGet，传参[提交地址]，可带参数，例子：https://www.baidu.com?test=12345&num=10

4.rsa_create，linux使用命令[生成密钥的方法]

5.rsa_sign,rsa签名方式，传参[需要加密的字符串,私钥存放路径]返回签名后的字符串。
注意，签名的目的是用于[防止伪造]，并不是防止被人看到明文，如果需要字符串加密，则使用6，rsa加密

6.rsa_verify_sign，传参[需要加密的字符串,传来的签名,公钥存放路径]上方的rsa_sign结果，
返回true（验证通过） 或者 false（验证不通过） 或者null（没有找到公钥）

7.rsa_encryp，rsa加密，将字符串传参[对方的公钥存放路径]加密，用于通讯中不想被人看到明文

8.rsa_decrypt，rsa解密，对方传来的加密字符串，传参[自己的私钥存放路径]，解密成明文

9.timedbg，linux记录日志，传参[开始的微秒，需要记录的内容，目录（默认logdbg）]，自动新建tmp/记录目录名

10.logdbg，只记录需要记录的内容，[不记录耗时]

11.uuid，获取一个唯[唯一订单号]

12.getClient_ip，获取[来访的IP地址]

13.ipWhite，传参[白名单的数组格式]，判断是否在[Ip白名单内]
