<?php 

class mediaHelper {

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
	

	function articlesonmedia($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT date_d,  campaign_id,  SUM(num_posts) AS num
					FROM sonar_report_campaign.news_daily_post
					WHERE campaign_id IN ({$ids}) {$dateFilter}
					GROUP BY date_d, campaign_id";
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
	function articlepost($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id,  SUM(num_posts) AS num
					FROM sonar_report_campaign.news_daily_post
					WHERE campaign_id IN ({$ids}) {$dateFilter}
					GROUP BY campaign_id";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}
	}

	function articlecomment($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$sql = "SELECT campaign_id,  SUM(num_comments) AS num
					FROM sonar_report_campaign.news_daily_post
					WHERE campaign_id IN ({$ids}) {$dateFilter}
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
					$fromLoop .= "FROM sonar_data_campaign_wordlist.news_daily_wordlist_{$value} AS IDX{$ids[$key]} ";
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
					$fromLoop .= "INNER JOIN sonar_data_campaign_wordlist.news_daily_wordlist_{$value} AS IDX{$ids[$key]}
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

	function mediasentiment($ids, $start_date=null, $end_date=null){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "AND date_d BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			/*$sql = "SELECT campaign_id, sentiment_type, SUM(num_posts) AS num
					FROM sonar_report_campaign.news_daily_sentiment
					WHERE campaign_id IN ({$ids}) {$dateFilter}
					GROUP BY campaign_id, sentiment_type";*/
			$sql="SELECT campaign_id, media_id, media_name, sentiment_type, SUM(num_posts) AS num
					FROM sonar_report_campaign.news_daily_sentiment AS A
					INNER JOIN web_collector.media AS B
					ON A.media_id = B.id
					WHERE campaign_id IN ({$ids}) {$dateFilter}
					GROUP BY campaign_id, media_id, sentiment_type";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			$categories=array();
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

				$data[$value['campaign_id']][$value['media_name']][$sentiment]=intval($value['num']);
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
			//pr($rs);exit;
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> array('categories'=>$categories,'series'=>$series));
		}
	}
	function mediapost($ids, $start_date=null, $end_date=null, $start=0, $limit=10, $sort_type=null, $sort_status=0){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			mysql_query('SET CHARACTER SET utf8');
			if($start_date!=null&&$end_date!=null){ 
				$start_date = date('Y-m-d',strtotime(str_replace('/', '-', $start_date)));
				$end_date = date('Y-m-d',strtotime(str_replace('/', '-', $end_date)));
				$dateFilter = "WHERE published_date BETWEEN '{$start_date}' AND '{$end_date}'";
				//$dateFilter = "WHERE DATE_FORMAT(published_date,'%d/%m/%Y') BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			$fromLoop = '';
			
			//Get keyword on each group
			$sql="SELECT keywords FROM sonar_data.tbl_campaign
					WHERE id IN ({$ids})";
			$rs = $this->apps->fetch($sql,1);

			foreach ($rs as $key => $value) {
				$keywords .= $value['keywords'];
			}
			$keywords = explode(',', $keywords);
			
			$ids = explode(',', $ids);

			foreach ($ids as $key => $value) {
				if($key!=0) $fromLoop .= " UNION ALL ";
				$fromLoop .= "SELECT id, link, media_name, title,  summary,  
					      DATE_FORMAT(published_date,'%d/%m/%Y') published_date,  comments_count, sentiment_value
					    FROM sonar_data_campaign.feeds_news_{$value}
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
			if($start>0)$prev="{$CONFIG['API_DOMAIN']}media/feeds/{$prev_start}/{$limit}";
			if($start<$total_row['total_rows']&&$isAvailable>$limit)$next="{$CONFIG['API_DOMAIN']}media/feeds/{$next_start}/{$limit}";

			if($sort_type==null) $sort_type="comments_count";

			if($sort_status>0) $sort_status="ASC";
			else $sort_status="DESC";

			$sortFilter="ORDER BY {$sort_type} {$sort_status}";
			
			$sql = "SELECT id, link, media_name, title, CONCAT(SUBSTRING(summary, 1, 200), \" ...\") AS summary,  
					published_date,  comments_count, sentiment_value
					FROM (
					    {$fromLoop}
					) AS A
					{$sortFilter}
					LIMIT {$start}, {$limit}";
			$rs = $this->apps->fetch($sql,1);

			
				foreach ($rs as $key => $value) {
					foreach ($keywords as $k => $v) {
						$rs[$key]['summary'] =  preg_replace('/(?<![\>\<\/])'.trim($v).'|(?<=\<\/b\>)'.trim($v).'/i', '<b style="font-weight:bold;">$0</b>',$rs[$key]['summary']);
						$rs[$key]['title'] =  preg_replace('/(?<![\>\<\/])'.trim($v).'|(?<=\<\/b\>)'.trim($v).'/i', '<b style="font-weight:bold;">$0</b>',$rs[$key]['title']);
					}
					//pr($rs[$key]['summary']);exit();
				}

			//pr($rs[0]['summary']);exit();
			

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

}

?>

