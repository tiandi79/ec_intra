<?php 
/*  描述：内部使用API获取文章内容        */
/*  名称：getarticle                     */
/*  作者：tiandi                         */
/*  版本：0.0.5                          */
/*  生成时间：2015.4.30                  */
/*  修订时间：2015.7.3                   */
/*  update:                              */
/*  0.0.2 返回数据加上goods_id           */
/*  0.0.3 文章内容双引号替换为单引号     */
/*  0.0.4 增加node_name,basic_isgood,btn_name     */
/*  0.0.5 增加title,author,pubtime       */

if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("getarticle");
	$help->set_ver("0.0.5");
	$help->set_time("2015.7.3");
	$help->set_des("获取文章内容");
	$help->set_indeed(array("articleids:文章ID，多个ID之间用英文逗号分隔，最多支持10个"));
	$help->set_success('[{"api_status":"0","api_msg":"success"},{"article_id":"60","article_type":1,"basic_isgood":"433","node_name":"橘乐部福利","content":[{"s_content":"这里是很长的文章内容...","goods_id":"1923","btn_name":"试用申请"}]}]');
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

$db_name_indexs = "content_article_indexs";
$db_name_bodys = "content_article_bodys";
$db_name_nodes = "content_article_nodes";

$fields = 'a.article_id,a.title,a.author,a.pubtime,b.content,a.basic_isgood,c.node_name ';

$limited = " limit 10";
$sql = "select ".$fields." from ".DB_PREFIX.$db_name_indexs ." as a ".
	   "left join ".DB_PREFIX.$db_name_bodys ." as b on b.article_id = a.article_id ".
	   "left join ".DB_PREFIX.$db_name_nodes ." as c on c.node_id = a.node_id ".
	   " where b.article_id in (".$articleids.")".$limited;

$rs = $apidb->query($sql);

$apidb->close();


if(empty($rs)) {
	$json->set_status("1");
	$json->set_msg("Query Error.");
	$json->create_json();	
}
else
{
	$arr = array();
	while($row = mysql_fetch_assoc($rs))
	{
		$content = $row['content'];
		$row['pubtime'] = date("Y-m-d", $row['pubtime']);
		$row['node_name'] = urlencode($row['node_name']);
		if(stripos($row['content'],'[buyitem=') > -1)  {
			$row['article_type'] = 1;
			$content = $row['content'];
			$content_array = get_array($content);
			$row['content'] = $content_array;
		}
		else {
			$row['article_type'] = 0;
			$row['content'] = urlencode(str_replace("\"","'",$row['content']));
		}
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
function get_array($content) {
	$btn_name = null;
	$ret = array();
	$i = stripos($content,'[buyitem=');
	while ( $i > -1) {
		$text = urlencode(str_replace("\"","'",substr($content,0,$i))); 
		$content = substr($content,$i+9); 
		$tmp = substr($content,0,stripos($content,']')+1);
		$j = stripos($tmp,' btn=');
		if($j > -1) { 
			$goods_id = substr($tmp,0,$j);
			$content = substr($content,$j+5); 
			$j = stripos($content,']');
			$btn_name = substr($content,0,$j);
		}	
		else {
			$j = stripos($content,']');
			$goods_id = substr($content,0,$j);
			$btn_name = "立即抢购";
		}
		$btn_name = urlencode($btn_name);
		$single_topic = array("s_content"=>$text,"goods_id"=>$goods_id,"btn_name"=>$btn_name);
		$ret[] = $single_topic;
		$content = substr($content,$j+1);
		$i = stripos($content,'[buyitem=');
	}
	return $ret;
}
