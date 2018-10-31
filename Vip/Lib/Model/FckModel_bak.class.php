<?php
class FckModel extends CommonModel {
	//数据库名称

   public function xiangJiao($Pid=0,$DanShu=1){
        //========================================== 往上统计单数
        $where = array();
        $where['id'] = $Pid;
        $field = 'treeplace,father_id';
        $vo = $this ->where($where)->field($field)->find();
        if ($vo){
            $Fid = $vo['father_id'];
            $TPe = $vo['treeplace'];
            $table = $this->tablePrefix.'fck';
            if ($TPe == 0 && $Fid > 0){
                $this->execute("update ". $table ." Set `l`=l+$DanShu, `shangqi_l`=shangqi_l+$DanShu  where `id`=".$Fid);
            }elseif($TPe == 1 && $Fid > 0){
                $this->execute("update ". $table ." Set `r`=r+$DanShu, `shangqi_r`=shangqi_r+$DanShu  where `id`=".$Fid);
            }
            if ($Fid > 0) $this->xiangJiao($Fid,$DanShu);
        }
        unset($where,$field,$vo);
    }

    public function addencAdd($ID=0,$inUserID=0,$money=0,$name=null,$UID=0,$time=0,$acttime=0,$bz=""){
        //添加 到数据表
        if ($UID > 0) {
            $where = array();
            $where['id'] = $UID;
            $frs = $this->where($where)->field('nickname')->find();
            $name_two = $name;
            $name = $frs['nickname'] . ' 开通会员 ' . $inUserID ;
            $inUserID = $frs['nickname'];
        }else{
            $name_two = $name;
        }

        $data = array();
        $history = M ('history');

        $data['user_id']		= $inUserID;
        $data['uid']			= $ID;
        $data['action_type']	= $name;
        if($time >0){
        	$data['pdt']		= $time;
        }else{
        	$data['pdt']		= mktime();
        }
        $data['epoints']		= $money;
        if(!empty($bz)){
        	$data['bz']			= $bz;
        }else{
        	$data['bz']			= $name;
        }
        $data['did']			= 0;
        $data['type']			= 1;
        $data['allp']			= 0;
        if($acttime>0){
        	$data['act_pdt']	= $acttime;
        }
        $result = $history ->add($data);
        unset($data,$history);
    }

    public function huikuiAdd($ID=0,$tz=0,$zk,$money=0,$nowdate=null){
        //添加 到数据表

        $data                   = array();
        $huikui                = M ('huikui');
        $data['uid']            = $ID;
        $data['touzi']    = $tz;
        $data['zhuangkuang']            = $zk;
        $data['hk']        = $money;
        $data['time_hk']             = $nowdate;
        $huikui ->add($data);
        unset($data,$huikui);
    }
    
    //对碰1：1
    public function touch1to1(&$Encash,$xL=0,$xR=0,&$NumS=0){
    	$xL = floor($xL);
    	$xR = floor($xR);
    
    	if ($xL > 0 && $xR > 0){
    		if ($xL > $xR){
    			$NumS = $xR;
    			$xL = $xL - $NumS;
    			$xR = $xR - $NumS;
    			$Encash['0'] = $Encash['0'] + $NumS;
    			$Encash['1'] = $Encash['1'] + $NumS;
    		}
    		if ($xL < $xR){
    			$NumS = $xL;
    			$xL   = $xL - $NumS;
    			$xR   = $xR - $NumS;
    			$Encash['0'] = $Encash['0'] + $NumS;
    			$Encash['1'] = $Encash['1'] + $NumS;
    		}
    		if ($xL == $xR){
    			$NumS = $xL;
    			$xL   = 0;
    			$xR   = 0;
    			$Encash['0'] = $Encash['0'] + $NumS;
    			$Encash['1'] = $Encash['1'] + $NumS;
    		}
    		$Encash['2'] = $NumS;
    	}else{
    		$NumS = 0;
    		$Encash['0'] = 0;
    		$Encash['1'] = 0;
    	}
    }
    
