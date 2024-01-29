<?php
/*
* Credits: Jesus Suarez & Gaston Della Valle
* Thanks to: https://license.co.id for their initial code
* Version: 1.0
* To Modulos Garnen: For allowing us to manage your software without a license
*
* How to use:
* Create a cron job that runs the file /whmcs.com/path.php
*/

##################### EDIT VARS #######################
$MODULE_NAME = "Lagom One Step Order Form For WHMCS"; #
$MODULE_MD5_VERSION = "1.2.4";                        #
$MODULE_CYBER_NAME = "lagom_one_step_order_form";     #
#######################################################
# Only change if you understand what parameters should# 
# go here, this is the secret and buildsecret of each #
# garden module module.                               #           
#######################################################
$SECRET = "0883ec58beaf14c0aab4f465dcd226dd";         # 
$BUILDSECRET = "657c44051ca55";                       #
#######################################################

class debug
{
	public function starttimer()
	{
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$starttime = $mtime;
		return $starttime;
	}

	public function endtimer()
	{
		global $starttime;
		$mtime = microtime();
		$mtime = explode(' ', $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		$totaltime = round($endtime - $this->starttimer(), 5);
		return $totaltime;
	}
}

class mysql
{
	private $db = null;
	private $query = null;
	private $result = null;
	private $row = null;
	private $debug = null;

	public function connect()
	{
		global $db_host;
		global $db_port;
		global $db_username;
		global $db_password;
		global $db_name;
		$this->db = mysqli_connect($db_host, $db_username, $db_password);

		if (!$this->db) {
			$this->fatalerror();
			return false;
		}
		else {
			mysqli_select_db($this->db, $db_name);
		}

		if (!mysqli_select_db($this->db, $db_name)) {
			$this->fatalerror();
			return false;
		}

		return true;
	}

	public function execute($query)
	{
		$this->connect();
		$this->query = $query;
		$this->result = mysqli_query($this->db, $this->query);
	}

	public function get_row()
	{
		if ($this->row = mysqli_fetch_array($this->result, MYSQLI_NUM)) {
			return $this->row;
		}
		else {
			return false;
		}
	}

	public function get_array()
	{
		if ($this->row = mysqli_fetch_array($this->result, MYSQLI_ASSOC)) {
			return $this->row;
		}
		else {
			return false;
		}
	}

	public function get_object()
	{
		if ($this->row = mysqli_fetch_object($this->result, MYSQLI_ASSOC)) {
			return $this->row;
		}
		else {
			return false;
		}
	}

	public function get_dataset()
	{
		$dataset = [];

		for ($i = 0; $qry = mysqli_fetch_row($this->result); ++$i) {
			$field = 0;

			for ($field = 0; $field < mysqli_num_fields($this->result); ++$field) {
				$dataset[$i][$field] = $qry[$field];
			}
		}

		return $dataset;
	}

	public function get_datarray()
	{
		$datarray = [];

		for ($i = 0; $data = mysqli_fetch_array($this->result); ++$i) {
			$datarray[$i] = $data;
		}

		return $datarray;
	}

	public function get_fetch_row()
	{
		if ($this->row = mysqli_fetch_row($this->result, MYSQL_ASSOC)) {
			return $this->row;
		}
		else {
			return false;
		}
	}

	public function get_num_rows()
	{
		$this->num_rows = mysqli_num_rows($this->result);
		return $this->num_rows;
	}

	public function close_connection()
	{
		mysqli_close($this->db);
	}

	public function get_mysql_id()
	{
		return mysqli_insert_id($this->db);
	}

	public function set_debug_mode($int = 0)
	{
		$this->obj['debug'] = (int) $int;

		if ($this->obj['debug']) {
			$this->obj['use_shutdown'] = 0;
		}
	}

