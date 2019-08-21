<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventory extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->common_model->checkpurview();
        $this->jxcsys = $this->session->userdata('jxcsys');
        $this->load->library('lib_pinyin');
    }

    //商品列表
    public function index()
    {
        $v = array();
        $data['status'] = 200;
        $data['msg'] = 'success';
        $page = max(intval($this->input->get_post('page', TRUE)), 1);
        $rows = max(intval($this->input->get_post('rows', TRUE)), 100);
        $skey = str_enhtml($this->input->get_post('skey', TRUE));
        $categoryid = intval($this->input->get_post('assistId', TRUE));
        $barCode = intval($this->input->get_post('barCode', TRUE));
        $where = '';
        $where .= $skey ? ' and (name like "%' . $skey . '%" or barCode like "%' . $skey . '%" or spec like "%' . $skey . '%")' : '';
        $where .= $barCode ? ' and barCode="' . $barCode . '"' : '';
        if ($categoryid > 0) {
            $cid = array_column($this->mysql_model->get_results(CATEGORY, '(1=1) and find_in_set(' . $categoryid . ',path)'), 'id');
            if (count($cid) > 0) {
                $cid = join(',', $cid);
                $where .= ' and categoryid in(' . $cid . ') ';
            }
        }
        $where .= ' and (uid=' . $this->jxcsys['uid'] . ')';
        $offset = $rows * ($page - 1);
        $data['data']['page'] = $page;                                                             //当前页
        $data['data']['records'] = $this->data_model->get_goods($where, 3);   //总条数
        $data['data']['total'] = ceil($data['data']['records'] / $rows);
        $list = $this->data_model->get_goods($where . 'order by id desc limit ' . $offset . ',' . $rows . '');
        foreach ($list as $arr => $row) {
            $v[$arr]['amount'] = (float)$row['iniamount'];
            $v[$arr]['barCode'] = $row['barCode'];
            $v[$arr]['categoryName'] = $row['categoryName'];
            $v[$arr]['currentQty'] = $row['totalqty'];                            //当前库存
            $v[$arr]['delete'] = intval($row['disable']) == 1 ? true : false;   //是否禁用
            $v[$arr]['discountRate'] = 0;
            $v[$arr]['id'] = intval($row['id']);
            $v[$arr]['isSerNum'] = intval($row['isSerNum']);
            $v[$arr]['josl'] = $row['josl'];
            $v[$arr]['name'] = $row['name'];
            $v[$arr]['number'] = $row['number'];
            $v[$arr]['pinYin'] = $row['pinYin'];
            $v[$arr]['locationId'] = intval($row['locationId']);
            $v[$arr]['locationName'] = $row['locationName'];
            $v[$arr]['locationNo'] = '';
            $v[$arr]['purPrice'] = $row['purPrice'];
            $v[$arr]['quantity'] = $row['iniqty'];
            $v[$arr]['salePrice'] = $row['salePrice'];
            $v[$arr]['skuClassId'] = $row['skuClassId'];
            $v[$arr]['spec'] = $row['spec'];
            $v[$arr]['unitCost'] = $row['iniunitCost'];
            $v[$arr]['unitId'] = intval($row['unitId']);
            $v[$arr]['unitName'] = $row['unitName'];
            $v[$arr]['unified'] =intval($row['unified']) == 1 ? true : false;
            $v[$arr]['colorType'] = $row['colorType'];
            $v[$arr]['diaopaiPrice'] = $row['diaopaiPrice'];
        }
        $data['data']['rows'] = $v;

        die(json_encode($data));

    }

    //商品选择
    public function listBySelected()
    {
        $v = array();
        $contactid = intval($this->input->post('contactId', TRUE));
        $id = intval($this->input->post('ids', TRUE));
        $data['status'] = 200;
        $data['msg'] = 'success';
        $list = $this->mysql_model->get_results(GOODS, '(isDelete=0) and (disable=0) and id=' . $id . '');
        foreach ($list as $arr => $row) {
            $v[$arr]['amount'] = (float)$row['amount'];
            $v[$arr]['barCode'] = $row['barCode'];
            $v[$arr]['categoryName'] = $row['categoryName'];
            $v[$arr]['currentQty'] = 0;                                           //当前库存
            $v[$arr]['delete'] = intval($row['disable']) == 1 ? true : false;   //是否禁用
            $v[$arr]['discountRate'] = 0;
            $v[$arr]['id'] = intval($row['id']);
            $v[$arr]['isSerNum'] = intval($row['isSerNum']);
            $v[$arr]['josl'] = '';
            $v[$arr]['name'] = $row['name'];
            $v[$arr]['number'] = $row['number'];
            $v[$arr]['pinYin'] = $row['pinYin'];
            $v[$arr]['locationId'] = intval($row['locationId']);
            $v[$arr]['locationName'] = $row['locationName'];
            $v[$arr]['locationNo'] = '';
            $v[$arr]['purPrice'] = $row['purPrice'];
            $v[$arr]['quantity'] = $row['quantity'];
            $v[$arr]['salePrice'] = $row['salePrice'];
            $v[$arr]['skuClassId'] = $row['skuClassId'];
            $v[$arr]['spec'] = $row['spec'];
            $v[$arr]['unitCost'] = $row['unitCost'];
            $v[$arr]['unitId'] = intval($row['unitId']);
            $v[$arr]['unitName'] = $row['unitName'];
        }
        $data['data']['result'] = $v;
        die(json_encode($data));
    }


    //获取信息
    public function query()
    {
        $id = intval($this->input->post('id', TRUE));
        str_alert(200, 'success', $this->get_goods_info($id));
    }


    //检测编号
    public function getNextNo()
    {
        $skey = str_enhtml($this->input->post('skey', TRUE));
        $this->mysql_model->get_count(GOODS, '(isDelete=0) and (number="' . $skey . '")') > 0 && str_alert(-1, '商品编号已经存在');
        str_alert(200, 'success');
    }

    //检测条码
    public function checkBarCode()
    {
        $barCode = str_enhtml($this->input->post('barCode', TRUE));
//        $this->mysql_model->get_count(GOODS, '(isDelete=0) and (barCode="' . $barCode . '")') > 0 && str_alert(-1, '商品条码已经存在');
        str_alert(200, 'success');
    }

    //检测规格
    public function checkSpec()
    {
        $spec = str_enhtml($this->input->post('spec', TRUE));
        $this->mysql_model->get_count(ASSISTSKU, '(isDelete=0) and (skuName="' . $spec . '")') > 0 && str_alert(-1, '商品规格已经存在');
        str_alert(200, 'success');
    }

    //检测名称
    public function checkname()
    {
        $skey = str_enhtml($this->input->post('barCode', TRUE));
        echo '{"status":200,"msg":"success","data":{"number":""}}';
    }

    //获取图片信息
    public function getImagesById()
    {
        $v = array();
        $id = intval($this->input->post('id', TRUE));
        $list = $this->mysql_model->get_results(GOODS_IMG, '(invId=' . $id . ') and isDelete=0');
        foreach ($list as $arr => $row) {
            $v[$arr]['pid'] = $row['id'];
            $v[$arr]['status'] = 1;
            $v[$arr]['name'] = $row['name'];
            $v[$arr]['url'] = site_url() . '/basedata/inventory/getImage?action=getImage&pid=' . $row['id'];
            $v[$arr]['thumbnailUrl'] = site_url() . '/basedata/inventory/getImage?action=getImage&pid=' . $row['id'];
            $v[$arr]['deleteUrl'] = '';
            $v[$arr]['deleteType'] = '';
        }
        $data['status'] = 200;
        $data['msg'] = 'success';
        $data['files'] = $v;
        die(json_encode($data));
    }

    //上传图片信息
    public function uploadImages()
    {
        require_once './application/libraries/UploadHandler.php';
        $config = array(
            'script_url' => base_url() . 'inventory/uploadimages',
            'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/data/upfile/goods/',
            'upload_url' => base_url() . 'data/upfile/goods/',
            'delete_type' => '',
            'print_response' => false
        );
        $uploadHandler = new UploadHandler($config);
        $list = (array)json_decode(json_encode($uploadHandler->response['files'][0]), true);
        $newid = $this->mysql_model->insert(GOODS_IMG, $list);
        $files[0]['pid'] = intval($newid);
        $files[0]['status'] = 1;
        $files[0]['size'] = (float)$list['size'];
        $files[0]['name'] = $list['name'];
        $files[0]['url'] = site_url() . '/basedata/inventory/getImage?action=getImage&pid=' . $newid;
        $files[0]['thumbnailUrl'] = site_url() . '/basedata/inventory/getImage?action=getImage&pid=' . $newid;
        $files[0]['deleteUrl'] = '';
        $files[0]['deleteType'] = '';
        $data['status'] = 200;
        $data['msg'] = 'success';
        $data['files'] = $files;
        die(json_encode($data));
    }

    //上传文件信息
    public function uploadFile()
    {
        require_once './application/libraries/UploadHandler.php';
        $config = array(
            'script_url' => base_url() . 'inventory/uploadFile',
            'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']) . '/data/upfile/',
            'upload_url' => base_url() . 'data/upfile/',
            'delete_type' => '',
            'print_response' => false
        );
        $uploadHandler = new UploadHandler($config);
        $list = (array)json_decode(json_encode($uploadHandler->response['files'][0]), true);

