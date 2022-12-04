<?php
namespace Timespay\Signrsa;
class Tools
{
    public static function test()
    {
        try{
            return 'test-ok';
        }catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}