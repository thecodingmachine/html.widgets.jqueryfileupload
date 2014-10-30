<?php
use Mouf\Html\Widgets\JqueryFileUpload\JqueryFileUploadWidget;
use Mouf\MoufManager;
use Mouf\Html\Widgets\JqueryFileUpload\MoufUploadHandler;

require_once '../../../../../mouf/Mouf.php';

if (!defined('ROOT_URL') && function_exists('apache_getenv')) {
	define('ROOT_URL', apache_getenv("BASE")."/../../../../../");
}

$moufManager = MoufManager::getMoufManager();
$moufManager->getInstance('sessionManager')->start();

$token = $_REQUEST['jqueryFileUploadUniqueId'];
$targetDir = $_SESSION['mouf_jqueryfileupload_autorizeduploads'][$token];

header('Content-Type: application/json');

$upload_handler = new MoufUploadHandler([
		'upload_dir'=>$targetDir
]);
