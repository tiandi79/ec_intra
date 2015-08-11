<?php 
/*  描述：内部使用API获取文章缩略图      */
/*  名称：getthumb                       */
/*  作者：tiandi                         */
/*  版本：0.0.1                          */
/*  生成时间：2015.4.28                  */
/*  修订时间：2015.4.28                  */

if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("getthumb");
	$help->set_ver("0.0.1");
	$help->set_time("2015.4.28");
	$help->set_des("获取文章缩略图");
	$help->set_indeed(array("articleids:文章ID，多个ID之间用英文逗号分隔，最多支持10个"));
	$help->set_noindeed(array("size:缩略图尺寸，默认为大，小尺寸请设置为1"));
	$help->set_success('[{"api_status":"0","api_msg":"success"},{"article_id":"61","image_url":"public/images/c0/60/c3/29123f182712a50d0a94b084e6acfc266d95ad86.png"},{"article_id":"62","image_url":""}]');
	$help->set_fail('{"api_status":"3","api_msg":"Need articleids."}');
	$help->show();
	exit;
}


require('../common/init.php');

$articleids = isset($request["articleids"])? $request["articleids"]:'';
if($articleids == '') {
	$json->set_status("3");
	$json->set_msg("Need articleids.");
	$json->create_json();
	exit;
}
$articleids = str_replace(",","','",$articleids);

$db_name_bodys = "content_article_bodys";
$db_images = "image_image";

$size = isset($request["size"])? $request["size"]:'0';

if($size == '0') 
	$fields = "a.article_id,b.url as image_url";
elseif($size == '1')
	$fields = "a.article_id,b.s_url as image_url";
else {
	$json->set_status("4");
	$json->set_msg("Size is error.");
	$json->create_json();
	exit;
}

$limited = " limit 10";
$sql = "select ".$fields." from ".DB_PREFIX.$db_name_bodys ." as a ".
	   "left join ".DB_PREFIX.$db_images ." as b on b.image_id = a.image_id ".
	   "where a.article_id in (".$articleids.")".$limited;

$rs = $apidb->query($sql);
//echo $sql;

$apidb->close();

if(empty($rs)) {
	$json->set_status("1");
	$json->set_msg("Query Error.");
	$json->create_json();	
}
else
{
	while($row = mysql_fetch_assoc($rs))
	{
		$row['article_id'] = urlencode($row['article_id']);
		$row['image_url'] = urlencode($row['image_url']);
		$arr[]=$row;
	}

	//print_r($arr);
	if(!empty($arr)) {
		$json->set_status("0");
		$json->set_msg("success");
		$json->set_content($arr);
		$json->create_json();
	}
	else
	{
		$json->set_status("2");
		$json->set_msg("No Records.");
		$json->create_json();
	}
}
