<?php
namespace SuphiGram\Instagram;

class InstagramUser extends InstagramRequest{
	public $email;
	public $username;
	public $userid;
	public $password;

	
		public function GenrateENCPassword($password = null){
	$password = $password ?? $this->password;
	$sendResponse = '#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password;
	return $sendResponse;		
		}
		
	private function findEmail($response) {
    $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
    if (preg_match($pattern, $response, $matches)) {
        return $matches[0];
    }
    return null;
}

	private function FindPhone($response) {
	$pattern = '/\+\d+/';
    if (preg_match($pattern, $response, $matches)) {
        return $matches[0];
    }
    return null;
}


private function FindEmailID($response) {

    $explodedText = explode("fx_settings", $response);
    $afterFxSettings = end($explodedText);
    $pattern = '/\b\d+\b/';
    preg_match($pattern, $afterFxSettings, $matches);
    if (!empty($matches)) {
        return $matches[0];
    }
    return null;
}

private function Find2FactorSeed($response){
		$step1 = explode('u00253Fsecret\\\\u00253', $response);
		$step2 = explode('\\\\', $step1[1]);
		return $step2[0];
		
}


public function FindBackUpCodes($response){
		$step1 = explode('bk.action.array.Make', $response);
		$step2 = explode(')', $step1[1]);
		preg_match_all('/\b\d+\b/', $step2[0], $matches);
		$result = array_chunk($matches[0], 2);
		return $result;

}

	private function getcontext($response){
    $step1 = explode("has_follow_up_screens", $response);
    
    if (isset($step1[2])) {
        $step2 = explode('"', $step1[2]);
        $step3 = explode('\\', $step2[2]);
        return $step3[0];
    } else{
        $step3 = explode('\\", \\"', $step1[1]);
		$step4 = explode('\\', $step3[1]);
        return $step4[0];
    } 
}

