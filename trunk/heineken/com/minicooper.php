<?php
error_reporting(0);
global $ENGINE_PATH;
include_once $ENGINE_PATH."Utility/Paginate.php";
		
class minicooper extends Admin{
	var $category;
	var $type;
	function __construct(){	
		parent::__construct();	
		
		$this->type = "1,3,4,5,6";
		$this->contentType = "0";
		$this->articleType = "4";
		$this->folder =  'minicooper';
		$this->dbclass = 'marlborohunt';
		$this->fromwho = 0; // 0 is admin/backend
		$this->total_per_page = 20;
		
	}
	
	function admin(){
		
		global $CONFIG;
	
		//get admin role
		foreach($this->roler as $key => $val){
		$this->View->assign($key,$val);
		}
		//get specified admin role if true
		if($this->specified_role){
			foreach($this->specified_role as $val){
				$type[] = $val['type'];
				$category[] = $val['category'];
			}
			if($type) $this->type = implode(',',$type);
			else return false;
			if($category) $this->category = implode(',',$category);
			else return false;
		}
		//helper
	
		$this->View->assign('folder',$this->folder);
		$this->View->assign('basedomain',$CONFIG['BASE_DOMAIN']);
		$this->View->assign('baseurl',$CONFIG['BASE_DOMAIN_PATH']);
		$act = $this->_g('act');
		if($act){
			return $this->$act();
		} else {
			return $this->home();
		}
	}
	
