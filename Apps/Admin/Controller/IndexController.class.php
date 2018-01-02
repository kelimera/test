<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
      //图文分类显示

    public function index(){
      $text= M('x_dongtai');
     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     // var_dump($show);
     $arr=$text->limit($Page->firstRow.','.$Page->listRows)->query("SELECT * FROM `x-lanmu` join x_dongtai ON `x-lanmu`.id=x_lanmu");
     $this->assign('arr',$arr);
     $this->assign('page',$show);

      $this->display();
    }

    
         //查找此ID分类等级 
function adm_products_jis($gcid){
    $sql = "select x_cid from x_dongtai where 1=1 and id={$gcid}";
    $rs = mysql_query($sql);
    $i = 1;
    if($rs){
        $row=mysql_fetch_assoc($rs);
        if($row["x_cid"]<>0){
            $i = $i + $this->adm_products_jis($row["x_cid"]);
        }
    }
    return $i;
}



    //循环分类 $pid代表父级ID $tid修改时上级分类的值
function adm_goods_class_selects($pid,$tid){
$sql = "select id,x_title from x_dongtai where 1=1 and x_cid={$pid} order by id asc";     
$rs = mysql_query($sql);


$str="";
if($rs){
  while($rows = mysql_fetch_assoc($rs)){
            $gang = "";
            for($i=0;$i<$this->adm_products_jis($rows["id"]);$i++){
                $gang = $gang."-";
            }
            $type_id=$rows['id']; 
            $type_title=$rows['x_title'];
            if($tid==$type_id){
                //分匹配到模板中进行显示
                $str.='<option selected="selected" value='.$type_id.'>'.$gang.$type_title.'</option>';
            }else{
                $str.='<option value='.$type_id.'>'.$gang.$type_title.'</option>';
            }
            // $str.='<option value='.$type_id.'>'.$gang.$type_name.'</option>';
            $str.=$this->adm_goods_class_selects($type_id,$tid);  

  }
 }
return $str;
}



   
    //图文分类添加
    public function add(){

    	$text = M('x_dongtai');
    	$text = M('x-lanmu');
        $voll=$text->where('l_type=2')->select();

        $result2=$this->adm_goods_class_selects(0,$tid);
        $this->assign('result2',$result2);

        $this->assign('voll',$voll);
        $this->display();
    }

    public function adds(){
    	$text = M ('x_dongtai');
    	$data['x_title']=I('x_title');                                                 
        $data['x_lanmu']=I('x_lanmu');
        $data['x_time']=date('Y-m-d H:i:s',time());
        $data['x_cid']=I('x_cid');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/index'));
      }else{
         $this->alert_back('失败');
       }

    }

     // 图文分类删除

    public function delete(){
    	$id=I('id');
    	$text = M('x_dongtai');
        $text->create();
        $delete=$text->where("id=$id")->delete();
       if($delete){
      $this->alert_location('删除成功',U('Index/index'));
      }else{
         $this->alert_back('失败');
       }

    }

     
   // 图文分类修改

    public function edit(){
       $text= M('x-lanmu');
        $voll=$text->select();
        $id=$_GET['id'];
        $text= M('x_dongtai');
        $result=$text->find($id);
        // print_r($result);
        // exit;
        $x_title=$result['x_title'];
        $x_time=$result['x_time'];
        $x_cid=$result['x_cid'];
        $x_lanmu=$result['x_lanmu'];
        $result2=$this->adm_goods_class_selects(0,$x_cid);
        $this->assign('result2',$result2);
        $this->assign('id',$id);
        $this->assign('x_title',$x_title);
        $this->assign('x_time',$x_time); 
        $this->assign('x_cid',$x_cid);
         $this->assign('x_lanmu',$x_lanmu);
        $this->assign('voll',$voll); 
        $this->display();
       }

       
    public function save(){
	    $id=$_GET['id'];
	    $text= M('x_dongtai');
	      $data['x_title']=I('x_title');                                                 
	      $data['x_lanmu']=I('x_lanmu');
	      $data['x_time']=date('Y-m-d H:i:s',time());
          $data['x_cid']=I('x_cid');
	    if ($text->where("id=$id")->save($data)) {
	      $this->alert_location('修改成功',U('Index/index'));

	    }else{
	      $this->alert_back('失败');
	    }


   }

    

    // 栏目显示
   public function indext(){

   	 $text= M('x-lanmu');
     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     // var_dump($show);
     $list = $text->order('l_num')->limit($Page->firstRow.','.$Page->listRows)->select();
     $this->assign('arr',$list);
     $this->assign('page',$show);

      $this->display();

   }

  
     // 添加栏目
     public function l_add(){
    	$text = M('x-lanmu');
    	$text->create();
    	$this->display();
    }

    public function l_adds(){
    	$text = M ('x-lanmu');
    	$data['l_title']=I('l_title');                                                 
        $data['l_time']=date('Y-m-d H:i:s',time());
        $data['l_num']=I('l_num');
        $data['l_lianji']=I('l_lianji');
        $data['l_type']=I('l_type');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/indext'));
      }else{
         $this->alert_back('失败');
       }

    }
     
    // 修改栏目
    public function l_edit(){
        $id=$_GET['id'];
        $text= M('x-lanmu');
        $result=$text->find($id);
        $l_title=$result['l_title'];
        $l_num=$result['l_num'];
        $l_time=$result['l_time'];
        $l_lianji=$result['l_lianji'];
        $l_type=$result['l_type'];
        $this->assign('id',$id);
        $this->assign('l_title',$l_title);
        $this->assign('l_num',$l_num);
        $this->assign('l_content',$l_content); 
        $this->assign('l_time',$l_time);
         $this->assign('l_lianji',$l_lianji);
         $this->assign('l_type',$l_type);  
        $this->display();
       }

       
    public function l_save(){
	    $id=$_GET['id'];
	    $text= M('x-lanmu');
	      $data['l_title']=I('l_title');                                                 
	      $data['l_num']=I('l_num');
	      $data['l_time']=date('Y-m-d H:i:s',time());
          $data['l_lianji']=I('l_lianji');
          $data['l_type']=I('l_type');
	    if ($text->where("id=$id")->save($data)) {
	      $this->alert_location('修改成功',U('Index/indext'));

	    }else{
	      $this->alert_back('失败');
	    }


   }



       // 删除栏目

        public function deletel(){
	    	$id=I('id');
	    	$text = M('x-lanmu');
	        $text->create();
	        $delete=$text->where("id=$id")->delete();
	       if($delete){
	      $this->alert_location('删除成功',U('Index/indext'));
	      }else{
	         $this->alert_back('失败');
	       }

    }



    // 栏目的显示隐藏
    public function show(){
        $id=I('id');
        $text = M('x-lanmu');
        $l_display= $text->where("id=$id")->getField('l_display');
      
      
      if($l_display==1){
        $data['l_display']="0";
      }else{
        $data['l_display']="1";
      }
      
       
      $text->where("id=$id")->save($data);
      $this->alert_location('成功',U('indext'));
    }



    public function news_display(){
        $id=I('id');
        $text = M('news_list');
        $about_display= $text->where("about_id=$id")->getField('about_display');
      
      
      if($about_display==1){
        $data['about_display']="0";
      }else{
        $data['about_display']="1";
      }
      
       
      if($text->where("about_id=$id")->save($data)){

      $this->alert_location('成功',U('news_list'));
    }else{
        $this->alert_back('失败');
    }
    }
     

   // 新闻分类显示
     public function fenlei(){
    
     $text= M('x-dongtai_type');
     $text=new \THink\Model();
     $arr=$text->query("SELECT * FROM `x-lanmu` join `x-dongtai_type` ON `x-lanmu`.id=type_lanmu");

     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     // var_dump($show);
     $fenlei = $text->limit($Page->firstRow.','.$Page->listRows)->select();
     $this->assign('fenlei',$fenlei);
     $this->assign('page',$show);
    $this->assign('arr',$arr);
      $this->display();

     }
      
    //查找此ID分类等级 