	public function fatalerror($the_error = '')
	{
		$the_error .= "\n\n" . 'MySQL Server Error : ' . mysqli_error() . "\n";
		$the_error .= 'MySQL Server Error Code : ' . mysqli_errno() . "\n";
		$the_error .= 'Current Date : ' . date('l dS of F Y h:i:s A');
		$out = "\n\t\t" . '<html>' . "\n\t\t\t" . '<head>' . "\n\t\t\t" . '<title>Database Error' . "\n\t\t\t" . '<style>P,BODY,blockquote{ font-family: \'courier new\',\'trebuchet ms\',tahoma,verdana; font-size:12px; }</style>' . "\n\t\t\t" . '</head>' . "\n\t\t\t" . '<body>' . "\n\t\t\t\t" . '<br /><br />' . "\n\t\t\t" . '<blockquote>' . "\n\t\t\t\t" . '<b>There appears to be an error with the database.</b> | ' . "\n\t\t\t\t" . 'You can try to refresh the page by clicking <a href="javascript:window.location=window.location;">here</a>.' . "\n\t\t\t\t" . '<br /><br />' . "\n\t\t\t\t" . '<b style="font-family:\'Courier New\'; font-size:16px; font-style:normal;">Error Returned</b>' . "\n\t\t\t\t" . '<br />' . "\n\t\t\t\t" . '<form name=\'mysql\'>' . "\n\t\t\t\t" . '<textarea rows="10" cols="60" style="font-family:\'Courier New\' !important;font-size:12px;width:100%;">' . "\n\t\t\t\t" . htmlspecialchars($the_error) . "\n\t\t\t\t" . '</textarea>' . "\n\t\t\t\t" . '</form>' . "\n\t\t\t\t" . '<br />' . "\n\t\t\t\t" . 'We apologise for any inconvenience | Please Contact <b><a href="mailto:developer@cybernet.co.id">The Programer</a></b>' . "\n\t\t\t" . '</blockquote>' . "\n\t\t\t" . '</body>' . "\n\t\t" . '</html>';
		echo $out;
		exit('');
	}

	public function query($query, $contype = 'mysqli_query')
	{
		if ($this->obj['debug']) {
			global $debug;
			$debug->starttimer();
		}

		$this->queryid = $contype($query, $this->connection_id);

		if ($this->obj['debug']) {
			$endtime = $debug->endtimer();

			if (preg_match('/^select/i', $query)) {
				$eid = mysqli_query('EXPLAIN ' . $query . '', $this->connection_id);

				while ($array = mysqli_fetch_array($eid)) {
					echo "\n\t\t\t\t\t" . '<h3>sql debug</h3>' . "\n\t\t\t\t\t" . 'table: ' . $array['table'] . '<br />' . "\n\t\t\t\t\t" . 'query: ' . $query . '<br />' . "\n\t\t\t\t\t" . 'type: ' . $array['type'] . '<br />' . "\n\t\t\t\t\t" . 'mysql time : ' . $endtime . '<br /><br />' . "\n\t\t\t\t";
				}
			}
		}
		else {
			++$this->querycount;
			return $this->queryid;
		}
	}

	public function Random($arr)
	{
		return $arr[array_rand($arr)];
	}

	public function sql_select_tbl($tbl, $where = '', $order = '', $limit = '')
	{
		if ($where != '') {
			$rwhere = 'WHERE ' . $where;
		}
		else {
			$rwhere = '';
		}

		if ($order != '') {
			$rorder = 'ORDER BY ' . $order;
		}
		else {
			$rorder = '';
		}

		if ($limit != '') {
			$rlimit = 'LIMIT ' . $limit;
		}
		else {
			$rlimit = '';
		}

		$this->execute('SELECT * FROM `' . $tbl . '` ' . $rwhere . ' ' . $rorder . ' ' . $rlimit . '');
		$qry = $this->get_array();
		return $qry;
	}

	public function sql_update_tbl($db, $rl = '', $wh = '')
	{
		if ($rl != '') {
			$rule = 'SET ' . $rl;
		}
		else {
			$rule = '';
		}

		if ($wh != '') {
			$where = 'WHERE ' . $wh;
		}
		else {
			$where = '';
		}

		$this->execute('UPDATE `' . $db . '` ' . $rule . ' ' . $where . '');
	}

	public function sql_insert_tbl($db, $rl = '', $vl = '')
	{
		$this->execute('INSERT INTO `' . $db . '` ' . $rl . ' ' . $vl . '');
		return $this->get_mysql_id();
	}
}

class CyberSoftClass
{

	public function LicenseKey()
	{
		$modulename = $this->datalocalname();
		$file = $this->getdirectory() . '/license.php';
		$fileRename = $this->getdirectory() . '/license_RENAME.php';
		if (!file_exists($file) && file_exists($fileRename)) {
			exit($modulename . ': Unable to find ' . $file . ' file. Please rename file license_RENAME.php to license.php');
		}

		if (!file_exists($file)) {
			exit('Unable to find ' . $file . ' file.');
		}

		$keyName = $modulename . '_licensekey';
		$content = file_get_contents($file);
		$matches = [];
		preg_match('/' . $keyName . '\\s?=\\s?\\"([A-Za-z0-9_]+)\\"/', $content, $matches);
		$key = $matches[1];

		if (!$key) {
			exit('Invalid License Content');
		}

		return $key;
	}


