<?php 
/*  描述：内部使用API最新十篇文章        */
/*  名称：timeline                       */
/*  作者：tiandi                         */
/*  版本：0.0.6                          */
/*  生成时间：2015.4.23                  */
/*  修订时间：2015.7.1                   */
/*  update:                              */
/*    0.0.2: 增加article_id字段显示      */
/*    0.0.3: 增加文章摘要显示            */
/*    0.0.4: 增加缩略图地址              */
/*    0.0.5: 增加article_type            */
/*    0.0.6: 增加分类目录node_name,点赞数basic_isgood  */

if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("timeline");
	$help->set_ver("0.0.6");
	$help->set_time("2015.7.1");
	$help->set_des("显示最新十篇文章");
	$help->set_noindeed(array("nodeids:文章节点ID，多个节点用英文逗号分隔"));
	$help->set_success('[{"api_status":"0","api_msg":"success"},{"article_id":"61","article_type":1,"title":"你有食瘾吗？——论如何克服对不健康食物的依赖","author":"十万个橘子","basic_isgood":"50","node_name":"橘乐部福利","pubtime":"1429729200","summary":" #酸橘子每周爆款，澳洲新品霸道包邮促# ★本周又有几款澳洲新品上架，现已加入澳洲免邮专场★ 橘子看了看Blackmores和Swisse 有4款产品性价比超高又实用，要推荐给橘友们， 老","image_url":"public/images/02/f0/b8/d3b985232d59df975dd86d3b4d16b20f5a231ebb.png"}]');
	$help->set_fail('{"api_status":"99","api_msg":"Need Appid."}');
	$help->show();
	exit;
}


require('../common/init.php');

$nodeids = isset($request["nodeids"])? $request["nodeids"]:'';
$nodeids = str_replace(",","','",$nodeids);

$db_name_indexs = "content_article_indexs";
$db_name_bodys = "content_article_bodys";
$db_name_nodes = "content_article_nodes";
$db_images = "image_image";

$fields = "a.article_id,a.title,a.author,d.node_name,a.pubtime,b.content as summary,c.url as image_url,a.basic_isgood ";
$limited = " order by pubtime DESC limit 10";
if($nodeids != '')
	$sql = "select ".$fields." from ".DB_PREFIX.$db_name_indexs ." as a ".
		   "left join ".DB_PREFIX.$db_name_bodys ." as b on b.article_id = a.article_id ".
		   "left join ".DB_PREFIX.$db_images ." as c on c.image_id = b.image_id ".
		   "left join ".DB_PREFIX.$db_name_nodes ." as d on d.node_id = a.node_id ".
		   "where platform = 'wap' and a.ifpub='true' and a.node_id in (".$nodeids.")".$limited;
else
	$sql = "select ".$fields." from ".DB_PREFIX.$db_name_indexs ." as a ".
		   "left join ".DB_PREFIX.$db_name_bodys ." as b on b.article_id = a.article_id ".
		   "left join ".DB_PREFIX.$db_images ." as c on c.image_id = b.image_id ".
		   "left join ".DB_PREFIX.$db_name_nodes ." as d on d.node_id = a.node_id ".
		   "where platform = 'wap' and a.ifpub='true'".$limited;

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
		$row['title'] = urlencode($row['title']);
		$row['author'] = urlencode($row['author']);
		$row['node_name'] = urlencode($row['node_name']);
		if(stripos($row['summary'],'[buyitem=') > -1)  
			$row['article_type'] = 1;
		else
			$row['article_type'] = 0;
		$row['summary'] = urlencode(mb_substr(strip_tags($row['summary']),0,100,'utf-8'));
		$row['image_url'] = stripslashes($row['image_url']);
		$arr[]=$row;
	}

	for($i=0;$i<count($arr);$i++) {
		ksort($arr[$i]);
	}

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

