<?php
/**
 * File: F_File.php
 * Functionality: Extra file functions
 * Author: 资料空白
 * Date: 2016-11-11再整理
 */
/*
 * ============== Delete a dir recursively ==============
 *
 * Note: Don't miss dir path ! 
  if flag = true, delete top dir as well
  if record = true, return success and failure count
 *
 * ===============================================
 */
if ( ! function_exists('delRDirectory')){
    function delRDirectory($dir, $flag = true, $record = false) {
    	if(!file_exists($dir)){
    		return false;
    	}

    	$result = array();
    	$result['i'] = $result['j'] = 0;

        $dirs = scandir($dir);
        foreach ($dirs as $value) {
            if ($value != '.' && $value != '..') {
                $file = $dir . DS . $value;
                if (is_dir($file)) {
                    delRDirectory($file);
                } else {
    				if(!$record){
    					unlink($file);
    				}else{
    					$code = unlink($file);
    					
    					if($code){
    						// 成功数
    						$result['i']++;
    					}else{
    						// 失败数
    						$result['j']++;
    					}
    				}
                }
            }
        }

        if ($flag) {
            @rmdir($dir);
        }
    	
    	if($result){
    		return $result;
    	}
    }
}

/**
 *  Create folder recursively
 */
if ( ! function_exists('createRDir')){
    function createRDir($folder) {
        $reval = false;
        if (!file_exists($folder)) {
            @umask(0);
            preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);
            $base = ($atmp[0][0] == '/') ? '/' : '';

            foreach ($atmp[1] AS $val) {
                if ('' != $val) {
                    $base .= $val;
                    if ('..' == $val || '.' == $val) {
                        $base .= '/';
                        continue;
                    }
                } else {
                    continue;
                }

                $base .= '/';

                if (!file_exists($base)) {
                    if (mkdir($base, 0777)) {
                        chmod($base, 0777);
                        $reval = true;
                    }
                }
            }
        } else {
            $reval = is_dir($folder);
        }

        clearstatcache();
        return $reval;
    }
}

// Get file extension
if ( ! function_exists('getExtension')){
    function getExtension($file) {
        $info = pathinfo($file);
        return $info['extension'];
    }
}

// Delete specific suffix file under a dir
if ( ! function_exists('deleteExtensionFiles')){
    function deleteExtensionFiles($dir, $extension) {
        $dirs = scandir($dir);
        // Do not scan current and parent dir:
        $exceptDirs = array('.',  '..');
        foreach ($dirs as $key => $value) {
            if (!in_array($value, $exceptDirs)) {
                if (is_dir($dir . DS . $value)) {
                    $dd = deleteExtensionFiles($dir . DS . $value, $extension);
                } else {
                    $ext = getExtension($value);
                    if ($ext == $extension) {
                       @unlink($value);
                    }
                }
            }
        }
    }
}

// Delete .svn recursively under a specific directory
if ( ! function_exists('deleteSVN')){
    function deleteSVN($dir) {
        $dirs = scandir($dir);
        // Do not scan current and parent dir:
        $exceptDirs = array('.',  '..');
        foreach ($dirs as $key => $value) {
            if (!in_array($value, $exceptDirs)) {
    			$name = $dir . DS . $value;
    			
    			if($value == '.svn'){
    				delRDirectory($name);
    			}else{
    				if (is_dir($name)) {
    					deleteSVN($name);
    				}
    			}
            }
        }
    }
}
// Get specific suffix files under a dir
if ( ! function_exists('getExtensionFiles')){
    function getExtensionFiles($dir, $extension) {
    	global $files;
        $dirs = scandir($dir);
        // Do not scan current and parent dir:
        $exceptDirs = array('.',  '..', '.svn');
        foreach ($dirs as $key => $value) {
            if (!in_array($value, $exceptDirs)) {
                if (is_dir($dir . DS . $value)) {
                    $dd = getExtensionFiles($dir . DS . $value, $extension);
                } else {
                    $ext = getExtension($value);
                    if ($ext == $extension) {
                       $files[] = $dir.DS.$value;
                    }
                }
            }
        }
    	
    	return $files;
    }
}

