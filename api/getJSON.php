<?php
    // 設定接受所有來源的AJAX
    header("Access-Control-Allow-Origin: *");
    if(!empty($_POST["url"])){
        // 初始化 curl
        $curl = curl_init();
    
        // 設定發出請求的瀏覽器
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
    
        // 設定接受所有 https 憑證，不做驗證
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
    
        // 設定跟隨重新導向
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true); 
    
        // 重新導向時自動設定訪客來源 referer
        curl_setopt($curl, CURLOPT_AUTOREFERER, true); 
    
        curl_setopt($curl, CURLOPT_URL, $_POST["url"]);
    
        curl_exec($curl);
        
        curl_close($curl);
        
        if(preg_match('/^\xEF\xBB\xBF/',$data)){
        //去除可能存在的BOM
            $data=substr($data,3);
        }
        
        $data=json_decode($data,true);
        $data=json_encode($data);

        echo $data;
    }
?>