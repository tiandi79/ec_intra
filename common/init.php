<?php 
/*  描述：内部使用API初始化文件   */
/*  名称：init                    */
/*  作者：tiandi                  */
/*  版本：0.0.2                   */
/*  生成时间：2015.4.22           */
/*  修订时间：2015.4.28           */
/*  update:                       */
/*    0.0.2: 文件位置调整         */

define('ROOT_PATH', str_replace('intra_api/common/init.php', '', str_replace('\\', '/', __FILE__)));
define('MY_APPID', ''); //必要参数，用户唯一标示
define('MY_CODE', '');  //密钥

require(ROOT_PATH . 'config/config.php');
require(ROOT_PATH . 'intra_api/models/conn.php');
require(ROOT_PATH . 'intra_api/models/json.php');

$apidb = new conn(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

$request = "";
if(isset($_REQUEST)) {
	foreach ($_REQUEST as $key=>$value) {
		$request[$key] = check_input($value);
	}
}

//print_r($request);

$json = new json();
$json->check_env($request);

function check_input($value)
{
	if (get_magic_quotes_gpc())
	{
		$value = stripslashes($value);
	}
	if (!is_numeric($value))
	{
		$value = "'" . mysql_real_escape_string($value) . "'";
	}
	return $value;
}
