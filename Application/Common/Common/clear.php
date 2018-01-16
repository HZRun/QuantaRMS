<?php
use Think\Controller;
use Think\Model;
$username = 'run';
$psw = '15a505';
$pdo = new PDO('mysql:host=localhost;dbname=RMS',$username,$psw);
//$draft = M('draft');
$time = time();
$time_ago=$time-86400*7;      //一周前的时间
$del = $pdo->exec("")
$limit['pub_time'] = array(array('gt',$time_ago),array('lt',$time)) ;
$draft->where($limit)->delete();