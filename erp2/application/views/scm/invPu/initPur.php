<?php $this->load->view('header');?>

<link href="<?php echo base_url()?>statics/js/layui/css/layui.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url()?>statics/js/layui/layui.js?ver=20140430"></script>
<script src="<?php echo base_url()?>statics/js/layui/phpyun_layer.js?ver=20140430"></script>
<script type="text/javascript">
var DOMAIN = document.domain;
var WDURL = "";
var SCHEME= "<?php echo sys_skin()?>";
try{
	document.domain = '<?php echo base_url()?>';
}catch(e){
}
//ctrl+F5 增加版本号来清空iframe的缓存的
$(document).keydown(function(event) {
	/* Act on the event */
	if(event.keyCode === 116 && event.ctrlKey){
		var defaultPage = Public.getDefaultPage();
		var href = defaultPage.location.href.split('?')[0] + '?';
		var params = Public.urlParam();
		params['version'] = Date.parse((new Date()));
		for(i in params){
			if(i && typeof i != 'function'){
				href += i + '=' + params[i] + '&';
			}
		}
		defaultPage.location.href = href;
		event.preventDefault();
	}
});
</script>

<link href="<?php echo base_url()?>statics/css/<?php echo sys_skin()?>/bills.css?ver=20150522" rel="stylesheet" type="text/css">
<style>
#barCodeInsert{margin-left: 10px;font-weight: 100;font-size: 12px;color: #fff;background-color: #B1B1B1;padding: 0 5px;border-radius: 2px;line-height: 19px;height: 20px;display: inline-block;}
#barCodeInsert.active{background-color: #23B317;}
</style>
<style>
  .admin_resume_dc_sub {
    width: 650px;
    text-align: center;
    border-top: 1px solid #ddd;
    float: left;
    padding-top: 10px;
  }
  .admin_button {
    padding: 0px 16px;
    height: 30px;
    border: none;
    background: #31b4e1;
    color: #fff;
    border-radius: 2px;
    cursor: pointer;
    margin-right: 5px;
  }
  .admin_buttonpz {
    width: 90px;
    height: 38px;
  }
  .admin_resume_dc_bth2 {
    width: 90px;
    height: 30px;
    background: #eee;
    color: #333;
    border: none;
    cursor: pointer;
    margin-left: 10px;
    font-size: 16px;
  }
</style>
</head>
<body>
<div class="wrapper">
  <span id="config" class="ui-icon ui-state-default ui-icon-config"></span>
  <div class="mod-toolbar-top mr0 cf dn" id="toolTop"></div>
  <div class="bills cf">
    <div class="con-header">
      <dl class="cf">
        <dd class="pct30">
          <label>供应商:</label>
          <span class="ui-combo-wrap" id="customer">
          <input type="text" name="" class="input-txt" autocomplete="off" value="" data-ref="date">
          <i class="ui-icon-ellipsis"></i></span></dd>
        <dd class="pct25 tc">
          <label>单据日期:</label>
          <input type="text" id="date" class="ui-input ui-datepicker-input" value="2015-06-08">
        </dd>
        <dd id="identifier" class="pct25 tc">
          <label>单据编号:</label>
          <span id="number"><?php echo str_no('CG')?></span></dd>
      </dl>
    </div>
    <div class="grid-wrap">
      <table id="grid">
      </table>
      <div id="page"></div>
    </div>
    <div class="con-footer cf">
      <div class="mb10">
      	<textarea type="text" id="note" class="ui-input ui-input-ph">暂无备注信息</textarea>
      </div>
      <ul id="amountArea" class="cf">
        <li>
          <label>优惠率:</label>
          <input type="text" id="discountRate" class="ui-input" data-ref="deduction">%
        </li>
        <li>
          <label>优惠金额:</label>
          <input type="text" id="deduction" class="ui-input" data-ref="payment">
        </li>
        <li>
          <label>优惠后金额:</label>
          <input type="text" id="discount" class="ui-input ui-input-dis" data-ref="discountRate" disabled>
        </li>
        <li>
          <label id="paymentTxt">本次付款:</label>
          <input type="text" id="payment" class="ui-input">&emsp;
        </li>
        <li id="accountWrap" class="dn">
          <label>结算账户:</label>
          <span class="ui-combo-wrap" id="account" style="padding:0;">
          <input type="text" class="input-txt" autocomplete="off">
          <i class="trigger"></i></span><a id="accountInfo" class="ui-icon ui-icon-folder-open" style="display:none;"></a>
        </li>
        <li>
          <label>本次欠款:</label>
          <input type="text" id="arrears" class="ui-input ui-input-dis" disabled>
        </li>
        <li class="dn">
          <label>累计欠款:</label>
          <input type="text" id="totalArrears" class="ui-input ui-input-dis" disabled>
        </li>
      </ul>
      <ul class="c999 cf">
        <li>
          <label>制单人:</label>
          <span id="userName"></span>
        </li>
        <li>
          <label>审核人:</label>
          <span id="checkName"></span>
        </li>
        <li>
          <label>最后修改时间:</label>
          <span id="modifyTime"></span>
        </li>
      </ul>
    </div>
    <div class="cf" id="bottomField">
    	<div class="fr" id="toolBottom"></div>
    </div>
    <div id="mark"></div>
  </div>
  
  <div id="initCombo" class="dn">
    <input type="text" class="textbox goodsAuto" name="goods" autocomplete="off">
    <input type="text" class="textbox storageAuto" name="storage" autocomplete="off">
    <input type="text" class="textbox unitAuto" name="unit" autocomplete="off" value="1">
    <input type="text" class="textbox batchAuto" name="batch" autocomplete="off">
    <input type="text" class="textbox dateAuto" name="date" autocomplete="off">
  </div>
  <div id="storageBox" class="shadow target_box dn">
  </div>
