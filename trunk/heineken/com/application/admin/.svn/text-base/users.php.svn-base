<?php

global $ENGINE_PATH;
include_once $ENGINE_PATH."Utility/Paginate.php";

class users extends Admin {
	var $category;
	var $type;
	function __construct(){
		parent::__construct();

		$this->folder =  'users';
		$this->dbclass = 'athreesix';
		$this->total_per_page = 20;
		// pr($this->user);
		//pr($this->user);
	}
	
	function admin(){
		global $CONFIG;
	
		//GET ADMIN ROLE
		foreach($this->roler as $key => $val){
			$this->View->assign($key,$val);
		}
		
		//GET SPECIFIED ADMIN ROLE IF TRUE
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
		
		//HELPER
		$this->View->assign('folder',$this->folder);		
		$this->View->assign('baseurl',$CONFIG['BASE_DOMAIN_PATH']);
		$act = $this->_g('act');
		if($act){
			return $this->$act();
		} else {
			return $this->home();
		}
	}

	function home(){
		global $CONFIG;
		//FILTER BOX
		$filter = "";
		$search = $this->_g("search") == NULL ? '' : $this->_g("search");
		$startdate = $this->_g("startdate") == NULL ? '' : $this->_g("startdate");
		$enddate = $this->_g("enddate") == NULL ? '' : $this->_g("enddate");
		$st = $this->_g("st");
		
		$filter .= $startdate=='' ? "" : "AND hp.created_date >= '{$startdate}' ";
		$filter .= $enddate=='' ? "" : "AND hp.created_date < '{$enddate}' ";		
		$filter .= $search=='' ? "" : "AND (hp.email LIKE '%{$search}%' OR hu.nama_lengkap LIKE '%{$search}%' OR hu.notelp LIKE '%{$search}%') ";
		
		$this->View->assign('startdate',$startdate);
		$this->View->assign('enddate',$enddate);
		$this->View->assign('st',$st);
		$this->View->assign('search',$search);
		
		$start = intval($this->_g('st'));
		
		/* HITUNG BANYAK RECORD DATA */
		$sql ="SELECT count(*) total 
			FROM hnk_upload hp 
			LEFT JOIN hnk_users hu ON hp.email = hu.email
			WHERE 1 {$filter}
		";
		
		$totalList = $this->fetch($sql);
		
		if($totalList){
			$total = intval($totalList['total']);
		} else $total = 0;
		
		/* LIST USER MEMBER */
		$sql = "
			SELECT hp.id,hp.nama_file,hu.email,hp.created_date,hp.hash_filename,hu.nama_lengkap,hu.notelp phonenumber,hp.nama_event
			FROM hnk_users hu  
			LEFT JOIN hnk_upload hp ON hp.email = hu.email
			WHERE 1 {$filter} 
			ORDER BY hp.created_date DESC 
			LIMIT {$start},{$this->total_per_page}
		";
		
		$list = $this->fetch($sql,1);
		// pr($list);
		if($list){
			$n = $start+1;
			foreach($list as $key => $val){
				$list[$key]['no'] = $n++;
			}
		}
		
		$this->View->assign('list',$list);
		$this->Paging = new Paginate();	
		$this->View->assign("paging",$this->Paging->getAdminPaging($start, $this->total_per_page, $total, "?s={$this->folder}&search={$search}&startdate={$startdate}&enddate={$enddate}"));
		return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_list.html");
	}
	
	// function add(){
		// global $CONFIG;
		
		// $data['nama_lengkap'] 	= $this->_p('nama_lengkap');
		// $data['email'] 			= $this->_p('email');
		// $data['notelp'] 		= $this->_p('nama_event');
		
		// foreach($data as $key => $val){
			// $this->View->assign($key,$val);
		// }
		
		// if($this->_p('simpan')==1){
			// foreach($data as $key => $val){
				// $$key= $val;
			// }
			
			// if( $nama_lengkap=='' ){
				// $this->View->assign('msg',"Please complete the form!");
				// return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_new.html");
			// }
			
			// $sql = "
				// INSERT INTO hnk_users (nama_lengkap,email,notelp,created_date) VALUES ('{$nama_lengkap}','{$email}',\"{$notelp}\",NOW())
			// ";
		
