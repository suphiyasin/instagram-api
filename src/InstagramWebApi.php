<?php
namespace SuphiGram\Instagram;

class InstagramWebApi extends InstagramRequest{
	
	public $username;
	public $password;
	
	public function login($username = null, $password = null){
		$password = $password ?? $this->password;
		$password = (strlen($password) > 1) ? '#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password : null;
		$username = $username ?? $this->username;
		$params = 'enc_password='.$password.'&optIntoOneTap=false&queryParams=%7B%7D&trustedDeviceRecords=%7B%7D&username='.$username;
		$url = $this->apibase2.'/api/v1/web/accounts/login/ajax/';
		$sendRequest = $this->post($url, $params, null);
		$myjson = json_decode($sendRequest, true);
		if(isset($myjson['status']) and $myjson["status"] == "ok"){
			
		}else if(isset($myjson["status"]) and $myjson["status"] == "fail" and $myjson["error_type"] == "two_factor_required"){
			$twofactorid = $myjson["two_factor_info"]["two_factor_identifier"];
			$sendResponse = '{"Status": "ok", "Finish": false, "Message": "Enter 6 Digit Code From Your Duo Mobile", "Suphi": "Corecct username and password but two factor required", "TwoFactorID": "'.$twofactorid.'"}';
		}else{
			$sendResponse = '{"Status": "fail", "Finish": false, "Message": "Your username or password is incorecct, please double-check your informations", "Suphi": "InCorecct username or password "}';	
		}
	}
	
	
	public function send2Factor($username = null, $twoid, $code){
		$username = $username ?? $this->username;
		$url = $this->apibase2.'/api/v1/web/accounts/login/ajax/two_factor/';
		$params = 'identifier='.$twoid.'&queryParams=%7B%22next%22%3A%22%2F%22%7D&trust_signal=true&username='.$username.'&verification_method=3&verificationCode='.$code;
		$sendRequest = $this->post($url, $params, null);
		$myjson = json_decode($sendRequest, true);
		if(isset($myjson["status"]) and $myjson["status"] == "fail"){
			$sendResponse = '{"Status": "fail", "Finish": false, "Message": "'.$myjson["message"].'", "Suphi": "Incorrect 2 Factor Code"}';
	}else if(isset($myjson["status"]) and $myjson["status"] == "ok"){
		$sendResponse = '{"Status": "ok", "Finish": true, "Message": "Successful logged", "DerviceTrust": "'.$myjson["trustedDeviceNonce"].'", "Suphi": "Correct username and password and 2factor code"}';
	}else{
		$sendResponse = '{"Status": "fail", "Finish": false, "Message": "Somthing went wrong", "Suphi": "Maybe IP Banned"}';
	}
	return $sendResponse;
	}
	
	
	public function getUserInfoFromUsername($token = null, $username = null){
		$username = $username ?? $this->username;
		$url = $this->apibase2.'/api/v1/users/web_profile_info/?username='.$username;
		if($token === null){
		$sendRequest = $this->get($url, null, null);
		}else{
		$sendRequest = $this->getwithcookie($url, null, $token);
		}
		return $sendRequest;
	}
	
	public function getRecommendUsers($token = null, $userid = null){
		$userid = $userid ?? $this->userid;
		$url = $this->apibase2.'https://www.instagram.com/graphql/query/?query_id=9957820854288654&user_id='.$userid.'&include_chaining=false&include_reel=true&include_suggested_users=false&include_logged_out_extras=false&include_live_status=false&include_highlight_reels=true';
		$sendRequest = is_null($token) ? $this->get($url, null, null) : $this->getwithcookie($url, $token, null);
		return $sendRequest;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}