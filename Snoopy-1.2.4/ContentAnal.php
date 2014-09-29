<?php
Class ContentAnal {

	var $content;
	var $next_link;
	var $bbs_body;
	var $topic;
	var $answer;
	var $content_flag;

	function ContentAnal($content) {
		$this->content = $content;

		// initialize 
		$this->next_link = "";
		$this->bbs_body = "";
		$this->topic = "";
		$this->answer = "";
		$this->topic_striptag = "";
		$this->answer_striptag = "";
		$this->content_flag = 0;

		// get next link
		preg_match_all("/<a[^>]* href=(['|\"]*)([^\\1\040>]*)\\1[^>]*class=\"goNext\">/is", $this->content, $matches);
		if (count($matches) > 0)
		{
			if (! is_null($matches[2][0]) )
				$this->next_link = $matches[2][0];
			else
				return;
		}

		// get  bbs body
		$start_pos = strpos($this->content, iconv("UTF-8", "EUC-KR", "<!--게시글 내용 출력시작-->") );
		$end_pos = strpos($this->content, iconv("UTF-8", "EUC-KR", "<!--/ 게시글 내용 출력끝-->"));
		if ($start_pos > 0 && $end_pos > 0)
		{
			//print "start : $start_pos, end : $end_pos \n";
			$this->bbs_body = substr($this->content, $start_pos, $end_pos - $start_pos);
			$this->content_flag++;
		}

		// get topic
		preg_match_all('#&\#9654; Topic :(.*?)</td>#is', $this->bbs_body, $topic_matches);
		if (count($topic_matches[0]) == 0)
		{
			preg_match_all('#Topic :(.*?)</td>#is', $this->bbs_body, $topic_matches);
		}
		if (count($topic_matches[0]) > 0)
		{
			$this->topic = $topic_matches[1][0];
			$this->topic_striptag = $this->br2nl(strip_tags($this->topic, '<p><br>'));
			$this->topic_striptag = $this->p2nl($this->topic_striptag);
			$this->content_flag++;

		} else {
			preg_match_all('#<IMG src="skin/technote/images/ttl.gif">(.*?)</TR>(.*?)</td>#is', $this->bbs_body, $topic_matches);
			if (count($topic_matches[0]) > 0) {
				$this->topic = $topic_matches[2][0];
				$this->topic_striptag = $this->br2nl(strip_tags($this->topic, '<p><br>'));
				$this->topic_striptag = $this->p2nl($this->topic_striptag);
				$this->content_flag++;
				//print "\n[" . $this->topic_striptag . "]\n";
			}
		}

		// get answer
		preg_match_all('#<td .*idwordcounttd(.*?)>(.*?)</td>#is', $this->bbs_body, $ans_matches);
		//print_r ($ans_matches);
		if (count($ans_matches[0]) == 0)
		{
			preg_match_all('#<td id=idwordcounttd (.*?)>(.*?)</td>#is', $this->bbs_body, $ans_matches);
		}

		if (count($ans_matches[0]) > 0)
		{
			$this->answer = $ans_matches[2][0];
			$str_pos = stripos($this->answer, "Your Answer :");
			$this->answer = substr($ans_matches[2][0], $str_pos + 13);
			//$this->answer_striptag = strip_tags($this->answer, '<p><br>');
			$this->answer_striptag = $this->br2nl(strip_tags($this->answer, '<p><br>'));
			$this->answer_striptag = $this->p2nl($this->answer_striptag);
			$this->content_flag++;
		}
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

	function getNextLink() {
		return $this->next_link;
	}

	function getBbsBody() {
		return $this->bbs_body;
	}

	function getTopic() {
		return $this->topic;
	}

	function getAnswer() {
		return $this->answer;
	}

	function getTopicStripTag() {
		return $this->topic_striptag;
	}

	function getAnswerStripTag() {
		return $this->answer_striptag;
	}

	function getContentFlag() {
		return $this->content_flag;
	}
}

?>