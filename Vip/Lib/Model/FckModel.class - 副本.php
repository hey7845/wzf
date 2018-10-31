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
            }elseif($TPe == 2 && $Fid > 0){
                $this->execute("update ". $table ." Set `lr`=lr+$DanShu, `shangqi_lr`=shangqi_lr+$DanShu  where `id`=".$Fid);
            }
            if ($Fid > 0) $this->xiangJiao($Fid,$DanShu);
        }
        unset($where,$field,$vo);
    }
    
	
	
	public function shangjiaTJ($ppath,$treep=0){
		$where = "id in (0".$ppath."0)";
		$lirs = $this->where($where)->order('p_level desc')->field('id,treeplace')->select();
		foreach($lirs as $lrs){
			$myid = $lrs['id'];
			$mytp = $lrs['treeplace'];
			if($treep==0){
				$this->execute("update __TABLE__ Set `re_nums_l`=re_nums_l+1,`re_nums_b`=re_nums_b+1 where `id`=".$myid);
			}else{
				$this->execute("update __TABLE__ Set `re_nums_r`=re_nums_r+1,`re_nums_b`=re_nums_b+1 where `id`=".$myid);
			}
			$treep = $mytp;
		}
		unset($lirs,$lrs,$where);
    }

//	public function xiangJiao($Pid=0,$DanShu=1,$plv=0,$op=1){
//        //========================================== 往上统计单数【有层碰奖】
//
//        $peng = M ('peng');
//        $where = array();
//        $where['id'] = $Pid;
//        $field = 'treeplace,father_id,p_level';
//        $vo = $this ->where($where)->field($field)->find();
//        if ($vo){
//            $Fid = $vo['father_id'];
//            $TPe = $vo['treeplace'];
//            $table = $this->tablePrefix .'fck';
//			$dt	= strtotime(date("Y-m-d"));//现在的时间
//            if ($TPe == 0 && $Fid > 0){
//            	$p_rs = $peng ->where("uid=$Fid and ceng = $op") ->find();
//            	if($p_rs){
//            		$peng->execute("UPDATE __TABLE__ SET `l`=l+{$DanShu}  WHERE uid=$Fid and ceng = $op");
//            	}else{
//            		$peng->execute("INSERT INTO __TABLE__ (uid,ceng,l) VALUES ($Fid	,$op,$DanShu) ");
//            	}
//
//                $this->execute("UPDATE ". $table ." SET `l`=l+{$DanShu}, `benqi_l`=benqi_l+{$DanShu}  WHERE `id`=".$Fid);
//            }elseif($TPe == 1 && $Fid > 0){
//            	$p_rs = $peng ->where("uid=$Fid and ceng = $op") ->find();
//            	if($p_rs){
//            		$peng->execute("UPDATE __TABLE__ SET `r`=r+{$DanShu}  WHERE uid=$Fid and ceng = $op");
//            	}else{
//            		$peng->execute("INSERT INTO __TABLE__ (uid,ceng,r) VALUES ($Fid,$op,$DanShu) ");
//            	}
//                $this->execute("UPDATE ". $table ." SET `r`=r+{$DanShu}, `benqi_r`=benqi_r+{$DanShu}  WHERE `id`=".$Fid);
//            }
//            $op++;-+*
//            if ($Fid > 0) $this->xiangJiao($Fid,$DanShu,$plv,$op);
//        }
//        unset($where,$field,$vo);
//    }

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
//额外收入
public function fenhong(){
         $fee=M('Fee');
         $fee_rs=$fee->field('s6')->find(1);
         $s6=$fee_rs['s6'];

     
        $list=$this->where('re_nums>0')->field('id,is_fh')->select();
        foreach ($list as $key => $value) {
            $money=$s6*$value['re_nums'];
          
      if($value['is_fh']==0 && $money>0){
         $this->rw_bonus($value['id'],$value['user_id'],1,$money);
         $this->execute("update __TABLE__ set if_fh=1 where id=".$value['id']);
       }
    
        
    }

   } 


    //计算奖金
    public function getusjj($uid,$type=0,$money=0){
        $mrs = $this->where('id='.$uid)->find();
        if($mrs){
        

         
                //报单奖
                $this->baodanfei($mrs['shop_id'],$mrs['user_id'],$money);
           
        }
        unset($mrs);
    }

