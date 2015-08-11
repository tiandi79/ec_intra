<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' type='text/css' href='../css/style.css' />
<title>Kvstore管理器</title>
</head>

<?php
define('ROOT_PATH', str_replace('intra_api/tools/kvstore_manager.php', '', str_replace('\\', '/', __FILE__)));
require(ROOT_PATH . 'config/config.php');
require(ROOT_PATH . 'intra_api/models/conn.php');
?>

<body>
<div id='wrap'>
<div id='title'>名称:  Kvstore管理器</div>
<div id="momo">查询data/kvstore/tbdefine目录下数据表dbschema缓存文件的文件名，所有项为必填。</div><br><br>
<form action = "kvstore_manager.php">
<input type="hidden" name="prefix" size= 20 value=<?=KV_PREFIX ?>  readonly />
instance: 
<input type="text" name="instance" size= 20 value='tbdefine' /> (代码base_kvstore::instance('tbdefine')中的tbdefine)<br><br>
key:
<input type="text" name="key" size= 20 /> 如果instance为tbdefine，这里是数据表的名字，去除前缀<?=DB_PREFIX?>后，再去除中间的连接符号所得的字符串，如表名为sdb_gift_card，则取值giftcard，instance为其他值时不详。<br><br>
<input type="radio" name="method" id="query" value="query" checked="checked" onclick="changemomo(value)" />查询
管理员密码:
<input type="password" name="password" size= 15 /><br><br>
<input type="submit" name="ok" value="ok" width=20 />
<input type="reset" value="reset" />
</form>
<br><br>


<?php
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
		query_kvstore($request,$apidb);
		break;
	case "delete":
		delete_kvstore($request,$apidb);
		break;
	case "":
		break;
	default:
		echo "method ".$method. " error";
		break;
}
$apidb->close();

function query_kvstore($request,$apidb) { 
	if(!isset($request['prefix']) || !isset($request['instance']) || !isset($request['key'])) {
		echo "para is invalid.";
	}
	else
	{
		$file = ROOT_PATH."data/kvstore/".trim($request['instance'])."/".md5(trim($request['prefix']) . trim($request['instance']) . trim($request['key'])).".php";
		echo $file."<a href='kvstore_manager.php?method=delete&url=".$file."'>[删除]</a>";
	}
}

function delete_kvstore($request) {
	$filename = $request['url'];
	echo "尝试删除文件=>".$filename."<br>";
	if( is_file( $filename ) ) {
		if( unlink($filename) ) {
			echo '文件删除成功。<br>';
		}
		else
		{
			echo '文件删除失败，权限不够！<br>';
		}
	}
	else
	{
	 echo '不是有一个有效的文件。<br>';
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
		document.getElementById("momo").innerHTML = "查询data/kvstore/tbdefine目录下数据表dbschema缓存文件的文件名，所有项为必填。";
	else if(value == 'delete')
		document.getElementById("momo").innerHTML = "删除符合条件的缓存文件，所有项均为必填项。";
}

</script>

</html>