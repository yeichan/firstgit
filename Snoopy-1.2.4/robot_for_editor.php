#!/usr/bin/php -q
<?php

	include "./inc/Snoopy.class.php";
	//include "ContentAnal.php";
	//include "database.php";
	include "./inc/database.php";

	echo date("Y-m-d H:i:s") . "\n";

	if ($argc == 2 && $argv[1] == "debug") {
		$debug = true;
	} else {
		$debug = false;
	}


	$buff_rate = array();

	$rate = Array(0=>95, 1=>5);
	foreach ($rate as $key=>$value) {
	        $buff_rate = array_merge($buff_rate, array_fill(0, $value, $key));
	}

	if (checkRunnable() == 0) {
		echo "Please wait for the next turn\n";
		exit();
	}

	$con = DBConnect();
	if (!$con) {
		print "DBConnect Fail!!!!\n";
		exit();
	}

	$RS = new CDatabase();

	$sql = "SELECT id, sentence, word_count FROM crawling.crawl_sentence where word_count < 11 and is_done = 'N' order by id asc limit 1";
	print "\nsql : $sql\n";
	if (!$RS->ExecSQL($con, $sql)) return false;

	$is_skip = false;
	while ($RS->Fetch())
	{
		$id = $RS->Row["id"];
		$sentence = $RS->Row["sentence"];
		$word_count = $RS->Row["word_count"];

		if ($sentence == "") {
			print "[$id] sentence is empty => skip\n";
			$is_skip = true;
			continue;
		}

		$sentence = preg_replace("#(\\\r\\\n|\\\r|\\\n)#"," ",$sentence);

		if (!eregi('^[a-zA-Z0-9\.\,\?\!\'\";:% -]+$', $sentence)) {
		        echo "[$id] $sentence\n";
		        echo "invalid character\n";
		        $is_skip = true;
		} else if ( preg_match_all('/[a-zA-Z]/', $sentence, $m) < 7 ) {
		        echo "[$id] $sentence\n";
		        echo "too short alphabet\n";
		        $is_skip = true;
		}
	}

	if ($is_skip) {
		$sql = "UPDATE crawling.crawl_sentence SET is_done = 'F' where id = $id";
		print "\nsql : $sql\n";
		if (!$RS->ExecSQL($con, $sql)) {
			$RS->ErrorMsg();
		}
	}
	else if ($sentence != "") {
		print "\n[$id] Sentence : $sentence\n";
		$return = doRobot($sentence);

		if ($return) {
			$sql = "UPDATE crawling.crawl_sentence SET is_done = 'Y' where id = $id";
			print "\nsql : $sql\n";
			if (!$RS->ExecSQL($con, $sql)) {
				$RS->ErrorMsg();
			}
		}
	}

	$RS->Close();
	DBClose($con);


function checkRunnable() {
        global $buff_rate;
        shuffle($buff_rate);
        return end($buff_rate);

}

function doRobot($sentence) {
	global $debug;

	$snoopy = new Snoopy;

	$editor_url = "http://museeditor.com/";

	if (!$snoopy->fetch($editor_url)) {
		echo "fetch fail : $editor_url\n";
		return false;
	}

	if (!strstr($snoopy->response_code, "HTTP/1.1 200 OK")) {
		echo "==> http error\n";
		return false;
	}

	if ($debug == true) 	print_r ($snoopy->headers);

	$snoopy->setcookies();

	if ($debug == true) 	print  ( "cookies['csrftoken'] : " . $snoopy->cookies['csrftoken']);

	$signin_url = $editor_url . "rest/sign_in/";
	$submit_vars["csrfmiddlewaretoken"] = $snoopy->cookies['csrftoken'];
	$submit_vars["username"] = "eric";
	$submit_vars["password"] = "1111";

	if ($debug == true)  print ("signin_url : " . $signin_url . "\n");
	if ($debug == true)  print_r ($submit_vars);

	$snoopy->submit($signin_url,$submit_vars);

	if ($debug == true)  print_r ($snoopy->headers);

	if (!strstr($snoopy->response_code, "HTTP/1.1 200 OK")) {
		echo "==> http error\n";
		return false;
	}


	$snoopy->setcookies();

	if ($debug == true)  print_r ($snoopy->cookies);

	//print $snoopy->results;

	$muse_url = $editor_url . "rest/muse/";
	$submit2_vars["csrfmiddlewaretoken"] = $snoopy->cookies['csrftoken'];
	$submit2_vars["category"] = 'Test Prep';
	$submit2_vars["text"] = $sentence;
	$submit2_vars["index"] = "0";

	if ($debug == true)  print ("muse_url : " . $muse_url . "\n");
	if ($debug == true)  print_r ($submit2_vars);

	$snoopy->submit($muse_url,$submit2_vars);

	if ($debug == true)  print_r ($snoopy->headers);

	if (!strstr($snoopy->response_code, "HTTP/1.1 200 OK")) {
		echo "==> http error\n";
		return false;
	}

	if ($debug == true)  print $snoopy->results;

	return true;
}

?>