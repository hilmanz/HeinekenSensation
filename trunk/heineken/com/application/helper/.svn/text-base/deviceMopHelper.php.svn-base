<?php
global $APP_PATH;
include_once $APP_PATH."/MOP/MOPClient_Admin.php";

class deviceMopHelper {
	var $mopClient;

	function __construct($apps){
		global $logger,$CONFIG;
		$this->logger = $logger;
		$this->apps = $apps;
		$this->mopClient =  new MOPClient(null);
		$this->config = $CONFIG;
		$this->uid= 0;
		if($this->apps->isUserOnline())  {
			if(is_object($this->apps->user)) $this->uid = intval($this->apps->user->id);					
		}
		$this->mopurl = "https://staging-aws-mop-id.es-dm.com/dm.mop.admin.webservice/centralAdminwebservice.asmx";
		
	}
	
	function getCredential($username = "BEATServer",$password = "Beat123",$deviceid = "BEAT Device ID",$devicedesc = "Test Device",$serialnumber = "00000000-0000-000-00-0-BEAT01"){
		
		/*		
		$username = "BEATServer";
		$password = "Beat123";
		$deviceid = "BEAT Device ID";
		$devicedesc = "Test Device";
		$serialnumber = "00000000-0000-000-00-0-BEAT01";
		
		
		$username = "ndianlira";
		$password = "ndianlir";
		$deviceid = "81cec667c7fd1a8261d46bf243799166";
		$devicedesc = "PMIIDPDAD06521";
		$serialnumber = "8dd51e3f53b5abf336a657b83461d4c7";
		
		*/
		 
		$uname = str_replace('_','.',trim($this->apps->_request('username')));
		$pwd = strip_tags($this->apps->_request('password'));
		$deid = strip_tags($this->apps->_request('deviceid'));
		$dedesc = strip_tags($this->apps->_request('devicedesc'));
		$snum = strip_tags($this->apps->_request('serialnumber'));
	
		if($uname) $username = $uname;
		if($pwd) $password = $pwd;
		if($deid) $deviceid = $deid;
		if($dedesc) $devicedesc = $dedesc;
		if($snum) $serialnumber = $snum;
		
		// $username = "BEATServer";
		// $password = "Beat123";
		// $deviceid = "BEAT Device ID";
		// $devicedesc = "Test Device";
		// $serialnumber = "00000000-0000-000-00-0-BEAT01";
		
		// $username = "verashiska";
		// $password = "beat123";
		// $deviceid = "352178052699475";
		// $devicedesc = "pyramid";
		// $serialnumber = "1a5e9a8a8406e868";
			
		$data['userName'] = $username;
		$data['password'] = $password;
		$data['deviceId'] = $deviceid;
		$data['deviceDescription'] = $devicedesc;
		$data['serialNumber'] = $serialnumber;
		
		return $data;
	}
	
