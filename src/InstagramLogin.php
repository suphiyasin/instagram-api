<?php

namespace SuphiGram\Instagram;
class InstagramLogin extends InstagramRequest{
	
	public $targetUsername;
	public $username;
	public $password;
	public $targetPk;



		private function genGSM($veriable){
		 $bro1 = explode(" ", $veriable);
   $countrycode = urlencode($bro1[0]);
   $gsm = $countrycode.'+'.$bro1[1].'+'.$bro1[2].'+'.$bro1[3].'+'.$bro1[4];
   return $gsm;
	}

	
	private function getcontext($response){
    $step1 = explode("has_follow_up_screens", $response);
    
    if (isset($step1[2])) {
        $step2 = explode('"', $step1[2]);
        $step3 = explode('\\', $step2[2]);
		if(strlen($step3[0]) > 15){
        return $step3[0];
		}else{
			$step3 = explode('\\", \\"', $step1[2]);
		$step4 = explode('\\', $step3[1]);
        return $step4[0];
		}
    } else{
        $step3 = explode('\\", \\"', $step1[1]);
		$step4 = explode('\\', $step3[1]);
        return $step4[0];
    } 
}
	
private function getChallendeContext($response){
	$step1 = explode("ig_challenge_navigation", $response);
	$step2 = explode("bk.action.i32.Cons", $step1[2]);
	$step3 = explode('"', $step2[2]);
	$step4 = explode("\\", $step3[1]);
	return $step4[0];
}

	
		public function GenrateENCPassword($password = null){
	$password = $password ?? $this->password;
	$sendResponse = '#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password;
	return $sendResponse;		
		}
		
		public function FindBearerToken($response){
			$step1 = explode("Bearer", $response);
			$step2 = explode('"', $step1[1]);
			$bearertoken = substr($step2[0], 0, -7);
			return $bearertoken;
		}
	
	
	public function challengeVerifiy($code, $context){
		$url = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.take_challenge/';
		$paramsjson = '{"bloks_version":"8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb","styles_id":"instagram"}';
		$params = 'security_code='.$code.'&perf_logging_id=1982787921&has_follow_up_screens=0&bk_client_context='.urlencode($paramsjson).'&challenge_context='.$context.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->postforlogin($url, $params);
		if(strpos($sendRequest, "ig.action.navigation.OpenDialog")){
			$sendResponse = '{"Status": "fail", "Message": "Please double-check your verification code", "Suphi": "Incorrect verification code"}';
		}else{
			$sendResponse = '{"Status": "ok", "Message": "Correct Code", "Suphi": "Correct code", "Token": "'.$this->MyCache("token").'"}';
		}
		return $sendResponse;
	}
	
	
	
	public function twofactor3($twoid, $code, $verify_type = null, $username = null){
		$username = $username ?? $this->username;
		$verify_type = $verify_type ?? '3';
		$url = $this->apibase2.'/api/v1/web/accounts/login/ajax/two_factor/';
		$params = 'identifier='.$twoid.'&queryParams=%7B%22next%22%3A%22https%3A%2F%2Fwww.instagram.com%2F%3F__coig_restricted%3D1%22%7D&trust_signal=true&username='.$username.'&verification_method='.$verify_type.'&verificationCode='.$code;
		$sendRequest = $this->post($url, $params, null);
		return $sendRequest;
	}
	
	public function twofactorv2($username = null, $twoid, $code, $verifiytype = null){
		$username = $username ?? $this->username;
		$verifiytype = $verifiytype ?? '3';
		//1= Sms Verifivation || 2 = backup codes || 3 = six digit code || 4= I guess its whatsapp verification
		$url = $this->apibase.'/api/v1/accounts/two_factor_login/';
		$paramsjson = '{"verification_code":"'.$code.'","phone_id":"3d15a79d-f8ae-4083-ba88-100c85648ed7","two_factor_identifier":"'.$twoid.'","username":"'.$username.'","trust_this_device":"1","guid":"3993b830-5663-47ad-bd9f-902dab9b4050","device_id":"android-80dbc8a0c0ab2a40","waterfall_id":"e07ccca7-f73c-4bcf-9db1-1f000e380024","verification_method":"'.$verifiytype.'"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		var_dump($params);
		$sendRequest = $this->postforlogin($url, $params);
		if(isset($myjson['status']) and $myjson['status'] == "ok"){
			$pk = $myjson['logged_in_user']['pk'];
			$fbid = $myjson['logged_in_user']['fbid_v2'];
			$phone = $myjson['logged_in_user']['phone_number'];
			$this->MyCache("pk", $pk);
			$this->MyCache("fbid", $fbid);
			$this->MyCache("phone", $phone);
		}
		return $sendRequest;
	}
	

