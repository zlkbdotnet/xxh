<?php
/**
 * File: F_Basic.php
 * Functionality: Global basic functions 
 * Author: 资料空白
 * Date: 2016-11-11再整理
 */

function createGateWayPOST($url,$postData){
    header("Content-type: text/html; charset=utf-8");
    $method = 'POST';
    $html = "<html><head><meta charset='utf-8'></head><body>";
    $html .= "<form id='xitouGateway' name='testGateway' action='" .$url. "' method='" . $method . "'>";
    foreach ($postData as $key =>$value){
        $html .= "<input type='hidden' name='".$key."' value='" . $value . "'/>";
    }
    $html .= "<input type='submit' style='display:none;' value='submit'></form>";
    $html .= "<script>document.forms['testGateway'].submit();</script>";
    $html .= "</body></html>";
    echo $html;
    exit;
}

if ( ! function_exists( 'exif_imagetype' ) ) {
	function exif_imagetype($file){
        list($width, $height, $type2, $attr) = getimagesize($file);
        return $type2;
	}
}

//判断PHP版本
if ( ! function_exists('is_php')){
	function is_php($version){
		static $_is_php;
		$version = (string) $version;
		if ( ! isset($_is_php[$version])){
			$_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
		}
		return $_is_php[$version];
	}
}

//CI那边拿过来，暂时不写功能的日志函数
if ( ! function_exists('log_message')){
	function log_message($level,$msg){
		return true;
	}
}

// Anti_SQL Injection, escape quotes
if (!function_exists('filter')){
    function filter($content) {
        if (!get_magic_quotes_gpc()) {
            return addslashes($content);
        } else {
            return $content;
        }
    }
}
//对字符串等进行过滤
if (!function_exists('filterStr')){
    function filterStr($arr) {  
        if (!isset($arr)) {
            return null;
        }

        if (is_array($arr)) {
            foreach ($arr as $k => $v) {
    			if($v){
    				$arr[$k] = filter(stripSQLChars(stripHTML(trim($v), true)));
    			}
            }
        } else {
            $arr = filter(stripSQLChars(stripHTML(trim($arr), true)));
        }

        return $arr;
    }
}
//对HTML、违法关键字进行排除
if (!function_exists('stripHTML')){
    function stripHTML($content, $xss = true) {
        $search = array("@<script(.*?)</script>@is",
            "@<iframe(.*?)</iframe>@is",
            "@<style(.*?)</style>@is",
            "@<(.*?)>@is"
        );

        $content = preg_replace($search, '', $content);

        if($xss){
            $ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 
            'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 
            'layer', 'bgsound', 'title', 'base');
                                    
            $ra2 = array('onabort', 'onactivate','onafterprint','onafterupdate', 'onbeforeactivate', 'onbeforecopy',
			'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload',
			'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect',
			'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 
			'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 
			'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 
			'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout',
			'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange',
			'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete',
			'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
            $ra = array_merge($ra1, $ra2);
            
            $content = str_ireplace($ra, '', $content);
        }

        return strip_tags($content);
    }
}
if (!function_exists('removeXSS')){
    function removeXSS($val) {
        $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
        $search = 'abcdefghijklmnopqrstuvwxyz';
        $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $search .= '1234567890!@#$%^&*()';
        $search .= '~`";:?+/={}[]-_|\'\\';
        for ($i = 0; $i < strlen($search); $i++) {
            $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); // with a ;
            $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); // with a ;
        }

        $ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 
                                'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 
                                'layer', 'bgsound', 'title', 'base');
                                
        $ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 
		'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 
		'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 
		'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 
		'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 
		'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown',
		'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend',
		'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
		'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
        $ra = array_merge($ra1, $ra2);

        $found = true;
        while ($found == true) {
            $val_before = $val;
            for ($i = 0; $i < sizeof($ra); $i++) {
                $pattern = '/';
                for ($j = 0; $j < strlen($ra[$i]); $j++) {
                    if ($j > 0) {
                        $pattern .= '(';
                        $pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
                        $pattern .= '|(&#0{0,8}([9][10][13]);?)?';
                        $pattern .= ')?';
                    }
                    $pattern .= $ra[$i][$j];
                }
                $pattern .= '/i';
                $replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2); // add in <> to nerf the tag
                $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
                if ($val_before == $val) {
                    $found = false;
                }
            }
        }

        return $val;
    }
}
/**
 *  Strip specail SQL chars
 */
if (!function_exists('stripSQLChars')){
    function stripSQLChars($str) {
        $replace = array('SELECT', 'INSERT', 'DELETE', 'UPDATE', 'CREATE', 'DROP', 'VERSION', 'DATABASES',
            'TRUNCATE', 'HEX', 'UNHEX', 'CAST', 'DECLARE', 'EXEC', 'SHOW', 'CONCAT', 'TABLES', 'CHAR', 'FILE',
            'SCHEMA', 'DESCRIBE', 'UNION', 'JOIN', 'ALTER', 'RENAME', 'LOAD', 'FROM', 'SOURCE', 'INTO', 'LIKE', 'PING', 'PASSWD');
        
        return str_ireplace($replace, '', $str);
    }
}
// Redirect directly
if (!function_exists('redirect')){
    function redirect($URL = '', $second = 0) {
        if (!isset($URL)) {
            $URL = $_SERVER['HTTP_REFERER'];
        }
            ob_start();
            ob_end_clean();
            header("Location: ".$URL, TRUE, 302); //header("refresh:$second; url=$URL", TRUE, 302);
            ob_flush(); //可省略
            exit;
    }
}