	function loginAdminMop($data=false){
	
		
		$credential = $this->getCredential();
		
		$url =$this->mopurl;
		// $this->getmopdevicedata($credential['userName'],$credential['password'],$credential['deviceId'],$credential['serialNumber'],$credential['deviceDescription']);
		$xml= $this->mopClient->CheckLogin($url,$credential['userName'],$credential['password'],$credential);		
		$this->logger->log($xml);		
		if($xml){
			$this->getmopdevicedata($credential['userName'],$credential['password'],$credential['deviceId'],$credential['serialNumber'],$credential['deviceDescription']);
			$session_mop = simplexml_load_string($xml);
			$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$session_mop);
		}else{
			$session_mop = false;
			$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",0);
		}
		return $session_mop;
	}
	
	
	function getmopdevicedata($username=null,$password=null,$udid=null,$serialnumber=null,$devidedesc=null){
		 // Full texts 	id 	deviceid 	password 		userid 	update_time 	n_status 
		 
		$uid = intval($this->uid) ;
		if($uid==0) return false;
		
		$sql ="INSERT INTO `social_device_member` 
		(`deviceid`, `password`, `userid`, `update_time`, `n_status`,udid ,	serialnumber ,	devidedesc ) 
		VALUES ('{$username}', '{$password}', '{$this->uid}', NOW(), '1', '{$udid}', '{$serialnumber}', '{$devidedesc}')
		ON DUPLICATE KEY UPDATE  password='{$password}',update_time=NOW(),userid='{$this->uid}',udid='{$udid}',serialnumber='{$serialnumber}',devidedesc='{$devidedesc}'
		";
		
		$this->logger->log($sql);
		$rs = $this->apps->query($sql);
		
		$sql = " UPDATE social_member SET deviceid='{$username}' WHERE id={$this->uid}  LIMIT 1 ";
		$this->logger->log($sql);
		$this->apps->query($sql);
		
	}
	
	function getloginmopbyserver(){
		
		$sql = "SELECT * FROM social_device_member WHERE userid='{$this->uid}' LIMIT 1";
		
		$rs = $this->apps->fetch($sql);
		
		if($rs){
			// $username = "BEATServer",$password = "Beat123",$deviceid = "BEAT Device ID",$devicedesc = "Test Device",$serialnumber = "00000000-0000-000-00-0-BEAT01"
			$this->getCredential($rs['deviceid'],$rs['password'],$rs['udid'],$rs['devidedesc'],$rs['serialnumber']);
		}
		
		
	}
	
	function checkReferralMop(){
		
	
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;

		if($session){
			$sessionid = $session->SessionID;
					
			$url =$this->mopurl;
			$xml= $this->mopClient->checkReferral($url,$sessionid,$credential);
			if($xml){
				$session_mop=	simplexml_load_string($xml);
				$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$session_mop);
			}else{
				$session_mop = false;
				$this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",0);
			}
			return $session_mop;
		}return false;

	}
	
	
	function AdminEndSession(){
		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;

		if($session){
			$sessionid = $session->SessionID;
			
			$url =$this->mopurl;
			$xml= $this->mopClient->AdminEndSession($url,$sessionid,$credential);
			if($xml){
				$session_mop=	simplexml_load_string($xml);
				
			}else{
				$session_mop = false;
				
			}
			return $session_mop;
		}return false;

	}
	
	function syncAdminUserRegistrant($type="AdminRegisterProfileDeDuplication"){
		
		$this->loginAdminMop();
		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		// $session = $this->apps->_request('sessionid') ;
		$resdata['result'] =  "98";
		$resdata['data']   =  "you beat not login, lease login";
		if(intval(@$this->apps->user->id)==0) return $resdata;
		if($session){
			$sessionid = $session->SessionID;
			// $sessionid = $session;
			$xml = $this->xmlRegisterProfile($type);
		
			// pr($xml);
			// exit;
			$url =$this->mopurl;
			$xml= $this->mopClient->registerAdminUser($type,$url,$sessionid,$xml,$credential);
			$this->logger->log($xml);
			// echo $xml;
			if($xml){
				$datamop =	simplexml_load_string($xml);
				if($datamop->SessionID) $this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$datamop);
				$resdata['result'] =  (string)$datamop->Result;
				$resdata['data'] =false;
				if($datamop->ProfileCollection){
						
						$profiledata = false;
						$n=0;
						foreach($datamop->ProfileCollection->Profile as $key => $mopdata){
										$mopattr = "@attributes";
										$profiledata[$n]["RegistrationID"] = (string)$mopdata["RegistrationID"];
										$profiledata[$n]["ResponseDescription"] = (string)$mopdata["ResponseDescription"];
								foreach($mopdata->FieldCollection->Field  as $fielddata){
										
										$profiledata[$n][(string)$fielddata["Name"]] = (string)$fielddata->Value;
										$profiledata[$n]["Status"] = (string)$fielddata->Status;
								}
							$n++;
						}
						$resdata['data'] = $profiledata;
					
				}
			
			
				$session_mop = $resdata;
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
		
	}
	
	function searchProfileUser(){
		$this->loginAdminMop();
		
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		if($session){
			$sessionid = $session->SessionID;
			
			$xml = $this->searchUserXML();
			
			$url =$this->mopurl;
			$xml= $this->mopClient->searchProfileUser($url,$sessionid,$xml,$credential);
			
			if($xml){
				$datamop =	simplexml_load_string($xml);
				if($datamop->SessionID) $this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$datamop);
				$resdata['result'] =  (string)$datamop->Result;
				$resdata['data'] =false;
				$usertype['users'] = 0;
				if($datamop->ProfileCollection){					
					$profiledata = false;
					$n=0;
					foreach($datamop->ProfileCollection->Profile as $key => $mopdata){
									$mopattr = "@attributes";
									$profiledata[$n]["RegistrationID"] = (string)$mopdata["RegistrationID"];
							foreach($mopdata->FieldCollection->Field  as $fielddata){									
									$profiledata[$n][(string)$fielddata["Name"]] = (string)$fielddata->Value;
							}
						$n++;
					}
					$resdata['data'] = $profiledata;
				
				}
				if($resdata['result']==1)$usertype['users'] = 2;
				$this->apps->session->setSession($this->config['SESSION_NAME'],'USERTYPE',$usertype);
				
				$session_mop = $resdata;
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
	}
	
	function AdminGetProfileonGiid(){	
		$this->loginAdminMop();
		$credential = $this->getCredential();
		
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		if($session){
			$sessionid = $session->SessionID;
			
			$xml = $this->searchgiidxml();
			// pr($xml);
			// exit;
			$url =$this->mopurl;
			$xml= $this->mopClient->AdminGetProfileonGiid($url,$sessionid,$xml,$credential);
			if($xml){
				$datamop =	simplexml_load_string($xml);
				if($datamop->SessionID) $this->apps->session->setSession($this->config['SESSION_NAME'],"MOPADMIN",$datamop);
				$resdata['result'] =  (string)$datamop->Result;
				$resdata['data'] =false;
               $usertype['users'] = 0;
				if($datamop->ProfileCollection){
					$profiledata = false;
					$n=0;
					foreach($datamop->ProfileCollection->Profile as $key => $mopdata){
									$mopattr = "@attributes";
									$profiledata[$n]["RegistrationID"] = (string)$mopdata["RegistrationID"];
							if($mopdata->FieldCollection->Field){
								foreach($mopdata->FieldCollection->Field  as $fielddata){									
										$profiledata[$n][(string)$fielddata["Name"]] = (string)$fielddata->Value;
								}
							}
					$n++;	
					}
					$resdata['data'] = $profiledata;
				}
					if($resdata['result']==1) $usertype['users'] = 2;
				
					$this->apps->session->setSession($this->config['SESSION_NAME'],'USERTYPE',$usertype);
				
						
				$session_mop = $resdata;
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
	}
	
	function getProfileUser(){
		$this->loginAdminMop();
		$credential = $this->getCredential();
		$session = $this->apps->session->getSession($this->config['SESSION_NAME'],"MOPADMIN") ;	
		
		if($session){
			$sessionid = $session->sessionid;
			$xml = $this->getProfileUserXML();
			// pr($xml);
			// exit;
			$url =$this->mopurl;
			$xml= $this->mopClient->getProfileUser($url,$sessionid,$xml,$credential);
			if($xml){
				$session_mop=	simplexml_load_string($xml);
			}else{
				$session_mop = false;
			}
			return $session_mop;
		}else return false;
		
	}

	
	function registerDeviceAdmin(){
		 
		$credential = $this->getCredential();
		
		$url =$this->mopurl;
		$xml= $this->mopClient->registerDeviceAdmin($url,$credential);
		echo $xml;exit;
		if($xml){
			$session_mop=simplexml_load_string($xml);
		}else{
			$session_mop = false;
		}
		return $session_mop;
		
	}
	
	
	function xmlRegisterProfile($type=false){
		
		
	
		/* $Campaign = "ID1300DS2AWS";
		$Phase = "PH01";
		$Audience = "A001";
		$MediaCategory = "OBW";
		$OfferCode = "WEB088";
		$OfferCategory = "WEB"; */
		// Campaign (C )  ID1300AM01001 2013 A360 Website
		$Campaign = "ID1300AM01001";
		$Phase = "PH01";
		$Audience = "A001";
		$MediaCategory = "OBW";
		$OfferCode = "WEB088";
		$OfferCategory = "WEB";
		
		$firstname=rtrim(ltrim($this->apps->_request("name")));
		$registerid=intval($this->apps->_request("registerid"));
		$lastname=rtrim(ltrim($this->apps->_request("lastname")));
		$nickname=rtrim(ltrim($this->apps->_request("nickname")));
		$email=$this->apps->_request("email");
		$img=$this->apps->_request("img");
		$small_img=$this->apps->_request("small_img");
		$city=$this->apps->_request("city");
		$state=$this->apps->_request("state");
		$giidnumber=$this->apps->_request("giidnumber");
		$giidtype=$this->apps->_request("giidtype");
		$companymobile=$this->apps->_request("companymobile");
		$sex=$this->apps->_request("sex");
		$birthday=$this->apps->_request("birthday");
		$description=$this->apps->_request("description");
		
		$socialaccount=$this->apps->_request("socialaccount");
		
		$phone_number=$this->apps->_request("phone_number");
		if($phone_number=='')$phone_number = $companymobile;
	
		$brand1=$this->apps->_request("Brand1_ID");
		if($brand1=='') {
				$brand1 = "0004";
				$brand1ref = "F/M/Z";
		}
		// $brand1 = "0004";
		$brandsub1=$this->apps->_request("Brand1SUB_ID");
		if($brandsub1==''){
			$brandsub1 = "0004"; 
			$brandsub1ref = "F/M/Z"; 			
		}
		
		// if($brandsub1=='')$brandsub1 = "F/M/Z";
		// $brandsub1 = "F/M/Z";
		
		$ctypes=$this->apps->_request("ctypes");
		$ctypeu=$this->apps->_request("ctypeu");
	
		// $referrerbybrand = $this->uid; /* use on segment 8  */
		$usertype=intval(@$this->apps->session->getSession($this->config['SESSION_NAME'],'USERTYPE')->users);
			
		$confirm18=1;
		$receiveinfo=1;
		$n_status=1;
		$verified = 1;
	
		/*brand*/
		
		$sql = "SELECT id,code,preference,preferenceid,others FROM tbl_brand_preferences_references WHERE preferenceid IN ('{$brand1}','{$brandsub1}') GROUP BY preferenceid LIMIT 2";
		$rs = $this->apps->fetch($sql,1);		
		$this->logger->log(" birthday pass throught : ". $birthday);
		$this->logger->log($sql);
		$this->logger->log(json_encode($rs));
		if($rs){
			$brandarr = false;
			foreach($rs as $val){
				if($val['others']==1){
					$brandarr[$val['preferenceid']]['brand'] = "0000";
					$brandarr[$val['preferenceid']]['preference'] = "Z/Z/Z";
				}else{
					$brandarr[$val['preferenceid']]['brand'] = $val['code'];
					$brandarr[$val['preferenceid']]['preference'] = $val['preference'];
				}
			}
			$this->logger->log(json_encode($brandarr));
			if($brandarr){
				$brand1ref=$brandarr[$brand1]['preference'];
				$brand1=$brandarr[$brand1]['brand'];
				$brandsub1ref=$brandarr[$brandsub1]['preference'];
				$brandsub1=$brandarr[$brandsub1]['brand'];
				
			}
		}
		

		$sql = "SELECT * FROM beat_city_reference WHERE cityidmop='{$city}' LIMIT 1";
		$rs = $this->apps->fetch($sql);		
		$state=$rs['provinceid'];
		$city=$rs['id'];
		$cutterstr = 3 - strlen($state);
		if($cutterstr>0) {
			$appendstr = "";
			for($i=1;$i<=$cutterstr;$i++){
				$appendstr .="0";
			}
			$state = $appendstr.$state;
		}
		
		$appendstr = "";
		$cutterstrcity = 3 - strlen($city);
		if($cutterstrcity>0) {
			$appendstr = "";
			for($i=1;$i<=$cutterstrcity;$i++){
				$appendstr .="0";
			}
			$city = $appendstr.$city;
		}
		// $state="033";
		$companymobile = "ST1";
		/* maybe */
		// $city = "471";
		// $brand1 = "0004";
		// $brandsub1 = "F/M/Z";
		
		$devicemopid = @$this->apps->user->deviceid;	
		if($devicemopid=='')$devicemopid = 'BEATServer';
		if($sex=='')$sex='Male';
		
		$data = '<ProfileCollection>';
		if($registerid!=0) $data .= '<Profile ID="" RegistrationID="'.$registerid.'" >';
		else $data .= '<Profile ID="">';
		$data .= '<FieldCollection>';
		if($registerid==0) $data .= '<Field Name="FirstName"><Value>'.$firstname.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="LastName"><Value>'.$lastname.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="DateOfBirth"><Value>'.$birthday.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="Gender"><Value>'.$sex.'</Value></Field>';
		if($email)$data .= '<Field Name="Email"><Value>'.$email.'</Value></Field>';
		if($phone_number)$data .= '<Field Name="Mobile"><Value>'.$phone_number.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="State"><Value>'.$state.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="City"><Value>'.$city.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="GIIDNumber"><Value>'.$giidnumber.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="GIIDType"><Value>'.$giidtype.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="CompanyMobile"><Value>'.$companymobile.'</Value></Field>';
		if($brand1)	{
			$data .= '<Field Name="SmokingPrefBrand1"><Value>'.$brand1.'</Value></Field>';
			$data .= '<Field Name="SmokingPrefSubBrand1"><Value>'.$brand1ref.'</Value></Field>';
			}
		if($brandsub1){
			$data .= '<Field Name="SmokingPrefBrand2"><Value>'.$brandsub1.'</Value></Field>';
			$data .= '<Field Name="SmokingPrefSubBrand2"><Value>'.$brandsub1ref.'</Value></Field>';		
			}
		if($registerid==0)$data .= '<Field Name="ConfirmAbove18"><Value>'.$confirm18.'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="ReceiveInfo"><Value>'.$receiveinfo.'</Value></Field>';
	 
		if($registerid==0)$data .= '<Field Name="SpareText8"><Value>'.ltrim($description).'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="SpareText2"><Value>'.ltrim($socialaccount).'</Value></Field>';
		if($registerid==0)$data .= '<Field Name="SpareText5"><Value>'.$devicemopid.'</Value></Field>';
		
		if($ctypeu||$ctypes){
			if($ctypeu=="on"){
				$data .= '<Field Name="UCampaign"><Value>'.$Campaign.'</Value></Field>';
				$data .= '<Field Name="UPhase"><Value>'.$Phase.'</Value></Field>';
				$data .= '<Field Name="UAudience"><Value>'.$Audience.'</Value></Field>';
				$data .= '<Field Name="UMediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				$data .= '<Field Name="UOfferCode"><Value>'.$OfferCode.'</Value></Field>';
				$data .= '<Field Name="UOfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
			if($ctypes=="on"){
				if($registerid==0)$data .= '<Field Name="Campaign"><Value>'.$Campaign.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Phase"><Value>'.$Phase.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Audience"><Value>'.$Audience.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="MediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCode"><Value>'.$OfferCode.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
		}else{
		
			if($type=="AdminRegisterProfileDeDuplication" ){
				$data .= '<Field Name="UCampaign"><Value>'.$Campaign.'</Value></Field>';
				$data .= '<Field Name="UPhase"><Value>'.$Phase.'</Value></Field>';
				$data .= '<Field Name="UAudience"><Value>'.$Audience.'</Value></Field>';
				$data .= '<Field Name="UMediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				$data .= '<Field Name="UOfferCode"><Value>'.$OfferCode.'</Value></Field>';
				$data .= '<Field Name="UOfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
			if( $type=="AdminRegisterProfileDeDuplication" || $type=="AdminRegisterProfile"  || $type=="AdminUpdateProfile"){
				if($registerid==0)$data .= '<Field Name="Campaign"><Value>'.$Campaign.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Phase"><Value>'.$Phase.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="Audience"><Value>'.$Audience.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="MediaCategory"><Value>'.$MediaCategory.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCode"><Value>'.$OfferCode.'</Value></Field>';
				if($registerid==0)$data .= '<Field Name="OfferCategory"><Value>'.$OfferCategory.'</Value></Field>';
			}
		}
		
		$data .= '</FieldCollection>';
		$data .= '</Profile>';
		$data .= '</ProfileCollection>';
		$this->logger->log($data);
	// print $data;
	return $data;
	}
	
	
	
	function searchUserXML(){
		
		$email = $this->apps->_request("email");
		$giidnumber = $this->apps->_request("giidnumber");
		
		$data = '<ProfileCollection>';
		$data .= '<Profile ID="" RegistrationID="">';
		if($email!='') $data .= '<FieldCollection><Field Name="Email"><Value>'.$email.'*</Value></Field></FieldCollection>';
		if($giidnumber!='') $data .= '<FieldCollection><Field Name="GIIDNumber"><Value>'.$giidnumber.'*</Value></Field></FieldCollection>';
		$data .= '</Profile></ProfileCollection>';

		return $data;
	}
	
	
	
	function getProfileUserXML(){
		$registerid = intval($this->apps->_request("registerid"));
		if($registerid==0) return false;
		$data = '<ProfileCollection><Profile ID="" RegistrationID="'.$registerid.'" ResponseCode=""></Profile></ProfileCollection>';
		return $data;
	}
	
	function searchgiidxml(){
		
		$giidnumber = $this->apps->_request("giidnumber");
		
		$data = '<ProfileCollection>';
		$data .= '<Profile GIIDNumber="'.$giidnumber.'" ResponseCode="" />';
		$data .= '</ProfileCollection>';
		// print $data;exit;
		return $data;
	}
	
	function gettokensecret(){
	
		$data['uname'] = str_replace('_','.',trim($this->apps->_request('username')));
		$data['pwd'] = strip_tags($this->apps->_request('password'));
		$data['deid'] = strip_tags($this->apps->_request('deviceid'));
		$data['dedesc'] = strip_tags($this->apps->_request('devicedesc'));
		$data['snum'] = strip_tags($this->apps->_request('serialnumber'));
		// $data['snum'] = '8ca2ff9fc3d28495';
		$this->logger->log(json_encode($data));
		$xml= $this->mopClient->getsecrettoken($data);
		
		return $xml;
	}
	
	function XMLtoArray($XML)
{
    $xml_parser = xml_parser_create();
    xml_parse_into_struct($xml_parser, $XML, $vals);
    xml_parser_free($xml_parser);
  
    $_tmp='';
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_level!=1 && $x_type == 'close') {
            if (isset($multi_key[$x_tag][$x_level]))
                $multi_key[$x_tag][$x_level]=1;
            else
                $multi_key[$x_tag][$x_level]=0;
        }
        if ($x_level!=1 && $x_type == 'complete') {
            if ($_tmp==$x_tag)
                $multi_key[$x_tag][$x_level]=1;
            $_tmp=$x_tag;
        }
    }
   
    foreach ($vals as $xml_elem) {
        $x_tag=$xml_elem['tag'];
        $x_level=$xml_elem['level'];
        $x_type=$xml_elem['type'];
        if ($x_type == 'open')
            $level[$x_level] = $x_tag;
        $start_level = 1;
        $php_stmt = '$xml_array';
        if ($x_type=='close' && $x_level!=1)
            $multi_key[$x_tag][$x_level]++;
        while ($start_level < $x_level) {
            $php_stmt .= '[$level['.$start_level.']]';
            if (isset($multi_key[$level[$start_level]][$start_level]) && $multi_key[$level[$start_level]][$start_level])
                $php_stmt .= '['.($multi_key[$level[$start_level]][$start_level]-1).']';
            $start_level++;
        }
        $add='';
        if (isset($multi_key[$x_tag][$x_level]) && $multi_key[$x_tag][$x_level] && ($x_type=='open' || $x_type=='complete')) {
            if (!isset($multi_key2[$x_tag][$x_level]))
                $multi_key2[$x_tag][$x_level]=0;
            else
                $multi_key2[$x_tag][$x_level]++;
            $add='['.$multi_key2[$x_tag][$x_level].']';
        }
        if (isset($xml_elem['value']) && trim($xml_elem['value'])!='' && !array_key_exists('attributes', $xml_elem)) {
            if ($x_type == 'open')
                $php_stmt_main=$php_stmt.'[$x_type]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
            else
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.' = $xml_elem[\'value\'];';
            eval($php_stmt_main);
        }
        if (array_key_exists('attributes', $xml_elem)) {
            if (isset($xml_elem['value'])) {
                $php_stmt_main=$php_stmt.'[$x_tag]'.$add.'[\'content\'] = $xml_elem[\'value\'];';
                eval($php_stmt_main);
            }
            foreach ($xml_elem['attributes'] as $key=>$value) {
                $php_stmt_att=$php_stmt.'[$x_tag]'.$add.'[$key] = $value;';
                eval($php_stmt_att);
            }
        }
    }
    return $xml_array;
}
}
?>
