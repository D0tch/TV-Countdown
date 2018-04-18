<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title>TvCountdown</title>
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
		<link href="style.css" rel="stylesheet">
		<link href="https://use.fontawesome.com/releases/v5.0.9/css/all.css" rel="stylesheet">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js"></script>	
	</head>
	<body>	
		
		<div class="container-fluid" style="padding-top: 10px;">
			<div class="row">
<?php
	
	$shows = ['greys-anatomy' , 'silicon-valley' , 'homeland' , 'suits' , 'Designated-Survivor' , 'New-Girl' , 'The-Simpsons'];
	
	$countdown_wrapper = array();
	
	foreach($shows as $show): 
	
		$info = json_decode(file_get_contents('https://api.tvmaze.com/search/shows?q='.$show), true);
		$title = $info[0]['show']['name'];
		$poster_url = $info[0]['show']['image']['original'];
		$poster_url = substr_replace($poster_url, 's', 4, 0);
	 	$next_episode_link = $info[0]['show']['_links']['nextepisode']['href'];
		$episode_info = json_decode(file_get_contents($next_episode_link), true);	
		$next_episode_name = $episode_info['name'];
		$next_episode_summary = $episode_info['summary'];
		$next_episode_air_date = strtotime($episode_info['airstamp']);
		$season = $episode_info['season'];
		$episode = $episode_info['number'];
		
		
		$countdown_wrapper[$next_episode_air_date] = [
			'next_episode_airtime' => $next_episode_air_date , 
			'title' => $title , 
			'poster_url' => $poster_url , 
			'next_episode_name' => $next_episode_name , 
			'next_episode_summary' => $next_episode_summary,
			'season' => $season,
			'episode' => $episode,
		];
	
	endforeach;

	sort($countdown_wrapper);
	
	foreach($countdown_wrapper as $countdown):

		$img = $countdown['poster_url'];

?>
	
	
	
	<div class="col-md-2 col-sm-12" style="height: 450px !important; min-height: 450px; margin-bottom: 8px;">
		<div class="card bg-dark text-white"  style="height: 450px !important; min-height: 450px;">
		  <div class="card-img-overlay" style="background-image: url('<?php echo $img; ?>'); background-size: cover;">
		  		  <div style="position: absolute; top:0; left:0; width:100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 1">
		  		  </div>
		  		  <div style="z-index: 10;">
				    <h3><b><?php echo $countdown['title']; ?></b></h3>
				    <h5 class="card-title"><b><?php echo $countdown['next_episode_name'] . ' (S'.$countdown['season'].'E'.$countdown['episode'].')'; ?></b></h5> 
				    <p class="card-text"><?php echo $countdown['next_episode_summary']; ?></p>
					<p class="card-text" style="bottom: 20px; position: absolute;">
					<?php if($countdown['next_episode_airtime'] < time() + 86400 ){
							$secs_to_air = $countdown['next_episode_airtime'] - time();
							echo 'Airs in: ' . floor($secs_to_air / 60 / 60) .  ' Hours'; 
						}else{
							echo 'Airs: '. date('d-m-Y H:i' , $countdown['next_episode_airtime']); 
						} ?>
					</p>
		  		  </div>
		  </div>	
		</div>
	</div>

	<?php endforeach; ?>
			</div>
		</div>
	</body>
</html>