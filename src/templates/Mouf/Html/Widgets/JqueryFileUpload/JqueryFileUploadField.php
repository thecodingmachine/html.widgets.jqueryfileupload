<?php
	use Mouf\Html\Widgets\JqueryFileUpload\JqueryFileUploadField;
/* @var $object JqueryFileUploadField */

	if($required) {
		$object->getLabel()->addText('<span class="required">*</span>');
	}
	$object->getLabel()->toHtml();
	if($object->isRequired()) {
		//$object->getJqueryFileUploadWidget()->setRequired('required');
	}
	$object->getJqueryFileUploadWidget()->toHtml();
	if($object->getHelpText()) {
		$object->getHelpText()->toHtml();
	}
