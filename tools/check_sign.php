<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' type='text/css' href='../css/style.css' />
<title>签名验证器</title>
</head>

<body>
<div id='wrap'>
<div id='title'>名称:  签名验证器</div>
请输入参数字符串：(url?之后的字符串)
<form action = "check_sign.php">
<input type="text" name="para" size= 50 /><br>
请输入密钥：<br>
<input type="text" name="code" size= 20 />
<input type="submit" name="ok" value="ok" />
<input type="reset" value="reset" />
</form>
<br><br>

<?php
if (isset($_REQUEST['ok']) && $_REQUEST['ok'] == "ok") {
	echo "<div>签名：".create_sign($_REQUEST)."</div>";
}

function create_sign($request) {
	if(check_env($request)) {
		$arr = explode("&",$request['para']); 
		foreach ($arr as $k => $v) {
			$temp = explode("=",$v); 
			$arr2[$temp[0]] = $temp[1];
		}
		ksort($arr2);
		$tempsign = "";
		foreach ($arr2 as $k => $v){
			if (null != $v && "null" != $v && "sign" != $k ) {
				if(is_numeric($v))
					$tempsign .= $k . "=" . $v . "&";
				else
					$tempsign .= $k . "='" . $v . "'&";
			}
		}
		$tempsign = substr($tempsign, 0, strlen($tempsign)-1)."&code=".$request['code'];
		echo "待签名字符串：".$tempsign;
		return strtoupper(md5($tempsign));
	}
	echo "Create sign fail.";
}
function check_env($request){
	if(!isset($request['para']['appid'])) {
		echo "Need appid.";
		return false;
	}
	elseif(!isset($request['para']['timestamp'])) {
		echo "Need timestamp.";
		return false;
	}
	elseif(!isset($request['code'])) {
		echo "Need security code.";
		return false;
	}
	else
		return true;
}
?>
</div>
</body>


<script>


</script>

</html>