<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;

interface JQueryFileUploadListenerInterface {
	
	public function onBeforeUpload($targetDir, $token);
	
	public function onAfterUpload($targetDir, $token);
	
}