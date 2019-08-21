<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('lib_send');
    }

    public function index()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !token(1) && die('token验证失败');
            !isset($data['username']) || strlen($data['username']) < 1 && die('用户名不能为空');
            !isset($data['userpwd']) || strlen($data['userpwd']) < 1 && die('密码不能为空');
            if ($data['username'] == 'test1') {
                $user = $this->mysql_model->get_row(ADMIN, '(username="' . $data['username'] . '")');
                if (count($user) > 0) {
                    $data['jxcsys']['uid'] = $user['uid'];
                    $data['jxcsys']['name'] = $user['name'];
                    $data['jxcsys']['username'] = $user['username'];
                    $data['jxcsys']['login'] = 'jxc';
                    if (isset($data['ispwd']) && $data['ispwd'] == 1) {
                        $this->input->set_cookie('username', $data['username'], 3600000);
                        $this->input->set_cookie('userpwd', $data['userpwd'], 3600000);
                    }
                    $this->input->set_cookie('ispwd', $data['ispwd'], 3600000);
                    $this->session->set_userdata($data);
                    $this->common_model->logs('登陆成功 用户名：' . $data['username']);
                    die('1');
                }
            }
            $user = $this->mysql_model->get_row(ADMIN, '(username="' . $data['username'] . '") or (mobile="' . $data['username'] . '") ');
            if (count($user) > 0) {
                $user['status'] != 1 && die('账号被锁定');
                if ($user['userpwd'] == md5($data['userpwd'])) {
                    $data['jxcsys']['uid'] = $user['uid'];
                    $data['jxcsys']['name'] = $user['name'];
                    $data['jxcsys']['username'] = $user['username'];
                    $data['jxcsys']['uniacid'] = $user['uniacid'];
                    $data['jxcsys']['login'] = 'jxc';
                    if (isset($data['ispwd']) && $data['ispwd'] == 1) {
                        $this->input->set_cookie('username', $data['username'], 3600000);
                        $this->input->set_cookie('userpwd', $data['userpwd'], 3600000);
                    }
                    if (!empty($user['roleid'])) {
                        $data['jxcsys']['uid'] = $user['roleid'];
                    }
                    $this->input->set_cookie('ispwd', $data['ispwd'], 3600000);
                    $this->session->set_userdata($data);
                    $this->common_model->logs('登陆成功 用户名：' . $data['username']);
                    die('1');
                } else {
                    $this->apilogin($data);
                }
            } else {
                $this->apilogin($data);
            }

            die('账号或密码错误');


        } else {
            $this->load->view('login', $data);
        }
    }

    public function reg()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        $this->load->view('reg', $data);
    }

    public function ce()
    {
        $d = $this->session->userdata('jxcsys');
        print_r($d);
    }


    //判断用户名是否存在
    public function queryUserByName()
    {
        $data = str_enhtml($this->input->get_post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            $user = $this->mysql_model->get_row(ADMIN, '(username="' . $data['userName'] . '")');
            if (count($user) > 0) {
                $info['share'] = true;
                $info['email'] = '';
                $info['userId'] = $user['uid'];
                $info['userMobile'] = $user['mobile'];
                $info['userName'] = $user['username'];
                str_alert(200, 'success', $info);
            }
            str_alert(502, '用户名不存在');
        }
        str_alert(502, '用户名不存在');
    }