	//计算奖金
    public function getusjj($uid,$type=0){
    	$mrs = $this->where('id='.$uid)->find();
    	if($mrs){
    		//tj
            $this->tuijj($mrs['re_id'],$mrs['user_id'],$mrs['cpzj']);
            //dp
    		$this->duipeng($mrs['p_path']);
    		if($type==1){
//     			//bd
//     			$this->baodanfei($mrs['shop_id'],$mrs['user_id']);
    		}
    	}
		unset($mrs);
    }
    
    //直推奖
    public function tuijj($ID=0,$inUserID=0,$cpzj=0){
    	$fee = M('fee');
    	$fee_rs = $fee->field('s3')->find(1);
    	$s3 = explode("|", $fee_rs['s3']);

    	$where = array();
    	$where['id'] = $ID;
    	$where['is_fenh'] = array('eq',0);
    	$field = 'id,u_level';
    	$frs = $this->where($where)->field($field)->find();
    	if ($frs){
    		$myid = $frs['id'];
    		$ssss = $frs['u_level']-1;
    		$prii = $s3[$ssss]/100;
    		$money_count = bcmul($cpzj, $prii,2);
    		if($money_count>0){
    			$this->rw_bonus($myid,$inUserID,1,$money_count);
    		}
    	}
    	unset($fee,$fee_rs,$frs,$where);
    }

    //见点奖
    public function jiandianjiang($ppath,$inUserID=0){
        
        $fee = M('fee');
        $fee_rs = $fee->field('s7,s15')->find(1);
        $s7 = $fee_rs['s7'];
        $s15 = explode("|",$fee_rs['s15']);
        $scc = count($s15);
        $max_c = 0;
        for($i=0;$i<$scc;$i++){
            if($s15[$i]>$max_c){
                $max_c = $s15[$i];
            }
        }
    
        $lirs = $this->where('id in (0'.$ppath.'0)')->field('id,u_level,is_fenh')->order('p_level desc')->limit($max_c)->select();
        $i = 1;
        foreach($lirs as $lrs){
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            $money_count = $s7;
            if($money_count>0&&$is_fenh==0){
                $this->rw_bonus($myid,$inUserID,2,$money_count);
            }
            $i++;
        }
        unset($fee,$fee_rs,$s15,$lirs,$lrs);
    }
    
    //对碰奖
    public function duipeng($ppath){
    	$fee = M ('fee');
    	$fee_rs = $fee->field('s1,s5,s9,s10,str5')->find(1);
    	$s1 = explode("|",$fee_rs['s1']);		//各级对碰比例
    	$s10 = explode("|",$fee_rs['s10']);		//各级对碰比例
    	$s9 = explode("|",$fee_rs['s9']);		//会员级别费用
    	$s5 = explode("|",$fee_rs['s5']);		//封顶
    	$one_mm = $s9[0];
    	$fck_array = 'is_pay>=1 and (shangqi_l>0 or shangqi_r>0) and id in (0'.$ppath.'0)';
    	$field = 'id,user_id,shangqi_l,shangqi_r,benqi_l,benqi_r,is_fenh,p_path,re_nums,nickname,u_level,re_id,day_feng,re_path,re_level,p_level,peng_num,n_pai';
    	$frs = $this->where($fck_array)->field($field)->select();
    	//BenQiL  BenQiR  ShangQiL  ShangQiR
    	foreach ($frs as $vo){
    		$L = 0;
    		$R = 0;
    		$L = $vo['shangqi_l'];
    		$R = $vo['shangqi_r'];
    		$sq_l = $vo['shangqi_l'];
    		$sq_r = $vo['shangqi_r'];
    		$Encash    = array();
    		$NumS      = 0;//碰数
    		$money     = 0;//对碰奖金额
    		$Ls        = 0;//左剩余
    		$Rs        = 0;//右剩余
    		$this->touch1to1($Encash, $L, $R, $NumS);
    		$Ls = $L - $Encash['0'];
    		$Rs = $R - $Encash['1'];
    		$myid = $vo['id'];
    		$myusid = $vo['user_id'];
            $myulv = $vo['u_level'];
    		$ss = $myulv-1;
    		$feng = $vo['day_feng'];
    		$is_fenh = $vo['is_fenh'];
    		$reid = $vo['re_id'];
    		$repath = $vo['re_path'];
    		$ul = $s1[$ss]/100;
    		$money = $one_mm*$NumS *$ul;//对碰奖 奖金
    		if($money>$s5[$ss]){
    			$money = $s5[$ss];
    		}
    		if($feng>=$s5[$ss]){
    			$money=0;
    		}else{
    			$jfeng=$feng+$money;
    			if ($jfeng>$s5[$ss]){
    				$money=$s5[$ss]-$feng;
    			}
    		}
    		$result = $this->query('UPDATE __TABLE__ SET `shangqi_l`='. $Ls .',`shangqi_r`='. $Rs .' where `id`='. $vo['id'].' and shangqi_l='.$sq_l.' and shangqi_r='.$sq_r);
    		$money_count = $money;
    		if($money_count>0&&$is_fenh==0&&$result>0&&$myulv==2){
    			$this->rw_bonus($myid,$myusid,2,$money_count);
    			//领导奖
    			$this->lingdaojiang($myid,$repath,$myusid,$money_count);
    		}
    	}
    	unset($fee,$fee_rs,$frs,$vo);
    }

