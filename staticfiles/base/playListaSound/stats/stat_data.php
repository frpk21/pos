<?php

	include "config.php";
	include "database.php";

	date_default_timezone_set('UTC');

	$top_level_page = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	
	$db = new database();

	$statistics_table = 'statistics';

	//clear stats
	if(isset($_GET['action'])){
		$action = $_GET['action'];
	    if($action == 'clear_statistics'){
	    	$stmt = $db->prepare("TRUNCATE TABLE {$statistics_table}");
			$stmt->execute();
	    }
	}





	//pagination

	// records per page
	$per_page = 33;
	// current page
	$curr_page = isset($_GET['page_num']) ? $_GET['page_num'] : 1;
	// number of records
	$stmt = $db->prepare("SELECT COUNT(id) FROM {$statistics_table} GROUP BY media_id");
	$stmt->execute();
	$num_records = $stmt->rowCount();

	// total pages
	$total_pages = intval(($num_records - 1) / $per_page) + 1;
	// limits
	$curr_page = intval($curr_page);
	if(empty($curr_page) or $curr_page < 0) $curr_page = 1;
	else if($curr_page > $total_pages) $curr_page = $total_pages;
	// start
	$start = $curr_page * $per_page - $per_page;




	//results for individual table
	if(isset($_GET['order'])){

		$order = $_GET['order'];
		$order = urldecode($order);

	}else{//artist/ title

		$order = 'artist';

	}


	$stmt = $db->prepare("SELECT media_id, title, artist, SUM(play) AS play, SUM(time) AS time, SUM(download) AS download, SUM(`like`) AS 'like' FROM {$statistics_table} GROUP BY media_id ORDER BY `$order` DESC LIMIT $start, $per_page");
	$stmt->execute();
	$results = $stmt->fetchAll(PDO::FETCH_ASSOC);


	//all time results
	$stmt = $db->prepare("SELECT SUM(play) AS play, SUM(time) AS time, SUM(download) AS download, SUM(`like`) AS 'like' FROM {$statistics_table}");
	$stmt->execute();
	$all = $stmt->fetch(PDO::FETCH_ASSOC);


    $all_time = $all['time'];
    $all_play = $all['play'];
    $all_download = $all['download'];
    $all_like = $all['like'];


	// top day
	$stmt = $db->prepare("SELECT media_id, artist, title, play FROM {$statistics_table} WHERE `date`= CURDATE() ORDER BY play DESC LIMIT 0,10");
	$stmt->execute();
	$top_day = $stmt->fetchAll(PDO::FETCH_ASSOC);



	// top week
	$stmt = $db->prepare("SELECT media_id, artist, title, SUM(play) AS weekcount FROM {$statistics_table} WHERE `date` > NOW() - INTERVAL 7 DAY GROUP BY media_id ORDER BY weekcount DESC LIMIT 0,10");
	$stmt->execute();
	$top_week = $stmt->fetchAll(PDO::FETCH_ASSOC);



	// top month
	$stmt = $db->prepare("SELECT media_id, artist, title, SUM(play) AS monthcount FROM {$statistics_table} WHERE `date` > NOW() - INTERVAL 30 DAY GROUP BY media_id ORDER BY monthcount DESC LIMIT 0,10");
	$stmt->execute();
	$top_month = $stmt->fetchAll(PDO::FETCH_ASSOC);



	// top plays all time
	$stmt = $db->prepare("SELECT media_id, artist, title, SUM(play) AS allcount FROM {$statistics_table} GROUP BY media_id ORDER BY allcount DESC LIMIT 0,10");
	$stmt->execute();
	$top_plays = $stmt->fetchAll(PDO::FETCH_ASSOC);



	// top downloads all time
	$stmt = $db->prepare("SELECT media_id, artist, title, SUM(download) AS allcount FROM {$statistics_table} GROUP BY media_id ORDER BY allcount DESC LIMIT 0,10");
	$stmt->execute();
	$top_downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);



	// top likes all time
	$stmt = $db->prepare("SELECT media_id, artist, title, SUM(`like`) AS allcount FROM {$statistics_table} GROUP BY media_id ORDER BY allcount DESC LIMIT 0,10");
	$stmt->execute();
	$top_likes = $stmt->fetchAll(PDO::FETCH_ASSOC);




	function hap_convertTime($time){
	    if ($time < 60) {
	        return $time.' sec';
	    } else if ($time >= 60 && $time < 3600) {
			$min = date("i", mktime(0, 0, $time));
	    	$sec = date("s", mktime(0, 0, $time));
			if($min < 10){
				$min = substr($min, -1);
			}
	        return $min.'.'.$sec.' min';
	    } else if ($time >= 3600 && $time < 86400) {
			$hour = date("H", mktime(0, 0, $time));
			$min = date("i", mktime(0, 0, $time));
			if($hour < 10){
				$hour = substr($hour, -1);
			}
	        return $hour.'.'.$min.' hr';
	    } else if ($time >= 86400 && $time) {
			$day = date("j", mktime(0, 0, $time, 0, 0));
			if($day < 10){
				$day = substr($day, -1);
			}
	        return '~'.$day.' days';
	    }
	}

	function hap_convertCount($num){
		if($num == NULL)return '0';
	    if($num < 1000){
	        return $num;
	    } else {
	        return round(($num / 1000), 2).' K';
	    }
	} 

