<?php

	/**
	$content = '  <div class="itemBtnSet" style="border:0px solid #00000;">
    	<div class="fl_ri bbsLinkSet">
    	    
    		<a href="zboard.php?id=essay&page=1&page_num=20&category=&sn=off&ss=on&sc=on&keyword=&prev_no=83779&sn1=&divpage=15&select_arrange=headnum,arrangenum&desc=&domain=&post_message=" class="goList">목록</a> 
    		        	<span class="bar">|</span>
        	
			<a href="zboard.php?id=essay&page=1&sp1=&sn1=&divpage=15&sp=off&sn=off&ss=on&sc=on&sf=off&sa=off&select_arrange=headnum&no=83780" class="goPrev">윗글</a>
        	        	        	<span class="bar">|</span>
        	
			<a href="zboard.php?id=essay&page=1&sp1=&sn1=&divpage=15&sp=off&sn=off&ss=on&sc=on&sf=off&sa=off&select_arrange=headnum&no=83777" class="goNext">아랫글</a>
        	    	</div>';
        	   **/

        	//$content = file_get_contents ('content.html');
        	    	//$content = file_get_contents ('content.html');


	//preg_match_all('|<a href=".*" class="goNext">아랫글</a>|', $content, $matches, PREG_SET_ORDER);
        	/**
        	preg_match_all("/<a[^>]* href=(['|\"]*)([^\\1\040>]*)\\1[^>]*class=\"goNext\">/is", $content, $matches);

	print "match_count : " . count($matches) . "\n";
	print_r ($matches);
	print "next_link : " . $matches[2][0];
	**/

	/**

	$start_pos = strpos($content, "<!--게시글 내용 출력시작-->");
	$end_pos = strpos($content, "<!--/ 게시글 내용 출력끝-->");

	if ($start_pos > 0 && $end_pos > 0)
	{
		print "start : $start_pos, end : $end_pos \n";
		$body_content = substr($content, $start_pos, $end_pos - $start_pos);
		print $body_content;
	}

	//topic
	//<strong>&#9654; Topic :</strong>
	//&#9654; Topic
	preg_match_all('#&\#9654; Topic(.*?)</td>#is', $content, $topic_matches);
	print "topic =====\n";
	print_r ($topic_matches);



	//<TD id=idwordcounttd
	preg_match_all('#<td id=idwordcounttd (.*?)>(.*?)</td>#is', $content, $ans_matches);
	print_r ($ans_matches);
	**/