////		$newid = $this->mysql_model->insert(GOODS_IMG,$list);
//		$files[0]['pid']          = intval($newid);
//		$files[0]['status']       = 1;
//		$files[0]['size']         = (float)$list['size'];
//		$files[0]['name']         = $list['name'];
//		$files[0]['url']          = site_url().'/basedata/inventory/getImage?action=getImage&pid='.$newid;
//		$files[0]['thumbnailUrl'] = site_url().'/basedata/inventory/getImage?action=getImage&pid='.$newid;
//		$files[0]['deleteUrl']    = '';
//		$files[0]['deleteType']   = '';
//		$data['status'] = 200;
//		$data['msg']    = 'success';
//		$data['files']  = $files;

        $return = array(
            'code' => 0,
            'msg' => '',
            'data' => array('src' => './data/upfile/' . $list['name'], 'url' => base_url() . 'data/upfile/' . $list['name'])
        );

        die(json_encode($return));
    }

    //保存上传图片信息
    public function addImagesToInv()
    {
        $data = $this->input->post('postData');
        if (strlen($data) > 0) {
            $v = $s = array();
            $data = (array)json_decode($data, true);
            $id = isset($data['id']) ? $data['id'] : 0;
            !isset($data['files']) || count($data['files']) < 1 && str_alert(-1, '请先添加图片！');
            foreach ($data['files'] as $arr => $row) {
                if ($row['status'] == 1) {
                    $v[$arr]['id'] = $row['pid'];
                    $v[$arr]['invId'] = $id;
                } else {
                    $s[$arr]['id'] = $row['pid'];
                    $s[$arr]['invId'] = $id;
                    $s[$arr]['isDelete'] = 1;
                }
            }
            $this->mysql_model->update(GOODS_IMG, array_values($v), 'id');
            $this->mysql_model->update(GOODS_IMG, array_values($s), 'id');
            str_alert(200, 'success');
        }
        str_alert(-1, '保存失败');
    }

    //获取图片信息
    public function getImage()
    {
        $id = intval($this->input->get_post('pid', TRUE));
        $data = $this->mysql_model->get_row(GOODS_IMG, '(id=' . $id . ')');
        if (count($data) > 0) {
            $url = './data/upfile/goods/' . $data['name'];
            $info = getimagesize($url);
            $imgdata = fread(fopen($url, 'rb'), filesize($url));
            header('content-type:' . $info['mime'] . '');
            echo $imgdata;
        }
    }

    //新增
    public function add()
    {
        $this->common_model->checkpurview(69);
        $data = $this->input->post(NULL, TRUE);
        if ($data) {
            $v = '';
            $data = $this->validform($data);
            $this->mysql_model->get_count(GOODS, '(isDelete=0) and (number="' . $data['number'] . '")') > 0 && str_alert(-1, '商品编号重复');
            $this->db->trans_begin();
            $info = array(
                'barCode', 'baseUnitId', 'unitName', 'categoryId', 'categoryName',
                'discountRate1', 'discountRate2', 'highQty', 'locationId', 'pinYin',
                'locationName', 'lowQty', 'name', 'number', 'purPrice',
                'remark', 'salePrice', 'spec', 'vipPrice', 'wholesalePrice'
            );
            $info = elements($info, $data, NULL);
            $info['uid'] = $this->jxcsys['uid'];
            $data['id'] = $invId = $this->mysql_model->insert(GOODS, $info);
            if (strlen($data['propertys']) > 0) {
                $list = (array)json_decode($data['propertys'], true);
                foreach ($list as $arr => $row) {
                    $v[$arr]['invId'] = $invId;
                    $v[$arr]['locationId'] = isset($row['locationId']) ? $row['locationId'] : 0;
                    $v[$arr]['qty'] = isset($row['quantity']) ? $row['quantity'] : 0;
                    $v[$arr]['price'] = isset($row['unitCost']) ? $row['unitCost'] : 0;
                    $v[$arr]['amount'] = isset($row['amount']) ? $row['amount'] : 0;
                    $v[$arr]['skuId'] = isset($row['skuId']) ? $row['skuId'] : 0;
                    $v[$arr]['billDate'] = date('Y-m-d');
                    $v[$arr]['billNo'] = '期初数量';
                    $v[$arr]['billType'] = 'INI';
                    $v[$arr]['transTypeName'] = '期初数量';
                }
                if (is_array($v)) {
                    $this->mysql_model->insert(INVOICE_INFO, $v);
                }
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, 'SQL错误回滚');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('新增商品:' . $data['name']);
                str_alert(200, 'success', $data);
            }
        }
        str_alert(-1, '添加失败');
    }

    //修改
    public function update()
    {
        $this->common_model->checkpurview(70);
        $data = $this->input->post(NULL, TRUE);
        if ($data) {
            $id = intval($data['id']);
            $data = $this->validform($data);
            //$this->mysql_model->get_count(GOODS,'(isDelete=0) and (id<>'.$id.') and (number="'.$data['number'].'")') > 0 && str_alert(-1,'商品编号重复');
            $this->db->trans_begin();
            /*$info = array(
                'barCode', 'baseUnitId', 'unitName', 'categoryId', 'categoryName',
                'discountRate1', 'discountRate2', 'highQty', 'locationId', 'pinYin',
                'locationName', 'lowQty', 'name', 'number', 'purPrice',
                'remark', 'salePrice', 'spec', 'vipPrice', 'wholesalePrice'
            );*/
            $info = array(
                'barCode', 'baseUnitId', 'unitName', 'categoryId', 'categoryName',
                'discountRate1', 'discountRate2', 'highQty', 'locationId', 'pinYin',
                'locationName', 'lowQty', 'name', 'number', 'purPrice',
                'remark', 'salePrice', 'spec', 'vipPrice', 'wholesalePrice','wholesalePrice2',
                'wholesalePrice3','colorType','unitCost','diaopaiPrice','taobaoPrice','taobaoLink',
                'jingdongPrice','jingdongLink'
            );
            $info = elements($info, $data, NULL);
            $this->mysql_model->update(GOODS, $info, '(id=' . $id . ')');
            if (strlen($data['propertys']) > 0) {
                $v = '';
                $list = (array)json_decode($data['propertys'], true);
                foreach ($list as $arr => $row) {
                    $v[$arr]['invId'] = $id;
                    $v[$arr]['locationId'] = isset($row['locationId']) ? $row['locationId'] : 0;
                    $v[$arr]['qty'] = isset($row['quantity']) ? $row['quantity'] : 0;
                    $v[$arr]['price'] = isset($row['unitCost']) ? $row['unitCost'] : 0;
                    $v[$arr]['amount'] = isset($row['amount']) ? $row['amount'] : 0;
                    $v[$arr]['skuId'] = isset($row['skuId']) ? $row['skuId'] : 0;
                    $v[$arr]['billDate'] = date('Y-m-d');
                    $v[$arr]['billNo'] = '期初数量';
                    $v[$arr]['billType'] = 'INI';
                    $v[$arr]['transTypeName'] = '期初数量';
                }
                $this->mysql_model->delete(INVOICE_INFO, '(invId=' . $id . ') and billType="INI"');
                if (is_array($v)) {
                    $this->mysql_model->insert(INVOICE_INFO, $v);
                }
            }
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                str_alert(-1, 'SQL错误回滚');
            } else {
                $this->db->trans_commit();
                $this->common_model->logs('修改商品:ID=' . $id . '名称:' . $data['name']);
                str_alert(200, 'success', $this->get_goods_info($id));
            }
        }
        str_alert(-1, '修改失败');
    }

    //删除
    public function delete()
    {
        $this->common_model->checkpurview(71);
        $id = str_enhtml($this->input->post('id', TRUE));
        $data = $this->mysql_model->get_results(GOODS, '(id in(' . $id . ')) and (isDelete=0)');
      //新增
        $uniacid=$this->jxcsys['uniacid'];
        $datasapi = $this->mysql_model->get_results(GOODS, '(id in(' . $id . ')) and (isDelete=0) and (unified=1)','id');
        foreach ($datasapi as $k => $v) {
            $cougd[]=$v['id'];
        }
        $apiurl =$this->config->item('apiurl');
        $url='http://'.$apiurl.'/web/index.php?c=api&a=productdele';
        $datas['list']=$cougd;
        $datas['uniacid']=$uniacid;
        if($uniacid&&$datasapi){
            $resu=https_request($url,json_encode($datas));
            $bod=json_decode($resu['body'],true);
        }
        //

        if (count($data) > 0) {
            $info['isDelete'] = 1;
//            $this->mysql_model->get_count(INVOICE_INFO, '(isDelete=0) and (invId in(' . $id . '))') > 0 && str_alert(-1, '其中有商品发生业务不可删除');
            $sql = $this->mysql_model->update(GOODS, $info, '(id in(' . $id . '))');
            if ($sql) {
                $name = array_column($data, 'name');
                $this->common_model->logs('删除商品:ID=' . $id . ' 名称:' . join(',', $name));
                str_alert(200, 'success', array('msg' => '', 'id' => '[' . $id . ']'));
            }
            str_alert(-1, '删除失败');
        }
    }
    //同步电商
    public function unified()
    {

        $this->common_model->checkpurview(71);
        $id = str_enhtml($this->input->post('id', TRUE));
        $uniacid=$this->jxcsys['uniacid'];
        $where = ' and (uid=' . $this->jxcsys['uid'] . ')';
        $list = $this->data_model->get_goods($where.' and (a.id in(' . $id . '))');
        $apiurl =$this->config->item('apiurl');
        $url='http://'.$apiurl.'/web/index.php?c=api&a=productadd';
        $datas['list']=$list;
        $datas['uniacid']=$uniacid;
        if($uniacid){
            $resu=https_request($url,json_encode($datas));
            $bod=json_decode($resu['body'],true);
            $info['unified'] = 1;
            $info['unifiedtime'] = time();
            $sql = $this->mysql_model->update(GOODS, $info, '(id in(' . $id . '))');
            str_alert(200, 'success', array('msg' => ''));
        }else{
            str_alert(201, '没有匹配电商数据', array('msg' => ''));
        }




    }

    //导入
    public function import()
    {
        $this->load->library('lib_cn2pinyin');
        require_once './application/libraries/phpexcel/PHPExcel.php';
        $filepath = $_POST['sy_logo'];
        header("Content-type:text/html;charset=utf-8");
        $objPHPExcel = new PHPExcel();
        $resut = $this->importExecl($filepath);
        unset($resut[1]);
//		$v=$resut[2];
//		str_alert(-1,'添加失败');
        foreach ($resut as $k => $v) {
            $data['number'] = $v['A'];
            $data['barCode'] = $v['B'];
            $data['name'] = $v['C'];
//            $data['img'] = $v['D'];//商品图片

            $data['categoryId'] = $v['E'];//商品类别
            if(!empty($v['F'])){
                $data['spec'] = $v['F'];//原来规格型号
            }

            if(!empty($v['G'])){
                $data['colorType'] = $v['G'];//颜色分类
            }

            if(!empty($v['H'])){
                $data['purPrice'] = $v['H'] ;//采购价格
            }

            if(!empty($v['I'])){
                $data['unitCost'] = $v['I'];//采购成本
            }

            if(!empty($v['J'])){
                $data['diaopaiPrice'] = $v['J'];//吊牌价
            }
            if(!empty($v['L'])){
                $data['wholesalePrice'] = $v['L'];//批发价1
            }
            if(!empty($v['M'])){
                $data['wholesalePrice2'] = $v['M'];//批发价2
            }
            if(!empty($v['N'])){
                $data['wholesalePrice3'] = $v['N'];//批发价3
            }
            if(!empty($v['O'])){
                $data['discountRate1'] = $v['O'];//折扣一
            }
            if(!empty($v['P'])){
                $data['discountRate2'] = $v['P'];//折扣二
            }
            if(!empty($v['Q'])){
                $data['taobaoPrice'] = $v['Q'];//淘宝天猫价
            }
            if(!empty($v['R'])){
                $data['taobaoLink'] = $v['R'];//淘宝天猫链接
            }
            if(!empty($v['S'])){
                $data['jingdongPrice'] = $v['S'];//京东价格
            }
            if(!empty($v['T'])){
                $data['jingdongLink'] = $v['T'];//京东链接
            }

            $data['salePrice'] = round($v['U'],2);//零售价
            $data['highQty'] = $v['V'];//期初库存
            if (!empty($data['number']) && !empty($data['barCode']) && !empty($data['name'])) {
                $uid = $this->jxcsys['uid'];
                $name = $data['categoryId'];
                //类别
                $cate = $this->mysql_model->get_row(CATEGORY, '(uid=' . $uid . ') and (name="' . $name . '")');
                if ($cate) {
                    $data['categoryId'] = $cate['id'];
                    $data['categoryName'] = $cate['name'];
                } else {
                    $datas['uid'] = $uid;
                    $datas['typeNumber'] = 'trade';
                    $datas['parentId'] = 0;
                    $datas['name'] = $name;
                    $datas['level'] = 1;
                    $newid = $this->mysql_model->insert(CATEGORY, $datas);
                    $sql = $this->mysql_model->update(CATEGORY, array('path' => $newid), '(id=' . $newid . ')');
                    $data['categoryId'] = $newid;
                    $data['categoryName'] = $name;
                }
//计量单位
                $unitname = '件';
                $unit = $this->mysql_model->get_row(UNIT, '(isDelete=0) and (name="' . $unitname . '") and (uid="' . $this->jxcsys['uid'] . '")');
                if ($unit) {
                    $data['baseUnitId'] = $unit['id'];
                    $data['unitName'] = $unitname;
                } else {
                    $datau['uid'] = $uid;
                    $datau['name'] = $unitname;
                    $datau['default'] = 0;
                    $sqlunit = $this->mysql_model->insert(UNIT, elements(array('name', 'default', 'uid'), $datau));
                    $data['baseUnitId'] = $sqlunit;
                    $data['unitName'] = $unitname;
                }
//仓库
                $storage = $this->mysql_model->get_results(STORAGE, '(disable=0) and (uid="' . $this->jxcsys['uid'] . '")');
                if ($storage) {
                    $data['locationId'] = $storage[0]['id'];
                    $data['locationName'] = $storage[0]['name'];
                } else {
                    $datalo['uid'] = $this->jxcsys['uid'];
                    $datalo['name'] = '默认仓';
                    $datalo['locationNo'] = 'bw001';
                    $sqllo = $this->mysql_model->insert(STORAGE, elements(array('name', 'locationNo', 'uid'), $datalo));
                    $data['locationId'] = $sqllo;
                    $data['locationName'] = '默认仓';

                }
                //$data['pinYin'] = $this->lib_cn2pinyin->encode($data['name']);
				$data['pinYin'] = '';
                /*$info = array(
                    'barCode', 'baseUnitId', 'unitName', 'categoryId', 'categoryName',
                    'discountRate1', 'discountRate2', 'highQty', 'locationId', 'pinYin',
                    'locationName', 'lowQty', 'name', 'number', 'purPrice',
                    'remark', 'salePrice', 'spec', 'vipPrice', 'wholesalePrice','colorType','unitCost',
                    'wholesalePrice2','wholesalePrice3','diaopaiPrice','taobaoPrice','taobaoLink',
                    'jingdongPrice','jingdongLink'
                );*/
                $info = array(
                    'barCode', 'baseUnitId', 'unitName', 'categoryId', 'categoryName',
                    'discountRate1', 'discountRate2', 'highQty', 'locationId', 'pinYin',
                    'locationName', 'name', 'number', 'purPrice',
                    'salePrice', 'spec', 'wholesalePrice','colorType','unitCost',
                    'wholesalePrice2','wholesalePrice3','diaopaiPrice','taobaoPrice','taobaoLink',
                    'jingdongPrice','jingdongLink'
                );
                $info = elements($info, $data, NULL);
                $info['uid'] = $this->jxcsys['uid'];

                //分仓仓库
                $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
                $countRow = count($v) - 10;
                $locationArr = [];
                if($countRow > 0){
                    for($i = 0; $i < $countRow; $i+=2){

                        $locationName = $v[$cellName[10+$i]];
                        $location = $this->mysql_model->get_results(STORAGE, '(disable=0) and (uid="' . $this->jxcsys['uid'] . '") and (name="' . $locationName . '")');
                        if(!empty($location[0]['id']) && !empty($v[$cellName[11+$i]])){
                            $locationArr[] = [
                                'locationId' => $location[0]['id'],
                                'quantity' => $v[$cellName[11+$i]],
                            ];
                        }
                    }
                }

//商品
                $goodsl = $this->mysql_model->get_row(GOODS, '(uid=' . $this->jxcsys['uid'] . ') and (isDelete=0) and (barCode="' . $data['barCode'] . '")');
                if($goodsl){
                    if(!empty($v['D'])){
                        //处理图片
                        $imgId = $this->mysql_model->get_results(GOODS_IMG, '(invId=' . $goodsl['id'].')');
                        foreach ($imgId as $i){
                            $imgUp['isDelete'] = 1;
                            $this->mysql_model->update(GOODS_IMG, $imgUp, '(id=' . $i['id'].')');
                            //删除图片
                            $url = './data/upfile/goods/'.$i['name'];
                            unlink($url);
                        }

                        $img['invId'] = $goodsl['id'];
                        $img['name'] = $v['D'];
                        $this->mysql_model->insert(GOODS_IMG, $img);
                    }

                    $gid=$goodsl['id'];
                    $adtotal=$data['highQty'];
                    $adprice=$data['salePrice'];
                    $infoupda['salePrice'] = $adprice;

                    if(!empty($v['L'])){
                        $infoupda['wholesalePrice'] = $data['wholesalePrice'];
                    }
                    if(!empty($v['M'])){
                        $infoupda['wholesalePrice2'] = $data['wholesalePrice2'];
                    }
                    if(!empty($v['N'])){
                        $infoupda['wholesalePrice3'] = $data['wholesalePrice3'];
                    }

                    $sql = $this->mysql_model->update(GOODS, $infoupda, '(uid=' . $this->jxcsys['uid'] . ') and (id=' . $gid . ')');
                    if(!empty($adtotal)){
                        $this->addpu($gid,$adtotal,$adprice,$locationArr);
                    }
                }else{
                    $data['id'] = $invId = $this->mysql_model->insert(GOODS, $info);

                    if(!empty($v['D'])){
                        //处理图片
                        $img['invId'] = $invId;
                        $img['name'] = $v['D'];
                        $this->mysql_model->insert(GOODS_IMG, $img);
                    }

                    //[{"locationId":8,"quantity":"4.0","unitCost":100,"amount":400,"batch":"","prodDate":"","safeDays":"","validDate":"","id":0}]
                /*$rowi['locationId'] = $data['locationId'];
                $rowi['quantity'] = $data['highQty'];
                $rowi['unitCost'] = 0;
                $rowi['amount'] = 0;
                $rowi['skuId'] = 0;
                $vs['invId'] = $invId;
                $vs['locationId'] = isset($rowi['locationId']) ? $rowi['locationId'] : 0;
                $vs['qty'] = isset($rowi['quantity']) ? $rowi['quantity'] : 0;
                $vs['price'] = isset($rowi['unitCost']) ? $rowi['unitCost'] : 0;
                $vs['amount'] = isset($rowi['amount']) ? $rowi['amount'] : 0;
                $vs['skuId'] = isset($row['skuId']) ? $rowi['skuId'] : 0;
                $vs['billDate'] = date('Y-m-d');
                $vs['billNo'] = '期初数量';
                $vs['billType'] = 'INI';
                $vs['transTypeName'] = '期初数量';
                if (is_array($vs)) {
                    $this->mysql_model->insert(INVOICE_INFO, $vs);
                }*/

                    if(!empty($data['id'] )){
                        if(!empty($locationArr)){
                            foreach ($locationArr as $it){
                                $rowi['locationId'] = $it['locationId'];
                                $rowi['quantity'] = $it['quantity'];
                                $rowi['unitCost'] = $data['salePrice'];
                                $rowi['amount'] = $data['salePrice'] * $rowi['quantity'];
                                $rowi['skuId'] = 0;

                                $vs['invId'] = $invId;
                                $vs['locationId'] = isset($rowi['locationId']) ? $rowi['locationId'] : 0;
                                $vs['qty'] = isset($rowi['quantity']) ? $rowi['quantity'] : 0;
                                $vs['price'] = isset($rowi['unitCost']) ? $rowi['unitCost'] : 0;
                                $vs['amount'] = isset($rowi['amount']) ? $rowi['amount'] : 0;
                                $vs['skuId'] = isset($row['skuId']) ? $rowi['skuId'] : 0;
                                $vs['billDate'] = date('Y-m-d');
                                $vs['billNo'] = '期初数量';
                                $vs['billType'] = 'INI';
                                $vs['transTypeName'] = '期初数量';
//                    $vs['buId'] = 14;
                                if(!empty($vs['locationId']) &&!empty($vs['qty'])){
                                    //save
                                    if (is_array($vs)) {
                                        $this->mysql_model->insert(INVOICE_INFO, $vs);
                                    }
                                }
                            }
                        }else{
                            $rowi['locationId'] = $data['locationId'];
                            $rowi['quantity'] = $data['highQty'];
                            $rowi['unitCost'] = 0;
                            $rowi['amount'] = 0;
                            $rowi['skuId'] = 0;
                            $vs['invId'] = $invId;
                            $vs['locationId'] = isset($rowi['locationId']) ? $rowi['locationId'] : 0;
                            $vs['qty'] = isset($rowi['quantity']) ? $rowi['quantity'] : 0;
                            $vs['price'] = isset($rowi['unitCost']) ? $rowi['unitCost'] : 0;
                            $vs['amount'] = isset($rowi['amount']) ? $rowi['amount'] : 0;
                            $vs['skuId'] = isset($row['skuId']) ? $rowi['skuId'] : 0;
                            $vs['billDate'] = date('Y-m-d');
                            $vs['billNo'] = '期初数量';
                            $vs['billType'] = 'INI';
                            $vs['transTypeName'] = '期初数量';
                            if (is_array($vs)) {
                                $this->mysql_model->insert(INVOICE_INFO, $vs);

                            }
                        }
                    }
                }

            }

        }
        @unlink($filepath);
        str_alert(200, 'success');


    }
