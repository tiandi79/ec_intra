<?php 
/*  描述：内部使用API数据库连接   */
/*  名称：conn                  */
/*  作者：tiandi                  */
/*  版本：0.0.1                   */
/*  生成时间：2015.4.22           */
/*  修订时间：2015.4.22           */

class conn {

	var $con;
	function conn($db_host,$db_user,$db_pwd,$db_name) {
		$con = mysql_connect($db_host, $db_user, $db_pwd);
		if (!$con) {    
			die('Could not connect: ' . mysql_error());
		}
		else {  
			$this->con = $con;
			mysql_select_db($db_name,$con);
			//echo "Database [".$db_name."] connected!<br>";
		}
	}

	function close() {
		mysql_close($this->con);
	}

	function query($sql) {
		mysql_query("SET NAMES 'UTF8'"); 
		$query = mysql_query($sql, $this->con);		
		if($query) {
			return $query;
		}
		else
		{
			die(mysql_error());
		}
	}

	function getone($sql) {
		mysql_query("SET NAMES 'UTF8'"); 
		$sql .= " limit 1";

		$res = $this->query($sql);
	
        if ($res !== false)
        {   
            $row = mysql_fetch_row($res);
            if ($row !== false)
            { 
                return $row[0];
            }
            else
            {
                return '';
            }
        }
        else
        {
            return false;
        }
	}

}