function adm_products_ji($gcid){
    $sql = "select type_sid from `x-dongtai_type` where 1=1 and type_id={$gcid}";
    $rs = mysql_query($sql);
    $i = 1;
    if($rs){
        $row=mysql_fetch_assoc($rs);
        if($row["type_sid"]<>0){
            $i = $i + $this->adm_products_ji($row["type_sid"]);
        }
    }
    return $i;
}
//循环分类 $pid代表父级ID $tid修改时上级分类的值
function adm_goods_class_select($pid,$tid){
$sql = "select type_id,type_title from `x-dongtai_type` where 1=1 and type_sid={$pid} order by type_id asc";     
$rs = mysql_query($sql);


$str="";
if($rs){
  while($rows = mysql_fetch_assoc($rs)){
            $gang = "";
            for($i=0;$i<$this->adm_products_ji($rows["type_id"]);$i++){
                $gang = $gang."-";
            }
            $type_id=$rows['type_id']; 
            $type_title=$rows['type_title'];
            if($tid==$type_id){
                //分匹配到模板中进行显示
                $str.='<option selected="selected" value='.$type_id.'>'.$gang.$type_title.'</option>';
            }else{
                $str.='<option value='.$type_id.'>'.$gang.$type_title.'</option>';
            }
            // $str.='<option value='.$type_id.'>'.$gang.$type_name.'</option>';
            $str.=$this->adm_goods_class_select($type_id,$tid);  

  }
 }
return $str;
}







       // 新闻分类增加
      public function type_add(){
        
        $text = M('x-lanmu');
        $vo=$text->where("l_type=1")->select();

        $result1=$this->adm_goods_class_select(0,$tid);
        $this->assign('result1',$result1);

        $this->assign('vo',$vo);
        $this->display();


      }


      public function type_adds(){

        $text = M ('x-dongtai_type');
        $data['type_title']=I('type_title');
        $data['type_lanmu']=I('type_lanmu');
        $data['type_time']=date('Y-m-d H:i:s',time());
        $data['type_sid']=I('type_sid');
        $data['type_content']=I('type_content');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/fenlei'));
      }else{
         $this->alert_back('失败');
       }
      }
   
    
   // 新闻分类删除
    public function type_delete(){
            $id=I('id');
            $text = M('x-dongtai_type');
            $deletes=$text->where("type_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/fenlei'));
          }else{
             $this->alert_back('失败');
           }

    }


   
   // 新闻分类修改
    public function type_edit(){
        $text= M('x-lanmu');
        $vol=$text->select();
        // print_r($vol);
        // exit;
        $id=$_GET['id'];
        $text= M('x-dongtai_type');
        $result=$text->find($id);
        // print_r($result);
        // exit;
        $type_title=$result['type_title'];
        $type_time=$result['type_time'];
        $type_sid=$result['type_sid'];
        $type_lanmu=$result['type_lanmu'];
        $type_content=$result['type_content'];
        $result1=$this->adm_goods_class_select(0,$type_sid);
        $this->assign('result1',$result1);
        $this->assign('id',$id);
        $this->assign('type_title',$type_title);
        $this->assign('type_time',$type_time); 
        $this->assign('type_sid',$type_sid);
         $this->assign('type_lanmu',$type_lanmu);
         $this->assign('type_content',$type_content);
        $this->assign('vol',$vol); 
        $this->display();

    }


    public function type_save(){
        $id=$_GET['id'];
        $text= M('x-dongtai_type');
        $data['type_title']=I('type_title');                                                 
        $data['type_lanmu']=I('type_lanmu');
        $data['type_time']=date('Y-m-d H:i:s',time());
        $data['type_sid']=I('type_sid');
        $data['type_content']=I('type_content');
        if ($text->where("type_id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/fenlei'));

        }else{
          $this->alert_back('失败');
        }

   
   }
   

   // 单页显示
    public function about(){
    
     $text= M('about');

     $text=new \THink\Model();
     $arr=$text->query("SELECT * FROM `x-lanmu` join about ON `x-lanmu`.id=about_lanmu");

     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     // var_dump($show);
     $ar = $text->limit($Page->firstRow.','.$Page->listRows)->select();
      $this->assign('ar',$ar);
     $this->assign('arr',$arr);
     $this->assign('page',$show);
   
      $this->display();

     }
    
     
     // 单页添加

    public function about_add(){
        $text = M('about');
        $text= M('x-lanmu');
        $vo=$text->where("l_type=0")->select();

        // $result1=$this->adm_goods_class_select(0,$tid);
        // $this->assign('result1',$result1);

        $this->assign('vo',$vo);
        $this->display();



      }


       public function about_adds(){

        $text = M ('about');
        $data['about_title']=I('about_title');
        $data['about_lanmu']=I('about_lanmu');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_content']=I('about_content');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/about'));
      }else{
         $this->alert_back('失败');
       }
      }
      


     // 单页修改

       public function about_edit(){
        $text= M('x-lanmu');
        $vol=$text->where("l_type=0")->select();
        // print_r($vol);
        // exit;
        $id=$_GET['id'];
        $text= M('about');
        $result=$text->find($id);
        // print_r($result);
        // exit;
        $about_title=$result['about_title'];
        $about_time=$result['about_time'];
        $about_content=$result['about_content'];
        $about_lanmu=$result['about_lanmu'];
        // $result1=$this->adm_goods_class_select(0,$type_sid);
        // $this->assign('result1',$result1);
        $this->assign('id',$id);
        $this->assign('about_title',$about_title);
        $this->assign('type_time',$type_time); 
        $this->assign('about_content',$about_content);
         $this->assign('about_lanmu',$about_lanmu);
        $this->assign('vol',$vol); 
        $this->display();

    }




      public function about_save(){
        $id=$_GET['id'];
        $text= M('about');
        $data['about_title']=I('about_title');                                                 
        $data['about_lanmu']=I('about_lanmu');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_content']=I('about_content');
        if ($text->where("about_id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/about'));

        }else{
          $this->alert_back('失败');
        }

   
   }


  // 单页删除
    public function about_delete(){
            $id=I('id');
            $text = M('about');
            $deletes=$text->where("about_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/about'));
          }else{
             $this->alert_back('失败');
           }

    }


    



      

      // 新闻列表显示
      public function news_list(){
    
     $text= M('news_list');
     $text=new \THink\Model();
   
     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     // var_dump($show);
     $arr=$text->limit($Page->firstRow.','.$Page->listRows)->query("SELECT * FROM `x-dongtai_type` join news_list ON `x-dongtai_type`.type_id=about_sid");
     $this->assign('arr',$arr);
     $this->assign('page',$show);
   
      $this->display();

     }

    // 添加新闻
    
     public function news_add(){
        $text= M('news_list');
        $result3=$this->adm_goods_class_select(0,$tid);
        $this->assign('result3',$result3);
        $this->assign('vo',$vo);
        $this->display();


      }


        public function news_adds(){

        $text = M ('news_list');
        $data['about_title']=I('about_title');
        $data['about_content']=I('about_content');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_sid']=I('about_sid');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/news_list'));
      }else{
         $this->alert_back('失败');
       }
      }

      
     // 修改新闻
       public function news_edit(){
        $id=$_GET['id'];
        $text= M('news_list');
        $result=$text->find($id);
        $about_title=$result['about_title'];
        $about_time=$result['about_time'];
        $about_sid=$result['about_sid'];
        $about_content=$result['about_content'];
        $result3=$this->adm_goods_class_select(0,$about_sid);
        $this->assign('result3',$result3);
        $this->assign('id',$id);
        $this->assign('about_title',$about_title);
        $this->assign('about_time',$about_time); 
        $this->assign('about_sid',$about_sid);
         $this->assign('about_content',$about_content);
        $this->display();

    }

 
    public function news_save(){
        $id=$_GET['id'];
        $text= M('news_list');
        $data['about_title']=I('about_title');                                                 
        $data['about_content']=I('about_content');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_sid']=I('about_sid');
        if ($text->where("about_id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/news_list'));

        }else{
          $this->alert_back('失败');
        }

   
   }
    

    // 新闻删除
     public function news_delete(){
            $id=I('id');
            $text = M('news_list');
            $deletes=$text->where("about_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/news_list'));
          }else{
             $this->alert_back('失败');
           }

    }




      // 图文列表显示
      public function product_list(){
    
     $text= M('product_list');
   

     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     // var_dump($show);
     $arr=$text->query("SELECT * FROM x_dongtai join product_list ON `x_dongtai`.id=about_sid");
     $this->assign('arr',$arr);
     $this->assign('page',$show);  
      $this->display();

     }
     


    // 添加图文
    
     public function product_add(){
        $text= M('product_list');
        $result2=$this->adm_goods_class_selects(0,$tid);
        $this->assign('result2',$result2);
        $this->assign('vo',$vo);
        $this->display();


      }


        public function product_adds(){
     
        $text = M ('product_list');
        $upload=$this->upload();
        $data['about_title']=I('about_title');
        $data['about_money']=I('about_money');
        $data['about_price']=I('about_price');
        $data['about_order']=I('about_order');
        $data['about_stock']=I('about_stock');
        $data['about_content']=I('about_content');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_sid']=I('about_sid');
        $data['about_img']=$upload;
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/product_list'));
      }else{
         $this->alert_back('失败');
       }
      }




      //上传图片类
  public function upload(){
  $upload = new \Think\Upload();
  $upload->maxSize = 3145728 ;
  $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
  $upload->savePath ='./Uploads/'; 
  $info   =   $upload->upload();
  if(!$info) {// 上传错误提示错误信息
      $this->alert_back($upload->getError());
   }else{// 上传成功 获取上传文件信息
      foreach($info as $file){
      return $file['savepath'].$file['savename'];
         }
       }

}



 // 修改图文
       public function product_edit(){
        $id=$_GET['id'];
        $text= M('product_list');
        $result=$text->find($id);
        $about_title=$result['about_title'];
        $about_money=$result['about_money'];
        $about_price=$result['about_price'];
        $about_order=$result['about_order'];
        $about_stock=$result['about_stock'];
        $about_time=$result['about_time'];
        $about_sid=$result['about_sid'];
        $about_content=$result['about_content'];
        $about_img=$result['about_img'];
        $result2=$this->adm_goods_class_selects(0,$about_sid);
        $this->assign('result2',$result2);
        $this->assign('id',$id);
        $this->assign('about_title',$about_title);
        $this->assign('about_money',$about_money);
        $this->assign('about_price',$about_price);
        $this->assign('about_order',$about_order);
        $this->assign('about_stock',$about_stock);
        $this->assign('about_time',$about_time); 
        $this->assign('about_sid',$about_sid);
         $this->assign('about_content',$about_content);
         $this->assign('about_img',$about_img);
        $this->display();

    }

 
    public function product_save(){
        $id=$_GET['id'];
        $text= M('product_list');
         $upload=$this->upload();
        $data['about_title']=I('about_title');
        $data['about_money']=I('about_money'); 
        $data['about_price']=I('about_price'); 
        $data['about_order']=I('about_order');
        $data['about_stock']=I('about_stock');                                                
        $data['about_content']=I('about_content');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_sid']=I('about_sid');
         $data['about_img']=$upload;
        if ($text->where("about_id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/product_list'));

        }else{
          $this->alert_back('失败');
        }

   
   }


     // 图文删除
     public function product_delete(){
            $id=I('id');
            $text = M('product_list');
            $deletes=$text->where("about_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/product_list'));
          }else{
             $this->alert_back('失败');
           }

    }




      public function gbook(){
         $text= M('pinglun');
         $count = $text->count();
         $Page = new \Think\Page($count,8);
         // var_dump($count);

        $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
        $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
        $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
        $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
        //分页样式

         $show = $Page->show();
         // var_dump($show);
         $list = $text->limit($Page->firstRow.','.$Page->listRows)->select();
         $this->assign('arr',$list);
         $this->assign('page',$show);

      $this->display();
    }



      public function gbook_edit(){

        $id=$_GET['id'];
        $text= M('pinglun');
        $result=$text->find($id);

        $p_user=$result['p_user'];
        $p_email=$result['p_email'];
        $p_content=$result['p_content'];
        $p_text=$result['p_text'];
        $this->assign('id',$id);
        $this->assign('p_user',$p_user);
        $this->assign('p_email',$p_email);   
        $this->assign('p_content',$p_content);
        $this->assign('p_text',$p_text);
        $this->display();


      }


      public function gbook_save(){
        $id=$_GET['id'];
        $text= M('pinglun');
        $data['p_user']=I('p_user');                                                 
        $data['p_email']=I('p_email');
        $data['p_content']=I('p_content');
        $data['p_text']=I('p_text');
        if ($text->where("p_id=$id")->save($data)) {
          $this->alert_location('回复成功！',U('Index/gbook'));

        }else{
          $this->alert_back('失败');
        }

   
   }



      public function accept(){

         $text= M('accept');
         $count = $text->count();
         $Page = new \Think\Page($count,8);
         // var_dump($count);

        $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
        $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
        $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
        $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
        //分页样式

         $show = $Page->show();
         // var_dump($show);
         $list = $text->limit($Page->firstRow.','.$Page->listRows)->select();
         $this->assign('arr',$list);
         $this->assign('page',$show);
          $this->display();

   }


      

       public function accept_add(){
        $text = M('accept');
        $this->display();
      }


       public function accept_adds(){

        $text = M ('accept');
        $data['about_title']=I('about_title');
        $data['about_num']=I('about_num');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_require']=I('about_require');
        $data['about_content']=I('about_content');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/accept'));
      }else{
         $this->alert_back('失败');
       }
      }

       public function accept_edit(){
        $id=$_GET['id'];
        $text= M('accept');
        $result=$text->find($id);
        $about_title=$result['about_title'];
        $about_time=$result['about_time'];
        $about_content=$result['about_content'];
        $about_require=$result['about_require'];
        $about_num=$result['about_num'];
        $this->assign('id',$id);
        $this->assign('about_title',$about_title);
        $this->assign('type_time',$type_time); 
        $this->assign('about_content',$about_content);
         $this->assign('about_require',$about_require);
        $this->assign('about_num',$about_num); 
        $this->display();

    }




      public function accept_save(){
        $id=$_GET['id'];
        var_dump($id);
        $text= M('accept');
        $data['about_title']=I('about_title');                                                 
        $data['about_num']=I('about_num');
        $data['about_time']=date('Y-m-d H:i:s',time());
        $data['about_require']=I('about_require');
        $data['about_content']=I('about_content');
        if ($text->where("about_id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/accept'));

        }else{
          $this->alert_back('失败');
        }

   
   }
    

   
     public function accept_deletel(){
            $id=I('id');
            $text = M('accept');
            $deletes=$text->where("about_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/accept'));
          }else{
             $this->alert_back('失败');
           }

    }


      public function resume(){

         $text= M('jianli');
         $count = $text->count();
         $Page = new \Think\Page($count,8);

        $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
        $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
        $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
        $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
        //分页样式

         $show = $Page->show();
         // var_dump($show);
         $list = $text->limit($Page->firstRow.','.$Page->listRows)->select();
         $this->assign('arr',$list);
         $this->assign('page',$show);
         $this->display();

   }

     // 简历
   public function biaoge(){
    $id=I('id');
    $text = M ('jianli');
    $arr=$text->where("zp_id=$id")->find();
    $this->assign('arr',$arr);
    $this->display();
   }


        public function resume_deletel(){
            $id=I('id');
            $text = M('jianli');
            $deletes=$text->where("zp_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/resume'));
          }else{
             $this->alert_back('失败');
           }

    }

    


    // 轮播图

       public function lunbo(){

         $text= M('lunbo');
         $count = $text->count();
         $Page = new \Think\Page($count,8);

         $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
         $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
         $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
         $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
        //分页样式

         $show = $Page->show();
         // var_dump($show);
         $list = $text->limit($Page->firstRow.','.$Page->listRows)->select();
         $this->assign('arr',$list);
         $this->assign('page',$show);
         $this->display();

       }



     public function lun_add(){
        $text = M('accept');
        $this->display();
      }


       public function lun_adds(){

        $text = M ('lunbo');
        $upload=$this->upload();
        $data['lun_img']=$upload;
        $data['lun_time']=date('Y-m-d H:i:s',time());
        $data['lun_display']=I('lun_display');
         if($text->add($data)){
      $this->alert_location('添加成功',U('Index/lunbo'));
      }else{
         $this->alert_back('失败');
       }
      }
      

    // 轮播图显示隐藏
    public function lun_display(){
        $id=I('id');
        $text = M('lunbo');
        $lun_display= $text->where("lun_id=$id")->getField('lun_display');   
      if($lun_display==1){
        $data['lun_display']="0";
      }else{
        $data['lun_display']="1";
      }
      
       
      if($text->where("lun_id=$id")->save($data)){

      $this->alert_location('成功',U('lunbo'));
    }else{
        $this->alert_back('失败');
    }
    }


    // 修改轮播图
       public function lun_edit(){
        $id=$_GET['id'];
        $text= M('lunbo');
        $result=$text->find($id);
        $lum_time=$result['lun_time'];
        $lun_img=$result['lun_img'];
        $this->assign('id',$id);
        $this->assign('lun_time',$lun_time); 
        $this->assign('lun_img',$lun_img);
        $this->display();

    }

 
    public function lun_save(){
        $id=$_GET['id'];
        $text= M('lunbo');
        $upload=$this->upload();                                              
        $data['lun_time']=date('Y-m-d H:i:s',time());
        $data['lun_img']=$upload;
        if ($text->where("lun_id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/lunbo'));

        }else{
          $this->alert_back('失败');
        }

   
   }


     // 轮播删除
     public function lun_delete(){
            $id=I('id');
            $text = M('lunbo');
            $deletes=$text->where("lun_id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/lunbo'));
          }else{
             $this->alert_back('失败');
           }

    }

    

           public function login(){

         $text= M('lc_member');
         $count = $text->count();
         $Page = new \Think\Page($count,8);

         $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
         $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
         $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
         $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
        //分页样式

         $show = $Page->show();
         // var_dump($show);
         $list = $text->limit($Page->firstRow.','.$Page->listRows)->select();
         $this->assign('arr',$list);
         $this->assign('page',$show);
         $this->display();

       }
     

       // 用户注册
       public function login_add(){
        $text = M ('login');
        $this->display();
       }

     
      public function login_adds(){
        $text = M ('login');
        $data['username']=I('username');
        $data['password']=md5(I('password'));
        $data['tel']=I('tel');
        $data['email']=I('email');
        $data['time']=date('Y-m-d H:i:s',time());

       if($text->add($data)){
            $this->alert_location('添加成功',U('Index/login'));
         }else{
             $this->alert_back('失败');
       }

      }


       public function login_edit(){
        $id=$_GET['id'];
        $text= M('login');
        $result=$text->find($id);
        $username=$result['username'];
        $password=$result['password'];
        $tel=$result['tel'];
        $email=$result['email'];
        $this->assign('id',$id);
        $this->assign('username',$username); 
        $this->assign('password',$password);
        $this->assign('tel',$tel);
        $this->assign('email',$email);
        $this->display();

    }

      public function login_save(){
        $id=$_GET['id'];;
        $text = M('login');
        $data['username']=I('username');
        $data['password']=md5(I('password'));
        $data['tel']=I('tel');
        $data['email']=I('email');
        $data['time']=date('Y-m-d H:i:s',time());
        if ($text->where("id=$id")->save($data)) {
          $this->alert_location('修改成功',U('Index/login'));

        }else{
          $this->alert_back('失败');
        }

   
   }


     public function login_delete(){
            $id=I('id');
            $text = M('login');
            $deletes=$text->where("id=$id")->delete();
           if($deletes){
          $this->alert_location('删除成功',U('Index/login'));
          }else{
             $this->alert_back('失败');
           }

    }



    public function order(){
     $d_statime=I('d_statime')." 00:00:00";
     $d_overtime=I('d_overtime')." 23:59:59";
     $seach = I('seach');
     $text= M('order');
     $count = $text->count();
     $Page = new \Think\Page($count,8);
     // var_dump($count);

    $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
    $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
    $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
    $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

     $show = $Page->show();
     $wheresql="1=1 ";                     // 注意此处有空格
     if (I('d_overtime')!="" && I('d_statime')!="") {
       $wheresql.="and d_time between '{$d_statime}' and '{$d_overtime}'";
     }
      if ($seach!="") {
       $wheresql.=" and d_number LIKE '%".$seach."%'";
     }
    $arr = $text->where($wheresql)->limit($Page->firstRow.','.$Page->listRows)->select();
   // $arr = $text->where("d_time between '{$d_statime}' and '{$d_overtime}' and d_number LIKE '%{$seach}%' ")->limit($Page->firstRow.','.$Page->listRows)->select();
   //$arr = $text->where("d_time >= '{$d_statime}' and d_time <= '{$d_overtime}'")->limit($Page->firstRow.','.$Page->listRows)->select();
     $this->assign('arr',$arr);
     $this->assign('page',$show);
     $this->display();
    }


    
    public function shop(){
      $d_num = I('id');
      $text= M('order_shop');
      $count = $text->count();
      $Page = new \Think\Page($count,8);
     // var_dump($count);

      $Page->setConfig('prev',"<span class='glyphicon glyphicon-backward' style='color:#f51'>上一页</span>");
      $Page->setConfig('next',"<span style='color:#f51'> 下一页<span class='glyphicon glyphicon-forward'style='color:f51'></span></span>");
      $Page->setConfig('header',"<span style='color:#888' > /当前是第%NOW_PAGE%页 /共%TOTAL_PAGE%页/ 共%TOTAL_ROW%数据</span>");
      $Page->setConfig('theme','%UP_PAGE%%FIRST%%LINK_PAGE%%END%%DOWN_PAGE%%HEADER%');
    //分页样式

      $show = $Page->show();
     // var_dump($show);
      $arr = $text->where("d_shophao=$d_num")->limit($Page->firstRow.','.$Page->listRows)->select();
     // echo "<pre>";
     // print_r($arr);
     // echo "</pre>";        
     //  exit();
      $this->assign('arr',$arr);
      $this->assign('page',$show);
      $this->display();
    }
   



}