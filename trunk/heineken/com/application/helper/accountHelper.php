<?php 

class accountHelper {

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

	
	function accountNkeyword($ids, $start_date=null, $end_date=null, $start=0, $limit=3, $sort_type=null, $sort_status=0){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			mysql_query('SET CHARACTER SET utf8');
			
			//Get keyword on each group
			$sql="SELECT id as campaign_id,keywords FROM sonar_data.tbl_campaign
					WHERE id IN ({$ids}) ORDER BY keywords DESC";
			$rs = $this->apps->fetch($sql,1);
			$rs = $this->swapCampaignIDtoName($rs);
			foreach ($rs as $key => $value) {
				$explode = explode(',', $value['keywords']);
				foreach ($explode as $k => $val) {
					$data[$k][$value['campaign_id']][]=trim($val);
				}
			}


			if(!$rs) return array('status'=>0,'message'=>"N/A");
			else return array('status'=>1,'message'=>'OK','data'=> $data);
		}
	}

}

?>

