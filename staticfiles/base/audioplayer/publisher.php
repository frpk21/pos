<?php


ini_set("log_errors", 1);
ini_set('display_errors', '0');
error_reporting(E_ALL);
ini_set("error_log", "publisher.log");
if(file_exists(dirname(__FILE__).'/dzsap-config.php')){
  include_once(dirname(__FILE__).'/dzsap-config.php');
}

$dzsap_portal = null;
if(isset($dzsap_config) && $dzsap_config['type']=='portal'){
  include_once(dirname(__FILE__).'/portal/class-portal.php');
  $dzsap_portal = new DZSAP_Portal();
}



function dzs_clean($string) {
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

  return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function get_pcm($argplayerid, $source){

  $aux = 'pcm';
  $playerid = '';
  $current='';

  if (isset($argplayerid) && $argplayerid) {
    $aux.=$argplayerid;
    $playerid = $argplayerid;
  }else{
    if (isset($source) && $source) {
      $aux.=dzs_clean($source);
//      return 'ceva';
      $playerid = $source;
    }
  }

  $file = dirname(__FILE__).'/db/'.$aux.'.html';
//    echo $file;
  if(file_exists($file)){

    $current = file_get_contents($file);
  }



  return $current;

}

if(isset($_GET['load-lightbox-css']) && $_GET['load-lightbox-css']=='on'){

  header("Content-type: text/css");
  ?>
  .dzsap-main-con.loaded-item {
  opacity: 1;
  visibility: visible; }

  .dzsap-main-con.loading-item {
  opacity: 1;
  visibility: visible; }

  .dzsap-main-con {
  z-index: 5555;
  position: fixed;
  width: 100%;
  height: 100%;
  opacity: 0;
  visibility: hidden;
  top: 0;
  left: 0;
  transition-property: opacity, visibility;
  transition-duration: 0.3s;
  transition-timing-function: ease-out; }

  .dzsap-main-con .overlay-background {
  background-color: rgba(50, 50, 50, 0.5);
  position: absolute;
  width: 100%;
  height: 100%; }

  .dzsap-main-con .box-mains-con {
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  pointer-events: none; }

  .dzsap-main-con .box-main {
  pointer-events: auto;
  max-width: 100%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate3d(-50%, -50%, 0);
  transition-property: left, opacity;
  transition-duration: 0.3s;
  transition-timing-function: ease-out; }

  .dzsap-main-con.transition-slideup.loaded-item .transition-target {
  opacity: 1;
  visibility: visible;
  transform: translate3d(0, 0, 0); }

  .dzsap-main-con.transition-slideup .transition-target {
  opacity: 0;
  visibility: hidden;
  transform: translate3d(0, 50px, 0);
  transition-property: all;
  transition-duration: 0.3s;
  transition-timing-function: ease-out; }

  .dzsap-main-con .box-main-media-con {
  max-width: 100%; }

  .dzsap-main-con .box-main .close-btn-con {
  position: absolute;
  right: -15px;
  top: -15px;
  z-index: 5;
  cursor: pointer;
  width: 30px;
  height: 30px;
  background-color: #dadada;
  border-radius: 50%; }

  .dzsap-main-con.gallery-skin-default .box-main-media {
  box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.3); }

  .dzsap-main-con .box-main-media-con .box-main-media {
  transition-property: width, height;
  transition-duration: 0.3s;
  transition-timing-function: ease-out; }

  .box-main-media.type-inlinecontent {
  background-color: #ffffff;
  padding: 15px; }

  .dzsap-main-con.skin-default .box-main:not(.with-description) .real-media {
  border-radius: 5px; }

  .dzsap-main-con .box-main-media-con .box-main-media > .real-media {
  width: 100%;
  height: 100%; }





  .real-media .hidden-content-for-zoombox, .real-media > .hidden-content {
  display: block !important; }

  .hidden-content {
  display: none !important; }

  .social-icon {
  margin-right: 3px;
  position: relative; }

  .social-icon > .fa {
  font-size: 30px;
  color: #999;
  transition-property: color;
  transition-duration: 0.3s;
  transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1); }

  .social-icon > .the-tooltip {
  line-height: 1;
  padding: 6px 5px;
  background: rgba(0, 0, 0, 0.7);
  color: #FFFFFF;
  font-family: "Lato", "Open Sans", arial;
  font-size: 11px;
  font-weight: bold;
  position: absolute;
  left: 8px;
  white-space: nowrap;
  pointer-events: none;
  bottom: 100%;
  margin-bottom: 7px;
  opacity: 0;
  visibility: hidden;
  transition-property: opacity,visibility;
  transition-duration: 0.3s;
  transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1); }


  .social-icon:hover > .the-tooltip{
  opacity:1;
  visibility: visible;
  }

  .social-icon > .the-tooltip:before {
  content: "";
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 6px 6px 0 0;
  border-color: rgba(0, 0, 0, 0.7) transparent transparent transparent;
  position: absolute;
  left: 0;
  top: 100%; }

  h6.social-heading {
  display: block;
  text-transform: uppercase;
  font-family: "Lato",sans-sarif;
  font-size: 11px;
  font-weight: bold;
  margin-top: 20px;
  margin-bottom: 10px;
  color: #222222; }

  .field-for-view {
  background-color: #f0f0f0;
  line-height: 1;
  color: #555;
  padding: 8px;
  white-space: nowrap;
  font-size: 13px;
  overflow: hidden;
  text-overflow: ellipsis;
  font-family: 'Monospaced', Arial; }

  textarea.field-for-view {
  width: 100%;
  white-space: pre-line;
  line-height: 1.75; }
  <?php
  die();
}


