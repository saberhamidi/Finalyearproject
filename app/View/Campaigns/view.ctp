<?php
	if($campaign['Campaign']['type'] == 'Re-tweeting Promotion' && $campaign['Campaign']['deployed']){
		require_once('app/Plugin/twitter-api/TwitterAPIExchange.php');
		$settings = array(
	    'oauth_access_token' => "3004107267-aPH4YiV9w6EQC1JOtrKspXtozjrki4l13RWBhFi",
	    'oauth_access_token_secret' => "0TuTv5xpZmXlDsdYhlp8mMOaBebK5GttniYjeiFGz8ebY",
	    'consumer_key' => "od1wruB6EJVCvm6THSolSuuY1",
	    'consumer_secret' => "XQ7NRinqQBt5Pb0gBossOXvPuvsrH5R7vSw7UGu9SV2CjRLJjQ"
		);

		$url ="https://api.twitter.com/1.1/search/tweets.json";
		$getfield = '?q='.$campaign['Campaign']['hashtag'].',&count=100';

		$twitter = new TwitterAPIExchange($settings);
		$tweets =  $twitter->setGetfield($getfield)
	    ->buildOauth($url, 'GET')
	    ->performRequest();

		$tweets = json_decode($tweets);
		$tweets = $tweets->statuses;

	}
?>
<div class="campaigns view">
<h2><?php echo __('Campaign'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Type'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['type']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Start Date'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['start_date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Expire Date'); ?></dt>
		<dd>
			<?php echo h($campaign['Campaign']['expire_date']); ?>
			&nbsp;
		</dd>
		<?php
			if($campaign['Campaign']['type'] == 'Re-tweeting Promotion'){
				echo "<dt>".__('Hashtag')."</dt><dd>";
				echo h($campaign['Campaign']['hashtag']);
				echo "</dd>";
			}
		?>
	</dl>

<!--Display the time left till the campaign expire date-->
<?php
	if($campaign['Campaign']['deployed']){
		$now = new DateTime('now');
		$expire = new DateTime($campaign['Campaign']['expire_date']);
		$d = date_diff($now, $expire);
		$timeLeftToExpire = ($d->m*43200)+($d->d*1440)+($d->i*60)+($d->i);
?>
<br>
<div class = 'timer'>
	<p>Time left to expire</p>
	<div class="clock" style="margin:2em;"></div>
	<script type="text/javascript">
		var clock;
		$(document).ready(function() {
			var time = '<?php echo $timeLeftToExpire?>'*60;
			clock = $('.clock').FlipClock(time, {
			clockFace: 'dailyCounter',
			countdown: true,
			autoStart: false,
			});
			clock.start();
		});
	</script>
	<br><br>
</div>

<!--Not to be removed-->
<?php 
	}
?>

<!--Popular tweets for this campaign-->
<?php if($campaign['Campaign']['type'] == 'Re-tweeting Promotion' && $campaign['Campaign']['deployed'] && count($tweets) >=1): ?>
	<div id ='topTweets'>
		<br>
		<h5 style="margin-left: 80px; font-size: 18px;">Current Highest Re-tweeted</h5>
		<br>
		<?php 

			$topTweet = $tweets[0];

			for($i =0; $i<count($tweets); $i++){
				if($tweets[$i]->retweet_count > $topTweet->retweet_count){
					$topTweet = $tweets[$i];
				}
			}

			$twitterLink = strrchr($topTweet->text, 'http');
			$topTweet->text = str_replace($twitterLink, '', $topTweet->text);
		?>
		<div class="box4">
	       <h1><?php echo $topTweet->user->name;?></h1>   
	       <img src=<?php if(isset($topTweet->entities->media))echo $topTweet->entities->media[0]->media_url?>>
	        <p><?php echo $topTweet->text; ?></p> 
	       <br />
	       <a href="<?php echo $twitterLink;?>">Twitter</a>
		</div>

		<br>
	</div>

<?php endif; ?>



<!--Compaign Deployment button-->
<div>
	<?php echo $this->Form->create(['action' => '/deploy/'.$campaign['Campaign']['id']]); 
		if($campaign['Campaign']['deployed'])
			echo $this->Form->end(array('label'=>'Deployed', 'disabled' =>true, 'class'=>'disabled'));
		else
			echo $this->Form->end('Deploy');
		echo "<a href='/pages/campaign".$campaign['Campaign']['id']."''>Preview Contents</a>";
	?>
</div>
<br>
	<h2><?php echo __('Participants'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th>Email</th>
			<th>First Name</th>
			<th>Last Name</th>
			<?php
				if($campaign['Campaign']['type'] == "Re-tweeting Promotion"){
					echo "<th>Number of Re-tweets</th>";
				}
				else{
					echo "<th>Loyalty Balance</th>";
				}
			?>
	</tr>
	</thead>
	<?php foreach ($participants as $participant): ?>
		<tr>
			<td><?php echo h($participant['email']); ?>&nbsp;</td>
			<td><?php echo h($participant['firstname']); ?>&nbsp;</td>
			<td><?php echo h($participant['lastname']); ?>&nbsp;</td>
			<td><?php 
			if($campaign['Campaign']['type'] != 'Re-tweeting Promotion'){
				echo h($participant['loyalty_balance']);
			}

			elseif($campaign['Campaign']['deployed'] && $campaign['Campaign']['type'] == 'Re-tweeting Promotion' && count($tweets) >= 1){
				$tweeted =false;
				for($i = 0; $i<count($tweets); $i++){
					if($tweets[$i]->user->screen_name == $participant['twitter_id']){
						echo h($tweets[$i]->retweet_count);
						$tweeted =true;

					}

				}
				if(!$tweeted){
					echo h("Haven't Tweeted Yet");
				}
			}

			?>&nbsp;</td>

		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>

</div>

<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Campaign'), array('action' => 'edit', $campaign['Campaign']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Campaign'), array('action' => 'delete', $campaign['Campaign']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $campaign['Campaign']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Campaigns'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Campaign'), array('action' => 'add')); ?> </li>
	</ul>
</div>
