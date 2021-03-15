<?php



$path = 'sounds/adg3.mp3';
$name = 'song.mp3';


if(isset($_GET['path'])){
    $path = $_GET['path'];
}
if(isset($_GET['name'])){
    $name = $_GET['name'];
}

if(strpos($path,'sounds/')!==0 && strpos($path,'Demos/')!==0){
    die("NOT ALLOWED!");
}


header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="'.$name.'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($path));

echo file_get_contents($path);