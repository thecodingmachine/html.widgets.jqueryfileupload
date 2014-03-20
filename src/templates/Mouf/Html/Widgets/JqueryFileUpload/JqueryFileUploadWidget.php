<?php
/* @var $object Mouf\Html\Widgets\JqueryFileUpload\JqueryFileUploadWidget */
?>
<span class="btn btn-success fileinput-button">
    <i class="glyphicon glyphicon-plus"></i>
    <span>Select files...</span>
    <!-- The file input field used as target for the file upload widget -->
    <input type="file" multiple="" name="files[]" id="fileupload">
</span>
<script type="text/javascript">
$(function () {
	'use strict';

	var options = <?php echo json_encode($object->options); ?>
	options.done = function (e, data) {
		$.each(data.result.files, function (index, file) {
			$('<p/>').text(file.name).appendTo('#files');
		});
	};
	options.progressall = function (e, data) {
		var progress = parseInt(data.loaded / data.total * 100, 10);
		$('#progress .progress-bar').css(
			'width',
			progress + '%'
		);
	};
	
	$('#fileupload').fileupload(options).prop('disabled', !$.support.fileInput)
		.parent().addClass($.support.fileInput ? undefined : 'disabled');
	});
}
</script>
