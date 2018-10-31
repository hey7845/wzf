<?php
class IndexAction extends CommonAction {
	// 框架首页
	public function index() {
		ob_clean();
		$this->_checkUser();
		$this->_Config_name();//调用参数
		C ( 'SHOW_RUN_TIME', false ); // 运行时间显示
		C ( 'SHOW_PAGE_TRACE', false );
		$fck = D ('Fck');

		$id = $_SESSION[C('USER_AUTH_KEY')];
		$field = '*';
		$fck_rs = $fck -> field($field) -> find($id);
		
		$reid = $fck_rs['re_id'];
		$news = M('form');
		$news_result = $news->where('status = 1')->field('title')->select();
		$_SESSION['news'] = $news_result; // 新闻信息
		
		// 留言数量
		$msg = M('msg');
		$msg_count = $msg->where("s_read = 0 and s_user_id ='".$fck_rs['user_id']."'")->count();
		$this->assign('msg_count', $msg_count);
		
		$HYJJ="";
		$this->_levelConfirm($HYJJ,1);
		$this->assign('voo',$HYJJ);//会员级别
		
		$k=explode(",",$fck_rs['prem']);
		$this -> assign('k',$k);
		
		$id = $_SESSION[C('USER_AUTH_KEY')]; // 登录AutoId
		$jiadan = M('Jiadan');
		$jiadanb = M('jiadanb');
		// 会员级别
		$urs = $fck->where('id=' . $id)
		->field('*')
		->find();
		$this->assign('fck_rs', $urs); // 总奖金
		$this->assign('lockAmount', $urs['cpzj'] * 0.7);
		// 团队人数
		$all_nn = $fck->where('re_path like "%,' . $id . ',%" and is_pay=1')->count();
		$this->assign('all_nn', $all_nn);
		// 团队总业绩
// 		$nowdate = strtotime(date('Y-m-d'));
		$nowdate = strtotime ("now");
		$all_nmoney = $fck->where('p_path like "%,' . $id . ',%" and is_pay=1 and pdt<' . $nowdate)->sum('cpzj');
		if (empty($all_nmoney)) {
		    $all_nmoney = 0.00;
		}
		$this->assign('all_nmoney', $all_nmoney);
		// 推荐总业绩
		$all_remoney = $fck->where('re_path like "%,' . $id . ',%" and is_pay=1 and pdt<' . $nowdate)->sum('cpzj');
		if (empty($all_remoney)) {
		    $all_remoney = 0.00;
		}
		// 货币价格
		$price = M('price');
		$rsP = $price->query("select price from xt_price where id = (select max(id) from xt_price)");
		$this->assign('price', $rsP[0]['price']);
		
		$this->assign('all_remoney', $all_remoney);
		// 出局分红包数
		$where = Array();
		$where['user_id'] = $urs['user_id'];
		$out_counts = $jiadan->where($where)->field("money,danshu")->find();
		if ($out_counts) {
		    $outCounts = floor(bcdiv($out_counts['money'], 1000,5));
		} else {
		    $outCounts = 0;
		}
		$this->assign('out_counts', $outCounts);
		// 未出局分红包数
		$where['user_id'] = $urs['user_id'];
		$in_counts = $jiadan->where($where)->field("money,danshu")->find();
		if ($in_counts) {
		    $inCounts = ceil(bcdiv(($in_counts['danshu']*1000 - $in_counts['money']), 1000,5));
		} else {
		    $inCounts = 0;
		}
		$this->assign('in_counts', $inCounts);
		// B网版块
		$in_countsb = $jiadanb->where($where)->field("money,danshu")->find();
		if ($in_countsb) {
		    $inCountsb = ceil(bcdiv(($in_countsb['danshu']*1000 - $in_countsb['money']), 1000,5));
		} else {
		    $inCountsb = 0;
		}
		$this->assign('in_countsb', $inCountsb);
		$out_countsb = $jiadanb->where($where)->field("money,danshu")->find();
		if ($out_countsb) {
		    $outCountsb = floor(bcdiv($out_countsb['money'], 1000,5));
		} else {
		    $outCountsb = 0;
		}
		$this->assign('out_countsb', $outCountsb);
		$netb = M('netb');
		$netb_rs = $netb->where('uid=' . $id)->field('*')->find();
		$this->assign('netB', $netb_rs);
		
		$HYJJ = "";
		$this->_levelConfirm($HYJJ, 1);
		$this->assign('voo', $HYJJ); // 会员级别
		
		$see = $_SERVER['HTTP_HOST'] . __APP__;
		$see = str_replace("//", "/", $see);
		$this->assign('server', $see);
		$fck->emptywTime();
		$this->display('index');
	}

    //每日自动结算
	public function aotu_clearings(){
		$fck = D ('Fck');
		$fee = M ('fee');
		$nowday = strtotime(date('Y-m-d'));
		$nowweek = date("w");
		if($nowweek==0){
			$nowweek = 7;
		}
		$kou_w = $nowweek-1;
		$weekday = $nowday-$kou_w*24*3600;
		
		$now_dtime = strtotime(date("Y-m-d"));
		if(empty($_SESSION['auto_cl_ok'])||$_SESSION['auto_cl_ok']!=$now_dtime){
			$js_c = $fee->where('id=1 and f_time<'.$weekday)->count();
			if($js_c>0){
				//经理分红
				$fck->jl_fenghong();
			}
			$_SESSION['auto_cl_ok'] = $now_dtime;
		}
		unset($fck,$fee);
	}

}
?>