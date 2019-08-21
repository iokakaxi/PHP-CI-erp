<?php $this->load->view('header');?>

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

<style>
body{overflow-y:hidden;}
.matchCon{width:280px;}
#tree{background-color: #fff;width: 225px;border: solid #ddd 1px;margin-left: 5px;height:100%;}
h3{background: #EEEEEE;border: 1px solid #ddd;padding: 5px 10px;}
.grid-wrap{position:relative;}
.grid-wrap h3{border-bottom: none;}
#tree h3{border-style:none;border-bottom:solid 1px #D8D8D8;}
.quickSearchField{padding :10px; background-color: #f5f5f5;border-bottom:solid 1px #D8D8D8;}
#searchCategory input{width:165px;}
.innerTree{overflow-y:auto;}
#hideTree{cursor: pointer;color:#fff;padding: 0 4px;background-color: #B9B9B9;border-radius: 3px;position: absolute;top: 5px;right: 5px;}
#hideTree:hover{background-color: #AAAAAA;}
#clear{display:none;}
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
	<div class="mod-search cf">
	    <div class="fl">
	      <ul class="ul-inline">
	        <li>
	          <input type="text" id="matchCon" class="ui-input ui-input-ph matchCon" value="按商品编号，商品名称，规格型号等查询">
	        </li>
	        <li><a class="ui-btn mrb" id="search">查询</a></li>
	      </ul>
	    </div>
	    <div class="fr">
			<a href="#" class="ui-btn ui-btn-sp mrb" id="btn-add">新增</a>
			<input class="admin_button" type="button" name="delsub" value="导入" onClick="Export();" />
			<input class="admin_button" id="btn-unified" type="button" value="同步" />


		<!--<a href="#" class="ui-btn mrb" id="btn-print">打印</a>--><a class="ui-btn mrb" id="btn-disable">禁用</a><a class="ui-btn mrb" id="btn-enable">启用</a><!--<a href="#" class="ui-btn mrb" id="btn-import">导入</a>--><a href="#" class="ui-btn mrb" id="btn-export">导出</a><a href="#" class="ui-btn" id="btn-batchDel">删除</a></div>
	  </div>
	  <div class="cf">
	    <div class="grid-wrap fl cf">
	    	<h3>当前分类：<span id='currentCategory'></span><a href="javascript:void(0);" id='hideTree'>&gt;&gt;</a></h3>
		    <table id="grid">
		    </table>
		    <div id="page"></div>
		</div>
		<div class="fl cf" id='tree'>
			<h3>快速查询</h3>
			<div class="quickSearchField dn">
				<form class="ui-search" id="searchCategory">
					<input type="text" class="ui-input" /><button type="submit" title="点击搜索" >搜索</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="export" style="display:none;">
	<div style=" margin-top:10px;">
		<div>
			<form action="../basedata/inventory/import" onsubmit="return mlogin(this);"   enctype= "multipart/form-data" method="post" id="formstatus" class="myform">
				<input type="hidden" name="pytoken" value="{yun:}$pytoken{/yun}">
				<input type="hidden" name="where" value="{yun:}$where{/yun}">
				<input type="hidden" name="ids">
				<a style="margin-left: 22px;color: red;font-size: 16px;" href="/data/upfile/chanpin_erp_mod.xlsx"  class=" " >下载导入模板</a>

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

<!--<script src="--><?php //echo base_url()?><!--statics/js/dist/goodsList.js?ver=20140430"></script>-->
<script src="<?php echo base_url()?>statics/js/dist/goodsList.js?ver=201404"></script>

<link href="<?php echo base_url()?>statics/js/layui/css/layui.css" rel="stylesheet" type="text/css">
<script src="<?php echo base_url()?>statics/js/layui/layui.js?ver=20140430"></script>
<!--<script src="--><?php //echo base_url()?><!--statics/js/layui.uploadex.js?ver=20140430"></script>-->
<script src="<?php echo base_url()?>statics/js/layui/phpyun_layer.js?ver=20140430"></script>

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
<script type="text/javascript">
	var weburl="";
	function Export(){
		add_class('上传导入文件','650','400','#export','');
	}
	function add_class(name,width,height,divid,url){
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
			$("#grid").trigger("reloadGrid")
		});
		return false;
	}
</script>
</body>
</html>