	function home (){

		//filter box
		$filter = "";
		$search = $this->_g("search") == NULL ? '' : $this->_g("search");
		$startdate = $this->_g("startdate") == NULL ? '' : $this->_g("startdate");
		$enddate = $this->_g("enddate") == NULL ? '' : $this->_g("enddate");
		$nstatus = (string) $this->_g("nstatus") == NULL ? '' : (string) $this->_g("nstatus");
		$filter .= $startdate=='' ? "" : "AND DATE(ncr.created_date) >= '{$startdate}' ";
		$filter .= $enddate=='' ? "" : "AND DATE(ncr.created_date) <= '{$enddate}' ";		
		$filter .= $search=='' ? "" : "AND (sm.name LIKE '%{$search}%' OR sm.last_name LIKE '%{$search}%' OR sm.email LIKE '%{$search}%')";
		
		$this->View->assign('search',$search);
		$this->View->assign('nstatus',$nstatus);
		$this->View->assign('startdate',$startdate);
		$this->View->assign('enddate',$enddate);	
	
		$start = intval($this->_g('st'));
		
		$sql ="
		SELECT COUNT(*) total 
		FROM tbl_code_inventory inv
		LEFT JOIN social_member sm ON sm.id = inv.userid
		WHERE  inv.histories like '%get from event%' ORDER BY inv.datetimes DESC ";
		$qData = $this->fetch($sql);
		if($qData) $total = $qData['total'];
		else $total = 0;
		$limit = 50;
		$sql ="
		SELECT inv.histories,sm.email,inv.datetimes,inv.codeid,inv.codepublicityid
		FROM tbl_code_inventory inv
		LEFT JOIN social_member sm ON sm.id = inv.userid
		WHERE  inv.histories like '%get from event%' ORDER BY inv.datetimes DESC LIMIT {$start},{$limit}";
		$list = $this->fetch($sql,1);
		// pr($sql);
		$this->View->assign('list',$list);
		$this->Paging = new Paginate();
	
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $this->total_per_page, $total, "?s={$this->folder}&startdate={$startdate}&enddate={$enddate}"));	
		// pr("application/admin/{$this->folder}/{$this->folder}_list.html");
		return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_list.html");
	}
	
	function upload(){
		// pr('masuk');exit;
		if($this->_p('upload')=='minicooper'){
			//read xls
			$files = $_FILES['files'];
			
			global $ENGINE_PATH;
			include_once $ENGINE_PATH."Utility/excel_reader2.php";
		
			$data = new Spreadsheet_Excel_Reader($files['tmp_name']);
		
			$n = 1;
			$x = 0;
			$y = 0;
			$newarr = false;
			foreach($data->realraw() as $val){
					if($n<12) $keys[] = str_replace(".","",str_replace(" ","",$val));
					
					if($n>11) {	
						
						$newarr[$y]["{$keys[$x]}"] = $val;						
						
						if($x==10){
							$x=0;
							$y++;
						}else  {
							$x++;
							
						}
						
					
					}
					$n++;
					
			}
			// [DeviceID] => 9
            // [TransacID] => 14
            // [BHID] => csantos
            // [BHName] => CAROLINA DELOS SANTOS
            // [BHCity] => Metro Manila
            // [TaskID] => 2
            // [email] => fe_bassi@yahoo.com
            // [code] => nxmfteuy
            // [surveydate] => 14/07/2013 01:42
            // [uploaddate] => 14/07/2013 02:38
			// pr($newarr);exit;
			// parse xls			
				foreach($newarr as $val){
				if(preg_match("/@/i",$email)!=1) $checkstructure[] = false;
				else $checkstructure[] = true;
			}
			
			if(!in_array(false,$checkstructure)){
				foreach($newarr as $val){
					
					$sql = " 
					INSERT INTO tbl_report_letter_elusive 
					(deviceid ,	transid ,	bhid ,	bhname ,	bhcity, 	taskid ,	email ,	code ,	surveydate 	,uploaddate ,	n_status ) 
					VALUES  ( '{$val['DeviceID']}', '{$val['TransacID']}','{$val['BHID']}','{$val['BHName']}','{$val['BHCity']}','{$val['TaskID']}','{$val['email']}','{$val['code']}','{$val['surveydate']}','{$val['uploaddate']}',1 )	
					";
					
					$qData = $this->query($sql);
					// if($qData){
						$success[] = $this->giveletter($val['email'],$val['code']);
						echo "sukses insert";
					// }
					
				}
			}
			// pr($success);
			return $this->View->showMessage("Success ALLOCATION LETTER", "index.php?s={$this->folder}");
		}
	}
	
	function giveletter($email=false,$code=false){
		if($code=='') return false;
		if(strlen($code)!=8) return false;
		
		global $LOCALE;
		$date = date('Y-m-d H:i:s');
		
		$sql = "SELECT id,name,last_name FROM social_member WHERE email = '{$email}' ";		
		$userdata = $this->fetch($sql);
		$uid = intval($userdata['id']);
		$name = $userdata['name']." ".$userdata['last_name'];
		
		if($uid==0) return false;
		
		$sql = "SELECT codeid,id FROM tbl_code_publicity WHERE maskcode = '{$code}' ";		
		$code = $this->fetch($sql);

		if(!$code) return false;
		if($code['id']==0) return false;
		
		//1462
		$tasklist['minicooper'] = 1462;
		$tasklist['stranger'] = 1464;
		$tasklist['marlboros'] = 1465;
		$hascode = $this->checkusergetthiscode($uid,$code);
		
		if($hascode) return false;
		$campaign = strip_tags($this->_p('campaign'));
		if($campaign=='') $campaign = 'minicooper';
		
		$getletter = $this->savetoinventory($uid,$code,$campaign);
		if($getletter) {
				$accompalishedlocale = $campaign."accompalished";
				$getletterlocale = $campaign."getletter";
				
				$sql = "INSERT INTO my_task (userid, taskid, taskdate, n_status)
						VALUES ({$uid}, {$tasklist[$campaign]}, '{$date}', 1)";
				$hasil = $this->query($sql);
				
				$sql =" SELECT codename FROM tbl_code_detail WHERE id={$code['codeid']} LIMIT 1";
				$letter = $this->fetch($sql);
				sleep(1);
				$this->log->sendUserActivity($uid, 'account', $LOCALE[1][$accompalishedlocale]);
				sleep(1);
				$this->log->sendUserActivity($uid, 'getletter', "{$name} {$LOCALE[1][$getletterlocale]} \"{$letter['codename']}\" ");
				
				return true;
				
		}
		return false;
	}
	function checkusergetthiscode($uid=false, $code=false){
			if($uid==false) return true;
			if($code==false) return true;
			
			$sql = "SELECT count(*) total FROM tbl_code_inventory WHERE codepublicityid = '{$code['id']}' AND userid={$uid}  ";		
			$code = $this->fetch($sql);
			
			if($code){
					
				if($code['total']>0) return true;
				else return false;
			}
			return true;
	}
	
	
	function savetoinventory($uid=false,$code=false,$campaign='minicooper'){
		
		if($uid==false) return false;
		if($code==false) return false;
		global $LOCALE;
			
					$date = date('Y-m-d H:i:s');
					
					$sql = "INSERT INTO  tbl_code_inventory (userid, codeid, codepublicityid, n_status, histories,datetimes)
							VALUES ({$uid}, {$code['codeid']}, {$code['id']}, 0, 'get from event {$campaign}','{$date}')";
					$res = $this->query($sql);
		
					if ($res){
						return true;
					}else{
						
						return false;
					}
					
							
	}
	
	function getreport(){
		
		$filename = "thisorthat_report_".date('Ymd_gia').".xls";
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		// echo "Some Text"; //no ending ; here
		$resReport = $this->downloadreport_old();
		$this->log->sendActivity("user printing thisorthat report");
		// pr($resReport);
		echo "<table border='1'>";
		echo "<tr>";
			echo "<th class='head0'>No</th>";
			echo "<th class='head1'>Name</th>";
			echo "<th class='head1'>Email</th>";
			echo "<th class='head1'>Date</th>";
			echo "<th class='head1'>Theme Name</th>";
			echo "<th class='head1'>Submission</th>";
		echo "</tr>";
		foreach ($resReport as $key => $val){			
			echo "<tr>";
				echo "<td>$val[no]</td>";
				echo "<td>$val[name] $val[last_name]</td>";
				echo "<td>$val[email]</td>";
				echo "<td>$val[created_date]</td>";
				echo "<td>$val[themes]</td>";
				echo "<td>$val[total]</td>";
			echo "</tr>";
		}
		echo "</table>";
		exit;
	}
	
	function downloadreport_old(){
		//filter box
		$filter = "";
		$search = $this->_g("search") == NULL ? '' : $this->_g("search");
		$startdate = $this->_g("startdate") == NULL ? '' : $this->_g("startdate");
		$enddate = $this->_g("enddate") == NULL ? '' : $this->_g("enddate");
		$nstatus = (string) $this->_g("nstatus") == NULL ? '' : (string) $this->_g("nstatus");
		$filter .= $startdate=='' ? "" : "AND DATE(ncr.created_date) >= '{$startdate}' ";
		$filter .= $enddate=='' ? "" : "AND DATE(ncr.created_date) <= '{$enddate}' ";		
		$filter .= $search=='' ? "" : "AND (sm.name LIKE '%{$search}%' OR sm.last_name LIKE '%{$search}%' OR sm.email LIKE '%{$search}%')";
		
		$this->View->assign('search',$search);
		$this->View->assign('nstatus',$nstatus);
		$this->View->assign('startdate',$startdate);
		$this->View->assign('enddate',$enddate);	
				
		if($nstatus=="2"){
			$filter .=  " AND photo_moderation='{$nstatus}' ";
		}
		
		if($nstatus=="1"){
			$filter .=  " AND photo_moderation='{$nstatus}' ";
		}
		
		$start = intval($this->_g('st'));
		
		/* Hitung banyak record data */
		$sql = "SELECT COUNT(*) total FROM {$this->dbclass}_news_content_repo a 
				LEFT JOIN {$this->dbclass}_news_content b on a.otherid = b.id
				LEFT JOIN social_member as c on b.id = c.id
				WHERE b.articleType = {$this->articleType} {$filter} GROUP BY a.created_date
				ORDER BY a.created_date DESC";
		$totalList = $this->fetch($sql,1);	
		if($totalList){
		$total = count($totalList);
		}else $total = 0;
		// pr($total);
		
		
		/* list article */
		 // DATE_FORMAT(a.datetime,'%Y-%m-%d') 
		$sql = "SELECT ncr . * , sm.name, sm.last_name, sm.email, mnc.tags,mnc.articleType
				FROM {$this->dbclass}_news_content_repo ncr
				LEFT JOIN {$this->dbclass}_news_content mnc ON ncr.otherid = mnc.id
				LEFT JOIN social_member sm ON ncr.userid = sm.id
				WHERE mnc.articleType = {$this->articleType} AND sm.name IS NOT NULL and sm.name <> '' {$filter}
				ORDER BY ncr.created_date DESC LIMIT {$start},{$this->total_per_page}";		
		$list = $this->fetch($sql,1);
		// pr($sql);
		if($list){
				
			$n=$start+1;
			foreach($list as $key => $val){
					$arrtags = unserialize($val['tags']);
					// pr($arrtags);exit;
					$list[$key]['themes'] = $arrtags[$val['typealbum']-2]['button'];
					$list[$key]['no'] = $n++;
					
			}
			
		}
		
		return $list;
	}	
	
	
}