	public function deleteMedia($mediaid){
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/delete/';
		//if you get error add ?media_type=PHOTO url's end.
		$paramsjson = '{"igtv_feed_preview":"false","media_id":"'.$mediaid.'","_uid":"'.$this->MyCache("pk").'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}

	public function saveMedia($mediaid){
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/save/';
		$params = 'signed_body=SIGNATURE.%7B%22inventory_source%22%3A%22media_or_ad%22%2C%22delivery_class%22%3A%22organic%22%2C%22module_name%22%3A%22feed_timeline%22%2C%22client_position%22%3A%222%22%2C%22ranking_info_token%22%3A%22bc76079f1d55423489c6193e8e647496%22%2C%22radio_type%22%3A%22wifi-none%22%2C%22_uid%22%3A%2263730780111%22%2C%22_uuid%22%3A%2269f73a0d-e663-4a2b-a7e9-56f38e1ad5a2%22%2C%22nav_chain%22%3A%22MainFeedFragment%3Afeed_timeline%3A1%3Acold_start%3A1708154730.212%3A%3A%22%7D';
		$sendRequest = $this->postwithcookie($url, $params);
		return $sendRequest;
	}


	public function getComments($mediaid){
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/comments/?inventory_source=media_or_ad&analytics_module=comments_v2_feed_contextual_profile&can_support_threading=true&is_carousel_bumped_post=false&feed_position=0';
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;
	}

	
	public function logout(){
		$url = $this->apibase.'/api/v1/accounts/logout/';
		$params = 'phone_id=b616617b-2152-40a7-a4b0-45165de4b455&guid=3993b830-5663-47ad-bd9f-902dab9b4050&device_id='.$this->MyCache("dervice_id").'&_uuid=3993b830-5663-47ad-bd9f-902dab9b4050';
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}
	

	public function getFollows($userid = null){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/friendships/'.$userid.'/following/?includes_hashtags=true&search_surface=follow_list_page&query=&enable_groups=true';
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;
	}
	
	public function getFollowers($userid = null){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/friendships/'.$userid.'/followers/?search_surface=follow_list_page&query=&enable_groups=true';
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;
	}
	
	public function SearchInFollows($userid = null, $query){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/friendships/'.$userid.'/following/?includes_hashtags=true&search_surface=follow_list_page&query='.$query.'&enable_groups=true';
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;
	}


	public function SearchInFollowers($userid = null, $query){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/friendships/'.$userid.'/followers/?search_surface=follow_list_page&query='.$query.'&enable_groups=true';
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;		
	}
	

	

	public function comment($token = null, $userid = null, $mediaid, $text){
		//mode = test
		$token = $token ?? $this->MyCache("token");
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/comment/';
		$paramsjson = '{"user_breadcrumb":"ukQgLujftnF+wD2A7pCH7LAgHVmkiRa4ogClzO+C4S0=\nNiAyMTQwIDAgMTcwNTEzMDg2NDM4Mw==\n","inventory_source":"media_or_ad","delivery_class":"organic","idempotence_token":"52752a05-97f1-4a53-80f7-c8288c2bed91","radio_type":"wifi-none","_uid":"'.$userid.'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","nav_chain":"MainFeedFragment:feed_timeline:1:cold_start:1705130823.800:10#230#301:3278953510916794970,UserDetailFragment:profile:4:media_owner:1705130834.399::,ProfileMediaTabFragment:profile:5:button:1705130834.930::,ContextualFeedFragment:feed_contextual_profile:6:button:1705130836.290::,CommentThreadFragment:comments_v2_feed_contextual_profile:7:button:1705130848.199::","logging_info_token":"18d5ced482914768b5ea7dba31335357","comment_text":"'.$text.'","is_carousel_bumped_post":"false","container_module":"comments_v2_feed_contextual_profile","feed_position":"0"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}
	
	
	
	public function deleteComment($token = null, $userid = null, $mediaid, $commentid){
		$token = $token ?? $this->MyCache("token");
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/comment/bulk_delete/';
		$paramsjson = '{"comment_ids_to_delete":"'.$commentid.'","_uid":"'.$userid.'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","container_module":"comments_v2_feed_contextual_profile"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}





	public function createNote($note){
		$url = $this->apibase.'/api/v1/notes/create_note/';
		$params = 'note_style=0&text='.urlencode($note).'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&audience=0';
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}
	
	
	public function deleteNote($noteid){
		$url = $this->apibase.'/api/v1/notes/delete_note/';
		$params = 'id='.$noteid.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c';
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}




	public function unfollow($token = null, $userid){
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/friendships/destroy/'.$userid.'/';
		$paramsjson = '{"inventory_source":"media_or_ad","media_id":"3273408257517143810_2182768664","ranking_info_token":"80e3af9e0f934e8d9f886123962a1014","user_id":"'.$userid.'","radio_type":"wifi-none","_uid":"'.$this->MyCache("token").'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","media_id_attribution":"3273408257517143810_2182768664","nav_chain":"MainFeedFragment:feed_timeline:1:cold_start:1705077502.197:10#230#301:3273408257517143810,UserDetailFragment:profile:5:media_owner:1705086648.279::,ProfileMediaTabFragment:profile:6:button:1705086648.814::,ContextualFeedFragment:feed_contextual:7:button:1705086651.632:10#230:3273408257517143810,UserDetailFragment:profile:10:media_owner:1705086892.203::,ProfileMediaTabFragment:profile:11:button:1705086892.707::,ProfileFollowRelationshipFragment:following_sheet:12:button:1705086898.769::","container_module":"following_sheet"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}


	public function follow($token = null, $userid){
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/friendships/create/'.$userid.'/';
		$paramsjson = '{"inventory_source":"media_or_ad","media_id":"3273408257517143810_2182768664","ranking_info_token":"80e3af9e0f934e8d9f886123962a1014","user_id":"'.$userid.'","radio_type":"wifi-none","_uid":"'.$this->MyCache("token").'","device_id":"android-80dbc8a0c0ab2a40","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","media_id_attribution":"3273408257517143810_2182768664","nav_chain":"MainFeedFragment:feed_timeline:1:cold_start:1705077502.197:10#230#301:3273408257517143810,UserDetailFragment:profile:5:media_owner:1705086648.279::,ProfileMediaTabFragment:profile:6:button:1705086648.814::,ContextualFeedFragment:feed_contextual:7:button:1705086651.632:10#230:3273408257517143810,UserDetailFragment:profile:10:media_owner:1705086892.203::,ProfileMediaTabFragment:profile:11:button:1705086892.707::,ProfileFollowRelationshipFragment:following_sheet:12:button:1705086898.769::,ProfileMediaTabFragment:profile:13:button:1705086909.957::"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}

	public function getMessages(){
		$url = $this->apibase.'/api/v1/direct_v2/inbox/?visual_message_return_type=unseen&thread_message_limit=10&persistentBadging=true&limit=20&is_prefetching=false&fetch_reason=manual_refresh';
		$sendRequest = $this->getwithcookie($url, null);
		return $sendRequest;
	}
	public function getRecommendedUsers($token = null, $userid){
		$url = $this->apibase.'/api/v1/discover/chaining/?module=profile&target_id='.$userid;
		$sendRequest = $this->getwithcookie($url);
		return $sendRequest;
	}

	public function likePost($token = null, $mediaid){
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/like/';
		$paramsjson = '{"inventory_source":"media_or_ad","delivery_class":"organic","tap_source":"button","media_id":"'.$mediaid.'","radio_type":"wifi-none","_uid":"'.$this->MyCache("pk").'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","nav_chain":"MainFeedFragment:feed_timeline:1:cold_start:1705077502.197::","logging_info_token":"80e3af9e0f934e8d9f886123962a1014","is_carousel_bumped_post":"false","container_module":"feed_timeline","feed_position":"0"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson).'&d=0';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}

	public function unlikePost($token = null, $mediaid){
		$url = $this->apibase.'/api/v1/media/'.$mediaid.'/unlike/';
		$paramsjson = '{"inventory_source":"media_or_ad","delivery_class":"organic","tap_source":"button","media_id":"'.$mediaid.'","radio_type":"wifi-none","_uid":"'.$this->MyCache("pk").'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","nav_chain":"MainFeedFragment:feed_timeline:1:cold_start:1705077502.197::","logging_info_token":"80e3af9e0f934e8d9f886123962a1014","is_carousel_bumped_post":"false","container_module":"feed_timeline","feed_position":"0"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson).'&d=0';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
		}




	public function disableFactorV2(){
		$url = $this->apibase.'/api/v1/accounts/disable_totp_two_factor/';
		$paramsjson = '{"_uid":"'.$this->MyCache("pk").'","device_id":"android-80dbc8a0c0ab2a40","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}



	public function seeSecurity(){
		$url = $this->apibase.'/api/v1/accounts/account_security_info/';
		$paramsjson = '{"_uid":"'.$this->MyCache("pk").'","device_id":"android-80dbc8a0c0ab2a40","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}

	public function addCloseFriend($userid){
	$url = $this->apibase.'/api/v1/stories/private_stories/add_member/';
	$params = 'module=audience_selection&source=settings&user_id='.$userid.'&_uuid=69f73a0d-e663-4a2b-a7e9-56f38e1ad5a2';
	$sendRequest = $this->postwithcookie($url, $params);
	return $sendRequest;
	}
	
	


	public function enable2Factor($opt){
		$url = $this->apibase.'/api/v1/accounts/enable_totp_two_factor/';
		$paramsjson = '{"verification_code":"'.$opt.'","_uid":"'.$this->MyCache("pk").'","device_id":"android-80dbc8a0c0ab2a40","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		$myjson = json_decode($sendRequest, true);
		if($myjson['status'] != 'fail'){
		$backupcodes = $myjson['backup_codes'];
				$stringedbackups = $backupcodes[0]."\r\n".$backupcodes[1]."\r\n".$backupcodes[2]."\r\n".$backupcodes[3]."\r\n".$backupcodes[4];
		$sendResponse = '{"Status": "ok", "Message": "Successful", "Suphi": "Code is correct", "BackupCodes": "'.$stringedbackups.'", "Response": "'.$sendRequest.'"}';
		}else{
		$sendResponse = '{"Status": "fail", "Message": "'.$myjson["message"].'", "Suphi": "Code is incorrect", "Response": "'.$sendRequest.'"}';
		}
		return $sendResponse;
	}


	public function add2factorv2($token = null){
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/accounts/generate_two_factor_totp_key/';
		$params = 'device_id=android-80dbc8a0c0ab2a40&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$myjson = json_decode($sendRequest, true);
		if(isset($myjson['status']) and $myjson['status'] == "ok"){
		$seed = $myjson['totp_seed'];
		$seedid = $myjson['totp_seed_id'];
		$sendResponse = '{"Status": "ok", "Message": "Paste this code into your authentication app. copy and paste six digit code", "Suphi": "Successfully get seed", "Seed": "'.$seed.'", "SeedId": "'.$seedid.'"}';
		}else{
			$sendResponse = '{"Status": "fail", "Message": "Somthing went wrong", "Suphi": "Error, maybe user not logged maybe endpoint expried please use v1 endpoint", "Response": "'.addslashes($sendRequest).'"}';
		}
		return $sendResponse;
	}




	public function uploadNewProfilePhoto($token = null, $imagepath){
		$token = $token ?? $this->MyCache("token");
		$image = file_get_contents($imagepath);
		$url = $this->apibase.'/rupload_igphoto/1705042294970_0_1308133934';
		$sendRequest = $this->postwithcookie($url, $image, $token);
		return $sendRequest;
	}


	
	
	public function sendMessage($token = null, $userid = null, $message){
		$token = $token ?? $this->MyCache("token");
		$userid = $userid ?? $this->userid;
		$url = $this->apibase.'/api/v1/direct_v2/threads/broadcast/text/';
		$settings1 = '[['.$userid.']]';
		$params = 'recipient_users='.urlencode($settings1).'&action=send_item&is_x_transport_forward=false&is_shh_mode=0&send_silently=false&send_attribution=message_button&client_context=7151461910951097531&text='.urlencode($message).'&device_id=android-80dbc8a0c0ab2a40&mutation_token=7151461910951097531&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&nav_chain=MainFeedFragment%3Afeed_timeline%3A4%3Amain_home%3A1705041257.113%3A%3A%2CUserDetailFragment%3Aprofile%3A6%3Amedia_owner%3A1705041345.939%3A%3A%2CProfileMediaTabFragment%3Aprofile%3A7%3Abutton%3A1705041351.403%3A%3A%2CDirectThreadFragment%3Adirect_thread%3A8%3Amessage_button%3A1705041354.846%3A%3A&offline_threading_id=7152807039707563491';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}
	




	public function seeBackupCodes($token = null){
		
	$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.security.two_factor.recovery_codes.regenerate/';
		$paramsjson = '{"client_input_params":{"machine_id":"ZZYnEgABAAHIt7pyhanAPrtKlp6L","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455"},"server_params":{"INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":94956018800047,"ig_auth_proof_json":"Bearer'.$token.'","account_type":1,"machine_id":null,"INTERNAL__latency_qpl_marker_id":36707139,"account_id":'.$this->MyCache("fbid").'}}';
		$params = 'params='.urlencode($paramsjson).'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$getcodes = $this->FindBackUpCodes($sendRequest);
		$codes = '';
		foreach($getcodes as $bcode){
			$codes = $codes."\r\n".$bcode[0].$bcode[1];
		}
		$sendResponse = '{"Status": "ok", "Message": "'.$codes.'", "BackUpCodes": "'.$getcodes.'", "Response": "'.$codes.'", "Suphi": "Successful"}';
		return $sendResponse;
	}

	public function disableFactor($token = null){
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.security.two_factor.totp.disable/';
		$paramsjson = '{"client_input_params":{"machine_id":"ZZYnEgABAAHIt7pyhanAPrtKlp6L","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455","ig_auth_proof_json":"Bearer'.$token.'","device_id":"android-80dbc8a0c0ab2a40"},"server_params":{"machine_id":null,"account_type":1,"INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":104389100700062,"INTERNAL__latency_qpl_marker_id":36707139,"account_id":'.$this->MyCache("fbid").'}}';
		$params = 'params='.urlencode($paramsjson).'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}


	public function factoropt($token = null, $opt){
		$token = $token ?? $this->MyCache("token");
		$paramsjson = '{"client_input_params":{"machine_id":"ZZYnEgABAAHIt7pyhanAPrtKlp6L","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455","device_id":"android-80dbc8a0c0ab2a40","verification_code":"'.$opt.'"},"server_params":{"account_type":1,"INTERNAL__latency_qpl_instance_id":93325075700036,"ig_auth_proof_json":"Bearer'.$token.'","INTERNAL__latency_qpl_marker_id":36707139,"account_id":'.$this->MyCache("fbid").'}}';
		$params = 'params='.urlencode($paramsjson).'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.security.two_factor.totp.enable/';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		return $sendRequest;
	}

	public function add2factor($token = null){
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.security.two_factor.totp.generate_key/';
		$paramsjson = '{"client_input_params":{"machine_id":"ZZYnEgABAAHIt7pyhanAPrtKlp6L","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455","device_id":"android-80dbc8a0c0ab2a40"},"server_params":{"INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":77831834500057,"ig_auth_proof_json":"Bearer'.$token.'","account_type":1,"machine_id":null,"INTERNAL__latency_qpl_marker_id":36707139,"account_id":'.$this->MyCache("fbid").'}}';
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$getSeed = $this->Find2FactorSeed($sendRequest);
		$sendResponse = '{"Status": "ok", "Message": "Paste this code into your authentication app. copy and paste six digit code", "Suphi": "seed received successfully", "Seed": "'.$getSeed.'"}';
		//var_dump($getSeed);
		return $sendResponse;
	}


	public function getMyInbox($token = null, $messagelimit = 10, $limit = 20){
		$token = $token ?? $this->MyCache("token");
		$url = $this->apibase.'/api/v1/direct_v2/inbox/?visual_message_return_type=unseen&thread_message_limit=10&persistentBadging=true&limit=20&is_prefetching=false&fetch_reason=manual_refresh';
		$sendRequest = $this->getwithcookie($url, $token);
		return $sendRequest;
	}

	public function getScores(){
		$params = '["autocomplete_user_list","coefficient_besties_list_ranking","coefficient_rank_recipient_user_suggestion","coefficient_ios_section_test_bootstrap_ranking","coefficient_direct_recipients_ranking_variant_2"]';
		$url = $this->apibase.'/api/v1/scores/bootstrap/users/?surfaces='.urlencode($params);
		$sendRequest = $this->getwithcookie($url, null, null);

	public function changeUsername($token = null, $username = null){
		$token = $token ?? $this->MyCache("token");
		$username = $username ?? $this->username;
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fxim.settings.username.change.async/';
		$paramsjson = urlencode('{"client_input_params":{"username":"'.$username.'","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455"},"server_params":{"machine_id":null,"identity_ids_DEPRECATED":"'.$this->MyCache("fbid").'","operation_type":"MUTATE","INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":62670334400129,"INTERNAL__latency_qpl_marker_id":36707139}}');
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$debug = var_dump($sendRequest);
		return $sendRequest;
		}
	
	
	public function ConfirmOPTEmail($token = null, $email = null, $opt){
		$token = $token ?? $this->MyCache("token");
		$email = $email ?? $this->email;
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.contact_point.verify.async/';
		$paramsjson = urlencode('{"client_input_params":{"family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455","pin_code":"'.$opt.'"},"server_params":{"contact_point_source":"fx_settings","selected_accounts":"'.$this->MyCache("fbid").'","INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":'.$this->MyCache("fbid").',"machine_id":null,"normalized_contact_point":"'.$email.'","ig_account_encrypted_auth_proof":"Bearer'.$token.'","INTERNAL__latency_qpl_marker_id":'.$this->MyCache("fbid").',"contact_point_event_type":"add","contact_point_type":"email"}}');
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = json_decode($this->postwithcookie($url, $params, $token), true);
		//$debug = var_dump($sendRequest);
	
		if(isset($sendRequest['status']) and $sendRequest['status'] == 'ok'){
		$sendResponse = '{"Status": "ok", "Message": "Successfull your email is confirmed", "Suphi": "Email Confimr Code is correct"}';
	}else{
		$sendResponse = '{"Status": "fail", "Message": "Your code is incorrect, please doble-check your code", "Suphi": "Email Confimr Code is incorrect"}';
	}
	return $sendResponse;
	
	}
	
	
	
	public function addNewEmail($token = null, $email = null){
		$token = $token ?? $this->MyCache("token");
		$email = $email ?? $this->email;
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.contact_point.add.async/';
		$paramsjson = urlencode('{"client_input_params":{"family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455","selected_accounts":["'.$this->MyCache("fbid").'"],"contact_point":"'.$email.'","country":null,"ig_account_encrypted_auth_proof":"Bearer'.$token.'"},"server_params":{"contact_point_source":"fx_settings","INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":54655113900050,"machine_id":null,"INTERNAL__latency_qpl_marker_id":36707139,"serialized_states":{"input_error":"2;jdg7nnv24;0","input_error_message":"2;jdg7nnv25;0"},"contact_point_type":"email","contact_point_event_type":"add"}}');
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$FindEmailcode = $this->FindEmailID($sendRequest);
		$sendResponse = '{"Status": "ok", "Message": "Please enter the your email verify code", "Suphi": "Email Verfication Code Sended", "EmailID": "'.$FindEmailcode.'"}';
		//$debug = var_dump($sendRequest);
		//$this->addNewEmailv2($token, $email);
		return $sendResponse;
	}
	
		public function addNewEmailv2($token = null, $email = null){
		$token = $token ?? $this->MyCache("token");
		$email = $email ?? $this->email;
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.contact_point.verify/';
		$paramsjson = urlencode('{"server_params":{"contact_point_source":"fx_settings","selected_accounts":"'.$this->MyCache("fbid").'","INTERNAL_INFRA_THEME":"default","normalized_contact_point":"'.$email.'","ig_account_encrypted_auth_proof":"Bearer'.$token.'","INTERNAL_INFRA_screen_id":"91o3qj:3","contact_point_type":"email","contact_point_event_type":"add"}}');
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$debug = var_dump($sendRequest);
		return $debug;
	}
	
	public function addNewEmailV3($email, $userid = null){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/accounts/send_confirm_email/';
		$paramsjson = '{"phone_id":"b616617b-2152-40a7-a4b0-45165de4b455","send_source":"personal_information","_uid":"'.$userid.'","guid":"ad9b4188-2663-435c-bfcf-fc22e029283c","device_id":"android-80dbc8a0c0ab2a40","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","email":"'.$email.'"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}
	
	public function verify_email_code($code, $email, $userid = null){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/accounts/verify_email_code/';
		$paramsjson = '{"_uid":"'.$userid.'","code":"'.$code.'","device_id":"android-80dbc8a0c0ab2a40","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","email":"'.$email.'"}';		
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;		
	}
	
	public function addPhone($phone, $userid = null){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/accounts/initiate_phone_number_confirmation/';
		$paramsjson = '{"phone_id":"b616617b-2152-40a7-a4b0-45165de4b455","phone_number":"'.$phone.'","send_source":"edit_profile","_uid":"'.$userid.'","guid":"ad9b4188-2663-435c-bfcf-fc22e029283c","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","android_build_type":"release"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
		}
		
	public function verify_sms_code($code, $phone, $userid = null){
		$userid = $userid ?? $this->MyCache("pk");
		$url = $this->apibase.'/api/v1/accounts/verify_sms_code/ ';
		$paramsjson = '{"verification_code":"'.$params.'","phone_number":"'.$phone.'","_uid":"'.$userid.'","_uuid":"ad9b4188-2663-435c-bfcf-fc22e029283c","has_sms_consent":"true"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}		
	
	public function setBirthday($day, $month, $year){
		$url = $this->apibase.'/api/v1/accounts/set_birthday/';
		$params = 'day='.$day.'&year='.$year.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&month='.$month;
		$sendRequest = $this->postwithcookie($url, $params, null);		
		return $sendRequest;
	}
	
	
	public function deleteMyEmail($token = null, $email = null){
		$token = $token ?? $this->MyCache("token");
		$email = $email ?? $this->MyCache("email");
		$paramsjson = urlencode('{"client_input_params":{"ig_account_encrypted_auth_proof":"Bearer'.$token.'","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455"},"server_params":{"selected_accounts":"'.$this->MyCache("fbid").'","INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":46577524900109,"normalized_contact_point":"'.$email.'","machine_id":null,"INTERNAL__latency_qpl_marker_id":36707139,"contact_point_type":"email"}}');
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.contact_point.delete.async/';
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		//$debug = var_dump($sendRequest);
		if(strpos($sendRequest, "bk.action.dialog.OpenDialogV2")){
			$sendResponse = '{"Status": "fail", "Message": "Because we noticed that you are using a device that you do not usually use, and we need to keep your account safe.We will let you make this change after you have used this device for a while.", "Suphi": "Permission denied because you are new login"}';
	}else{
		$sendResponse = '{"Status": "ok", "Message": "Successfuly Deleted", "Suphi": "User\'s email deleted"}';
	}
	return $sendResponse;
	
	}
	
	
	
	
	
	public function deleteMyPhone($token = null, $phone = null){
		$token = $token ?? $this->MyCache("token");
		$phone = $phone ?? $this->MyCache("phone");
		$paramsjson = urlencode('{"client_input_params":{"ig_account_encrypted_auth_proof":"Bearer'.$token.'","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455"},"server_params":{"selected_accounts":"'.$this->MyCache("fbid").'","INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":43288383300109,"normalized_contact_point":"'.$phone.'","machine_id":null,"INTERNAL__latency_qpl_marker_id":36707139,"contact_point_type":"phone_number"}}');	
		$params = 'params='.$paramsjson.'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.contact_point.delete.async/';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		//$debug = var_dump($sendRequest);
		if(strpos($sendRequest, "bk.action.dialog.OpenDialogV2")){
			$sendResponse = '{"Status": "fail", "Message": "Because we noticed that you are using a device that you do not usually use, and we need to keep your account safe.We will let you make this change after you have used this device for a while.", "Suphi": "Permission denied because you are new login"}';
	}else{
		$sendResponse = '{"Status": "ok", "Message": "Successfuly Deleted", "Suphi": "User\'s phonenumber deleted"}';
	}
	return $sendResponse;
	
	}
	
	
	public function InfoMyAccount($token = null){
		$token = $token ?? $this->MyCache("token");
		$params = 'params=%7B%22server_params%22%3A%7B%22INTERNAL_INFRA_THEME%22%3A%22default%22%2C%22INTERNAL__latency_qpl_instance_id%22%3A11163921800002%2C%22entrypoint%22%3A%22app_settings%22%2C%22parent_id%22%3A11163921800000%2C%22machine_id%22%3Anull%2C%22node_id%22%3A%22personal_info%22%2C%22INTERNAL__latency_qpl_marker_id%22%3A36707139%2C%22header_id%22%3A11163921800001%7D%7D&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fxcal.xplat.settings.update.navigation.node.async/';
		$sendRequest = $this->postwithcookie($url, $params, $token);
		$decode = json_decode($sendRequest, true);
		//$debug = var_dump($sendRequest);
		if($decode['status'] != 'ok'){
		$sendResponse = '{"Status": "ok", "Message": "Somthing went wrond.", "Suphi": "Maybe user not logged."}';
		}else{
		$email = $this->FindEmail($sendRequest);
		$phone = $this->FindPhone($sendRequest);
		$emailmessage = $email ?? 'your email is not detected';
		$phonemessage = $phone ?? 'your phonenumber is not detected';
		$sendResponse = '{"Status": "ok", "Message": "Email: '.$emailmessage.' Phone: '.$phonemessage.'", "Email": "'.$email.'", "Phone": "'.$phone.'", "Suphi": "Successfull"}';
		}
		return $sendResponse;
		
		
		
	}
	
	
	
	
	
	public function deleteAccount($username = null){
		$username = $username ?? $this->username;
		$token = $this->MyCache("token");
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.ig.inactivation.submit_finish/';
		$paramsjson = '{"client_input_params":{"ig_account_encrypted_auth_proof":"Bearer '.$this->MyCache("token").'","device_id":"android-80dbc8a0c0ab2a40"},"server_params":{"INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":92896471500034,"username":"'.$username.'","identity_id":'.$this->MyCache("fbid").',"reason":"other","family_device_id":"b616617b-2152-40a7-a4b0-45165de4b455","is_deletion":1,"event_request_id":"d0b0a5c7-7f72-4c52-9cca-eea3d0367b39","INTERNAL__latency_qpl_marker_id":36707139}}';
		$params = 'params='.urlencode($paramsjson).'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postwithcookie($url, $params, null);
		return $sendRequest;
	}
	
	
	
	
	
	
	
	public function ChangePassword($oldpassword, $newpassword, $username){
		$token = $this->MyCache("token");
		$username = $username ?? $this->username;
		$oldpassword = $this->GenrateENCPassword($oldpassword);
		$newpassword = $this->GenrateENCPassword($newpassword);
		$url = $this->apibase.'/api/v1/bloks/apps/com.bloks.www.fx.settings.security.change_password.submit_password/';
		
	$paramsjson = '{"client_input_params":{"device_id":"android-80dbc8a0c0ab2a40","current_password":"'.$oldpassword.'","machine_id":"ZZYnEgABAAHIt7pyhanAPrtKlp6L","account_type":1,"new_password":"'.$newpassword.'","account_name":"'.$username.'","ig_account_encrypted_auth_proof":"Bearer '.$token.'","new_password_confirm":"'.$newpassword.'","account_id":'.$this->MyCache("fbid").'},"server_params":{"machine_id":null,"profile_picture_url":"https://scontent.fadb5-1.fna.fbcdn.net/v/t1.30497-1/115870214_694925034696967_1870022665148339563_n.jpg?stp=dst-jpg_p200x200&_nc_cat=1&ccb=1-7&_nc_sid=810bd0&_nc_ohc=5JY2vtePp4IAX9q-POQ&_nc_ad=z-m&_nc_cid=0&_nc_ht=scontent.fadb5-1.fna&oh=00_AfB4BCaeOcsUvylZ1g1OnE3uHNg0fboE7KGOJJkMp6QzDA&oe=65CF1062","INTERNAL_INFRA_THEME":"default","INTERNAL__latency_qpl_instance_id":121571436800277,"INTERNAL__latency_qpl_marker_id":36707139}}';
	$params = 'params='.urlencode($paramsjson).'&_uuid=ad9b4188-2663-435c-bfcf-fc22e029283c&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';

		
		$getResponse = $this->postwithcookie($url, $params, null);
		$responseArray = json_decode($getResponse, true);

if (isset($responseArray['status']) && $responseArray['status'] === 'fail') {
    $sendResponse = '{"Status": "fail", "Message": "Please check your current password", "Suphi": "Something went wrong, maybe current password wrong or new password is not usable", "Response": "'.$getResponse.'"}';
} else {
    $sendResponse = '{"Status": "ok", "Message": "Password changed successfully", "Suphi": "Password Changed", "Response": "'.$getResponse.'"}';
}

return $getResponse;
	}			
	
	
	
	
	
}
