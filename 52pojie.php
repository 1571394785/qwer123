<?php
error_reporting(0);
function c($url, $ua = 0){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_REFERER, $url);
if ($ua) {
	curl_setopt($ch, CURLOPT_USERAGENT, $ua);
} else {
	//$ua留空后默认执行，下行 "" 内是HTC_One_X手机的UA
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; U; Android 4.0.4; es-mx; HTC_One_X Build/IMM76D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0");
}
$data = curl_exec($ch);
curl_close($ch);
return $data;
}

function start($id, $link =0){
//start函数中$link留空即为0，不为空则执行下列第一条if语句获取ID
if ($link) {
	$length = strlen($link)-strrpos($link,"/")+1;//[获取ID的长度]用$link的全长 - $link从开头到最后一次出现的"/"的长度+1
	$id = substr($link,strrpos($link,"/")+1,$length);//[截取ID]用 $link从开头到最后一次出现的"/"的长度+1 来作为$link截取的开始位置，截取$length(ID长度)
}
//使用curl_post函数获取网页源码
$curldata = c('https://pan.lanzou.com/tp/'.$id, "");
//检测源码中是否包含"您输入地址有误"字符串，如果有就是ID错误，也就是$link错误，直接返回结果"linkerror"
if (strpos($curldata, "您输入地址有误")) {
	$result="linkerror";
	return $result;//返回出错原因结果
}else{
//如果没有就执行下列语句
	preg_match("|urlq = '(.+?)'|U", $curldata, $oarr);//使用正则表达式匹配链接前半段
	$olink = $oarr[1];//把链接前半段赋值给变量olink
	preg_match_all("|urlq \\+ '(.*)'|U",$curldata,$tarr);//使用正则表达式匹配链接前半段
	$tlink = $tarr[1][1];//把链接后半段赋值给变量tlink
	$result = $olink.$tlink;//把链接前半段olink和链接后半段tlink连接赋值给变量result
	return $result;//返回下载链接结果
	}
}


$id = isset($_GET['id']) ? $_GET['id'] : NULL;//获取ID，默认为空
$link = isset($_GET['link']) ? $_GET['link'] : NULL;//获取link，默认为空

if ($id && $link) {
	//如果id和link参数同时存在直接报错如下
	$msg = "错误，可用的参数仅能单独存在！";
}elseif ($id==NULL && $link==NULL) {
	//如果id和link参数都没填，也就是默认网页打开时的情况
	$msg ="蓝奏云解析下载系统：</br>支持ID(id)解析和外链分享地址(link)解析</br>*括号内为目前支持的可用参数名";
}elseif ($id) {
	//如果只获取id，执行start函数获取result
	$result = start($id, "");
}elseif ($link) {
	//如果只获取link，执行start函数获取result
	$result = start("", $link);
}
if ($result == "linkerror"){exit("<h1>错误，您输入的参数有误</br>没有找到你要访问的文件</h1>");}//如果返回的$result等于"linkerror"，退出并显示错误原因
//如果返回的$result不为"linkerror"，输出下载链接...
if ($result){$msg = "获取链接成功！你的下载链接：</br><input type='text' style='width:500px;height:50px;font-size:20px' value='".$result."'/></br><a href='".$result."'>点我下载</a>";}
//直接跳转下载代码如下，去掉64行代码，把下面代码的注释符号去掉
//if ($result){header('location:'.$result);exit();}
if ($msg){echo '<h1>'.$msg.'</h1>';}
?>