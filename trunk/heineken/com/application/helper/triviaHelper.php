<?php 

class triviaHelper {
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

	function loadFavoriteList(){
		if($this->fbID==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			//interest
			$sql="SELECT * FROM hnk_favorite WHERE n_status=1 AND fav_type=1";
			$int = $this->apps->fetch($sql,1);
			//weekend
			$sql="SELECT * FROM hnk_favorite WHERE n_status=1 AND fav_type=2";
			$weekend = $this->apps->fetch($sql,1);
			//music
			$sql="SELECT * FROM hnk_favorite WHERE n_status=1 AND fav_type=3";
			$music = $this->apps->fetch($sql,1);

			return array('interest'=>$int,'weekend'=>$weekend,'music'=>$music);
		}
	}

	function loadTrivia(){
		//Load and shuffle the question
		$sql="SELECT * FROM hnk_trivia_question WHERE n_status = 1";
		$question=$this->apps->fetch($sql,1);
		if($question){
			shuffle($question);

			//Load and shuffle the answer
			foreach ($question as $key => $value) {
				if($key<8){
					$q[$key]['question']=utf8_encode($question[$key]['question']);
					$sql="SELECT * FROM hnk_trivia_answer 
							WHERE n_status = 1 AND question_id={$value['id']}";
					$answer=$this->apps->fetch($sql,1);

					if($answer){
						foreach ($answer as $k => $v) {
							$answer[$k]['answer']=utf8_encode($answer[$k]['answer']);
						}
						shuffle($answer);
						$q[$key]['answer']=$answer;
					}
					$key++;
				}	
			}
			
		}

		return $q;
	}

	function notSubmitTheForm(){
		if($this->fbID==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			//did user submit the form?
			$sql="SELECT n_status FROM hnk_user_result WHERE fb_id={$this->fbID} LIMIT 1";
			$notSubmit=$this->apps->fetch($sql);

			if($notSubmit){
				if($notSubmit['n_status']==1)return 1;
				else return 0;
			}else{
				return 0;
			}
		}
	}

	function submitTrivia($pattern=null,$reasonToWin=null,$hobby=null,$weekend=null,$music=null){
		if($this->fbID==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($pattern!=null){
				//Check user first time submit
				$sql="SELECT * FROM hnk_user_result WHERE fb_id={$this->fbID} AND n_status=0 LIMIT 1";
				if($this->apps->fetch($sql)){
					$sql="UPDATE hnk_user_result
							SET reasonToWin='{$reasonToWin}', n_status=1, hobby={$hobby}, weekend={$weekend}, genre={$music}
							WHERE fb_id={$this->fbID} AND n_status=0";
					if($this->apps->query($sql)){
						return array('status'=>1,'message'=>"Success");exit;
					}else{
						return array('status'=>3,'message'=>"Error");exit;
					}
				}else{
					return array('status'=>2,'message'=>"You've already submit this before");exit;
				}

				
			}else{
				return array('status'=>2,'message'=>"Please answer all trivia quiz");exit;
			}
		}
	}
	function loadResultTrivia($pattern=null){
		if($this->fbID==0){
			return array('status'=>0,'message'=>"Please Login first.");exit;
		}else{
			if($pattern!=null){
				$sql="SELECT COUNT(pt.id) AS total, SUM(pt.weight) AS weight, pt.personality_type AS type, ut.personality_type, ut.personality_desc
						FROM hnk_trivia_answer pt
						LEFT JOIN hnk_user_type ut
						ON pt.personality_type=ut.id
						WHERE pt.id IN ({$pattern})
						GROUP BY pt.personality_type
						ORDER BY total DESC LIMIT 5";
			
				$rs = $this->apps->fetch($sql,1);
				$rs=max($rs);
				
				$rs['personality_desc']=utf8_encode($rs['personality_desc']);

				//Check user first time submit
				$sql="SELECT * FROM hnk_user_result WHERE fb_id={$this->fbID} LIMIT 1";
				if(!$this->apps->fetch($sql)){
					$sql="INSERT INTO hnk_user_result (fb_id,user_answer_pattern,submit_date,n_status)
							VALUES ({$this->fbID}, '{$pattern}', NOW(), 0)";
					$this->apps->query($sql);
					$sql="INSERT INTO hnk_user_playing_logs
							VALUES (NULL, {$this->fbID}, NOW(),'{$pattern}',{$rs['type']})";
					$this->apps->query($sql);
					$sql="UPDATE hnk_member
							SET personal_type={$rs['type']}
							WHERE fb_id={$this->fbID}";
					$this->apps->query($sql);
				}else{
					//interval 5 minutes are allowed
					/*$sql="SELECT submit_date 
							FROM hnk_user_playing_logs 
							WHERE fb_id={$this->fbID}
							AND TIMEDIFF(NOW(), submit_date) > 100 LIMIT 1";
					if($this->apps->fetch($sql)){*/
						$sql="INSERT INTO hnk_user_playing_logs
							VALUES (NULL, {$this->fbID}, NOW(),'{$pattern}',{$rs['type']})";
						//pr($sql);exit;	
						$this->apps->query($sql);
					/*}else{
						return array('status'=>3,'message'=>"You only allow submit every 5 minutes");exit;
					}*/
				}

				
				return array('status'=>1,'message'=>"Success",'data'=>$rs);exit;
			}else{
				return array('status'=>2,'message'=>"Please answer all trivia quiz");exit;
			}
		}
	}

}

?>

