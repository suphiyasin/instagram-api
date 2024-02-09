<?php
namespace SuphiGram\Instagram;
class InstagramManuel extends InstagramRequest{
	
	public $username;
	public $userid;
	private $digitcodelenght = 6;
	
		public function genGSM($veriable){
		 $bro1 = explode(" ", $veriable);
   $countrycode = urlencode($bro1[0]);
   $gsm = $countrycode.'+'.$bro1[1].'+'.$bro1[2].'+'.$bro1[3].'+'.$bro1[4];
   return $gsm;
	}
	
	public function generateMail(){
		$step1 = file_get_contents("https://www.1secmail.com/api/v1/?action=genRandomMailbox&count=1");
		$emailArray = json_decode($step1);
		return $emailArray[0];
	}
	
	public function getMailInbox($mail){
		$step1 = explode("@", $mail);
		$login = $step1[0];
		$domain = $step1[1];
		$step2 = file_get_contents("https://www.1secmail.com/api/v1/?action=getMessages&login=$login&domain=$domain");
		$mails = json_decode($step2, true);
if (!empty($mails[0]['id'])) {
    $subject = $mails[0]['id'];
	return $subject;
} else {
    return null;
}
	}
	
	public function seeMailv2($mail, $id){
		$step1 = explode("@", $mail);
		$login = $step1[0];
		$domain = $step1[1];
		$step2 = file_get_contents("https://www.1secmail.com/api/v1/?action=readMessage&login=$login&domain=$domain&id=$id");
		preg_match('/\b\d{6}\b/', $step2, $matches);
		return isset($matches[0]) ? $matches[0] : null;
	}
	
	
	

	public function getUserInfo($userid = null, $token = null){
		$userid = $userid ?? $this->userid;
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/users/'.$userid.'/info/?is_prefetch=false&entry_point=profile&from_module=feed_timeline';
		$sendRequest = $this->getwithcookie($url, $token);
		return $sendRequest;
		
	}
	
	public function getRecommendUsersFromUserId($token = null, $userid = null){
		$token = $token ?? $this->MyCache("token");
		$userid = $userid ?? $this->userid;
		$url = $this->apibase.'/api/v1/discover/chaining/?module=profile&target_id='.$userid;
		$sendRequest = $this->getwithcookie($url, $token);
		return $sendRequest;
	}
	
	
	
	public function getHashtag($hash){
		$url = $this->apibase.'/api/v1/tags/hash/sections/';
		$params = 'supported_tabs=%5B%22account%22%5D&media_recency_filter=default&lat=39.905498333333334&lng=116.39099833333333&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&include_persistent=true&rank_token=60c450db-62b0-4404-bd92-eadc67b5a82b';
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}
	
	
	public function search($query){
	$url = $this->apibase.'/api/v1/fbsearch/ig_typeahead/?search_surface=typeahead_search_page&timezone_offset=10800&count=30&query='.$query.'&context=blended';
	$sendRequest = $this->getwithcookie($url, null);
	return $sendRequest;
	}
	
	public function getFeed($token = null, $userid = null){
		$token = $token ?? $this->MyCache("token");
		$userid = $userid ?? $this->userid;
		$url = $this->apibase.'/api/v1/feed/user/'.$userid.'/?exclude_comment=true&only_fetch_first_carousel_media=false';
		$sendRequest = $this->getwithcookie($url, $token);
		return $sendRequest;
	}
	
	public function getReelsInfo($x){
		if (strpos($x, "https://") === 0) {
			$step1 = explode("/", $x);
			$id = $step1[4];
		} else {
			$id = $x;
		}
		$url = $this->apibase.'/api/v1/clips/item/?clips_media_shortcode='.$id;
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;
	}
	public function getLikersFromPost(){
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/likers/';
		$sendRequest = $this->getwithcookie($url);
		return $sendRequest;
	}
	
}