?>

<link rel="stylesheet" type="text/css" href="stats.css?rand=<?php rand(); ?>">

<div class="wrap">

<div class="hap-stats-total">

	<div class="hap-stats-total-inner">
    	<div><p class="hap-stats-total-value"><?php echo(!empty($all_time)? hap_convertTime($all_time) : '0'); ?></p><p class="hap-stats-total-title">Total time played</p></div>
    	<div><p class="hap-stats-total-value"><?php echo hap_convertCount($all_play) ?></p><p class="hap-stats-total-title">Total plays</p></div>
    	<div><p class="hap-stats-total-value"><?php echo hap_convertCount($all_download) ?></p><p class="hap-stats-total-title">Total downloads</p></div>
    	<div><p class="hap-stats-total-value"><?php echo hap_convertCount($all_like) ?></p><p class="hap-stats-total-title">Total likes</p></div>
	</div>
	</div>

	<div class="top-box-wrap">

		<div class="top-box">
			<div class="top-box-inner">
				<div class="top-box-title">
					<h2>TOP PLAYS OF THE DAY</h2>
				</div>
				<hr>
				<?php if(empty($top_day)) : ?>
					<div class="hap-stat-no-data"><p>Data Not Available</p></div>
				<?php else : ?>
					<ol>
					<?php foreach($top_day as $key) : ?>
						<?php if($key['title'] !== '' && $key['artist'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b> - <?php echo(stripcslashes($key['title'])); ?><span class="hap-stat-info">(<?php echo($key['play']); ?>)</span></li>
						<?php elseif($key['title'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['title'])); ?></b></li>
						<?php else : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b></li>
						<?php endif; ?>
					<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		</div>

		<div class="top-box">
			<div class="top-box-inner">
				<div class="top-box-title">
					<h2>TOP PLAYS OF THE WEEK</h2>
				</div>
				<hr>
				<?php if(empty($top_week)) : ?>
					<div class="hap-stat-no-data"><p>Data Not Available</p></div>
				<?php else : ?>
					<ol>
					<?php foreach($top_week as $key) : ?>
						<?php if($key['title'] !== '' && $key['artist'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b> - <?php echo(stripcslashes($key['title'])); ?><span class="hap-stat-info">(<?php echo($key['weekcount']); ?>)</span></li>
						<?php elseif($key['title'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['title'])); ?></b></li>
						<?php else : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b></li>
						<?php endif; ?>
					<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		</div>

		<div class="top-box">
			<div class="top-box-inner">
				<div class="top-box-title">
					<h2>TOP PLAYS OF THE MONTH</h2>
				</div>
				<hr>
				<?php if(empty($top_month)) : ?>
					<div class="hap-stat-no-data"><p>Data Not Available</p></div>
				<?php else : ?>
					<ol>
					<?php foreach($top_month as $key) : ?>
						<?php if($key['title'] !== '' && $key['artist'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b> - <?php echo(stripcslashes($key['title'])); ?><span class="hap-stat-info">(<?php echo($key['monthcount']); ?>)</span></li>
						<?php elseif($key['title'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['title'])); ?></b></li>
						<?php else : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b></li>
						<?php endif; ?>
					<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		</div>

	</div>

	<div class="top-box-wrap">

		<div class="top-box">
			<div class="top-box-inner">
				<div class="top-box-title">
					<h2>TOP PLAYS ALL TIME</h2>
				</div>
				<hr>
				<?php if(empty($top_plays)) : ?>
					<div class="hap-stat-no-data"><p>Data Not Available</p></div>
				<?php else : ?>
					<ol>
					<?php foreach($top_plays as $key) : ?>
						<?php if($key['title'] !== '' && $key['artist'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b> - <?php echo(stripcslashes($key['title'])); ?><span class="hap-stat-info">(<?php echo($key['allcount']); ?>)</span></li>
						<?php elseif($key['title'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['title'])); ?></b></li>
						<?php else : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b></li>
						<?php endif; ?>
					<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		</div>

		<div class="top-box">
			<div class="top-box-inner">
				<div class="top-box-title">
					<h2>TOP DOWNLOADS ALL TIME</h2>
				</div>
				<hr>
				<?php if(empty($top_downloads)) : ?>
					<div class="hap-stat-no-data"><p>Data Not Available</p></div>
				<?php else : ?>
					<ol>
					<?php foreach($top_downloads as $key) : ?>
						<?php if($key['title'] !== '' && $key['artist'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b> - <?php echo(stripcslashes($key['title'])); ?><span class="hap-stat-info">(<?php echo($key['allcount']); ?>)</span></li>
						<?php elseif($key['title'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['title'])); ?></b></li>
						<?php else : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b></li>
						<?php endif; ?>
					<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		</div>

		<div class="top-box">
			<div class="top-box-inner">
				<div class="top-box-title">
					<h2>TOP LIKES ALL TIME</h2>
				</div>
				<hr>
				<?php if(empty($top_likes)) : ?>
					<div class="hap-stat-no-data"><p>Data Not Available</p></div>
				<?php else : ?>
					<ol>
					<?php foreach($top_likes as $key) : ?>
						<?php if($key['title'] !== '' && $key['artist'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b> - <?php echo(stripcslashes($key['title'])); ?><span class="hap-stat-info">(<?php echo($key['allcount']); ?>)</span></li>
						<?php elseif($key['title'] !== '') : ?>
							<li><b><?php echo(stripcslashes($key['title'])); ?></b></li>
						<?php else : ?>
							<li><b><?php echo(stripcslashes($key['artist'])); ?></b></li>
						<?php endif; ?>
					<?php endforeach; ?>
					</ol>
				<?php endif; ?>
			</div>
		</div>

	</div>

<table class='minimalistBlack'>
	<thead>
		<tr>
			<th style="width:30%"><a href="<?php echo $top_level_page."?page_num=".$curr_page ?>">Song</a></th>
			<th style="width:10%"><a href="<?php echo $top_level_page."?order=time&page_num=".$curr_page ?>">Time</a></th>
			<th style="width:10%"><a href="<?php echo $top_level_page."?order=play&page_num=".$curr_page ?>">Plays</a></th>
			<th style="width:10%"><a href="<?php echo $top_level_page."?order=download&page_num=".$curr_page ?>">Downloads</a></th>
			<th style="width:10%"><a href="<?php echo $top_level_page."?order=like&page_num=".$curr_page ?>">Likes</a></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($results as $result) : ?>
			<tr>
				<td>
					<?php if($result['title'] !== '' && $result['artist'] !== '') : ?>
						<b><?php echo(stripcslashes($result['artist'])); ?></b> - <?php echo(stripcslashes($result['title'])); ?>
					<?php elseif($result['title'] !== '') : ?>
						<b><?php echo(stripcslashes($result['title'])); ?></b>
					<?php else : ?>
						<b><?php echo(stripcslashes($result['artist'])); ?></b>
					<?php endif; ?>
				</td>
				<td><?php echo(hap_convertTime($result['time'])); ?></td>
				<td><?php echo(hap_convertCount($result['play'])); ?></td>
				<td><?php echo(hap_convertCount($result['download'])); ?></td>
				<td><?php echo(hap_convertCount($result['like'])); ?></td>
			</tr>
		<?php endforeach; ?>	
	</tbody>		 
</table>

<?php if($total_pages > 1) : ?>
    <ul class="hap-pgn">
    <?php for($i = 1; $i <= $total_pages; $i++) : ?>
        <?php if($i !== $curr_page) : ?>
            <li><a class="hap-pgn-btn" href="<?php echo $top_level_page."?order=$order&page_num=$i" ?>"><?php echo $i ?></a></li>
        <?php else : ?>
            <li><a class="hap-pgn-btn hap-pgn-current"><?php echo $i ?></a></li>
        <?php endif; ?>
    <?php endfor; ?>
    </ul>
<?php endif; ?>

<p class="hap-actions">				
	<a class='button-primary' href='<?php echo $top_level_page."?action=clear_statistics"; ?>' title='Clear Statistics' onclick="return confirm('Are you sure you want to clear all statistics?');">Clear Statistics</a> 
</p> 

</div><!-- end wrap -->

