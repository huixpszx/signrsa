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

    public static function rsa_sign(string $string, string $privKey_path):string
        {
            try{
                    //通过存放路径，读取私钥
                    if(file_exists($privKey_path)){
                        $prk = file_get_contents($privKey_path);
                        $privKey = openssl_pkey_get_private($prk);
                    }else{
                        return ('RSA私钥文件不存在');
                    }
                  if(!empty($privKey)){
                        openssl_sign($string, $sign, $privKey);
                        //base64编码
                      return base64_encode($sign);
                    }
                  return 'err';
            }catch (\Exception $e) {
                        return$e->getMessage();
                    }
        }

    public static function rsa_verify_sign(string $string, string $sign, string $times_pubKey_path):bool
    {
        try{
                //通过存放路径，读取我方公钥
                if(file_exists($times_pubKey_path)){
                    $prk = file_get_contents($times_pubKey_path);
                    $times_pubKey = openssl_pkey_get_private($prk);
                }else{
                    return ('公钥文件不存在');
                }
            return (bool)openssl_verify($string, base64_decode($sign), $times_pubKey);
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

}