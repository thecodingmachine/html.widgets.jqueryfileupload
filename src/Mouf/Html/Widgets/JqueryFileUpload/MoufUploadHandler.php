<?php 
namespace Mouf\Html\Widgets\JqueryFileUpload;

// The blueimp package does not feature an autoloader, so let's autoload manually.
require_once __DIR__.'/../../../../../../../blueimp/jquery-file-upload/server/php/UploadHandler.php';

/**
 * A class extending and customizing the behaviour of the default UploadHandler
 * provided by the blueimp/jquery-file-upload component.
 * 
 * @author David Négrier <david@mouf-php.com>
 */
class MoufUploadHandler extends \UploadHandler {
	
	protected function set_additional_file_properties($file) {
		parent::set_additional_file_properties($file);
		// Let's add the HTML representing the file to the object returned.
		$fileWidget = new FileWidget((isset($file->file_path)?$file->file_path:$file->name), md5($file->name));
		
		ob_start();
		$fileWidget->toHtml();
		$file->html = ob_get_clean();  
	}
}