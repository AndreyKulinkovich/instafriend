<?php
//https://github.com/postaddictme/instagram-php-scraper/tree/master/examples
/*
global $path;
global $tg_admin;
$path = explode('kozyon.com', __FILE__)[0].'kozyon.com';
require_once($path.'/crm/php/functions.php');
my_timezone();*/
function accessProtected($obj, $prop) {
  $reflection = new ReflectionClass($obj);
  $property = $reflection->getProperty($prop);
  $property->setAccessible(true);
  return $property->getValue($obj);
}
$path = explode('kozyon.com', __FILE__)[0].'kozyon.com';
$input = file_get_contents('php://input');
$input = json_decode($input,1);
$username = (isset($input['username'])&&$input['username']?$input['username']:'');
if(!$username){echo json_encode(array('error'=>"Нет username"),JSON_UNESCAPED_UNICODE);return false;}
use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\Config\Config;

require __DIR__ . '/vendor/autoload.php';

require_once($path.'/crm/php/functions.php');
sql_openconnect();

$config = new Config();
$config->setDefaultTtl(86400);

$instagram = \InstagramScraper\Instagram::withCredentials(new \GuzzleHttp\Client(), 'iamagamedeveloper', 'lmtlop90Ilog', new Psr16Adapter('Files'));
$instagram->setUserAgent('User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.141 Safari/537.36');
	//$array = (array) $instagram;
	//print_r($array);
$instagram->login();
	//$instagram->login(false, true);
$instagram->saveSession(86400);

$username = $username;
$followers = [];
$account = $instagram->getAccount($username);

if(accessProtected($account, 'username')){
	$fullinfo = array();
	$fullinfo['u'] = accessProtected($account, 'username');
	$fullinfo['d'] = accessProtected($account, 'fullName');
	$fullinfo['a'] = accessProtected($account, 'profilePicUrlHd');
	$fullinfo['f'] = accessProtected($account, 'followedByCount');
	$fullinfo['t'] = time();

	$medias = accessProtected($account, 'medias');
	$medurl = array();
	foreach ($medias as $key => $value) {
		$url = accessProtected($value, 'imageThumbnailUrl');
		$medurl[] = $url;
	}
	$fullinfo['m'] = $medurl;
} else {
	$fullinfo = array('error'=>'Что-то пошло не так');
}

echo json_encode($fullinfo,JSON_UNESCAPED_UNICODE);

?>