<?php 
/*  描述：内部使用API获取文章分类        */
/*  名称：getcat                         */
/*  作者：tiandi                         */
/*  版本：0.0.1                          */
/*  生成时间：2015.4.29                  */
/*  修订时间：2015.4.29                  */

if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("getcat");
	$help->set_ver("0.0.1");
	$help->set_time("2015.4.29");
	$help->set_des("获取文章分类");
	$help->set_indeed(array("articleids:文章ID，多个ID之间用英文逗号分隔，最多支持10个"));
	$help->set_success('[{"api_status":"0","api_msg":"success"},{"article_id":"60","node_id":"22","node_name":"橘乐部福利"},{"article_id":"62","node_id":"24","node_name":"达人使用心得"}]');
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

$db_name_index = "content_article_indexs";
$db_nodes = "content_article_nodes";

$fields = 'article_id,a.node_id,node_name';

$limited = " limit 10";
$sql = "select ".$fields." from ".DB_PREFIX.$db_name_index ." as a ".
	   "left join ".DB_PREFIX.$db_nodes ." as b on b.node_id = a.node_id ".
	   "where a.article_id in (".$articleids.")".$limited;

$rs = $apidb->query($sql);

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
		$row['node_id'] = urlencode($row['node_id']);
		$row['node_name'] = urlencode($row['node_name']);
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