</div>

<div id="export" style="display:none;">
  <div style=" margin-top:10px;">
    <div>
      <form action="../scm/invPu/importAdd" onsubmit="return mlogin(this);"   enctype= "multipart/form-data" method="post" id="formstatus" class="myform">
        <input type="hidden" name="pytoken" value="{yun:}$pytoken{/yun}">
        <input type="hidden" name="where" value="{yun:}$where{/yun}">
        <input type="hidden" name="ids">
        <a style="margin-left: 22px;color: red;font-size: 16px;" href="/data/upfile/ruku_erp_mod.xlsx"  class=" " >下载购货单表导入模板</a>

        <div class="admin_resume_dc">

          <button style="margin-left: 214px;" type="button" class="yun_bth_pic adminupload" lay-data="{name: 'sy_logo',imgid: 'imglogo'}">上传文件</button>
          <input type="hidden" id="layupload_type" value="2"/>
          <input type="hidden" id="upload_path" value="excel"/>
          <input type="hidden" name="sy_logo" value="{yun:}$config.sy_logo{/yun}"/>
          <label id="imglogo"  style="max-width:300px;_width:300px;color: red" class="none"></label>
        </div>


        <div class="admin_resume_dc_sub" style=" margin-top:135px;">
          <input class="admin_button admin_buttonpz admin_resume_dc_bth1"  type="submit" name="waterconfig" value="提交" />&nbsp;&nbsp;

          &nbsp;&nbsp;<input type="button" onClick="layer.closeAll();" class="admin_resume_dc_bth2" value='取消'></div>
      </form>
    </div>
  </div>
</div>
<!--<script src="--><?php //echo base_url()?><!--statics/js/dist/purchase.js?ver=20150522"></script>-->
<!--<script src="--><?php //echo base_url()?><!--statics/js/dist/purchase.js?ver=201505"></script>-->
<script src="<?php echo base_url()?>statics/js/dist/purchase.js"></script>

