<?php
namespace SuphiGram\Instagram;
class InstagramRequest{
	
	public $apibase   = 'https://i.instagram.com';
	public $apibase2  = 'https://www.instagram.com';
	public $proxy = null;
	public $proxyus = null;
	

public function post($url, $data, $header = null){ 
$uid = uniqid();
$headers = $this->getHeaders(null, $uid, $data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
if ($this->proxyus !== null && $this->proxyus !== null) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyus);
    }

    if ($this->proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
return $response;
}


public function postforlogin($url, $data) {
    $uid = uniqid();
    $headers = $this->getHeaders(null, $uid, $data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	
	if ($this->proxyus !== null && $this->proxyus !== null) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyus);
    }

    if ($this->proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
	curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);    

$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$header = substr($response, 0, $header_size);
$body = substr($response, $header_size);

$authorizationHeader = '';

$headers = explode("\r\n", $header);
foreach ($headers as $header) {
    if (strpos($header, 'ig-set-authorization: Bearer') === 0) {
        $authorizationHeader = trim(substr($header, strlen('ig-set-authorization: Bearer')));
		$this->MyCache("token", $authorizationHeader);
        break;
    }
}

    curl_close($ch);
    
    return $body;
}



public function postwithcookie($url, $data, $header = null){ 
$uid = uniqid();
$token = $header ?? $this->MyCache("token");
$headers = $this->Headerv2($token, $uid, $data);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
if ($this->proxyus !== null && $this->proxyus !== null) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyus);
    }

    if ($this->proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
return $response;
}


public function getwithcookie($url, $header = null){ 
$uid = uniqid();
$token = $header ?? $this->MyCache("token");
$headers = $this->Headerv2($token, $uid, null);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
if ($this->proxyus !== null && $this->proxyus !== null) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyus);
    }

    if ($this->proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
return $response;
}



