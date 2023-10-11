<?php
$path    = 'file';
$files = scandir($path);
$files = array_diff(scandir($path), array('.', '..'));
$time = time();
foreach($files as $file){
    $arr = explode("_",$file);
    if($arr[0] !="compress"){
        $timeFile = $arr[0];
    }else{
        $timeFile = $arr[1];
    }
    if(((int)$timeFile + 300) < $time ){
        unlink($path."/".$file);
    }
}

?>