<?php
/*  描述：内部使用API HELP类             */
/*  名称：help                           */
/*  作者：tiandi                         */
/*  版本：0.0.4                          */
/*  生成时间：2015.4.24                  */
/*  修订时间：2015.4.28                  */
/*  update:                              */
/*    0.0.2: 增加显示正确或者错误时的    */
/*           返回信息                    */
/*    0.0.3: 修正必要参数样式            */
/*    0.0.4: 增加frame样式               */


define('CSSURL','../css/style.css');
define('ROOT_PATH', str_replace('intra_api/classes/help.php', '', str_replace('\\', '/', __FILE__)));

class help {
	var $name;
	var $ver;
	var $modtime;
	var $des;
	var $api;
	var $indeed;
	var $noindeed;
	var $success;
	var $fail;

	function help($name) {
		$this->name = $name;
		$this->set_api();
	}

	function set_ver($ver) {
		$this->ver = $ver; 
	}

	function set_time($mod_time) {
		$this->modtime = $mod_time;
	}

	function set_des($des) {
		$this->des = $des;
	}

	function set_api() {
		$this->api = "http://".$_SERVER['HTTP_HOST']."/intra_api/".$this->name."/";
	}

	function set_indeed($indeed) {
		$this->indeed = $indeed;
	}

	function set_noindeed($noindeed) {
		$this->noindeed = $noindeed;
	}

	function set_success($success) {
		$this->success = $success;
	}

	function set_fail($fail) {
		$this->fail = $fail;
	}

	function show() {
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
		echo "<link rel='stylesheet' type='text/css' href=".CSSURL." />";
		echo "<div id='wrap'>";
		echo "<div id='title'>名称: ".$this->name."</div>";
		echo "<div class='info'>描述:  ".$this->des."</div>";
		echo "<div class='info'>版本:  ".$this->ver."</div>";
		echo "<div class='info'>修订时间:  ".$this->modtime."</div>";
		echo "<div id='api'>API： ".$this->api."</div>";
		echo "<div class='smalltitle'>必要参数：<div>";
		echo "<div class='smallcontent'>appid:应用唯一标识</div>";
		echo "<div class='smallcontent'>timestamp:时间戳</div>";
		echo "<div class='smallcontent'>sign:签名 <a href = 'http://".$_SERVER['HTTP_HOST']."/intra_api/docs/help_sign.php'>《如何生成签名？》</a></div>";
		for($i=0;$i<count($this->indeed);$i++) {
			echo "<div class='smallcontent'>".$this->indeed[$i]."</div>";
		}
		echo "<div class='smalltitle'>可选参数: </div>";
		for($i=0;$i<count($this->noindeed);$i++) {
			echo "<div class='smallcontent'>".$this->noindeed[$i]."</div>";
		}
		echo "<div class='smalltitle'>成功返回: </div>";
		echo "<div class='smallcontent'>".$this->success."</div>";
		echo "<div class='smalltitle'>错误返回: </div>";
		echo "<div class='smallcontent'>".$this->fail."</div>";
		echo "</div>";
	}
}