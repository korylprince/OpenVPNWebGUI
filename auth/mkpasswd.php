<?php
if( php_sapi_name() != 'cli' ) die('');
include('options.php');
$hash = call_user_func($options['hashFunction'], $argv[1]. $options['passwordSalt'])."\n";
print $hash;
?>
