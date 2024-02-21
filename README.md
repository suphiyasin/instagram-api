[![Contributors][contributors-shield]][contributors-url]
[![Forks][forks-shield]][forks-url]
[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![MIT License][license-shield]][license-url]
[![Hits](https://hits.seeyoufarm.com/api/count/incr/badge.svg?url=https://github.com/suphiyasin/instagram-api&count_bg=%23C83D3D&title_bg=%23057386&icon=&icon_color=%23BA0808&title=View&edge_flat=false)](https://github.com/suphiyasin/instagram)

<!-- PROJECT LOGO -->
<br />
<p align="center">
<a href="https://github.com/suphiyasin/instagram-api/">
<img src="https://cdn.cdnlogo.com/logos/i/4/instagram.svg" alt="Logo" width="80" height="80" />
</a>

<h3 align="center">Suphiyasin / Instagram-API</h3>

<p align="center">
    With this PHP library, you can use all features of the instagram Mobile App
    <br />
    <a href="https://github.com/suphiyasin/instagram-api/issues">Feedback</a>

  
  
</p>

## Donation

| Coin | Network | Wallet |
| ------------- | ------------- | ------------- |
| USDT | TRC-20 |  TRsWGn75MPwMgKaEuETPqB4P67e6w9L9JT |

# Packet Features

| Feature                                | Instagram Mobil API                     | 
|----------------------------------------|-----------------------------------|
| Use Proxy                              | :heavy_check_mark:                | 
| Login                                  | :heavy_check_mark:                |
| Two Factor Login                       | :heavy_check_mark:<br/>(Email - DUO Tested) |
| Suspicious Login Code                  | :heavy_check_mark:<br/>(If you bypass the suspicious login but add double factor to the account, account owner verification will be required.The suspicious login code and the account ownership code are not the same.) |
| Add Duo                                | :heavy_check_mark:                |
| Change Password                        | :heavy_check_mark:                |
| Change Email                           | :heavy_check_mark:                |
| Duo Remove                             | :heavy_check_mark:                |
| Get My Inbox                           | :heavy_check_mark:                 |
| Send Message                           | :heavy_check_mark:                 |
| Get User Info                          | :heavy_check_mark:                 |
| Info my account                        | :heavy_check_mark:                 |
| Phone Remove                           | :heavy_check_mark:                 |
| Email Remove                           | :heavy_check_mark:                 |
| Change Birthday                        | :heavy_check_mark:                 |
| Add Phone                              | :heavy_check_mark:<br/> (SMS Verification Needed) |
| See Duo Backup Codes                   | :heavy_check_mark:                 |
| Upload new profile photo               | :heavy_check_mark:                 |
| Like a post                            | :heavy_check_mark:                 |
| Unlike a post                          | :heavy_check_mark:                 |
| Save a post                            | :heavy_check_mark:                 |
| Follow                                 | :heavy_check_mark:                 |
| Unfollow                               | :heavy_check_mark:                 |
| Get Follows                            | :heavy_check_mark:<br/>(Searchable)|
| Get Followers                          | :heavy_check_mark:<br/>(Searchable)|
| Create Note                            | :heavy_check_mark:                 |
| Delete Note                            | :heavy_check_mark:                 |
| Get Comments                           | :heavy_check_mark:                 |
| Create Comment                         | :heavy_check_mark:                 |
| Delete Comment                         | :heavy_check_mark:                 |
| Logout                                 | :heavy_check_mark:                 |
| Delete Media                           | :heavy_check_mark:                 |
| Get Hashtag                            | :heavy_check_mark:                 |
| Get Recommended Users                  | :heavy_check_mark:                 |
| Search                                 | :heavy_check_mark:                 |
| Get users feed                         | :heavy_check_mark:                 |
| Get users story                        | :heavy_check_mark:                 |
| Like a post                            | :heavy_check_mark:                 |
| Get Likers                             | :heavy_check_mark:                 |
| Get code from duo seed function        | :lock:<br/>(telegram: @suphi007)      |  

| Feature                                | Instagram Web API                     | 
|----------------------------------------|-----------------------------------|
| Use Proxy                              | :heavy_check_mark:                | 
| Login                                  | :heavy_check_mark:                | 
| Login with 2 factor                    | :heavy_check_mark:                | 
| Get User info from username            | :heavy_check_mark:                | 
| Get Recommended Users                  | :heavy_check_mark:                | 



<!-- TABLE OF CONTENTS -->
<details open="open">
  <summary><h2 style="display: inline-block">Contents</h2></summary>
  <ol>
    <li>
      <a href="#about-project">About Project</a>
    </li>
    <li>
      <a href="#get-started">Get Started</a>
      <ul>
        <li><a href="#requirements">Requirements</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#road-map">Road Map</a></li>
    <li><a href="#contributors">Contributors</a></li>
    <li><a href="#license">License</a></li>
    <li><a href="#contact-us">Contant Us</a></li>
  </ol>
</details>

## About Project

This project is made in PHP library of all instagram mobile app features. This
library can send exactly same queries like mobile app
and returns server responses.

<!-- GETTING STARTED -->

## Updates
Added post save


## Getting Started

Please read carefully.

### Requirements

- You must have to "composer" application on your PC. For
  installation  https://getcomposer.org/download/
- PHP 7.4 or above

### File permissions

Give permission to the following files and folders with chmod 777.

`/vendor/suphiyasin/`

## Setup via Composer

* you must determine your root(working) folder after that open console (
  terminal )
  ```sh
  composer require suphigram/instagram
  ```

## Installing via download Repository

1. Firsty download repository
   ```sh
   git clone https://github.com/suphiyasin/Instagram.git
   ```
2. Use the command below to download the required libraries.
   ```sh
   composer install
   ```

<!-- USAGE EXAMPLES -->

## Examples

# Login

You must login before each operation. In your first login operation, the system
will be cached and your operation will run faster.

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use SuphiGram\Instagram\Instagram;

$api = new Instagram();

$username = 'username';
$password = 'password';

$login = json_decode($api->login->loginv2($username, $password), true);
if ($login["Status"] == "ok") {
    echo 'Login success';
} else {
    echo 'Login Fail';
}

// LOGIN CONTROL
$login_control = $api->login->MyCache("token"); // Fix the variable name to use $api instead of $instagram
if (strlen($login_control) > 0) {
    echo 'Token exists'; // Correct the typo in the echo statement
} else {
    echo 'Token does not exist'; // Correct the typo in the echo statement
}

```

# Two factor authorization

In your first login attemp, if two factor authorization are enabled, instagram
will send you a code. If you enter the code into the input area, yout login
operation will be completed automatically.
After your next logins, if yout IP is not changed, you can login without asking
code.

```php
<?php

    require __DIR__ . '/vendor/autoload.php';
    
   use SuphiGram\Instagram\Instagram;
    
    $api = new Instagram();
    $username = 'username';
    $password = 'password';
if(isset($_GET['factorcode'])){
$twoid = $_GET['twoid'];
	//1= Sms Verifivation || 2 = backup codes || 3 = Duo app code || 4= I guess its whatsapp 
	$step3 = json_decode($api->login->twofactorv2($username, $twofid, $_GET["factorcode"], "3"), true);
if($step3["status"] == "ok"){
echo "login success";
}else{
echo "login fail";
}
}else{
$step1 = json_decode($api->login->loginv2($username, $password), true);
if($step1["Status"] == "ok"){
echo "already logged";
}else if($step1["Status"] == "fail" and isset($step1["TwoFactorId"])){
	$twofid = $step1["TwoFactorId"];
	echo '<form action="" method="GET"><input type="hidden" value="'.$step1["TwoFactorId"].'" name="twoid"/><input type="number" name="factorcode" palceholder="Enter the 6 digit code" /> <input type="submit"/></form>';
	
}else{
//somthing went wrong
	var_dump($step1);
}
}

    

```

# Change everything in the account

When you run the code, it does the username, password, email, removing double factor on the account, and then adding double factor.

```php

//add duo factor
$removeduo = $api->user->disableFactorV2();
$step1 = json_decode($api->user->add2factorv2(null), true);
var_dump($step1);
if($step1["Status"] == "ok"){
	$step2 = $api->manuel->get2FACode($step1["Seed"]);
	echo $api->user->enable2Factor($step2);
}else{
	echo "Seed Genaration Failed";
}

//remove old email and add new email
$step1 = json_decode($api->user->InfoMyAccount(), true);
$step2 = $api->user->deleteMyEmail(null, $step1["Email"]);
$step3 = $api->user->addNewEmail(null, "newemail@email.com");
$step4 = $api->user->ConfirmOPTEmail(null, "newemail@email.com", "123456");

//change password
$step1 = $api->user->ChangePassword("OLD-PASSWORD", "NEW-PASSSWORD", $username);

//change username
$step1 = $api->user->changeUsername(null, "NewUsername");


```

<!-- ROADMAP -->



## License

You can download and use it as long as this project is under development. If
used for other purposes
The person who wrote the codes is not responsible. By downloading and using this
project, you agree to this.

## Contact

Suphi<br/>
Website : [https://suphi.org](https://suphi.org) <br/>
Email: yasin@suphi.org <br/>
Telegram: [@suphi007](https://t.me/suphi007) <br/>

[contributors-shield]: https://img.shields.io/github/contributors/suphiyasin/instagram-api.svg?style=for-the-badge

[contributors-url]: https://github.com/suphiyasin/instagram-api/graphs/contributors

[forks-shield]: https://img.shields.io/github/forks/suphiyasin/instagram-api.svg?style=for-the-badge

[forks-url]: https://github.com/suphiyasin/instagram-api/network/members

[stars-shield]: https://img.shields.io/github/stars/suphiyasin/instagram-api.svg?style=for-the-badge

[stars-url]: https://github.com/suphiyasin/instagram-api/stargazers

[issues-shield]: https://img.shields.io/github/issues/suphiyasin/instagram-api.svg?style=for-the-badge

[issues-url]: https://github.com/suphiyasin/instagram-api/issues

[license-shield]: https://img.shields.io/github/license/suphiyasin/instagram-api.svg?style=for-the-badge

[license-url]: https://github.com/suphiyasin/instagram-api/blob/main/LICENSE

[instagram-shield]: https://img.shields.io/badge/-Instagram-black.svg?style=for-the-badge&logo=Instagram&colorB=555
