<?php

class Utils_CryptTools
{
    public static function aesCbcEncrypt($data, $key, $iv = null, $pkcs7pad = false) {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $pkcs7pad ? self::pkcs7Pad($data, mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)) : $data, MCRYPT_MODE_CBC, is_null($iv) ? pack('x' . mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)) : $iv);
    }

    public static function aesCbcDecrypt($data, $key, $iv = null, $pkcs7pad = false) {
        $dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, is_null($iv) ? pack('x' . mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)) : $iv);

        return $pkcs7pad ? self::pkcs7UnPad($dec) : $dec;
    }

    public static function aesCbcEncryptI32a(array $i32a, $key, $iv = null) {
        return Utils_MiscTools::bin2i32a(self::aesCbcEncrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv));
    }

    public static function aesCbcDecryptI32a(array $i32a, $key, $iv = null) {
        return Utils_MiscTools::bin2i32a(self::aesCbcDecrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv));
    }

    public static function aesEcbEncrypt($data, $key, $iv = null, $pkcs7pad = false) {
        return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $pkcs7pad ? self::pkcs7Pad($data, mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB)) : $data, MCRYPT_MODE_ECB, is_null($iv) ? pack('x' . mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB)) : $iv);
    }

    public static function aesEcbDecrypt($data, $key, $iv = null, $pkcs7pad = false) {
        $dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_ECB, is_null($iv) ? pack('x' . mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB)) : $iv);

        return $pkcs7pad ? self::pkcs7UnPad($dec) : $dec;
    }

    public static function aesEcbEncryptI32a(array $i32a, $key, $iv = null) {
        return Utils_MiscTools::bin2i32a(self::aesEcbEncrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv));
    }

    public static function aesEcbDecryptI32a(array $i32a, $key, $iv = null) {
        return Utils_MiscTools::bin2i32a(self::aesEcbDecrypt(Utils_MiscTools::i32a2Bin($i32a), Utils_MiscTools::i32a2Bin($key), $iv));
    }

    public static function pkcs7Pad($data, $blocksize) {
        $pad = $blocksize - (strlen($data) % $blocksize);

        return $data . str_repeat(chr($pad), $pad);
    }

    public static function pkcs7UnPad($data) {
        $pad = ord($data[strlen($data) - 1]);

        return ($pad > strlen($data) || strspn($data, chr($pad), strlen($data) - $pad) != $pad) ? false : substr($data, 0, -1 * $pad);
    }

    public static function decryptMegaDownloaderLinks($data) {
        
        return preg_replace_callback('/mega\:\/\/(?P<folder>f)?(?P<enc>enc\d*?)\?(?P<linkdata>[\da-z_,-]*?)(?=https?\:|mega\:|[^\da-z_,-]|$)/i', 
                
            function($match) {
				
                $key = ['enc' => '6B316F36416C2D316B7A3F217A30357958585858585858585858585858585858', 
						'enc2' => 'ED1F4C200B35139806B260563B3D3876F011B4750F3A1A4A5EFD0BBE67554B44'];

                $iv = '79F10A01844A0B27FF5B2D4E0ED3163E';

                return Utils_MegaApi::MEGA_HOST . '/#' . strtoupper($match['folder']) . Utils_CryptTools::aesCbcDecrypt(Utils_MiscTools::urlBase64Decode($match['linkdata']), Utils_MiscTools::hex2bin($key[$match['enc']]), Utils_MiscTools::hex2bin($iv), true); }, 
                
            $data);
    }
    
    public static function opensslDecryptWithPass($data, $method, $password, $base64=false) {
        
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w']
        ];

        if (is_resource(($process = proc_open("openssl enc -{$method} -pass pass:'{$password}' -d".($base64?' -a':''), $descriptorspec, $pipes)))) {
            
            fwrite($pipes[0], $data);
            
            fclose($pipes[0]);

            $dec = stream_get_contents($pipes[1]);

            fclose($pipes[1]);
            
            proc_close($process);
            
            return $dec;
            
        } else {
            
            return false;
        }
    }
    
    public static function opensslEncryptWithPass($data, $method, $password, $base64=false) {
        
        $descriptorspec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w']
        ];

        if (is_resource(($process = proc_open("openssl enc -{$method} -pass pass:'{$password}' -e".($base64?' -a':''), $descriptorspec, $pipes)))) {
            
            fwrite($pipes[0], $data);
            
            fclose($pipes[0]);

            $enc = stream_get_contents($pipes[1]);

            fclose($pipes[1]);
            
            proc_close($process);
            
            return $enc;
            
        } else {
            
            return false;
        }
    }
    
    public static function passHMAC($algo, $pass, $salt, $iterations, $raw_output=true) {
		
        for($i=1, $xor=($last=hash_hmac($algo, $salt, $pass, true)); $i<$iterations; $i++) {

            $xor^=($last=hash_hmac($algo, $last, $pass, true));
        }

        return $raw_output?$xor:bin2hex($xor);
    }

}
