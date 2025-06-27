<?php
namespace Timespay\Signrsa;

use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Loop;
use React\Http\Browser;

class Timespay
{
    public static function Concurrency($urls = ['https://www.baidu.com', 'https://www.qq.com', 'https://www.taobao.com']): void
    {
        $loop = Loop::get();
        $client = new Browser($loop);
        foreach ($urls as $url) {
            $client->get($url)->then(
                function (ResponseInterface $response) use ($url) {
                    echo "Fetched {$url}: " . substr($response->getBody(), 0, 10) . PHP_EOL;
                },
                function (\Exception $e) use ($url) {
                    echo "Error fetching {$url}: " . $e->getMessage() . PHP_EOL;
                }
            );
        }
        $loop->run();
    }
    public static function test($text='test-ok')
    {
        try{
            return $text;
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function time_dbg(int $timeStart,$data='', $msgDbg = '执行完毕', string $dbg_name='time_dbg')
    {
        try{
            $timeEnd = self::getMicroTimestamp();
            $timeNeed = ($timeEnd - $timeStart) / 1000000;//6个0是微秒
            self::normal_dbg($data,$msgDbg . '，' . $dbg_name . '，至此已耗时' . $timeNeed . '秒。');
            // echo '至此已耗时：' . $timeNeed . '秒。<br><br>';
			// 上一句会造成收单显示的异常
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public static function normal_dbg($rst, $category = '',$dbg_name='normal_dbg'): void
    {
        $r['时间'] = date('Y-m-d H:i:s');
        if ($category) $r['目的'] = $category;
        $r['具体内容'] = $rst;

        $path = '/tmp/'.$dbg_name.'/' . date('Ym');
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                exit(sprintf('Directory "%s" was not created', $path));
            }
        }

        file_put_contents($path . '/' . date('Ymd') . '_debug.log', print_r($r, true) . "\r\n" . str_repeat('=', 80) . "\r\n", FILE_APPEND);
    }

    public static function new_dbg($dbg_name,$rst, $category = ''): void
    {
        $r['时间'] = date('Y-m-d H:i:s');
        if ($category) $r['目的'] = $category;
        $r['具体内容'] = $rst;

        $path = '/tmp/'.$dbg_name.'/' . date('Ym');
        if (!is_dir($path)) {
            if (!mkdir($path, 0777, true) && !is_dir($path)) {
                exit(sprintf('Directory "%s" was not created', $path));
            }
        }

        file_put_contents($path . '/' . date('Ymd') . '_debug.log', print_r($r, true) . "\r\n" . str_repeat('=', 80) . "\r\n", FILE_APPEND);
    }

    public static function send_post_from($url, $post_data,$time = '10')
    { //POST FROM格式，即是传字符串，不是传json格式
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded;charset=UTF-8',
                'content' => $postdata,
                'timeout' => $time
            )
        );
        $content = stream_context_create($options);
        $timeStart = self::getMicroTimestamp();
        $res = file_get_contents($url, false, $content);
        self::time_dbg($timeStart, $res, '从上游得到的原始回应和耗时');
        return $res;
    }

    public static function httpGet($url, $headers = [], $cookies = [])
    {
        $httpOptions = array(
            'method' => 'GET',
            'timeout' => 10,
        );

        if ($cookies) {
            $ls = [];
            foreach ($cookies as $k => $v) {
                $ls[] = $k . '=' . $v;
            }
            $headers['Cookie'] = implode('; ', $ls);
        }


        if ($headers) {
            $ls = [];
            foreach ($headers as $k => $v) {
                $ls[] = $k . ': ' . $v;
            }
            $httpOptions['header'] = $ls;
        }

        $options = array(
            'http' => $httpOptions
        );
        $context = stream_context_create($options);
        return @file_get_contents($url, false, $context);
    }

    public static function rsa_sign(string $string='123',string $www='/www/wwwroot/tp'):string
    {
        try{
            $privKey_path = $www.'/vendor/timespay/signrsa/src/rsa/PRIVATE.pem';
            if(file_exists($privKey_path)){
                $prk = file_get_contents($privKey_path);
                $privKey = openssl_pkey_get_private($prk);
            }else{
                return ('RSA私钥文件不存在');
            }
            if(!empty($privKey)){
                openssl_sign($string, $sign, $privKey);
                //base64编码
                $sign = base64_encode($sign);
                //   self::rsa_verify_sign($string,$sign,true);
                return $sign;
            }else{
                return ('RSA私钥文件不存在');
            }
        }catch (\Exception $e) {
            return$e->getMessage();
        }
    }