    //领导奖
    public function lingdaojiang($uid,$repath,$inUserID=0,$money=0){
    	$fee = M('fee');
    	$fee_rs = $fee->field('s11,s12')->find(1);
    	$s11 = $fee_rs['s11']/100;
    	$s12 = explode("|",$fee_rs['s12']);
    	
    	$sgg = array();
    	
    	$sgg[1][1] = 0.2;
    	
    	$sgg[2][1] = 0.3;
    	$sgg[2][2] = 0.2;
    	
    	$sgg[3][1] = 0.4;
    	$sgg[3][2] = 0.3;
    	$sgg[3][3] = 0.2;
    	
    	$sgg[4][1] = 0.5;
    	$sgg[4][2] = 0.4;
    	$sgg[4][3] = 0.3;
    	$sgg[4][4] = 0.2;
    	$sgg[4][5] = 0.1;

        //给上5代
    	$lirs = $this->where('id in (0'.$repath.'0)')->field('id,is_fenh,u_level')->order('re_level desc')->limit(5)->select();
    	$i = 1;
    	foreach($lirs as $lrs){
    		$money_count = 0;
    		$myid = $lrs['id'];
    		$is_fenh = $lrs['is_fenh'];
            $u_level = $lrs['u_level'];
            if($u_level>=4){
            	$prii = $sgg[$u_level][$i];
            	$money_count = bcmul($prii, $money,2);
            }elseif($u_level==3){
            	if($i<=3){
	            	$prii = $sgg[$u_level][$i];
	            	$money_count = bcmul($prii, $money,2);
            	}
            }elseif($u_level==2){
            	if($i<=2){
	            	$prii = $sgg[$u_level][$i];
	            	$money_count = bcmul($prii, $money,2);
            	}
            }elseif($u_level==1){
            	if($i<=1){
	            	$prii = $sgg[$u_level][$i];
	            	$money_count = bcmul($prii, $money,2);
            	}
            }
    		if($money_count>0&&$is_fenh==0){
    			$this->rw_bonus($myid,$inUserID,3,$money_count);
    		}
    		$i++;
    	}
    	unset($lirs,$lrs);
    	unset($fee,$fee_rs);
    }

    //报单费
    public function baodanfei($uid,$inUserID){
        $fee = M('fee');
        $fee_rs = $fee->field('s14')->find();
        $s14 = $fee_rs['s14'];
        $money_count = $s14;
        $frs = $this->where('id='.$uid.' and is_pay>0 and is_fenh=0')->field('id,user_id,u_level')->find();
        if($frs){
            $myid = $frs['id'];
            $myusid = $frs['user_id'];
            if($money_count>0){
                $this->rw_bonus($myid,$inUserID,5,$money_count);
            }
        }
        unset($bonus,$fee,$fee_rs,$frs,$s14);
    }
    
