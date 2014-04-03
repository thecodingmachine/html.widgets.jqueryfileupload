<?php
/* @var $object Mouf\Html\Widgets\JqueryFileUpload\JqueryFileUploadWidget */
$id = htmlentities($object->getId(), ENT_QUOTES);
?>
<div id="<?= $id ?>">
	<span class="btn btn-success fileinput-button">
	    <i class="glyphicon glyphicon-plus"></i>
	    <span>Select files...</span>
	    <!-- The file input field used as target for the file upload widget -->
	    <input type="file" multiple="multiple" name="files[]" id="fileupload">
	</span>
	<span class="help-inline"></span>
	<div class="progress">
	        <div class="progress-bar progress-bar-success bar"></div>
	</div>
	<div class="files"></div>
	<input type="hidden" name="<?= htmlentities($object->getName(), ENT_QUOTES) ?>" value="<?= htmlentities($object->getToken(), ENT_QUOTES) ?>" />
</div>
<script type="text/javascript">
$(function () {
	'use strict';

	var rootElem = $("#<?= $id ?>");
	var options = <?php echo json_encode($object->getOptions()); ?>;
	
	options.done = function (e, data) {
		var result = data.result;
		// Depending on browser we get a string or some JSON
		if (typeof(result) == 'string') {
			result = jQuery.parseJSON(data.result);
		} 
		
		$.each(result.files, function (index, file) {
			$('<p/>').text(file.name).appendTo(jQuery(rootElem).find('.files'));
		});
	};
	options.progressall = function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		jQuery(rootElem).find('.progress-bar').css(
			'width',
			progress + '%'
		);
	};
	options.fail = function (e, data) {
		rootElem.addClass('error');
		if (data.jqXHR.status == 404) {
			$("#<?= $id ?> .help-inline").text("Error while contacting server to upload document. Unable to contact server (HTTP 404 error returned)");
		} else {
			$("#<?= $id ?> .help-inline").text("Error while contacting server to upload document. HTTP code: "+data.jqXHR.status+". Message: "+data.jqXHR.responseText);			
		}
	}
	
	$('#fileupload').fileupload(options).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');
});
</script>