/**
 * 	Define function file_get_contents if not exists
 */
if (!function_exists('file_get_contents')) {
    function file_get_contents($file) {
        if (($fp = @fopen($file, 'rb')) === false) {
            return false;
        } else {
            $fileSize = @filesize($file);
            if ($fileSize) {
                $contents = fread($fp, $fileSize);
            } else {
                $contents = '';
            }

            fclose($fp);
            return $contents;
        }
    }
}


/**
 * 	Define function file_pet_contents if not exists
 */
if (!function_exists('file_put_contents')) {
    function file_put_contents($file, $data, $flag = '') {
        $contents = (is_array($data)) ? implode('', $data) : $data;

        if (trim($flag) == 'FILE_APPEND') {
            $mode = 'ab+';
        } else {
            $mode = 'wb';
        }

        if (($fp = @fopen($file, $mode)) === false) {
            return false;
        } else {
            $result = fwrite($fp, $contents);
            fclose($fp);

            return $result;
        }
    }
}


/**
 * 	获取一个目录下的所有文件, 不包括子目录
 */
if ( ! function_exists('getFilenames')){
    function getFilenames($dir) {
        if (!is_dir($dir)) {
            return null;
        }

        $fileArr = scandir($dir);

        if (empty($fileArr)) {
            return null;
        }

        $files = array();
        foreach ($fileArr as $key => $value) {
            if (!is_dir($value)) {
                $files[] = $value;
            }
        }

        clearstatcache();
        return $files;
    }
}
/**
 * 	判断一个目录下面是否有文件  
 */
if ( ! function_exists('checkFileExists')){
    function checkFileExists($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        $fileArr = scandir($dir);

        if (empty($fileArr)) {
            return false;
        }

        foreach ($fileArr as $key => $value) {
            if (!is_dir($value)) {
                return true;
            }
        }
    	
        clearstatcache();
    	return false;
    }
}

/**
 *  将日志写入文件
 */
if ( ! function_exists('logger')){
    function logger($file, $msg) {
        $content = date('Y-m-d H:i:s') . ' ' . $msg . "\r\n";
        file_put_contents($file, $content, FILE_APPEND);
    }
}

// removes files and non-empty directories
if ( ! function_exists('rrmdir')){
    function rrmdir($dir) {
    	if (is_dir($dir)) {
    		$files = scandir($dir);
    		foreach ($files as $file)
    			if ($file != "." && $file != ".."){
    				rrmdir("$dir/$file");
    			}
    			rmdir($dir);
    	}else if (file_exists($dir)){
    		unlink($dir);
    	}
    }
}

// copies files and non-empty directories
if ( ! function_exists('rcopy')){
    function rcopy($src, $dst) {
    	if (file_exists($dst)){
    		rrmdir($dst);
    	}
    	
    	if (is_dir($src)) {
    		mkdir($dst);
    		$files = scandir($src);
    		foreach ($files as $file)
    		if ($file != "." && $file != ".."){
    			rcopy("$src/$file", "$dst/$file"); 
    		}
    	}else if (file_exists($src)){
    		copy($src, $dst);
    	}
    }
}

/**
 * 拷贝文件
 * $source:源目录名
 * $destination:目的目录名
 */
if ( ! function_exists('xCopy')){
    function xCopy($src, $dst) {
    	if (!is_dir($src)) {
    		return 0;
    	}

    	if (!is_dir($dst)){
    		mkdir($dst, 0777);
    	}
		$dir = opendir($src);
		@mkdir($dst);
		while(false !== ( $file = readdir($dir)) ) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if ( is_dir($src . '/' . $file) ) {
					xCopy($src . '/' . $file,$dst . '/' . $file);
				}
				else {
					copy($src . '/' . $file,$dst . '/' . $file);
				}
			}
		}
		closedir($dir);
		return 1;
    }
}

