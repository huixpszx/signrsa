<?php
namespace Timespay\Signrsa;

class Timespay
{
    public static function test($text='test-ok')
        {
            try{
                return $text;
            }catch (\Exception $e) {
                        return $e->getMessage();
                    }
        }

    public static function dbg(int $timeStart, string $dbg_name='logdbg', $msgDbg = '执行完毕')
            {
                try{
                        $timeEnd = self::getMicroTimestamp();
                        $timeNeed = ($timeEnd - $timeStart) / 1000000;//6个0是微秒
                        self::logdbg($dbg_name,$msgDbg . '，' . $dbg_name . '，至此已耗时' . $timeNeed . '秒。');
                        echo '至此已耗时：' . $timeNeed . '秒。<br><br>';
                }catch (\Exception $e) {
                            return $e->getMessage();
                        }
            }

    public static function logdbg($dbg_name,$rst, $category = '')
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
        return file_get_contents($url, false, $content);
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
            $time_start = TimeHelper::getMicroTimestamp();
            $privKey_path = $www.'/vendor/timespay/signrsa/src/rsa/rsa_private_key.pem';
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
                self::rsa_verify_sign($string,$sign);
                return $sign;
            }else{
                return ('RSA私钥文件不存在');
            }
        }catch (\Exception $e) {
            return$e->getMessage();
        }
    }

    public static function rsa_verify_sign(bool $show=false,string $string='123',string $sign='', string $www='/www/wwwroot/tp'):bool
    {
        try{
            $times_pubKey_path = $www.'/vendor/timespay/signrsa/src/rsa/rsa_public_key.pem';
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

    public static function rsa_encryp(string $string, bool $dbg=false):string
    {
        try{

        }catch (\Exception $e) {
            return$e->getMessage();
        }
    }

    public static function rsa_decrypt(string $string, bool $dbg=false):string
    {
        try{

        }catch (\Exception $e) {
            return$e->getMessage();
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

}