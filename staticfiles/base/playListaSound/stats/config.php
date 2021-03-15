<?php

$localhost = array(
    '127.0.0.1',
    '::1'
);

if(in_array($_SERVER['REMOTE_ADDR'], $localhost)){//localhost
  
	define ('DB_HOST','localhost');
	define ('DB_USER','');
	define ('DB_PASS','');
	define ('DB_NAME','');

}else{//online

	define ('DB_HOST','localhost');
	define ('DB_USER','');
	define ('DB_PASS','');
	define ('DB_NAME','');

}



