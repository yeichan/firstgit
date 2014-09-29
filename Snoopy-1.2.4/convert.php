<?php
	//include "Snoopy.class.php";
	//include "ContentAnal.php";
	include "database.php";

	$con = DBConnect();
	if (!$con) {
		print "DBConnect Fail!!!!\n";
		exit();
	}

	$start = 0;
	$limit = 2000;
	while ($start < 47000) {
		$result = refineBbsContent($start, $limit, $con);
		$start += $limit;
		if ($result) {
			print "Success!!!\n";
		} else {
			print "Fail!!!\n";
			break;
		}
	}
	


	if ($con) {
		DBClose($con);
	}


function refineBbsContent($start, $limit, $con) {
	$patterns = array("/[^\x20-\x7e\n]/", "/\(.*\)/iU", "/\&nbsp\;/", "/\&rdquo\;/", "/\&ldquo\;/", "/\&rsquo\;/" );
	$replace =  array("", "", " ", "\"", "\"", "'" );
	//$patterns = array("/[^\x20-\x7e]/");
	//$replace =  array("");

	$strike_count = 0;
	$RS = new CDatabase();
	$RS2 = new CDatabase();

	$sql = "SELECT * FROM bbs_data order by id asc limit $start, $limit";
	print "\nsql : $sql\n";
	if (!$RS->ExecSQL($con,	$sql)) return false;
	while ($RS->Fetch())
	{
		//print "[" . $RS->Row['id'] . "] ";
		$url = $RS->Row["url"];
		$temp = strpos($url, "&no=");
		$bbs_id = substr($url, $temp + 4, 5);
		//print "bbs_id [$bbs_id]\n";

		$body = $RS->Row["body"];
		$topic = "";
		$answer = "";
		$answer_md5 = "";

		if ($body == "") {
			print "body is empty => skip\n";
			continue;
		}

		$flag = 1;

		// get topic
		preg_match_all('#&\#9654; Topic :(.*?)</td>#is', $body, $topic_matches);
		if (count($topic_matches[0]) == 0)
		{
			preg_match_all('#Topic :(.*?)</td>#is', $body, $topic_matches);
		}
		if (count($topic_matches[0]) > 0)
		{
			$topic = $topic_matches[1][0];
		} else {
			preg_match_all('#<IMG src="skin/technote/images/ttl.gif">(.*?)</TR>(.*?)</td>#is', $body, $topic_matches);
			if (count($topic_matches[0]) > 0) {
				$topic = $topic_matches[1][0];
			
			}
		}

		if (trim($topic) != "") {
			$topic = br2nl(strip_tags($topic, '<p><br>'));
			$topic = p2nl($topic);
			//$topic = trim(str_replace("&nbsp;", " ",$topic));
			// ASCII 범주 이외의 모든 문자 제거
			//$topic = preg_replace("/[^\x20-\x7e]/", "", $topic);
			$topic = preg_replace($patterns, $replace, $topic);
			$topic = trim($topic);
			$flag++;
		}

		//print "\n[" . $topic . "]\n";

		// get answer
		preg_match_all('#<td .*idwordcounttd(.*?)>(.*?)</td>#is', $body, $ans_matches);
		//print_r ($ans_matches);
		if (count($ans_matches[0]) == 0)
		{
			preg_match_all('#<td id=idwordcounttd (.*?)>(.*?)</td>#is', $body, $ans_matches);
		}

		if (count($ans_matches[0]) > 0)
		{
			$answer = $ans_matches[2][0];
			$str_pos = stripos($answer, "Your Answer :");
			if ($str_pos !== false)
			{
				$answer = substr($ans_matches[2][0], $str_pos + 13);
			}

			preg_match_all('#<strike(.*?)>(.*?)</strike>#is', $answer, $strike_matches);
			if (count($strike_matches[0]) > 0)
			{
				print "[" . $bbs_id . "] ";
				$strike_count++;
				continue;
				//print_r ($strike_matches);
			}

			$answer = br2nl(strip_tags($answer, '<p><br>'));
			$answer = p2nl($answer);
			if (trim($answer) != "") {
				//$answer = trim(str_replace("&nbsp;", " ",$answer));
				// ASCII 범주 이외의 모든 문자 제거
				//$answer = preg_replace("/[^\x00-\x7e]/", "", $answer);
				$answer = preg_replace($patterns, $replace, $answer);
				$answer_md5 = md5($answer);
				$flag++;
			}
		}

		// insert converted data
		if ($answer_md5 != "") {
			$sql = "SELECT * FROM bbs_refine_data WHERE answer_md5 = '$answer_md5'";
			if (!$RS2->ExecSQL($con,	$sql)) return false;
			if ($RS2->Fetch()) {
				print "Already Exists : " . $RS2->Row["answer_md5"] . "\n";
				continue;
			}
		}
		else {
			$sql = "SELECT * FROM bbs_refine_data WHERE url = '$url'";
			if (!$RS2->ExecSQL($con,	$sql)) return false;
			if ($RS2->Fetch()) {
				print "Already Exists : " . $RS2->Row["url"] . "\n";
				continue;
			}
		}

		$body = str_replace("'", "''",$body);
		$topic = str_replace("'", "''",$topic);
		$answer = str_replace("'", "''",$answer);

		$sql = "INSERT INTO bbs_refine_data (
					url, bbs_id, body, topic, answer, answer_md5, flag
				) VALUES (
					'$url', $bbs_id, '$body', '$topic', '$answer',
					'$answer_md5', $flag
				)";

		//print $sql;

		if (!$RS2->ExecSQL($con, $sql)) {
			$RS2->ErrorMsg();
			return false;
		}
	} // End of While

	print "strike count : $strike_count \n";

	return true;
}

function br2nl($string) 
{ 
    //return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string); 
    return preg_replace(array("/\<br(\s*)?\/?\>/i","/\<\/br(\s*)?\/?\>/iU"), 
                        array("\n","\n"), 
                        $string); 
} 

function p2nl ($str) { 
    return preg_replace(array("/<p[^>]*>/iU","/<\/p[^>]*>/iU"), 
                        array("","\n"), 
                        $str); 
}



?>