			// if(!$this->query($sql)){
				// $this->View->assign("msg","Add process failure");
				// return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_new.html");
			// }else{
				// return $this->View->showMessage("Success Create {$this->folder} ", "index.php?s={$this->folder}&act=add");
			// }
		// }
		// return $this->View->toString("application/admin/{$this->folder}/{$this->folder}_new.html");
	// }
	
	function hapus(){
		$id = intval($this->_g('id'));
		$search = $this->_g('search');
		$startdate = $this->_g('startdate');
		$enddate = $this->_g('enddate');
		$st = $this->_g('st');
		if (strip_tags($this->_g('do')=="photo")) {
			$set = 'SET img = ""';
		} else {
			$set = "SET n_status = 3";
		}
		
		$sql = "UPDATE social_member {$set} WHERE id={$id}";
		if(!$this->query($sql)){
			return $this->View->showMessage('Gagal',"index.php?s={$this->folder}&startdate={$startdate}&enddate={$enddate}&search={$search}&st={$st}");
		}else{
			return $this->View->showMessage('Berhasil',"index.php?s={$this->folder}&startdate={$startdate}&enddate={$enddate}&search={$search}&st={$st}");
		}
	}
	
	function getUsersReport(){
		$filename = "users_report_".date('Ymd_gia').".xls";
		header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
		header("Content-Disposition: attachment; filename=$filename");  //File name extension was wrong
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		// echo "Some Text"; //no ending ; here
		$resReport = $this->reportQuery();
		
		//$this->log->sendActivity("user printing dyo report");
		echo "<style> .phone{ mso-number-format:\@; } </style>";
		echo "<table border='1'>";
		echo "<tr>";
			echo "<td>No</td>";
			echo "<td>Image</td>";
			echo "<td>Nama Lengkap</td>";
			echo "<td>Email</td>";
			echo "<td>No Telphone</td>";
			echo "<td>Nama Event</td>";
			echo "<td>Created Date</td>";
		echo "</tr>";
		foreach ($resReport as $key => $val){
			echo "<tr>";
				echo "<td>$val[no]</td>";
				echo "<td class='phone'>$val[nama_file]</td>";
				echo "<td>$val[nama_lengkap]</td>";
				echo "<td>$val[email]</td>";
				echo "<td class='phone'>$val[phonenumber]</td>";
				echo "<td>$val[nama_event]</td>";
				echo "<td>$val[created_date]</td>";
			echo "</tr>";
		}
		echo "</table>";
		exit;
	}
	
	function reportQuery() {
		//FILTER BOX
		$filter = "";
		$search = $this->_g("search") == NULL ? '' : $this->_g("search");
		$startdate = $this->_g("startdate") == NULL ? '' : $this->_g("startdate");
		$enddate = $this->_g("enddate") == NULL ? '' : $this->_g("enddate");
		$st = $this->_g("st");
		
		$filter .= $startdate=='' ? "" : "AND hp.created_date >= '{$startdate}' ";
		$filter .= $enddate=='' ? "" : "AND hp.created_date < '{$enddate}' ";		
		$filter .= $search=='' ? "" : "AND (hp.email LIKE '%{$search}%' OR hu.nama_lengkap LIKE '%{$search}%' OR hu.notelp LIKE '%{$search}%') ";
	
		$start = intval($this->_g('st'));
		
		/* LIST USER MEMBER */
		$sql = "
			SELECT hp.id,hp.nama_file,hu.email,hp.created_date,hp.event_date,hp.nama_file,hp.hash_filename,hu.nama_lengkap,hu.notelp phonenumber,hp.nama_event
			FROM hnk_users hu  
			LEFT JOIN hnk_upload hp ON hp.email = hu.email
			WHERE 1 {$filter} 
			ORDER BY hp.created_date DESC
		";
		
		$list = $this->fetch($sql,1);
		if($list){
			$n=$start+1;
			foreach($list as $key => $val){
				list($y,$m,$d) = explode('-',substr($val['event_date'],0,10));
				list($name,$ext) = explode('.',$val['hash_filename']);
				$eventdate = $y.$m.$d;
				
				$list[$key]['no'] = $n++;
			}
		}
		
		return $list;		
	}
}