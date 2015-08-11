<?php
/*  描述：内部使用API DEMO               */
/*  名称：demo                           */
/*  作者：tiandi                         */
/*  版本：0.0.2                          */
/*  生成时间：2015.4.24                  */
/*  修订时间：2015.4.27                  */
/*  update:                              */
/*    0.0.2: 修正url_api地址             */


echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
//请求api,最后斜杠补全
$url_api = "http://".$_SERVER['HTTP_HOST']."/intra_api/timeline/";

//参数
$para = array();
$para['appid'] = "";   //必要参数，用户唯一标示
$para['timestamp'] = time();   //必要参数，时间戳
$para['nodeids'] = 22;         //可选参数，文章节点ID
//密钥
$code = "";    //用户密钥，严禁前端文件直接使用密钥
//签名
$sign = create_sign($para,$code);
$url = $url_api."?".array_to_string($para)."&sign=".$sign;

$result = post($url);

echo $result;


function array_to_string($arr) {
	$tempsign = "";
	foreach ($arr as $k => $v){
		if (null != $v && "null" != $v) {
			$tempsign .= $k . "=" . $v . "&";
		}
	}
	return $tempsign;
}

function create_sign($request,$code) {
	if(check_env($request)) {
		ksort($request);
		$tempsign = "";
		foreach ($request as $k => $v){
			if (null != $v && "null" != $v && "sign" != $k ) {
				if(is_numeric($v))
					$tempsign .= $k . "=" . $v . "&";
				else
					$tempsign .= $k . "='" . $v . "'&";
			}
		}
		$tempsign = substr($tempsign, 0, strlen($tempsign)-1)."&code=".$code;
		return strtoupper(md5($tempsign));
	}
	echo "Create sign fail.";
}

function check_env($request){
	if(!isset($request['appid'])) {
		echo "Need appid.";
		return false;
	}
	elseif(!isset($request['timestamp'])) {
		echo "Need timestamp.";
		return false;
	}
	else
		return true;
}


function post($url) {
	$curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, false);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}