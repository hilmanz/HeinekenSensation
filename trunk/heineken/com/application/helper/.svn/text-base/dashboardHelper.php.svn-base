<?php 

class dashboardHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)){
				$this->uid = intval($this->apps->user->id);	
			}
		}
	}

	function swapCampaignIDtoName($rs=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if(!$rs){return array('status'=>0,'message'=>"No data found.");exit;}

			$campaignName = $this->apps->widgetHelper->getCampaignName();
			$campaignName = $campaignName[data];
			foreach ($rs as $key => $value) {
				$rs[$key]['campaign_id'] = ucwords(strtolower($campaignName[$value['campaign_id']]));
			}
			return $rs;
		}
	}
	function autolink($string){
		$content_array = explode(" ", $string);
		$output = '';

		foreach($content_array as $content)
		{
		//starts with http://
		if(substr($content, 0, 7) == "http://")
		$content = '<a style="color:#3399FF" target="_blank" href="' . $content . '">' . $content . '</a>';

		//starts with www.
		if(substr($content, 0, 4) == "www.")
		$content = '<a style="color:#3399FF" target="_blank" href="http://' . $content . '">' . $content . '</a>';

		$output .= " " . $content;
		}

		$output = trim($output);
		return $output;
	}
	function hackDateGapofDailyChart($results,$customLimit,$from,$to,$arrLabel){
			if($from==null&&$to==null){
				$customLimit = true;
			}
			//To make data start and/or end from the date that was given
			$n_size = sizeof($results);
			
			//To fix the gap between date
			$n_size = sizeof($results); //recalculate n_size
			$start_over = 0;
			if($n_size>0){
				foreach($results as $n=>$rs){		
					$results[$n]['ts'] = strtotime($rs['date_d']);
				}
				$results = subval_sort($results, 'ts');
				//pr($results);exit;
				for($i=0;$i<$n_size;$i++){
					$rs = $results[$i];
					if($i>0){						
						$curr_ts = strtotime($rs['date_d']);
						$last_ts = strtotime($results[$i-1]['date_d']);
						$diff_ts = $curr_ts - $last_ts;
						if($diff_ts>(60*60*24)){				
							$n_days = ceil($diff_ts/(60*60*24));
							
							while($n_days>1){
								$hasil[$start_over]['date_d'] = date("Y-m-d",$curr_ts-(($n_days-1)*60*60*24));
								foreach($arrLabel as $l){
									$hasil[$start_over][$l]=0;
								}
								$start_over++;
								$n_days--;
							}				
						}
					}
					
					$hasil[$start_over]['date_d'] = $rs['date_d'];
					foreach($arrLabel as $l){
						$hasil[$start_over][$l]=(($rs[$l]>0)?$rs[$l]:0);
					}
					$start_over++;
				}
			}

			return $hasil;
	}

	function getdailybuzz($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{

			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";

			}
			
			$sql = "SELECT campaign_id, date_d, SUM(num_posts) AS num FROM
					(
						SELECT campaign_id, date_d, SUM(num_posts) AS num_posts FROM sonar_report_campaign.facebook_daily_post
						WHERE campaign_id IN ({$ids}) {$dateFilter}
						GROUP BY campaign_id, date_d
						UNION ALL
						SELECT campaign_id, date_d, SUM(num_posts) AS num_posts FROM sonar_report_campaign.twitter_daily_post
						WHERE campaign_id IN ({$ids}) {$dateFilter}
						GROUP BY campaign_id, date_d
						UNION ALL 
						SELECT campaign_id, date_d, SUM(num_posts) AS num_posts FROM sonar_report_campaign.news_daily_post
						WHERE campaign_id IN ({$ids}) {$dateFilter}
						GROUP BY campaign_id, date_d
					) AS B
					GROUP BY campaign_id, date_d";	
			
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			
			$campaign_name = array();
			$campaign_date = array();
			foreach ($rs as $key => $value) {
				if(!in_array($value['campaign_id'], $campaign_name)) $campaign_name[]=$value['campaign_id'];
				if(!in_array($value['date_d'], $campaign_date)) $campaign_date[]=$value['date_d'];
				
				$data[$value['date_d']][$value['campaign_id']]=$value['num'];
			}
			
			foreach ($campaign_date as $key => $value) {
				foreach ($campaign_name as $k => $v) {
					$hackDate[$key][$v]=$data[$value][$v];
				}
				$hackDate[$key]['date_d']=$value;
			}
			
			$data = $this->hackDateGapofDailyChart($hackDate,false,$start_date,$end_date,$campaign_name);
			
			foreach ($data as $key => $value) {
				$categories[]=date('d/m/Y',strtotime($value['date_d']));
				foreach ($campaign_name as $k => $v) {
					$temp[$v][]=intval($value[$v]);
				}
				
			}
			foreach ($temp as $key => $value) {
				$series[]=array('name'=>$key,'data'=>$value);
			}
			/*pr($categories);
			pr($series);exit;*/


			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> array('categories'=>$categories,'series'=>$series));
		}
	}

	function getengagement($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";			
			}
			$sql = "SELECT campaign_id, SUM(num) AS num FROM (
					    SELECT campaign_id, SUM(num_comments) AS num FROM sonar_report_campaign.facebook_daily_post
					    WHERE campaign_id IN ({$ids}) {$dateFilter}
					    GROUP BY campaign_id
					    UNION ALL
					    SELECT campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.twitter_daily_post
					    WHERE campaign_id IN ({$ids}) {$dateFilter} AND status_type > 0
					    GROUP BY campaign_id
					    UNION ALL
					    SELECT campaign_id, SUM(num_posts + num_comments) AS num FROM sonar_report_campaign.news_daily_post
					    WHERE campaign_id IN ({$ids}) {$dateFilter}
					    GROUP BY campaign_id
					    UNION ALL
					    SELECT campaign_id, SUM(num_replies) AS num FROM sonar_report_campaign.forum_post
					    WHERE campaign_id IN ({$ids})
					    GROUP BY campaign_id
					) AS A
					GROUP BY campaign_id";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function getpostshareofvoice($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";			
			}
			$sql="SELECT campaign_id, SUM(num) AS num FROM (
				    SELECT campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.facebook_daily_post
				    WHERE campaign_id IN ({$ids}) {$dateFilter}
				    GROUP BY campaign_id
				    UNION ALL
				    SELECT campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.twitter_daily_post
				    WHERE campaign_id IN ({$ids}) {$dateFilter}
				    GROUP BY campaign_id
				    UNION ALL
				    SELECT campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.news_daily_post
				    WHERE campaign_id IN ({$ids}) {$dateFilter}
				    GROUP BY campaign_id
				    UNION ALL
				    SELECT campaign_id, SUM(num_threads) AS num FROM sonar_report_campaign.forum_post
				    WHERE campaign_id IN ({$ids})
				    GROUP BY campaign_id
				) AS A
				GROUP BY campaign_id";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);	
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function gettopengagement($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND published_date BETWEEN '{$start_date}' AND '{$end_date}'";			
			}
			$ids=explode(',', $ids);
			
			// twitter
			$num_rt=array();
			foreach ($ids as $key => $value) {
				/*$sql=" SELECT rted_feed_id,num_retweets, {$value} as campaign_id
			            FROM sonar_data_campaign.rt_count_{$value}
			            ORDER BY num_retweets DESC LIMIT 1";*/
			    $sql="SELECT num_rts AS rt, link, content, author_name, author_link, author_avatar, 
						DATE_FORMAT(published_datetime,'%d/%m/%Y') AS published_datetime
						FROM sonar_data_campaign.feeds_twitter_{$value}
						WHERE 1 {$dateFilter}
						ORDER BY num_rts DESC
						LIMIT 1";
			    $rs=$this->apps->fetch($sql);
			    //var_dump($sql);exit;
			    if($rs){
			    	$num_rt[$rs['rt']]=$rs;
				}
			}
			//pr($num_rt);
			$rs = max($num_rt);
			//pr($value);exit;
			/*$key = array_search($value, $num_rt);
			$split = explode('~', $key);
			$feed_id = $split[0];
			$campaign_id = $split[1];*/
			
			/*$sql="SELECT {$value} AS rt, link, content, author_name, author_link, author_avatar, DATE_FORMAT(published_datetime,'%d/%m/%Y') AS published_datetime
        			FROM sonar_data_campaign.feeds_twitter_{$campaign_id}
        			WHERE feed_id = {$feed_id} LIMIT 1";
        	$rs=$this->apps->fetch($sql);*/
        	if($rs) $rs['content'] = $this->autolink($rs['content']);
        	$top['twitter']=$rs;

        	//facebook
        	$fb_max=array();
        	foreach ($ids as $key => $value) {
        		/*$sql="SELECT num_comments, {$value} AS campaign_id, object_id AS id
            			FROM sonar_data_campaign.feeds_facebook_{$value}
						ORDER BY num_comments DESC LIMIT 1";*/
				$sql="SELECT num_comments, {$value} AS campaign_id, object_id AS id
						FROM sonar_data_campaign.feeds_facebook_{$value}
						WHERE 1 {$dateFilter}
						ORDER BY num_comments DESC LIMIT 1";
				$rs=$this->apps->fetch($sql);
				$fb_max[$rs['id']."~".$rs['campaign_id']]=$rs['num_comments'];
        	}
        	$value = max($fb_max);
			$key = array_search($value, $fb_max);
			$split = explode('~', $key);
			$feed_id = $split[0];
			$campaign_id = $split[1];
			/*$sql=" SELECT object_id, author_id, author_name, CONCAT(SUBSTRING(message, 1, 100), \" ...\") AS message, DATE_FORMAT(FROM_UNIXTIME(created_time_ts),'%d/%m/%Y') AS publised_datetime, num_comments
	            FROM sonar_data_campaign.feeds_facebook_{$campaign_id}
	            WHERE object_id='{$feed_id}' LIMIT 1";*/

	        $sql="SELECT *, {$campaign_id} AS campaign_id,CONCAT(SUBSTRING(message, 1, 100), \" ...\") AS message, DATE_FORMAT(FROM_UNIXTIME(created_time_ts),'%d/%m/%Y') AS publised_datetime
					FROM sonar_data_campaign.feeds_facebook_{$campaign_id}
					WHERE 1 {$dateFilter}
					ORDER BY num_comments DESC LIMIT 1";
	        $top['facebook']=$this->apps->fetch($sql);
	       	//pr($sql);exit;
	        //forum
        	$fo_max=array();
        	foreach ($ids as $key => $value) {
        		/*$sql="SELECT id, num_replies, {$value} AS campaign_id
						FROM sonar_data_campaign.feeds_forum_{$value}
            			ORDER BY num_replies DESC LIMIT 1";*/

            	$sql="SELECT id, num_replies, {$value} AS campaign_id
						FROM sonar_data_campaign.feeds_forum_model
						WHERE 1 {$dateFilter}
						ORDER BY num_replies DESC LIMIT 1";
				$rs=$this->apps->fetch($sql);
				$fo_max[$rs['num_replies']]=$rs;
        	}
        	$value = max($fo_max);
        	$top['forum']=$rs;
			/*$key = array_search($value, $fo_max);
			$split = explode('_', $key);
			$feed_id = $split[0];
			$campaign_id = $split[1];
			$sql=" SELECT link,title, media_id, media_name, DATE_FORMAT(published_date,'%d/%m/%Y') AS published_date, num_replies
		            FROM sonar_data_campaign.feeds_forum_{$campaign_id}
		            WHERE id={$feed_id} LIMIT 1";
	        $top['forum']=$this->apps->fetch($sql);*/

	         //news
        	$news_max=array();
        	foreach ($ids as $key => $value) {
        		$sql="SELECT id, comments_count, {$value} AS campaign_id
		            FROM sonar_data_campaign.feeds_news_{$value}
		            ORDER BY comments_count DESC LIMIT 1;";
				$rs=$this->apps->fetch($sql);
				$news_max[$rs['id']."_".$rs['campaign_id']]=$rs['comments_count'];
        	}
        	$value = max($news_max);
			$key = array_search($value, $news_max);
			$split = explode('_', $key);
			$feed_id = $split[0];
			$campaign_id = $split[1];
			$sql="SELECT link,title, media_id, media_name, DATE_FORMAT(published_date,'%d/%m/%Y') AS published_date, comments_count
            		FROM sonar_data_campaign.feeds_news_{$campaign_id}
		            WHERE id={$feed_id} LIMIT 1";
	        $top['news']=$this->apps->fetch($sql);
			

			if(!$top) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $top);
		}
	}

	function getpostofchannel($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";			
			}
			$sql="SELECT 'fb' AS channel, campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.facebook_daily_post
				    WHERE campaign_id IN ({$ids}) {$dateFilter}
				    GROUP BY campaign_id
				    UNION ALL
				    SELECT 'tw' AS channel, campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.twitter_daily_post
				    WHERE campaign_id IN ({$ids}) {$dateFilter}
				    GROUP BY campaign_id
				    UNION ALL
				    SELECT 'nw' AS channel, campaign_id, SUM(num_posts) AS num FROM sonar_report_campaign.news_daily_post
				    WHERE campaign_id IN ({$ids}) {$dateFilter}
				    GROUP BY campaign_id
				    UNION ALL
				    SELECT 'fo' AS channel, campaign_id, SUM(num_threads) AS num FROM sonar_report_campaign.forum_post
				    WHERE campaign_id IN ({$ids})
				    GROUP BY campaign_id";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			foreach ($rs as $key => $value) {
				switch($value['channel']){
					case 'fb':
						$data['Facebook'][$value['campaign_id']]=intval($value['num']);
						break;
					case 'tw':
						$data['Twitter'][$value['campaign_id']]=intval($value['num']);
						break;
					case 'nw':
						$data['News'][$value['campaign_id']]=intval($value['num']);
						break;
					case 'fo':
						$data['Forum'][$value['campaign_id']]=intval($value['num']);
						break;
					default:
				}
			}

			foreach ($data as $key => $value) {
				$categories[]=$key;
				foreach ($value as $ky => $val) {
					$series[$ky][]=$val;
				}
			}
			//pr($series);exit;
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> array('categories'=>$categories,'series'=>$series));
		}
	}

	function getsetimentonchannel($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";			
			}
			/*$sql="SELECT 'fb' AS channel,sentiment_type, SUM(num_posts) AS num
					FROM sonar_report_campaign.facebook_daily_sentiment
					{$dateFilter}
					GROUP BY sentiment_type
					UNION ALL
					SELECT 'fo' AS channel, sentiment_type, SUM(num_threads) AS num
					FROM sonar_report_campaign.forum_media_post
					{$dateFilter}
					GROUP BY sentiment_type
					UNION ALL
					SELECT 'nw' AS channel, sentiment_type, SUM(num_posts) AS num
					FROM sonar_report_campaign.news_daily_sentiment
					{$dateFilter}
					GROUP BY sentiment_type
					UNION ALL
					SELECT 'tw' AS channel, sentiment_type, SUM(num_posts) AS num
					FROM sonar_report_campaign.twitter_daily_post
					{$dateFilter}	
					GROUP BY sentiment_type";*/
			$sql="SELECT campaign_id, 'fb' AS channel, sentiment_type, SUM(num_posts) AS num
				FROM sonar_report_campaign.facebook_daily_sentiment
				WHERE campaign_id IN ({$ids}) {$dateFilter}
				GROUP BY campaign_id, sentiment_type
				UNION ALL
				SELECT campaign_id, 'fo' AS channel, sentiment_type, SUM(num_threads) AS num
				FROM sonar_report_campaign.forum_media_post
				WHERE campaign_id IN ({$ids})
				GROUP BY campaign_id, sentiment_type
				UNION ALL
				SELECT campaign_id, 'nw' AS channel, sentiment_type, SUM(num_posts) AS num
				FROM sonar_report_campaign.news_daily_sentiment
				WHERE campaign_id IN ({$ids}) {$dateFilter}
				GROUP BY campaign_id, sentiment_type
				UNION ALL
				SELECT campaign_id, 'tw' AS channel, sentiment_type, SUM(num_posts) AS num
				FROM sonar_report_campaign.twitter_daily_post
				WHERE campaign_id IN ({$ids}) {$dateFilter}
				GROUP BY campaign_id, sentiment_type";
			
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			//pr($rs);exit;
			foreach ($rs as $key => $value) {
				switch($value[sentiment_type]){
					case '-1':
						$sentiment="Negative";
						break;
					case '1':
						$sentiment="Positive";
						break;
					case '0':
						$sentiment="Neutral";
						break;
					default:
						$sentiment="Neutral";
				}
				switch($value[channel]){
					case 'fb':
						$channel='Facebook';
						break;
					case 'tw':
						$channel='Twitter';
						break;
					case 'nw':
						$channel='News';
						break;
					case 'fo':
						$channel='Forum';
						break;
					default:
				}

				$data[$channel][$value['campaign_id']][$sentiment]=intval($value['num']);
			}
			//pr($data);exit;
			foreach ($data as $key => $value) {
				$categories[]=$key;
				foreach ($value as $ky => $val) {
					$temp[$ky]['Positive'][]=array('y'=>$val['Positive'],'name'=>$ky);
					$temp[$ky]['Negative'][]=array('y'=>$val['Negative'],'name'=>$ky);
					$temp[$ky]['Neutral'][]=array('y'=>$val['Neutral'],'name'=>$ky);
				}
			}

			foreach ($temp as $key => $value) {
				$series[]=array('stack'=>$key,'name'=>'Positive','data'=>$value['Positive'],'color'=>'#64bf0a');
				$series[]=array('stack'=>$key,'name'=>'Negative','data'=>$value['Negative'],'color'=>'#e12626');
				$series[]=array('stack'=>$key,'name'=>'Neutral','data'=>$value['Neutral'],'color'=>'#999999');
			}
			//pr($series);exit;
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> array('categories'=>$categories,'series'=>$series));
		}
	}
	
}

?>