    //分红
    public function fenhongjiang($type=0){
    	$now_time = strtotime(date("Y-m-d"));
    	$fee = M('fee');
    	$fee_rs = $fee->field('s6,s13,s4,f_time')->find();
    	$s6 = explode("|",$fee_rs['s6']);//分红
        $s13 = (int)$fee_rs['s13'];//封顶
    	$f_time = $fee_rs['f_time'];
    	if($f_time<$now_time||$type==1){
    		$result = $fee->execute("update __TABLE__ set f_time=".$now_time." where id=1 and f_time=".$f_time);
    		if($result||$type==1){
				$whereb = "is_fenh=0 and is_lock=0 and is_pay>0 and fanli=0 and fanli_num<=".$s13;
				$listb = $this->where($whereb)->field('id,user_id,u_level,fanli_num')->order('id asc')->select();
                foreach($listb as $lrsb){
					$money_count = 0;
					$myid = $lrsb['id'];
					$inUserID = $lrsb['user_id'];
                    $fanli_num = $lrsb['fanli_num'];
                    $u_level = $lrsb['u_level'];
                    $ssss = $u_level-1;
                    $money_count = $s6[$ssss];
                    $next_num = $fanli_num+1;
                    if($next_num>=$s13){
                    	$usql = ",fanli=1";
                    }
					$this->execute("update __TABLE__　set fanli_num=fanli_num+1".$usql." where id=".$myid);
					if($money_count>0){
						$this->rw_bonus($myid,$inUserID,4,$money_count);
					}
				}
				unset($listb,$lrsb);
    		}
    	}
    	unset($fee,$fee_rs);
    }

	//各种扣税
    public function rw_bonus($myid,$inUserID=0,$bnum=0,$money_count=0){
    	$fee = M('fee');
    	$fee_rs = $fee->field('s3,str4,str5')->find();
    	$s3 = $fee_rs['s3']/100;
    	$str4 = $fee_rs['str4']/100;
    	$str5 = $fee_rs['str5']/100;

    	$usqla = "";
    	$usqlb = "";
		$money_ka = 0;
		$money_kb = 0;
		$money_kc = 0;
    	$money_ka = bcmul($money_count, $str4,2);
    	if($money_ka<0){
    		$money_ka = 0;
    	}
		
// 		$money_kb = $money_count*$str4;
// 		if($money_kb<0){
// 			$money_kb = 0;
// 		}
// 		$money_kb = ((int)($money_kb*100))/100;
		
		$last_m = $money_count-$money_ka-$money_kb-$money_kc;//剩余，此值写入K币
		
// 		//计算封顶
// 		$mrs = $this->where('id='.$myid.' and re_nums=0')->field('id,cpzj,zjj')->find();
// 		if($mrs){
// 			$mycpzj = $mrs['cpzj'];
// 			$zjj = $mrs['zjj'];
// 			$all_cc = $zjj+$last_m;
// 			if($all_cc>$mycpzj){
// 				$last_m = $mycpzj-$zjj;
// 			}
// 			if($last_m<0){
// 				$last_m = 0;
// 			}
// 		}
// 		unset($mrs);
	
    	$bonus = M('bonus');
    	$bid = $this->_getTimeTableList($myid);
    	$inbb = "b".$bnum;
    	if($bnum==2){
    		$usqla .=",day_feng=day_feng+".$money_count;
    	}elseif($bnum==4){
    		$usqla .=",fanli_money=fanli_money+".$money_count;
    	}
    	if($money_ka>0){
            $usqla .=",agent_cf=agent_cf+".$money_ka;
    		$usqlb .= ",b5=b5-".$money_ka."";
    	}
    	// if($money_kc>0){
    	// 	$usqlb .= ",b5=b5-".$money_ka."";
    	// }
    	$bonus->execute("UPDATE __TABLE__ SET b0=b0+".$last_m.",".$inbb."=".$inbb."+".$money_count."".$usqlb." WHERE id={$bid}"); //加到记录表
        $this->execute("update __TABLE__ set agent_use=agent_use+".$last_m.",zjj=zjj+".$last_m."".$usqla." where id=".$myid);//加到fck
        unset($bonus);

    	if($money_count>0){
    		$this->addencAdd($myid,$inUserID,$money_count,$bnum);
    	}
//     	if($money_ka>0){
//     		$this->addencAdd($myid,$inUserID,-$money_ka,4);
//     	}
//     	if($money_kb>0){
//     		$this->addencAdd($myid,$inUserID,-$money_kb,6);
//     	}
//     	if($money_kc>0){
//     		$this->addencAdd($myid,$inUserID,-$money_kc,11);
//     	}
    	unset($fee,$fee_rs,$mrs);
    }
    
