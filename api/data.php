<?php
header('Access-Control-Allow-Origin:*');
include_once "../base.php";
$data=["areaList"=>["板橋區","三重區","新莊區","中和區","新店區","汐止區","樹林區","土城區","林口區","淡水區","蘆洲區","永和區","五股區","鶯歌區","三峽區","泰山區","八里區","瑞芳區","金山區","深坑區","三芝區","石門區","萬里區"]];
$dataName=chkp("data");
$temp=json_encode($data[$dataName]);
echo $temp;
// echop($data);
// echop($data["$dataName"]);


?>
