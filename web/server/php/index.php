<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
$options = [
    'imageMaxWidth' => 1920,
    'imageMaxHeight' => 1080,
    'maxFileSize' => 50000000,
    'album' => $_GET['album']
];
$upload_handler = new UploadHandler($options);
