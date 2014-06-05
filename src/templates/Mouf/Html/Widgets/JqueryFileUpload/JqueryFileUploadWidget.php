<?php
/* @var $object Mouf\Html\Widgets\JqueryFileUpload\JqueryFileUploadWidget */
$id = htmlentities($object->getId(), ENT_QUOTES);
$options = $object->getOptions();
?>
<div id="<?= $id ?>">
	<span class="btn btn-success fileinput-button">
	    <i class="glyphicon glyphicon-plus"></i>
	    <span>Select files...</span>
	    <!-- The file input field used as target for the file upload widget -->
	    <input type="file" <?php echo (isset($options['maxNumberOfFiles']) && $options['maxNumberOfFiles'] != 1)?"multiple='multiple'":"" ?> name="files[]">
	</span>
	<span class="help-inline"></span>
	<div class="progress">
	        <div class="progress-bar progress-bar-success bar"></div>
	</div>
	<div class="files">
	<?php foreach ($object->getDefaultFiles() as $file) {
		$file->toHtml();
	}
	?>
	</div>
	<input type="hidden" name="<?= htmlentities($object->getName(), ENT_QUOTES) ?>" value="<?= htmlentities($object->getToken(), ENT_QUOTES) ?>" />
</div>
<script type="text/javascript">
jQuery(function () {
	'use strict';

	var rootElem = jQuery("#<?= $id ?>");
	<?php
	$acceptFileTypes = null;
	if (isset($options['acceptFileTypes'])) {
		$acceptFileTypes = $options['acceptFileTypes'];
		unset($options['acceptFileTypes']);
	}
	?>
	var options = <?php echo json_encode($options); ?>;

	options.add = function(e, data) {
        var uploadErrors = [];
        <?php if ($acceptFileTypes) { ?>
        var acceptFileTypes = <?= $acceptFileTypes ?>;
        if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
            uploadErrors.push('Not an accepted file type');
        }
        <?php } ?>
        <?php if ($minFileSize) { ?>
        if(data.originalFiles[0]['size'].length && data.originalFiles[0]['size'] < <?= $minFileSize ?>) {
            uploadErrors.push('Filesize is too small');
        }
        <?php } ?>
        <?php if ($maxFileSize) { ?>
        if(data.originalFiles[0]['size'].length && data.originalFiles[0]['size'] > <?= $maxFileSize ?>) {
            uploadErrors.push('Filesize is too big');
        }
        <?php } ?>
        if(uploadErrors.length > 0) {
            alert(uploadErrors.join("\n"));
        } else {
            data.submit();
        }
	};
	
	options.done = function (e, data) {
		var result = data.result;
		// Depending on browser we get a string or some JSON
		if (typeof(result) == 'string') {
			result = jQuery.parseJSON(data.result);
		} 
		
		jQuery.each(result.files, function (index, file) {
			//jQuery('<p/>').text(file.name).appendTo(jQuery(rootElem).find('.files'));
            if(options.maxNumberOfFiles == 1){
                jQuery("#<?= $id ?> .file-delete-button").trigger("click");
            }
			jQuery(file.html).appendTo(jQuery(rootElem).find('.files'));
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
			jQuery("#<?= $id ?> .help-inline").text("Error while contacting server to upload document. Unable to contact server (HTTP 404 error returned)");
		} else {
			jQuery("#<?= $id ?> .help-inline").text("Error while contacting server to upload document. HTTP code: "+data.jqXHR.status+". Message: "+data.jqXHR.responseText);			
		}
	}
	
	jQuery('#<?= $id ?> input').fileupload(options).prop('disabled', !jQuery.support.fileInput)
		.parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');

	jQuery(document).on("click", "#<?= $id ?> .file-delete-button", null, function() {
		var id = jQuery(this).data('id');
		jQuery(this).parent('.file-widget').remove();

		var hiddenInput = jQuery('<input type="hidden" />').attr('name', <?= json_encode($object->getDeleteName().'[]') ?>)
			.val(id)
			.appendTo(jQuery("#<?= $id ?>"));
	});
});
</script>