    //分红添加记录
    public function add_xf($uid,$user_id,$money=0,$gnum=0){
		$fenhong = M('fenhong');
		$data = array();
		$data['uid'] = $uid;
		$data['user_id'] = $user_id;
		$data['f_num'] = $gnum;
		$data['f_money'] = $money;
		$data['pdt'] = mktime();
		$fenhong->add($data);
		unset($fenhong,$data);
    }


    //清空时间
	public function emptyTime(){

		$nowdate = strtotime(date('Y-m-d'));

		$this->query("UPDATE `xt_fck` SET `day_feng`=0,_times=".$nowdate." WHERE _times !=".$nowdate."");

	}
	
	public function find_check($ppath,$plv=0){
		$max_c = 10;
		$max_n = pow(2,$max_c);
		$max_plv = $plv-$max_c;
		$mrs = $this->where('id in (0'.$ppath.'0)')->field('id')->find();
		if($mrs){
			$myid = $mrs['id'];
			$count = $this->where('p_path like "%,'.$myid.',%" and is_pay>0 and p_level='.$plv)->count();
			if($count==$max_n){
				$this->execute("update __TABLE__ set is_aa=1 where id=".$myid);
			}
		}
		unset($mrs);
	}


    public function _addBonus($DID=0){
    //统计总奖金 到 xt_times_bonus 表   奖金结算完后调用
        $times_bonus = M ('times_bonus');
        $fee = M ('fee');
        $rs = $fee->field('b0,b1,b2,b3,b4,b5,b6,b7,b8,b9,b10')->find();
        $times = M ('times');
        $bonus = M ('bonus');
        $trs = $times->field('*')->order('id desc')->find();
        $data = array();
        if ($trs){
            $b0 = 0;
            $b1 = 0;
            $b2 = 0;
            $b3 = 0;
            $b4 = 0;
            $where['did'] = $trs['id'];
            $b0 = $bonus->where($where)->sum('b0');
            $b1 = $bonus->where($where)->sum('b1');
            $b2 = $bonus->where($where)->sum('b2');
            $b3 = $bonus->where($where)->sum('b3');
            $b4 = $bonus->where($where)->sum('b4');
            $b5 = $bonus->where($where)->sum('b5');
            $b6 = $bonus->where($where)->sum('b6');
            $b7 = $bonus->where($where)->sum('b7');
            $b8 = $bonus->where($where)->sum('b8');
            //=======汇总结束============
            $s_date = 0;
            $e_date = 0;
            $s_date = $trs['shangqi'];
            $e_date = $trs['benqi'];
            $data['b0']      = $b0;
            $data['b1']      = $b1;
            $data['b2']      = $b2;
            $data['b3']      = $b3;
            $data['b4']      = $b4;
            $data['b5']      = $b5;
            $data['b6']      = $b6;
            $data['b7']      = $b7;
            $data['b8']      = $b8;
            $data['did']     = $trs['id'];
            $data['s_date']  = $s_date;
            $data['e_date']  = $e_date;
            $times_bonus_rs = $times_bonus->where("did={$DID}")->find();
            if ($times_bonus_rs){
                $data['id'] = $times_bonus_rs['id'];
                $times_bonus->save($data);
            }else{
                $times_bonus->add($data);
            }
        }
        unset($times_bonus,$fee,$rs,$times,$bonus,$trs,$data);
    }

/*
奖金结束===================================================================================================================
===========================================================================================================================
*/

