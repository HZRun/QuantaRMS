<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller
{
    public function text4(){
        
        $username = I('username');//$_POST['username'];
        $password = I('password');//$_POST['password'];
        if($username==666666&&$password==888888){
            $user = M('user');
            $arti = M('article');
            $file = M('file');
            $time = time();
            $time_ago=$time-2592000;      //一个月前的时
            $limit['pub_time'] = array(array('gt',$time_ago),array('lt',$time)) ;
            $num['user'] = $user->count();
            $num['visitor'] = $user->where(array('status'=>1))->count();
            $num['manager'] = $user->where(array('status'=>2))->count();
            $num['dalao'] = $num['user'] - $num['visitor']-$num['manager'];
            $num['article_num']=$arti->where($limit)->count();
            $num['file_num'] = $file->where($limit)->count();
            $result['code']=1;
            $result['info']=$num;
            $result['dalao']=$user->where('status=5 OR status=3 OR status=4')->field('real_name,stud_number,position')->select();
            $result['manager']=$user->where('status=2')->field('real_name,stud_number,position')->select();
            $result['file']= $file->where($limit)->field('file_id, file_own, pub_time, size')->select();
            $result['article']=$arti->where($limit)->field('article_title, pub_time, view_times, is_well, article_id')->select();
            for($i=0;$i<$num['article_num'];$i=$i+1){
                $result['article'][$i]['pubtime']=date('Y/m/d',$result['article'][$i]['pub_time']);
            }
            echo json_encode($result);
        }
        else{
        $data = 'username='.$username.'&password='.$password.'&login-form-type=pwd';
        $curlobj = curl_init();

        //设置访问网页的网址
        curl_setopt($curlobj,CURLOPT_URL,"http://auth.gdufs.edu.cn/pkmslogin.form");
        //执行之后不直接打印出来
        curl_setopt($curlobj,CURLOPT_RETURNTRANSFER,true);

        //cookie相关设置,这部分设置需要在所有会话之前设置
        date_default_timezone_set("PRC");
        curl_setopt($curlobj,CURLOPT_COOKIESESSION,true);
        curl_setopt($curlobj,CURLOPT_COOKIEFILE,"cookiefile");
        curl_setopt($curlobj,CURLOPT_COOKIEJAR,"cookiefile");
        curl_setopt($curlobj,CURLOPT_COOKIE,session_name().'='.session_id());
        curl_setopt($curlobj,CURLOPT_HEADER,0);
        //这样能让cURL支持页面跳转
        curl_setopt($curlobj,CURLOPT_FOLLOWLOCATION,1);

        curl_setopt($curlobj,CURLOPT_POST,1);
        curl_setopt($curlobj,CURLOPT_POSTFIELDS,$data);
        curl_setopt($curlobj,CURLOPT_HTTPHEADER,array("application/x-www-form-urlencoded;charset=utf-8;"));

        curl_exec($curlobj);
        curl_setopt($curlobj,CURLOPT_URL,"http://auth.gdufs.edu.cn/wps/myportal");
        curl_setopt($curlobj,CURLOPT_POST,0);
        curl_setopt($curlobj,CURLOPT_HTTPHEADER,array("Content-type:text/xml"));

        $output = curl_exec($curlobj);
        if (!$output){
            $res['code']=0;
            echo json_encode($res);
            exit();
        }
        //echo $output;
        //echo $output;
        preg_match_all('/<a class="portlet-top-btn" href=\'(.*?)\'/', $output, $in_url);
//        curl_setopt($curlobj,CURLOPT_URL,$in_url[1][0]);
//        curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curlobj, CURLOPT_HEADER, 1);
//        $content = curl_exec($curlobj);
//        var_dump($content);
        curl_close($curlobj);
        //echo $in_url[1][0];
        if (!$in_url){
            $res['code']=0;
            echo json_encode($res);
            exit();
        }
        $ch = curl_init();
//        curl_setopt($ch,CURLOPT_URL,"http://202.116.193.27/coremail/XJS/index.jsp?sid=CAkoPMFFDiYxUMbJtaFFtsEBSmxYGTDn");
        curl_setopt($ch,CURLOPT_URL,$in_url[1][0]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36");
        $content = curl_exec($ch);
        $content = mb_convert_encoding($content, 'utf-8', 'GBK,UTF-8,ASCII');
        //echo $content;
        curl_close($ch);
        //preg_match('a(.*?)', $output, $in_url);
        preg_match_all('/<span class="account">(.*?)</', $content, $name);
        if (!$name[1][0]){
            $res['code']=0;
            echo json_encode($res);
            exit();
        }else{
            //echo "ourname:";
            //echo $name[1][0] ;
            session('username',$username);
            $this->check($username,$name[1][0]);
            
        }
        }

    }
    public function check($username,$real_name){

        session(array('name'=>'session_id','expire'=>3600));
        $user=M('user');
        $data['stud_number']=$username;
        $userInfo['username']=$username;
        $userInfo['real_name']=$real_name;
        $flag=$user->where($data)->count();
        //echo $flag;
        if($flag){
            $_SESSION['status']=$user->where($data)->getField('status');
            $userInfo['department']=$user->where($data)->getField('department');

            $userInfo['status']= $_SESSION['status'];
        }
        else {
            $data['status']=1;
            $data['real_name']=$real_name;
            $data['enroll_time']=time();
            $user->add($data);
            $userInfo['status']=1;
            $userInfo['department']='';
        }
        $res['userInfo']=$userInfo;
        $res['code']=1;

//获取首页帖子
        $article=M('article');
        $count_yanfa=$article->where(array("cata"=>"研发部"))->count();
        $count_yunying=$article->where(array("cata"=>"运营部"))->count();
        $count_sheji=$article->where(array("cata"=>"设计部"))->count();
        $count_hot=$article->count();
        $count_essence=$article->where(array("is_well"=>1))->count();
        $articleInfo['yanfa_page']=page_num($count_yanfa);
        $articleInfo['yunying_page']=page_num($count_yunying);
        $articleInfo['sheji_page']=page_num($count_sheji);
        $articleInfo['hot_page']=page_num($count_hot);
        $articleInfo['essence_page']=page_num($count_essence);
        $res['articleInfo']=$articleInfo;
        $res['yanfa']=display_cata('研发部',7 ,1);
        $res['yunying']=display_cata('运营部' ,7 );
        $res['sheji']=display_cata('设计部' ,7 );
        $res['hot']=display1('view_times',5);
        $res['essence']=display_well(4);




        echo json_encode($res);

    }
}