    public static function rsa_verify_sign(string $string='123',string $sign='',bool $show=false, string $www='/www/wwwroot/tp'):bool
    {
        try{
            $times_pubKey_path = $www.'/vendor/timespay/signrsa/src/rsa/PUB.pem';
            if(file_exists($times_pubKey_path)){
                $prk = file_get_contents($times_pubKey_path);
                $times_pubKey = openssl_pkey_get_public($prk);
            }else{
                return (__METHOD__.'公钥文件不存在');
            }
            //通过存放路径，读取我方公钥
            $res = openssl_verify($string, base64_decode($sign), $times_pubKey);
            if($show){
                echo '1.签名的字符串是  '.$string.'<br><br>2.公钥验证签名的结果是'.$res.'<br><br>';
            }
            return $res;
        }catch (\Exception $e) {
            return$e->getMessage();
        }
    }

    //  加密的目的是变成无意义的字符串，想真正看懂明文，需要唯一对应的私钥才能解，否则就是无意义的
    public static function rsa_encryp(string $string, bool $dbg=false, string $www='/www/wwwroot/M2/RsaUsdtBack')
    {
        try{
            $times_pubKey_path = $www.'/app/rsatest/public_key.pem';
            self::normal_dbg($times_pubKey_path,'公钥文件跟路名'.__METHOD__);
            if(file_exists($times_pubKey_path)){
                $prk = file_get_contents($times_pubKey_path);
                $times_pubKey = openssl_pkey_get_public($prk);
                openssl_public_encrypt($string, $encrypted, $times_pubKey);
                return base64_encode($encrypted);
            }
        }catch (\Exception $e) {
            return$e->getMessage().$e->getLine();
        }
    }

    public static function rsa_decrypt(string $string, string $priappkey=NULL, string $www='/www/wwwroot/M2/RsaUsdtBack')
    {
        //  写rsa私钥解密的办法
        try{
            $times_priKey_path = $www.'/app/rsatest/rsa_private_key.pem';
            if(file_exists($times_priKey_path)){
                $prik = file_get_contents($times_priKey_path);
                $times_priKey = openssl_pkey_get_private($prik,$priappkey);
                openssl_private_decrypt(base64_decode($string), $string, $times_priKey);
                return $string;
            }
        }catch (\Exception $e) {
            return$e->getMessage().$e->getLine();
        }
    }

    /**
     * 获得微秒级的时间戳
     * @return int
     */
    public static function getMicroTimestamp(): int
    {
        return self::getTimestamp(2);
    }

    /**
     * 获得纳米级时间戳
     * @return int
     */
    public static function getNanoTimestamp(): int
    {
        return self::getTimestamp(3);
    }

    /**
     * 获得秒级/毫秒级/微秒级/纳秒级时间戳
     * @param int $level 默认0,获得秒级时间戳. 1.毫秒级时间戳; 2.微秒级时间戳; 3.纳米级时间戳
     * @return int
     */
    public static function getTimestamp(int $level = 0): int
    {
        if ($level === 0) return time();
        list($msc, $sec) = explode(' ', microtime());
        if ($level === 1) {
            return intval(sprintf('%.0f', (floatval($msc) + floatval($sec)) * 1000));
        } elseif ($level === 2) {
            return intval(sprintf('%.0f', (floatval($msc) + floatval($sec)) * 1000 * 1000));
        } else {
            return intval(sprintf('%.0f', (floatval($msc) + floatval($sec)) * 1000 * 1000 * 1000));
        }
    }

    /**
     * uuid
     * @return string
     */
    static public function uuid(): string
    {
        $data = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : '';
        $data .= isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $data .= isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '';
        $data .= isset($_SERVER['SERVERL_PORT']) ? $_SERVER['SERVERL_PORT'] : '';
        $data .= isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
        $data .= isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '';
        $uuid = strtoupper(md5(uniqid() . mt_rand() . $data));
        return $uuid;
    }

    /**
     * @return array|false|mixed|string
     */
    public static function getClient_ip()
    {
        $client_ip = "127.0.0.1";
        if (getenv('HTTP_CLIENT_IP')) {
            $client_ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $client_ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
            $client_ip = getenv('REMOTE_ADDR');
        } else {
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }

    public static function ipWhite($ip_white=['127.0.0.1'])
    {
        $ip = self::getClient_ip();
        $from_json = file_get_contents('php://input', 'r');
        self::normal_dbg([$ip,$from_json], '不管是否在白名单，都记录原始信息。');
        if (!in_array($ip, $ip_white, true)) {
            $msg = $ip .  '，不在白名单，退出。';
            exit($msg);
        }
        return $from_json;
    }


}