/***
$text = '<p style="MARGIN: 0cm 0cm 0pt">Test paragraph.</p><br>ssdsfsf </br><!-- Comment --> <a href="#fragment">Other text</a>';
echo strip_tags($text);
echo "\n";

// Allow <p> and <a>
echo strip_tags($text, '<p><br>');
exit();
***/

	
/***
	$str = '<table style="BORDER-RIGHT: windowtext 1px solid; BORDER-TOP: windowtext 1px solid; BORDER-LEFT: windowtext 1px solid; WIDTH: 450pt; BORDER-BOTTOM: windowtext 1px solid" height="300" cellspacing="0" cellpadding="0" width="450">
<tbody>
<tr>
<td id="idwordcounttd" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; VERTICAL-ALIGN: top; PADDING-TOP: 5px">&#9654; Your Answer :<br></td><td id="idwordcounttd" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; VERTICAL-ALIGN: top; PADDING-TOP: 5px"><br><br><br>&nbsp;In this set of materials, both the reading passage and the lecture deal with the pros and cons of green roofs. Regarding this topic, the lecturer argues that green roofs are sometimes good, but mostly exaggerate. This contradicts the writer\'s assertion that green roofs have many advantages.<br>&nbsp;First, according to the lecturer, there is not enough spaces to build green roofs and shortage city park. So, it\'s impossible too shortage city park to build green roof. This counters the writer\'s viewpoint that, large buildings can be used &nbsp;green roof so they can create large square.<br>&nbsp;Second, the lecturer goes on to say that, green roof is not spectacular view. Because, most people walking the side and can\'t see green roof. This challenges the writer\'s argument that green roofs offer a great view for many people to enjoy.<br>&nbsp;Finally, the lecturer maintains that, green roofs needs extra money to maintain falilities. For example, if soil is drain, there are needs more water and even change soil. So, additional cost is too high and expensive. This rebuts the writer\'s claim that, green roofs saving cost and energy.<br><br><br></td><td id="idwordcounttd" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; VERTICAL-ALIGN: top; PADDING-TOP: 5px"><br></td><td id="idwordcounttd" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; VERTICAL-ALIGN: top; PADDING-TOP: 5px"><br></td><td id="idwordcounttd" style="PADDING-RIGHT: 5px; PADDING-LEFT: 5px; PADDING-BOTTOM: 5px; VERTICAL-ALIGN: top; PADDING-TOP: 5px"><br></td></tr></tbody></table></td></tr></tbody></table>
</div>';

	preg_match_all('#<td .*idwordcounttd(.*?)>(.*?)</td>#is', $str, $ans_matches);
//preg_match_all('#<td id="idwordcounttd"(.*?)>(.*?)</td>#is', $str, $ans_matches);
	print_r($ans_matches);

	exit();
	
***/

	
	/***
	preg_match_all('|<div id="bbsContent".*</TBODY></TABLE>|', $content, $body_matches, PREG_SET_ORDER);

	print_r ($body_matches);
	print "body_matches : " . $body_matches[2][0];
	***/


	include "Snoopy.class.php";
	include "ContentAnal.php";
	include "database.php";

	$snoopy = new Snoopy;

	$max_crawling_count = 10000;

	$bbs_url = "http://www.gohackers.com/bbs/zboard.php?id=essay&page=1&sp1=&sn1=&divpage=15&sp=off&sn=off&ss=on&sc=on&sf=off&sa=off&select_arrange=headnum&no=21536";

	if ($bbs_url == "")
	{
		$fetch_domain = "http://www.gohackers.com";
		$seed_url = $fetch_domain . "/bbs/zboard.php?id=essay&idx=&no=&open_mode=&gre_gra=&page=&select_arrange=&headnum=&desc=&category=&sp=&sn=&ss=&sc=&sf=&keyword=&sp1=&sn1=&divpage=&sff=&sa=&md=&cmt=&outlink_frm=&show_bbs=&session=&referer1=&is_pa=1";
		$snoopy->fetchlinks($seed_url);
		print_r  ($snoopy->results);

		foreach ($snoopy->results as $url)
		{
			if (strpos($url, "http://www.gohackers.com/bbs/zboard.php?id=essay&no=") === 0) {
				print $url . "\n";
				$bbs_url = $url;
				break;
			}
		}

	}
	
	$crawling_count = 0;
	$fail_count = 0;
	$content_dic = array();

	while ($bbs_url != "") 
	{
		$crawling_count++;
		sleep(2);

		$con = DBConnect();
		if (!$con) {
			print "DBConnect Fail!!!!\n";
			exit();
		}

		print $crawling_count . ". " . $bbs_url . "\n";
		if ($snoopy->fetch($bbs_url)) 
		{
			//echo "response code: ".$snoopy->response_code."<br>\n";
			if (!strstr($snoopy->response_code, "HTTP/1.1 200 OK")) {
				echo "==> http error\n";
			}

			unset($content_dic);
			$content_dic['url'] = $bbs_url;

			if (is_array ($snoopy->results) ) 
			{
				$contents =  end($snoopy->results);
			}
			else
			{
				$contents = $snoopy->results;
			}

			$contentAnal = new ContentAnal($contents);
			$next_link = $contentAnal->getNextLink();
			//print "next_link : " . $next_link . "\n";
			if ($next_link != "")
			{
				$next_full_link = $snoopy->_expandlinks($next_link, $bbs_url);
				$bbs_url = $next_full_link;
				//print "next_full_link : " . $next_full_link . "\n";
			} else {
				$bbs_url = "";
			}

			$content_dic['body'] = $contentAnal->getBbsBody();
			//print "bbsBody : " . $bbsBody . "\n";

			$content_dic['topic'] = $contentAnal->getTopic();
			$content_dic['topic_striptag'] = $contentAnal->getTopicStripTag();
			//print "topic : " . $topic . "\n";

			$content_dic['answer'] = $contentAnal->getAnswer();
			$content_dic['answer_striptag'] = $contentAnal->getAnswerStripTag();
			//print "answer : " . $answer . "\n";

			if ($content_dic['body'] != "") $body_flag = 1;
			else $body_flag = 0;
			if ($content_dic['topic'] != "")      $topic_flag = 1;
			else $topic_flag = 0;
			if ($content_dic['answer'] != "")   $answer_flag = 1;
			else $answer_flag = 0;

			$content_dic['content_flag'] = $contentAnal->getContentFlag();

			print "$body_flag $topic_flag $answer_flag\n";

			if ($content_dic['content_flag'] > 0) {
				$result = insertBbsContent($content_dic, $con);
				if ($result == false)
				{
					print "DB insert fail\n";
					break;
				}
			} else {
				;
				//print_r ($snoopy->results);
			}

		} 
		else {
			print "url fetch fail\n";
			$fail_count++;
			sleep(10);
			if ($fail_count > 50) {
				print "fail_count : $fail_count\n";
				break;
			}
		}

		DBClose($con);

		if ($crawling_count >= $max_crawling_count)
		{
			break;
		}
		
	}

	if ($con) {
		DBClose($con);
	}


