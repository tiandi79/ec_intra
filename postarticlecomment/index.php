<?php 
/*  描述：内部使用API文章评论            */
/*  名称：postarticlecomment             */
/*  作者：tiandi                         */
/*  版本：0.0.1                          */
/*  生成时间：2015.7.3                   */
/*  修订时间：2015.7.3                   */
/*  update:                              */


if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("postarticlecomment");
	$help->set_ver("0.0.1");
	$help->set_time("2015.7.3");
	$help->set_des("文章评论");
	$help->set_indeed(array("article_id:文章ID","member_id:用户ID","content:评论内容"));
	$help->set_noindeed(array("parent_id:父文章ID"));
	$help->set_success('[{"api_status":"0","api_msg":"success"}]');
	$help->set_fail('{"api_status":"99","api_msg":"Need Appid."}');
	$help->show();
	exit;
}


require('../common/init.php');

$artid = isset($request["article_id"])? $request["article_id"]:'';
$member = isset($request["member_id"])? $request["member_id"]:'';
$content = isset($request["content"])? $request["content"]:'';

if($artid == '') {
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

if($content == '') {
	$json->set_status("13");
	$json->set_msg("No content.");
	$json->create_json();	
	exit;
}

$db_name_members = "pam_members";
$db_name_article_comment = "content_article_comments";
$fields = "article_id,author_id,author,content,pubtime,ip ";

$sql = "select login_account from ".DB_PREFIX.$db_name_members ." where member_id = ".$member;
$name = $apidb->getone($sql);
$insertfields = $artid.",".$member.",'".$name."',".$content.",'".time()."','".get_ip()."'";

$sql = "insert ".DB_PREFIX.$db_name_article_comment ."(".$fields.") values(".$insertfields.")";
$rs = $apidb->query($sql);
$apidb->close();

if(!$rs) {
	$json->set_status("1");
	$json->set_msg("Query Error.");
	$json->create_json();	
}
else
{
	$json->set_status("0");
	$json->set_msg("success");
	$json->create_json();
}

function get_ip() {
	if(getenv('HTTP_CLIENT_IP')) { 
		$ip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR')) { 
		$ip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR')) { 
		$ip = getenv('REMOTE_ADDR');
	} else { 
		$ip = $HTTP_SERVER_VARS['REMOTE_ADDR'];
	}
	return $ip;
}

