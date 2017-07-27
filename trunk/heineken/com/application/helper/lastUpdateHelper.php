<?php 

class lastUpdateHelper {

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

	function lastUpdate(){
		$campaign_id = $_REQUEST['cid'];
		if($campaign_id){
			$campaign_id = implode(',', $campaign_id);
		}else{
			$campaign_id = $this->apps->user->campaign_ids;
		}
		$sql = "SELECT DATE_FORMAT(MAX(twitter_last_dt),'%d/%m/%Y %H:%s:%i') twitter, 
					DATE_FORMAT(MAX(facebook_last_dt),'%d/%m/%Y %H:%s:%i') facebook, 
					DATE_FORMAT(MAX(news_last_dt),'%d/%m/%Y %H:%s:%i') media, 
					DATE_FORMAT(MAX(forum_last_dt),'%d/%m/%Y %H:%s:%i') forum
					FROM sonar_report_campaign.campaign_traffic
					WHERE campaign_id IN ({$campaign_id})";
		//pr($sql);exit;
		$rs = $this->apps->fetch($sql);
		//pr($rs);exit;

		return $rs;
	}
}

?>

