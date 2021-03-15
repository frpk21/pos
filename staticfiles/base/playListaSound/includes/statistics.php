<?php

	include "ipaddress.php";
	include "../stats/config.php";
	include "../stats/database.php";

	if(isset($_POST['action']) && isset($_POST['media_id'])) {
	    $action = $_POST['action'];
	    switch($action) {
	    	case 'hap_play_count' : hap_play_count();break;
	        case 'hap_like_count' : hap_like_count();break;
	        case 'hap_download_count' : hap_download_count();break;
	        case 'hap_all_count' : hap_all_count();break;
	        default: echo json_encode('');exit;break;
	    }
	}

	function hap_play_count(){

		/*
		set play status on media start, to do: option to set play on media end, or at least x seconds played
		*/

		$media_id = $_POST['media_id'];
		$date = date("Y-m-d");
		$title = str_replace('"', "'", stripslashes($_POST['title']));
		$artist = str_replace('"', "'", stripslashes($_POST['artist']));
		$user_ip = get_ip_address();

		$currentTime = (int)$_POST['currentTime'];
		$duration = (int)$_POST['duration'];
		$percent = $duration / 4;//at least 25% played to count
		if($currentTime > $percent){
		    $play_add = 1;
		} else {
		    $play_add = 0;
		}

		$db = new database();

		//check if exist
	    $stmt = $db->prepare("SELECT id FROM statistics WHERE media_id=? AND date='$date'");
	    $stmt->execute([$media_id]);

	    if($stmt->rowCount() == 0){//create entry

	    	try{
				$stmt = $db->prepare("INSERT INTO statistics (`title`, `artist`, `play`, `time`, `like`, `download`, `date`, `user_ip`, `media_id`) VALUES ('$title', '$artist', 1, '$currentTime', 0, 0, '$date', '$user_ip', ?)");
				$stmt->execute([$media_id]);
			}catch(PDOException $e) {
	            $error = $e->getMessage();
	        }	

	    }else{//update 

	    	try{
		    	$stmt = $db->prepare("UPDATE statistics SET `play`=`play`+$play_add, `time`=`time`+$currentTime WHERE `media_id`=? AND `date`='$date' LIMIT 1");
		    	$stmt->execute([$media_id]);
	    	}catch(PDOException $e) {
	            $error = $e->getMessage();
	        }

	    }

	    //get count
	    try{
	    	$stmt = $db->prepare("SELECT SUM(play) AS play FROM statistics WHERE media_id=?");
		    $stmt->execute([$media_id]);
        }catch(PDOException $e) {
            $error = $e->getMessage();
        }

	    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));

		$stmt = null;
	    $db = null;
	   
	}

	function hap_like_count(){

		/*
		to do: cookies to remember already liked 
		*/

		$media_id = $_POST['media_id'];
		$date = date("Y-m-d");
		$title = str_replace('"', "'", stripslashes($_POST['title']));
		$artist = str_replace('"', "'", stripslashes($_POST['artist']));
		$user_ip = get_ip_address();

		$db = new database();

		//check if exist
	    $stmt = $db->prepare("SELECT id, `like` FROM statistics WHERE media_id=? AND date='$date' AND `user_ip`='$user_ip'");
	    $stmt->execute([$media_id]);

	    if($stmt->rowCount() == 0){//create entry

	    	try{
				$stmt = $db->prepare("INSERT INTO statistics (`title`, `artist`, `play`, `time`, `like`, `download`, `date`, `user_ip`, `media_id`) VALUES ('$title', '$artist', 0, 0, 1, 0, '$date', '$user_ip', ?)");
				$stmt->execute([$media_id]);
			}catch(PDOException $e) {
	            $error = $e->getMessage();
	        }	

	    }else{//update 

	    	try{
	    		$results = $stmt->fetch(PDO::FETCH_ASSOC);
		    	if($results["like"] == 0){
		    		$stmt = $db->prepare("UPDATE statistics SET `like`=1 WHERE `media_id`=? AND `date`='$date' AND `user_ip`='$user_ip'");
		    		$stmt->execute([$media_id]);
		    	}else{
		    		//$stmt = $db->prepare("UPDATE statistics SET `like`=`like`+1 WHERE `media_id`=? AND `date`='$date' AND `user_ip`='$user_ip'");
		    	}
		    	
	    	}catch(PDOException $e) {
	            $error = $e->getMessage();
	        }

	    }

	    //get count
	    try{
	    	$stmt = $db->prepare("SELECT SUM(`like`) AS 'like' FROM statistics WHERE media_id=?");
		    $stmt->execute([$media_id]);
        }catch(PDOException $e) {
            $error = $e->getMessage();
        }

	    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));

		$stmt = null;
	    $db = null;
	   
	}
	
	function hap_download_count(){

		$media_id = $_POST['media_id'];
		$date = date("Y-m-d");
		$title = str_replace('"', "'", stripslashes($_POST['title']));
		$artist = str_replace('"', "'", stripslashes($_POST['artist']));
		$user_ip = get_ip_address();

		$db = new database();

	 	//check if exist
	    $stmt = $db->prepare("SELECT id FROM statistics WHERE media_id=? AND date='$date'");
	    $stmt->execute([$media_id]);

	    if($stmt->rowCount() == 0){//create entry

	    	try{
				$stmt = $db->prepare("INSERT INTO statistics (`title`, `artist`, `play`, `time`, `like`, `download`, `date`, `user_ip`, `media_id`) VALUES ('$title', '$artist', 0, 0, 0, 1, '$date', '$user_ip', ?)");
				$stmt->execute([$media_id]);
			}catch(PDOException $e) {
	            $error = $e->getMessage();
	        }	

	    }else{//update 

	    	try{
		    	$stmt = $db->prepare("UPDATE statistics SET `download`=`download`+1 WHERE `media_id`=? AND `date`='$date' LIMIT 1");
		    	$stmt->execute([$media_id]);
	    	}catch(PDOException $e) {
	            $error = $e->getMessage();
	        }

	    }

	    //get count
	    try{
	    	$stmt = $db->prepare("SELECT SUM(download) AS download FROM statistics WHERE media_id=?");
		    $stmt->execute([$media_id]);
        }catch(PDOException $e) {
            $error = $e->getMessage();
        }

	    echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));

		$stmt = null;
	    $db = null;
	   
	}

	function hap_all_count(){

		$media_id = $_POST['media_id'];

		$db = new database();

		$ids = explode(',', $media_id);
		$in = implode(',', array_fill(0, count($ids), '?'));

		try{
			$stmt = $db->prepare("SELECT media_id, SUM(play) AS play, SUM(`like`) AS 'like', SUM(download) AS download FROM statistics WHERE media_id IN ($in) GROUP BY media_id");
	    	$stmt->execute($ids);
        }catch(PDOException $e) {
            $error = $e->getMessage();
        }

		echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
		exit;

		$stmt = null;
		$db = null;
	   
	}



?>