class DZSAP_Publisher {

  public $dir = 'db/';

  function __construct() {


    $this->check_post_input();







  }



  function get_all_views(){

    $arr = array();




    $dir=$this->dir;
    $files1 = scandir($dir);

//        print_r($files1);

    foreach ($files1 as $fil){
      if(strpos($fil,'db-views')!==false){
//                echo $fil;




        $current = file_get_contents($this->dir.$fil);


        $id = str_replace('db-views','',$fil);
        $id = str_replace('.html','',$id);


        $aux = array(
          'label'=>$id,
          'views'=>$current,
        );

        array_push($arr, $aux);


      }
    }

//        print_r($arr);

    echo json_encode($arr);
  }

  function check_post_input() {

    global $dzsap_portal,$dzsap_config;

    if (isset($_REQUEST['action'])){



      if($_REQUEST['action']=='dzsap_get_views_all'){

        $this->get_all_views();
        die();
      }
    }
    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_get_views') {

      $aux = 'db-views';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }

      $file = dirname(__FILE__).'/db/'.$aux.'.html';


      $current = file_get_contents($file);
      echo $current;

      if (isset($_COOKIE['viewsubmitted-'.$playerid])) {
        echo 'viewsubmitted';
      }


      echo '{{theip-';
      echo $this->misc_get_ip();
      echo '}}';

      //print_r($_COOKIE);

      die();
    }



    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_submit_views') {

      $aux = 'db-views';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }



      if (isset($dzsap_config) && $dzsap_config['type'] == 'portal') {

        echo $dzsap_portal->submit_view();
      } else {
        $file = dirname(__FILE__).'/db/'.$aux.'.html';

        $current = '';
        if(file_exists($file)) {

          $current = file_get_contents($file);
        }


        if ($current == '') {
          $current = 0;
        }
        $current = intval($current);

        $confirmer = 'hascookie';

        if (isset($_COOKIE['viewsubmitted-'.$playerid])) {
          //echo $current;
        } else {
          $current = $current + 1;
          $confirmer = file_put_contents($file,$current);
          //echo $current;
        }
        setcookie('viewsubmitted-'.$playerid,'1',time() + 36000);


        echo $confirmer;


      }
      die();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_submit_download') {

      $aux = 'db-download';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }



      if (isset($dzsap_config) && $dzsap_config['type'] == 'portal') {

        echo $dzsap_portal->submit_view();
      } else {
        $file = dirname(__FILE__).'/db/'.$aux.'.html';


        $current = file_get_contents($file);

        if ($current == '') {
          $current = 0;
        }
        $current = intval($current);

        $confirmer = 'hascookie';

        if (isset($_COOKIE['downloadsubmitted-'.$playerid])) {
          //echo $current;
        } else {
          $current = $current + 1;
          $confirmer = file_put_contents($file,$current);
          //echo $current;
        }
        setcookie('downloadsubmitted-'.$playerid,'1',time() + 36000);


        echo $confirmer;
      }
      die();
    }

    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'dzsap_shoutcast_get_streamtitle') {


//      echo 'dada';

//      print_r($_GET);
      //print_r($_COOKIE);

      $source = $_GET['source'];





      include_once "shoutcast_now_playing.php";

      echo shoutcast_get_now_playing($source);

      die();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_get_comments') {


      $aux = 'comments';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }

      $file = dirname(__FILE__).'/db/'.$aux.'.html';
