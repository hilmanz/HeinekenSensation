<?php
class reports extends App{
	
	function beforeFilter(){
		global $LOCALE,$CONFIG;
		$this->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->assign('assets_domain', $CONFIG['ASSETS_DOMAIN_WEB']);
		$this->uploadHelper = $this->useHelper('uploadHelper');
		$this->userHelper = $this->useHelper('userHelper');		
		$this->loadProfile = $this->userHelper->getUserProfile();
		if($this->loadProfile['isAdmin']!=1){
			sendRedirect("{$CONFIG['BASE_DOMAIN']}home");
			$this->assign("msg","Please wait...");
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/login_message.html');
			die();
		}
	}
	
	function main(){
		if($this->loadProfile['isAdmin']==1){
			$interval = intval($this->_p('interval'));
			$report_type = intval($this->_p('report_type'));

			if($interval){
				$this->download($interval,$report_type);
			}

			if(strip_tags($this->_g('page'))=='reports') $this->log('surf','reports');
			return $this->View->toString(TEMPLATE_DOMAIN_WEB .'/reports.html');	
		}	

	}

	function download($interval,$report_type){
		if($this->loadProfile['isAdmin']==1){

			if($report_type==4){
				/*Result of the quiz complete the form*/
				$sql="SELECT hm.fb_id, hm.email, hut.personality_type, hur.submit_date, hur.reasonToWin AS reason_to_win, 
							(SELECT fav_name FROM hnk_favorite WHERE id = hur.hobby) interest,
							(SELECT fav_name FROM hnk_favorite WHERE id = hur.genre) genre,
							(SELECT fav_name FROM hnk_favorite WHERE id = hur.weekend) weekend_activity
							FROM hnk_member hm
							LEFT JOIN hnk_user_type hut
							ON hm.personal_type=hut.id
							LEFT JOIN hnk_user_result hur
							ON hm.fb_id=hur.fb_id
							WHERE hm.user_type IN (1,2) 
							AND hur.n_status = 1
							AND hm.personal_type <> 0 
							AND DATE(hur.submit_date) =  DATE(DATE_SUB(NOW(), INTERVAL {$interval} DAY))";
			}
			if($report_type==5){
			/*Result of the quiz, not complete*/
			$sql="SELECT hm.fb_id, hm.email, hut.personality_type, hur.submit_date
						FROM hnk_member hm
						LEFT JOIN hnk_user_type hut
						ON hm.personal_type=hut.id
						LEFT JOIN hnk_user_result hur
						ON hm.fb_id=hur.fb_id
						WHERE hm.user_type IN (1,2) 
						AND hur.n_status = 0
						AND hm.personal_type <> 0 
						AND DATE(hur.submit_date) =  DATE(DATE_SUB(NOW(), INTERVAL {$interval} DAY))";
			}
			

			if($report_type==3){
			/*User List Login but never come*/
			$sql="SELECT hm.fb_id, hm.email, hal.date_time 
					FROM hnk_member hm
					INNER JOIN tbl_activity_log hal
					ON hm.fb_id = hal.user_id
					/*WHERE hm.user_type IN (1) AND hal.action_value = 'login'*/
					WHERE hm.user_type IN (1)
					AND DATE(hal.date_time) =  DATE(DATE_SUB(NOW(), INTERVAL {$interval} DAY))";
			}
			if($report_type==1){
				/*User List Login and came*/
				$sql="SELECT hm.fb_id, hm.email, hal.date_time 
						FROM hnk_member hm
						INNER JOIN tbl_activity_log hal
						ON hm.fb_id = hal.user_id
						/*WHERE hm.user_type IN (2) AND hal.action_value = 'login'*/
						WHERE hm.user_type IN (2)
						AND DATE(hal.date_time) = DATE(DATE_SUB(NOW(), INTERVAL {$interval} DAY))";
			}

			if($report_type==6){
			/*User List Login and LIKE*/
				$sql="SELECT fb_id, email
							FROM hnk_member
							WHERE user_type IN (1,2) AND isLike=1";
			}

			/*User List came but not login yet*/
			if($report_type==2){
				$sql="SELECT email
							FROM hnk_member
							WHERE user_type IN (4)";
			}

			if($report_type==7){
			/*Number of each user take the quiz*/
				$sql="SELECT  hm.fb_id, hm.email, COUNT(pl.fb_id) AS total_quiz, pl.submit_date AS last_update, hm.user_type
							FROM hnk_member hm
							INNER JOIN hnk_user_playing_logs pl
							ON hm.fb_id = pl.fb_id
							WHERE hm.user_type IN (1,2)
							GROUP BY hm.fb_id
							ORDER BY last_update DESC";
			}

			if($report_type==8){
			/*WINNER*/
				$sql="SELECT  hm.first_name, hm.last_name, 
							hm.email, hm.active_email, 
							hm.phone, hut.personality_type,
							hur.reasonToWin, hur.submit_date,
							(SELECT fav_name FROM hnk_favorite WHERE fav_type=1 AND id=hur.hobby) AS hobby,
							(SELECT fav_name FROM hnk_favorite WHERE fav_type=2 AND id=hur.weekend) AS weekend,
							(SELECT fav_name FROM hnk_favorite WHERE fav_type=3 AND id=hur.genre) AS genre,
							COUNT(pl.fb_id) AS total_quiz, 
							pl.submit_date AS last_update, 
							IF(hm.user_type=2,'Datang','Tidak Datang') AS visit,
							(SELECT COUNT(email) FROM hnk_event_gallery WHERE email = hm.email) AS visit_on_event
							FROM hnk_member hm
							INNER JOIN hnk_user_playing_logs pl
							ON hm.fb_id = pl.fb_id
							INNER JOIN hnk_user_type hut
							ON hut.id = hm.personal_type
							INNER JOIN hnk_user_result hur
							ON hm.fb_id = hur.fb_id
							WHERE hm.user_type IN (1,2)
							GROUP BY hm.fb_id
							ORDER BY last_update DESC";
			}

			$rs = $this->fetch($sql,1);

			$dd = date("F_j_Y", time() - ((60 * 60 * 24)*$interval));
			header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
			
			if($report_type==1){
				header("Content-Disposition: attachment; filename=Datang_tapi_login_{$dd}.xls");  
			}
			if($report_type==2){
				header("Content-Disposition: attachment; filename=Datang_tapi_ga_login_{$dd}.xls");  
			}
			if($report_type==3){
				header("Content-Disposition: attachment; filename=Ga_datang_tapi_login_{$dd}.xls");  
			}
			if($report_type==4){
				header("Content-Disposition: attachment; filename=Result_of_the_quiz(complete)_{$dd}.xls");  
			}
			if($report_type==5){
				header("Content-Disposition: attachment; filename=Result_of_the_quiz(not_complete)_{$dd}.xls");  
			}
			if($report_type==7){
				header("Content-Disposition: attachment; filename=Number_of_each_user_take_the_quiz_{$dd}.xls");  
			}
			if($report_type==8){
				header("Content-Disposition: attachment; filename=Report_Pemenang_Korea_{$dd}.xls");  
			}
			if($report_type==6){
				header("Content-Disposition: attachment; filename=FB_Like_{$dd}.xls");  
			}
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
				if($report_type==8){
					echo "<table border='1'>";
					echo "<tr>
							<th>Name</th>
							<th>Email FB</th>
							<th>Email Update</th>
							<th>Phone Number</th>
							<th>Result Quiz</th>
							
							<th>Total Quiz Taken</th>
							<th>Essay</th>
							<th>Hobby</th>
							<th>Weekend Activity</th>
							<th>Genre</th>
							<th>Visit</th>
							<th>Visit on Event</th>
							<th>Submit Date</th>
							
							</tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "<td>{$val['first_name']} {$val['last_name']}</td>
							 <td>{$val['email']}</td> 	
							 <td>{$val['active_email']}</td> 	
							 <td>{$val['phone']}</td> 	
							 <td>{$val['personality_type']}</td> 	
							 <td>{$val['total_quiz']}</td> 	
							 <td>{$val['reasonToWin']}</td> 	
							 <td>{$val['hobby']}</td> 	
							 <td>{$val['weekend']}</td> 	
							 <td>{$val['genre']}</td> 	
							 <td>{$val['visit']}</td> 	
							 <td>{$val['visit_on_event']}</td> 	
							 <td>{$val['submit_date']}</td>
							 ";
						echo "</tr>";
					}
					echo "</table>";
				}
				if($report_type==4){
					echo "<table border='1'>";
					echo "<tr>
							<th>FB ID</th>
							<th>Email</th>
							<th>Personality Type</th>
							<th>Submit Date</th>
							<th>Reason To Win</th>
							<th>Interest</th>
							<th>Genre</th>
							<th>Weekend Activity</th>
							</tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "<td>{$val['fb_id']}</td>
							 <td>{$val['email']}</td> 	
							 <td>{$val['personality_type']}</td> 	
							 <td>{$val['submit_date']}</td>
							 <td>{$val['reason_to_win']}</td> 
							 <td>{$val['interest']}</td> 
							 <td>{$val['genre']}</td> 
							 <td>{$val['weekend_activity']}</td> 

							 ";
						echo "</tr>";
					}
					echo "</table>";
				}
				if($report_type==5){
					echo "<table border='1'>";
					echo "<tr>
							<th>FB ID</th>
							<th>Email</th>
							<th>Personality Type</th>
							<th>Submit Date</th>
							</tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "<td>{$val['fb_id']}</td>
							 <td>{$val['email']}</td> 	
							 <td>{$val['personality_type']}</td> 	
							 <td>{$val['submit_date']}</td>
							 ";
						echo "</tr>";
					}
					echo "</table>";
				}
				if($report_type==3||$report_type==1){
					echo "<table border='1'>";
					echo "<tr><th>FB_ID</th><th>Email</th><th>Date_time</th></tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "<td>{$val['fb_id']}</td>
							 <td>{$val['email']}</td> 	
							 <td>{$val['date_time']}</td> 	
							 ";
						echo "</tr>";
					}
					echo "</table>";
				}
				if($report_type==2){
					echo "<table border='1'>";
					echo "<tr><th>Email</th></tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "
							 <td>{$val['email']}</td> 	
							 ";
						echo "</tr>";
					}
					echo "</table>";
				}
				if($report_type==6){
					echo "<table border='1'>";
					echo "<tr><th>Email</th></tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "
							 <td>{$val['email']}</td> 	
							 ";
						echo "</tr>";
					}
					echo "</table>";
				}
				if($report_type==7){
					echo "<table border='1'>";
					echo "<tr><th>FB ID</th><th>Email</th><th>Total Quiz</th><th>Last Update</th><th>User Type</th></tr>";
					foreach ($rs as $key => $val){	
						echo "<tr>";
						echo "<td>{$val['fb_id']}</td>
							 <td>{$val['email']}</td> 	
							 <td>{$val['total_quiz']}</td> 	
							 <td>{$val['last_update']}</td>
							 <td>{$val['user_type']}</td> ";
						echo "</tr>";
					}
					echo "</table>";
				}
				exit;
			
		}
	}

}
?>