	public function loginv2($username = null, $password = null){
		$username = $username ?? $this->username;
		$password = $password ?? $this->password;
		$password = $this->GenrateENCPassword($password);
		$url = $this->apibase.'/api/v1/accounts/login/';
		$paramsjson = '{"jazoest":"22384","country_codes":"[{\"country_code\":\"90\",\"source\":[\"sim\",\"network\",\"default\"]}]","phone_id":"b616617b-2152-40a7-a4b0-45165de4b455","enc_password":"'.$password.'","username":"'.$username.'","adid":"e64c113d-3921-4fc6-8efd-3a08b2a72d48","guid":"3993b830-5663-47ad-bd9f-902dab9b4050","device_id":"'.$this->MyCache("dervice_id").'","google_tokens":"[]","login_attempt_count":"0"}';
		$params = 'signed_body=SIGNATURE.'.urlencode($paramsjson);
		$sendRequest = $this->postforlogin($url, $params);
		$myjson = json_decode($sendRequest, true);
			var_dump($sendRequest);
		if(isset($myjson['status']) and $myjson['status'] == "ok"){
			$pk = $myjson['logged_in_user']['pk'];
			$fbid = $myjson['logged_in_user']['fbid_v2'];
			$phone = $myjson['logged_in_user']['phone_number'];
			$this->MyCache("pk", $pk);
			$this->MyCache("fbid", $fbid);
			$this->MyCache("phone", $phone);
			$sendResponse = '{"Status": "ok", "Message": "Successfuly logged", "Suphi": "Correct useranme and password", "Token": "'.$this->MyCache("token").'"}';
	}else if(isset($myjson['status']) and $myjson['status']== "fail" and isset($myjson["two_factor_info"]["two_factor_identifier"]) and $myjson["two_factor_required"] === true){
		$is_sms = $myjson["two_factor_info"]["sms_two_factor_on"] ? 'true' : 'false';
		$is_whatsapp = $myjson["two_factor_info"]["whatsapp_two_factor_on"] ? 'true' : 'false';
		$is_topt = $myjson["two_factor_info"]["totp_two_factor_on"] ? 'true' : 'false';
		$whatmyphone = (strlen($myjson["two_factor_info"]["obfuscated_phone_number_2"]) > 5) ? substr_replace($myjson["two_factor_info"]["obfuscated_phone_number_2"], $myjson["two_factor_info"]["obfuscated_phone_number"], -5) : false;
		$sendResponse = '{"Status": "fail", "is_sms": '.$is_sms.', "is_whatsapp": '.$is_whatsapp.', "is_topt": '.$is_topt.', "phone": "'.$whatmyphone.'", "Message": "Please enter 6 digit code from vertification application", "Suphi": "Correct useranme and password but two factor required", "TwoFactorId": "'.$myjson["two_factor_info"]["two_factor_identifier"].'"}';
	}else if(isset($myjson["status"]) and $myjson["status"] == "fail" and isset($myjson["message"]) and $myjson["message"] == "challenge_required"){
		$challengeendpoint = $myjson["challenge"]["api_path"];
		$challengecontext = $myjson["challenge"]["challenge_context"];
		$urlv2 = $this->apibase.'/api/v1'.$challengeendpoint.'?guid=ad9b4188-2663-435c-bfcf-fc22e029283c&device_id=android-80dbc8a0c0ab2a40&challenge_context='.urlencode($challengecontext);
		$sendRequestV2 = $this->get($urlv2, null, null);
		$myjsonv2 = json_decode($sendRequestV2, true);
		$email = $myjsonv2["step_data"]["email"] ?? $myjsonv2["step_data"]["contact_point"];
		$step_name = $myjsonv2["step_name"];
		$contextv2 = $myjsonv2["challenge_context"];
		$choise = $myjsonv2["choice"] ?? '1';
		$nonce_code = $myjsonv2["nonce_code"];
		$user_id = $myjsonv2["user_id"];
		$cni = $myjsonv2["cni"];
		$urlv3 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.take_challenge/';
		$paramsjsonv3 = '{"bloks_version":"8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb","styles_id":"instagram"}';
		$paramsv3 = 'user_id='.$user_id.'&cni='.$cni.'&nonce_code='.$nonce_code.'&bk_client_context='.urlencode($paramsjsonv3).'&fb_family_device_id=b616617b-2152-40a7-a4b0-45165de4b455&challenge_context='.$contextv2.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb&get_challenge=true';
		$sendRequestV3 = $this->post($urlv3, $paramsv3, null);
		$getResult = $this->getcontext($sendRequestV3);
		if($step_name == "review_contact_point_change"){
		
		$urlv4 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.take_challenge/';
		$paramsv4 = 'choice=1&has_follow_up_screens=0&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getResult.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequestV4 = $this->post($urlv4, $paramsv4, null);
		$getResultV2 = $this->getcontext($sendRequestV4);
				$urlv4 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.take_challenge/';
		$paramsv4 = 'choice=1&has_follow_up_screens=0&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getResultV2.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequestV4 = $this->post($urlv4, $paramsv4, null);
		$getResultV2 = $this->getcontext($sendRequestV4);
		
		if(stripos($sendRequestV4, "This field is required") !== false) {
			$url5 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.replay_challenge/';
			$paramsv5 = 'choice=1&is_bloks_web=False&skip=0&contact_point='.urlencode($email).'&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getResult.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
			$sendRequest5 = $this->post($url5, $paramsv5, null);
			$sendResponse = '{"Status": "fail", "Message": "We cannot send a code to your email address. Please come in 10 minutes", "Email": "'.$email.'", "Context": "'.$getResultV2.'", "Suphi": "Correct Username and Password but suspicious login detected. Code not sent. The user was told to come in 10 minutes."}';
		}else{
		$sendResponse = '{"Status": "fail", "Message": "Please enter the code sent to you.", "Email": "'.$email.'", "Context": "'.$getResultV2.'", "Suphi": "Correct username and password but suspicious login detected. Code sent"}';	
		}		
			}else{
		
		$urlv4 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.take_challenge/';
		$paramsv4 = 'choice=1&has_follow_up_screens=0&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getResult.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequestV4 = $this->post($urlv4, $paramsv4, null);
		$getResultV2 = $this->getcontext($sendRequestV4);
		
		if(stripos($sendRequestV4, "This field is required") !== false) {
			$url5 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.replay_challenge/';
			$paramsv5 = 'choice=1&is_bloks_web=False&skip=0&contact_point='.urlencode($email).'&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getResult.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
			$sendRequest5 = $this->post($url5, $paramsv5, null);
			$sendResponse = '{"Status": "fail", "Message": "We cannot send a code to your email address. Please come in 10 minutes", "Email": "'.$email.'", "Context": "'.$getResultV2.'", "Suphi": "Correct Username and Password but suspicious login detected. Code not sent. The user was told to come in 10 minutes."}';
		}else{
				$url5 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.rewind_challenge/';
			$paramsv5 = 'is_bloks_web=False&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getResult.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
			$sendRequest5 = $this->post($url5, $paramsv5, null);
			$getres2 = $this->getcontext($sendRequest5);
			$url6 = $this->apibase.'/api/v1/bloks/apps/com.instagram.challenge.navigation.rewind_challenge/';
			$paramsv6 = 'choice=1&is_bloks_web=False&has_follow_up_screens=0&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&challenge_context='.$getres2.'&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
			$sendreq7 = $this->post($url6, $paramsv6, null);
			$getres7 = $this->getcontext($sendreq7);
		$sendResponse = '{"Status": "fail", "Message": "Please enter the code sent to you.", "Email": "'.$email.'", "Context": "'.$getres7.'", "Suphi": "Correct username and password but suspicious login detected.Code sent"}';	
		}
	}
	}else{
		$sendResponse = '{"Status": "fail", "Message": "Incorrect password or username, please double-check your informations", "Suphi": "Incorrect useranme or password"}';
	}
	return $sendResponse;
	}	
	