// 购货新增
    //新增
    public function addpu($gid,$total,$price,$locationArr){

//            $data = $this->validform($data);

        $results['goodsdata']= Array(Array( 'gid' => $gid, 'total' => $total ,'price'=> $price ));
        $totalQtyco=0;
        $couamount=0;
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

//        print_r($data);exit();
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

        if(!empty($locationArr)){
            foreach ($locationArr as $it){
                foreach ($data['entries'] as &$row) {
                    $row['locationId']    = $it['locationId'];
                    $row['qty']           = $it['quantity'];
                    $row['amount']        = $row['price'] * $it['quantity'];
                }
                $this->invoice_infopu($iid,$data);
            }
        }

        $this->account_infopu($iid,$data);
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            str_alert(-1,'SQL错误');
        } else {
            $this->db->trans_commit();
            $this->common_model->logs('新增购货 单据编号：'.$info['billNo']);
//            str_alert(200,'success',array('id'=>intval($iid)));
        }

    }
    // 购货新增
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
// 购货新增
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

    /*function importExecl($file = '', $sheet = 0)
    {
        $file = iconv("utf-8", "gb2312", $file);   //转码
        if (empty($file) OR !file_exists($file)) {
            die('file not exists!');
        }
        $objRead = new PHPExcel_Reader_Excel2007();   //建立reader对象
        if (!$objRead->canRead($file)) {
            $objRead = new PHPExcel_Reader_Excel5();
            if (!$objRead->canRead($file)) {
                die('No Excel!');
            }
        }
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        $obj = $objRead->load($file);  //建立excel对象
        $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
        $columnH = $currSheet->getHighestColumn();   //取得最大的列号
        $columnCnt = array_search($columnH, $cellName);
        $rowCnt = $currSheet->getHighestRow();   //获取总行数
        $data = array();
        for ($_row = 1; $_row <= $rowCnt; $_row++) {  //读取内容
            for ($_column = 0; $_column <= $columnCnt; $_column++) {
                $cellId = $cellName[$_column] . $_row;
//                $cellValue = $currSheet->getCell($cellId)->getValue();
                $cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
                if ($cellValue instanceof PHPExcel_RichText) {   //富文本转换字符串
                    $cellValue = $cellValue->__toString();
                }
                $data[$_row][$cellName[$_column]] = $cellValue;
            }
        }
        return $data;
    }*/
    function importExecl($file = '', $sheet = 0)
    {
        $file = iconv("utf-8", "gb2312", $file);   //转码
        if (empty($file) OR !file_exists($file)) {
            die('file not exists!');
        }
        $objRead = new PHPExcel_Reader_Excel2007();   //建立reader对象
        if (!$objRead->canRead($file)) {
            $objRead = new PHPExcel_Reader_Excel5();
            if (!$objRead->canRead($file)) {
                die('No Excel!');
            }
        }
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        $obj = $objRead->load($file);  //建立excel对象
        $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
        $columnH = $currSheet->getHighestColumn();   //取得最大的列号
        $columnCnt = array_search($columnH, $cellName);
        $rowCnt = $currSheet->getHighestRow();   //获取总行数
        $data = array();
        for ($_row = 1; $_row <= $rowCnt; $_row++) {  //读取内容
            for ($_column = 0; $_column <= $columnCnt; $_column++) {
                $cellId = $cellName[$_column] . $_row;
//                $cellValue = $currSheet->getCell($cellId)->getValue();
                $cellValue = $currSheet->getCell($cellId)->getCalculatedValue();  #获取公式计算的值
                if ($cellValue instanceof PHPExcel_RichText) {   //富文本转换字符串
                    $cellValue = $cellValue->__toString();
                }
                $data[$_row][$cellName[$_column]] = $cellValue;
            }
        }

        //处理图片
        $imageFileName = "";
//        $imageFilePath='./data/upfile/goods/'.date('Y-m-d').'/';//图片在本地存储的路径
        $imageFilePath='./data/upfile/goods/';//图片在本地存储的路径
        if(!is_dir($imageFilePath)){
            mkdir(iconv("UTF-8", "GBK", $imageFilePath),0777,true);
        }

        foreach ($currSheet->getDrawingCollection() as $drawing) {
            $xy=$drawing->getCoordinates();
            list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($drawing->getCoordinates());

            $path = $imageFilePath;

            // for xlsx
            if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
                $filename = $drawing->getPath();

                $imageFileName = $drawing->getIndexedFilename();
//                $path = $path . $drawing->getIndexedFilename();

                $str = strstr($imageFileName,'.');
                $imgName = date('Ymd').rand(10000, 99999).$str;
                $path = $path.$imgName;
                copy($filename, $path);
//                $data[$xy] = $path;
//                $data[$startRow][$startColumn] = $path;
                $data[$startRow][$startColumn] = $imgName;

                // for xls
            } else if ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                $image = $drawing->getImageResource();

                $renderingFunction = $drawing->getRenderingFunction();

                switch ($renderingFunction) {

                    case PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG:

//                        $imageFileName = $drawing->getIndexedFilename();
//                        $path = $path . $drawing->getIndexedFilename();
                        $imageFileName = date('Ymd').rand(10000, 99999).'.jpeg';
                        $path = $path . $imageFileName;
                        imagejpeg($image, $path);
                        break;

                    case PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF:
//                        $imageFileName = $drawing->getIndexedFilename();
//                        $path = $path . $drawing->getIndexedFilename();
                        $imageFileName = date('Ymd').rand(10000, 99999).'.gif';
                        $path = $path . $imageFileName;
                        imagegif($image, $path);
                        break;

                    case PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG:
//                        $imageFileName = $drawing->getIndexedFilename();
//                        $path = $path . $drawing->getIndexedFilename();
                        $imageFileName = date('Ymd').rand(10000, 99999).'.png';
                        $path = $path . $imageFileName;
                        imagegif($image, $path);
                        break;

                    case PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT:
                        $imageFileName = $drawing->getIndexedFilename();
                        $path = $path . $drawing->getIndexedFilename();
                        imagegif($image, $path);
                        break;
                }
//                $data[$xy] = $imageFileName;
                $data[$startRow][$startColumn] = $imageFileName;
            }
        }
        return $data;
    }

    /*//导出
    public function exporter()
    {
        $this->common_model->checkpurview(72);
        $name = 'goods_' . date('YmdHis') . '.xls';
        sys_csv($name);
        $this->common_model->logs('导出商品:' . $name);
        $skey = str_enhtml($this->input->get_post('skey', TRUE));
        $categoryid = intval($this->input->get_post('assistId', TRUE));
        $barCode = intval($this->input->get_post('barCode', TRUE));
        $where = '';
        $where .= $skey ? ' and (name like "%' . $skey . '%" or number like "%' . $skey . '%" or spec like "%' . $skey . '%")' : '';
        $where .= $barCode ? ' and barCode="' . $barCode . '"' : '';
        $where .= ' and (uid=' . $this->jxcsys['uid'] . ')';
        if ($categoryid > 0) {
            $cid = array_column($this->mysql_model->get_results(CATEGORY, '(1=1) and find_in_set(' . $categoryid . ',path)'), 'id');
            if (count($cid) > 0) {
                $cid = join(',', $cid);
                $where .= ' and categoryid in(' . $cid . ')';
            }
        }
        $data['ini'] = $this->data_model->get_invoice_info('and billType="INI"');
//        $data['list'] = $this->data_model->get_goods($where . ' order by id desc limit 10');
        $data['list'] = $this->data_model->get_goods_img($where . ' order by id desc');
        $this->load->view('settings/goods-export', $data);

    }*/
    //导出带图片
    public function exporter()
    {
        $this->common_model->checkpurview(72);
        $name = 'goods_' . date('YmdHis') . '.xls';
//        sys_csv($name);
        $this->common_model->logs('导出商品:' . $name);
        $skey = str_enhtml($this->input->get_post('skey', TRUE));
        $categoryid = intval($this->input->get_post('assistId', TRUE));
        $barCode = intval($this->input->get_post('barCode', TRUE));
        $where = '';
        $where .= $skey ? ' and (name like "%' . $skey . '%" or number like "%' . $skey . '%" or spec like "%' . $skey . '%")' : '';
        $where .= $barCode ? ' and barCode="' . $barCode . '"' : '';
        $where .= ' and (uid=' . $this->jxcsys['uid'] . ')';
        if ($categoryid > 0) {
            $cid = array_column($this->mysql_model->get_results(CATEGORY, '(1=1) and find_in_set(' . $categoryid . ',path)'), 'id');
            if (count($cid) > 0) {
                $cid = join(',', $cid);
                $where .= ' and categoryid in(' . $cid . ')';
            }
        }
        $data['ini'] = $this->data_model->get_invoice_info('and billType="INI"');
        $data['list'] = $this->data_model->get_goods_img($where . ' order by id desc');


        $this->load->model('goods_ex_model');
        $this->goods_ex_model->goodsStyle($data['list'],$name);
    }

    //状态
    public function disable()
    {
        $this->common_model->checkpurview(72);
        $disable = intval($this->input->post('disable', TRUE));
        $id = str_enhtml($this->input->post('invIds', TRUE));
        if (strlen($id) > 0) {
            $info['disable'] = $disable;
            $sql = $this->mysql_model->update(GOODS, $info, '(id in(' . $id . '))');
            if ($sql) {
                $this->common_model->logs('商品' . $disable == 1 ? '禁用' : '启用' . ':ID:' . $id . '');
                str_alert(200, 'success');
            }
        }
        str_alert(-1, '操作失败');
    }

    //库存预警
    public function listinventoryqtywarning()
    {
        $v = array();
        $data['status'] = 200;
        $data['msg'] = 'success';
        $page = max(intval($this->input->get_post('page', TRUE)), 1);
        $rows = max(intval($this->input->get_post('rows', TRUE)), 100);
        $where = '';
        $data['data']['total'] = 1;
        $data['data']['records'] = $this->data_model->get_inventory($where . ' GROUP BY invId HAVING qty>highQty or qty<lowQty', 3);
        $list = $this->data_model->get_inventory($where . ' GROUP BY invId HAVING qty>highQty or qty<lowQty');
        foreach ($list as $arr => $row) {
            $qty1 = (float)$row['qty'] - (float)$row['highQty'];
            $qty2 = (float)$row['qty'] - (float)$row['lowQty'];
            $v[$arr]['highQty'] = (float)$row['highQty'];
            $v[$arr]['id'] = intval($row['invId']);
            $v[$arr]['lowQty'] = (float)$row['lowQty'];
            $v[$arr]['name'] = $row['invName'];
            $v[$arr]['number'] = $row['invNumber'];
            $v[$arr]['warning'] = $qty1 > 0 ? $qty1 : $qty2;
            $v[$arr]['qty'] = (float)$row['qty'];
            $v[$arr]['unitName'] = $row['unitName'];
            $v[$arr]['spec'] = $row['invSpec'];
        }
        $data['data']['rows'] = $v;
        die(json_encode($data));
    }

    //通过ID 获取商品信息
    private function get_goods_info($id)
    {
        $data = $this->mysql_model->get_row(GOODS, '(id=' . $id . ') and (isDelete=0)');
        if (count($data) > 0) {
            $v = array();
            $data['id'] = intval($id);
            $data['count'] = 0;
            $data['unitTypeId'] = intval($data['unitTypeId']);
            $data['baseUnitId'] = intval($data['baseUnitId']);
            $data['categoryId'] = intval($data['categoryId']);
            $data['salePrice'] = (float)$data['salePrice'];
            $data['vipPrice'] = (float)$data['vipPrice'];
            $data['purPrice'] = (float)$data['purPrice'];
            $data['wholesalePrice'] = (float)$data['wholesalePrice'];
            $data['discountRate1'] = (float)$data['discountRate1'];
            $data['discountRate2'] = (float)$data['discountRate2'];
            $data['remark'] = $data['remark'];
            $data['locationId'] = intval($data['locationId']);
            $data['baseUnitId'] = intval($data['baseUnitId']);
            $data['unitTypeId'] = intval($data['unitTypeId']);
            $data['unitId'] = intval($data['unitId']);
            $data['highQty'] = (float)$data['highQty'];
            $data['lowQty'] = (float)$data['lowQty'];
            $data['property'] = $data['property'] ? $data['property'] : NULL;
            $data['quantity'] = (float)$data['quantity'];
            $data['isWarranty'] = (float)$data['isWarranty'];
            $data['advanceDay'] = (float)$data['advanceDay'];
            $data['unitCost'] = (float)$data['unitCost'];
            $data['isSerNum'] = (float)$data['isSerNum'];
            $data['amount'] = (float)$data['amount'];
            $data['quantity'] = (float)$data['quantity'];
            $data['unitCost'] = (float)$data['unitCost'];
            $data['delete'] = intval($data['disable']) == 1 ? true : false;   //是否禁用
            $propertys = $this->data_model->get_invoice_info('and (a.invId=' . $id . ') and a.billType="INI"');
            foreach ($propertys as $arr => $row) {
                $v[$arr]['id'] = intval($row['id']);
                $v[$arr]['locationId'] = intval($row['locationId']);
                $v[$arr]['inventoryId'] = intval($row['invId']);
                $v[$arr]['locationName'] = $row['locationName'];
                $v[$arr]['quantity'] = (float)$row['qty'];
                $v[$arr]['unitCost'] = (float)$row['price'];
                $v[$arr]['amount'] = (float)$row['amount'];
                $v[$arr]['skuId'] = intval($row['skuId']);
                $v[$arr]['skuName'] = '';
                $v[$arr]['date'] = $row['billDate'];
                $v[$arr]['tempId'] = 0;
                $v[$arr]['batch'] = '';
                $v[$arr]['invSerNumList'] = '';
            }
            $data['propertys'] = $v;
        }
        return $data;
    }


    //公共验证
    private function validform($data)
    {
        $this->load->library('lib_cn2pinyin');
        strlen($data['name']) < 1 && str_alert(-1, '商品名称不能为空');
        strlen($data['number']) < 1 && str_alert(-1, '商品编号不能为空');
      //  intval($data['categoryId']) < 1 && str_alert(-1, '商品类别不能为空');
        intval($data['baseUnitId']) < 1 && str_alert(-1, '计量单位不能为空');
        $data['lowQty'] = (float)$data['lowQty'];
        $data['purPrice'] = (float)$data['purPrice'];
        $data['salePrice'] = (float)$data['salePrice'];
        $data['vipPrice'] = (float)$data['vipPrice'];
        $data['discountRate1'] = (float)$data['discountRate1'];
        $data['discountRate2'] = (float)$data['discountRate2'];
        $data['wholesalePrice'] = (float)$data['wholesalePrice'];
        $data['barCode'] = $data['barCode'] ? $data['barCode'] : NULL;
        $data['remark'] = $data['remark'] ? $data['remark'] : NULL;
        $data['spec'] = $data['spec'] ? $data['spec'] : NULL;

        $data['unitName'] = $this->mysql_model->get_row(UNIT, '(id=' . $data['baseUnitId'] . ')', 'name');

        $data['categoryName'] = $this->mysql_model->get_row(CATEGORY, '(id=' . $data['categoryId'] . ')', 'name');
        $data['pinYin'] = $this->lib_cn2pinyin->encode($data['name']);
    //    !$data['categoryName'] && str_alert(-1, '商品类别不存在');
        if (strlen($data['propertys']) > 0) {
            $list = (array)json_decode($data['propertys'], true);
            $storage = $this->mysql_model->get_results(STORAGE, '(disable=0)');
            $locationId = array_column($storage, 'id');
            $locationName = array_column($storage, 'name', 'id');
            foreach ($list as $arr => $row) {
                !in_array($row['locationId'], $locationId) && str_alert(-1, @$locationName[$row['locationId']] . '仓库不存在或不可用！');
            }
        }
        return $data;
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */