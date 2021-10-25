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

$instanceName = $_REQUEST['instanceName'];
$uploader = MoufManager::getMoufManager()->get($instanceName);

$uploader->beforeUpload($targetDir, $token);


header('Content-Type: application/json');

$upload_handler = new MoufUploadHandler([
		'upload_dir'=>$targetDir,
        'accept_file_types'=>$uploader->getAcceptFileTypes()
]);

$uploader->afterUpload($targetDir, $token);