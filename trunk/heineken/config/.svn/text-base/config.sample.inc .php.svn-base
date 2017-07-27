<?php
@include_once "locale.inc.php";

$CONFIG['LOG_DIR'] = "../logs/";
$GLOBAL_PATH = "../";
$APP_PATH = "../com/";
$ENGINE_PATH = "../engines/";
$WEBROOT = "../public_html/";

error_reporting(E_ALL);
//set aplikasi yang digunakan
define('APPLICATION','application');
define('COORPORATE_APPS','coorporate_apps');
define('MOBILE_APPS','mobile');
define('WAP_APPS','wap_apps'); 
define('DASHBOARD_APPS','dashboard'); 

define('WIDGET_DOMAIN_WEB',APPLICATION."/widgets/");
define('WIDGET_DOMAIN_COORPORATE',COORPORATE_APPS."/widgets/");
define('WIDGET_DOMAIN_MOBILE',MOBILE_APPS."/widgets/");
define('WIDGET_DOMAIN_WAP',WAP_APPS."/widgets/"); //new
define('WIDGET_DOMAIN_DASHBOARD',DASHBOARD_APPS."/widgets/"); //new

define('HELPER_DOMAIN_WEB',APPLICATION."/helper/");
define('HELPER_DOMAIN_COORPORATE',COORPORATE_APPS."/helper/");
define('HELPER_DOMAIN_MOBILE',MOBILE_APPS."/helper/");
define('HELPER_DOMAIN_WAP',WAP_APPS."/helper/"); //new
define('HELPER_DOMAIN_DASHBOARD',DASHBOARD_APPS."/helper/"); //new

define('MODULES_DOMAIN_WEB',$APP_PATH.APPLICATION."/modules/");
define('MODULES_DOMAIN_COORPORATE',$APP_PATH.COORPORATE_APPS."/modules/");
define('MODULES_DOMAIN_MOBILE',$APP_PATH.MOBILE_APPS."/modules/");
define('MODULES_DOMAIN_WAP',$APP_PATH.WAP_APPS."/modules/"); //new
define('MODULES_DOMAIN_DASHBOARD',$APP_PATH.DASHBOARD_APPS."/modules/"); //new

define('TEMPLATE_DOMAIN_WEB',APPLICATION."/web/");
define('TEMPLATE_DOMAIN_COORPORATE',APPLICATION."/coorporate/");
define('TEMPLATE_DOMAIN_MOBILE',APPLICATION."/mobile/");
define('TEMPLATE_DOMAIN_WAP',APPLICATION."/wap/"); //new
define('TEMPLATE_DOMAIN_DASHBOARD',APPLICATION."/dashboard/"); //new

define('SCHEMA_DATA','heineken_micro');
//set TRUE jika dalam local
$local = true;
$DEVELOPMENT_MODE = true;
$CONFIG['DEFAULT_MODULES'] = "home.php";
$CONFIG['VIEW_ON']  = 1;
$CONFIG['DINAMIC_MODULE']  = "home";
$CONFIG['REGISTER_PAGE']  = "register";
$CONFIG['LOCAL_DEVELOPMENT'] = true;
$CONFIG['DELAYTIME'] = 0;
//WEB APP BASE DOMAIN
// echo ("preview.kanadigital.com");
if(preg_match("/dev./i",$_SERVER['HTTP_HOST'])){
	$DOMAIN = "http://{$_SERVER['HTTP_HOST']}/";
	$PUBLIC_HTML = "";
}else{
	$DOMAIN = "http://{$_SERVER['HTTP_HOST']}/Heineken_Micro/";
	$PUBLIC_HTML = "public_html/";
}
$CONFIG['BASE_DOMAIN_PATH'] = "http://localhost/Heineken_Micro/public_html/";

