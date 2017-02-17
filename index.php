<?php
ob_start();
require('main.php');

echo "\n\r";
//  Return the contents of the output buffer
$htmlStr = ob_get_contents();
// Clean (erase) the output buffer and turn off output buffering
ob_end_clean(); 
// Write final string to file
file_put_contents('.log', $htmlStr, FILE_APPEND);
?>