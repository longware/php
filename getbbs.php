<?php
 set_time_limit(0);//防止超时
 /* by longware@gmail.com */
/*
d:\WebServer\php\php.exe D:\wwwroot\autohome\bbs.php 1 100
*/
 function getHttpContentBySock($url, $referer=""){//获取http请求的内容，包括可以获取html和二进制的图片
     $info = parse_url($url);
     $referer = empty($referer) ? $url : $referer;
     $contents = '';
 
     $fp = fsockopen($info['host'], 80, $errno, $errstr, 30);
     if (!$fp) {
         echo " $errstr ($errno) <br />\n";
     } else {
         $out = "GET $url HTTP/1.1\r\n";
         $out .= "Host: ".$info['host']."\r\n";
         $out .= "Connection: Keep-Alive\r\n";
         $out .= "User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)\r\n";
         $out .= "Accept: */*\r\n";
         $out .= "Referer: $referer\r\n";//这个不能少
         $out .= "Accept-Language: zh-CN,zh;q=0.8\r\n";
         $out .= "Accept-Charset: GBK,utf-8;q=0.7,*;q=0.3\r\n\r\n";
 
         fwrite($fp, $out);
 
         $line = 1; $flag = false;
         while (!feof($fp)) {
             $tmp = fgets($fp, 2048);
             if($line>1){
                 if($flag){
                     $contents .= $tmp;
                 }else{
                     if (strlen(trim($tmp))<1) {//去掉http response的头文本
                         $flag = true;
                     }
                 }
             }
             $line++;
         }
         fclose($fp);
     }
 
     return $contents;
 }
 
 /* by longware */
 function writeContent($url, $page){//写文件
     $html = getHttpContentBySock($url,$url);
 
     $pattern = "/src9=\"http:\/\/[a-zA-Z0-9_\.\/]+\"/";//正则匹配图片网址
     preg_match_all($pattern, $html, $matches, PREG_PATTERN_ORDER);
     //print_r($matches);
 
     $search  = array('"', 'src9=');
     foreach ($matches as $matche_array) {//获取真实图片网址
         foreach ($matche_array as $matche_line) {
             $imgurl = str_replace($search,'',$matche_line);
             $path_info = explode("//", $imgurl);
             $dir = "./".dirname($path_info[1])."/";//按网址的路径建目录
             if (!is_dir($dir)) {
                 mkdir($dir,0777,true);
             }
             $filename = "./".$path_info[1];
             file_put_contents($filename, getHttpContentBySock($imgurl,$url) );//获取图片并写文件
             echo $filename."<br>\r\n";
         }
     }
 
     //replace href 替换无用代码
     $html = str_replace('src="http://x.autoimg.cn/club/lazyload.png" src9="','src="', $html);
     $html = str_replace('http://','./',$html);
     $html = str_replace('onload=','x1=',$html);
     $html = str_replace('onerror=','x2=',$html);
     $html = str_replace('<script','<a style="display:none" ',$html);
     $html = str_replace('</script','</a',$html);
 
     $filename = "./threadowner-o-200042-19582947-".$page.".html";//threadowner-o-200042-19582947-2.html 写本地html文件
     file_put_contents($filename, $html);
     echo $filename."--------------------------------------------<br>\r\n";
 }
 /* by longware */
 //main
 if( empty($_SERVER['argv'][1]) || empty($_SERVER['argv'][2]) ){die('input args');}
 $url = "http://club.autohome.com.cn/bbs/threadowner-o-200042-19582947-#.html";//主帖URL规律
 $min = $_SERVER['argv'][1];//下载起始页
 $max = $_SERVER['argv'][2];//下载结束页
 //print_r($_SERVER['argv']);
 for ($i = $min; $i <= $max; $i++) {
     writeContent( str_replace('#',$i,$url), $i );
 }
 /* by longware */
 ?>