// Get current microtime
if (!function_exists('calculateTime')){
    function calculateTime() {
        list($usec, $sec) = explode(' ', microtime());
        return ((float) $usec + (float) $sec);
    }
}
/**
 * 裁剪中文
 * 
 * @param type $string
 * @param type $length
 * @param type $dot
 * @return type
 */
if (!function_exists('cutstr')){
    function cutstr($string, $length, $dot = ' ...') {
    	if(strlen($string) <= $length) {
    		return $string;
    	}

    	$pre = chr(1);
    	$end = chr(1);
    	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), $string);

    	$strcut = '';
    	if(strtolower(CHARSET) == 'utf-8') {

    		$n = $tn = $noc = 0;
    		while($n < strlen($string)) {

    			$t = ord($string[$n]);
    			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
    				$tn = 1; $n++; $noc++;
    			} elseif(194 <= $t && $t <= 223) {
    				$tn = 2; $n += 2; $noc += 2;
    			} elseif(224 <= $t && $t <= 239) {
    				$tn = 3; $n += 3; $noc += 2;
    			} elseif(240 <= $t && $t <= 247) {
    				$tn = 4; $n += 4; $noc += 2;
    			} elseif(248 <= $t && $t <= 251) {
    				$tn = 5; $n += 5; $noc += 2;
    			} elseif($t == 252 || $t == 253) {
    				$tn = 6; $n += 6; $noc += 2;
    			} else {
    				$n++;
    			}

    			if($noc >= $length) {
    				break;
    			}

    		}
    		if($noc > $length) {
    			$n -= $tn;
    		}

    		$strcut = substr($string, 0, $n);

    	} else {
    		$_length = $length - 1;
    		for($i = 0; $i < $length; $i++) {
    			if(ord($string[$i]) <= 127) {
    				$strcut .= $string[$i];
    			} else if($i < $_length) {
    				$strcut .= $string[$i].$string[++$i];
    			}
    		}
    	}

    	$strcut = str_replace(array($pre.'&'.$end, $pre.'"'.$end, $pre.'<'.$end, $pre.'>'.$end), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

    	$pos = strrpos($strcut, chr(1));
    	if($pos !== false) {
    		$strcut = substr($strcut,0,$pos);
    	}
    	return $strcut.$dot;
    }
}
if (!function_exists('cn_json_encode')){
    function cn_json_encode($array) {
        return urlencode(json_encode($array));
    }
}
/**
 * 中文 json 数据解码
 * @param $string
 * @return mixed
 */
if (!function_exists('cn_json_decode')){
    function cn_json_decode($string) {

        $string = urldecode($string);
        return json_decode($string, true);
    }
}
/**
 *  JavaScript redirect
 */
if (!function_exists('jsRedirect')){
    function jsRedirect($url, $die = true) {
        echo "<script type='text/javascript'>window.location.href=\"$url\"</script>";
        if($die){
        	die;
        }
    }
}
if (!function_exists('password')){
    function password($password, $secret=''){
    	$pwd = array();
    	$salt=substr(uniqid(rand()), -6);
    	$pwd['secret'] = $secret ? $secret : $salt; 
    	$pwd['password'] = md5(md5(trim($password)).$pwd['secret'].'onepeople');
    	return $secret ? $pwd['password'] : $pwd;
    }
}
/**
 * 邮件模板标签替换
 *
 * @param string $string
 *        	内容
 * @param array $params
 *        	替换的内容，键名为要替换标签
 * @return string
 */
if (!function_exists('templateTag')){
    function templateTag($string, $params = array()){
    	$matchs = null;
    	preg_match_all("/\{(.+?)\}/", $string, $matchs);
    	$froms = $tos = array();
    	foreach($matchs[1] as $match){
    		$froms[] = "{{$match}}";
    		$tos[] = $params[$match];
    	}
    	return str_replace($froms, $tos, $string);
    }
}

/**
 * 求今天与传入日期相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $day
 * @return number
 */
if (!function_exists('diffBetweenDays')){
	function diffBetweenDays($day1,$day2){
 		if(!is_numeric($day1)){
            $second1 = strtotime($day1);
		}else{
			$second1 =$day1;
		}         
		if(!is_numeric($day2)){
            $second2 = strtotime($day2);
		}else{
			$second2 = $day2;
		}
		return floor(($second1-$second2) / 86400);
	}
}

//yaf redirect 跳转参数生成
if (! function_exists('paramscreate')) {
    function paramscreate($action,$url){
        $str=$action;
        if (strlen($url)>0) {
            $str.='?referer_url='.$url;
            $str.='&sign='.md5(URL_KEY.$url);
        }
        return $str;      
    }
}