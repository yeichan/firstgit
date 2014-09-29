#!/usr/bin/php -q
<?php

	//include "./inc/Snoopy.class.php";
	//include "ContentAnal.php";
	//include "database.php";
	include "./database.php";
	
	print "aaaaaaaaaa";
	$con = DBConnect('edge_editor');
		if (!$con) {
		print "DBConnect Fail!!!!\n";
		exit();
	}
	

	print "aaaaaaaaaa";
	
	$RS = new CDatabase();
	$RS2 = new CDatabase();


	$sql = "select prompt from adjust_data where pj_id = 27";
	print "\nsql : $sql\n";
	if (!$RS->ExecSQL($con, $sql)) return false;

	$is_skip = false;
	while ($RS->Fetch())
	{
		$prompt = $RS->Row["prompt"];


		if ($prompt == "") {
			print "prompt is empty => skip\n";
			continue;
		}

		$prompt = trim($prompt);
		$prompt = str_replace("\"", "", $prompt);
		$prompt = str_replace("\\", "", $prompt);
		$prompt = preg_replace("#(\\\r\\\n|\\\r|\\\n)#", "", $prompt);

		$token = explode('&', $prompt);

		if (count($token) > 1) {
			//print "prompt : $prompt\n";
			print "token : " . $token[1] . "\n";
			$new_prompt = $token[1];
		} else {
			print "token : " . $token[0] . "\n";
			$new_prompt = $token[0];
		}

		$query = 'insert into temp_prompt set prompt = "' . $new_prompt . '"';

		//print $query . "\n";

		if (!$RS2->ExecSQL($con, $query)) {
				$RS->ErrorMsg();
				break;
		}
	}

	$RS2->Close();
	$RS->Close();
	
	DBClose($con);

?>