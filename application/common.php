<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
error_reporting(7);
function encrypt($txt, $key = '', $expiry = 0) {
	strlen($key) > 5 or $key = 'SYS_KEY';
	$str = $txt.substr($key, 0, 3);
	return str_replace(array('+', '/', '0x', '0X'), array('-P-', '-S-', '-Z-', '-X-'), mycrypt($str, $key, 'ENCODE', $expiry));
}

function decrypt($txt, $key = '') {
	strlen($key) > 5 or $key = 'SYS_KEY';
	$str = mycrypt(str_replace(array('-P-', '-S-', '-Z-', '-X-'), array('+', '/', '0x', '0X'), $txt), $key, 'DECODE');
	return substr($str, -3) == substr($key, 0, 3) ? substr($str, 0, -3) : '';
}

function mycrypt($string, $key, $operation = 'DECODE', $expiry = 0) {
	$ckey_length = 4;
	$key = md5($key);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';
	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + TIMES : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - TIMES > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}
}
// 过滤关键词
function filter($keyword,$text){
    // $keyword = model('keyword')->select();
    $key = '';
    foreach ($keyword as $k => $v) {
        if (!$k) {              
            $key .= $v['keyname'];
        }else{
            $key .= '|'.$v['keyname'];
        }
    }
    // 忽略空格
    for ($i=0; $i < mb_strlen($key,'utf-8'); $i++) { 
        $x[] = mb_substr($key,$i,1,'utf-8');
    }
    foreach($x as $kk => $vv){
        $keyname .= $vv.'\s*';
    }
    $key = $keyname;
    $text = preg_replace("/$key/i",'**',$text);
    return $text;
}
//发送邮件
function send_mail($to,$from,$password,$subject,$body){
	//标题不能带换行
	$subject=str_replace("\r\n",' ',$subject);
	//行首的“.”是SMTP预留的格式，需要用“..”转意
	$body=preg_replace('/(=?^|\r\n)\./','..',$body);
	//从发信邮箱中找到用户名和服务器域名
	$u=explode('@',$from);
	//连接邮箱SMTP服务器的25端口
	$s=fsockopen('smtp.'.$u[1],25);
	fgets($s);
	//构造邮件内容数据
	$data=array(
	'MIME-Version: 1.0',
	'Content-Type: text/html',
	'Charset: utf-8',
	"From: 管理员<$from>","To: $to",
	"Subject: $subject",
	"\r\n$body",'.'
	);
	//根据SMTP协议与邮件服务器做一些应答
	foreach(array(
	'HELO sb',
	'AUTH LOGIN',
	base64_encode($u[0]),
	base64_encode($password),
	"MAIL FROM: <$from>",
	"RCPT TO: <$to>",
	'DATA',implode("\r\n",$data)
	) as $i){
	//发送消息
	fwrite($s,"$i\r\n");
	//等待返回并获取返回信息
	$m=fgets($s);
	//如果返回的是错误信息则结束函数
	if($m[0]>3)return $m;
	};
	//关闭sock
	fclose($s);
}