function proc_singlequo($str) {
	$str = str_replace("'", "''", $str);
	$str = str_replace("\\''", "\\'", $str);
	return $str;
}

function insertBbsContent($content_dic, $con) {
	$RS = new CDatabase();

	$url = $content_dic["url"];
	$body = proc_singlequo($content_dic["body"]);
	$topic = proc_singlequo($content_dic["topic"]);
	$topic_striptag = proc_singlequo($content_dic["topic_striptag"]);
	if ($topic_striptag != "" ) {
		$topic_md5 = md5($topic_striptag);
	} else {
		$topic_md5 = "";
	}
	$answer = proc_singlequo($content_dic["answer"]);
	$answer_striptag = proc_singlequo($content_dic["answer_striptag"]);
	if ($answer_striptag != "" ) {
		$answer_md5 = md5($answer_striptag);
	}
	else {
		$answer_md5 = "";
	}
	$flag = $content_dic["content_flag"];

	if ($answer_md5 != "") {
		$sql = "SELECT * FROM bbs_data WHERE answer_md5 = '$answer_md5'";
		if (!$RS->ExecSQL($con,	$sql)) return false;
		if ($RS->Fetch()) {
			print "Already Exists : " . $RS->Row["answer_md5"] . "\n";
			return true;
		}
	}
	else {
		$sql = "SELECT * FROM bbs_data WHERE url = '$url'";
		if (!$RS->ExecSQL($con,	$sql)) return false;
		if ($RS->Fetch()) {
			print "Already Exists : " . $RS->Row["url"] . "\n";
			return true;
		}
	}	

	$sql = "INSERT INTO bbs_data (
				url, body, topic, topic_striptag, topic_md5, answer, answer_striptag, answer_md5, flag
			) VALUES (
				'$url', '$body', '$topic', '$topic_striptag', '$topic_md5', '$answer',
				'$answer_striptag', '$answer_md5', $flag
			)";

	//print $sql;

	if ($RS->ExecSQL($con, $sql)) { return true; }
	else { 
		$RS->ErrorMsg();
		return false;
	}
}

?>