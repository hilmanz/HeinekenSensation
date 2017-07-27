<?php 

class playerSubmitHelper {

	function __construct($apps){
		global $logger;
		$this->logger = $logger;
		$this->apps = $apps;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);	
		}
	}

	function getUserID(){
		if($this->uid==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			return array('status'=>1,'message'=>'OK','data'=> array('user_id'=>$this->uid,'survey_date'=>date("YmdHi")));exit;
		}
	}
	
	function getPlayerLogs($orderBy=0,$orderType=0,$from=null,$to=null,$startPage=0,$limit=10){
		global $CONFIG;
		
		//filter
		$filter="";
		if($from!=null && $to!=null){
			if($from!='null'&&$to!='null'){
				$filter = "WHERE submit_date BETWEEN '{$from} 00:00:00'AND '{$to} 23:59:59'";
			}
		}
		$limitFilter="";
		if($limit!='all'){
			$limitFilter="LIMIT {$startPage},{$limit}";
		}

		if($orderType==0){
			$orderType="DESC";
		}else{
			$orderType="ASC";
		}

		switch($orderBy){
			case 'date':
				$orderBy = 'submit_date_ts';
				break;
			case 'name':
				$orderBy = 'first_name';
				break;
			case 'email':
				$orderBy = 'email';
				break;
			case 'baID':
				$orderBy = 'ba_id';
				break;
			case 'play':
				$orderBy = 'play';
				break;
			case 'win':
				$orderBy = 'win';
				break;
			case 'lose':
				$orderBy = 'lose';
				break;
			default:
				$orderBy = 'submit_date_ts';

		}
		
		$sql = "SELECT DATE_FORMAT(MAX(submit_date),'%d/%m/%Y %h:%i') AS submit_date, 
						SUM(play) as play, 
						SUM(win) as win, 
						SUM(lose) as lose, 
						MAX(submit_date_ts) AS submit_date_ts, 
						first_name, last_name, email, ba_id
				FROM player_logs
				{$filter}
				GROUP BY email
				ORDER BY {$orderBy} {$orderType} 
				{$limitFilter}";	
		$qData = $this->apps->fetch($sql,1);		
		//pr($sql);exit;
		if($qData){
			$sql = "SELECT COUNT(DISTINCT email) total
					FROM player_logs
					{$filter}";	
			$qDataTotal = $this->apps->fetch($sql);
			return array('data'=>$qData,'total'=>$qDataTotal['total']);
		}else{
			return array('data'=>null,'total'=>0);
		}
		
	}

	function setPlayerLogs(){

		$token = strip_tags($this->apps->_p('token'));		
		$first_name = strip_tags($this->apps->_p('first_name'));
		$last_name = strip_tags($this->apps->_p('last_name'));
		$email = strip_tags($this->apps->_p('email'));
		$ba_id = strip_tags($this->apps->_p('ba_id'));
		$play = intval(strip_tags($this->apps->_p('play')));
		$win = intval(strip_tags($this->apps->_p('win')));
		$lose = intval(strip_tags($this->apps->_p('lose')));
		$submit_date = date("Y-m-d H:i:s");
		$submit_date_ts = (strtotime($submit_date) * 1000);
		$salt = "InOrOut_Game";

		if(!$token){ 
			return array('status'=>0,'message'=>'Access token is not found.');exit;
		}
		if($this->uid==0){
			return array('status'=>0,'message'=>'Admin is not login yet.');exit;
		}

		/* token matching with erwin */
		$mytoken = sha1($this->uid.date("YmdHi")."true{".$salt."}");
		$mytokentolerance = sha1($this->uid.date("YmdHi",strtotime(date("YmdHi")." -1 minute "))."true{".$salt."}"); /* tolerance 1 minute */
		
		if($token!=$mytoken) {
			if($token!=$mytokentolerance){
				return array('status'=>0,'message'=>'Your session has expired.');exit;
			}
		}
			$sql="INSERT INTO player_logs
					(first_name,last_name,email,ba_id,submit_date,submit_date_ts,play,win,lose)
				VALUES ('{$first_name}',
						'{$last_name}',
						'{$email}',
						'{$ba_id}',
						'{$submit_date}',
						{$submit_date_ts},
						{$play},
						{$win},
						{$lose})";
			$this->apps->query($sql);
			//pr($sql);
			if($this->apps->getLastInsertId()){
				return array('status'=>1,'message'=>"Player data has been saved.");
			}else return false;
		
	}
}

?>