// 将指定目录下的所有文件[不包括文件夹]重命名为一个新的
if ( ! function_exists('xRename')){
    function xRename($dir, $newName){
    	$dirs = scandir($dir);
    	foreach ($dirs as $value) {
    		if ($value != '.' && $value != '..') {
    			$file = $dir . '/' . $value;
    			if (is_dir($file)) {
    				xRename($file, $newName);
    			} else {
    				rename($file, $dir.'/'.$newName);
    			}
    		}
    	}
    }
}

/**
 * 功能：php完美实现下载远程图片保存到本地
 * 参数：文件url,保存文件目录,保存文件名称，使用的下载方式
 * 当保存文件名称为空时则使用远程文件原来的名称
*/
if ( ! function_exists('getImage')){
    function getImage($url, $save_dir='', $filename='', $type=0){
        if(trim($url) == ''){
    		return array('file_name'=>'', 'save_path'=>'', 'error'=>1);
    	}
    	
    	if(trim($save_dir) == ''){
    		$save_dir = './';
    	}
    	
    	//保存文件名
        if(trim($filename) == ''){
            $ext = strrchr($url, '.');
            if($ext != '.gif' && $ext != '.jpg'){
    			return array('file_name'=>'', 'save_path'=>'', 'error'=>3);
    		}
            $filename=time().$ext;
        }
        
    	if(0 !== strrpos($save_dir, '/')){
    		$save_dir.='/';
    	}
    	
    	//创建保存目录
    	if(!file_exists($save_dir) && !mkdir($save_dir, 0777, true)){
    		return array('file_name' => '', 'save_path' => '', 'error' => 5);
    	}
    	
        //获取远程文件所采用的方法 
        if($type){
    		$ch = curl_init();
    		$timeout = 1005;
    		curl_setopt($ch, CURLOPT_URL, $url);
    		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    		$img = curl_exec($ch);
    		curl_close($ch);
        }else{
    	    ob_start(); 
    	    readfile($url);
    	    $img=ob_get_contents(); 
    	    ob_end_clean(); 
        }
        
        //文件大小 
        $fp2 = @fopen($save_dir.$filename, 'a');
        fwrite($fp2, $img);
        fclose($fp2);
    	unset($img, $url);
        return array('file_name' => $filename, 'save_path' => $save_dir.$filename, 'error' => 0);
    }
}
// 下载文件
if ( ! function_exists('download')){
    function download($dir, $name, $realname = ''){
    	$realname = $realname ? $realname : $name;
    	if (!file_exists($dir.$name)){
    		header("Content-type: text/html; charset=utf-8");
    		echo "File not found!";
    		exit;
    	} else {
    		$file = fopen($dir.$name, "r");
    		Header("Content-type: application/octet-stream");
    		Header("Accept-Ranges: bytes");
    		Header("Accept-Length: ".filesize($dir . $name));
    		Header("Content-Disposition: attachment; filename=".$realname);
    		echo fread($file, filesize($dir.$name));
    		fclose($file);
        }
    }
}

// 获取中文文件名
if ( ! function_exists('getChineseFileName')){
    function getChineseFileName($file){
    	return mb_substr($file, mb_strrpos($file, '/')+1);
    }
}

/** 
* Converts bytes into human readable file size. 
* 
* @param string $bytes 
* @return string human readable file size (2,87 Мб)
* @author Mogilev Arseny 
*/ 
if ( ! function_exists('FileSizeConvert')){
	function FileSizeConvert($bytes)
	{
		$bytes = floatval($bytes);
			$arBytes = array(
				0 => array(
					"UNIT" => "TB",
					"VALUE" => pow(1024, 4)
				),
				1 => array(
					"UNIT" => "GB",
					"VALUE" => pow(1024, 3)
				),
				2 => array(
					"UNIT" => "MB",
					"VALUE" => pow(1024, 2)
				),
				3 => array(
					"UNIT" => "KB",
					"VALUE" => 1024
				),
				4 => array(
					"UNIT" => "B",
					"VALUE" => 1
				),
			);

		foreach($arBytes as $arItem)
		{
			if($bytes >= $arItem["VALUE"])
			{
				$result = $bytes / $arItem["VALUE"];
				$result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
				break;
			}
		}
		return $result;
	}
}