	public function Secret()
	{
		$Secret = "0883ec58beaf14c0aab4f465dcd226dd";
		return $Secret;
	}

	public function BuildSecret()
	{
		$BuildSecret = "657c44051ca55";
		return $BuildSecret;
	}
	public function getDirectory()
	{
		return dirname(__FILE__);
	}

	public function DataLocalName()
	{
		return MODULE_CYBER_NAME;
	}

	public function DataLocalKey()
	{
		return $this->datalocalname() . '_localkey';
	}

	public function getIp()
	{
		return (isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']);
	}

	public function getWhmcsDomain()
	{
		global $CONFIG;
		global $MySQL;

		if (!empty($_SERVER['SERVER_NAME'])) {
			return $_SERVER['SERVER_NAME'];
		}

		$result = $MySQL->sql_select_tbl('tblconfiguration', 'setting=\'SystemURL\'');
		return parse_url($result['value'], PHP_URL_HOST);
	}

	public function CheckToken()
	{
		$check = time() . md5(mt_rand(100000000, mt_getrandmax()) . $this->licensekey());
		return $check;
	}

	public function UrlLastVerify()
	{
		$UrlLastVerify = "module-garden";
		return $UrlLastVerify;
	}

	public function UrlLastVerifySecret()
	{
		$UrlLastVerifySecret = "version";
		return $UrlLastVerifySecret;
	}

	public function ModuleVersion()
	{
		$mFile = 'moduleVersion.php';
		$mVer = '';

		if (file_exists($mFile)) {
			$content = file_get_contents($mFile);
			preg_match('/\\$moduleVersion\\s?=\\s?\'([A-Za-z0-9_\\.\\-]+)\'/', $content, $matches);
			$mVer = $matches[1];
		}

		return ($mVer ? $mVer : NULL);
	}

	public function ForceLicense($reset = '')
	{
		global $MySQL;
		$result = $MySQL->sql_select_tbl('tblconfiguration', 'setting=\'' . $this->datalocalkey() . '\'');

		if ($result['id'] == '') {
			$MySQL->sql_insert_tbl('tblconfiguration', '(`setting`,`value`,`created_at`,`updated_at`)', 'VALUES(\'' . $this->datalocalkey() . '\',\'\',\'' . date('Y-m-d H:i:s') . '\',\'' . date('Y-m-d H:i:s') . '\')');
			$localkey = '';
		}
		else if ($reset == 'reset') {
			$MySQL->sql_update_tbl('tblconfiguration', 'value=\'\', updated_at=\'' . date('Y-m-d H:i:s') . '\'', 'setting=\'' . $this->datalocalkey() . '\'');
			$localkey = '';
		}
		else {
			$localkey = $result['value'];
		}

		return $localkey;
	}

	public function GetIonCubeLoaderVersion()
	{
		ob_start();
		phpinfo(INFO_GENERAL);
		$aux = str_replace('&nbsp;', ' ', ob_get_clean());

		if ($aux !== false) {
			$pos = mb_stripos($aux, 'ionCube PHP Loader');

			if ($pos !== false) {
				$aux = mb_substr($aux, $pos + 18);
				$aux = mb_substr($aux, mb_stripos($aux, ' v') + 2);
				$version = '';
				$c = 0;
				$char = mb_substr($aux, $c++, 1);

				while (mb_strpos('0123456789.', $char) !== false) {
					$version .= $char;
					$char = mb_substr($aux, $c++, 1);
				}

				return $version;
			}
		}

		return '-';
	}

	public function GetMCryptExt()
	{
		if (extension_loaded('mcrypt') && function_exists('openssl_encrypt')) {
			$hasil = 'Installed';
		}
		else {
			$hasil = '-';
		}

		return $hasil;
	}

public function CheckLicense($licensekey, $localkey = '') {
	$Secret =  $this->Secret();
	$BuildSecret = $this->BuildSecret();
	$checkToken = sha1(time() . $licensekey . random_int(1000000000, PHP_INT_MAX));
	$randomString = uniqid(rand(), true);
	$md5Hash = md5($randomString);
	$ip_server = $_SERVER['SERVER_ADDR']; 
	$getdir = dirname(__FILE__);
   	$getdomain = $_SERVER['SERVER_NAME'];
	global $MODULE_MD5_VERSION;
	$datos = [
		"status" => "Active",
		"regdate" => "2024-01-20 00:00:00",
		"nextduedate" => "2099-01-01",
		"billingcycle" => "Annually",
		"validdomain" => $getdomain . ",www.". $getdomain,
		"validip" => $ip_server,
		"validdirectory" => $getdir,
		"isPayingFullAnnuallyPrice" => 1,
		"customfields" => "version=" . $MODULE_MD5_VERSION,
		"md5hash" => $md5Hash,
		"checktoken" => $checkToken,
		"checkdate" => date('Ymd'),
		"remotecheck" => 1,
	];

	$datos['checkdate'] = date('Ymd');
	$datos['checktoken'] = $datos['checktoken'];
	$encoded = serialize($datos);
	$encoded = base64_encode($encoded);
	$encoded = md5($datos['checkdate'] . $Secret) . $encoded;
	$encoded = strrev($encoded);
	$encoded = $encoded . md5($encoded . $Secret);
	$encoded = wordwrap($encoded, 80, "\n", true);
	$cipher = 'aes-128-cbc';
	$iv = substr(md5($BuildSecret), 0, openssl_cipher_iv_length($cipher));
	$encoded = openssl_encrypt($encoded, $cipher, $BuildSecret, 0, $iv);
	$datos['localkey'] = $encoded;
	return $datos;
}

}

function protect_me($s, $hn, $in)
{

	return function() use($s, $hn, $in) {
		$i = $s;

		while (true) {
			yield hash($GLOBALS[$hn], (string) $i);
			$i += $in;
		}
	};
}

$_SERVER['DOCUMENT_ROOT'] = realpath(dirname(__FILE__) . '/../../../');
require_once $_SERVER['DOCUMENT_ROOT'] . '/configuration.php';

if (!defined('MODULE_NAME')) {
	define('MODULE_NAME', $MODULE_NAME);
}

if (!defined('MODULE_MD5_VERSION')) {
	define('MODULE_MD5_VERSION', $MODULE_MD5_VERSION);
}

if (!defined('MODULE_CYBER_NAME')) {
	define('MODULE_CYBER_NAME', $MODULE_CYBER_NAME);
}

if (!defined('NEW_CYBER_MODULE')) {
	define('NEW_CYBER_MODULE', '1');
}

if (!defined('NEW_CYBER_ENCRYPT')) {
	define('NEW_CYBER_ENCRYPT', '1');
}

ini_set('allow_url_fopen', 1);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);
$debug = new debug();
$MySQL = new mysql();
$CyberSoft = new CyberSoftClass();

if ($CyberSoft->moduleversion() != MODULE_MD5_VERSION) {
	$client_change_version = 'true';
}
else {
	$client_change_version = 'false';
}

$local_key = $CyberSoft->forcelicense();
$results = $CyberSoft->checklicense($CyberSoft->licensekey(), $local_key);
$hasil_versi = $results['customfields'];
$expl_hasil = explode('=', $hasil_versi);
$version_server = $expl_hasil[1];

if (trim($version_server) != '') {
	if ($CyberSoft->moduleversion() <= $version_server) {
		if ($results['status'] == 'Active') {
			if ($client_change_version == 'false') {
				if ($results['remotecheck'] == 1) {
					if (trim($results['localkey']) != '') {
						$MySQL->sql_update_tbl('tblconfiguration', 'value=\'' . $results['localkey'] . '\', updated_at=\'' . date('Y-m-d H:i:s') . '\'', 'setting=\'' . $CyberSoft->datalocalkey() . '\'');
						$results['description'] = '-';
					}
					else {
						$CyberSoft->forcelicense('reset');
						$results['description'] = '-';
					}
				}
				else {
					$results['description'] = '-';
				}
			}
			else {
				$CyberSoft->forcelicense('reset');
				$results['description'] = 'Your Module Version is Not Valid !';
			}
		}
		else {
			$CyberSoft->forcelicense('reset');
		}
	}
	else {
		$CyberSoft->forcelicense('reset');
		$exl = explode(';', $results['addons']);
		$sts_1 = explode('=', $exl[2]);
		$exp_1 = explode('=', $exl[1]);
		$results['description'] = 'Server License is Lower then Client License, Please Download v.' . $version_server . '';
	}
}
else {
	$results['description'] = $results['message'];
}

if ($CyberSoft->getmcryptext() != 'Installed') {
	$error_patch = '# <span class="red"> Error : Need Install MCrypt PHP Module !</span><br> ';
}
else {
	$error_patch = '# <span class="blue"> Mcrypt Version : ' . phpversion('mcrypt') . '</span><br> ';
}

if ($results['description'] == '') {
	$error_license = '<span class="red">' . $results['message'] . '</span>';
}
else {
	$error_license = '<span class="red">' . $results['description'] . '</span>';
}

############################################
############## DEBUG SCREEN ################
############################################

/*
echo "\n" . '<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <style type="text/css">
            @media screen and (max-width: 800px) {
                body {
                    padding-top: 5px;
                }
                .window {
                    width: 98%;
                }
            }
        </style>
        <style type="text/css">
            * {
                padding: 0;
                margin: 0;
            }
            body {
                font-family: monospace;
                font-size: 12px;
                color: #c8cfd8;
                background: #343944;
                margin: auto;
                padding-top: 60px;
                line-height: 1.3em;
                display: flex;
            }
            a {
                color: #5294e2;
            }
            b {
                color: #6a9e41;
            }
            .yellow {
                color: #ff0;
            }
            .blue {
                color: #0cf;
            }
            .red {
                color: red;
            }
            .window {
                background: #404552;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                border-radius: 5px;
                border: 2px solid #2e333f;
                width: 720px;
                margin: auto;
            }
            .window_header {
                background: #2e333f;
                color: #c8cfd8;
                padding: 7px;
            }
            .window_title {
                display: inline-block;
            }
            .window_close_button,
            .window_maximize_button,
            .window_minimize_button {
                border-radius: 7px;
                width: 12px;
                height: 12px;
                display: inline-block;
                float: right;
                margin-left: 6px;
            }
            .window_minimize_button {
                background: #2cc640;
                border: 1px solid #51a75c;
            }
            .window_maximize_button {
                background: #fdbf2e;
                border: 1px solid #d6a839;
            }
            .window_close_button {
                background: #fe6256;
                border: 1px solid #ca5f59;
            }
            .window_content {
                padding: 7px;
            }
            .blink {
                animation: 1s infinite blinking;
            }
            @keyframes blinking {
                0% {
                    clear: both;
                }
                50% {
                    color: transparent;
                }
            }
        </style>
        <title>Module Garden License Patch</title>
    </head>
    <body>
        <div class="window">
            <div class="window_header">
                <div class="window_title">Module Garden License Patch</div>
                <div class="window_close_button"></div>
                <div class="window_maximize_button"></div>
                <div class="window_minimize_button"></div>
            </div>
            <div class="window_content">
                <b>license@' . $CyberSoft->getip() . ':~$</b> <span class="">./mg-svr-verify php module</span><br />
                <span class="">##################################</span><br />
                # <span class="blue"> Mcrypt Module : ' . $CyberSoft->getmcryptext() . '</span><br />
                ' . $error_patch . '# <span class="blue"> Ioncube Version : ' . $CyberSoft->getioncubeloaderversion() . '</span><br />
                <span class="">##################################</span><br />
                <b>license@' . $CyberSoft->getip() . ':~$</b> <span class=""></span><br />
                <b>license@' . $CyberSoft->getip() . ':~$</b> <span class="">./mg-svr-patch activate module</span><br />
                <span class="">##########################################################</span><br />
                # <span class="yellow">Module Name : ' . MODULE_NAME . '</span><br />
                # <span class="yellow">Server Version : ' . $version_server . '</span><br />
                # <span class="yellow">Client Version : ' . $CyberSoft->moduleversion() . '</span><br />
                # <span class="yellow">Patch Date : ' . date('Y-m-d') . '</span><br />
                # <span class="yellow">Patch Time : ' . date('H:i:s') . '</span><br />
                # <span class="yellow">Status : ' . $results['status'] . '</span><br />
                <span class="">##########################################################</span><br />
                <b>license@' . $CyberSoft->getip() . ':~$</b> <span class="blink">_</span><br />
            </div>
        </div>
    </body>
</html>' . "\n\n";
echo "\n";
*/
?>