	public function login($username = null, $password = null){
		$uid = uniqid();
		
		$username = $username ?? $this->username;
		$password = ($password=== null) ? $this->GenrateENCPassword($this->password) : $this->GenrateENCPassword($password);
		$sendheader = array(
		"uid" => $uid,
		"token" => null,
		);
		$url = 'https://i.instagram.com/api/v1/bloks/apps/com.bloks.www.bloks.caa.login.async.send_login_request/';
		$params = 'params=%7B%22client_input_params%22%3A%7B%22password%22%3A%22'.urlencode($password).'%22%2C%22contact_point%22%3A%22'.$username.'%22%2C%22fb_ig_device_id%22%3A%5B%5D%2C%22event_flow%22%3A%22login_manual%22%2C%22openid_tokens%22%3A%7B%7D%2C%22machine_id%22%3A%22ZZYnEgABAAHIt7pyhanAPrtKlp6L%22%2C%22family_device_id%22%3A%22b616617b-2152-40a7-a4b0-45165de4b455%22%2C%22accounts_list%22%3A%5B%7B%22metadata%22%3A%7B%22device_base_login_session%22%3A%22Bearer+IGT%3A2%3AeyJkc191c2VyX2lkIjoiMjQ2NjAwODgxNTIiLCJzZXNzaW9uaWQiOiIyNDY2MDA4ODE1MiUzQWVZdmdlVHVFNFA5MHhUJTNBMTQlM0FBWWY1M0dadVZTMXVCVkJvUlE5Ui1rbkxJNEstbDNUZHdKYlpHNWdsSncifQ%3D%3D%22%2C%22big_blue_token%22%3Anull%7D%2C%22account_type%22%3A%22nonce%22%2C%22token%22%3A%22Z5vCGPiu2mISnTVq7eSHBWzfnTC2PKrVXRhJwseAe12uS3HmbAxGvsqCzherg8Et%22%2C%22uid%22%3A%2224660088152%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%226184140674%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%226187633439%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%227397592855%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%2235892726440%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%2262573070257%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%2263730780111%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%2264127482655%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%2232076210833%22%2C%22credential_type%22%3A%22none%22%7D%2C%7B%22token%22%3A%22%22%2C%22uid%22%3A%2224660088152%22%2C%22credential_type%22%3A%22none%22%7D%5D%2C%22try_num%22%3A1%2C%22has_whatsapp_installed%22%3A1%2C%22login_attempt_count%22%3A1%2C%22device_id%22%3A%22android-80dbc8a0c0ab2a40%22%2C%22headers_infra_flow_id%22%3A%22%22%2C%22auth_secure_device_id%22%3A%22%22%2C%22encrypted_msisdn%22%3A%22%22%2C%22sso_token_map_json_string%22%3A%22%7B%5C%226184140674%5C%22%3A%5B%5D%2C%5C%226187633439%5C%22%3A%5B%5D%2C%5C%227397592855%5C%22%3A%5B%5D%2C%5C%2235892726440%5C%22%3A%5B%5D%2C%5C%2262573070257%5C%22%3A%5B%5D%2C%5C%2263730780111%5C%22%3A%5B%5D%2C%5C%2264127482655%5C%22%3A%5B%5D%2C%5C%2232076210833%5C%22%3A%5B%5D%2C%5C%2224660088152%5C%22%3A%5B%7B%5C%22credential_type%5C%22%3A%5C%22local_auth%5C%22%2C%5C%22token%5C%22%3A%5C%22Bearer+IGT%3A2%3AeyJkc191c2VyX2lkIjoiMjQ2NjAwODgxNTIiLCJzZXNzaW9uaWQiOiIyNDY2MDA4ODE1MiUzQWVZdmdlVHVFNFA5MHhUJTNBMTQlM0FBWWY1M0dadVZTMXVCVkJvUlE5Ui1rbkxJNEstbDNUZHdKYlpHNWdsSncifQ%3D%3D%5C%22%7D%2C%7B%5C%22credential_type%5C%22%3A%5C%22nonce%5C%22%2C%5C%22token%5C%22%3A%5C%22Z5vCGPiu2mISnTVq7eSHBWzfnTC2PKrVXRhJwseAe12uS3HmbAxGvsqCzherg8Et%5C%22%7D%5D%7D%22%2C%22device_emails%22%3A%5B%5D%2C%22client_known_key_hash%22%3A%22%22%2C%22event_step%22%3A%22home_page%22%2C%22secure_family_device_id%22%3A%22%22%7D%2C%22server_params%22%3A%7B%22is_caa_perf_enabled%22%3A1%2C%22is_platform_login%22%3A0%2C%22is_from_logged_out%22%3A0%2C%22login_credential_type%22%3A%22none%22%2C%22qe_device_id%22%3A%22ad9b4188-2663-435c-bfcf-fc22e029283c%22%2C%22should_trigger_override_login_2fa_action%22%3A0%2C%22is_from_logged_in_switcher%22%3A0%2C%22family_device_id%22%3A%22b616617b-2152-40a7-a4b0-45165de4b455%22%2C%22reg_flow_source%22%3A%22aymh_multi_profiles_native_integration_point%22%2C%22credential_type%22%3A%22password%22%2C%22waterfall_id%22%3A%22f01b0d24-7081-4aa7-bad5-ad32666527d7%22%2C%22username_text_input_id%22%3A%22lhwdso%3A53%22%2C%22password_text_input_id%22%3A%22lhwdso%3A54%22%2C%22offline_experiment_group%22%3A%22caa_iteration_v3_perf_ig_4%22%2C%22INTERNAL_INFRA_THEME%22%3A%22default%22%2C%22INTERNAL__latency_qpl_instance_id%22%3A129985404000232%2C%22device_id%22%3A%22android-80dbc8a0c0ab2a40%22%2C%22server_login_source%22%3A%22login%22%2C%22login_source%22%3A%22Login%22%2C%22caller%22%3A%22gslr%22%2C%22lois_settings%22%3A%7B%22lara_override%22%3A%22%22%2C%22lois_token%22%3A%22%22%7D%2C%22should_trigger_override_login_success_action%22%3A0%2C%22ar_event_source%22%3A%22login_home_page%22%2C%22INTERNAL__latency_qpl_marker_id%22%3A36707139%7D%7D&bk_client_context=%7B%22bloks_version%22%3A%228ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb%22%2C%22styles_id%22%3A%22instagram%22%7D&bloks_versioning_id=8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb';
		$sendRequest = $this->post($url, $params, $sendheader);
		
		//$debug = var_dump($sendRequest);
		if(strpos($sendRequest, "bk.action.caa.HandleLoginResponse")){
			$bearerToken = $this->FindBearerToken($sendRequest);
			$this->MyCache("token", $bearerToken);
				$sendResponse = '{"Status": "ok", "Finish": true, "Message": "Successfuly logged", "Suphi": "Correct Username and Password - No 2 Factor", "Bearer": "'.$bearerToken.'"}';
				return $sendResponse;
			}else if(strpos($sendRequest, "ig.action.cdsdialog.OpenDialog")){
				$sendResponse = '{"Status": "fail", "Finish": false, "Message": "Incorrect username or password , please double-check your informations", "Suphi": "Incorrect Username or Password"}';	
				return $sendResponse;	
			}else if(strpos($sendRequest, "bk.action.bloks.GetVariable2")){
				$sendResponse = '{"Status": "ok", "Finish": false, "Message": "Please Enter 2 Factor Codes", "Suphi": "Correct Username and password - yes 2 factor"}';
				return $sendResponse;
			}else{
				$sendResponse = '{"Status": "fail", "Finish": false, "Message": "Somthing went wrong , plase try again in 10 minutes", "Suphi": "Error - Maybe IP Blocked"}';	
				return $sendResponse;
			}
			
}



	
	
}