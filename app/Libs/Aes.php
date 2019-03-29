<?php
/**
 * Created by PhpStorm.
 * User: hongpo
 * Date: 2018/8/22
 * Time: 15:50
 */

namespace App\Libs;


class Aes
{
    //加密
    public static function encrypt($str, $key)
    {
        $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $input =Aes::pkcs5_pad($str, $size);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $input);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $data = base64_encode($data);
        return $data;
    }
    private static  function pkcs5_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
    //解密
    public static function decrypt($str, $key)
    {
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,base64_decode( $str), MCRYPT_MODE_ECB,'0');
        $dec_s = strlen($decrypted);
        $padding = ord($decrypted[$dec_s - 1]);
        $decrypted = substr($decrypted, 0, -$padding);

        return $decrypted;
    }


}