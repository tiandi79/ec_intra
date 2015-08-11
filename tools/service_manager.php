<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' type='text/css' href='../css/style.css' />
<title>Service管理器</title>
</head>

<body>
<div id='wrap'>
<div id='title'>名称:  Service管理器</div>
<div id="momo">查询时只查询content_name，即service。</div><br><br>
<form action = "service_manager.php">
appid:
<input type="text" name="appid" size= 10 /><br><br>
content_name(service):
<input type="text" name="content_name" size= 50 /><br><br>
content_path(class):
<input type="text" name="content_path" size= 50 /><br><br>
ordernum:
<input type="text" name="ordernum" size= 5 value=50 /><br><br>
<input type="radio" name="method" id="query" value="query" checked="checked" onclick="changemomo(value)" />查询
<input type="radio" name="method" id="add" value="add" onclick="changemomo(value)" />添加
<input type="radio" name="method" id="update" value="update" onclick="changemomo(value)" />修改
管理员密码:
<input type="password" name="password" size= 15 /><br><br>
<input type="submit" name="ok" value="ok" width=20 />
<input type="reset" value="reset" />
</form>
<br><br>

<?php
define('ROOT_PATH', str_replace('intra_api/tools/service_manager.php', '', str_replace('\\', '/', __FILE__)));

require(ROOT_PATH . 'config/config.php');
require(ROOT_PATH . 'intra_api/models/conn.php');

$apidb = new conn(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

$request = "";
if(isset($_REQUEST)) {
	foreach ($_REQUEST as $key=>$value) {
		$request[$key] = check_input($value);
	}
}

check_env($request,$apidb);
$method = isset($request['method'])?$request['method']:"";

switch($method){
	case "query":
		query_service($request,$apidb);
		break;
	case "add":
		add_service($request,$apidb);
		break;
	case "update":
		update_service($request,$apidb);
		break;
	case "":
		break;
	default:
		echo "method ".$method. " error";
		break;
}
$apidb->close();

function query_service($request,$apidb) { 
	if(!isset($request['content_name']) || ($request['content_name'] == '')) {
		echo "service is empty.";
	}
	else
	{
		$db_app_content = "base_app_content";
		$sql = "select * from ".DB_PREFIX.$db_app_content ." where content_name ='".$request['content_name']."' and content_type='service' order by input_time DESC";
		//echo $sql;
		$rs = $apidb->query($sql);
		while($row = mysql_fetch_assoc($rs))
		{
			if($row['content_path']) {
				foreach($row as $k => $v) {
					if($k != 'content_title') 
						echo $k . " => " .$v."<br>";
				}
				echo "<br><br>";
			}
			else {
				echo "service ".$request['content_name']. " is not exist.";
				exit;
			}
		}
	}
}

function add_service($request,$apidb) {
	if(!isset($request['content_name']) || ($request['content_name'] == '')) {
		echo "service is empty.";
	}
	elseif(!isset($request['appid']) || ($request['appid'] == '')) {
		echo "appid is empty.";
	}
	elseif(!isset($request['content_path']) || ($request['content_path'] == '')) {
		echo "content_path is empty.";
	}
	elseif(!isset($request['ordernum']) || ($request['ordernum'] == '')) {
		echo "ordernum is empty.";
	}
	else {
		$db_app_content = "base_app_content";
		$sql = "select * from ".DB_PREFIX.$db_app_content ." where content_name ='".$request['content_name']."' and content_type='service' and app_id='".$request['appid']."' and content_path='".$request['content_path']."'";
		$rs = $apidb->query($sql);
		$row = mysql_fetch_assoc($rs);
		if($row['content_id']) {
			echo "service already exist.";
			return false;
		}

		$insert_values = "('service','".$request['appid']."','".$request['content_name']."','".$request['content_path']."','".$request['ordernum']."',".time().",'false')";
		$sql = "insert ".DB_PREFIX.$db_app_content ." (content_type,app_id,content_name,content_path,ordernum,input_time,disabled) values ".
			$insert_values;
		$rs = $apidb->query($sql);
		if(!$rs) {
			echo "<br>add service error.";
		}
		else {
			echo "add service as follow:<br>";
			query_service($request,$apidb);
		}
	}
}

function update_service($request,$apidb){
	if(!isset($request['content_name']) || ($request['content_name'] == '')) {
		echo "service is empty.";
	}
	elseif(!isset($request['appid']) || ($request['appid'] == '')) {
		echo "appid is empty.";
	}
	elseif(!isset($request['content_path']) || ($request['content_path'] == '')) {
		echo "content_path is empty.";
	}
	elseif(!isset($request['ordernum']) || ($request['ordernum'] == '')) {
		echo "ordernum is empty.";
	}
	else {
		$db_app_content = "base_app_content";
		$sql = "select * from ".DB_PREFIX.$db_app_content ." where content_name ='".$request['content_name']."' and content_type='service'";
		$rs = $apidb->query($sql);
		$row = mysql_fetch_assoc($rs);
		if(!$row['content_id']) {
			echo "service not exist.";
			return false;
		}
		$appid = $request['appid'];
		$content_name = $request['content_name'];
		$content_path = $request['content_path'];
		$ordernum = $request['ordernum'];
		$sql = "update ".DB_PREFIX.$db_app_content ." set app_id='".$appid."',content_name='".$content_name."',content_path='".$content_path."',ordernum=".$ordernum.",input_time=".time().",disabled='false'".
			" where content_name ='".$content_name."' and content_type='service'";
		$rs = $apidb->query($sql);
		if(!$rs) {
			echo "<br>update service error.";
		}
		else {
			echo "update service as follow:<br>";
			query_service($request,$apidb);
		}
	}
}

function check_env($request,$apidb){
	if(isset($request['password'])) {
		if($request['password']=='') {
			echo "Need admin's password.<br>";
			exit;
		}
		else {
			$db_pam_account = "pam_account";
			$sql = "select * from ".DB_PREFIX.$db_pam_account ." where account_id =1";
			$rs = $apidb->query($sql);

			if(empty($rs)) {
				echo "Query error.<br>";
				exit;
			}
			else
			{
				$row = mysql_fetch_assoc($rs);
				if(extends_md5($request['password'],$row['createtime']) == $row['login_password'])
					return true;
				else {
					echo "Password error.<br>";
					exit;
				}
			}
		}
	}
}

function extends_md5($source_str,$createtime)
{
	$string_md5 = md5(md5($source_str).'admin'.$createtime);
	$front_string = substr($string_md5,0,31);
	$end_string = 's'.$front_string;
	return $end_string;
}

function check_input($value)
{
	if (get_magic_quotes_gpc())
	{
		$value = stripslashes($value);
	}
	return $value;
}
	
?>
</div>
</body>


<script>
function changemomo(value) {
	if(value == 'query')
		document.getElementById("momo").innerHTML = "查询时只查询content_name，即service。";
	else if(value == 'add')
		document.getElementById("momo").innerHTML = "添加服务时各字段均为必填项。";
	else if(value == 'update')
		document.getElementById("momo").innerHTML = "按content_name进行服务更新，content_name不可更改，其他项目均为必填项。";
	}

</script>

</html>