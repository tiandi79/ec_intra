<html>
<!--
/*  描述：内部使用API 签名机制说明文档   */
/*  名称：help_sign                      */
/*  作者：tiandi                         */
/*  版本：0.0.1                          */
/*  生成时间：2015.4.24                  */
/*  修订时间：2015.4.24                  */ 
-->

<link rel='stylesheet' type='text/css' href='../css/style.css' />
<div id='wrap'>
<div id='title'>名称:  如何生成签名</div>
<div class='info'>版本:  0.01</div>
<div class='info'>修订时间:  2015.4.24</div>
<div class='info'>
<br>
  签名规则为:                                                  <br>
  1.首先将参数中非数字的值添加单引号                           <br>
  2.将所有参数按字典升序排序生成数组                           <br>
  3.去除数组中的空值和签名项                                   <br>
  4.将数组转成字符串                                           <br>
  5.在字符串尾部拼接密钥，&code=密钥                           <br>
  6.按MD5 32位加密字符串后大写                                 <br>
<br>
例如：提交的参数为appid=suanjuzi&amp;timestamp=123456&nodeids=22<br>
1.添加单引号后appid='suanjuzi'&amp;timestamp=123456&nodeids=22<br>
2.字典排序后<br>
  array(
	'appid' =>'suanjuzi',
	'nodeids' =>22,
	'timestamp' =>123456
  )<br>
3.将数组转成字符串并拼接密钥后appid='suanjuzi'&nodeids=22&amp;timestamp=123456&code=ABCDEFGHIJK<br>
4.MD5 32位加密后得出最终签名3A8EA19A9BE810F85D20464297D80753
</div>
<br>
<div class='info'><a href ='../tools/check_sign.php' >《签名验证器》</a></div>
</div>
</html>