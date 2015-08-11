<?php 
/*  描述：内部使用API文章点赞            */
/*  名称：postarticlegood                */
/*  作者：tiandi                         */
/*  版本：0.0.1                          */
/*  生成时间：2015.7.1                   */
/*  修订时间：2015.7.1                   */
/*  update:                              */


if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("postarticlegood");
	$help->set_ver("0.0.1");
	$help->set_time("2015.7.1");
	$help->set_des("文章点赞");
	$help->set_indeed(array("article_id:文章ID","member_id:用户ID"));
	$help->set_success('[{"api_status":"0","api_msg":"success"}]');
	$help->set_fail('{"api_status":"99","api_msg":"Need Appid."}');
	$help->show();
	exit;
}


require('../common/init.php');

$artids = isset($request["article_id"])? $request["article_id"]:'';
$member = isset($request["member_id"])? $request["member_id"]:'';

if($artids == '') {
	$json->set_status("12");
	$json->set_msg("No article_id.");
	$json->create_json();	
	exit;
}

if($member == '') {
	$json->set_status("13");
	$json->set_msg("No member_id.");
	$json->create_json();	
	exit;
}

$db_name_indexs = "content_article_indexs";

$fields = "basic_isgood,isgood ";
$limited = " limit 1";
$sql = "select ".$fields." from ".DB_PREFIX.$db_name_indexs .
		   " where platform = 'wap' and ifpub='true' and article_id = ".$artids.$limited;

$rs = $apidb->query($sql);



if(empty($rs)) {
	$json->set_status("1");
	$json->set_msg("Query Error.");
	$json->create_json();	
}
else
{
	$row = mysql_fetch_assoc($rs);
	if(checkpost($member,$row['isgood'])) {
		$apidb->close();
		$json->set_status("14");
		$json->set_msg("Already Post.");
		$json->create_json();	
	}
	else
	{
		$isgood = $row['isgood'].",".$member;
		if(substr($isgood,0,1) == ',')
			$isgood = substr($isgood,1);
		$basic_isgood = $row['basic_isgood']+1;
		$sql = "update ".DB_PREFIX.$db_name_indexs .
			" set basic_isgood = ".$basic_isgood.",isgood='".$isgood."'".
			" where article_id = ".$artids;
		$rs = $apidb->query($sql);
		$apidb->close();

		$arr = array();
		$json->set_status("0");
		$json->set_msg("success");
		$json->set_content($arr);
		$json->create_json();
	}
}

function checkpost($member,$isgood) {
	$isgood = explode(",",$isgood);
	if(in_array($member,$isgood))
		return true;
	else 
		return false;
}

