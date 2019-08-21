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
				,accept: layaccept,url: weburl+'/index.php?m=ajax&c=layui_upload'
				,data: {path: path}
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
