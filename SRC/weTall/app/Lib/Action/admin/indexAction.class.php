<?php
class indexAction extends backendAction {

    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('menu');
        $this->item_order=M('item_order');
        
    }

    public function index() {
    	//dump($_SESSION);exit;
        $top_menus = $this->_mod->admin_menu(0);
        $this->assign('top_menus', $top_menus);
        $my_admin = array('username'=>$_SESSION['admin']['username'], 'rolename'=>$_SESSION['admin']['role_id']);
        $this->assign('my_admin', $my_admin);        

        $this->assign('tokenTall', $this->getTokenTall());
        $_SESSION["tokenTall"] = $this->getTokenTall();
        
        $this->assign('wxid', $_SESSION['wxid']);
        
        $token=$_SESSION["tokenTall"];
        $weshop=M("wecha_shop");
        $where["tokenTall"]=$token;
        $where2["token"]=$token;
        if (false == $weshop->where($where)->find()) {
        	$data["tokenTall"] = $token;
        	$data["headurl"] = "__PARENTURL__/tpl/User/default/common/images/portrait.jpg";
        	$data["HaveReal"] = "0";
        	$wxUser=M("wxuser")->where($where2)->find();
        	
        	$data["name"]=$wxUser["wxname"];
        	$data["weName"]=$wxUser["wxname"];
        	$data["title"]="微信商城店铺";
        	$data["descr"]="微信商城店铺";
        	$data["keywords"]="微信商城店铺";
        	$weshop->add($data);
        }
        
       
        $tokenTall = $this->getTokenTall();
        $weshopData["tokenTall"] = $tokenTall;
        $weChaShopDetail = $weshop->where($weshopData)->find();//商城基本信息var_dump($weChaShopDetail);die();
        $this->assign("weshopData",$weChaShopDetail);
        $this->display(); 
    }

    public function edithead(){
    	if(IS_POST){
    		//必须上传图片
    		if (empty($_FILES['headurl']['name'])) {
    			$this->error('请上传头像图片');
    		}
    		//上传图片
    		$date_dir = date('ym/d/'); //上传目录
    		$item_headurls = array(); //相册
    		$result = $this->_upload($_FILES['headurl'], 'item/'.$date_dir, array(
    				'width'=>C('pin_item_bimg.width').','.C('pin_item_img.width').','.C('pin_item_simg.width'),
    				'height'=>C('pin_item_bimg.height').','.C('pin_item_img.height').','.C('pin_item_simg.height'),
    				'suffix' => '_b,_m,_s',
    				//'remove_origin'=>true
    		));
    		if ($result['error']) {
    			$this->error($result['info']);
    		} else {
    			$data['headurl'] = $date_dir . $result['info'][0]['savename'];
    			//保存一份到相册
    			$item_imgs[] = array(
    					'headurl'     => $data['headurl'],
    			);
    		}
    		$datahead['headurl'] = "__PARENTURL__/weTall/data/upload/item/".$data['headurl'];
    		$datahead2['tokenTall'] = $_SESSION["tokenTall"];
    		$wshop=M("wecha_shop");
    		if($wshop->where($datahead2)->save($datahead)){
    			$this->success("头像修改成功");
    		}else{
    			$this->error("头像修改失败");
    		}
    		
    	}else{
    		$token=$this->_get("tokenTall","trim");
    		
    		$data["tokenTall"]=$token;
    		if($token != "") $_SESSION["tokenTall"] = $token;
    		$weChaShop = M("wecha_shop")->where($data)->find();
    		$this->assign("we_shop", $weChaShop);
    		
    	    if (IS_AJAX) {
    			$response = $this->fetch();
    			$this->ajaxReturn(1, '', $response);
    		} else {
    			$this->display();
    		}
    	}
    }
    public function panel() {

    	$map = array();
    	$UserDB = D('info_notice');
    	/*店铺*/
    	$weChaShop = M("wecha_shop");
    	$count = $UserDB->where($map)->count();
    	$Page       = new Page($count,8);// 实例化分页类 传入总记录数
    	// 进行分页数据查询 注意page方法的参数的前面部分是当前的页数使用 $_GET[p]获取
    	$nowPage = isset($_GET['p'])?$_GET['p']:1;
    	$show       = $Page->show();// 分页显示输出
    	$list2 = $UserDB->where($map)->order('ptime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
    	foreach ($list2 as $content){
    		if(strlen($content["content"]) > 60){
    			$content["content"] = mb_substr($content["content"], 0,33,"utf-8")."...";
    		}
    		if(strlen($content["title"]) > 10){
    			$content["title"] = mb_substr($content["title"], 0,10,"utf-8")."...";
    		}
    		$list[] = $content;
    	}
    	
    	//tax for mall
    	$tax = M("set_tax");
    	$currentTax = $tax->find();//var_dump($currentTax);die();
    	
    	$this->assign("currentTax",$currentTax);
    	$this->assign('list',$list);
    	$this->assign('page',$show);// 赋值分页输出pti

    	$tokenTall = $this->getTokenTall();
    	$weshopData["tokenTall"] = $tokenTall;
    	$weChaShopDetail = $weChaShop->where($weshopData)->find();//商城基本信息var_dump($weChaShopDetail);die();
    	$this->assign("weshopData",$weChaShopDetail);
    	$this->assign('tokenTall', $tokenTall);

    	$result2 = include './../data/conf/info.php';
    	$this->assign("weQQ",$result2["site_qq"]);
    	
        $message = array();
        if (is_dir('./install')) {
            $message[] = array(
                'type' => 'error',
                'content' => "您还没有删除 install 文件夹，出于安全的考虑，我们建议您删除 install 文件夹。",
            );
        }
        if (APP_DEBUG == true) {
            $message[] = array(
                'type' => 'error',
                'content' => "您网站的 DEBUG 没有关闭，出于安全考虑，我们建议您关闭程序 DEBUG。",
            );
        }
        if (!function_exists("curl_getinfo")) {
            $message[] = array(
                'type' => 'error',
                'content' => "系统不支持 CURL ,将无法采集商品数据。",
            );
        }
        $this->assign('message', $message);
        $system_info = array(
            'pinphp_version' => PIN_VERSION . ' RELEASE '. PIN_RELEASE .' [<a href="http://www.pinphp.com/" class="blue" target="_blank">查看最新版本</a>]',
            'server_domain' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
            'server_os' => PHP_OS,
            'web_server' => $_SERVER["SERVER_SOFTWARE"],
            'php_version' => PHP_VERSION,
            'mysql_version' => mysql_get_server_info(),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . '秒',
            'safe_mode' => (boolean) ini_get('safe_mode') ?  L('yes') : L('no'),
            'zlib' => function_exists('gzclose') ?  L('yes') : L('no'),
            'curl' => function_exists("curl_getinfo") ? L('yes') : L('no'),
            'timezone' => function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no')
        );
        $this->assign('system_info', $system_info);
        
        
        
        $buycount= M('item')->where(array('status'=>1,'tokenTall'=>$tokenTall))->count();
        $nobuycount= M('item')->where(array('status'=>0,'tokenTall'=>$tokenTall))->count();
        
        $fukuan= $this->item_order->where(array('status'=>1,'tokenTall'=>$tokenTall))->count();
        $fahuo= $this->item_order->where(array('status'=>2,'tokenTall'=>$tokenTall))->count();
        $yfahuo= $this->item_order->where(array('status'=>3,'tokenTall'=>$tokenTall))->count();
        $this->assign('count',
	        array('fukuan'=>$fukuan,
	        'fahuo'=>$fahuo,
	        'yfahuo'=>$yfahuo,
	        'buycount'=>$buycount,
	        'nobuycount'=>$nobuycount
	        )
        );
        
        $account_mod = M('account_bill_mst');
        $account_weiduizhang = $account_mod->where("status in (0,1) and tokenTall='".$tokenTall."'")->count();
        $account_where_weijie = "status != 4 and tokenTall = '".$tokenTall."'";
        $account_weijie_cnt = $account_mod->where($account_where_weijie)->count();
        $account_weijie_amt = $account_mod->where($account_where_weijie)->sum('yingjie');
        $account_weijie_amt = $account_weijie_amt ? $account_weijie_amt : 0;
        $account_where_yijie = "status = 4 and tokenTall = '".$tokenTall."'";
        $account_yijie_cnt = $account_mod->where($account_where_yijie)->count();
        $account_yijie_amt = $account_mod->where($account_where_yijie)->sum('yingjie');
        $account_yijie_amt = $account_yijie_amt ? $account_yijie_amt : 0;

        $this->assign('account_cnt',
        	array('weiduizhang'=>$account_weiduizhang,
				'weijie_cnt'=>$account_weijie_cnt,
        		'weijie_amt'=>$account_weijie_amt,
        		'yijie_cnt'=>$account_yijie_cnt,
        		'yijie_amt'=>$account_yijie_amt,
        	)
        );

        $this->display();
    }

    public function login() {
       // if (IS_POST) {
            $username = 'admin';
            $password = 'admin';
           // $verify_code = $this->_post('verify_code', 'trim');
          //  if(session('verify') != md5($verify_code)){
          //      $this->error(L('verify_code_error'));
           // }
			$where['username'] = $username;
			$where['status'] = 1;
            $admin = M('admin')->where($where)->find();
            if (!$admin) {
                $this->error(L('admin_not_exist'));
            }
            if ($admin['password'] != md5($password)) {
                $this->error(L('password_error'));
            }
            session('admin', array(
                'id' => $admin['id'],
                'role_id' => $admin['role_id'],
                'username' => $admin['username'],
            ));
            M('admin')->where(array('id'=>$admin['id']))->save(array('last_time'=>time(), 'last_ip'=>get_client_ip()));
            $tokenTall = $_SESSION["tokenTall"];
            header("location: ".__ROOT__."/index.php?g=admin&m=index&a=index&tokenTall=".$tokenTall);
        //    $this->success(L('login_success'), U('index/index'));
       // } else {
       //     $this->display();
      //  }
    }

    public function logout() {
        session('admin', null);
        session('tokenTall', null);
        $this->success(L('logout_success'), U('index/login'));
        exit;
    }

    public function verify_code() {
        Image::buildImageVerify(4,1,'gif','50','24');
    }

    public function left() {
        $menuid = $this->_request('menuid', 'intval');
       
        if ($menuid) {
            $left_menu = $this->_mod->admin_menu($menuid);
            foreach ($left_menu as $key=>$val) {
                $left_menu[$key]['sub'] = $this->_mod->admin_menu($val['id']);
            }
        } else {
            $left_menu[0] = array('id'=>0,'name'=>'商品管理');
            $left_menu[0]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>0))->select()) {
                $left_menu[0]['sub'] = $r;
            }
            
            $left_menu[1] = array('id'=>1,'name'=>'交易管理');
            $left_menu[1]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>1))->select()) {
                $left_menu[1]['sub'] = $r;
            }
            $left_menu[2] = array('id'=>2,'name'=>'广告管理');
            $left_menu[2]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>2))->select()) {
                $left_menu[2]['sub'] = $r;
            }
            /*
            $left_menu[3] = array('id'=>3,'name'=>'账务管理');
            $left_menu[3]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>3))->select()) {
            	$left_menu[3]['sub'] = $r;
            }
            */
            $left_menu[4] = array('id'=>4,'name'=>'会员管理');
            $left_menu[4]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>4))->select()) {
            	$left_menu[4]['sub'] = $r;
            }
            
            $left_menu[99] = array('id'=>99,'name'=>'店铺管理');
            $left_menu[99]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>99))->select()) {
                $left_menu[99]['sub'] = $r;
            }

            array_unshift($left_menu[0]['sub'], array('id'=>0,'name'=>'后台首页','module_name'=>'index','action_name'=>'panel'));
        }
        $this->assign('left_menu', $left_menu);
       
        $this->assign('tokenTall', $this->getTokenTall());
        $this->display();
    }

    public function often() {
        if (isset($_POST['do'])) {
            $id_arr = isset($_POST['id']) && is_array($_POST['id']) ? $_POST['id'] : '';
            $this->_mod->where(array('ofen'=>1))->save(array('often'=>0));
            $id_str = implode(',', $id_arr);
            $this->_mod->where('id IN('.$id_str.')')->save(array('often'=>1));
            $this->success(L('operation_success'));
        } else {
            $r = $this->_mod->admin_menu(0);
            $list = array();
            foreach ($r as $v) {
                $v['sub'] = $this->_mod->admin_menu($v['id']);
                foreach ($v['sub'] as $key=>$sv) {
                    $v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
                }
                $list[] = $v;
            }
            $this->assign('list', $list);
            $this->display();
        }
    }

    public function map() {
        $r = $this->_mod->admin_menu(0);
        $list = array();
        foreach ($r as $v) {
            $v['sub'] = $this->_mod->admin_menu($v['id']);
            foreach ($v['sub'] as $key=>$sv) {
                $v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
            }
            $list[] = $v;
        }
        $this->assign('list', $list);
        $this->display();
    }
}
