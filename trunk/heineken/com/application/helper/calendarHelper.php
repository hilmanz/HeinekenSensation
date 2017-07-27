<?php 

class calendarHelper {
	function __construct($apps=null){
		global $CONFIG,$logger;
		$this->logger = $logger;
		$this->apps = $apps;

		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)){
				$this->fbID = $this->apps->user->id;	
			}
		}else{
			sendRedirect("{$CONFIG['BASE_DOMAIN']}login/fb");
			$this->apps->assign("msg","Your session is expire due to inactivity. Please re-login...");
			return $this->apps->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
			die();
		}
	}

	function loadCalendar($city=null,$start=0,$limit=5){
		if($this->fbID==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($city!=null||$city!='') $cityFilter="AND city LIKE '%{$city}%'";

			$sql="SELECT a.id,a.name,a.desc,a.place,a.address,a.city,a.src,a.n_status,DATE_FORMAT(a.date,'%d/%m/%Y') AS tgl 
					FROM hnk_event_calendar a
					WHERE 1 {$cityFilter} AND a.n_status IN (1,4)
					ORDER BY a.n_status ASC, a.date ASC
					LIMIT {$start},{$limit}";
			$rs=$this->apps->fetch($sql,1);
			
			$sql="SELECT COUNT(id) AS total FROM hnk_event_calendar 
					WHERE 1
					{$cityFilter}
					LIMIT 1";
			$total=$this->apps->fetch($sql);
			//pr($rs);exit;
			return array('data'=>$rs,'total'=>$total['total']);
		}
	}

	function uploadCalendar(){
		global $CONFIG,$ENGINE_PATH;

	}

}

?>

