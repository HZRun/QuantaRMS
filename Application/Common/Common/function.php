<?php
 function display_cata($cata,$num,$page=1){    //根据分类显示帖子函数，
$arti=M('article');
$result=$arti->where(array("cata"=>$cata))->order("pub_time desc")->limit($num*($page-1),$num)
      ->field('cata,article_id,article_title,abstract,author,view_times,pub_time,is_well')->select();
     for($i=0;$i<$num;$i=$i+1){
         $result[$i]['pub_time']=date('Y/m/d',$result[$i]['pub_time']);
     }
return $result;
}
 function display1($orderby,$num,$page=1){              // 普通显示帖子函数,可根据浏览量，时间
$arti=M('article');
$result=$arti->order("$orderby desc")->limit($num*($page-1),$num)
    ->field('article_id,cata,article_title,abstract,author,view_times,pub_time,is_well')->select();
     for($i=0;$i<$num;$i=$i+1){
         $result[$i]['pub_time']=date('Y/m/d',$result[$i]['pub_time']);
     }
return $result;
}
function display_user($user,$num,$page){
    $arti=M('article');
    $result=$arti->where(array("stud_number"=>$user))->limit($num*($page-1),$num)
        ->field('article_id,cata,article_title,abstract,author,view_times,pub_time,is_well')->select();
    for($i=0;$i<$num;$i=$i+1){
        $result[$i]['pub_time']=date('Y/m/d',$result[$i]['pub_time']);
    }
    return $result;
}
function display_well($orderby='pub_time',$num,$page){
    $arti=M('article');
    $result=$arti->where(array("is_well"=>1))->order("$orderby desc")->limit($num*($page-1),$num)
        ->field('article_id,cata,article_title,abstract,author,view_times,pub_time,is_well')->select();
    for($i=0;$i<$num;$i=$i+1){
        $result[$i]['pub_time']=date('Y/m/d',$result[$i]['pub_time']);
    }
    return $result;
}

function display_title($keyword,$num=7,$page){
    $arti=M('article');
    $search['article_title'] = array('like',"%$keyword%");
    //$testtt['author']=array('like',"$author%");
    $result=$arti->where($search)->order("pub_time desc")->limit($num*($page-1),$num)->field('article_id,article_title,abstract,author,view_times,pub_time,is_well,cata')->select();
    for($i=0;$i<$num;$i=$i+1){
        $result[$i]['pub_time']=date('Y/m/d',$result[$i]['pub_time']);
    }
    return $result;

}

function getfile($num,$page){
    $file=M('file');
    $result=$file->order("pub_time desc")->limit($num*($page-1),$num)->field('file_id,file_own,url,pub_time,size,title')->select();
    return $result;
}
function myfile($usename,$num,$page){
    $file=M('file');
    $result=$file->where(array('file_own' => $usename))->order("pub_time desc")->limit($num*($page-1),$num)->field('file_id,file_own,url,pub_time,size')->select();
    return $result;
}


 function home1(){
$res1=display_cata('研发部','pub_time' ,7 ,1);
$res2=display_cata('运营部','pub_time' ,7 );
$res3=display_cata('设计部','pub_time' ,7 );
return $res=array_merge($res1,$res2,$res3);

}
 function home2(){
return display1('view_times',5);
}
 function  home3()
 {
     $arti = M('article');
     return $result = $arti->where(array('is_well' => 1))->order('pub_time desc')->limit(4)->select();
 }


