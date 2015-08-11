<?php 
/*  描述：内部使用API JSON类      */
/*  名称：json                    */
/*  作者：tiandi                  */
/*  版本：0.0.3                   */
/*  生成时间：2015.4.23           */
/*  修订时间：2015.7.2            */
/*  update:                       */
/*  0.0.2 返回数据加上HMTL转义    */
/*  0.0.3 返回错误数据修正content */


class json {
	/* status : string  : 状态码   */
	/* msg    : string  : 说明     */
	/* content: array   : 内容     */
	var $status;
	var $msg;
	var $content;

	function json(){
	}
	
	function set_status($status) {
		$this->status = $status;
	}

	function set_msg($msg) {
		$this->msg = $msg;
	}

	function set_content($content) {
		$this->content = $content;
	}

	function create_json() {
		$arr = array();
		$arr['api_status'] = $this->status;
		$arr['api_msg'] = $this->msg;
		if(!$this->content) {
			$this->content = array();
		}
		array_unshift($this->content,$arr);
		if($arr['api_status'] == '0') {			
			echo htmlspecialchars(str_replace('\/','/',urldecode(json_encode($this->content))));	
		}
		else
		{
			echo urldecode(json_encode($this->content));	
		}
	}

	function check_env($request){
		//check appid
		if(!isset($request['appid'])) {
			$this->set_status("99");
			$this->set_msg("Need appid.");
			echo $this->create_json();
			exit;
		}
		elseif(!$this->compare($request['appid'],MY_APPID)) {
			$this->set_status("98");
			$this->set_msg("Appid is invalid.");
			echo $this->create_json();
			exit;
		}
		//check timestamp
		elseif(!isset($request['timestamp'])) {
			$this->set_status("97");
			$this->set_msg("Need timestamp.");
			echo $this->create_json();
			exit;
		}
		//check sign
		elseif(!isset($request['sign'])) {
			$this->set_status("96");
			$this->set_msg("Need sign.");
			echo $this->create_json();
			exit;
		}
		elseif(!$this->compare($request['sign'],$this->create_sign($request))) {
			$this->set_status("95");
			//$this->set_msg("Sign[".$request['sign']."]is invalid.");
			$this->set_msg("Sign is invalid.");
			echo $this->create_json();
			exit;
		}

	}

	function compare($str1,$str2) {
		if($str1 == "'".$str2."'" || $str1 == $str2 || "'".$str1."'" == $str2)
			return true;
		else
			return false;
	}

	/**************************  生成签名  ***************************/
	/*  签名规则为:                                                  */
	/*  1.首先将参数中非数字的值添加单引号                           */
	/*  2.将所有参数按字典升序排序生成数组                           */
	/*  3.去除数组中的空值和签名项                                   */
	/*  4.将数组转成字符串                                           */
	/*  5.在字符串尾部拼接密钥，&code=密钥                           */
	/*  6.按MD5 32位加密字符串后大写                                 */
	/*****************************************************************/

	function create_sign($request) {
		ksort($request);
		$tempsign = "";
		foreach ($request as $k => $v){
			if (null != $v && "null" != $v && "sign" != $k) {
				$tempsign .= $k . "=" . $v . "&";
			}
		}
		$tempsign = substr($tempsign, 0, strlen($tempsign)-1)."&code=".MY_CODE;
		return strtoupper(md5($tempsign));
	}
}