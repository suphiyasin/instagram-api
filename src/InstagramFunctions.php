<?php

namespace SuphiGram\Instagram;
class InstagramFunctions extends InstagramRequest{
	
	// --------------------------------- Şifreleme Grubu ----------------------------------------
		public function GenrateENCPassword($password = null){
	$password === null ? $this->password : $password;
	$sendResponse = '#PWD_INSTAGRAM_BROWSER:0:' . time() . ':' . $password;
	return $sendResponse;		
			
		}
		
		public function test(){

	}
		
		
		
				
				
				
	
	
	
	
	
	
	
}