public function tz($ppath,$money){

      $fck=M('fck');
        $lirs = $this->where('id in (0'.$ppath.'0)')->field('id,is_fenh,u_level,re_nums,user_id')->order('rdt desc')->select();
        foreach ($lirs as $key => $v) {
       
            $this->execute("update __TABLE__ set ach=ach+{$money} where id=".$v['id']);
        }

}




	//直推奖
    public function tuijj($ID=0,$inUserID=0,$money=0){
        $fee = M('fee');
        $fee_rs = $fee->field('s3')->find(1);
        $s3 = explode("|",$fee_rs['s3']);
        
        $where = array();
        $where['id'] = $ID;
        $where['is_fenh'] = array('eq',0);
        $field = 'id,user_id,u_level,cpzj';
        $frs = $this->where($where)->field($field)->find();
        if ($frs){
            $myid = $frs['id'];
            $myusid = $frs['user_id'];
            $uLevel = $frs['u_level'];
            
            $prii = $s3[$uLevel-1]/100;
            
            $money_count = bcmul($prii,$money,2);
          
            if($money_count>0){
                $this->rw_bonus($myid,$inUserID,1,$money_count);
                $this->jjc($myusid,$money_count);
            }
        }
        unset($fee,$fee_rs,$frs,$where);
    }

 
	
	//对碰奖
    public function duipeng($ppath,$level){
    	$fee = M ('fee');
    	$fee_rs = $fee->field('str1,s11,s1')->find(1);
    	$s1 = explode("|",$fee_rs['s1']);		//各级对碰比例
    	$s11 = $fee_rs['s11'];		//各级对碰比例
    	
    	$str1 = explode("|",$fee_rs['str1']);  		//封顶
    	$one_mm = $s11;
    	// $fck_array = 'is_pay>=1 and (shangqi_l>0 or shangqi_r>0) and id in (0'.$ppath.'0)';
        $fck_array = 'is_pay>=1 and  id in (0'.$ppath.'0)';
    	$field = '*';
    	$frs = $this->where($fck_array)->field($field)->select();
    	//BenQiL  BenQiR  ShangQiL  ShangQiR
    	foreach ($frs as $vo){
            $list=$this->where('father_id='.$vo['id'])->order('id asc')->select();

            $l_ach=$list[0]['ach']+$list[0]['cpzj'];
            $r_ach=$list[1]['ach']+$list[1]['cpzj'];
            $l_nums=$list[0]['l_nums'];
            $r_nums=$list[1]['r_nums'];

            $lo=$l_ach/$s11;
            $ro=$r_ach/$s11;
            $lo=floor($lo);
            $ro=floor($ro);
    		$L = 0;
    		$R = 0;
           
            $L=($l_ach-$l_nums*$s11)/$s11;
            $R=($r_ach-$r_nums*$s11)/$s11;
            $L=floor($L);
            $R=floor($R);

    		// $L = $vo['shangqi_l'];
    		// $R = $vo['shangqi_r'];
    		$sq_l = $vo['shangqi_l'];
    		$sq_r = $vo['shangqi_r'];
            $z_date=$vo['z_date'];
    		$Encash    = array();
    		$NumS      = 0;//碰数
    		$money     = 0;//对碰奖金额
    		$Ls        = 0;//左剩余
    		$Rs        = 0;//右剩余
    		$this->touch1to1($Encash, $lo, $ro, $NumS);
    		$Ls = $lo - $Encash['0'];
    		$Rs = $ro - $Encash['1'];
    		$myid = $vo['id'];
    		$myusid = $vo['user_id'];
            $myulv = $vo['u_level'];
    		$ss = $myulv-1;
    		$feng = $vo['day_feng'];
    		$is_fenh = $vo['is_fenh'];
    		$reid = $vo['re_id'];
    		$repath = $vo['re_path'];
    		$relevel = $vo['re_level'];
    		$ul = $s1[$ss]/100;
            $aa = $str1[$ss];
    		$money = $one_mm*$NumS *$ul;//对碰奖 奖金
    		if($money>$aa){
    			$money = $aa;
    		}
    		if($feng>=$aa){
    			$money=0;
    		}else{
    			$jfeng=$feng+$money;
    			if ($jfeng>$aa){
    				$money=$aa-$feng;
    			}
    		}
    	   // echo $Ls;
            // $result = $this->query('UPDATE __TABLE__ SET `shangqi_l`='. $Ls .',peng_num=peng_num+' . $NumS . ',`shangqi_r`='. $Rs .' where `id`='. $vo['id'].' and shangqi_l='.$sq_l.' and shangqi_r='.$sq_r);
            $result = $this->query('UPDATE __TABLE__ SET `ls`='. $lo .',`l_nums`='. $Ls .',`rs`='. $ro .',peng_num=peng_num+' . $NumS . ',`r_nums`='. $Rs .' where `id`='. $vo['id']);
    		$money_count = $money;
    		if($money_count>0){
    			$this->rw_bonus($myid,$myusid,2,$money_count);
                $this->jjc($myusid,$money_count);
                    
                    if($z_date==0){
                        $time=time();
                        $this->query('UPDATE __TABLE__ SET  `z_date`='. $time .' where `id`='. $vo['id']);
                    }
                   


           
    			//领导奖
    			//$this->guanglij($repath,$myusid,$money_count);
				//互助奖
				//$this->Huzhufenhong($myid,$relevel,$myusid,$money_count);
    		}
    	}
    	unset($fee,$fee_rs,$frs,$vo);
    }
	



    //领导奖
    public function lingdaojiang($repath,$inUserID=0,$money=0){
        $fee = M('fee');
        $fee_rs = $fee->field('s4,s5,s7,str6')->find(1);
        $s5 = explode("|",$fee_rs['s5']);
         $s7 = $fee_rs['s7'];
         $str6 = $fee_rs['str6'];
        $s4=$fee_rs['s4'];
        //给上5代
        $lirs = $this->where('id in (0'.$repath.'0)')->field('id,is_fenh,u_level,re_nums,user_id,fh_nums,c_date')->order('rdt desc')->select();
        $i = 0;
        foreach($lirs as $lrs){
            $money_count = 0;
            $myid = $lrs['id'];
     
            $uLevel = $lrs['u_level'];
            $myusid = $lrs['user_id'];
            $feng=$lrs['fh_nums'];
            $c_date=$lrs['c_date'];
            $k = $s7;
            if($k>$i && $uLevel>=4){
            $prii = $s5[$i]/100;
            $money_count = bcmul($prii, $s4,2);

            if($feng>=$str6){
                $money_count=0;
            }else{
                $jfeng=$feng+$money_count;
                if ($jfeng>$str6){
                    $money_count=$str6-$feng;
                }
            }

            if($money_count>0 ){
                $this->rw_bonus($myid,$inUserID,4,$money_count);
                $this->jjc($myusid,$money_count);

                if($c_date==0){
                        $time=time();
                        $this->query('UPDATE __TABLE__ SET  `c_date`='. $time .' where `id`='. $lrs['id']);
                    }
            }
          
            }
              $i++;
        }
        unset($lirs,$lrs);
        unset($fee,$fee_rs);
    }



  //报单费
    public function baodanfei($uid,$inUserID,$cpzj=0){
        
        $fee = M('fee');
        $fee_rs = $fee->field('str5')->find();
        $str5 = $fee_rs['str5'];
        // $money_count = bcmul($cpzj,$s14,2);
        $money_count=$str5;
        $frs = $this->where('id='.$uid.' and is_pay>0')->field('id,user_id,re_path,u_level')->find();
        if($frs){
            $myid = $frs['id'];
            $myusid = $frs['user_id'];
            $repath = $frs['re_path'];
            if($money_count>0){
                $this->rw_bonus($myid,$inUserID,7,$money_count);
                 $this->jjc($myusid,$money_count);
            }
        }
        unset($fee,$fee_rs,$frs,$s14);
    }
