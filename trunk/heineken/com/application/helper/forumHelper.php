<?php 

class forumHelper {

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
			$campaignName = $campaignName['data'];
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

	

	function forumdaily($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id, date_d, num_posts AS num
					FROM sonar_report_campaign.facebook_daily_post
					WHERE campaign_id IN ({$ids}) {$dateFilter}";
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
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> array('categories'=>$categories,'series'=>$series));
		}
	}

	/*function fbposts($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id, SUM(num_posts) AS num
					FROM sonar_report_campaign.facebook_daily_post
					WHERE campaign_id IN ({$ids})
					{$dateFilter}
					GROUP BY campaign_id";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function fbcomments($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id, SUM(num_comments) AS num
					FROM sonar_report_campaign.facebook_daily_post
					WHERE campaign_id IN ($ids)
					{$dateFilter}
					GROUP BY campaign_id";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function tagcommonthemes($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			$fromLoop = '';
			$sumNum = '';
			$select = '';
			$theRest = '';
			$ids = explode(',', $ids);
			foreach ($ids as $key => $value) {
				if($key==0){
					$select =", IDX{$ids[$key]}.keyword";
					$fromLoop .= "FROM sonar_data_campaign_wordlist.facebook_daily_wordlist_{$value} AS IDX{$ids[$key]} ";
					$sumNum .= "IDX{$ids[$key]}.num";
					if($start_date!=null&&$end_date!=null){ 
						$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
						$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
						$dateFilter = "AND IDX{$ids[$key]}.date_d BETWEEN '{$start_date}' AND '{$end_date}'";
						// $dateFilter = "AND DATE_FORMAT(IDX{$ids[$key]}.date_d,'%d/%m/%Y') BETWEEN '{$start_date}' AND '{$end_date}'";
					}
					$theRest = "WHERE IDX{$ids[$key]}.keyword NOT IN (SELECT keyword FROM sonar_locale.stopword_id_2)
								{$dateFilter} GROUP BY IDX{$ids[$key]}.keyword";
				}else{
					$fromLoop .= "INNER JOIN sonar_data_campaign_wordlist.facebook_daily_wordlist_{$value} AS IDX{$ids[$key]}
					ON IDX{$ids[$key-1]}.date_d = IDX{$ids[$key]}.date_d AND IDX{$ids[$key-1]}.keyword = IDX{$ids[$key]}.keyword ";
					$sumNum .= " + IDX{$ids[$key]}.num";	
				}	
				
			}
			$sql="SELECT SUM($sumNum) AS nums {$select}
					{$fromLoop}
					{$theRest}					
					ORDER BY nums DESC LIMIT 50";
			$rs=$this->apps->fetch($sql,1);

			shuffle($rs);
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function fblikes($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id, SUM(num_likes) AS num
					FROM sonar_report_campaign.facebook_daily_post
					WHERE campaign_id IN ({$ids})
					{$dateFilter}
					GROUP BY campaign_id;";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function fbshares($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id, SUM(num_shares) AS num
					FROM sonar_report_campaign.facebook_daily_post
					WHERE campaign_id IN ({$ids})
					{$dateFilter}
					GROUP BY campaign_id;";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function fbcontentinteraction($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id, post_type, SUM(num_posts) AS num
					FROM sonar_report_campaign.facebook_daily_type_post 
					WHERE campaign_id IN ({$ids})
					{$dateFilter}
					GROUP BY campaign_id, post_type";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			$categories=array();
			foreach ($rs as $key => $value) {
				if(!in_array($value['campaign_id'], $categories))$categories[]=$value['campaign_id'];
				$series[$value['post_type']][]=intval($value['num']);			
			}

			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> array('categories'=>$categories,'series'=>$series));
		}
	}

	function fbfeeds($ids,$start_date=null, $end_date=null, $start=0, $limit=10, $sort_type=null, $sort_status=0){
		global $CONFIG;
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			mysql_query('SET CHARACTER SET utf8');
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "WHERE published_date BETWEEN '{$start_date}' AND '{$end_date}'";
			}

			$sql="SELECT GROUP_CONCAT(B.keyword_id) AS ids FROM sonar_data.tbl_campaign_keyword AS B
					WHERE B.campaign_id IN ({$ids}) AND B.keyword_type = 3";
			$keyids=$this->apps->fetch($sql);
			//pr($sql);exit;
			$fromLoop = '';
			
			//Get keyword on each group
			$sql="SELECT keywords FROM sonar_data.tbl_campaign
					WHERE id IN ({$ids})";
			$rs = $this->apps->fetch($sql,1);

			foreach ($rs as $key => $value) {
				$keywords .= $value['keywords'];
			}
			$keywords = explode(',', $keywords);
			//pr($keywords);exit;
			$ids = explode(',', $keyids['ids']);
			
			foreach ($ids as $key => $value) {
				if($key!=0) $fromLoop .= " UNION ALL ";
				$fromLoop .= "SELECT id, object_id, author_id, 
							author_name, message, num_likes, 
							num_comments, num_shares, sentiment_value, post_type, DATE_FORMAT(FROM_UNIXTIME(created_time_ts),'%d/%m/%Y %H:%s:%i') as created_time
							FROM sonar_data_keyword.feeds_facebook_{$value} 
							{$dateFilter}";
			}
			$sql="SELECT COUNT(id) AS total_rows
				FROM (
					{$fromLoop}
				) AS A";
			$total_row = $this->apps->fetch($sql);

			//paging attribute
			$next=false;
			$prev=false;
			$index=$start;
			$next_start = $start+1;
			$prev_start = $start-1;
			$start=$start*$limit;
			$isAvailable = $total_row['total_rows']-$start;
			if($start>0)$prev="{$CONFIG['API_DOMAIN']}facebook/feeds/{$prev_start}/{$limit}";
			if($start<$total_row['total_rows']&&$isAvailable>$limit)$next="{$CONFIG['API_DOMAIN']}facebook/feeds/{$next_start}/{$limit}";

			if($sort_type==null) $sort_type="num_comments";

			if($sort_status>0) $sort_status="ASC";
			else $sort_status="DESC";

			$sortFilter="ORDER BY {$sort_type} {$sort_status}";

			$sql = "SELECT id, object_id, author_id, 
						author_name,CONCAT(SUBSTRING(message, 1, 200), \" ...\") AS message, num_likes, 
						num_comments, num_shares, sentiment_value, post_type, created_time
					FROM (
					    {$fromLoop}
					) AS A
					{$sortFilter}
					LIMIT {$start}, {$limit}";

			$rs = $this->apps->fetch($sql,1);
			
			
				if($rs){
					foreach ($rs as $key => $value) {					
						foreach ($keywords as $k => $v) {
							$rs[$key]['message'] =  preg_replace('/(?<![\>\<\/])'.trim($v).'|(?<=\<\/b\>)'.trim($v).'/i', '<b style="font-weight:bold;">$0</b>',$rs[$key]['message']);
						}
						
						$rs[$key]['message'] = $this->autolink($rs[$key]['message']);
					}
				}
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK',
								'data'=> array('list'=>$rs,
												'details'=>array(
													'total_rows'=>$total_row['total_rows'],
													'prev'=>$prev,
													'next'=>$next,
													'current_page'=>$start+1,
													'current_limit'=>($start+$limit),
													'index'=>$index+1,
													'limit'=>$limit
												)
								)
							);
		}
	}

	function fbpersonals($ids,$start_date=null, $end_date=null, $start=0, $limit=10, $sort_type=null, $sort_status=0){
		global $CONFIG;
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			mysql_query('SET CHARACTER SET utf8');
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "WHERE published_date BETWEEN '{$start_date}' AND '{$end_date}'";
			}

			$sql="SELECT GROUP_CONCAT(B.keyword_id) AS ids FROM sonar_data.tbl_campaign_keyword AS B
					WHERE B.campaign_id IN ({$ids}) AND B.keyword_type = 4";
			$keyids=$this->apps->fetch($sql);
			//pr($sql);exit;
			$fromLoop = '';

			//Get keyword on each group
			$sql="SELECT keywords FROM sonar_data.tbl_campaign
					WHERE id IN ({$ids})";
			$rs = $this->apps->fetch($sql,1);

			foreach ($rs as $key => $value) {
				$keywords .= $value['keywords'];
			}
			$keywords = explode(',', $keywords);

			$ids = explode(',', $keyids['ids']);
			
			foreach ($ids as $key => $value) {
				if($key!=0) $fromLoop .= " UNION ALL ";
				$fromLoop .= "SELECT id, object_id, author_id, 
							author_name, message, num_likes, 
							num_comments, num_shares, sentiment_value, post_type, DATE_FORMAT(FROM_UNIXTIME(created_time_ts),'%d/%m/%Y %H:%s:%i') as created_time
							FROM sonar_data_keyword.feeds_facebook_{$value} 
							{$dateFilter}";
			}
			$sql="SELECT COUNT(id) AS total_rows
				FROM (
					{$fromLoop}
				) AS A";
			$total_row = $this->apps->fetch($sql);

			//paging attribute
			$next=false;
			$prev=false;
			$index=$start;
			$next_start = $start+1;
			$prev_start = $start-1;
			$start=$start*$limit;
			$isAvailable = $total_row['total_rows']-$start;
			if($start>0)$prev="{$CONFIG['API_DOMAIN']}facebook/feeds/{$prev_start}/{$limit}";
			if($start<$total_row['total_rows']&&$isAvailable>$limit)$next="{$CONFIG['API_DOMAIN']}facebook/feeds/{$next_start}/{$limit}";

			if($sort_type==null) $sort_type="num_comments";

			if($sort_status>0) $sort_status="ASC";
			else $sort_status="DESC";

			$sortFilter="ORDER BY {$sort_type} {$sort_status}";

			$sql = "SELECT id, object_id, author_id, 
						author_name,CONCAT(SUBSTRING(message, 1, 200), \" ...\") AS message, num_likes, 
						num_comments, num_shares, sentiment_value, post_type, created_time
					FROM (
					    {$fromLoop}
					) AS A
					{$sortFilter}
					LIMIT {$start}, {$limit}";
			//pr($sql);exit;
			$rs = $this->apps->fetch($sql,1);
			
				if($rs){
					foreach ($rs as $key => $value) {					
						foreach ($keywords as $k => $v) {
							$rs[$key]['message'] =  preg_replace('/(?<![\>\<\/])'.trim($v).'|(?<=\<\/b\>)'.trim($v).'/i', '<b style="font-weight:bold;">$0</b>',$rs[$key]['message']);
						}
						
						$rs[$key]['message'] = $this->autolink($rs[$key]['message']);
					}
				}
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK',
								'data'=> array('list'=>$rs,
												'details'=>array(
													'total_rows'=>$total_row['total_rows'],
													'prev'=>$prev,
													'next'=>$next,
													'current_page'=>$start+1,
													'current_limit'=>($start+$limit),
													'index'=>$index+1,
													'limit'=>$limit
												)
								)
							);
		}
	}*/

}

?>

