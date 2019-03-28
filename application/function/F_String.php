<?php
/**
 * File: F_String.php
 * Functionality: 字符串处理
 * Author: 资料空白
 * Date: 2016-11-11再整理
 */
if ( ! function_exists('getRandom')){
    function getRandom($length = 4, $type = 1) {
        switch ($type) {
            case 1:
                $string = '1234567890';
            break;

            case 2:
                $string = 'abcdefghijklmnopqrstuvwxyz';
            break;

            case 3:
                $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;

            case 4:
                $string = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;

            case 5:
                $string = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        }
        $output = '';
        for ($i = 0; $i < $length; $i++) {
            $pos = mt_rand(0, strlen($string) - 1);
            $output .= $string[$pos];
        }
        return $output;
    }
}
if ( ! function_exists('getRawText')){
    function getRawText($str='',$clearblank = true){
        $str = strip_tags(trim($str));
		if($clearblank){
			$search = array(" ","　","\n","\r","\t");
		}else{
			$search = array("\n","\r","\t");
		}
        return str_replace($search, "", $str);
    }
}
/**************************************************************
*
*  将数组转换为JSON字符串（兼容中文）
*  @param  array   $array      要转换的数组
*  @return string      转换得到的json字符串
*  @access public
*
*************************************************************/
if ( ! function_exists('arrayRecursive')){
function arrayRecursive(&$array, $function, $apply_to_keys_also = false){
	static $recursive_counter = 0;
	if (++$recursive_counter > 1000) {
		die('possible deep recursion attack');
	}

	if($array){
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				arrayRecursive($array[$key], $function, $apply_to_keys_also);
			} else {
				$array[$key] = $function($value);
			}

			if ($apply_to_keys_also && is_string($key)) {
				$new_key = $function($key);
				if ($new_key != $key) {
					$array[$new_key] = $array[$key];
					unset($array[$key]);
				}
			}
	    }
	}
    $recursive_counter--;
}
}
if ( ! function_exists('JSON')){
function JSON($array) {
	arrayRecursive($array, 'urlencode', TRUE);
	$json = json_encode($array);
	return urldecode($json);
}
}
