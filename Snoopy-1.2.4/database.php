<?php
	define("AUTOINCKEY", "''");
	define("DB_READ", 0);
	define("DB_WRITE", 1);
	//define("DBERRSTMT", "<meta http-equiv='Refresh' content='0; URL=$_HOME_URL/setc/error.php?err=11'>");

	$_ISMASTER	= false;

	function DBConnect($database="crawl_bbs", $access=DB_READ)
	{
		global $_ISMASTER;

		$DB_HOST	= "localhost";
		$DB_HOST_W	= "localhost";
		$DB_USER	= "root";
		$DB_PASSWD	= "root";
		if ($database == "") $DB_DB = "crawl_bbs";
		else $DB_DB = $database;

		if ($access == DB_WRITE) {
			$DB_HOST = $DB_HOST_W;
			$_ISMASTER = true;
		}
		else $_ISMASTER = false;

		$DB_CON = mysql_connect($DB_HOST, $DB_USER, $DB_PASSWD);
		$ret = mysql_select_db($DB_DB, $DB_CON);
		if (!$ret) {
			return false;
		}

		return $DB_CON;
	}

	function DBClose(& $DB_CON)
	{
		return @mysql_close($DB_CON);
	}


class CDatabase
{
	var	$DB_CON;
	var	$DB_STMT;
	var	$DB_QUERY = "";
	var	$ISCON = FALSE;

	var	$Row;
	var	$FetchCount = 0;

	function ExecSQL(& $con, $query, $limitflag=false)
	{
		global $_ISMASTER, $_HOME_URL;

		if (!$con) return FALSE;

		$this->ISCON	= TRUE;
		$this->DB_CON	= $con;

		$query1 = strtoupper(substr(trim($query), 0, 6));
		$query2 = substr(trim($query), 6);

		if ($query1 == "SELECT") {
			if ($limitflag == true) {

				$this->DB_QUERY	= $query1." SQL_CALC_FOUND_ROWS ".$query2;
				$this->DB_STMT	= @mysql_query($this->DB_QUERY);
				$arr = @mysql_fetch_row(@mysql_query("SELECT FOUND_ROWS();"));
				$this->FetchCount = $arr[0];
/*
				$this->DB_QUERY	= $query;
				$this->DB_STMT	= @mysql_query($this->DB_QUERY);
				$spos = strpos($query2, "FROM");
				$epos = strpos($query2, "LIMIT");
				$query	= $query1." COUNT(1) AS cnt ".substr($query2, $spos, $epos - $spos);
				$row	= @mysql_fetch_row(@mysql_query($query));
				$this->FetchCount = $row[0];
*/
			}
			else {
				$this->DB_QUERY	= $query;
				$this->DB_STMT	= @mysql_query($this->DB_QUERY);
				$this->FetchCount = @mysql_num_rows($this->DB_STMT);
			}
		}
		else {

			//if (!$_ISMASTER) header("Location: $_HOME_URL/setc/error.php?err=11&msg=".urlencode($query."<br>".$_SERVER['HTTP_REFERER']));
			$this->DB_QUERY	= $query;
			$this->DB_STMT	= @mysql_query($this->DB_QUERY);
			$this->FetchCount = @mysql_affected_rows();
		}

		return $this->DB_STMT;
	}

	function GoNext($nextidx)
	{
		return @mysql_data_seek($this->DB_STMT, $nextidx);
	}

	function GetID()
	{
		return @mysql_insert_id();
	}

	function Fetch()
	{
		$this->Row = @mysql_fetch_array($this->DB_STMT);
		return $this->Row;
	}

	function ErrorMsg()
	{
		if (mysql_error())
			echo $this->DB_QUERY.":".mysql_error()."<BR>";
	}

	function Close()
	{
		$imsi = strtoupper(substr(trim($this->DB_QUERY), 0, 6));
		if ($imsi == "SELECT") {
			mysql_free_result($this->DB_STMT);
		}
		return TRUE;
	}
}

?>