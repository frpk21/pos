<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


function shoutcast_get_now_playing($arg){


  $final_metadata = array();
  $source = $arg;
  $url_vars = parse_url($source);
  $host = $url_vars['host'];
  $path = isset($url_vars['path'])?$url_vars['path']:'/';


  $url = $source;
  $ch = curl_init($url);

  $headers = array(
    'GET '.$path.' HTTP/1.0',
    'Host: ' . $url_vars['host'] . '',
    'Connection: Close',
    'User-Agent: Winamp',
    'Accept: */*',
    'icy-metadata: 1',
    'icy-prebuffer: 2314',
  );

  $construct_url = $url_vars['scheme'] . '://'. $url_vars['host'] . $path;


  $err_no = '';
  $err_str = '';

  $fp = @fsockopen($url_vars['host'], $url_vars['port'], $err_no, $err_str, 10);


//  print_r($url_vars); echo '<br><br>';
//  print_r($fp); echo '<br><br>';

  if ($fp) {


    $headers_str = '';

    foreach ($headers as $key=>$val){
      $headers_str.=$val.'\r\n';
    }



//    echo $headers_str . '<-headers_str<br><br>';

    define('CRLF', "\r\n");


    $headers_str = 'GET '.$path.' HTTP/1.0' . CRLF .
      'Host: ' . $url_vars['host'] . CRLF .
      'Connection: Close' . CRLF .
      'User-Agent: Winamp 2.51' . CRLF .
      'Accept: */*' . CRLF .
      'icy-metadata: 1'.CRLF.
      'icy-prebuffer: 65536'.CRLF. CRLF;


    fwrite($fp, $headers_str);

    stream_set_timeout($fp, 2, 0);
    $response = "";

    while (!feof($fp)){



      $line = fgets($fp, 4096);
      if('' == trim($line)){
        break;
      }
      $response .= $line;
    }

//    echo 'response ->> <pre>'; print_r($response); echo '</pre><--';


    preg_match_all('/(.*?):(.*)[^|$]/', $response, $fout_arr);

//    echo 'response ->> <pre>'; print_r($fout_arr); echo '</pre><--';

    if(isset($fout_arr[1])){

      $final_arr = array();
      foreach ($fout_arr[1] as $key=>$val){
        $final_arr[$val] = $fout_arr[2][$key];
      }


//      echo '$final_arr ->> <pre>'; print_r($final_arr); echo '</pre><--';









      // -- snippet from https://stackoverflow.com/questions/15803441/php-script-to-extract-artist-title-from-shoutcast-icecast-stream
      if (!isset($final_arr['icy-metaint'])){
        $data = ''; $metainterval = 512;
        while(!feof($fp)){
          $data .= fgetc($fp);
          if (strlen($data) >= $metainterval) break;
        }
        $this->print_data($data);
        $matches = array();
        preg_match_all('/([\x00-\xff]{2})\x0\x0([a-z]+)=/i', $data, $matches, PREG_OFFSET_CAPTURE);
        preg_match_all('/([a-z]+)=([a-z0-9\(\)\[\]., ]+)/i', $data, $matches, PREG_SPLIT_NO_EMPTY);


//        echo '<pre>';var_dump($matches);echo '</pre>';


        $title = $artist = '';
        foreach ($matches[0] as $nr => $values){
          $offset = $values[1];
          $length = ord($values[0]{0}) +
            (ord($values[0]{1}) * 256)+
            (ord($values[0]{2}) * 256*256)+
            (ord($values[0]{3}) * 256*256*256);
          $info = substr($data, $offset + 4, $length);
          $seperator = strpos($info, '=');
          $final_metadata[substr($info, 0, $seperator)] = substr($info, $seperator + 1);
          if (substr($info, 0, $seperator) == 'title') $title = substr($info, $seperator + 1);
          if (substr($info, 0, $seperator) == 'artist') $artist = substr($info, $seperator + 1);
        }
        $final_metadata['streamtitle'] = $artist . ' - ' . $title;
      }else{
        $metainterval = $final_arr['icy-metaint'];
        $intervals = 0;
        $metadata = '';
        while(1){
          $data = '';
          while(!feof($fp)){
            $data .= fgetc($fp);
            if (strlen($data) >= $metainterval) break;
          }
          //$this->print_data($data);
          $len = join(unpack('c', fgetc($fp))) * 16;
          if ($len > 0){
            $metadata = str_replace("\0", '', fread($fp, $len));
            break;
          }else{
            $intervals++;
            if ($intervals > 100) break;
          }
        }
        $metarr = explode(';', $metadata);
        foreach ($metarr as $meta){
          $t = explode('=', $meta);
          if (isset($t[0]) && trim($t[0]) != ''){
            $name = preg_replace('/[^a-z][^a-z0-9]*/i','', strtolower(trim($t[0])));
            array_shift($t);
            $value = trim(implode('=', $t));
            if (substr($value, 0, 1) == '"' || substr($value, 0, 1) == "'"){
              $value = substr($value, 1);
            }
            if (substr($value, -1) == '"' || substr($value, -1) == "'"){
              $value = substr($value, 0, -1);
            }
            if ($value != ''){
              $final_metadata[$name] = $value;
            }
          }
        }
      }



//      echo '$final_metadata ->> <pre>'; print_r($final_metadata); echo '</pre><--';
    }

    fclose($fp);
  }


  if(isset($final_metadata) && isset($final_metadata['streamtitle'])){
    return $final_metadata['streamtitle'];


  }else{
    return 'Song name not found';
  }


}


//$source = 'http://masterv2.shoutcast.com:8000/stream/102843/';
//echo ''.shoutcast_get_now_playing($source);
//echo 'resp - '; print_r(shoutcast_get_now_playing($source)); echo '<-';






