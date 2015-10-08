<?php
namespace Mouf\Html\Widgets\JqueryFileUpload;

/**
 * Possible constants passed to the File::move* methods.
 */
class RenameEnum
{
    // Move the file, fails if a file with same name already exists
    const MOVE = "MOVE";
    // Move the file, overwrite if a file with same name already exists
    const MOVE_AND_OVERWRITE = "MOVE_AND_OVERWRITE";
    // Move the file, rename the file if a file with same name already exists
    const MOVE_AND_RENAME = "MOVE_AND_RENAME";
}