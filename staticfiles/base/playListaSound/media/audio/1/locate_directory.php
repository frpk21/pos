<?php
	
	echo substr(substr(__FILE__, strlen(realpath($_SERVER['DOCUMENT_ROOT']))), 0, - strlen(basename(__FILE__)));
	
?>
