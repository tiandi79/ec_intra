<!DOCTYPE html>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link rel='stylesheet' type='text/css' href='../css/style.css' />
<title>Menu管理器</title>
</head>

<body>
<div id='wrap'>
<div id='title'>名称:  Menu管理器</div>
<div id="momo">查询时只查询menu_title，即二级菜单名。</div><br><br>
<form action = "menu_manager.php">
appid:
<input type="text" name="appid" size= 10 /><br><br>
workground:
<input type="text" name="workground" size= 50 /><br><br>
menu_group(一级菜单名):
<input type="text" name="menu_group" size= 20 /><br><br>
menu_title(二级菜单名):
<input type="text" name="menu_title" size= 20 /><br><br>
menu_path(执行路径):
<input type="text" name="menu_path" size= 50 /><br><br>
permission:
<input type="text" name="permission" size= 20 /><br><br>
menu_order:
<input type="text" name="menu_order" size= 5 value=50 /><br><br>
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
define('ROOT_PATH', str_replace('intra_api/tools/menu_manager.php', '', str_replace('\\', '/', __FILE__)));

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
		query_menu($request,$apidb);
		break;
	case "add":
		add_menu($request,$apidb);
		break;
	case "update":
		update_menu($request,$apidb);
		break;
	/*case "delete":
		delete_menu($request,$apidb);
		break;*/
	case "":
		break;
	default:
		echo "method ".$method. " error";
		break;
}
$apidb->close();

function query_menu($request,$apidb) { 
	if(!isset($request['menu_title']) || ($request['menu_title'] == '')) {
		echo "menu_title is empty.";
	}
	else
	{
		$db_app_content = "desktop_menus";
		$sql = "select * from ".DB_PREFIX.$db_app_content ." where menu_title ='".$request['menu_title']."' and menu_type='menu'";
		//echo $sql;
		$rs = $apidb->query($sql);
		$row = mysql_fetch_assoc($rs);
		if($row['menu_id']) {
			echo "menu_id => ". $row['menu_id']."<br>";
			echo "app_id => ". $row['app_id']."<br>";
			echo "workground => ". $row['workground']."<br>";
			echo "menu_group => ". $row['menu_group']."<br>";
			echo "menu_title => ". $row['menu_title']."<br>";
			echo "menu_path => ". $row['menu_path']."<br>";
			echo "permission => ". $row['permission']."<br>";
			echo "menu_order => ". $row['menu_order']."<br>";
		}
		else 
			echo "menu ".$request['menu_title']. " is not exist.";
	}
}

function add_menu($request,$apidb) {
	if(!isset($request['menu_title']) || ($request['menu_title'] == '')) {
		echo "menu_title is empty.";
	}
	elseif(!isset($request['appid']) || ($request['appid'] == '')) {
		echo "appid is empty.";
	}
	elseif(!isset($request['workground']) || ($request['workground'] == '')) {
		echo "workground is empty.";
	}
	elseif(!isset($request['menu_group']) || ($request['menu_group'] == '')) {
		echo "menu_group is empty.";
	}
	elseif(!isset($request['menu_path']) || ($request['menu_path'] == '')) {
		echo "menu_path is empty.";
	}
	elseif(!isset($request['permission']) || ($request['permission'] == '')) {
		echo "permission is empty.";
	}
	elseif(!isset($request['menu_order']) || ($request['menu_order'] == '')) {
		echo "menu_order is empty.";
	}
	else {
		$db_app_content = "desktop_menus";
		$sql = "select * from ".DB_PREFIX.$db_app_content ." where menu_title ='".$request['menu_title']."' and menu_type='menu'";
		$rs = $apidb->query($sql);
		$row = mysql_fetch_assoc($rs);
		if($row['menu_id']) {
			echo "menu already exist.";
			return false;
		}

		$insert_values = "('menu','".$request['appid']."','".$request['workground']."','".$request['menu_group']."','".$request['menu_title']."','".$request['menu_path']."','true','".$request['permission']."','N;','".$request['menu_order']."')";
		$sql = "insert ".DB_PREFIX.$db_app_content ." (menu_type,app_id,workground,menu_group,menu_title,menu_path,display,permission,addon,menu_order) values ".
			$insert_values;
		$rs = $apidb->query($sql);
		if(!$rs) {
			echo "<br>add menu error.";
		}
		else {
			echo "add menu as follow:<br>";
			query_menu($request,$apidb);
		}
	}
}

function update_menu($request,$apidb){
	if(!isset($request['menu_title']) || ($request['menu_title'] == '')) {
		echo "menu_title is empty.";
	}
	elseif(!isset($request['appid']) || ($request['appid'] == '')) {
		echo "appid is empty.";
	}
	elseif(!isset($request['workground']) || ($request['workground'] == '')) {
		echo "workground is empty.";
	}
	elseif(!isset($request['menu_group']) || ($request['menu_group'] == '')) {
		echo "menu_group is empty.";
	}
	elseif(!isset($request['menu_path']) || ($request['menu_path'] == '')) {
		echo "menu_path is empty.";
	}
	elseif(!isset($request['permission']) || ($request['permission'] == '')) {
		echo "permission is empty.";
	}
	elseif(!isset($request['menu_order']) || ($request['menu_order'] == '')) {
		echo "menu_order is empty.";
	}
	else {
		$db_app_content = "desktop_menus";
		$sql = "select * from ".DB_PREFIX.$db_app_content ." where  menu_title='".$request['menu_title']."' and menu_type='menu'";
		$rs = $apidb->query($sql);
		$row = mysql_fetch_assoc($rs);
		if(!$row['menu_id']) {
			echo "menu not exist.";
			return false;
		}
		$appid = $request['appid'];
		$menu_title = $request['menu_title'];
		$workground = $request['workground'];
		$menu_group = $request['menu_group'];
		$permission = $request['permission'];
		$menu_path = $request['menu_path'];
		$menu_order = $request['menu_order'];
		$sql = "update ".DB_PREFIX.$db_app_content ." set app_id='".$appid."',menu_title='".$menu_title."',workground='".$workground."',menu_group='".$menu_group."',menu_path='".$menu_path."',menu_order=".$menu_order.",permission='".$permission."'".
			" where menu_title ='".$menu_title."' and menu_type='menu'";

		$rs = $apidb->query($sql);
		if(!$rs) {
			echo "<br>update menu error.";
		}
		else {
			echo "update menu as follow:<br>";
			query_menu($request,$apidb);
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
		document.getElementById("momo").innerHTML = "查询时只查询menu_title，即二级菜单名。";
	else if(value == 'add')
		document.getElementById("momo").innerHTML = "添加菜单时各字段均为必填项，只能添加二级菜单。只有wordground和menu_group一致，才会显示在同级菜单下。";
	else if(value == 'update')
		document.getElementById("momo").innerHTML = "按menu_title进行菜单更新，menu_title不可更改，其他项目均为必填项。";
	}

</script>

</html>