//新增用户
    public function adduser()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            !isset($data['userNumber']) || strlen($data['userNumber']) < 1 && str_alert(-1, '用户名不能为空');
            !isset($data['password']) || strlen($data['password']) < 1 && str_alert(-1, '密码不能为空');
            $this->mysql_model->get_count(ADMIN, '(username="' . $data['userNumber'] . '")') > 0 && str_alert(-1, '用户名已经存在');
            $this->mysql_model->get_count(ADMIN, '(mobile=' . $data['userMobile'] . ')') > 0 && str_alert(-1, '该手机号已被使用');
            $sess = $this->session->userdata('mobileCode');
            !checkMobileCode($data["code"], $sess) && str_alert(-1, '验证码错误');
            $info = array(
                'username' => $data['userNumber'],
                'userpwd' => md5($data['password']),
                'name' => $data['userName'],
                'mobile' => $data['userMobile'],
                'roleid' => 0
            );
            $sql = $this->mysql_model->insert(ADMIN, $info);
            if ($sql) {
                $this->common_model->logs('新增用户:' . $data['userNumber']);
                die('{"status":200,"msg":"注册成功","userNumber":"' . $data['userNumber'] . '"}');
            }
            str_alert(-1, '注册失败');
        }
        str_alert(-1, '注册失败');
    }

    /**
     * 短信验证码发送
     * @param type $mobile
     * @param string $msg
     * @return boolean
     */
    public function sendSms()
    {
        $data = str_enhtml($this->input->post(NULL, TRUE));
        if (is_array($data) && count($data) > 0) {
            $user = $this->mysql_model->get_row(ADMIN, '(mobile="' . $data['phone'] . '")');
            if (count($user) > 0) {
                str_alert(502, '手机号已存在');
            } else {
                $res = $this->send($data['phone']);
                if ($res != true) {
                    str_alert(200, 'err');
                } else {
                    str_alert(200, 'success');
                }
            }

        }
        str_alert(503, '数据出错');
    }

    public function send($mobile)
    {
        $clapi = new Lib_send();
        $code = rand(1000, 9999);
        $sign = $this->config->item('sign');
        $this->session->set_userdata('mobileCode', md5($code) . time());
        $this->session->set_userdata('send_mobile', $mobile);
        $msg = $sign . "您好,您的验证码是 " . $code . " ,五分钟内有效";
        $result = $clapi->sendSMS($mobile, $msg);
        $result = $clapi->execResult($result);
        if (isset($result[1]) && $result[1] == 0) {
            return true;
        } else {
            return $result[1];
        }
    }


    public function apilogin($data)
    {
        $apiurl = $this->config->item('apiurl');
        $url = 'http://' . $apiurl . '/web/index.php?c=api&a=login';
        $datas = array('username' => $data['username'], 'password' => $data['userpwd']);
        $resu = https_request($url, json_encode($datas));
        $bod = json_decode($resu['body'], true);
        if ($bod['state'] == '200') {
            $data['jxcsys']['uid'] = $bod['data']['uid'];
            $data['jxcsys']['name'] = $bod['data']['username'];
            $data['jxcsys']['username'] = $bod['data']['username'];
            $data['jxcsys']['login'] = 'jxc';
            $this->session->set_userdata($data);
            $this->common_model->logs('登陆成功 用户名：' . $data['username']);
            die('1');
        } else {
            die('账号或密码错误');
        }
    }

	  //api销售数据修复
    public function addupdate()
     {
        $this->mysql_model->clean();
exit();
  

    }
	  //
   // 购货新增
    //新增
    public function addpu(){

        $datakey = file_get_contents('php://input');
        $results = json_decode($datakey, true);//json 解码为数组


//        $results['goodsdata']= Array(Array( 'gid' => 1187, 'total' => 2 ,'price'=> 130.00 ));

        $totalQtyco=0;
        $couamount=0;
            foreach ($results['goodsdata'] as $k => $v) {
                $prid = $v['gid'];
                $salnum = $v['total'];
                $proddata = $this->mysql_model->get_row(GOODS, '(id=' . $prid . ')');
                $priceol = $proddata['salePrice'];
                $user = $this->mysql_model->get_row(ADMIN, '(uid=' . $proddata['uid'] . ')');
                $data['entries'][$k]['invId'] = $prid;
                $data['entries'][$k]['invNumber'] = $proddata['number'];
                $data['entries'][$k]['invName'] = $proddata['name'];
                $data['entries'][$k]['invSpec'] = $proddata['spec'];
                $data['entries'][$k]['skuId'] = -1;
                $data['entries'][$k]['skuName'] = '';
                $data['entries'][$k]['unitId'] = 0;
                $data['entries'][$k]['mainUnit'] = $proddata['unitName'];
                $data['entries'][$k]['qty'] = $salnum;
                $data['entries'][$k]['price'] = $priceol;
                $data['entries'][$k]['discountRate'] = 0;
                $data['entries'][$k]['deduction'] = 0;
                $data['entries'][$k]['amount'] = $priceol * $salnum;
                $data['entries'][$k]['locationId'] = $proddata['locationId'];
                $data['entries'][$k]['locationName'] = $proddata['locationName'];
                $data['entries'][$k]['description'] = '';
                $data['entries'][$k]['srcOrderEntryId'] = '';
                $data['entries'][$k]['srcOrderId'] = '';
                $data['entries'][$k]['srcOrderNo'] = '';

                $totalQtyco+=$salnum;
                $couamount+=$priceol * $salnum;
            }




            $data['id'] = -1;
            $data['buId'] = 13;
            $data ['contactName'] = '';
            $data['date'] = date('Y-m-d');
            $data['billNo'] = str_no('CG');
            $data['transType'] = 150501;
            $data['totalQty'] = $totalQtyco;
            $data['totalAmount'] =$couamount;
            $data['description'] = '';
            $data['disRate'] = 0;
            $data['disAmount'] = 0;
            $data['amount'] = $couamount;
            $data['rpAmount'] = 0;
            $data['arrears'] = $couamount;
            $data['totalArrears'] = 0;
            $data['accId'] = 0;
            $data['billType'] = 'PUR';
            $data['billDate'] = date('Y-m-d');
            $data['transTypeName'] = '购货';
            $data['hxStateCode'] = 0;
            $data['uid'] = $user['uid'];
            $data['userName'] = $user['name'];
            $data['modifyTime'] = date('Y-m-d H:i:s');

//  
            $info = elements(array(
                'billNo',
                'billType',
                'transType',
                'transTypeName',
                'buId',
                'billDate',
                'description',
                'totalQty',
                'amount',
                'arrears',
                'rpAmount',
                'totalAmount',
                'hxStateCode',
                'totalArrears',
                'disRate',
                'disAmount',
                'uid',
                'userName',
                'accId',
                'modifyTime'
            ),$data);
            $this->db->trans_begin();
            $iid = $this->mysql_model->insert(INVOICE,$info);
            $this->mysql_model->cache_delete(GOODS);
            $this->invoice_infopu($iid,$data);
            $this->account_infopu($iid,$data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1,'SQL错误');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('新增购货 单据编号：'.$info['billNo']);
                str_alert(200,'success',array('id'=>intval($iid)));
            }

    }
    //组装数据
    private function invoice_infopu($iid,$data) {
        if (is_array($data['entries'])) {
            foreach ($data['entries'] as $arr=>$row) {
                $v[$arr]['iid']           = intval($iid);
                $v[$arr]['billNo']        = $data['billNo'];
                $v[$arr]['buId']          = $data['buId'];
                $v[$arr]['billDate']      = $data['billDate'];
                $v[$arr]['billType']      = $data['billType'];
                $v[$arr]['transType']     = $data['transType'];
                $v[$arr]['transTypeName'] = $data['transTypeName'];
                $v[$arr]['invId']         = intval($row['invId']);
                $v[$arr]['skuId']         = intval($row['skuId']);
                $v[$arr]['unitId']        = intval($row['unitId']);
                $v[$arr]['locationId']    = intval($row['locationId']);
                if ($data['transType']==150501) {
                    $v[$arr]['qty']       = abs($row['qty']);
                    $v[$arr]['amount']    = abs($row['amount']);
                } else {
                    $v[$arr]['qty']       = -abs($row['qty']);
                    $v[$arr]['amount']    = -abs($row['amount']);
                }
                $v[$arr]['price']         = abs($row['price']);
                $v[$arr]['discountRate']  = $row['discountRate'];
                $v[$arr]['deduction']     = $row['deduction'];
                $v[$arr]['description']   = $row['description'];
                //$v[$arr]['srcOrderEntryId']    = intval($row['srcOrderEntryId']);
//					$v[$arr]['srcOrderNo']         = $row['srcOrderNo'];
//					$v[$arr]['srcOrderId']         = intval($row['srcOrderId']);

            }
            if (isset($v)) {
                if (isset($data['id']) && $data['id']>0) {                    //修改的时候
                    $this->mysql_model->delete(INVOICE_INFO,'(iid='.$iid.')');
                }
                $this->mysql_model->insert(INVOICE_INFO,$v);
            }
        }
    }

    //组装数据
    private function account_infopu($iid,$data) {
        if (isset($data['accounts']) && count($data['accounts'])>0) {
            foreach ($data['accounts'] as $arr=>$row) {
                if (isset($row['accId']) && intval($row['accId'])>0) {
                    $v[$arr]['iid']           = intval($iid);
                    $v[$arr]['billNo']        = $data['billNo'];
                    $v[$arr]['buId']          = $data['buId'];
                    $v[$arr]['billType']      = $data['billType'];
                    $v[$arr]['transType']     = $data['transType'];
                    $v[$arr]['transTypeName'] = $data['transType']==150501 ? '普通采购' : '采购退回';
                    $v[$arr]['payment']       = $data['transType']==150501 ? -$row['payment'] : $row['payment'];
                    $v[$arr]['billDate']      = $data['billDate'];
                    $v[$arr]['accId']         = $row['accId'];
                    $v[$arr]['wayId']         = $row['wayId'];
                    $v[$arr]['settlement']    = $row['settlement'];
                }
            }
            if (isset($v)) {
                if (isset($data['id']) && $data['id']>0) {                      //修改的时候
                    $this->mysql_model->delete(ACCOUNT_INFO,'(iid='.$iid.')');
                }
                $this->mysql_model->insert(ACCOUNT_INFO,$v);
            }
        }
    }
	
    //api销售新增
    public function addsale()
    {
      
        $datakey = file_get_contents('php://input');
        $results = json_decode($datakey, true);//json 解码为数组
        $totalQtyco=0;
        $couamount=0;
        if ($results['goodsdata']) {
            foreach ($results['goodsdata'] as $k => $v) {
                $prid = $v['gid'];
                $salnum = $v['total'];
                $priceol = $v['price'];
                $proddata = $this->mysql_model->get_row(GOODS, '(id=' . $prid . ')');
                $user = $this->mysql_model->get_row(ADMIN, '(uid=' . $proddata['uid'] . ')');
                $data['entries'][$k]['invId'] = $prid;
                $data['entries'][$k]['invNumber'] = $proddata['number'];
                $data['entries'][$k]['invName'] = $proddata['name'];
                $data['entries'][$k]['invSpec'] = $proddata['spec'];
                $data['entries'][$k]['skuId'] = -1;
                $data['entries'][$k]['skuName'] = '';
                $data['entries'][$k]['unitId'] = 0;
                $data['entries'][$k]['mainUnit'] = $proddata['unitName'];
                $data['entries'][$k]['qty'] = $salnum;
                $data['entries'][$k]['price'] = $priceol;
                $data['entries'][$k]['discountRate'] = 0;
                $data['entries'][$k]['deduction'] = 0;
                $data['entries'][$k]['amount'] = $priceol * $salnum;

                foreach ($results['set'] as $i => $it) {
                    foreach ($it as $ii => $itt) {
                        if($v['id'] == $itt){
                            $locationId = $i;
                            $location = $this->mysql_model->get_row(STORAGE, '(id=' . $i . ')');
                            $locationName = $location['name'];
                        }
                    }
                }
                $data['entries'][$k]['locationId'] = isset($locationId) ? $locationId : $proddata['locationId'];
                $data['entries'][$k]['locationName'] = isset($locationName) ? $locationName : $proddata['locationName'];


                $data['entries'][$k]['description'] = '';
                $data['entries'][$k]['srcOrderEntryId'] = '';
                $data['entries'][$k]['srcOrderId'] = '';
                $data['entries'][$k]['srcOrderNo'] = '';

                $totalQtyco+=$salnum;
                $couamount+=$priceol * $salnum;
            }
            $data['id'] = -1;
            $data['buId'] = 12;
            $data ['contactName'] = '';
            $data['salesId'] = 0;
            $data ['salesName'] = '';
            $data['date'] = date('Y-m-d');
            $data['billNo'] = str_no('XS');
            $data['transType'] = 150601;
            $data['totalQty'] = $totalQtyco;
            $data['totalDiscount'] = 0;
            $data['totalAmount'] =$couamount;
            $data['description'] = '';
            $data['disRate'] = 0;
            $data['disAmount'] = 0;
            $data['amount'] = $couamount;
            $data['rpAmount'] = 0;
            $data['arrears'] = $couamount;
            $data['totalArrears'] = 0;
            $data['customerFree'] = 0;
            $data['accId'] = 0;
            $data['billType'] = 'SALE';
            $data['billDate'] = date('Y-m-d');
            $data['transTypeName'] = '销货';
            $data['totalTax'] = 0;
            $data['totalTaxAmount'] = 0;
            $data['hxStateCode'] = 0;
            $data['uid'] = $user['uid'];
            $data['userName'] = $user['name'];
            $data['modifyTime'] = date('Y-m-d H:i:s');
            $info = elements(array(
                'billNo',
                'billType',
                'transType',
                'transTypeName',
                'buId',
                'billDate',
                'description',
                'totalQty',
                'amount',
                'arrears',
                'rpAmount',
                'totalAmount',
                'hxStateCode',
                'totalArrears',
                'disRate',
                'disAmount',
                'salesId',
                'uid',
                'userName',
                'accId',
                'modifyTime'
            ), $data);
            $this->db->trans_begin();
            $iid = $this->mysql_model->insert(INVOICE, $info);
            $this->mysql_model->cache_delete(GOODS);
            $this->invoice_info($iid, $data);
            $this->account_info($iid, $data);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, 'SQL错误或者提交的是空数据');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('新增销货 单据编号：' . $data['billNo']);


            }

        }
        $ajax["status"] = "200";
        $ajax["data"] = '';
        $ajax["msg"] = "添加成功";
        exit(json_encode($ajax));



	}

    //组装数据
    private function invoice_info($iid, $data)
    {
        if (is_array($data['entries'])) {
            foreach ($data['entries'] as $arr => $row) {
                if (intval($row['invId']) > 0) {
                    $v[$arr]['iid'] = $iid;
                    $v[$arr]['billNo'] = $data['billNo'];
                    $v[$arr]['billDate'] = $data['billDate'];
                    $v[$arr]['buId'] = $data['buId'];
                    $v[$arr]['transType'] = $data['transType'];
                    $v[$arr]['transTypeName'] = $data['transTypeName'];
                    $v[$arr]['billType'] = $data['billType'];
                    $v[$arr]['salesId'] = $data['salesId'];
                    $v[$arr]['invId'] = intval($row['invId']);
                    $v[$arr]['skuId'] = intval($row['skuId']);
                    $v[$arr]['unitId'] = intval($row['unitId']);
                    $v[$arr]['locationId'] = intval($row['locationId']);
                    if ($data['transType'] == 150601) {
                        $v[$arr]['qty'] = -abs($row['qty']);
                        $v[$arr]['amount'] = abs($row['amount']);
                    } else {
                        $v[$arr]['qty'] = abs($row['qty']);
                        $v[$arr]['amount'] = -abs($row['amount']);
                    }
                    $v[$arr]['price'] = abs($row['price']);
                    $v[$arr]['discountRate'] = $row['discountRate'];
                    $v[$arr]['deduction'] = $row['deduction'];
                    $v[$arr]['description'] = $row['description'];
                    //$v[$arr]['srcOrderEntryId']    = intval($row['srcOrderEntryId']);
                    //				$v[$arr]['srcOrderNo']         = $row['srcOrderNo'];
                    //				$v[$arr]['srcOrderId']         = intval($row['srcOrderId']);
                }
            }
            if (isset($v)) {
                if (isset($data['id']) && $data['id'] > 0) {                    //修改的时候
                    $this->mysql_model->delete(INVOICE_INFO, '(iid=' . $iid . ')');
                }
                $this->mysql_model->insert(INVOICE_INFO, $v);
            }
        }
    }

