<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;

class RMSController extends Controller
{
    public function publish()
    {//发表博文
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $arti = M('article');

            $article['article_id'] = base_convert(uniqid(), 16, 10);
            $article['article_title'] = I('title');
            $blog = I('blog');
            $article['author'] = I('real_name');
            $article['cata'] = I('apartment');
            $article['abstract'] = I('synopsis');
            $article['stud_number'] = I('username');
            //$img=I('img');
            //$article['image_num']=I('img_count');
            // echo $article['image_num'];
            $name = './Public/blog/' . $article['article_id'] . '.txt';
            $myfile = fopen("$name", "w");
            fwrite($myfile, $blog);
            fclose($myfile);
            $article['content'] = $name;
            $article['pub_time'] = time();
            $article['view_times'] = 0;

            $res = $arti->add($article);
            if ($res) {
                $result['code'] = 1;
                echo json_encode($result);
            } else {
                $result['code'] = 0;
                echo json_encode($result);

            }
        }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }


    public function del_article()
    {
        if (isset($_SESSION['username']) && ($_SESSION['status']>=1)) {
        $model = new model();
        $model->startTrans();
        $arti = M('article');
        $id = I('article_id');
        $file = $arti->where("article_id=$id")->getField('content');
        $flag2 = unlink($file);
        $flag1 = $arti->where("article_id=$id")->delete();
        if ($flag1 && $flag2) {
            $model->commit();
            $result['code'] = 1;
            echo json_encode($result);
        } else {
            $result['code'] = 0;
            echo json_encode($result);
            $model->rollback();
        }
    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }

    public function detail()
    {
        if (isset($_SESSION['username']) && ($_SESSION['status']>=1)) {
        $arti = M('article');
        $id = I('article_id');
        $result = $arti->where("article_id=$id")->field('article_id,article_title,content,author,view_times,cata,pub_time,is_well')->select();
        //echo "aaa=".$result1[0]['view_times'];
        //var_dump($result);
        $new['view_times'] = $result[0]['view_times'] + 1;

        $first = $result[0]['content'];
        $first = substr($first, 1, strlen($first) - 1);
        //echo $new['view_times'];
        $result[0]['content'] = 'http://120.77.222.115/RMS' . $first;
        //echo $result[0]['content'];
        $arti->where("article_id=$id")->save($new);
        //echo $result['article'][0]['article_id'];


        if ($result[0]['article_id']) {
            $result['code'] = 1;
            echo json_encode($result);
        } else {
            $res['code'] = 0;
            echo json_encode($res);
        }

    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }
    public function essence()
    {
        if (isset($_SESSION['username']) && ($_SESSION['status']>=1)) {
        $arti = M('article');
        $id = I('article_id');
        $res = $arti->where(array("article_id" => $id))->field('is_well')->select();
        //$res[0]['is_well'];
        if ($res[0]['is_well']) {
            $result['code'] = 0;
            echo json_encode($result);
        } else {
            $data['is_well'] = 1;
            $arti->where(array("article_id" => $id))->save($data);
            $result['code'] = 1;
            echo json_encode($result);
        }
    }
        elseif(isset($_SESSION['username'])){
        echo json_encode(array('code'=>272));
    }
        else json_encode(array('code'=>374));
    }

    public function home()
    {

        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $article = M('article');
            $user = M('user');
            $data['stud_number'] = $_SESSION['username'];
            $count_yanfa = $article->where(array("cata" => "研发部"))->count();
            $count_yunying = $article->where(array("cata" => "运营部"))->count();
            $count_sheji = $article->where(array("cata" => "设计部"))->count();
            $count_hot = $article->count();
            $count_essence = $article->where(array("is_well" => 1))->count();
            $articleInfo['yanfa_page'] = page_num($count_yanfa);
            $articleInfo['yunying_page'] = page_num($count_yunying);
            $articleInfo['sheji_page'] = page_num($count_sheji);
            $articleInfo['hot_page'] = page_num($count_hot);
            $articleInfo['essence_page'] = page_num($count_essence);
            $res['articleInfo'] = $articleInfo;
            $res['yanfa'] = display_cata('研发部', 7, 1);
            $res['yunying'] = display_cata('运营部', 7);
            $res['sheji'] = display_cata('设计部', 7);
            $res['hot'] = display1('view_times', 5);
            $res['essence'] = display_well('pub_time', 4 ,1);
            $result=$user->where($data)->field('real_name,position,status,department')->select();

            //var_dump($result);

            $userInfo['status']=$result[0]['status'];
            $userInfo['position']=$result[0]['position'];
            $userInfo['department']=$result[0]['department'];
            $userInfo['real_name']=$result[0]['real_name'];
            $userInfo['username']=$_SESSION['username'];
            $res['userInfo']=$userInfo;
            $res['code']=1;
            echo json_encode($res);
        }

        else {
            echo json_encode(array('code'=>374));
        }
    }

    public function upload()
    {
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->saveName = array('uniqid', '');
        $upload->exts = array();// 设置附件上传类型
        $upload->rootPath = './Public/'; // 设置附件上传根目录
           $upload-> autoSub       =  false; //自动子目录保存文件
        $upload->savePath = 'file/'; // 设置附件上传（子）目录
        $info = $upload->upload();
        $filename = $_FILES["file"]["name"];
        //$photo_user = I('phoneNum');
        $model = M('file');
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
            echo json_encode(array('code' => 0));
        } else {// 上传成功 获取上传文件信息
            foreach ($info as $file) {
                //$name=$file['savename'];
                $file_url = './Public/' . $file['savepath'] . $file['savename'];
                $data['url'] = $file_url;
                $fileId = base_convert(uniqid(), 16, 10);
                $pub_time=time();
                $size=formatSizeUnits($file['size']);
                $data['size']=$size;

                $data['pub_time']= $pub_time;

                //echo $movementId;
                $data['file_id'] = $fileId;
                $data['file_own']=$_SESSION['username'];
                $data['title']=$filename;
                $model->add($data);
                echo json_encode(array('code' => 1));

            }
        }
    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else echo json_encode(array('code'=>374));
    }
    public function download(){
          $file=M('file');
          $id=I('file_id');
          $result=$file->where("file_id=$id")->field('url')->select();
          $first = $result[0]['url'];
          $then = substr($first, 1, strlen($first) - 1);
        //echo $new['view_times'];
          $filename= 'http://120.77.222.115/RMS' . $then;
       // echo $filename;
          $res=download_file($filename);
        echo json_encode(array('code' => 1));

       // echo $res;
    }
    public function my_file(){
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $page=I('page');
            $username=I('username');
            $file = M('file');
            $count = $file->where("file_own=$username")->count();
            if($page > ceil($count / 10)){
                echo json_encode(array('code'=>404));
                exit();

            }

            else{
                $res['page_num'] = page_num($count,10,$page);
                $res['file'] = myfile($username,10,$page);
                for($i=0;$i<$res['page_num'];$i=$i+1){
                    $res['file'][$i]['pub_time']=date('Y/m/d',$res['file'][$i]['pub_time']);
                }

            }
            $res['code']=1;

            echo json_encode($res);
        }
        elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }


    public function file(){
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $page=I('page');
            $file = M('file');
            $count = $file->count();
            if($page > ceil($count / 10)){
                echo json_encode(array('code'=>404));
                exit();

            }

            else{        $res['page_num'] = page_num($count,10,$page);
                $res['file'] = getfile(10,$page);
                for($i=0;$i<$res['page_num'];$i=$i+1){
                    $res['file'][$i]['pub_time']=date('Y/m/d',$res['file'][$i]['pub_time']);

                    $first = $res['file'][$i]['url'];
                    $first = substr($first, 1, strlen($first) - 1);
                    $res['file'][$i]['url'] = 'http://120.77.222.115/QuantaRMS' . $first;
                }
            }
            $res['code']=1;

            echo json_encode($res);
        }
        elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }

    public function search()
    {        //搜索
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $arti = M('article');
        $keyword = I('keyword');
        //$num=I('num');     //每页$num条记录
        $page = I('page');   //第 $page 页
        $search['article_title'] = array('like', "%$keyword%");
        $result['count'] = $arti->where($search)->count();
        if ($page > ceil($result['count'] / 7)) {
            echo json_encode(array('code' => 0));
            exit();
        }
        $result['page_num'] = page_num($result['count'], 7, $page);
        $result['article'] = display_title($keyword, 7, $page);
        if ($result) {
            $result['code'] = 1;
            echo json_encode($result);
        } else {
            $res['code'] = 0;
            echo json_encode($res);
        }
        //echo $result[2][pub_time];
        // echo time();

    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }


    public function search_file()
    {        //搜索
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $file = M('file');
            $keyword = I('keyword');
            //$num=I('num');     //每页$num条记录
            $page = I('page');   //第 $page 页
            $search['title'] = array('like', "%$keyword%");
            $result['count'] = $file->where($search)->count();
            if ($page > ceil($result['count'] / 7)) {
                echo json_encode(array('code' => 0));
                exit();
            }
            $result['page_num'] = page_num($result['count'], 10, $page);
            $result['file'] = $file->where($search)->limit(10*($page-1),10)->field('file_id, url, file_own, pub_time, size, title')->select();
            for($i=0;$i<$result['count'];$i=$i+1){
                $result['file'][$i]['pub_time']=date('Y/m/d',$result['file'][$i]['pub_time']);
            }
            if ($result) {
                $result['code'] = 1;
                echo json_encode($result);
            } else {
                $res['code'] = 0;
                echo json_encode($res);
            }
            //echo $result[2][pub_time];
            // echo time();

        }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }


    public function search_user()
    {    //用户查询
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $user = M('user');
        $stud_num = I('stud_number');
        $sear['stud_number'] = $stud_num;
        $result = $user->where($sear)->field('stud_number,real_name')->select();
        if ($result[0]['stud_number']) {
            $result['code'] = 1;
            echo json_encode($result);
        } else echo json_encode($result['code'] = 0);
    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }
    public function set_position()
    {     //权限赋予
        //A1=>CEO, A2=>VP,A3=>CTO,A4=>COO,A5=>CDO
        //B1=>'研发部VO，B2=>'运营部VO'，B3=>'设计部经理'
        //C1=>'研发部经理'，C2=>'运营部经理'，C3=>'设计部经理'
        //D1=>'研发部经理实习生'，D2=>'运营部经理实习生'，D3=>'设计部经理实习生'
        //E=>'普通用户'
        //1=>普通用户，2=>quanta实习生，3=>经理，4=>vo,5=>vo以上
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $user = M('user');
        $stud['stud_number'] = I('stud_number');
        $stud['position'] = I('position');
        switch ($stud['position']) {
            case '研发部实习生':
            case '研发部经理':
                $stud['department'] = '研发部';
                $stud['status'] = 2;
                break;
            case '运营部实习生':
            case '运营部经理':
                $stud['department'] = '运营部';
                $stud['status'] = 2;
            case '设计部经理':
            case '设计部实习生':
                $stud['department'] = '设计部';
                $stud['status'] = 2;
                break;
            case 'VO':
                $stud['status'] = 3;
                break;
            case 'CTO':
            case 'COO':
            case 'VP':
            case 'CDO':
                $stud['status'] = 4;
                break;
            case 'CEO':
                $stud['status'] = 5;
            default :
                echo json_encode(array('code' => 0));
        }
        $user->save($stud);
        echo json_encode(array('code' => 1));
    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }

    public function my_article()
    {      //我的博文
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $arti = M('article');
        $data['stud_number'] = I('username');
        $page = I('page');
        $count = $arti->where(array("stud_number" => $data['stud_number']))->count();
        if ($page > ceil($count / 7)) {
            echo json_encode(array('code' => 0));
            exit();
        }
        if ($data['stud_number']) {
            if ($count) {
                $result = display_user($data['stud_number'], 7, $page);
                $result['count'] = $count;
                $result['code'] = 1;
                $result['page_num'] = page_num($count, 7, $page);
                echo json_encode($result);
            } else {
                $result['count'] = $count;
                $result['code'] = 1;
                echo json_encode($result);
            }
        } else echo json_encode(array('code' => 0));
    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }
    public function show_member()
    {
        $user = M('user');
        $num['user'] = $user->count();
        $data['status'] = 1;
        $num['visitor'] = $user->where($data)->count();
        $num['member'] = $num['user'] - $num['visitor'];
        return $num;
    }


    public function sub_article()
    {
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            if ($_SESSION['status'] > 3) {
                $article = M('article');
                $username = I('username');
                $page = I('page');
                if ($_SESSION['status'] == 4) {
                    $user = M('user');
                    $department = $user->where(array('stud_number' => $username))->getField('department');
                    //    echo $department;


                    //    var_dump($result['article']);
                    $result['count'] = $article->where(array('cata' => $department))->count();
                    if ($page > ceil($result['count'] / 7)) {
                        echo json_encode(array('code' => 0));
                        exit();
                    }
                    $result['page_num'] = page_num($result['count'], 7, $page);
                    $result['article'] = display_cata($department, 7, $page);
                    echo json_encode($result);
                }
                if ($_SESSION['status'] == 5) {
                    $result['count'] = $article->count();

                    if ($page > ceil($result['count'] / 7)) {
                        echo json_encode(array('code' => 0));
                        exit();
                    }
                    $result['page_num'] = page_num($result['count'], 7, $page);
                    $result['article'] = display1('pub_time', 7, $page);
                    echo json_encode($result);
                }
            } else echo json_encode(array('code' => 272));
        } else echo json_encode(array('code' => 374));
    }

    public function draft()
    {//发表博文
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $dra = M('draft');
        $draft['draft_id'] = base_convert(uniqid(), 16, 10);
        $draft['draft_title'] = I('title');
        $blog = I('blog');
        //$draft['author'] = I('real_name');
        $draft['cata'] = I('apartment');
        //$article['abstract']= I('synopsis');
        $draft['stud_number'] = $_SESSION['username'];
        $name = './Public/draft/' . $draft['draft_id'] . '.txt';
        $myfile = fopen("$name", "w");
        fwrite($myfile, $blog);
        fclose($myfile);
        $draft['content'] = $name;
        $draft['pub_time'] = time();

        $result = $dra->add($draft);
        if ($result) {
            $result['code'] = 1;
            echo json_encode($result);
        } else {
            $result['code'] = 0;
            echo json_encode($result);

        }
    }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }

    public function get_draft()    {      //我的草稿
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $draft = M('draft');
            $data['stud_number'] = $_SESSION['username'];
            $time = time();
            $time_ago=$time-86400*117;      //一周前的时间
            $limit['pub_time'] = array(array('gt',$time_ago),array('lt',$time)) ;
            $limit['stud_number']=$data['stud_number'];
            $count = $draft->where($limit)->count();
            if (!$count) {
                echo json_encode(array('code' => 2));
                exit();
            }

            $result['draft'] = $draft->where(array($limit))->field('draft_id,draft_title,pub_time,stud_number')->select();
            $result['count']=$count;
            $result['code'] = 1;
            echo json_encode($result);
        }
        elseif(isset($_SESSION['username'])){
            echo json_encode(array('code'=>272));
        }
        else json_encode(array('code'=>374));
    }