//业绩,vip,周计算
    public function yeji($ppath,$level,$money){
        $fck=M('fck');
        $lirs = $this->where('id in (0'.$ppath.'0)')->field('id,is_fenh,u_level,re_nums,user_id')->order('rdt desc')->select();
        foreach ($lirs as $key => $v) {
            if($level>=4){
                $this->execute("update __TABLE__ set vip4=vip4+1 where id=".$v['id']);
            }
    
        }


        $nowdate = time();
        $list=$fck->where('u_level>=5')->field('*')->select();
        foreach ($list as $key => $value) {
            $ulevel  = $value['u_level'];
            $l       = $value['l'];
            $r       = $value['r'];
            $ach     = $value['ach'];
            $re_nums = $value['re_nums'];
            $zdt     = $value['zdt'];

            $shang_l    =$value['shang_l'];
            $shang_r    =$value['shang_r'];
            $shang_nums =$value['shang_nums'];
            $shang_ach  =$value['shang_ach'];
           
            
            
            if($zdt==0){
                $zdt= $value['pdt'];
            }
            // $outdate=$t+60*60*24*30;
            $outdate=$zdt+30;
             if($nowdate>=$outdate){
            //新增
            $ls=$l-$shang_l;
            $rs=$r-$shang_r;
            $num=$re_nums-$shang_nums;
                if($ulevel==5){
                    if($ls>=6 && $rs>=6 && $num>=6){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=tz_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=0 where id=".$value['id']);
                    }
                }

                if($ulevel==6){
                    if($ls>=8 && $rs>=8 && $num>=6){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=tz_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=0 where id=".$value['id']);
                    }
                }

                  if($ulevel==7){
                    if($ls>=12 && $rs>=12 && $num>=6){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=tz_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=0 where id=".$value['id']);
                    }
                }

                  if($ulevel==8){
                    if($ls>=18 && $rs>=18 && $num>=6){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=tz_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},tz_nums=0 where id=".$value['id']);
                    }
                }

                if($ulevel>=9){
                $res=$fck->where('father_id='.$value['id'])->order('id desc')->select();
                $l_nums=$res[0]['ach']+$res[0]['cpzj'];
                $r_nums=$lis[1]['ach']+$res[0]['cpzj'];
                $achs=$ach-$shang_ach;
                }

               if($ulevel==9){
                    if($l_nums>20000 || $r_nums>20000){
                        $one=1;
                    }else{
                        $one=0;
                    }

                    if($ls>=24 && $rs>=24 && $num>=6 && $achs>=40000 && $one==0){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=jia_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=0 where id=".$value['id']);
                    }
                }

                  if($ulevel==10){
                    if($l_nums>30000 || $r_nums>30000){
                        $one=1;
                    }else{
                        $one=0;
                    }

                    if($ls>=36 && $rs>=36 && $num>=6 && $achs>=60000 && $one==0){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=jia_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=0 where id=".$value['id']);
                    }
                }


                 if($ulevel==11){
                    if($l_nums>80000 || $r_nums>80000){
                        $one=1;
                    }else{
                        $one=0;
                    }

                    if($ls>=50 && $rs>=50 && $num>=6 && $achs>=150000 && $one==0){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=jia_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=0 where id=".$value['id']);
                    }
                }


                if($ulevel==12){
                    if($l_nums>160000 || $r_nums>160000){
                        $one=1;
                    }else{
                        $one=0;
                    }

                    if($ls>=75 && $rs>=75 && $num>=6 && $achs>=300000 && $one==0){
                        $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=jia_nums+1 where id=".$value['id']);
                    }else{
                         $thi->execute("update __TABLE__ set shang_l={$ls},shang_r={$rs},shang_nums={$num},shang_ach={$achs},jia_nums=0 where id=".$value['id']);
                    }
                }

             }
        }
        
    }
   public function getReid($id){
   		$rs = $this->where('id='.$id)->field('id,re_nums,is_fenh')->find();
   		return array('re_id'=>$rs['id'],'re_nums'=>$rs['re_nums'],'is_fenh'=>$rs['is_fenh']); 
   }
    
	//劳务奖b3
    public function guanglij($repath,$inUserID=0,$money=0){
  
    	$fee = M('fee');
    	$fee_rs = $fee->field('s7')->find(1);
    	$s7 = explode("|",$fee_rs['s7']); //代数
    
    	$lirs = $this->where('id in (0'.$repath.'0)')->field('id,u_level,re_nums,is_fenh')->order('re_level desc')->limit(1)->select();
    	
    	$i = 1;
		foreach($lirs as $lrs){
			$myid = $lrs['id'];		
			$is_fenh = $lrs['is_fenh'];	
			$re_nums = $lrs['re_nums'];	
			
			if($re_nums>10){$re_nums=10;}	
			
			$sss = $re_nums-1;
			$myccc = $s7[$sss]/100;
			
			$money_count = bcmul($myccc,$money,2);

			
			if($money_count>0&&$is_fenh==0){
				$this->rw_bonus($myid,$inUserID,5,$money_count);
			}
			
			$i++;
		}
        unset($fee,$fee_rs,$s15,$lirs,$lrs);
    }
	
	
	//互助奖(加权平分)
    public function Huzhufenhong($uid,$relevel,$inUserID,$money){
    	$fee = M('fee');
    	$fee_rs = $fee->field('s12')->find(1);
    	$s12 = $fee_rs['s12'];
		    	

		$prii = $s12/100;
		$b5_money = bcmul($prii,$money,2);
		$max_re_level = $relevel + 2;
		$where1 = "is_pay>0 and re_path like '%,".$uid.",%' and re_level<=".$max_re_level;
    	$rs_count1 = $this->where($where1)->count();
		if($rs_count1>0){
			$rs=$this->where($where1)->select();
			foreach ($rs as $vo){
				$money_count = 0;
				$myid = $vo['id'];
				$mis_fenh = $vo['is_fenh'];
				$money_count=$b5_money/$rs_count1;
				if($money_count>0&&$mis_fenh==0){
					$this->rw_bonus($myid,$inUserID,6,$money_count);
				}
			}
		}
		unset($where1,$rs_count1,$rs);		
    }  
    

	
	//每日分红
    public function mr_fenhong($type=0){
        $now_time = strtotime(date('Y-m-d'));
    	$fee = M('fee');
    	$promo = M('promo');
    	$fee_rs = $fee->field('s1,s3,f_time')->find(1);
        $s15 = $fee_rs['s1']/100;
		$s3 = explode("|",$fee_rs['s3']);
				
        $f_time = $fee_rs['f_time'];
        if($f_time<$now_time||$type==1){
            $result = $fee->execute("update __TABLE__ set f_time=".$now_time." where id=1 and f_time=".$f_time);
            if($result||$type==1){
            	$where = "is_pay=1 and is_fenh=0 and is_lockfh=0 and fanli=0 and fanli_time<".$now_time;
            	$list = $this->where($where)->field('id,user_id,u_level,fanli_money,xy_money,fanli_time,re_path,cpzj')->select();
            	foreach ($list as $lrs) {
            		$myid = $lrs['id'];
                    $inUserID = $lrs['user_id'];
                    $fanli_money = $lrs['fanli_money'];
                    $xy_money = $lrs['xx_money'];
                    $fanli_time = $lrs['fanli_time'];
                    $mycpzj = $lrs['cpzj'];
                    $myulv = $lrs['u_level'];
                    $repath = $lrs['re_path'];
					
					if($myulv==1){
						$beii = 2;
					}elseif($myulv==2){
						$beii = 2;
					}elseif($myulv==3){
						$beii = 2;
					}elseif($myulv==4){
						$beii = 2;
					}elseif($myulv==5){
						$beii = 2;
					}
					
//					$pcount = $promo -> where('danshu=0 and uid='.$myid)->count();
//					if($pcount>0){
//						$small_level = $promo->where('danshu=0 and uid='.$myid)->min('u_level');
//						$mycpzj = $s3[$small_level-1];
//					}
					
					$maxfenhong = $mycpzj*$beii;
					
            		$money_count = bcmul($s15, $mycpzj,2);
					
					$all_g = $fanli_money+$money_count;
					if($all_g>=$maxfenhong){
						if($fanli_money<$maxfenhong){
                        	$money_count = $maxfenhong-$fanli_money;
						}else{
							$money_count = 0;
						}
                        $this->execute("update __TABLE__ set fanli=1 where id=".$myid);
                    }
					
					if($now_time>$fanli_time){
						$this->query("UPDATE __TABLE__ SET `fanli_time`=".$now_time." where `id`=".$myid);
						if($money_count > 0){
							$this->query("UPDATE __TABLE__ SET `fanli_money`=fanli_money+".$money_count." where `id`=".$myid);
							
							$this->rw_bonus($myid,$inUserID,1,$money_count);
							//领导奖
							$this->lingdaojiang($repath,$inUserID,$money_count);
						}	
					}
            	}
        		unset($list,$lrs,$where);
            }
        }
    	unset($fee_rs);
    }
	
	public function jingtaibufa($uid,$money){
		echo $now_time = strtotime(date('Y-m-d'));
		$fee = M('fee');
		$fee_rs = $fee->field('s1,s3,f_time')->find(1);
        $s15 = $fee_rs['s1']/100;
		$s3 = explode("|",$fee_rs['s3']);
		
		$where = "is_pay=1 and is_fenh=0 and is_lockfh=0 and fanli=0 and id=".$uid;
		$lrs = $this->where($where)->field('id,user_id,u_level,fanli_money,xy_money,fanli_time,re_path,cpzj')->find();
		if($lrs){
			$myid = $lrs['id'];
			$inUserID = $lrs['user_id'];
			$fanli_money = $lrs['fanli_money'];
			$xy_money = $lrs['xy_money'];
			// echo $fanli_time = $lrs['fanli_time'];
			$mycpzj = $lrs['cpzj'];
			$myulv = $lrs['u_level'];
			$repath = $lrs['re_path'];
			
			if($myulv==1){
				$beii = 2;
			}elseif($myulv==2){
				$beii = 2;
			}elseif($myulv==3){
				$beii = 2;
			}elseif($myulv==4){
				$beii = 2;
			}elseif($myulv==5){
				$beii = 2;
			}
			
			$maxfenhong = $mycpzj*$beii;
			
			if($fanli_time<$now_time){		
				$money_count = bcmul($s15, $mycpzj,2);
			}else{
				$money_count = bcmul($s15, $money,2);
			}
			
			$all_g = $fanli_money+$money_count;
			if($all_g>=$maxfenhong){
				if($fanli_money<$maxfenhong){
					$money_count = $maxfenhong-$fanli_money;
				}else{
					$money_count = 0;
				}
				$this->execute("update __TABLE__ set fanli=1 where id=".$myid);
			}
			
			if($now_time>$fanli_time){
				$this->query("UPDATE __TABLE__ SET `fanli_time`=".$now_time." where `id`=".$myid);
			}
			
			if($money_count > 0){
				$this->rw_bonus($myid,$inUserID,1,$money_count);
				//领导奖
				$this->lingdaojiang($repath,$inUserID,$money_count);
			}
		}
	}
	




	//销售奖
    public function jiandianjiang($ppath,$inUserID=0,$money=0){

        $fee = M('fee');
        $fee_rs = $fee->field('s5,s11')->find(1);
		$s5 = explode("|",$fee_rs['s5']);
        $s11 = explode("|",$fee_rs['s11']);
        $scc = count($s11);
        $max_c = 0;
		for($i=0;$i<$scc;$i++){
            if($s11[$i]>$max_c){
                $max_c = $s11[$i];
            }
        }
    
        $lirs = $this->where('id in (0'.$ppath.'0)')->field('id,u_level,re_nums,cpzj,is_fenh')->order('p_level desc')->limit($max_c)->select();
        $i = 0;
        foreach($lirs as $lrs){
            $money_count = 0;
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            $re_nums = $lrs['re_nums'];
            $mycpzj = $lrs['cpzj'];
            $myulv = $lrs['u_level'];
            $sssss = $myulv-1;
            $myccc = $s11[$sssss];
			$prii = $s5[$sssss]/100;
			//if($mycpzj>$money){
            $money_count = bcmul($prii,$money,2);
			//}else{
			//$money_count = bcmul($s5, $mycpzj,2);
			//}
            if($money_count>0 && $is_fenh==0 && $i<$myccc){
                $this->rw_bonus($myid,$inUserID,3,$money_count);
            }
            $i++;
        }
        unset($fee,$fee_rs,$s11,$lirs,$lrs);
    }
	
    //层奖和对碰奖的日封顶
    public function zfd_jj($uid,$money=0){
		$fee = M('fee');
		$fee_rs = $fee->field('str1')->find();
		$str1 = explode("|",$fee_rs['str1']);//分红奖封顶
	
		$rs = $this->where('id='.$uid)->field('u_level,day_feng')->find();
		if($rs){
			$day_feng = $rs['day_feng'];
			$feng = $str1[$rs['u_level']-1];
			if($money > $feng){
				$money = $feng;
			}
	
			if($day_feng >= $feng){
				$money = 0;
			}else{
				$tt_money = $money + $day_feng;
				if( $tt_money > $feng){
					$money = $feng-$day_feng;
				}
			}
		}
	
		return $money;
	}
    	

    //各种扣税
    public function rw_bonus($myid,$inUserID=0,$bnum=0,$money_count=0,$corid=0){
        $fee = M('fee');
        $fee_rs = $fee->field('s3,s15')->find();
        
        $s3 = $fee_rs['s3']/100;  //进入税收
        $s15 = $fee_rs['s15']/100;  //进入购物积分
        

        $money_ka = 0;
        $money_kb = 0;
        $money_kc = 0;
        $money_kd = 0;
        
        $money_ka = $money_count*$s3;
        $mm=$money_count-$money_ka;
        $money_kc = $mm*$s15;
                    
        $usqla = "";
        
        $last_m = $money_count-$money_kc;//剩余，此值写入现金账户
    
        $bonus = M('bonus');
        $bid = $this->_getTimeTableList($myid);
        $inbb = "b".$bnum;
        
        // if($bnum==2){
        //     $usqla = ",day_feng=day_feng+".$money_count.""; 
        // }
        
        //  if($bnum==4){
        //     $usqla2 = ",fh_nums=fh_nums+".$money_count.""; 
        // }
        

        $usqlc = "agent_use=agent_use+".$last_m; //agent_cf重消奖
        
        $bonus->execute("UPDATE __TABLE__ SET b0=b0+".$last_m.",".$inbb."=".$inbb."+".$money_count."  WHERE id={$bid}"); //加到记录表
        $this->execute("update __TABLE__ set ".$usqlc.",agent_cf=agent_cf+".$money_kc.$usqla.$usqla2." where id=".$myid);//加到fck
        
        

        if($money_count>0){
            $this->addencAdd($myid,$inUserID,$money_count,$bnum);
        }
        
         if($money_kc>0){
             $bonus->execute("UPDATE __TABLE__ SET b5=b5-".$money_kc."  WHERE id={$bid}");
            $this->addencAdd($myid,$inUserID,-$money_kc,5);
        }
          if($money_ka>0){
             $bonus->execute("UPDATE __TABLE__ SET b6=b6-".$money_ka."  WHERE id={$bid}");
            $this->addencAdd($myid,$inUserID,-$money_kc,6);
        }
unset($bonus);
        unset($fee,$fee_rs,$s9,$mrs);
    }
        

  public  function _getTimeTableList1($uid)
    {

        $fck = M ('fck');
        $bonus = M ('bonus');
        $times1 = M ('times1');
        $bonus1= M('bonus1');
        $boid = 0;
        $one = $this->where("id={$uid}")->field('id,user_id,is_cha')->find();
        $nowdate = strtotime(date('Y-m-d'));
        // $nowdate = time();
    
    
        $res=$one['is_cha'];
        $brs = $bonus1->where("uid={$uid}")->select();
     
        foreach ($brs as $key => $value) {
           $ar[].=$value['id'];
        }
       
if($res>0){
    if ($res==0){

            $arr=$ar;

             $this->execute("update __TABLE__ set  is_cha=0 where id=".$uid);
            return $arr;
        }else{


        
            $frs = $this->where("id={$uid}")->field('id,user_id')->find();
            $data = array();
            $data['did'] = $boid;
            $data['uid'] = $frs['id'];
            $data['user_id'] = $frs['user_id'];
            $data['e_date'] = $nowdate;
            $data['s_date'] = $nowdate;
            $data['nums']  =0;
            $data['re_nums']  =$res;
            $bid = $bonus1->add($data);
            $cc[].=$bid;

            
       
        if($ar[0]){
            $arr = array_merge($ar, $cc);
        }else{
            $arr=$cc;
        }
        
        
         $this->execute("update __TABLE__ set  is_cha=0 where id=".$uid);
            return $arr;
}


}


        if($res==0){
             $arr=$ar;
             $this->execute("update __TABLE__ set  is_cha=0 where id=".$uid);
             return $arr;


        }else{
            $frs = $this->where("id={$uid}")->field('id,user_id')->find();
            $data = array();
            $data['did'] = $boid;
            $data['uid'] = $frs['id'];
            $data['user_id'] = $frs['user_id'];
            $data['e_date'] = $benqi;
            $data['s_date'] = $shangqi;
            $data['re_nums']  =1;
            $bid = $bonus1->add($data);
            $cc[]=$bid;

        if($ar[0]){
            $arr = array_merge($ar, $cc);
        }else{
            $arr=$cc;
        }           
           $this->execute("update __TABLE__ set  is_cha=0 where id=".$uid);
            return $arr;


        }
    }





    public  function _getTimeTableList($uid)
    {
        $times = M ('times');
        $bonus = M ('bonus');
        $boid = 0;
        $nowdate = strtotime(date('Y-m-d'))+3600*24-1;
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







    //分红添加记录
    public function add_xf($one_prices=0,$cj_ss=0){
		$fenhong = M('fenhong');
		$data = array();
// 		$data['uid'] = 1;
// 		$data['user_id'] = $cj_ss;
		$data['f_num'] = $cj_ss;
		$data['f_money'] = $one_prices;
		$data['pdt'] = mktime();
		$fenhong->add($data);
		unset($fenhong,$data);
    }

	//日封顶
    public function ap_rifengding(){

    	$fee = M('fee');
    	$fee_rs = $fee->field('s7')->find();
    	$s7 = explode("|",$fee_rs['s7']);

    	$where=array();
    	$where['b8'] = array('gt',0);
    	$mrs=$this->where($where)->field('id,b8,day_feng,get_level')->select();
    	foreach($mrs as $vo){
    		$day_feng = $vo['day_feng'];
    		$ss = $vo['get_level'];
    		$bbb = $vo['b8'];
    		$fedd = $s7[$ss];//封顶
			$get_money = $bbb;
    		$all_money = $bbb+$day_feng;
    		$fdok = 0;
    		if($all_money>=$fedd){
    			$fdok = 1;
    			$get_money = $fedd-$day_feng;
    		}
    		if($get_money<0){
    			$get_money = 0;
    		}
    		if($get_money>=0){
    			$this->query("UPDATE __TABLE__ SET `b8`=".$get_money.",day_feng=day_feng+".$get_money." where `id`=".$vo['id']);
    		}
    		if($get_money>0){
    			if($fdok==1){
    				$this->query("UPDATE __TABLE__ SET x_num=x_num+1 where `id`=".$vo['id']);
    			}
    		}
    	}
    	unset($fee,$fee_rs,$s7,$where,$mrs);
    }

	//总封顶
    public function ap_zongfengding(){

    	$fee = M('fee');
    	$fee_rs = $fee->field('s15')->find();
    	$s15 = $fee_rs['s15'];

    	$where=array();
    	$where['b0'] = array('gt',0);
    	$where['_string'] = 'b0+zjj>'.$s15;
    	$mrs=$this->where($where)->field('id,b0,zjj')->select();
    	foreach($mrs as $vo){
    		$zjj = $vo['zjj'];
    		$bbb = $vo['b0'];
    		$get_money = $s15-$zjj;

    		if($get_money>0){
    			$this->query("UPDATE __TABLE__ SET `b0`=".$get_money." where `id`=".$vo['id']);
    		}
    	}
    	unset($mrs);
    }

	//奖金大汇总（包括扣税等）
    public function quanhuizong(){

    	$this->execute('UPDATE __TABLE__ SET `b0`=b1+b2+b3+b4+b5+b6+b7+b8');

    	$this->execute('UPDATE __TABLE__ SET `b0`=0,b1=0,b2=0,b3=0,b4=0,b5=0,b6=0,b7=0,b8=0,b9=0,b10=0 where is_fenh=1');

    }


    //清空时间
	public function emptyTime(){

		$nowdate = strtotime(date('Y-m-d'));

		$this->query("UPDATE `xt_fck` SET `is_fh`=0,_times=".$nowdate." WHERE _times !=".$nowdate."");

	}
	
	
	//清空月封顶
    public function emptyMonthTime(){  //zyq_date 记录当前月
    
        $nowmonth = date('m');
    
        $this->query("UPDATE `xt_fck` SET `agent_cf`=0,zyq_date=".$nowmonth." WHERE zyq_date !=".$nowmonth."");
    }
    
	public function gongpaixtsmall($uid){
		$fck = M ('fck');
		$mouid=$uid;
		$field = 'id,user_id,p_level,p_path,u_pai';
		$where = 'is_pay>0 and (p_path like "%,'.$mouid.',%" or id='.$mouid.')';
	
		$re_rs = $fck ->where($where)->order('p_level asc,u_pai asc')->field($field)->select();
		$fck_where = array();
		foreach($re_rs as $vo){
			$faid=$vo['id'];
			$fck_where['is_pay']   = array('egt',0);
			$fck_where['father_id']   = $faid;
			$count = $fck->where($fck_where)->count();
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
    public function bobifengding(){

		$fee = M ('fee');
		$bonus = M ('bonus');
		$fee_rs = M ('fee') -> find();
    	$table = $this->tablePrefix .'fck';
    	$z_money = 0;//总支出
        $z_money = $this->where('is_pay = 1')->sum('b2');
        $times = M ('times');
        $trs = $times->order('id desc')->field('shangqi')->find();
        if ($trs){
            $benqi = $trs['shangqi'];
        }else{
            $benqi = strtotime(date('Y-m-d'));
        }
        $zsr_money = 0;//总收入
        $zsr_money = $this->where('pdt>='. $benqi .' and is_pay=1')->sum('cpzj');
        $bl = $z_money / $zsr_money ;
        $fbl = $fee_rs['s11'] / 100;
        if ($bl > $fbl){
            //$bl = $fbl;
            //$xbl = $bl - $fbl;
            $z_o1=$zsr_money*$fbl;
            $z_o2=$z_o1/$z_money;
            $this->query("UPDATE ". $table ." SET `b2`=b2*{$z_o2} where `is_pay`>=1 ");
        }



    }



	
	//判断进入B网
    public function pd_into_websiteb($uid){
		//$fck = D ('fck');
		$fck=new FckModel('fck');
    	$fck2 = M ('fck2');
    	$where = "is_pay>0 and is_lock=0 and is_bb>=0 and id=".$uid;
    	$lrs = $fck->where($where)->field('id,user_id,re_id,user_name,nickname,u_level')->find();
    	if($lrs){
    		$myid = $lrs['id'];
    		$result = $fck->execute("update __TABLE__ set is_bb=is_bb+1 where id=".$myid." and is_bb>=0");
    		if($result){
    			$data=array();
    			$data['fck_id'] = $lrs['id'];
    			$data['re_num'] = $lrs['re_id'];
    			$data['user_id'] = $lrs['user_id'];
    			$data['user_name'] = $lrs['user_name'];
    			$data['nickname'] = $lrs['nickname'];
    			$data['u_level'] = $lrs['u_level'];
    			$data['ceng'] = 0;
    
    			$farr = $fck->gongpaixt_Two_big_B();
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
    				$fck->jiandianjiang_bb($ppath,$inUserID,$ulevel);
    			}
    		}
    	}
    	unset($fck2,$lrs,$where,$fck);
    }
	
}
?>