	public  function _getTimeTableList($uid)
    {
    	$times = M ('times');
    	$bonus = M ('bonus');
    	$boid = 0;
    	$nowdate = strtotime(date('Y-m-d'))+3600*24-1;
//     	$nowdate = time();
    	$settime_two['benqi'] = $nowdate;
    	$settime_two['type']  = 0;
    	$trs = $times->where($settime_two)->find();
    	if (!$trs){
    		$rs3 = $times->where('type=0')->order('id desc')->find();
    		if ($rs3){
    			$data['shangqi']  = $rs3['benqi'];
    			$data['benqi']    = $nowdate;
    			$data['is_count'] = 0;
    			$data['type']     = 0;
    		}else{
    			$data['shangqi']  = strtotime('2010-01-01');
    			$data['benqi']    = $nowdate;
    			$data['is_count'] = 0;
    			$data['type']     = 0;
    		}
    		$shangqi = $data['shangqi'];
    		$benqi   = $data['benqi'];
    		unset($rs3);
    		$boid = $times->add($data);
    		unset($data);
    	}else{
    		$shangqi = $trs['shangqi'];
    		$benqi   = $trs['benqi'];
    		$boid = $trs['id'];
    	}
    	$_SESSION['BONUSDID'] = $boid;
    	$brs = $bonus->where("uid={$uid} AND did={$boid}")->find();
    	if ($brs){
    		$bid = $brs['id'];
    	}else{
    		$frs = $this->where("id={$uid}")->field('id,user_id')->find();
    		$data = array();
    		$data['did'] = $boid;
    		$data['uid'] = $frs['id'];
    		$data['user_id'] = $frs['user_id'];
    		$data['e_date'] = $benqi;
    		$data['s_date'] = $shangqi;
    		$bid = $bonus->add($data);
    	}
    	return $bid;
    }
    
    //判断进入B网
    public function pd_into_websiteb($uid){
    	$fck2 = M ('fck2');
    	$where = "is_pay>0 and is_lock=0 and is_bb=0 and id=".$uid;
    	$lrs = $this->where($where)->field('id,user_id,user_name,nickname,u_level')->find();
    	if($lrs){
    		$myid = $lrs['id'];
    		$result = $this->execute("update __TABLE__ set is_bb=is_bb+1 where id=".$myid." and is_bb=0");
    		if($result){
    			$data=array();
    			$data['fck_id'] = $lrs['id'];
    			$data['user_id'] = $lrs['user_id'];
    			$data['user_name'] = $lrs['user_name'];
    			$data['nickname'] = $lrs['nickname'];
    			$data['u_level'] = $lrs['u_level'];
    			$data['ceng'] = 0;
    
    			$farr = $this->gongpaixt_Two_big_B();
    			$data['father_id']		= $farr['father_id'];
    			$data['father_name']	= $farr['father_name'];
    			$data['treeplace']		= $farr['treeplace'];
    			$data['p_level']		= $farr['p_level'];
    			$data['p_path']			= $farr['p_path'];
    			$data['u_pai']			= $farr['u_pai'];
    			$data['is_pay']			= 1;
    			$data['pdt']			= time();
    			$ress = $fck2->add($data);  // 添加
    			$ppath = $data['p_path'];
    			$inUserID = $data['user_id'];
    			$ulevel = $data['u_level'];
    			unset($data,$farr);
    			if($ress){
    				//b网见点
    				$this->jiandianjiang_b($ppath,$inUserID,$ulevel);
    			}
    		}
    	}
    	unset($fck2,$lrs,$where);
    }
    
    public function gongpaixt_Two_big_B(){
    	$fck = M ('fck2');
    	$field = 'id,user_id,p_level,p_path,u_pai';
    	$re_rs = $fck ->where('is_pay>0')->order('p_level asc,u_pai asc')->field($field)->select();
    	foreach($re_rs as $vo){
    		$faid=$vo['id'];
    		$count = $fck->where("is_pay>0 and father_id=".$faid)->count();
    		if ( is_numeric($count) == false){
    			$count = 0;
    		}
    		if ($count<2){
    			$father_id=$vo['id'];
    			$father_name=$vo['user_id'];
    			$TreePlace=$count;
    			$p_level=$vo['p_level']+1;
    			$p_path=$vo['p_path'].$vo['id'].',';
    			$u_pai=$vo['u_pai']*2+$TreePlace;
    
    			$arry=array();
    			$arry['father_id']=$father_id;
    			$arry['father_name']=$father_name;
    			$arry['treeplace']=$TreePlace;
    			$arry['p_level']=$p_level;
    			$arry['p_path']=$p_path;
    			$arry['u_pai']=$u_pai;
    			return $arry;
    			break;
    		}
    	}
    }

}
?>