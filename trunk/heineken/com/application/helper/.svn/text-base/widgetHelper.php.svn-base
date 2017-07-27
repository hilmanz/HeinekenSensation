<?php 

class widgetHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);	
		}
	}

	function getCampaignName(){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			$sql = "SELECT id, campaign_name AS name
					FROM sonar_data.tbl_campaign
					WHERE id IN ({$this->apps->user->campaign_ids}) 
					AND n_status=1 AND client_id = {$this->uid}";
			$rs = $this->apps->fetch($sql,1);
			foreach ($rs as $key => $value) {
				$campaign[$value[id]]=$value[name];
			}

			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $campaign);
		}
	}

	function getPostCaptured(){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			$sql="SELECT IFNULL(SUM(posts),0) AS num FROM (
					SELECT SUM(num_posts) AS posts FROM sonar_report_campaign.facebook_daily_post A
					WHERE campaign_id IN ({$this->apps->user->campaign_ids}) 
					UNION ALL
					SELECT SUM(num_threads) AS posts FROM sonar_report_campaign.forum_media_post B
					WHERE campaign_id IN ({$this->apps->user->campaign_ids})
					UNION ALL
					SELECT SUM(num_posts) AS posts FROM sonar_report_campaign.news_daily_post C
					WHERE campaign_id IN ({$this->apps->user->campaign_ids})
					UNION ALL
					SELECT SUM(num_posts) AS posts FROM sonar_report_campaign.twitter_daily_post D
					WHERE campaign_id IN ({$this->apps->user->campaign_ids})
				) AS E";
			$rs = $this->apps->fetch($sql);
			
			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $rs);
		}

	}
	
}

?>

