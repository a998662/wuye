/*
**************************
(C)2010-2013 phpMyWind.com
update: 2012-4-27 11:35:55
person: Feng
**************************
*/


/***
 * 上传图片 后台专用
 * @access  public
 * @num int 一次上传图片num张图
 * @elementid string 上传成功后返回路径插入指定ID元素内
 * @path  string 指定上传保存文件夹,默认存在uploads/目录
 * @callback string  回调函数(单张图片返回保存路径字符串，多张则为路径数组
 * @width	string
 * @height	string
 */
function GetUploadify(num,path,callback,wrap_width,img_width,thumb_w,thumb_h)
{
    var w1 = wrap_width ? wrap_width : 0;
    var w2 = img_width ? img_width : 0;
    var w3 = thumb_w ? thumb_w : 0;
    var h3 = thumb_h ? thumb_h : 0;
    var upurl = '/upload/upload/num/'+num+'/path/'+path+'/func/'+callback+'/wrap_width/'+w1+'/img_width/'+w2+'/thumb_w/'+w3+'/thumb_h/'+h3;
    var iframe_str='<iframe frameborder="0"';
    iframe_str=iframe_str+'id=uploadify';
    iframe_str=iframe_str+' src='+upurl;
    iframe_str=iframe_str+' allowtransparency="true" class="uploadframe" scrolling="no"> ';
    iframe_str=iframe_str+'</iframe>';
    $("body").append(iframe_str);
    $("iframe.uploadframe").css("height",$(document).height()).css("width","100%").css("position","absolute").css("left","0px").css("top","0px").css("z-index","999999").show();
    $(window).resize(function(){
        $("iframe.uploadframe").css("height",$(document).height()).show();
    });
}

/*
 * 删除组图input
 *
 * @access   public
 * @val      string  删除的图片input
 */

function ClearPicArr(val,path)
{
	//$("li[rel='"+ val +"']").remove();
	$.get(
			path+'/upload/delupload/',
			{action:"del", filename:val},
			function(){}
	);
}