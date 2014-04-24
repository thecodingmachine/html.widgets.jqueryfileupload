<?php
/* @var $object Mouf\Html\Widgets\JqueryFileUpload\FileWidget */
?>
<span class="badge file-widget" data-id="<?php echo htmlentities($id); ?>">
<?php echo htmlentities($object->getFileInfo()->getFilename()); ?>
 <i class="icon-remove file-delete-button" data-id="<?php echo htmlentities($id); ?>"></i>
</span>
