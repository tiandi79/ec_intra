<?php 
/*  描述：内部使用API获取文章评论        */
/*  名称：getarticlecomment              */
/*  作者：tiandi                         */
/*  版本：0.0.3                          */
/*  生成时间：2015.7.6                   */
/*  修订时间：2015.8.14                  */
/*  update:                              */
/*  0.0.3 修复没有过滤文章ID的问题       */


if (isset($_REQUEST['help'])) {
	require('../models/help.php');
	$help = new help("getarticlecomment");
	$help->set_ver("0.0.3");
	$help->set_time("2015.8.14");
	$help->set_des("获取文章评论");
	$help->set_indeed(array("article_id:文章ID"));
	$help->set_noindeed(array("page:页码"));
	$help->set_success('[{"api_status":"0","api_msg":"success"},{"parent_id":"0","author":"3224234_qq***","content":"cccc","is_useful":null,"pubtime":"1435915305","avatar":"public/images/6f/6a/95/3e3ba97910e99f946daba0a87b2163674dee9de1.jpg"},{"parent_id":"0","author":"138****6622","content":"bbbb","is_useful":null,"pubtime":"1435915185","avatar":null},{"parent_id":"0","author":"bbextra.tiandi@***","content":"aaaa","is_useful":null,"pubtime":"1435914989","avatar":null}]');
	$help->set_fail('{"api_status":"99","api_msg":"Need Appid."}');
	$help->show();
	exit;
}


require('../common/init.php');

$articleid = isset($request["article_id"])? $request["article_id"]:'';
$page = isset($request["page"])? $request["page"]:1;

if($articleid == '') {
	$json->set_status("12");
	$json->set_msg("No article_id.");
	$json->create_json();
	exit;
}

$db_name_comments = "content_article_comments";
$db_name_member = "b2c_members";
$db_name_image = "image_image";

$limited = " limit ".(string)(10*($page-1)).",10";


//正式用
$sql = "select parent_id,author,content,is_useful,pubtime,c.url as avatar from ".DB_PREFIX.$db_name_comments ." as a".
	   " left join ".DB_PREFIX.$db_name_member ." as b on b.member_id = a.author_id ".
	   " left join ".DB_PREFIX.$db_name_image ." as c on c.image_id = b.avatar ".
	   " where display = 'true' and parent_id = 0 and article_id = ".$articleid." order by pubtime desc".$limited;
//

/*测试用
$sql = "select parent_id,author,content,is_useful,pubtime,c.url as avatar from ".DB_PREFIX.$db_name_comments ." as a".
	   " left join ".DB_PREFIX.$db_name_member ." as b on b.member_id = a.author_id ".
	   " left join ".DB_PREFIX.$db_name_image ." as c on c.image_id = b.avatar ".
	   " where parent_id = 0 order by pubtime desc".$limited;
*/
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
		$row['content'] = urlencode($row['content']);
		$row['author'] = nametonick($row['author']);
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
//by tiandi 处理评论者描述
function nametonick($author) {	
	if($j = strpos($author,'@'))  {
		$author = substr($author,0,$j+1) . "***";
	}
	elseif(preg_match("/^1[34578]{1}[0-9]{9}$/",$author)) {
		$author = substr($author,0,3) ."****". substr($author,7);
	}
	elseif($j = strpos($author,'_qq'))  {
		$author = substr($author,0,$j+3) . "***";
	}
	elseif($j = strpos($author,'_weibo'))  {
		$author = substr($author ,0,$j+6) . "***";
	}
	return $author;
}