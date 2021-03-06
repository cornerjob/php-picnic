<?php

namespace Picnic;

class SimpleUtils {

    /**
     * @return string
     */
    public static function new_uuid()
    {
        return strtolower(sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0,65535), mt_rand(0,65535), mt_rand(0,65535), mt_rand(16384,20479), mt_rand(32768,49151), mt_rand(0,65535), mt_rand(0,65535), mt_rand(0,65535)));
    }

    /**
     * @param $md5
     * @return string
     */
    public static function md5_to_uuid($md5)
    {
        return strtolower(substr($md5,0,8)."-".substr($md5,8,4)."-".substr($md5,12,4)."-".substr($md5,16,4)."-".substr($md5,20,12));
    }

    /**
     * @param $url
     * @return bool|int
     */
    public static function url_exists($url)
    {
        $hdrs = @get_headers($url);
        return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
    }

    /**
     * @param $image
     * @return bool
     */
    public static function check_image($image) {
        //checks if the file is a browser compatible image

        $mimes = array('image/gif','image/jpeg','image/pjpeg','image/png');
        //get mime type
        $mime = getimagesize($image);
        $mime = $mime['mime'];

        $extensions = array('jpg','png','gif','jpeg');
        $extension = strtolower( pathinfo( $image, PATHINFO_EXTENSION ) );

        if ( in_array( $extension , $extensions ) AND in_array( $mime, $mimes ) ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $string
     * @return bool
     */
    public static function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * @param $num
     * @return string
     */
    public static function random_words($num)
    {
        $dictionary = "/usr/share/dict/words";

        $arWords = file($dictionary);

        $n = 0;

        $arRet = array();

        while ($n < $num) {
            $random = rand(0,235885);
            $arRet[] = rtrim($arWords[$random]);

            $n++;
        }

        return implode(" ", $arRet);
    }

    /**
     * @param array $array
     * @param $column
     * @param array $values
     * @param bool $notIn
     * @return array
     */
    public static function array_filter_values(array $array, $column, array $values, $notIn = false)
    {
        sort($values);

        return array_filter($array, function($item) use ($column, $values, $notIn) {
            return ($notIn xor (Utils::binary_search($item[$column], $values) !== false));
        });
    }

    /**
     * @param array $array
     * @param callable $callback
     * @param array $keys
     */
    public static function call_func_array(array &$array, callable $callback, array $keys = array())
    {
        $auxArray = empty($keys) ? $array : array_intersect_key($array, array_flip($keys));

        foreach ($auxArray as $key => $value) {
            $array[$key] = call_user_func($callback, $value);
        }
    }

    /**
     * @param array $array
     * @param $field
     * @param bool $reverse
     */
    public static function usort_by_array_field(array &$array, $field, $reverse = false)
    {
        uasort($array, function($item1, $item2) use ($field, $reverse) {
            return (($reverse xor $item1[$field] < $item2[$field]) ? -1 : 1);
        });
    }

    /**
     * @param array $array
     * @param $column
     * @param array $values
     * @return array
     */
    public static function array_intersect_column(array $array, $column, array $values)
    {
        $colA = array_flip(array_column($array, $column));
        $colB = array_flip($values);

        $intersect = array_intersect_key($colA, $colB);

        return array_intersect_key($array, array_flip($intersect));
    }

    public static function curl_image($url, $file)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $rawdata = curl_exec ($ch);
        curl_close ($ch);

        $fp = fopen($file,'w');
        fwrite($fp, $rawdata);
        fclose($fp);
    }

    public static function curl_request($url, $type="GET", $json=false, $headers=array('Accept: application/json'))
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $rawdata = curl_exec ($ch);
        curl_close ($ch);

        return $rawdata;
    }

    public static function curl_request_xml($url, $type="GET", $xml=false, $headers=array('Content-Type: application/xml'))
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $rawdata = curl_exec ($ch);
        curl_close ($ch);

        return $rawdata;
    }

    /**
     * @param $url
     * @return int
     */
    public static function up_check($url)
    {
        $up = 0;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);

        if ($response) {
            $up = 1;
        }

        // Then, after your curl_exec call:
        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        return $up;
    }

    public static function pass_hash($password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // echo $hash."\n";
        
        return $hash;
    }

    public static function pass_check($password, $hash)
    {
        if (password_verify($password, $hash)) {
            // echo 'Password is valid!';
            return true;
        } else {
            // echo 'Invalid password.';
            return false;
        }
    }

    public static function get_server_headers()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

    public static function normalize_chars($str)
    {
        $str = str_replace("á", "a", $str);
        $str = str_replace("é", "e", $str);
        $str = str_replace("í", "i", $str);
        $str = str_replace("ó", "o", $str);
        $str = str_replace("ú", "u", $str);

        $str = str_replace("à", "a", $str);
        $str = str_replace("è", "e", $str);
        $str = str_replace("ì", "i", $str);
        $str = str_replace("ò", "o", $str);
        $str = str_replace("ù", "u", $str);

        $str = str_replace("Á", "A", $str);
        $str = str_replace("É", "E", $str);
        $str = str_replace("Í", "I", $str);
        $str = str_replace("Ó", "O", $str);
        $str = str_replace("Ú", "U", $str);

        $str = str_replace("À", "A", $str);
        $str = str_replace("È", "E", $str);
        $str = str_replace("Ì", "I", $str);
        $str = str_replace("Ò", "O", $str);
        $str = str_replace("Ù", "U", $str);

        $str = str_replace("ñ", "n", $str);
        $str = str_replace("Ñ", "n", $str);

        $str = str_replace("ç", "c", $str);
        $str = str_replace("Ç", "c", $str);

        $str = addslashes($str);

        return $str;
    }

    public static function unicode_chars($str)
    {
        $str = str_replace("u00e1", "á", $str);
        $str = str_replace("u00e9", "é", $str);
        $str = str_replace("u00ed", "í", $str);
        $str = str_replace("u00f3", "ó", $str);
        $str = str_replace("u00fa", "ú", $str);

        $str = str_replace("u00e0", "à", $str);
        $str = str_replace("u00e8", "è", $str);
        $str = str_replace("u00ec", "ì", $str);
        $str = str_replace("u00f2", "ò", $str);
        $str = str_replace("u00f9", "ù", $str);

        $str = str_replace("u00c1", "Á", $str);
        $str = str_replace("u00c9", "É", $str);
        $str = str_replace("u00cd", "Í", $str);
        $str = str_replace("u00d3", "Ó", $str);
        $str = str_replace("u00da", "Ú", $str);

        $str = str_replace("u00c0", "À", $str);
        $str = str_replace("u00c8", "È", $str);
        $str = str_replace("u00cc", "Ì", $str);
        $str = str_replace("u00d2", "Ò", $str);
        $str = str_replace("u00d9", "Ù", $str);

        $str = str_replace("u00f1", "ñ", $str);
        $str = str_replace("u00d1", "Ñ", $str);

        $str = str_replace("u00e7", "ç", $str);
        $str = str_replace("u00c7", "Ç", $str);

        $str = str_replace("00e2", "â", $str);
        $str = str_replace("00e3", "ã", $str);
        $str = str_replace("00e4", "ä", $str);
        $str = str_replace("00e5", "å", $str);

        $str = str_replace("00ea", "ê", $str);
        $str = str_replace("00eb", "ë", $str);

        $str = str_replace("00ee", "î", $str);
        $str = str_replace("00ef", "ï", $str);

        $str = str_replace("00f4", "ô", $str);
        $str = str_replace("00f5", "õ", $str);
        $str = str_replace("00f6", "ö", $str);

        $str = str_replace("00fb", "û", $str);
        $str = str_replace("00fc", "ü", $str);

        $str = str_replace("u00bf", "¿", $str);

        return $str;
    }

}