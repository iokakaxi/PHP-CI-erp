<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model{

	public function __construct(){
  		parent::__construct();
		$this->jxcsys = $this->session->userdata('jxcsys');
	}
	
	//判断是否登陆
	public function is_login() {
	    if (!$this->jxcsys) return false; 
		if ($this->jxcsys['login'] != 'jxc') return false; 
		return true;
	}
    
	//检测是否有权限
	public function checkpurview($id=0) {
	    !$this->is_login() && redirect(site_url('login'));
		if ($id<1) return true;
		$data = $this->mysql_model->get_row(ADMIN,'(uid='.$this->jxcsys['uid'].')'); 
		if (count($data)>0) {
		    //$data['status'] != 1 && die('该账号已被锁定');    
			if ($data['roleid']==0) {
			    return true; 
			} else {	
			    $lever = strlen($data['lever'])>0 ? explode(',',$data['lever']) : array();	
				if (in_array($id,$lever)) return true;
			}
		}
		alert('对不起，您没有此页面的管理权',site_url('home/main'));
		str_alert(-1,'对不起，您没有此页面的管理权');  	  
	}
	
	//写入日志
	public function logs($info) {
		$data['userId']     =  $this->jxcsys['uid'];
		$data['name']       =  $this->jxcsys['name'];
		$data['ip']         =  $this->input->ip_address();
		$data['log']        =  $info;
		$data['loginName']  =  $this->jxcsys['username'];
		$data['adddate']    =  date('Y-m-d H');
		$data['modifyTime'] =  date('Y-m-d H:i:s');
		$this->mysql_model->insert(LOG,$data);		
	}	
	
	//写入配置
	public function insert_option($key,$val,$uid) {
		if (!$this->get_optioncheck($key,$uid)) {
			$data['uid']  = $uid;
			$data['option_name']  = $key;
			$data['option_value'] = serialize($val);
			return $this->mysql_model->insert(OPTIONS,$data);
		}
		return $this->update_option($key,$val,$uid);
	}
	
	//更新配置
	public function update_option($key,$val,$uid) {
		if(!empty($uid)){
			$data['option_value'] = serialize($val);
			return $this->mysql_model->update(OPTIONS,$data,'(option_name="'.$key.'" and uid='.$uid.')');
		}else{
			$data['option_value'] = serialize($val);
			return $this->mysql_model->update(OPTIONS,$data,'(option_name="'.$key.'")');
		}

	}
	//获取配置
	public function get_optioncheck($key,$uid) {
		if(!empty($uid)){
			$option_value = $this->mysql_model->get_row(OPTIONS,'(option_name="'.$key.'" and uid='.$uid.')','option_value');
		}else{
			$option_value = $this->mysql_model->get_row(OPTIONS,'(option_name="'.$key.'")','option_value');
		}

		return $option_value ? unserialize($option_value) : '';
	}
 
	//获取配置
	public function get_option($key,$uid) {
		if(!empty($uid)){
			$option_value = $this->mysql_model->get_row(OPTIONS,'(option_name="'.$key.'" and uid='.$uid.')','option_value');
			if(empty($option_value)){
				$option_value='a:10:{s:11:"companyName";s:9:"进销存";s:11:"companyAddr";s:9:"杨浦区";s:5:"phone";s:5:"12312";s:3:"fax";s:3:"312";s:8:"postcode";s:4:"3123";s:9:"qtyPlaces";s:1:"1";s:11:"pricePlaces";s:1:"1";s:12:"amountPlaces";s:1:"2";s:10:"valMethods";s:13:"movingAverage";s:18:"requiredCheckStore";s:1:"1";}';
			}
		}else{
			$option_value = $this->mysql_model->get_row(OPTIONS,'(option_name="'.$key.'")','option_value');
		}
		return $option_value ? unserialize($option_value) : '';
	}
	
	
	
}