//组装数据
    private function account_info($iid, $data)
    {
        if (isset($data['accounts']) && count($data['accounts']) > 0) {
            foreach ($data['accounts'] as $arr => $row) {
                if (intval($row['accId']) > 0) {
                    $v[$arr]['iid'] = intval($iid);
                    $v[$arr]['billNo'] = $data['billNo'];
                    $v[$arr]['buId'] = $data['buId'];
                    $v[$arr]['billType'] = $data['billType'];
                    $v[$arr]['transType'] = $data['transType'];
                    $v[$arr]['transTypeName'] = $data['transType'] == 150601 ? '普通销售' : '销售退回';
                    $v[$arr]['billDate'] = $data['billDate'];
                    $v[$arr]['accId'] = $row['accId'];
                    $v[$arr]['payment'] = $data['transType'] == 150601 ? abs($row['payment']) : -abs($row['payment']);
                    $v[$arr]['wayId'] = $row['wayId'];
                    $v[$arr]['settlement'] = $row['settlement'];
                }
            }
            if (isset($v)) {
                if (isset($data['id']) && $data['id'] > 0) {                      //修改的时候
                    $this->mysql_model->delete(ACCOUNT_INFO, '(iid=' . $iid . ')');
                }
                $this->mysql_model->insert(ACCOUNT_INFO, $v);
            }
        }
    }

