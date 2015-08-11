<?php 
/*  描述：内部使用API用户上传身份证      */
/*  名称：sfz                            */
/*  作者：sky                            */
/*  版本：0.0.1                          */
/*  生成时间：2015.5.12                  */
/*  修订时间：2015.5.12                  */

if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("sfz");
	$help->set_ver("0.0.1");
	$help->set_time("2015.5.12");
	$help->set_des("用户上传身份证");
	$help->set_indeed(array("uid:登录用户的用户id","sfz_name:身份证上的姓名","sfz_number:身份证号","sfz_font_pic:身份证正面图片","sfz_back_pic:身份证背面图片"));
	$help->set_success('[{"api_status":"0","api_msg":"success"},{"sfz_id":"61","font_url":"http://www.suanjuzi.com/images/sfz/font/123.jpg","back_url":"http://www.suanjuzi.com/images/sfz/back/123.jpg"}]');
	$help->set_fail('{"api_status":"99","api_msg":"Need appid."}');
	$help->show();
	exit;
}

require('../common/init.php');

/********** 验证输入参数 start **********/
$uid = isset($request["uid"]) ? intval(trim($request["uid"],"'")) : 0;
$sfz_name = isset($request["sfz_name"]) ? trim($request["sfz_name"]) : '';
$sfz_number = isset($request["sfz_number"]) ? "'".trim($request["sfz_number"],"'")."'" : '';
$sfz_font_pic = isset($_FILES["sfz_font_pic"]["error"]) ? intval($_FILES["sfz_font_pic"]["error"]) : 4;
$sfz_back_pic = isset($_FILES["sfz_back_pic"]["error"]) ? intval($_FILES["sfz_back_pic"]["error"]) : 4;

if(empty($uid)) {
	$json->set_status("4");
	$json->set_msg("Need uid.");
	$json->create_json();
	$apidb->close();
	exit;
}
if(empty($sfz_name)) {
	$json->set_status("5");
	$json->set_msg("Need sfz_name.");
	$json->create_json();
	$apidb->close();
	exit;
}
if(empty($sfz_number)) {
	$json->set_status("6");
	$json->set_msg("Need sfz_number.");
	$json->create_json();
	$apidb->close();
	exit;
}
if($sfz_font_pic==4 || $sfz_back_pic==4) {
	$json->set_status("7");
	$json->set_msg("Need upload_file.");
	$json->create_json();
	$apidb->close();
	exit;
}
if($sfz_font_pic!=0 || $sfz_back_pic!=0) {
	$json->set_status("8");
	$json->set_msg("fail upload_file.");
	$json->create_json();
	$apidb->close();
	exit;
}
//允许上传的图片格式
$allow_type = array('jpg','jpeg','png');
//上传的图片格式
$sfz_font_pic_type = getStrEnd($_FILES["sfz_font_pic"]["type"], '/');
$sfz_back_pic_type = getStrEnd($_FILES["sfz_back_pic"]["type"], '/');
if(!in_array($sfz_font_pic_type, $allow_type) || !in_array($sfz_back_pic_type, $allow_type))
{
	$json->set_status("9");
	$json->set_msg("error upload_file_ext.");
	$json->create_json();
	$apidb->close();
	exit;
}
/********** 验证输入参数 end **********/

//当前时间戳,用于写入数据库添加或修改时间
$now = time();

//上传的图片扩展名
$sfz_font_pic_ext = getStrEnd($_FILES["sfz_font_pic"]["name"], '.');
$sfz_back_pic_ext = getStrEnd($_FILES["sfz_back_pic"]["name"], '.');

/********** 验证用户id start **********/
$table_name = "b2c_members";
$fields = 'member_id';

$sql = "select ".$fields." from ".DB_PREFIX.$table_name." where member_id=".$uid;
$rs = $apidb->query($sql);

if(empty($rs)) {
	$json->set_status("1");
	$json->set_msg("Query Error.");
	$json->create_json();
	$apidb->close();
	exit;
}
$row = mysql_fetch_assoc($rs);
if(!$row)
{
	$json->set_status("2");
	$json->set_msg("No Records.");
	$json->create_json();
	$apidb->close();
	exit;
}
/********** 验证用户id end **********/

/********** 新增或修改 start **********/
$table_name = "test_sfz";
$fields = 'id,font_pic,back_pic,status';

$sql = "select ".$fields." from ".DB_PREFIX.$table_name." where member_id=".$uid;
$rs = $apidb->getone($sql);

if(empty($rs)) {
	$json->set_status("1");
	$json->set_msg("Query Error.");
	$json->create_json();
	$apidb->close();
	exit;
}
$record = mysql_fetch_assoc($rs);
//新增
if(!$record)
{
	$mov1 = move_uploaded_file($_FILES['sfz_font_pic']['tmp_name'], ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext);
	$mov2 = move_uploaded_file($_FILES['sfz_back_pic']['tmp_name'], ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext);
	//保存文件成功
	if($mov1 && $mov2)
	{
		$sql = "insert into ".DB_PREFIX.$table_name."(member_id,name,number,font_pic,back_pic,add_time) ".
			   "values(".$uid.",".$sfz_name.",".$sfz_number.",'images/sfz/font/".$uid.".".$sfz_font_pic_ext."','images/sfz/back/".$uid.".".$sfz_back_pic_ext."',".$now.")";
		$rs = $apidb->query($sql);
		//入库成功
		if($rs !== false)
		{
			$sql = "select id as sfz_id,font_pic as font_url,back_pic as back_url from ".DB_PREFIX.$table_name." where member_id=".$uid;
			$rs = $apidb->getone($sql);
			$row = mysql_fetch_assoc($rs);
		}
		//入库失败
		else
		{
			unlink(ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext);
			unlink(ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext);
			$json->set_status("1");
			$json->set_msg("Query Error.");
			$json->create_json();
			$apidb->close();
			exit;
		}
	}
	//保存文件失败
	else
	{
		if(file_exists(ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext))
		{
			unlink(ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext);
		}
		if(file_exists(ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext))
		{
			unlink(ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext);
		}
		$json->set_status("10");
		$json->set_msg("fail save_file.");
		$json->create_json();
		$apidb->close();
		exit;
	}
}
//修改
else
{
	//身份证未审核或审核不通过
	if($record['status'] != 1)
	{
		//先备份原图
		$font_bak = getStrEnd($record['font_pic'], '.');
		$font_bak = '.'.$font_bak;
		$font_bak = str_replace($font_bak, '_bak'.$font_bak, $record['font_pic']);
		$back_bak = getStrEnd($record['back_pic'], '.');
		$back_bak = '.'.$back_bak;
		$back_bak = str_replace($back_bak, '_bak'.$back_bak, $record['back_pic']);
		rename(ROOT_PATH.$record['font_pic'], ROOT_PATH.$font_bak);
		rename(ROOT_PATH.$record['back_pic'], ROOT_PATH.$back_bak);

		$mov1 = move_uploaded_file($_FILES['sfz_font_pic']['tmp_name'], ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext);
		$mov2 = move_uploaded_file($_FILES['sfz_back_pic']['tmp_name'], ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext);
		//保存文件成功
		if($mov1 && $mov2)
		{
			$sql = "update ".DB_PREFIX.$table_name." set name=".$sfz_name.",".
														"number=".$sfz_number.",".
														"font_pic='images/sfz/font/".$uid.".".$sfz_font_pic_ext."',".
														"back_pic='images/sfz/back/".$uid.".".$sfz_back_pic_ext."',".
														"modify_time='".$now."',".
														"status='0',".
														"reason='' ".
														"where id=".$record['id'];
			$rs = $apidb->query($sql);
			//修改记录成功
			if($rs !== false)
			{
				//删除备份文件
				unlink(ROOT_PATH.$font_bak);
				unlink(ROOT_PATH.$back_bak);

				$row = array('sfz_id' => $record['id']);
				$row['font_url'] = "images/sfz/font/".$uid.".".$sfz_font_pic_ext;
				$row['back_url'] = "images/sfz/back/".$uid.".".$sfz_back_pic_ext;
			}
			//修改记录失败
			else
			{
				unlink(ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext);
				unlink(ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext);
				rename(ROOT_PATH.$font_bak, ROOT_PATH.$record['font_pic']);
				rename(ROOT_PATH.$back_bak, ROOT_PATH.$record['back_pic']);
				$json->set_status("1");
				$json->set_msg("Query Error.");
				$json->create_json();
				$apidb->close();
				exit;
			}
		}
		//保存文件失败
		else
		{
			if(file_exists(ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext))
			{
				unlink(ROOT_PATH."images/sfz/font/".$uid.".".$sfz_font_pic_ext);
			}
			if(file_exists(ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext))
			{
				unlink(ROOT_PATH."images/sfz/back/".$uid.".".$sfz_back_pic_ext);
			}
			rename(ROOT_PATH.$font_bak, ROOT_PATH.$record['font_pic']);
			rename(ROOT_PATH.$back_bak, ROOT_PATH.$record['back_pic']);
			$json->set_status("10");
			$json->set_msg("fail save_file.");
			$json->create_json();
			$apidb->close();
			exit;
		}
	}
	//身份证已审核通过
	else
	{
		$json->set_status("11");
		$json->set_msg("error not_allow_modify.");
		$json->create_json();
		$apidb->close();
		exit;
	}
}
/********** 新增或修改 end **********/
$apidb->close();

$json->set_status("0");
$json->set_msg("success");
$row['font_url'] = "http://".$_SERVER['HTTP_HOST']."/".$row['font_url'];
$row['back_url'] = "http://".$_SERVER['HTTP_HOST']."/".$row['back_url'];
$arr[] = $row;
$json->set_content($arr);
$json->create_json();

function getStrEnd($str, $spe)
{
	$p = strrpos($str, $spe);
	if($p === false)
	{
		return false;
	}
	return substr($str, $p+1);
}