//    echo $file;
      $current='';
      if(file_exists($file)){

        $current = file_get_contents($file);
      }



      echo $current;



      //print_r($_COOKIE);

      die();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_get_pcm') {


      echo get_pcm($_POST['playerid'],$_POST['source']);


      //print_r($_COOKIE);

      die();
    }


    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_submit_pcm') {

      $aux = 'pcm';

      if (isset($_POST['playerid'])) {
        $aux.=dzs_clean($_POST['playerid']);
      }


      $file = dirname(__FILE__).'/db/'.$aux.'.html';
      $current = '';



      $content = $_POST['postdata'];
      $current = $content;
      $confirmer = file_put_contents($file,$current);

      echo $confirmer;


      // TODO: temp
      die();


      die();
    }


    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_front_submitcomment') {

      $aux = 'comments';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
      }


      if (isset($dzsap_config) && $dzsap_config['type'] == 'portal') {

        echo $dzsap_portal->submit_comment();
      } else {
        $file = dirname(__FILE__).'/db/'.$aux.'.html';
        $current = '';

        if(file_exists($file)){
          $current = file_get_contents($file);
        }


        $content = $_POST['postdata'];
        if(isset($_POST['skinwave_comments_process_in_php']) && $_POST['skinwave_comments_process_in_php']=='on'){

          $content = '<span class="dzstooltip-con" style="left:' . $_POST['comm_position'] . '"><span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black" style="width: 250px;"><span class="the-comment-author">@' .$_POST['skinwave_comments_account'] .'</span> says:<br>';
          $content.= htmlentities($_POST['postdata']);


          $content .= '</span><div class="the-avatar" style="background-image: url(' . $_POST['skinwave_comments_avatar'].')"></div></span>';
        }else{

        }
        $current .= $content;
        $confirmer = file_put_contents($file,$current);

        echo $confirmer;
      }


      die();
    }


    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_submit_playlist_entry') {
      $dzsap_portal->submit_playlist_entry($_POST['playlistid'], $_POST['mediaid']);
    }

    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_retract_playlist_entry') {
      $dzsap_portal->retract_playlist_entry($_POST['playlistid'], $_POST['mediaid']);
    }



    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_get_rate') {


      $aux = 'db-rates';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }

      $file = dirname(__FILE__).'/db/'.$aux.'.html';

      $current = file_get_contents($file);



      if (isset($_COOKIE['ratesubmitted-'.$playerid])) {
        $current.='|'.$_COOKIE['ratesubmitted-'.$playerid];
      }

      echo $current;



      //print_r($_COOKIE);

      die();
    }


    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_submit_rate') {



      $aux = 'db-rates';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }


      if (isset($dzsap_config) && $dzsap_config['type'] == 'portal') {

        echo $dzsap_portal->submit_rating();
      } else {


        $file = dirname(__FILE__).'/db/'.$aux.'.html';





        $current = file_get_contents($file);
        $current_arr = explode("|",$current);
//    print_r($current_arr);

        $rate_index = 0;
        $rate_nr = 0;

        if (count($current_arr) == 1 && $current_arr[0] == '') {
//        echo 'ceva';
        } else {
          $rate_index = $current_arr[0];
          $rate_nr = intval($current_arr[1]);

          if ($rate_index == '' || $rate_index == ' ') {
            $rate_index = 0;
          }
        }

        if (!isset($_COOKIE['ratesubmitted-'.$playerid])) {
          $rate_nr++;
        }


        if ($rate_nr <= 0) {
          $rate_nr = 1;
        }

        $rate_index = ($rate_index * ($rate_nr - 1) + intval($_POST['postdata'])) / ($rate_nr);

//    echo ' $rate_index: '; print_r($rate_index);
//    echo ' $rate_nr: '; print_r($rate_nr);

        $fout = $rate_index.'|'.$rate_nr;

//    echo ' $fout: '; print_r($fout);

        $confirmer = file_put_contents($file,$fout);


        setcookie('ratesubmitted-'.$playerid,intval($_POST['postdata']),time() + 36000);

        echo $confirmer;
      }

      die();
    }






    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_get_likes') {


      $aux = 'db-likes';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }

      $file = dirname(__FILE__).'/db/'.$aux.'.html';

      $current = file_get_contents($file);
      echo $current;


      if (isset($_COOKIE['likesubmitted-'.$playerid])) {
        echo 'likesubmitted';
      }

      //print_r($_COOKIE);


      die();
    }


    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_submit_like') {



      $aux = 'db-likes';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }





      if (isset($dzsap_config) && $dzsap_config['type'] == 'portal') {

        echo $dzsap_portal->submit_like();
      } else {

        $file = dirname(__FILE__).'/db/'.$aux.'.html';



        $current = file_get_contents($file);
        $current = intval($current);
        $current = $current + 1;
        $confirmer = file_put_contents($file,$current);
        setcookie('likesubmitted-'.$playerid,'1',time() + 36000);


        echo $confirmer;
      }



      die();
    }
    if (isset($_POST['action']) && $_POST['action'] == 'dzsap_retract_like') {

      $aux = 'db-likes';
      $playerid = '';

      if (isset($_POST['playerid'])) {
        $aux.=$_POST['playerid'];
        $playerid = $_POST['playerid'];
      }

      $file = dirname(__FILE__).'/db/'.$aux.'.html';


      if (isset($dzsap_config) && $dzsap_config['type'] == 'portal') {

        echo $dzsap_portal->retract_like();
      } else {

        $file = dirname(__FILE__).'/db/'.$aux.'.html';



        $current = file_get_contents($file);
        $current = intval($current);
        $current = $current - 1;
        $confirmer = file_put_contents($file,$current);


        setcookie('likesubmitted-'.$playerid,'',time() - 36000);

        echo $confirmer;
      }




      die();
    }
  }

  function misc_get_ip() {

    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    $ip = filter_var($ip,FILTER_VALIDATE_IP);
    $ip = ($ip === false) ? '0.0.0.0' : $ip;


    return $ip;
  }

}

$dzsap_publisher = new DZSAP_Publisher();



//print_r($_POST);