<script type="text/javascript">
  function add_clas1s(name,width,height,divid,url){
    if(url){$(divid).append("<input id='surl' value='"+url+"' type='hidden'/>");}
    $.layer({
      type : 1,
      title : name,
      offset: [($(window).height() - height)/2 + 'px', ''],
      closeBtn : [0 , true],
      border : [10 , 0.3 , '#000', true],
      area : [width+'px',height+'px'],
      page : {dom :divid}
    });
  }
  layui.use(['layer', 'form'], function () {
    var layer = layui.layer
        , form = layui.form
        , $ = layui.$;
  });
  function mlogin(obj) {
    $.post($(obj).attr('action'),  $(obj).serialize(), function (data) {
      layer.closeAll();
      $("#grid").delRowData(1);
      $("#grid").delRowData(2);
      $("#grid").delRowData(3);
      $("#grid").delRowData(4);
      $("#grid").delRowData(5);
      $("#grid").delRowData(6);
      $("#grid").delRowData(7);
      $("#grid").delRowData(8);

      var estJson = $.parseJSON(data);
      var qty = 0;
      var amount = 0;
      for (var i = 0; i < estJson.length; i++) {
        $("#grid").addRowData(i+9, estJson[i], "first");

        var num = i + 9;
        $("#" + num).data("goodsInfo", estJson[i].goodsInfo).data("storageInfo", {
          id: estJson[i].locationId,
          name: estJson[i].locationName
        });
        qty += estJson[i].qty;
        amount += estJson[i].amount;
      }

      $("#grid").jqGrid("footerData", "set", {
       qty: qty,
       amount: amount
       });
    });
    return false;
  }
</script>
<script type="text/javascript">
  /**
   * 文件上传
   *
   *
   */
  var layupload_type = $("#layupload_type").val();   //文件上传方式   2、选完文件后自动上传
  var laynoupload = $("#laynoupload").val(); 			//	1、选完不上传
  var path = $("#upload_path").val();                 //文件上传位置。如user、com、product
  var showid = 0;
  layui.use('upload', function(){
    var upload = layui.upload;
    //选完不上传，url暂未用到，只是需要其样式
    if (laynoupload == 1){
      var layfiletype = $("#layfiletype").val();
      //上传文件类型
      if (layfiletype == 2){
        var layaccept = 'file', layexts = 'doc|docx|rar|zip|pdf';
      }else{
        var layaccept = 'images', layexts = 'jpg|png|gif|bmp|jpeg';
      }
      var field = $("#upload_field").val();
      if (!field){
        var field = 'file';
      }
      upload.render({
        elem: '#noupload'
        ,url: '../basedata/inventory/uploadImages'
        ,auto: false
        ,bindAction: '#test9'   //触发上传的对象，暂未用到
        ,accept: layaccept
        ,exts: layexts
        ,field: field
        ,done: function(res){

        }
      });
    }
    if (layupload_type == 2){
      if($(".adminupload").length>0){
        var layaccept = 'file', layexts = 'doc|docx|rar|zip|pdf';
        upload.render({
          elem: '.adminupload'
          ,accept: layaccept,url: '../basedata/inventory/uploadFile'
          ,multiple: true,field:'files[]'
          ,done: function(res){
            if(res.code > 0){                //上传失败，返回失败原因
              return layer.msg(res.msg);
            }else{
              layer.closeAll('loading');
              //个人会员中心上传技能图片
              if ($('#'+this.fileid).length>0){
                $('#'+this.fileid).val(res.data.src);
              }else{
                $('input[name="'+ this.name +'"]').val(res.data.src);
              }
              //图片外层有其他元素
              if ($('#'+this.parentid).length>0){
                $('#'+this.parentid).removeClass('none');
                $('#'+this.imgid).text('已选择');
              }else if ($('#'+this.imgnews).length>0){            //后台上传新闻图片，需要缩略图
                $('input[name="'+ this.imgthumb +'"]').val(res.data.s_thumb);
                $('#'+this.imgnews).removeClass('none');
                $('#'+this.imgnews).attr('onclick','news_preview("'+ res.data.url +'")');
              }else if($('#'+this.imgdiv).length>0){
                $('#'+this.imgdiv).removeClass('none');
                $('#'+this.imgid).text('已选择');

              }else{
                $('#'+this.imgid).removeClass('none');
                $('#'+this.imgid).text('已选择');
              }
              //执照查看原图
              if($('#'+this.imga).length>0){
                $('#'+this.imga).attr('href', res.data.url);
              }
              //logo和二维码
              if(this.path){
                $.post(weburl+'/index.php?m=ajax&c=uploadfast',{path: this.path,url: res.data.src,img: this.imgid},function(data){})
              }
            }
          }
        });
      }
    }
  });

</script>
</body>
</html>