$CONFIG['CLOSED_WEB'] = false;
$CONFIG['API_DOMAIN'] =  "{$DOMAIN}service/";
$CONFIG['MAINTENANCE'] = false;
$CONFIG['BASE_DOMAIN'] = "{$DOMAIN}{$PUBLIC_HTML}";
$CONFIG['DASHBOARD_DOMAIN'] = "{$DOMAIN}dashboard_html/";
$CONFIG['COORPORATE_DOMAIN'] = "{$DOMAIN}coorporate_html/";
$CONFIG['WAP_DOMAIN'] =  "{$DOMAIN}wap_html/"; //new
$CONFIG['Postpaid_OnlineRegistration'] = "{$DOMAIN}Postpaid_OnlineRegistration/";
$CONFIG['Prepaid_Registrations'] = "{$DOMAIN}Prepaid_Registrations/";

$CONFIG['ASSETS_DOMAIN_WEB'] = $CONFIG['BASE_DOMAIN']."assets/";
$CONFIG['ASSETS_DOMAIN_COORPORATE'] = $CONFIG['COORPORATE_DOMAIN']."assets/";
$CONFIG['ASSETS_DOMAIN_WAP'] = $CONFIG['WAP_DOMAIN']."assets/"; //new
$CONFIG['ASSETS_DOMAIN_DASHBOARD'] = $CONFIG['DASHBOARD_DOMAIN']."assets/"; //new

$CONFIG['PUBLIC_ASSET'] = "public_assets/";
$CONFIG['LOCAL_PUBLIC_ASSET'] = "/var/www/Heineken_Micro/public_html/public_assets/";

$CONFIG['LOCAL_ASSET'] = "/var/www/Heineken_Micro/public_html/assets/";
$CONFIG['AGE_CHECK']  = "{$DOMAIN}{$PUBLIC_HTML}login/age_check";
if($CONFIG['LOCAL_DEVELOPMENT']) $CONFIG['LOGIN_PAGE']  = "{$DOMAIN}{$PUBLIC_HTML}login/fb";
else  $CONFIG['LOGIN_PAGE']  = "{$DOMAIN}{$PUBLIC_HTML}login/fb"; 

$CONFIG['MOBILE_SITE'] =  "{$DOMAIN}mobile_html/";
$CONFIG['ASSETS_DOMAIN_MOBILE'] = $CONFIG['MOBILE_SITE']."assets/"; //new

$CONFIG['SESSION_NAME'] = "heineken_micro";

$CONFIG['MODERATION'] = 1;


/* allow access page on unverified */
$CONFIG['access-unverified'] = array("home");


//SOCIAL MEDIA
//FB
$FB['appID'] = "168991029969615";
$FB['appSecret'] = "3b189d9224ddd1adc2bf00357076fcae";
$FB['heinekenPageID']="7174672354";

/**
 * memcache setting
 */
 $CONFIG['memcache_host'] = "127.0.0.1";
 $CONFIG['memcache_port'] = 11211;




if($local){
	$CONFIG['DATABASE'][0]['HOST'] 		= "localhost";
	$CONFIG['DATABASE'][0]['USERNAME'] 	= "root";
	$CONFIG['DATABASE'][0]['PASSWORD'] 	= "coppermine";
	$CONFIG['DATABASE'][0]['DATABASE'] 	= "heineken_micro";
	

}else{
	$CONFIG['DATABASE'][0]['HOST'] 		= "117.54.1.99";
	$CONFIG['DATABASE'][0]['USERNAME'] 	= "amild";
	$CONFIG['DATABASE'][0]['PASSWORD'] 	= "m1ldl1ght*";
	$CONFIG['DATABASE'][0]['DATABASE'] 	= "amild_athreesix_web_2013";
	

}

$SMAC_SECRET = sha1("harveyspecterssuits");
$SMAC_HASH = sha1("mikerosssuits");

$CONFIG['SERVICE_URL'] = "service/";
$CONFIG['salt'] = '12345678';
/* DATETIME SET */
$timeZone = 'Asia/Jakarta';
date_default_timezone_set($timeZone);


$CONFIG['SERVICE_KEY'] = sha1("axis2012");

?>