public function get($url, $data = null, $header = null){ 
    $headers = $this->getHeaders();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
if ($this->proxyus !== null && $this->proxyus !== null) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyus);
    }

    if ($this->proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

public function getforchallenge($url, $data = null, $header = null){ 
    $headers = $this->getHeadersForChallenge();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
if ($this->proxyus !== null && $this->proxyus !== null) {
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxyus);
    }

    if ($this->proxy !== null) {
        curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
	
	
//------------------------ Cache System --------------------------
public function MyCache($key, $value = null) {
    $cacheFile = './Cache/cache.json';
	if (!file_exists($cacheFile)) {
    touch($cacheFile);
    chmod($cacheFile, 0777); // İzinleri uygun bir şekilde ayarlayın
}
    $cacheData = json_decode(file_get_contents($cacheFile), true);
    if (is_null($value)) {
        return isset($cacheData[$key]) ? $cacheData[$key] : null;
    }

    $cacheData[$key] = $value;
    file_put_contents($cacheFile, json_encode($cacheData, JSON_PRETTY_PRINT));
    return $value;
}
	
	
	
	
//----------------------------Header vs vs --------------------------------
	
	
	
public function getHeadersForChallenge(){
	$headers = [
    'Host: i.instagram.com',
    'X-Ig-App-Locale: tr_TR',
    'X-Ig-Device-Locale: tr_TR',
    'X-Ig-Mapped-Locale: tr_TR',
    'X-Pigeon-Session-Id: UFS-491781e9-fa12-45ac-aa9b-60f7f49a2693-5',
    'X-Pigeon-Rawclienttime: 1705181952.884',
    'X-Ig-Bandwidth-Speed-Kbps: 18844.000',
    'X-Ig-Bandwidth-Totalbytes-B: 79573386',
    'X-Ig-Bandwidth-Totaltime-Ms: 8367',
    'X-Bloks-Version-Id: 8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb',
    'X-Ig-Www-Claim: 0',
    'X-Bloks-Is-Layout-Rtl: false',
    'X-Ig-Device-Id: ad9b4188-2663-435c-bfcf-fc22e029283c',
    'X-Ig-Family-Device-Id: b616617b-2152-40a7-a4b0-45165de4b455',
    'X-Ig-Android-Id: android-80dbc8a0c0ab2a40',
    'X-Ig-Timezone-Offset: 10800',
    'X-Ig-Nav-Chain: SelfFragment:self_profile:1:cold_start:1705175407.930::,AccountSwitchFragment:account_switch_fragment:2:button:1705175409.781::,AddAccountBottomSheetFragment:add_account_bottom_sheet:3:button:1705175410.855::,TRUNCATEDx1,bloks_unknown_class:select_verification_method:5:button:1705175454.456::,bloks_unknown_class:verify_email_code:6:button:1705176043.360::,bloks_unknown_class:select_verification_method:7:button:1705176461.493::,LoginLandingFragment:login_landing:8:button:1705176461.832::,bloks_unknown_class:select_verification_method:10:button:1705176533.975::,bloks_unknown_class:verify_email_code:11:button:1705176632.276::,LoginLandingFragment:login_landing:12:button:1705181938.199::',
    'X-Fb-Connection-Type: WIFI',
    'X-Ig-Connection-Type: WIFI',
    'X-Ig-Capabilities: 3brTv10=',
    'X-Ig-App-Id: 567067343352427',
    'Priority: u=3',
    'User-Agent: Instagram 275.0.0.27.98 Android (25/7.1.2; 320dpi; 900x1600; samsung; SM-N976N; d2q; qcom; tr_TR; 458229257)',
    'Accept-Language: tr-TR, en-US',
    'X-Mid: ZZYnEgABAAHIt7pyhanAPrtKlp6L',
    'Ig-Intended-User-Id: 0',
    'Accept-Encoding: identity',
    'X-Fb-Http-Engine: Liger',
    'X-Fb-Client-Ip: True',
    'X-Fb-Server-Cluster: True',
];
return $headers;
}
	
	
	
	
	
	
	
	
	
	
public function getHeaders($token = null, $uid = null, $data = null) {
    $headers = [
        'Host: i.instagram.com',
        'X-Ig-App-Locale: tr_TR',
        'X-Ig-Device-Locale: tr_TR',
        'X-Ig-Mapped-Locale: tr_TR',
        'X-Pigeon-Session-Id: UFS-356db362-be8c-45af-94ae-bd5d3fd8925e-1',
        'X-Pigeon-Rawclienttime: ' . microtime(true),
        'X-Ig-Bandwidth-Speed-Kbps: 899.000',
        'X-Ig-Bandwidth-Totalbytes-B: 271587',
        'X-Ig-Bandwidth-Totaltime-Ms: 302',
        'X-Bloks-Version-Id: 8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb',
        'X-Ig-Www-Claim: 0',
        'X-Bloks-Is-Layout-Rtl: false',
        'X-Ig-Device-Id: ad9b4188-2663-435c-bfcf-fc22e029283c',
        'X-Ig-Family-Device-Id: b616617b-2152-40a7-a4b0-45165de4b455',
        'X-Ig-Android-Id: android-80dbc8a0c0ab2a40',
        'X-Ig-Timezone-Offset: 10800',
        'X-Ig-Nav-Chain: SelfFragment:self_profile:6:main_profile:1704963606.991::,ProfileMenuFragment:bottom_sheet_profile:7:button:1704963610.162::,UserOptionsFragment:settings_category_options:8:button:1704963611.305::,CaaLoginOneTapLogOutFragment:caa_login_one_tap_log_out_fragment:9:button:1704963614.31::',
        'X-Fb-Connection-Type: WIFI',
        'X-Ig-Connection-Type: WIFI',
        'X-Ig-Capabilities: 3brTv10=',
        'X-Ig-App-Id: 567067343352427',
        'Priority: u=3',
        'User-Agent: Instagram 275.0.0.27.98 Android (25/7.1.2; 320dpi; 900x1600; samsung; SM-N976N; d2q; qcom; tr_TR; 458229257)',
        'Accept-Language: tr-TR, en-US',
        'X-Mid: ZZYnEgABAAHIt7pyhanAPrtKlp6L',
        'Ig-Intended-User-Id: 0',
        'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
        'Content-Length: ' . (is_null($data) ? 0 : strlen($data)),
        'Accept-Encoding: identity',
        'X-Fb-Http-Engine: Liger',
        'X-Fb-Client-Ip: True',
        'X-Fb-Server-Cluster: True',
		'X-Forwarded-For: 37.120.207.190',
    ];

    if (!is_null($token)) {
        $headers[] = 'Authorization: Bearer' . $token;
    }

    return $headers;
}

	
	
	
public function Headerv2($token = null, $uid = null, $data = null) {

$dokuz = rand(100000000, 999999999);
   $headers = [
    'Host: i.instagram.com',
    'X-Ig-App-Locale: tr_TR',
    'X-Ig-Device-Locale: tr_TR',
    'X-Ig-Mapped-Locale: tr_TR',
    'X-Pigeon-Session-Id: UFS-356db362-be8c-45af-94ae-bd5d3fd8925e-1',
    'X-Pigeon-Rawclienttime: ' . microtime(true),
    'X-Ig-Bandwidth-Speed-Kbps: 899.000',
    'X-Ig-Bandwidth-Totalbytes-B: 271587',
    'X-Ig-Bandwidth-Totaltime-Ms: 302',
    'X-Bloks-Version-Id: 8ca96ca267e30c02cf90888d91eeff09627f0e3fd2bd9df472278c9a6c022cbb',
    'X-Ig-Www-Claim: hmac.AR25O9AXw1mAdVrBFBec1B15DDUbs1M9cDoZx_wCRpl9kFjE',
    'X-Bloks-Is-Layout-Rtl: false',
    'X-Ig-Device-Id: ad9b4188-2663-435c-bfcf-fc22e029283c',
    'X-Ig-Family-Device-Id: b616617b-2152-40a7-a4b0-45165de4b455',
    'X-Ig-Android-Id: android-80dbc8a0c0ab2a40',
    'X-Ig-Timezone-Offset: 10800',
    'X-Ig-Nav-Chain: SelfFragment:self_profile:2:main_profile:1704967840.976::,ProfileMenuFragment:bottom_sheet_profile:3:button:1704967843.900::,UserOptionsFragment:settings_category_options:4:button:1704967845.199::',
    'X-Fb-Connection-Type: WIFI',
    'X-Ig-Connection-Type: WIFI',
    'X-Ig-Capabilities: 3brTv10=',
    'X-Ig-App-Id: 567067343352427',
    'Priority: u=3',
    'User-Agent: Instagram 275.0.0.27.98 Android (25/7.1.2; 320dpi; 900x1600; samsung; SM-N976N; d2q; qcom; tr_TR; '.$dokuz.')',
    'Accept-Language: tr-TR, en-US',
    'Authorization: Bearer '.$token,
    'X-Mid: ZZYnEgABAAHIt7pyhanAPrtKlp6L',
    'Ig-U-Shbid: 4621,3106778656,1736946745:01f761dd0ddb98b301b3fa99c2356ae1f3f69765828d13cf48eb5eda7d0acc83c157c9ad',
	'Ig-U-Shbts: 1705410745,3106778656,1736946745:01f70eb364ca614e141775ab9cd4d034e530ab65fdd9606b459a784c84f8699a2af25112',
    'Ig-U-Ds-User-Id: '.$this->MyCache("pk").'',
	'Ig-U-Rur: RVA,3106778656,1736946746:01f7b07cee5ad0976d95f98730cfadd798b394dd17299bf6946e3d0cc173d107b95f87a0',
    'Ig-Intended-User-Id: '.$this->MyCache("pk").'',
    'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
    'Content-Length: ' . (is_null($data) ? 0 : strlen($data)),
    'Accept-Encoding: identity',
    'X-Fb-Http-Engine: Liger',
    'X-Fb-Client-Ip: True',
    'X-Fb-Server-Cluster: True',
];

    return $headers;
}
	
	
	

	
}
