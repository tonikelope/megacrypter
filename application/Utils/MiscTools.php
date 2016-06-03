<?php

class Utils_MiscTools
{
    public static function urlBase64Decode($data) {
        return base64_decode(str_replace(['-', '_', ','], ['+', '/', ''], str_pad($data, strlen($data) + (4 - strlen($data) % 4) % 4, '=')));
    }

    public static function urlBase64Encode($data) {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($data));
    }

    public static function i32a2Bin(array $i32a) {
        return call_user_func_array('pack', array_merge(['N*'], $i32a));
    }

    public static function bin2i32a($bin) {
        return array_values(unpack('N*', str_pad($bin, 4 * ceil(strlen($bin) / 4), chr(0))));
    }

    public static function i32a2UrlBase64(array $i32a) {
        return self::urlBase64Encode(self::i32a2Bin($i32a));
    }

    public static function urlBase642i32a($data) {
        return self::bin2i32a(self::urlBase64Decode($data));
    }
    

    public static function deflateUrl($url, $retry = 3, $https=true) {
        
        do {
            
            $ch = curl_init('https://www.googleapis.com/urlshortener/v1/url?key='.GOOGLE_URL_SHORT_API_KEY);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['longUrl' => $url]));
            curl_setopt($ch, CURLOPT_USERAGENT, CURL_USERAGENT);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

            $resp = json_decode(curl_exec($ch));

            $curl_error = curl_errno($ch);

            curl_close($ch);
            
            $tiny_url = trim($resp->id);

        } while (($curl_error || !preg_match('/^https?\:\/\//i', $tiny_url)) && --$retry > 0);
        
        return $retry > 0 ? ($https?str_ireplace('http://', 'https://', $tiny_url):$tiny_url): $url;
    }
    
    public static function truncateText($text, $max_length, $separator=' ... ') {
	
        $max_length -= strlen($separator);
        
        if($max_length % 2 != 0) {
            
            $max_length--;
        }
        
        return (strlen($text) > $max_length)?preg_replace('/^(.{1,'.($max_length/2).'}).*?(.{1,'.($max_length/2).'})$/u', '\1'.$separator.'\2', $text):$text;
    }

    public static function isCacheableError($ecode) {
        $cacheable_ecodes = [
            Utils_MegaApi::EBLOCKED,
            Utils_MegaApi::EKEY,
            Utils_MegaApi::ETOOMANY
        ];

        return in_array($ecode, $cacheable_ecodes);
    }

    public static function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = min(floor(($bytes ? log($bytes) : 0) / log(1024)), count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public static function hideFileName($filename, $salt = null) {
        return preg_replace_callback('/^(.*?)(?:(s?\d+ *?[xe] *?\d+(?: *?\- *?s?\d+ *?[xe] *?\d+)?)(.*?))?(\.(?:(?:vol|part|tar)?[\d\+]*\.)?[^\.]+)?$/i', is_null($salt) ? (function($m) use ($filename) {
                            return ($m[1] . $m[3] != '' && !is_numeric($m[1] . $m[3])) ? (($m[2] ? '('.str_replace(' ', '', $m[2]).')' : '') . '************' . $m[4]) : $filename;
                        }) : (function($m) use($filename, $salt) {
                            return ($m[1] . $m[3] != '' && !is_numeric($m[1] . $m[3])) ? (($m[2] ? '('.str_replace(' ', '', $m[2]).')' : '') . md5($salt . $m[1] . $m[3] . $salt) . $m[4]) : $filename;
                        }), $filename);
    }

    public static function getMaxStringLength(array $strings) {
        $max_length = 0;

        foreach ($strings as $string) {
            $length = is_array($string) ? self::getMaxStringLength($string) : strlen($string);

            if ($length > $max_length)
                $max_length = $length;
        }

        return $max_length;
    }

    public static function addExtraInfoToFilename($filename, $extra_info) {
        return !empty($extra_info) ? preg_replace(['/\s+/', '/[^\wñÑ()\[\].]+/i'], ['_', ''], $extra_info) . '__' . $filename : $filename;
    }

    public static function isStreameableFile($filename) {
        $ext = [
            'mp2', 'mp3', 'mpga', 'mpega', 'mpg', 'mpeg', 'mpe', 'vob', 'aac',
            'mp4', 'mpg4', 'm4v', 'avi', 'ogg', 'ogv', 'asf', 'asx', 'ogv', 'wmv',
            'wmx', 'wma', 'wav', '3gp', '3gp2', 'divx', 'flv', 'mkv', 'mka', 'm3u',
            'webm', 'rm', 'ra', 'amr', 'flac', 'mov', 'qt'];

        return preg_match('/\.(' . implode('|', $ext) . ')$/i', trim($filename));
    }
    
    public static function extractHostFromUrl($url, $ignore_www=false) {
        
        return preg_match('/^(?:https?\:\/\/)?'.($ignore_www?'(?:www\.)?':'').'((?:[^\/]+\.)+[^\/.]+)/i', trim($url), $match)?strtolower($match[1]):null;
    }
    
    public static function extractLinks($data) {
        
        return preg_match_all('/https?\:\/\/[^ ]*?(?=https?\:\/\/|$)/im', $data, $match)?$match[0]:null;
    }

    public static function rimplode($glue, Array $items) {
        if (!empty($items)) {
            $aux = [];

            foreach ($items as $element) {
                $aux[] = is_array($element) ? self::rimplode($glue, $element) : $element;
            }

            return implode($glue, $aux);
        }
    }

    public static function unescapeUnicodeChars($str, $to_encoding = 'UTF-8', $from_encoding = 'UCS-2BE') {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', function($match) use ($to_encoding, $from_encoding) {
                    return mb_convert_encoding(pack('H*', $match[1]), $to_encoding, $from_encoding);
                }, $str);
    }

    public static function rCount(array $array) {
        
        $tot = 0;

        array_walk_recursive($array, function($k, $v) use (&$tot) {$tot++;});
        
        return $tot;
    }
    
    public static function sendGmail($username, $pass, Array $email, $charset = 'UTF-8') {
        
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->CharSet = $charset;
        $mail->SMTPDebug = 0;
        $mail->Host = GMAIL_HOST;
        $mail->Port = GMAIL_PORT;
        $mail->SMTPAuth = GMAIL_SMTP_AUTH;
        $mail->SMTPSecure = GMAIL_SMTP_SECURE;
        $mail->Username = $username;
        $mail->Password = $pass;
        
        foreach ($email as $to => $email_data) {
 
            $mail->clearAllRecipients();
            
            $mail->setFrom($username);
            
            $mail->addAddress($to);

            if (isset($email_data['cc'])) {
                
                $mail->clearCCs();
                
                foreach(is_array($email_data['cc'])?$email_data['cc']:[$email_data['cc']] as $cc) {
                    
                    $mail->addCC($cc);
                }
            }

            if (isset($email_data['bcc'])) {
                
                $mail->clearBCCs();

                foreach(is_array($email_data['bcc'])?$email_data['bcc']:[$email_data['bcc']] as $bcc) {
                    
                    $mail->addBCC($bcc);
                }
            }

            $mail->Subject = $email_data['subject'];

            $mail->Body = $email_data['body'];

            if(!$mail->send()) {
                
                throw new Exception(__METHOD__ . $mail->ErrorInfo);
            }
        }
    }
}
