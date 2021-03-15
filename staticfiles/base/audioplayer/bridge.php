<?php

include_once(dirname(__FILE__).'/class-portal.php');
$dzsap_portal = new DZSAP_Portal();
?>
<!doctype html>
<html>
<head>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $dzsap_portal->url_base; ?>audioplayer/audioplayer.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $dzsap_portal->url_base; ?>dzstooltip/dzstooltip.css"/>
</head>
<body>
<?php
$args = array();
if(isset($_GET['type']) && $_GET['type']=='gallery'){
    
    $args = array(
        'id' => $_GET['id'],
        'embedded' => 'on',
    );


            if(isset($_GET['db'])){
                $args['db'] = $_GET['db'];
            };
    echo $dzsap_portal->show_shortcode($args);

}

//print_r($_GET);

if(isset($_GET['type']) && $_GET['type']=='player'){
    
    
//    echo $_GET['margs'];
    $args = unserialize(stripslashes($_GET['margs']));
//    print_r($args);
    $args['embedded']='on';


    echo $dzsap_portal->shortcode_player($args);

}


?>
<script type="text/javascript" src="<?php echo $dzsap_portal->url_base; ?>audioplayer/audioplayer.js"></script>
</body>
</html>