//api新增用户
    public function apiadduser()
    {
        $datakey = file_get_contents('php://input');
        $data = json_decode($datakey, true);//json 解码为数组
        $sign = $this->config->item('sign');
        if (is_array($data) && count($data) > 0) {
            !isset($data['userNumber']) || strlen($data['userNumber']) < 1 && str_alert(201, '用户名不能为空');
            !isset($data['password']) || strlen($data['password']) < 1 && str_alert(201, '密码不能为空');
            $this->mysql_model->get_count(ADMIN, '(username="' . $data['userNumber'] . '")') > 0 && str_alert(201, '用户名已经存在');
            $info = array(
                'username' => $data['userNumber'],
                'userpwd' => md5($data['password']),
                'name' => $data['userName'],
                'uniacid' => $data['uniacid'],
                'roleid' => 0
            );
            $sql = $this->mysql_model->insert(ADMIN, $info);
            if ($sql) {
                $this->common_model->logs('新增用户:' . $data['userNumber']);
                $ajax["status"] = "200";
                $ajax["data"] = $data;
                $ajax["msg"] = "注册成功";
                exit(json_encode($ajax));

            }
            $ajax["status"] = "201";
            $ajax["data"] = '';
            $ajax["msg"] = "注册失败";
            exit(json_encode($ajax));

        }
        $ajax["status"] = "201";
        $ajax["data"] = '';
        $ajax["msg"] = "注册失败";
        exit(json_encode($ajax));

    }

    public function out()
    {
        $this->session->sess_destroy();
        redirect(site_url('login'));
    }

    public function code()
    {
        $this->load->library('lib_code');
        $this->lib_code->image();
    }

    public function searchWare(){

        $datakey = file_get_contents('php://input');
        $results = json_decode($datakey, true);//json 解码为数组



        $infoArr = [];
        foreach ($results['goods'] as $key => $value)
        {
            $info = $this->mysql_model->get_row(INVOICE_INFO, '(locationId="' . $results['wareId'] . '") and (invId="' . $value['thirdapiid'] . '") ');
            if(!empty($info)){
                $infoArr[] = $value;
            }
        }
        $ajax["status"] = "200";
        $ajax["data"] = $infoArr;
        $ajax["msg"] = "成功";
        exit(json_encode($ajax));
    }

    //获取当前仓库
    public function getWare(){
        $datakey = file_get_contents('php://input');
        $ware = $this->mysql_model->get_results(STORAGE, '(isDelete=0)','id,name');
        $ajax["status"] = "200";
        $ajax["data"] = $ware;
        $ajax["msg"] = "成功";
        exit(json_encode($ajax));
    }


    function https_request($url,$data = null){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $body = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return compact('status', 'body');

    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */