<?php 
$dsn="mysql:host=localhost;charset=utf8;dbname=resume";
$pdo=new PDO($dsn,"root","");
// $dsn="mysql:host=localhost;charset=utf8;dbname=s1080317";
// $pdo=new PDO($dsn,"s1080317","s1080317");

session_start();

if(empty($_SESSION['total'])){
  $total=find("resume_total",1);
  $_SESSION['total']=$total['total']+1;
  $total['total']=$total['total']+1;
  save("resume_total",$total);
}


function br(){
  echo "<br>";
}
function echop(...$arg){
  if(isset($arg[1])){
    echo $arg[1] . "=";
  }
  if(!is_array($arg[0])){
    echo $arg[0];
    br();
  }else{
    print_r($arg[0]);
    br();
  }
}
function chkG($a,...$b){
  if(!empty($_GET[$a])){
    return $_GET[$a];
  }else{
    if(!empty($b)){
      return $b[0];
    }else{
      return "";
    }
  }
}
function chkP($a,...$b){
  if(!empty($_POST[$a])){
    return $_POST[$a];
  }else{
    if(!empty($b)){
      return $b[0];
    }else{
      return "";
    }
  }
}
function chkSS($a,...$b){
  if(!empty($_SESSION[$a])){
    return $_SESSION[$a];
  }else{
    if(!empty($b)){
      return $b[0];
    }else{
      return "";
    }
  }
}
//取得單筆資料
function find($table,...$arg){
  global $pdo;

  $sql="select * from $table where ";

  if(is_array($arg[0])){

    foreach($arg[0] as $key => $value){

      $tmp[]=sprintf("`%s`='%s'",$key,$value);

    }

    $sql=$sql . implode(" && ",$tmp);

  }else{

    $sql=$sql . " `id`='".$arg[0]."'";

  }

  // echo $sql;

  return $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
  // return $pdo->query($sql);

}


//取得全部資料
function all($table,...$arg){
  global $pdo;
  // echop($arg);
  $sql="select * from $table";

    if(!empty($arg[0])){

      foreach($arg[0] as $key => $value){

        $tmp[]=sprintf("`%s`='%s'",$key,$value);

      }
      $sql=$sql . " where " . implode(" && ",$tmp);

    }    

    if(!empty($arg[1])){

      $sql=$sql . $arg[1];

    }

  // echo $sql;

  return $pdo->query($sql)->fetchAll();
}

//計算資料筆數
function nums($table,...$arg){
  global $pdo;

  $sql="select count(*) from $table";

    if(!empty($arg[0])){

      foreach($arg[0] as $key => $value){

        $tmp[]=sprintf("`%s`='%s'",$key,$value);

      }

      $sql=$sql . " where " . implode(" && ",$tmp);

    }    

    if(!empty($arg[1])){

      $sql=$sql . $arg[1];

    }

  // echo $sql;

  return $pdo->query($sql)->fetchColumn();  

}

//新增/編輯單筆資料
function save($table,$data){
  global $pdo;

  if(!empty($data['id'])){
    //update

    foreach($data as $key => $value){

      if($key!="id"){

        $tmp[]=sprintf("`%s`='%s'",$key,$value);

      }

    }

    $sql="update $table set ".implode(",",$tmp)." where `id`='".$data['id']."'";

  }else{
    //insert
    $sql="insert into $table (`".implode("`,`",array_keys($data))."`) value('".implode("','",$data)."')";

  }

  // echo $sql;
  return $pdo->exec($sql);
}


//刪除資料
function del($table,...$arg){
  global $pdo;

  $sql="delete from $table where ";

  if(is_array($arg[0])){

    foreach($arg[0] as $key => $value){

      $tmp[]=sprintf("`%s`='%s'",$key,$value);

    }

    $sql=$sql . implode(" && ",$tmp);

  }else{

    $sql=$sql . " `id`='".$arg[0]."'";

  }

  //echo $sql;

  return $pdo->exec($sql);
}


//查詢資料
function q($sql){
  global $pdo;
  // echop($sql);
  return $pdo->query($sql)->fetchAll();

}

//頁面導向
function to($path){
  
  header("location:".$path);

}

?>