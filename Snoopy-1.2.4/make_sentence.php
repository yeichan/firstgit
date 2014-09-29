<?php
	//include "Snoopy.class.php";
	//include "ContentAnal.php";
	include "./inc/database.php";

	$con = DBConnect();
	if (!$con) {
		print "DBConnect Fail!!!!\n";
		exit();
	}

	$start = 100;
	$limit = 2000;
	while ($start < 28000) {
		$result = makeSentence($start, $limit, $con);
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


function makeSentence($start, $limit, $con) {
	$sentence_count = 0;
	$RS = new CDatabase();
	$RS2 = new CDatabase();

	$sql = "SELECT id, answer FROM edge_editor.bbs_refine_data where answer_md5 != ''order by id asc limit $start, $limit";
	print "\nsql : $sql\n";
	if (!$RS->ExecSQL($con, $sql)) return false;

	while ($RS->Fetch())
	{
		$id = $RS->Row["id"];
		$answer = $RS->Row["answer"];
		if ($answer == "") {
			print "[$id] answer is empty => skip\n";
			continue;
		}

		$sentences = explode(".", $answer);
		foreach($sentences as $sentence) {
			if (strlen(trim($sentence)) <= 5) {
				continue;
			}

			$sentence = trim($sentence) . ".";
			$sentence_md5 = md5($sentence);
			$words = explode(" ", $sentence);
			$word_count = count($words);

			$sql = "SELECT * FROM crawl_sentence WHERE sentence_md5 = '$sentence_md5'";
			if (!$RS2->ExecSQL($con,	$sql)) return false;
			if ($RS2->Fetch()) {
				print "[$id] Already Exists : " . $RS2->Row["sentence"] . "\n";
				continue;
			}

			$sentence = str_replace("'", "''",$sentence);

			$sql = "INSERT INTO crawl_sentence (
					sentence, sentence_md5, word_count, is_done
				) VALUES (
					'$sentence', '$sentence_md5', $word_count, 'N'
				)";

			if (!$RS2->ExecSQL($con, $sql)) {
				$RS2->ErrorMsg();
				return false;
			}

			$sentence_count++;

		}

	} // End of While

	print "sentence count : $sentence_count \n";

	return true;
}
?>