/*    public function get_draft()
    {
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $model = new model();
            $model->startTrans();
            $dra = M('draft');
            $id = I('id');
            $result = $dra->where("draft_id=$id")->field('draft_id,draft_title,content,pub_time,stud_number')->select();
            //echo "aaa=".$result1[0]['view_times'];
            $first = $result[0]['content'];
            $then = substr($first, 1, strlen($first) - 1);
            //echo $new['view_times'];
            $result[0]['content'] = 'http://120.77.222.115/RMS' . $then;
            $flag1 = unlink($first);
            $flag1;
            $flag2 = $dra->where("draft_id=$id")->delete();
            //echo $flag2;
            if ($flag1 && $flag2) {
                $result['code'] = 1;
                echo json_encode($result);
                $model->commit();
            } else {
                $res['code'] = 0;
                echo json_encode($res);
                $model->rollback();
            }

        } elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }*/
    public function add_face()
    {
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $username=I('username');
        $image=I('image');
        face($username,$image);
    }
        elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }
    public function get_face(){
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $username=I('username');
        $face=M('face');

        $result=$face->where("username= $username")->field('face_url')->select();
        $first = $result[0]['face_url'];
        $then = substr($first, 1, strlen($first) - 1);
        //echo $new['view_times'];
        $result[0]['content'] = 'http://120.77.222.115/QuantaRMS' . $then;
        echo json_encode($result);
    }
        elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }
    public function refresh(){
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
            $model=M('model');
            $model->startTrans();
         $face=M('face');
        $data['username']=I('username');
        $image=I('image');
        $result=$face->where($data)->field('face_url')->select();
        $first = $result[0]['content'];
        $then = substr($first, 1, strlen($first) - 1);
        //echo $new['view_times'];
        $result[0]['content'] = 'http://120.77.222.115/QuantaRMS' . $then;
        $flag1 = unlink($first);
        $flag2=face($data['username'],$image);
            if($flag1&&$flag2){
                $model->commit();
                echo json_encode(array('code'=>1));
            }
            else{
                $model->rollback();
                echo json_encode(array('code'=>0));
            }


    }
        elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }
    public function more(){
        if (isset($_SESSION['username']) && isset($_SESSION['status'])) {
        $page=I('page');
        $article = M('article');
        $count_yanfa = $article->where(array("cata" => "研发部"))->count();
        $count_yunying = $article->where(array("cata" => "运营部"))->count();
        $count_sheji = $article->where(array("cata" => "设计部"))->count();
        if(($page > ceil($count_yanfa / 13))&&($page > ceil($count_yunying / 13))&&($page > ceil($count_sheji / 13))){
            echo json_encode(array('code'=>404));
            exit();

        }
        if ($page > ceil($count_yanfa / 13)) {
            $articleInfo['yanfa_page']=0;
        }
        else{        $articleInfo['yanfa_page'] = page_num($count_yanfa,13,$page);
            $res['yanfa'] = display_cata('研发部', 13, $page);
        }
        if ($page > ceil($count_yunying / 13)) {
            $articleInfo['yunying_page']=0;
        }
        else{        $articleInfo['yunying_page'] = page_num($count_yunying,13,$page);
            $res['yunying'] = display_cata('运营部', 13,$page);
        }
        if($page > ceil($count_sheji / 13)) {
            $articleInfo['sheji_page']=0;
        }
        else{        $articleInfo['sheji_page'] = page_num($count_sheji,13,$page);
            $res['sheji'] = display_cata('设计部', 13,$page);
        }

        $res['articleInfo'] = $articleInfo;
        $res['code']=1;

        echo json_encode($res);
    }
        elseif (isset($_SESSION['username'])) {
            echo json_encode(array('code' => 272));
        } else json_encode(array('code' => 374));
    }
	
	public function manage(){
		 //成员列表
        $user=M('user');
        $search['status']=array('gt',1);
        $result['person']=$user->where($search)->field('stud_number,real_name,position,department')->select();
        //成员信息

        $result['user']=$this->show_member();
         //帖子管理
        $article = M('article');
        $result['countOfBlog'] = $article->count();
        $result['blog']= $article->select();

         //文件管理
        $file= M('file');
        $result['countOfFlie'] =$file->count();
        $result['file'] = $article->select();
        echo json_encode($result);

		
	}


}