function page_num($count,$perpage=7,$page=1){
if($count){
    $last_page=ceil($count/$perpage);
    if($page==$last_page){
        $page_num=$count%$perpage;
    }
    else $page_num=$perpage;
    return $page_num;
}
else return 0;
}
function face($username,$image){

    //$base64_image_content ="data:image/png;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAQAQAAAAAAAAAAAAAAAAAAAAAAAB9SR//fUkf/31JH/99SR//fUkf/31JH/99SR//fUkf/31JH/99SR//fUkf/6mHbP+LXDf/fUkf/31JH/99SR//fUkf/31JH/99SR//fUkf/31JH/99SR//i104/5lwT/+RZkP/fksh/6eFaP/8/Pv/mG9N/31JH/99SR//fUkf/31JH/99SR//fUkf/31JH/+tjHL/6uHb//7+/v////////////Xx7v/8+/r//////6N/Yv99SR//fUkf/31JH/99SR//fUkf/35LIf/PvK3///////////////////////////////////////////+vj3b/fUkf/31JH/99SR//fUkf/31JH/++pI/////////////08Oz/vqSQ/8y3p///////////////////////u6CK/31JH/99SR//fUkf/31JH/+IWDL/+vn3///////s5d//iVo1/6B7XP/6+ff/8Orl/9TDtv+5nYb/nXZX/4NRKf9+SyL/fUkf/31JH/99SR//sJF3////////////onxe/35LIv+ge1z/ils1/31JH/99SR//fUkf/6qIbf/dz8T/1MK0/31JH/99SR//fUkf/8WunP///////Pv7/39MIv99SR//fUkf/31JH/99SR//fUkf/31JH//ay7///////+ri2/99SR//fUkf/31JH//Frpv///////38+/9/TCP/fUkf/31JH/99SR//fUkf/31JH/99SR//2szA///////q4dv/fUkf/31JH/99SR//r491////////////pIBi/31JH/99SR//fUkf/31JH/99SR//hVUt//j29P//////1MK1/31JH/99SR//fUkf/4dXMP/59/b//////+7o4/+MXjn/fUkf/31JH/99SR//gE0k/9XFuP///////////6aDZ/99SR//fUkf/31JH/99SR//up+I////////////9vPw/8OrmP+si3D/uZ2G/+ri2////////////97Rx/99SiD/fUkf/31JH/99SR//fUkf/31KIP/KtqX//v7+/////////////////////////////////+Xb0/+HWDH/fUkf/31JH/99SR//fUkf/31JH/99SR//fUkf/6eFaP/l2tL//v7+////////////8evn/7yhi/+BTyb/fUkf/31JH/99SR//fUkf/31JH/99SR//fUkf/31JH/99SR//fUkf/4dXMP+Uakf/jV86/31JH/99SR//fUkf/31JH/99SR//fUkf/31JH/9/SyH/f0sh/39LIf9/SyH/f0sh/39LIf9/SyH/f0sh/39LIf9/SyH/f0sh/39LIf9/SyH/f0sh/39LIf9/SyH/AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==";
    $base64_image_content=$image;
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
        var_dump($result);
        $type = $result[2];
        $new_file = "./face/$username.$type";
        if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
            $face=M('face');
            echo '新文件保存成功：', $new_file;
            $data['username']=$username;
            $data['face_url']=$new_file;
            $face->add($data);
            return 1;
        }
        else  return 0;
    }
    else return 0;
}

function formatSizeUnits($bytes)
{
    if ($bytes >= 1073741824)
    {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    }
    elseif ($bytes >= 1048576)
    {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    }
    elseif ($bytes >= 1024)
    {
        $bytes = number_format($bytes / 1024, 2) . ' kB';
    }
    elseif ($bytes > 1)
    {
        $bytes = $bytes . ' bytes';
    }
    elseif ($bytes == 1)
    {
        $bytes = $bytes . ' byte';
    }
    else
    {
        $bytes = '0 bytes';
    }
    return $bytes;
}
function download_file($file){
    if(is_file($file)){
        $length = filesize($file);
        $type = mime_content_type($file);
        $showname =  ltrim(strrchr($file,'/'),'/');
        header("Content-Description: File Transfer");
        header('Content-type: ' . $type);
        header('Content-Length:' . $length);
        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) { //for IE
            header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $showname . '"');
        }
        readfile($file);
        return 1;
    } else {
        return 0;
    }
}
