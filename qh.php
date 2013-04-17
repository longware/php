<?php
//get songs real URL from http://www.youban.com/mp3-4261.html

set_time_limit(0);//防止超时
/* by longware@gmail.com */
/*
 d:\WebServer\php\php.exe D:\wwwroot\qh.php
 wget -i mp3.txt
*/

function getHttpContentBySock($url, $method="", $referer="", $param = ""){//获取http请求的内容，包括可以获取html和二进制的图片
    $info = parse_url($url);
    $method = empty($method) ? "GET" : "POST";
    $referer = empty($referer) ? $url : $referer;
    $contents = '';

    $fp = fsockopen($info['host'], 80, $errno, $errstr, 30);
    if (!$fp) {
        echo " $errstr ($errno) <br />\n";
    } else {
        $out = $method." $url HTTP/1.1\r\n";
        $out .= "Host: ".$info['host']."\r\n";
        $out .= "Connection: Keep-Alive\r\n";
        $out .= "User-Agent: Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)\r\n";
        $out .= ($method=="POST") ? "Content-Type: application/x-www-form-urlencoded\r\n" : "";
        $out .= "Accept: */*\r\n";
        $out .= "x-flash-version: 11,7,700,169\r\n";
        $out .= "Pragma: no-cache\r\n";
        $out .= "Pragma: no-cache\r\n";
        $out .= "Referer: $referer\r\n";//这个不能少
        $out .= empty($param) ? "" : "Content-Length: ".strlen($param)."\r\n";
        $out .= "Accept-Language: zh-CN,zh;q=0.8\r\n";
        $out .= "Cookie: fromurl=http%3A%2F%2Fwww.youban.com%2Fmp3-4261.html; vtime=1366201681; Hm_lvt_c4412d2ffc4bf832f4a2b1e8d0c92266=1366201707; Hm_lpvt_c4412d2ffc4bf832f4a2b1e8d0c92266=1366201779; firstmp3info=1366201964325; openmp3box=true; ad_play_index=37; bdshare_firstime=1366201706378\r\n\r\n";
        $out .= $param."\r\n";
        
        //echo "#".$out."#";

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

//songs id
$ids = array(4210, 4210, 4216, 4216, 4236, 4236, 4238, 4238, 4250, 4250, 4239, 4239, 4203, 4203, 4261, 4261, 4246, 4246, 4226, 4226, 4200, 4200, 4275, 4275, 4230, 4230, 4255, 4255, 4213, 4213, 4281, 4281, 4211, 4211, 4266, 4266, 4232, 4232, 4272, 4272, 4237, 4237, 4242, 4242, 4204, 4204, 4224, 4224, 4265, 4265, 4229, 4229, 4271, 4271, 4257, 4257, 4269, 4269, 4291, 4291, 4280, 4280, 4278, 4278, 4209, 4209, 4233, 4233, 4295, 4295, 4217, 4217, 4243, 4243, 4262, 4262, 4212, 4212, 4290, 4290, 4270, 4270, 4274, 4274, 4234, 4234, 4267, 4267, 4251, 4251, 4276, 4276, 4258, 4258, 4245, 4245, 4208, 4208, 4296, 4296, 4227, 4227, 4218, 4218, 4252, 4252, 4219, 4219, 4207, 4207, 4256, 4256, 4287, 4287, 4289, 4289, 4240, 4240, 4221, 4221, 4228, 4228, 4273, 4273, 4293, 4293, 4282, 4282, 4284, 4284, 4223, 4223, 4259, 4259, 4199, 4199, 4220, 4220, 4279, 4279, 4254, 4254, 4286, 4286, 4222, 4222, 4244, 4244, 4268, 4268, 4277, 4277, 4285, 4285, 4263, 4263, 4283, 4283, 4247, 4247, 4288, 4288, 4260, 4260, 4294, 4294, 4264, 4264, 4201, 4201, 4202, 4202, 4215, 4215, 4205, 4205, 4225, 4225, 4249, 4249, 4241, 4241, 4248, 4248, 4231, 4231, 4214, 4214, 4253, 4253, 4235, 4235, 4292, 4292, 4206, 4206);
$ids = array_unique($ids);

$filename = "./mp3.txt";
$baseurl = "http://www.youban.com/book/getmp3.php";
$referer = "http://www.youban.com/swf/musicbox/playerpage_bottom.swf?20121130";

if (!$handle = fopen($filename, 'a')) {
    echo "can not open file $filename";
    exit;
}

foreach ($ids as $id) {
    $html = getHttpContentBySock($baseurl, "POST", $referer, "id=".$id);
    
    if (fwrite($handle, $html."\r\n") === FALSE) {
        echo "can not write content to file $filename";
        exit;
    }
    
    echo "song ".$id." get ok<br>\r\n";
    //break;
}

fclose($handle);
echo "end";
?>
