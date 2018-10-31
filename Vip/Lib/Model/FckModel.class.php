<?php

class FckModel extends CommonModel
{
    // 数据库名称
    public function xiangJiao($Pid = 0, $DanShu = 1)
    {
        //  往上统计单数
        $where = array();
        $where['id'] = $Pid;
        $field = 'treeplace,father_id';
        $vo = $this->where($where)
            ->field($field)
            ->find();
        if ($vo) {
            $Fid = $vo['father_id'];
            $TPe = $vo['treeplace'];
            $table = $this->tablePrefix . 'fck';
            if ($TPe == 0 && $Fid > 0) {
                $this->execute("update " . $table . " Set `l`=l+$DanShu, `shangqi_l`=shangqi_l+$DanShu  where `id`=" . $Fid);
            } elseif ($TPe == 1 && $Fid > 0) {
                $this->execute("update " . $table . " Set `r`=r+$DanShu, `shangqi_r`=shangqi_r+$DanShu  where `id`=" . $Fid);
            } elseif ($TPe == 2 && $Fid > 0) {
                $this->execute("update " . $table . " Set `lr`=lr+$DanShu, `shangqi_lr`=shangqi_lr+$DanShu  where `id`=" . $Fid);
            }
            if ($Fid > 0)
                $this->xiangJiao($Fid, $DanShu);
        }
        unset($where, $field, $vo);
    }

    public function shangjiaTJ($ppath, $treep = 0)
    {
        $where = "id in (0" . $ppath . "0)";
        $lirs = $this->where($where)
            ->order('p_level desc')
            ->field('id,treeplace')
            ->select();
        foreach ($lirs as $lrs) {
            $myid = $lrs['id'];
            $mytp = $lrs['treeplace'];
            if ($treep == 0) {
                $this->execute("update __TABLE__ Set `re_nums_l`=re_nums_l+1,`re_nums_b`=re_nums_b+1 where `id`=" . $myid);
            } else {
                $this->execute("update __TABLE__ Set `re_nums_r`=re_nums_r+1,`re_nums_b`=re_nums_b+1 where `id`=" . $myid);
            }
            $treep = $mytp;
        }
        unset($lirs, $lrs, $where);
    }
    
    // public function xiangJiao($Pid=0,$DanShu=1,$plv=0,$op=1){
    // //========================================== 往上统计单数【有层碰奖】
    //
    // $peng = M ('peng');
    // $where = array();
    // $where['id'] = $Pid;
    // $field = 'treeplace,father_id,p_level';
    // $vo = $this ->where($where)->field($field)->find();
    // if ($vo){
    // $Fid = $vo['father_id'];
    // $TPe = $vo['treeplace'];
    // $table = $this->tablePrefix .'fck';
    // $dt = strtotime(date("Y-m-d"));//现在的时间
    // if ($TPe == 0 && $Fid > 0){
    // $p_rs = $peng ->where("uid=$Fid and ceng = $op") ->find();
    // if($p_rs){
    // $peng->execute("UPDATE __TABLE__ SET `l`=l+{$DanShu} WHERE uid=$Fid and ceng = $op");
    // }else{
    // $peng->execute("INSERT INTO __TABLE__ (uid,ceng,l) VALUES ($Fid ,$op,$DanShu) ");
    // }
    //
    // $this->execute("UPDATE ". $table ." SET `l`=l+{$DanShu}, `benqi_l`=benqi_l+{$DanShu} WHERE `id`=".$Fid);
    // }elseif($TPe == 1 && $Fid > 0){
    // $p_rs = $peng ->where("uid=$Fid and ceng = $op") ->find();
    // if($p_rs){
    // $peng->execute("UPDATE __TABLE__ SET `r`=r+{$DanShu} WHERE uid=$Fid and ceng = $op");
    // }else{
    // $peng->execute("INSERT INTO __TABLE__ (uid,ceng,r) VALUES ($Fid,$op,$DanShu) ");
    // }
    // $this->execute("UPDATE ". $table ." SET `r`=r+{$DanShu}, `benqi_r`=benqi_r+{$DanShu} WHERE `id`=".$Fid);
    // }
    // $op++;-+*
    // if ($Fid > 0) $this->xiangJiao($Fid,$DanShu,$plv,$op);
    // }
    // unset($where,$field,$vo);
    // }
    public function addencAdd($ID = 0, $inUserID = 0, $money = 0, $name = null, $UID = 0, $time = 0, $acttime = 0, $bz = "", $agent_use = 0, $agent_cash = 0, $agent_xf = 0, $agent_active = 0)
    {
        // 添加 到数据表
        if ($UID > 0) {
            $where = array();
            $where['id'] = $UID;
            $frs = $this->where($where)
                ->field('nickname')
                ->find();
            $name_two = $name;
            $name = $frs['nickname'] . ' 开通会员 ' . $inUserID;
            $inUserID = $frs['nickname'];
        } else {
            $name_two = $name;
        }
        
        $data = array();
        $history = M('history');
        
        $data['user_id'] = $inUserID;
        $data['uid'] = $ID;
        $data['action_type'] = $name;
        if ($time > 0) {
            $data['pdt'] = $time;
        } else {
            $data['pdt'] = mktime();
        }
        $data['epoints'] = $money;
        if (! empty($bz)) {
            $data['bz'] = $bz;
        } else {
            $data['bz'] = $name;
        }
        $data['did'] = 0;
        $data['type'] = 1;
        $data['allp'] = 0;
        if ($acttime > 0) {
            $data['act_pdt'] = $acttime;
        }
        $data['agent_use'] = $agent_use;
        $data['agent_cash'] = $agent_cash;
        $data['agent_xf'] = $agent_xf;
        $data['agent_active'] = $agent_active;
        $result = $history->add($data);
        unset($data, $history);
    }
    
    /**
     * 添加到分红包数据表
     * @param 用户ID $uid
     * @param 用户名 $user_id
     * @param 分红开始时间 $adt
     * @param 分红截止时间 $pdt
     * @param 已分红金额 $money
     * @param 应分红金额 $fhMoney
     * @param 天数 $day
     * @param 复投月份 $ftMonth
     * @param action_type 0.注册复投，1.分红，2.补助，3.推荐奖金
     * @param is_pay 0.未支付，1.已支付
     */
    public function jiaDan($uid = 0, $user_id = 0, $adt = 0, $pdt = 0, $money = 0, $fhMoney = 0, $day = 0, $ftMonth = 0,$action_type = 0,$is_pay = 0)
    {
        $data = array();
        $jiadan = M('jiadan');
    
        $data['uid'] = $uid;
        $data['user_id'] = $user_id;
        // 复投时间
        $data['adt'] = $adt;
        // 出局时间
        $data['pdt'] = $pdt;
        // 已分红金额
        $data['money'] = $money;
        // 应分红金额
        $data['fhMoney'] = $fhMoney;
        // 复投月份
        $data['ftMonth'] = $ftMonth;
        // 分红天数
        $data['day'] = $day;
        $data['action_type'] = $action_type;
        $data['is_pay'] = $is_pay;
        
        $result = $jiadan->add($data);
        unset($data, $jiadan);
    }
    
    /**
     * 添加到B网分红包数据表
     * @param 用户ID $uid
     * @param 用户名 $user_id
     * @param 分红开始时间 $adt
     * @param 分红截止时间 $pdt
     * @param 已分红金额 $money
     * @param 单数 $danshu
     * @param 是否出局 $is_pay
     * @param 加单类型0：注册，1升级，2复投 $out_level
     */
    public function jiadanb($uid = 0, $user_id = 0, $adt = 0, $pdt = 0, $money = 0, $danshu = 0, $is_pay = 0, $out_level = 0)
    {
        $data = array();
        $jiadan = M('jiadanb');
    
        $data['uid'] = $uid;
        $data['user_id'] = $user_id;
        $data['adt'] = $adt;
        $data['pdt'] = $pdt;
        $data['money'] = $money;
        $data['danshu'] = $danshu;
        $data['is_pay'] = $is_pay;
        $data['out_level'] = $out_level;
    
        $result = $jiadan->add($data);
        unset($data, $jiadan);
    }

    public function huikuiAdd($ID = 0, $tz = 0, $zk, $money = 0, $nowdate = null)
    {
        // 添加 到数据表
        $data = array();
        $huikui = M('huikui');
        $data['uid'] = $ID;
        $data['touzi'] = $tz;
        $data['zhuangkuang'] = $zk;
        $data['hk'] = $money;
        $data['time_hk'] = $nowdate;
        $huikui->add($data);
        unset($data, $huikui);
    }
    
    // 对碰1：1
    public function touch1to1(&$Encash, $xL = 0, $xR = 0, &$NumS = 0)
    {
        $xL = floor($xL);
        $xR = floor($xR);
        
        if ($xL > 0 && $xR > 0) {
            if ($xL > $xR) {
                $NumS = $xR;
                $xL = $xL - $NumS;
                $xR = $xR - $NumS;
                $Encash['0'] = $Encash['0'] + $NumS;
                $Encash['1'] = $Encash['1'] + $NumS;
            }
            if ($xL < $xR) {
                $NumS = $xL;
                $xL = $xL - $NumS;
                $xR = $xR - $NumS;
                $Encash['0'] = $Encash['0'] + $NumS;
                $Encash['1'] = $Encash['1'] + $NumS;
            }
            if ($xL == $xR) {
                $NumS = $xL;
                $xL = 0;
                $xR = 0;
                $Encash['0'] = $Encash['0'] + $NumS;
                $Encash['1'] = $Encash['1'] + $NumS;
            }
            $Encash['2'] = $NumS;
        } else {
            $NumS = 0;
            $Encash['0'] = 0;
            $Encash['1'] = 0;
        }
    }
    // 静态分红
    public function fenhong()
    {
        $con=mysqli_connect("localhost","root","123qwe!@#","hlc");
        // 检查连接
        if (!$con) {     
               print 'Cant connect to MySQL Server'.mysqli_connect_error();     
            } 
        // str1每单分红金额 s5分红倍数
        $fee_sql = "select str1,s5 from xt_fee";
        $fee_rs=mysqli_fetch_assoc(mysqli_query($con,$fee_sql));
        $str1 = $fee_rs['str1'];
        $s5 = $fee_rs['s5'];
        $nowdate = strtotime ("now");
        // 查询会员表数据
        $fck_sql = "select id,is_fenh,u_level,re_nums,user_id,agent_sfw,agent_sf,agent_sfo,day_feng,f4,is_cc,tz_nums,month_tag from xt_fck where id>0 and is_pay=1 and is_day_active = 0";
        $fck_result = mysqli_query($con,$fck_sql);
        if (!empty($fck_result)) {
            // 获取数据
            $fck_Contents = mysqli_fetch_all($fck_result,MYSQLI_ASSOC);
            // 释放结果集
            mysqli_free_result($fck_result);
            // 循环取得会员表数据
            $kk = 0;
            $rc = 0;
            ini_set("max_execution_time", 0);
            foreach($fck_Contents as $key=>$value){
                // 待更新到会员表金钱
                $fck_money = 0;
                // 检索分红包表数据
                $jiadan_sql = "select * from xt_jiadan where is_pay = 1 and user_id='{$value['user_id']}'";
                $jiadan_rs = mysqli_query($con,$jiadan_sql);
                $jiadan_Contents = mysqli_fetch_all($jiadan_rs,MYSQLI_ASSOC);
                // 释放结果集
                mysqli_free_result($jiadan_rs);
                // 分红
                if (!empty($jiadan_rs) && $jiadan_Contents['ftMonth'] <= $value['month_tag']) {
                    // 循环取得分红包表数据
                    foreach ($jiadan_Contents as $k=>$v) {
                        $kk++;
                        if($v['action_type'] == 0) {
                            $tmpMoney1 = bcdiv(ceil(bcdiv($v['fhMoney'], $v['ftMonth'],2)),30,2);
                            if($tmpMoney1 + $v['money'] >= $v['fhMoney']) {
                                $tmpMoney1 = $v['fhMoney'] - $v['money'];
                                // 设置出局标志
                                $jiadan_updateSql = "update xt_jiadan set is_out=1,day=day+1,pdt={$nowdate},money=money+".$tmpMoney1." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            } else {
                                $jiadan_updateSql = "update xt_jiadan set day=day+1,money=money+".$tmpMoney1." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            }
                            $fck_money += $tmpMoney1;
                        } else if($v['action_type'] == 1) {
                            $tmpMoney2 = bcdiv(ceil(bcdiv($v['fhMoney'], 24,2)),30,2);
                            if($tmpMoney2 + $v['money'] >= $v['fhMoney']) {
                                $tmpMoney2 = $v['fhMoney'] - $v['money'];
                                // 设置出局标志
                                $jiadan_updateSql = "update xt_jiadan set is_out=1,day=day+1,pdt={$nowdate},money=money+".$tmpMoney2." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            } else {
                                $jiadan_updateSql = "update xt_jiadan set day=day+1,money=money+".$tmpMoney2." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            }
                            $fck_money += $tmpMoney2;
                        } else if ($v['action_type'] == 2) {
                            $tmpMoney3 = bcdiv($v['fhMoney'], 30,2);
                            if($tmpMoney3 + $v['money'] >= $v['fhMoney']) {
                                $tmpMoney3 = $v['fhMoney'] - $v['money'];
                                // 设置出局标志
                                $jiadan_updateSql = "update xt_jiadan set is_out=1,day=day+1,pdt={$nowdate},money=money+".$tmpMoney3." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            } else {
                                $jiadan_updateSql = "update xt_jiadan set day=day+1,money=money+".$tmpMoney3." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            }
                            $fck_money += $tmpMoney3;
                        } else if ($v['action_type'] == 3) {
                            $tmpMoney4 = bcdiv(ceil(bcdiv($v['fhMoney'], 24,2)),30,2);
                            if($tmpMoney4 + $v['money'] >= $v['fhMoney']) {
                                $tmpMoney4 = $v['fhMoney'] - $v['money'];
                                // 设置出局标志
                                $jiadan_updateSql = "update xt_jiadan set is_out=1,day=day+1,pdt={$nowdate},money=money+".$tmpMoney4." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            } else {
                                $jiadan_updateSql = "update xt_jiadan set day=day+1,money=money+".$tmpMoney4." where id=" . $v['id'];
                                mysqli_query($con,$jiadan_updateSql);
                            }
                            $fck_money += $tmpMoney4;
                        }
                    }
                    // 会员表ID
                    $myid = $value['id'];
                    // 会员表用户名
                    $inUserID = $value['user_id'];
                    if ($fck_money > 0) {
                        // str1每单分红金额 s5分红倍数
                        $fee_sql2 = "select s15,str10,str11,str7,s9 from xt_fee";
                        $fee_rs2=mysqli_fetch_assoc(mysqli_query($con,$fee_sql2));
                         
                        $s15 = $fee_rs2['s15'] / 100; // 现金币比例
                        $str10 = $fee_rs2['str10'] / 100; // 复投币比例
                        $str11 = $fee_rs2['str11'] / 100; // 公益基金比例
                        $str7 = $fee_rs2['str7'] / 100; // 平台管理费比例
                        $s9 = $fee_rs2['s9']; // 投资基数
                        // 现金币
                        $money_ka = 0;
                        // 复投币
                        $money_kb = 0;
                        // 公益基金
                        $money_kc = 0;
                        // 平台管理费
                        $money_kd = 0;
                        // 查询会员表数据
                        $fck_sql2 = "select agent_use,is_pp from xt_fck";
                        $one=mysqli_fetch_assoc(mysqli_query($con,$fck_sql2));
                        // 账户中现金币余额
                        $agent_use = $one['agent_use'];
                        // 扣除管理费
                        $money_kd = $fck_money * $str7;
                        $fck_money = $fck_money - $money_kd;
                        // 待转入现金币账户数据
                        $money_ka = $fck_money;
                        // 待转入公益基金数据
                        $money_kc = $money_kb * $str11;
                        // 待转入复投币账户数据
                        $money_kb = $fck_money * $str10 - $money_kc;
                        // 剩余，此值写入现金账户
                        $last_m = $fck_money;
                        // $myid为应得到奖金的会员ID
                        $boid = 0;
                        $bid = 0;
                        // 现在时间
                        $nowdate = strtotime(date('Y-m-d')) + 3600 * 24 - 1;
                        // 查询类型为0的时间表数据
                        $time_Sql = "select * from xt_times where benqi={$nowdate} and type = 0 limit 1";
                        $trs = mysqli_fetch_assoc(mysqli_query($con,$time_Sql));
                        // 如果当前时间，类型为0的数据不存在
                        if (! $trs) {
                            // 检索以前存在的类型为0的数据
                            $time_Sql2 = "select * from xt_times where type = 0 order by id desc limit 1";
                            $rs3 = mysqli_fetch_assoc(mysqli_query($con,$time_Sql2));
                            // 如果存在以前的时间记录
                            if ($rs3) {
                                // 把本期数据作为上期数据
                                $data['shangqi'] = $rs3['benqi'];
                                // 把现在时间作为本期数据
                                $data['benqi'] = $nowdate;
                                $data['is_count'] = 0;
                                $data['type'] = 0;
                                $times_InsertSql = "insert into xt_times (shangqi, benqi,is_count_b,is_count_c,is_count,type) VALUES ({$rs3['benqi']},{$nowdate},0,0,0,0)";
                                mysqli_query($con,$times_InsertSql);
                                $rc += mysqli_affected_rows($con);
                            } else {
                                // 如果存在以前的时间记录，也就是新纪录
                                $data['shangqi'] = strtotime('2010-01-01');
                                $data['benqi'] = $nowdate;
                                $data['is_count'] = 0;
                                $data['type'] = 0;
                                $times_InsertSql = "insert into xt_times (shangqi, benqi,is_count_b,is_count_c,is_count,type) VALUES ({$data['shangqi']},{$nowdate},0,0,0,0)";
                                mysqli_query($con,$times_InsertSql);
                                $rc += mysqli_affected_rows($con);
                            }
                            $shangqi = $data['shangqi'];
                            $benqi = $data['benqi'];
                        } else {
                            // 如果当前时间存在记录
                            $shangqi = $trs['shangqi'];
                            $benqi = $trs['benqi'];
                            $boid = $trs['id'];
                        }
                        $bonus_Sql2 = "select * from xt_bonus where uid={$myid} and did = {$boid} limit 1";
                        $brs = mysqli_fetch_assoc(mysqli_query($con,$bonus_Sql2));
                        if ($brs) {
                            $bid = $brs;
                        } else {
                            $fck_Sql4 = "select id,user_id from xt_fck where id={$myid} limit 1";
                            $frs = mysqli_fetch_assoc(mysqli_query($con,$fck_Sql4));
                            $data = array();
                            $data['did'] = $boid;
                            $data['uid'] = $frs['id'];
                            $data['user_id'] = $frs['user_id'];
                            $data['e_date'] = $benqi;
                            $data['s_date'] = $shangqi;
                            $bonus_InsertSql = "insert into xt_bonus (did, uid,user_id,e_date,s_date,b0,b1,b2,b3,b4,b5,b6,b7,b8,b9,b10,b11,b12,encash_l,encash_r,encash,is_count_b,is_count_c,is_pay,u_level,additional,encourage)
                            VALUES ('{$data['did']}','{$data['uid']}','{$data['user_id']}','{$data['e_date']}','{$data['s_date']}',0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0)";
                            mysqli_query($con,$bonus_InsertSql);
                            $bid = mysqli_fetch_assoc(mysqli_query($con,"SELECT id from xt_bonus where user_id = '{$data['user_id']}' and uid = '{$data['uid']}' order by e_date desc"));
                            $rc += mysqli_affected_rows($con);
                        }
                        $inbb = "b1";
                        // 待更新到现金账户数据
                        $usqlc = "agent_use=agent_use+" . $money_ka;
                        // 加到奖金记录表
                        $bonus_updateSql = "update xt_bonus set b0=b0+" . $last_m . "," . $inbb . "=" . $inbb . "+" .$fck_money." where id={$bid['id']}";
                        mysqli_query($con,$bonus_updateSql);
                        $rc += mysqli_affected_rows($con);
                        // 加到会员表
                        $fck_updateSql3 = "update xt_fck set " . $usqlc . ",day_feng=day_feng+" . $fck_money . ",agent_xf=agent_xf+" . $money_kb . ",agent_cf=agent_cf+" . $money_kc . " where id=" . $myid;
                        mysqli_query($con,$fck_updateSql3);
                        $rc += mysqli_affected_rows($con);
                        // 更新到货币历史记录表
                        $nowdate = strtotime ("now");
                        $history_InsertSql = "insert into xt_history (user_id, uid,action_type,pdt,epoints,bz,did,type,allp,user_did) VALUES ('{$inUserID}','{$myid}',1,'{$nowdate}','{$fck_money}',1,0,1,0,1)";
                        mysqli_query($con,$history_InsertSql);
                        $rc += mysqli_affected_rows($con);
                        // 平台管理费大于0历史记录表
                        if ($money_kd > 0) {
                            // 加到奖金记录表
                            $bonus_updateSql2 = "update xt_bonus set b9=b9+" . $money_kd . " where id={$bid['id']}";
                            mysqli_query($con,$bonus_updateSql2);
                            $rc += mysqli_affected_rows($con);
                            $nowdate = strtotime ("now");
                            $history_InsertSql2 = "insert into xt_history (user_id, uid,action_type,pdt,epoints,bz,did,type,allp) VALUES ('{$inUserID}','{$myid}',9,'{$nowdate}','{$money_kd}',9,0,1,0)";
                            mysqli_query($con,$history_InsertSql2);
                            $rc += mysqli_affected_rows($con);
                        }
                        $fck_updateSql4 = "update xt_fck set is_sf=1,tz_nums=tz_nums+{$fck_money} where id=" . $myid;
                        mysqli_query($con,$fck_updateSql4);
                        $rc += mysqli_affected_rows($con);
                    }
                }
                $jiadanDay_sql = "select day from xt_jiadan where user_id='{$value['user_id']}' and is_out = 0 order by day desc limit 1";
                $jiadanDay_rs = mysqli_query($con,$jiadanDay_sql);
                $jiadanDay_Contents = mysqli_fetch_assoc($jiadanDay_rs);
                // 释放结果集
                mysqli_free_result($jiadanDay_rs);
                // 月份标志
                $month_tag = ceil(bcdiv(($jiadanDay_Contents['day'] + 1), 30,2));
                if ($month_tag == null || $month_tag == 0) {
                    $month_tag = 1;
                }
                $jiadanMonth_sql = "select ftMonth from xt_jiadan where user_id='{$value['user_id']}' order by ftMonth desc limit 1";
                $jiadanMonth_rs = mysqli_query($con,$jiadanMonth_sql);
                $jiadanMonth_Contents = mysqli_fetch_assoc($jiadanMonth_rs);
                if ($jiadanMonth_Contents['ftMonth'] < $month_tag) {
                    $fck_updateSql5 = "update xt_fck set month_tag={$month_tag},is_day_active = 1 where id=" . $myid;
                } else {
                    $fck_updateSql5 = "update xt_fck set month_tag={$month_tag} where id=" . $myid;
                }
                
                mysqli_query($con,$fck_updateSql5);
        
                unset($jiadan_rs,$jiadan_Contents,$jd_danshu,$jd_money,$jd_oldMoney,$jd_sumMoney,$jd_updateSql,$money,$myid,$inUserID);
            }
        }

    }
    // 计算奖金
    public function getusjj($uid, $money = 0)
    {
        $mrs = $this->where('id=' . $uid)->find();
        if ($mrs) {
//             $this->tz($mrs['p_path'], $money);
//             $this->tuijj($mrs['re_path'], $mrs['user_id'], $money);
//             $this->jiandianjiang($mrs['p_path'], $mrs['user_id']);
//             // $this->pingheng($mrs['p_path'],$mrs['user_id'],$mrs['p_level']);
//             $this->lingdao22($mrs['p_path'], $mrs['user_id'], $money);
//             // $this->grade($mrs['p_path'],$mrs['user_id'],$money);
//             $this->sh_level($mrs['p_path']);
//             // $this->getLevel();
            
//             $this->baodanfei($mrs['shop_id'], $mrs['user_id'], $money, $mrs['is_agent']);
//             $this->dsfenhong($mrs['p_path'], $mrs['user_id'], $money);
            $nowdate = strtotime(date('c'));
            $this->jiaDan($mrs['re_id'], $mrs['re_name'], $nowdate, 0, 0, $money * 0.03 * 24, 0, $mrs['month_tag'], 3,1);
        }
        unset($mrs);
    }
    /**
     * 投资业绩统计
     * $p_path : 节点路径
     * $money  :金额
     * */
    public function tz($p_path, $money)
    {
        $fck = M('fck');
        $lirs = $this->where('id in (0' . $p_path . '0)')
            ->field('id,is_fenh,u_level,re_nums,user_id')
            ->order('rdt desc')
            ->select();
        foreach ($lirs as $key => $v) {
            // 统计业绩之后加到会员表
            $this->execute("update __TABLE__ set ach=ach+{$money},ach_s=ach_s+{$money} where id=" . $v['id']);
        }
    }
    
    /**
     * 直推奖 间推奖 隔推奖
     * $re_path : 推荐路径
     * $inUserID：当前正在投资的会员
     * $money  :金额
     * */
    public function tuijj($re_path = 0, $inUserID = 0, $money = 0)
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s11,s4')->find(1);
        // 直推奖比例参数分割
        $s11 = explode("|", $fee_rs['s11']);
        // 查询所有直推路径下的会员数据
        $lirs = $this->where('id in (0' . $re_path . '0)  and is_fenh=0 and is_pay=1')
            ->field('id,re_nums,is_fenh')
            ->order('id desc')
            ->select();
        $i = 0;
        // 循环分配奖金
        foreach ($lirs as $lrs) {
            $money_count = 0;
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            // 按照直推奖比例算出奖金
            $prii = $s11[$i] / 100;
            $money_count = bcmul($prii, $money, 2);
            if ($i == 0) {
                $mm = 2;
            }
            if ($i == 1) {
                $mm = 3;
            }
            if ($i == 2) {
                $mm = 4;
            }
            if ($money_count > 0 && $is_fenh == 0 && $i < 3) {
                // 2为直推奖 3为间推奖 4为隔推奖
                $this->rw_bonus($myid, $inUserID, $mm, $money_count);
            }
            $i ++;
        }
        unset($fee, $fee_rs, $s13, $lirs, $lrs);
    }
    
    /**
     * 见点奖
     * $p_path : 节点路径
     * $inUserID：当前正在投资的会员
     * $money  :金额
     * */
    public function jiandianjiang($p_path, $inUserID)
    {
        $fee = M('fee');
        // str5为见点奖比例 s13为见点奖层数
        $fee_rs = $fee->field('s13,str5')->find(1);
        $s13 = explode("|", $fee_rs['s13']);
        $str5 = $fee_rs['str5'];
        // 检索节点路径数据
        $lirs = $this->where('id in (0' . $p_path . '0)  and is_fenh=0 and is_pay=1')
            ->field('id,re_nums,is_fenh,re_nums')
            ->order('id desc')
            ->select();
        $i = 0;
        foreach ($lirs as $lrs) {
            $money_count = 0;
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            $re_nums = $lrs['re_nums'];
            // 推荐0-2人
            if ($re_nums >= 0 && $re_nums <= 2) {
                $k = $s13[0];
            }
            // 推荐3-4人
            if ($re_nums > 2 && $re_nums <= 4) {
                $k = $s13[1];
            }
            // 推荐5人以上
            if ($re_nums > 4) {
                $k = $s13[2];
            }
            // 见点奖比例
            $money_count = $str5;
            if ($k > $i && $is_fenh == 0) {
                // ID 用户名 见点奖标志 金额
                $this->rw_bonus($myid, $inUserID, 5, $money_count);
            }
            $i++;
        }
        unset($fee, $fee_rs, $s13, $lirs, $lrs);
    }

    public function pingheng($p_path, $inUserID, $p_level)
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s12,s14')->find(1);
        $s14 = $fee_rs['s14'];
        $s12 = $fee_rs['s12'];
        $lirs = $this->where('id in (0' . $p_path . '0) and is_fenh=0')
            ->field('id,re_nums,is_fenh,re_nums,p_level,is_p')
            ->order('id desc')
            ->select();
        $i = 0;
        foreach ($lirs as $lrs) {
            $money_count = 0;
            $myid = $lrs['id'];
            $is_p = $lrs['is_p'];
            $p_level2 = $lrs['p_level'];
            $is_fenh = $lrs['is_fenh'];
            $num = $p_level - $p_level2;
            $nums = pow(2, $num);
            $p_nums = $nums / 2;
            $count = $this->where('p_level =' . $p_level)->count();
            $money_count = $s12;
            if ($s14 > $i && $is_fenh == 0 && $is_p < $num && $count >= $p_nums) {
                
                $this->rw_bonus($myid, $inUserID, 4, $money_count);
                $this->execute("update __TABLE__ set is_p={$num} where id=" . $myid);
            }
            $i ++;
        }
        unset($fee, $fee_rs, $s13, $lirs, $lrs);
    }
    /**
     * 领导奖
     * $p_path ：节点路径
     * $inUserID：正在投资的会员ID
     * $money:投资的金额
     * **/
    public function lingdao22($p_path, $inUserID, $money)
    {
        $fee = M('fee');
        // 领导奖比例：合伙人，市场总监，市场监理，市场董事
        $fee_rs = $fee->field('s4')->find(1);
        $s4 = explode("|", $fee_rs['s4']);
        // 搜索节点路径下的会员数据
        $lirs = $this->where('id in (0' . $p_path . '0)  and is_fenh=0 and is_pay=1')
            ->field('id,re_nums,is_fenh,sh_level')
            ->order('id asc')
            ->select();
        // 循环分配奖金
        foreach ($lirs as $lrs) {
            $money_count = 0;
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            // 合伙人级别
            $sh_level = $lrs['sh_level'];
            $small_level = $this->where('id in (0' . $p_path . '0) and id>' . $myid . '')->max('sh_level');
            if ($small_level >= $sh_level) {
                continue;
            } else {
                $mm = $s4[$sh_level - 1] - $s4[$small_level - 1];
                $prii = $mm / 100;
            }
            // 根据投资金额领导奖比例算出应得金额
            $money_count = bcmul($money, $prii, 2);
            if ($money_count > 0 && $is_fenh == 0) {
                // 6为领导奖
                $this->rw_bonus($myid, $inUserID, 6, $money_count);
            }
        }
        unset($fee, $fee_rs, $s13, $lirs, $lrs);
    }
    /**
     * 复投领导奖
     * $p_path ：节点路径
     * $inUserID：正在投资的会员ID
     * $money:投资的金额
     * **/
    public function lingdao33($p_path, $inUserID, $money)
    {
        $fee = M('fee');
        // 领导奖比例：合伙人，市场总监，市场监理，市场董事
        $fee_rs = $fee->field('s4')->find(1);
        $s4 = explode("|", $fee_rs['s4']);
        // 搜索节点路径下的会员数据
        $lirs = $this->where('id in (0' . $p_path . '0)  and is_fenh=0 and is_pay=1')
        ->field('id,re_nums,is_fenh,sh_level')
        ->order('id asc')
        ->select();
        // 循环分配奖金
        foreach ($lirs as $lrs) {
            $money_count = 0;
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            // 合伙人级别
            $sh_level = $lrs['sh_level'];
            $small_level = $this->where('id in (0' . $p_path . '0) and id>' . $myid . '')->max('sh_level');
            if ($small_level == null) {
                $small_result = $this->where("user_id = '".$inUserID."'")->field('sh_level')->find();
                $small_level = $small_result['sh_level'];
            }
            if ($small_level >= $sh_level) {
                continue;
            } else {
                $mm = $s4[$sh_level - 1] - $s4[$small_level - 1];
                $prii = $mm / 100;
            }
            // 根据投资金额领导奖比例算出应得金额
            $money_count = bcmul($money, $prii, 2);
            if ($money_count > 0 && $is_fenh == 0) {
                $money_count = bcdiv($money_count, 2, 2);
                // 6为领导奖
                $this->rw_bonus($myid, $inUserID, 6, $money_count);
            }
        }
        unset($fee, $fee_rs, $s13, $lirs, $lrs);
    }
    // 
    /**
     * 报单奖
     * $uid ：
     * $inUserID：正在投资的会员ID
     * $cpzj:投资的金额
     * **/
    public function baodanfei($uid, $inUserID, $cpzj = 0)
    {
        $fee = M('fee');
        $fee_rs = $fee->field('str9,s7')->find();
        // 报单费比例
        $str9 = $fee_rs['str9'] / 100;
        // 报单推报单比例
        $s7 = $fee_rs['s7'] / 100;
        // 报单奖励金额
        $money_count = bcmul($cpzj, $str9, 2);
        // 检索报单中心数据
        $frs = $this->where('id=' . $uid . ' and is_pay=1 ')
            ->field('id,user_id,re_path,u_level,re_id')
            ->find();
        if ($frs) {
            $myid = $frs['id'];
            if ($money_count > 0) {
                // 7为报单中心
                $this->rw_bonus($myid, $inUserID, 7, $money_count);
            }
            // 报单中心的推荐人ID
            $uid = $frs['re_id'];
            $re_path = $frs['re_path'];
            $lirs = $this->where('id in (0' . $re_path . '0)  and is_fenh=0 and is_pay=1')->order('id desc')->select();
            // 循环检索报单中心上面的报单中心分配报单中心推报单中心的奖励
            foreach ($lirs as $lrs) {
                // 如果推荐人也为报单中心
                if ($lrs['is_agent'] == 2) {
                    // 报单中心推报单中心的奖励金额
                    $money = bcmul($cpzj, $s7, 2);
                    if ($money > 0) {
                        $this->rw_bonus($lrs['id'], $inUserID, 7, $money);
                        break;
                    }
                }
            }
            
        }
        unset($fee, $fee_rs, $frs, $s14);
    }
    // 董事分红
    public function dsfenhong($p_path, $inUserID, $money)
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s6')->find();
        // 董事分红比例
        $s6 = $fee_rs['s6'] / 100;
        // 查询所有级别达到全国董事的会员数据
        $lirs = $this->where('is_fenh=0 and is_pay=1 and sh_level=5')
            ->field('id,re_nums,is_fenh,sh_level')
            ->order('id desc')
            ->select();
        // 查询级别为全国董事的人员数量
        $count = $this->where('sh_level =5')->count();
        // 算出董事分红的金额
        $money_a = bcmul($money, $s6, 2);
        // 算出全国董事应得的加权平均分红的金额
        $money_count = bcdiv($money_a, $count, 2);
        foreach ($lirs as $lrs) {
            // 对应全国董事的ID
            $myid = $lrs['id'];
            $is_fenh = $lrs['is_fenh'];
            if ($money_count > 0 && $is_fenh == 0) {
                // myid 是全国董事对应的ID,   8为全国董事分红的key，$money_count全国董事应分红的金额
                $this->rw_bonus($myid, $inUserID, 8, $money_count);
            }
        }
        unset($fee, $fee_rs, $s13, $lirs, $lrs);
    }
    
    /**
     * 方法功能：检测是否达到对应领导级别，达到则提升至对应级别
     * **/
    public function sh_level($p_path)
    {
        // 取得会员数据
        $list = $this->where('id in (0' . $p_path . '0)')
            ->field('id,ach,sh_level,p_path')
            ->order('id asc')
            ->select();
        $jiadan = M("jiadan");
        foreach ($list as $key => $value) {
            // id
            $myid = $value['id'];
            // 领导等级
            $sh_level = $value['sh_level'];
            // 节点路径
            $p_path = $value['p_path'];
            // 团队总业绩
            $ach = $value['ach'];
            if (empty($ach)) {
                $ach = 0.00;
            }
//             // 左右区各有一个推荐
//             $countleft = 0;
//             $countright = 0;
//             $tree0 = $this->where('treeplace=0 and father_id=' . $myid)->field('id,user_id,re_id')->find();
//             $tree1 = $this->where('treeplace=1 and father_id=' . $myid)->field('id,user_id,re_id')->find();
//             if ($tree0['re_id'] == $myid) {
//                 $jiadan0Tmp = $jiadan->where("uid=".$tree0["id"]." and is_pay = 0")->select();
//                 if ($jiadan0Tmp) {
//                     $countleft += 1;
//                 } else {
//                     $tree0Result = $this->where("p_path like '%,{$tree0['id']},%' and re_id=" . $myid)->field("id,user_id")->select();
//                     if ($tree0Result) {
//                         foreach ($tree0Result as $value) {
//                             $jiadan0Tmp = $jiadan->where("uid=".$value["id"]." and is_pay = 0")->select();
//                             if ($jiadan0Tmp) {
//                                 $countleft += 1;
//                             }
//                         }
//                     }
//                 }
//             } else {
//                 $tree0Result = $this->where("p_path like '%,{$tree0['id']},%' and re_id=" . $myid)->field("id,user_id")->select();
//                 if ($tree0Result) {
//                     foreach ($tree0Result as $value) {
//                         $jiadan0Tmp = $jiadan->where("uid=".$value["id"]." and is_pay = 0")->select();
//                         if ($jiadan0Tmp) {
//                             $countleft += 1;
//                         }
//                     }
//                 }
//             }
//             if ($tree1['re_id'] == $myid) {
//                 $jiadan1Tmp = $jiadan->where("uid=".$tree1["id"]." and is_pay = 0")->select();
//                 if ($jiadan1Tmp) {
//                     $countright += 1;
//                 } else {
//                     $tree1Result = $this->where("p_path like '%,{$tree1['id']},%' and re_id=" . $myid)->field("id,user_id")->select();
//                     if ($tree1Result > 0) {
//                         foreach ($tree1Result as $value) {
//                             $jiadan1Tmp = $jiadan->where("uid=".$value["id"]." and is_pay = 0")->select();
//                             if ($jiadan1Tmp) {
//                                 $countright += 1;
//                             }
//                         }
//                     }
//                 }
//             } else {
//                 $tree1Result = $this->where("p_path like '%,{$tree1['id']},%' and re_id=" . $myid)->field("id,user_id")->select();
//                 if ($tree1Result > 0) {
//                     foreach ($tree1Result as $value) {
//                         $jiadan1Tmp = $jiadan->where("uid=".$value["id"]." and is_pay = 0")->select();
//                         if ($jiadan1Tmp) {
//                             $countright += 1;
//                         }
//                     }
//                 }
//             }
            // 推荐人数 >= 2
            $re_count = $this->where('re_id=' . $myid)->count();
//             if ($countleft >= 1 && $countright >= 1) {
            if ($re_count >= 2) {
                // 合伙人
                if ($sh_level < 1) {
//                     $one11 = $this->where('treeplace=0 and father_id=' . $myid)
//                         ->field('id,user_id,ach')
//                         ->find();
//                     $nowdate = strtotime(date('Y-m-d'));
//                     $ach1 = $this->where('p_path like "%,' . $one11['id'] . ',%" and is_pay=1 and pdt<' . $nowdate)->sum('cpzj');
//                     if (empty($ach1)) {
//                         $ach1 = 0.00;
//                     }
//                     $one22 = $this->where('treeplace=1 and father_id=' . $myid)
//                         ->field('id,user_id,ach')
//                         ->find();
//                     $ach2 = $this->where('p_path like "%,' . $one22['id'] . ',%" and is_pay=1 and pdt<' . $nowdate)->sum('cpzj');
//                     if (empty($ach2)) {
//                         $ach2 = 0.00;
//                     }
                    if ($ach >= 160000) {
                        $this->execute("update __TABLE__ set sh_level=1,sh_one=sh_one+1 where id=" . $myid);
                        $this->execute("update __TABLE__ set sh_one=sh_one+1 where id in (0" . $p_path . "0)");
                    }
                    unset($one11, $one22, $ach2, $ach1);
                }
                
                // 市场总监
                else if ($value['sh_level'] < 2) {
                    $one11 = $this->where('treeplace=0 and father_id=' . $myid)
                        ->field('id,user_id,sh_one')
                        ->find();
                    $sh_one1 = $one11['sh_one'];
                    $one22 = $this->where('treeplace=1 and father_id=' . $myid)
                        ->field('id,user_id,sh_one')
                        ->find();
                    $sh_one2 = $one22['sh_one'];
                    if ($sh_one1 > 0 && $sh_one2 > 0) {
                        $this->execute("update __TABLE__ set sh_level=2,sh_two=sh_two+1 where id=" . $myid);
                        $this->execute("update __TABLE__ set sh_two=sh_two+1 where id in (0" . $p_path . "0)");
                    }
                    unset($one11, $one22,$sh_one1,$sh_one2);
                }
                // 市场监理
                else if ($value['sh_level'] < 3) {
                    $one11 = $this->where('treeplace=0 and father_id=' . $myid)
                        ->field('id,user_id,sh_two')
                        ->find();
                    $sh_two1 = $one11['sh_two'];
                    $one22 = $this->where('treeplace=1 and father_id=' . $myid)
                        ->field('id,user_id,sh_two')
                        ->find();
                    $sh_two2 = $one22['sh_two'];
                    $shnums = $sh_two1 + $sh_two2;
                    if ($sh_two1 > 0 && $sh_two2 > 0 && $shnums > 2) {
                        $this->execute("update __TABLE__ set sh_level=3,sh_three=sh_three+1 where id=" . $myid);
                        $this->execute("update __TABLE__ set sh_three=sh_three+1 where id in (0" . $p_path . "0)");
                    }
                    unset($one11, $one22,$sh_two1,$sh_two2,$shnums);
                }
                // 市场董事
                else if ($value['sh_level'] < 4) {
                    $one11 = $this->where('treeplace=0 and father_id=' . $myid)
                        ->field('id,user_id,sh_three')
                        ->find();
                    $sh_three1 = $one11['sh_three'];
                    $one22 = $this->where('treeplace=1 and father_id=' . $myid)
                        ->field('id,user_id,sh_three')
                        ->find();
                    $sh_three2 = $one22['sh_three'];
                    $shThreeNums = $sh_three1 + $sh_three2;
                    if ($sh_three1 > 1 && $sh_three2 > 1 && $shThreeNums > 2) {
                        $this->execute("update __TABLE__ set sh_level=4,sh_four=sh_four+1 where id=" . $myid);
                        $this->execute("update __TABLE__ set sh_four=sh_four+1 where id in (0" . $p_path . "0)");
                    }
                    unset($one11, $one22,$sh_three1,$sh_three2,$shThreeNums);
                }
                // 全国董事
                else if ($value['sh_level'] < 5) {
                    $one11 = $this->where('treeplace=0 and father_id=' . $myid)
                        ->field('id,user_id,sh_four,ach')
                        ->find();
                    $sh_four1 = $one11['sh_four'];
                    $ach1 = $one11['ach'];
                    if (empty($ach1)) {
                        $ach1 = 0.00;
                    }
                    $one22 = $this->where('treeplace=1 and father_id=' . $myid)
                        ->field('id,user_id,sh_four,ach')
                        ->find();
                    $sh_four2 = $one22['sh_four'];
                    $ach2 = $one22['ach'];
                    if (empty($ach2)) {
                        $ach2 = 0.00;
                    }
                    $miannums = min($ach1, $ach2);
                    $shnums22 = $sh_four1 + $sh_four2;
                    if ($sh_four1 > 1 && $sh_four2 > 1 && $shnums22 > 2) {
                        $this->execute("update __TABLE__ set sh_level=5 where id=" . $myid);
                    }
                    unset($one11, $one22,$sh_four1,$sh_four2,$ach1,$ach2,$nowdate,$miannums,$shnums22);
                }
                // $count11=$this->where('father_id ='.$uuid1.' and is_pay>=1')->count();
                // $count22=$this->where('father_id ='.$uuid2.' and is_pay>=1')->count();
            } else {
                // 重置领导人级别
                $this->execute("update __TABLE__ set sh_level=0 where id=" . $myid);
            }
        }
    }
    
    // 统计单数
    public function countnums($ppath)
    {
        $lirs = $this->where('id in (0' . $ppath . '0)')
            ->field('id')
            ->order('p_level desc')
            ->select();
        foreach ($lirs as $lrs) {
            $myid = $lrs['id'];
            $this->execute("update __TABLE__ set p_nums=p_nums+1 where id=" . $myid);
        }
    }
    
    // 出局奖
    public function out()
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s1,s7')->find(1);
        $s1 = $fee_rs['s1'];
        $s7 = $fee_rs['s7'];
        $list = $this->where('is_path=0 and is_fenh=0')
            ->field('id,re_id,user_id,re_path,p_path')
            ->order('id asc')
            ->select();
        foreach ($list as $key => $value) {
            $myid = $value['id'];
            $count = $this->where('father_id =' . $myid . ' and is_pay>=1')->count();
            if ($count == 2) {
                $one11 = $this->where('treeplace=0 and father_id=' . $myid)
                    ->field('id,user_id,p_nums')
                    ->find();
                $uuid1 = $one11['id'];
                $one22 = $this->where('treeplace=1 and father_id=' . $myid)
                    ->field('id,user_id,p_nums')
                    ->find();
                $uuid2 = $one22['id'];
                $count11 = $this->where('father_id =' . $uuid1 . ' and is_pay>=1')->count();
                $count22 = $this->where('father_id =' . $uuid2 . ' and is_pay>=1')->count();
                if ($count11 >= 2 && $count22 >= 2) {
                    $money_count = $s1;
                    if ($money_count > 0) {
                        $this->rw_bonus($value['id'], $value['user_id'], 3, $money_count);
                        $this->outnums($value['p_path']);
                    }
                    $one = $this->where('id=' . $value['re_id'])
                        ->field('id,user_id')
                        ->find();
                    $money = $s7;
                    if ($money > 0) {
                        $this->rw_bonus($one['id'], $value['user_id'], 3, $money);
                    }
                    $this->execute("update __TABLE__ set is_path=1,outnums=outnums+1 where id=" . $value['id']);
                }
            }
        }
    }
    // 统计出局人数
    public function outnums($re_path)
    {
        $lirs = $this->where('id in (0' . $re_path . '0)')
            ->field('id')
            ->order('id desc')
            ->select();
        foreach ($lirs as $lrs) {
            $myid = $lrs['id'];
            $this->execute("update __TABLE__ set outnums=outnums+1 where id=" . $myid);
        }
    }
    
    // 升级
    public function getLevel()
    {
        $list = $this->where('re_nums>4')
            ->field('id,re_id,user_id,sh_level,re_path,re_nums,father_id,p_path')
            ->order('id asc')
            ->select();
        
        foreach ($list as $key => $value) {
            $myid = $value['id'];
            $p_path = $value['p_path'];
            // 一级合伙人
            if ($value['sh_level'] == 0) {
                $count = $this->where('father_id =' . $myid . '  and outnums>0')->count();
                if ($count > 1 && $value['re_nums'] > 4) {
                    $this->execute("update __TABLE__ set sh_level=1,sh_one=sh_one+1 where id=" . $myid);
                    
                    $lirs = $this->where('id in (0' . $p_path . '0)')
                        ->field('id')
                        ->order('id desc')
                        ->select();
                    foreach ($lirs as $lrs) {
                        $uid = $lrs['id'];
                        $this->execute("update __TABLE__ set sh_one=sh_one+1 where id=" . $uid);
                    }
                    unset($lirs, $uid);
                }
            }
            // 二级合伙人
            if ($value['re_nums'] > 4 && $value['sh_level'] < 2) {
                $count = $this->where('father_id =' . $myid . ' and sh_one>4')->count();
                if ($count > 1) {
                    $this->execute("update __TABLE__ set sh_level=2,sh_two=sh_two+1 where id=" . $myid);
                    
                    $lirs = $this->where('id in (0' . $p_path . '0)')
                        ->field('id')
                        ->order('id desc')
                        ->select();
                    foreach ($lirs as $lrs) {
                        $uid = $lrs['id'];
                        $this->execute("update __TABLE__ set sh_two=sh_two+1 where id=" . $uid);
                    }
                    unset($lirs, $uid);
                }
            }
            // 三级合伙人
            if ($value['re_nums'] > 4 && $value['sh_level'] < 3) {
                $count = $this->where('father_id =' . $myid . '  and sh_two>4')->count();
                if ($count > 1) {
                    $this->execute("update __TABLE__ set sh_level=3,sh_three=sh_three+1 where id=" . $myid);
                    $lirs = $this->where('id in (0' . $p_path . '0)')
                        ->field('id')
                        ->order('id desc')
                        ->select();
                    foreach ($lirs as $lrs) {
                        $uid = $lrs['id'];
                        $this->execute("update __TABLE__ set sh_three=sh_three+1 where id=" . $uid);
                    }
                    unset($lirs, $uid);
                }
            }
            // 四级合伙人
            if ($value['re_nums'] > 4 && $value['sh_level'] < 4) {
                $count = $this->where('father_id =' . $myid . '  and sh_three>4')->count();
                if ($count > 1) {
                    $this->execute("update __TABLE__ set sh_level=4,sh_four=sh_four+1 where id=" . $myid);
                    $lirs = $this->where('id in (0' . $p_path . '0)')
                        ->field('id')
                        ->order('id desc')
                        ->select();
                    foreach ($lirs as $lrs) {
                        $uid = $lrs['id'];
                        $this->execute("update __TABLE__ set sh_four=sh_four+1 where id=" . $uid);
                    }
                    unset($lirs, $uid);
                }
            }
            
            // 五级合伙人
            if ($value['re_nums'] > 40 && $value['sh_level'] < 5) {
                $count = $this->where('re_id =' . $myid . '  and sh_four>4')->count();
                if ($count > 1) {
                    $this->execute("update __TABLE__ set sh_level=5 where id=" . $myid);
                }
            }
        }
    }

    public function shangpin()
    {
        $fee = M('Fee');
        $gouwu = M('gouwu');
        $address = M('address');
        $fee_rs = $fee->field('s1,s9')->find(1);
        $s1 = $fee_rs['s1'];
        $s9 = $fee_rs['s9'];
        $list = $this->where('agent_cf>=' . $s1)
            ->field('*')
            ->select();
        foreach ($list as $key => $v) {
            $this->execute("update __TABLE__ set agent_cf=agent_cf-{$s1} where id=" . $v['id']);
            $ars = $address->where('id=' . $v['id'])->find();
            $gwd = array();
            $gwd['uid'] = $v['id'];
            $gwd['user_id'] = $v['user_id'];
            $gwd['lx'] = 1;
            $gwd['ispay'] = 0;
            $gwd['pdt'] = mktime();
            $gwd['us_name'] = $v['user_name'];
            ;
            $gwd['us_address'] = $ars['address'];
            $gwd['us_tel'] = $ars['tel'];
            $gwd['did'] = 赠送;
            $gwd['money'] = $s9;
            $gwd['shu'] = 1;
            $gwd['cprice'] = $s9;
            // $gwd['countid'] = ;
            
            $gouwu->add($gwd);
        }
    }

    public function fenxiao($repath, $user_id)
    {
        $fee = M('Fee');
        
        $fee_rs = $fee->field('s6,s5,s12,s4')->find(1);
        $s6 = $fee_rs['s6'];
        $s5 = $fee_rs['s5'];
        $s12 = $fee_rs['s12'];
        $s4 = $fee_rs['s4'];
        $lirs = $this->where('id in (0' . $repath . '0)')
            ->field('id,is_fenh,u_level,is_fenh,re_nums,user_id')
            ->order('id desc')
            ->select();
        $i = 0;
        
        foreach ($lirs as $k => $v) {
            
            if ($i == 0 && $v['is_fenh'] == 0) {
                $money = $s6;
                $this->rw_bonus($v['id'], $user_id, 1, $money);
            }
            if ($i == 1 && $v['is_fenh'] == 0) {
                $money = $s12;
                $this->rw_bonus($v['id'], $user_id, 2, $money);
            }
            if ($i == 2 && $v['is_fenh'] == 0) {
                $money = $s5;
                $this->rw_bonus($v['id'], $user_id, 3, $money);
            }
            
            if ($i > 2 && $v['is_fenh'] == 0 && $i < 15) {
                $money = $s4;
                
                $this->rw_bonus($v['id'], $user_id, 4, $money);
            }
            
            $i ++;
        }
    }

    public function daili($s_province, $s_city, $s_county, $user_id, $money)
    {
        $fee = M('Fee');
        $fee_rs = $fee->field('s4,s7,s11')->find(1);
        $s4 = $fee_rs['s4']; // 省
        $s7 = $fee_rs['s7']; // 市
        $s11 = $fee_rs['s11'] / 100; // 县
        
        $list = $this->where('u_level>3')
            ->field('id,s_county,s_city,s_province,is_fenh,u_level')
            ->select();
        
        foreach ($list as $key => $value) {
            if ($value['is_fenh'] == 0) {
                $myid = $value['id'];
                
                if ($value['s_county'] == $s_county && $value['u_level'] == 4) {
                    
                    $money_count = bcmul($money, $s11, 2);
                    $this->rw_bonus($myid, $user_id, 7, $money_count);
                }
            }
        }
    }
    
    // 对碰奖
    public function duipeng($ppath, $level)
    {
        $fee = M('fee');
        $fee_rs = $fee->field('str1,s11,s1')->find(1);
        $s1 = explode("|", $fee_rs['s1']); // 各级对碰比例
        $s11 = $fee_rs['s11']; // 各级对碰比例
        
        $str1 = explode("|", $fee_rs['str1']); // 封顶
        $one_mm = $s11;
        // $fck_array = 'is_pay>=1 and (shangqi_l>0 or shangqi_r>0) and id in (0'.$ppath.'0)';
        $fck_array = 'is_pay>=1 and  id in (0' . $ppath . '0)';
        $field = '*';
        $frs = $this->where($fck_array)
            ->field($field)
            ->select();
        // BenQiL BenQiR ShangQiL ShangQiR
        foreach ($frs as $vo) {
            $list = $this->where('father_id=' . $vo['id'])
                ->order('id asc')
                ->select();
            
            $l_ach = $list[0]['ach'] + $list[0]['cpzj'];
            $r_ach = $list[1]['ach'] + $list[1]['cpzj'];
            $l_nums = $list[0]['l_nums'];
            $r_nums = $list[1]['r_nums'];
            
            $lo = $l_ach / $s11;
            $ro = $r_ach / $s11;
            $lo = floor($lo);
            $ro = floor($ro);
            $L = 0;
            $R = 0;
            
            $L = ($l_ach - $l_nums * $s11) / $s11;
            $R = ($r_ach - $r_nums * $s11) / $s11;
            $L = floor($L);
            $R = floor($R);
            
            // $L = $vo['shangqi_l'];
            // $R = $vo['shangqi_r'];
            $sq_l = $vo['shangqi_l'];
            $sq_r = $vo['shangqi_r'];
            $z_date = $vo['z_date'];
            $Encash = array();
            $NumS = 0; // 碰数
            $money = 0; // 对碰奖金额
            $Ls = 0; // 左剩余
            $Rs = 0; // 右剩余
            $this->touch1to1($Encash, $lo, $ro, $NumS);
            $Ls = $lo - $Encash['0'];
            $Rs = $ro - $Encash['1'];
            $myid = $vo['id'];
            $myusid = $vo['user_id'];
            $myulv = $vo['u_level'];
            $ss = $myulv - 1;
            $feng = $vo['day_feng'];
            $is_fenh = $vo['is_fenh'];
            $reid = $vo['re_id'];
            $repath = $vo['re_path'];
            $relevel = $vo['re_level'];
            $ul = $s1[$ss] / 100;
            $aa = $str1[$ss];
            $money = $one_mm * $NumS * $ul; // 对碰奖 奖金
            if ($money > $aa) {
                $money = $aa;
            }
            if ($feng >= $aa) {
                $money = 0;
            } else {
                $jfeng = $feng + $money;
                if ($jfeng > $aa) {
                    $money = $aa - $feng;
                }
            }
            // echo $Ls;
            // $result = $this->query('UPDATE __TABLE__ SET `shangqi_l`='. $Ls .',peng_num=peng_num+' . $NumS . ',`shangqi_r`='. $Rs .' where `id`='. $vo['id'].' and shangqi_l='.$sq_l.' and shangqi_r='.$sq_r);
            $result = $this->query('UPDATE __TABLE__ SET `ls`=' . $lo . ',`l_nums`=' . $Ls . ',`rs`=' . $ro . ',peng_num=peng_num+' . $NumS . ',`r_nums`=' . $Rs . ' where `id`=' . $vo['id']);
            $money_count = $money;
            if ($money_count > 0) {
                $this->rw_bonus($myid, $myusid, 2, $money_count);
                $this->jjc($myusid, $money_count);
                
                if ($z_date == 0) {
                    $time = time();
                    $this->query('UPDATE __TABLE__ SET  `z_date`=' . $time . ' where `id`=' . $vo['id']);
                }
                
                // 领导奖
                // $this->guanglij($repath,$myusid,$money_count);
                // 互助奖
                // $this->Huzhufenhong($myid,$relevel,$myusid,$money_count);
            }
        }
        unset($fee, $fee_rs, $frs, $vo);
    }
    
    // 领导奖
    public function lingdaojiang($repath, $inUserID = 0, $money = 0)
    {
        $fee = M('fee');
        // s4为领导奖 s5分红倍数 s7报单推报单 str6充值账户
        $fee_rs = $fee->field('s4,s5,s7,str6')->find(1);
        $s5 = explode("|", $fee_rs['s5']);
        $s7 = $fee_rs['s7'];
        $str6 = $fee_rs['str6'];
        $s4 = $fee_rs['s4'];
        // 给上5代
        $lirs = $this->where('id in (0' . $repath . '0)')
            ->field('id,is_fenh,u_level,re_nums,user_id,fh_nums,c_date')
            ->order('rdt desc')
            ->select();
        $i = 0;
        foreach ($lirs as $lrs) {
            $money_count = 0;
            $myid = $lrs['id'];
            
            $uLevel = $lrs['u_level'];
            $myusid = $lrs['user_id'];
            $feng = $lrs['fh_nums'];
            $c_date = $lrs['c_date'];
            $k = $s7;
            if ($k > $i && $uLevel >= 4) {
                $prii = $s5[$i] / 100;
                $money_count = bcmul($prii, $s4, 2);
                
                if ($feng >= $str6) {
                    $money_count = 0;
                } else {
                    $jfeng = $feng + $money_count;
                    if ($jfeng > $str6) {
                        $money_count = $str6 - $feng;
                    }
                }
                
                if ($money_count > 0) {
                    $this->rw_bonus($myid, $inUserID, 4, $money_count);
                    $this->jjc($myusid, $money_count);
                    
                    if ($c_date == 0) {
                        $time = time();
                        $this->query('UPDATE __TABLE__ SET  `c_date`=' . $time . ' where `id`=' . $lrs['id']);
                    }
                }
            }
            $i ++;
        }
        unset($lirs, $lrs);
        unset($fee, $fee_rs);
    }

    public function svip($ppath, $level)
    {
        $lirs = $this->where('id in (0' . $ppath . '0)')
            ->field('id,is_fenh,u_level,re_nums,user_id')
            ->order('rdt desc')
            ->select();
        foreach ($lirs as $key => $v) {
            if ($level >= 4) {
                $this->execute("update __TABLE__ set vip4=vip4+1 where id=" . $v['id']);
            }
            if ($level >= 5) {
                $this->execute("update __TABLE__ set vip4=vip4+1, vip5=vip5+1 where id=" . $v['id']);
            }
            
            if ($level >= 5) {
                $this->execute("update __TABLE__ set vip4=vip4+1, vip5=vip5+1 where id=" . $v['id']);
            }
            
            if ($level >= 9) {
                $this->execute("update __TABLE__ set vip4=vip4+1, vip5=vip5+1, vip6=vip6+1 where id=" . $v['id']);
            }
        }
    }
    
    public function getReid($id)
    {
        $rs = $this->where('id=' . $id)
            ->field('id,re_nums,is_fenh')
            ->find();
        return array(
            're_id' => $rs['id'],
            're_nums' => $rs['re_nums'],
            'is_fenh' => $rs['is_fenh']
        );
    }
    
    /**
     * 各种扣税以及奖金结算
     * @param 应得到奖金的会员ID $myid
     * @param 正在投资的会员ID $inUserID
     * @param 奖金类别 $bnum
     * @param 投资金额 $money_count
     * @param  $corid
     */
    public function rw_bonus($myid, $inUserID = 0, $bnum = 0, $money_count = 0, $corid = 0)
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s15,str10,str11,str7,s9')->find();
        
        $s15 = $fee_rs['s15'] / 100; // 现金币比例
        $str10 = $fee_rs['str10'] / 100; // 复投币比例
        $str11 = $fee_rs['str11'] / 100; // 公益基金比例
        $str7 = $fee_rs['str7'] / 100; // 平台管理费比例
        $s9 = $fee_rs['s9']; // 投资基数
        
        // 现金币
        $money_ka = 0;
        // 复投币
        $money_kb = 0;
        // 公益基金
        $money_kc = 0;
        // 平台管理费
        $money_kd = 0;
        
        // 查询会员表数据
        $one = $this->where('id=' . $myid)->field('agent_use,is_pp,net_status')->find();
        // 账户中现金币余额
        $agent_use = $one['agent_use'];
        // 扣除管理费
        $money_kd = $money_count * $str7;
        $money_count = $money_count - $money_kd;
        // 根据奖金类型，判断奖金分配 1为静态分红，234为直推奖，间推奖，隔推奖，5为见点奖，6为领导奖，7为报单奖，8为全国董事分红
        if ($bnum < 7) {
            // 如果现金币余额小于投资基础金额
//             if ($agent_use < $s9) {
//                 // 待转入现金币账户数据
//                 $money_ka = $money_count * $s15;
//                 // 待转入复投币账户数据
//                 $nums = $money_count * $str10;
//                 // 待转入公益基金数据
//                 $money_kc = $nums * $str11;
//                 // 复投币 - 公益基金
//                 $money_kb = $nums - $money_kc;
//             } else {
//                 // 现金币临时数据
//                 $nums = $money_count * $s15;
//                 // 待转入复投币账户数据
//                 $money_kb = $money_count * $str10;
//                 // 待转入公益基金数据
//                 $money_kc = $nums * $str11;
//                 // 现金币：复投币 - 公益基金
//                 $money_ka = $nums - $money_kc;
//             }
            // 待转入现金币账户数据
            $money_ka = $money_count * $s15;
            // 待转入公益基金数据
            $money_kc = $money_kb * $str11;
            // 待转入复投币账户数据
            $money_kb = $money_count * $str10 - $money_kc;

        } else {
            // 报单费直接进入现金账户
            $money_ka = $money_count;
        }
        
        $last_m = $money_count; // 剩余，此值写入现金账户
        
        $bonus = M('bonus');
        // $myid为应得到奖金的会员ID
        $bid = $this->_getTimeTableList($myid);
        $inbb = "b" . $bnum;
        // 待更新到现金账户数据
        $usqlc = "agent_use=agent_use+" . $money_ka;
        // 加到奖金记录表
        $bonus->execute("UPDATE __TABLE__ SET b0=b0+" . $last_m . "," . $inbb . "=" . $inbb . "+" . $money_count . "  WHERE id={$bid}");
        // 加到会员表
        if ($one['net_status'] == 'b') {
            $netb = M('netb');
            $netb->execute("update xt_netB set agent_futou=agent_futou+" . $money_kb . " where id=" . $myid);
            $this->execute("update __TABLE__ set " . $usqlc . ",day_feng=day_feng+" . $money_count . ",agent_cf=agent_cf+" . $money_kc . " where id=" . $myid);
        } else {
            $this->execute("update __TABLE__ set " . $usqlc . ",day_feng=day_feng+" . $money_count . ",agent_xf=agent_xf+" . $money_kb . ",agent_cf=agent_cf+" . $money_kc . " where id=" . $myid);
        }
        // 如果金额大于0，更新到货币历史记录表
        if ($money_count > 0) {
            $this->addencAdd($myid, $inUserID, $money_count, $bnum);
        }
        
        // 平台管理费大于0更新到会员表以及历史记录表
        if ($money_kd > 0) {
            $bonus->execute("UPDATE __TABLE__ SET b9=b9+" . $money_kd . "  WHERE id={$bid}");
            $this->addencAdd($myid, $inUserID, $money_kd, 9);
        }
        
        unset($bonus);
        unset($fee, $fee_rs, $s9, $mrs);
    }
    /**
     * 每项奖金加到奖金记录表
     * @param unknown $uid
     */
    public function _getTimeTableList($uid)
    {
        $times = M('times');
        // 奖金表
        $bonus = M('bonus');
        $boid = 0;
        // 现在时间
        $nowdate = strtotime(date('Y-m-d')) + 3600 * 24 - 1;
        // $nowdate = strtotime(date('Y-m-d'))+3600*12;
        // 本期时间设置为现在时间
        $settime_two['benqi'] = $nowdate;
        $settime_two['type'] = 0;
        // 查询类型为0的时间表数据
        $trs = $times->where($settime_two)->find();
        // 如果当前时间，类型为0的数据不存在
        if (! $trs) {
            // 检索以前存在的类型为0的数据
            $rs3 = $times->where('type=0')->order('id desc')->find();
            // 如果存在以前的时间记录
            if ($rs3) {
                // 把本期数据作为上期数据
                $data['shangqi'] = $rs3['benqi'];
                // 把现在时间作为本期数据
                $data['benqi'] = $nowdate;
                $data['is_count'] = 0;
                $data['type'] = 0;
            } else {
                // 如果存在以前的时间记录，也就是新纪录
                $data['shangqi'] = strtotime('2010-01-01');
                $data['benqi'] = $nowdate;
                $data['is_count'] = 0;
                $data['type'] = 0;
            }
            $shangqi = $data['shangqi'];
            $benqi = $data['benqi'];
            unset($rs3);
            // 更新到时间记录表
            $boid = $times->add($data);
            unset($data);
        } else {
            // 如果当前时间存在记录
            $shangqi = $trs['shangqi'];
            $benqi = $trs['benqi'];
            $boid = $trs['id'];
        }
        $_SESSION['BONUSDID'] = $boid;
        $brs = $bonus->where("uid={$uid} AND did={$boid}")->find();
        if ($brs) {
            $bid = $brs['id'];
        } else {
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
    
    // 分红添加记录
    public function add_xf($one_prices = 0, $cj_ss = 0)
    {
        $fenhong = M('fenhong');
        $data = array();
        $data['f_num'] = $cj_ss;
        $data['f_money'] = $one_prices;
        $data['pdt'] = mktime();
        $fenhong->add($data);
        unset($fenhong, $data);
    }
    
    // 日封顶
    public function ap_rifengding()
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s7')->find();
        $s7 = explode("|", $fee_rs['s7']);
        
        $where = array();
        $where['b8'] = array(
            'gt',
            0
        );
        $mrs = $this->where($where)
            ->field('id,b8,day_feng,get_level')
            ->select();
        foreach ($mrs as $vo) {
            $day_feng = $vo['day_feng'];
            $ss = $vo['get_level'];
            $bbb = $vo['b8'];
            $fedd = $s7[$ss]; // 封顶
            $get_money = $bbb;
            $all_money = $bbb + $day_feng;
            $fdok = 0;
            if ($all_money >= $fedd) {
                $fdok = 1;
                $get_money = $fedd - $day_feng;
            }
            if ($get_money < 0) {
                $get_money = 0;
            }
            if ($get_money >= 0) {
                $this->query("UPDATE __TABLE__ SET `b8`=" . $get_money . ",day_feng=day_feng+" . $get_money . " where `id`=" . $vo['id']);
            }
            if ($get_money > 0) {
                if ($fdok == 1) {
                    $this->query("UPDATE __TABLE__ SET x_num=x_num+1 where `id`=" . $vo['id']);
                }
            }
        }
        unset($fee, $fee_rs, $s7, $where, $mrs);
    }
    
    // 总封顶
    public function ap_zongfengding()
    {
        $fee = M('fee');
        $fee_rs = $fee->field('s15')->find();
        $s15 = $fee_rs['s15'];
        
        $where = array();
        $where['b0'] = array(
            'gt',
            0
        );
        $where['_string'] = 'b0+zjj>' . $s15;
        $mrs = $this->where($where)
            ->field('id,b0,zjj')
            ->select();
        foreach ($mrs as $vo) {
            $zjj = $vo['zjj'];
            $bbb = $vo['b0'];
            $get_money = $s15 - $zjj;
            
            if ($get_money > 0) {
                $this->query("UPDATE __TABLE__ SET `b0`=" . $get_money . " where `id`=" . $vo['id']);
            }
        }
        unset($mrs);
    }
    
    // 奖金大汇总（包括扣税等）
    public function quanhuizong()
    {
        $this->execute('UPDATE __TABLE__ SET `b0`=b1+b2+b3+b4+b5+b6+b7+b8');
        
        $this->execute('UPDATE __TABLE__ SET `b0`=0,b1=0,b2=0,b3=0,b4=0,b5=0,b6=0,b7=0,b8=0,b9=0,b10=0 where is_fenh=1');
    }
    
    // 清空时间
    public function emptywTime()
    {
        // 当前日期
        $sdefaultDate = date("Y-m-d");
        // $first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $first = 1;
        // 获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $w = date('w', strtotime($sdefaultDate));
        // 获取本周开始日期，如果$w是0，则表示周日，减去 6 天
        // $week_start=date('Y-m-d',strtotime("$sdefaultDate -".($w ? $w - $first : 6).' days'));
        $week_strt = strtotime("$sdefaultDate -" . ($w ? $w - $first : 6) . ' days');
        
        $this->query("UPDATE `xt_fck` SET `is_fh`=0,`is_sf`=0,_times=" . $week_strt . " WHERE _times !=" . $week_strt . "");
    }
    // 清空时间
    public function emptyTime()
    {
        $nowdate = strtotime(date('Y-m-d'));
        
        $this->query("UPDATE `xt_fck` SET `is_fh`=0,`is_sf`=0,_times=" . $nowdate . " WHERE _times !=" . $nowdate . "");
    }
    
    // 清空月封顶
    public function emptyMonthTime()
    { // zyq_date 记录当前月
        $nowmonth = date('m');
        
        $this->query("UPDATE `xt_fck` SET `agent_cf`=0,zyq_date=" . $nowmonth . " WHERE zyq_date !=" . $nowmonth . "");
    }

    public function gongpaixtsmall($uid)
    {
        $fck = M('fck');
        $mouid = $uid;
        $field = 'id,user_id,p_level,p_path,u_pai';
        $where = 'is_pay>0 and (p_path like "%,' . $mouid . ',%" or id=' . $mouid . ')';
        
        $re_rs = $fck->where($where)
            ->order('p_level asc,u_pai asc')
            ->field($field)
            ->select();
        $fck_where = array();
        foreach ($re_rs as $vo) {
            $faid = $vo['id'];
            $fck_where['is_pay'] = array(
                'egt',
                0
            );
            $fck_where['father_id'] = $faid;
            $count = $fck->where($fck_where)->count();
            if (is_numeric($count) == false) {
                $count = 0;
            }
            if ($count < 2) {
                $father_id = $vo['id'];
                $father_name = $vo['user_id'];
                $TreePlace = $count;
                $p_level = $vo['p_level'] + 1;
                $p_path = $vo['p_path'] . $vo['id'] . ',';
                $u_pai = $vo['u_pai'] * 2 + $TreePlace;
                
                $arry = array();
                $arry['father_id'] = $father_id;
                $arry['father_name'] = $father_name;
                $arry['treeplace'] = $TreePlace;
                $arry['p_level'] = $p_level;
                $arry['p_path'] = $p_path;
                $arry['u_pai'] = $u_pai;
                return $arry;
                break;
            }
        }
    }

    public function bobifengding()
    {
        $fee = M('fee');
        $bonus = M('bonus');
        $fee_rs = M('fee')->find();
        $table = $this->tablePrefix . 'fck';
        $z_money = 0; // 总支出
        $z_money = $this->where('is_pay = 1')->sum('b2');
        $times = M('times');
        $trs = $times->order('id desc')
            ->field('shangqi')
            ->find();
        if ($trs) {
            $benqi = $trs['shangqi'];
        } else {
            $benqi = strtotime(date('Y-m-d'));
        }
        $zsr_money = 0; // 总收入
        $zsr_money = $this->where('pdt>=' . $benqi . ' and is_pay=1')->sum('cpzj');
        $bl = $z_money / $zsr_money;
        $fbl = $fee_rs['s11'] / 100;
        if ($bl > $fbl) {
            // $bl = $fbl;
            // $xbl = $bl - $fbl;
            $z_o1 = $zsr_money * $fbl;
            $z_o2 = $z_o1 / $z_money;
            $this->query("UPDATE " . $table . " SET `b2`=b2*{$z_o2} where `is_pay`>=1 ");
        }
    }
    
    // 判断进入B网
    public function pd_into_websiteb($uid)
    {
        // $fck = D ('fck');
        $fck = new FckModel('fck');
        $fck2 = M('fck2');
        $where = "is_pay>0 and is_lock=0 and is_bb>=0 and id=" . $uid;
        $lrs = $fck->where($where)
            ->field('id,user_id,re_id,user_name,nickname,u_level')
            ->find();
        if ($lrs) {
            $myid = $lrs['id'];
            $result = $fck->execute("update __TABLE__ set is_bb=is_bb+1 where id=" . $myid . " and is_bb>=0");
            if ($result) {
                $data = array();
                $data['fck_id'] = $lrs['id'];
                $data['re_num'] = $lrs['re_id'];
                $data['user_id'] = $lrs['user_id'];
                $data['user_name'] = $lrs['user_name'];
                $data['nickname'] = $lrs['nickname'];
                $data['u_level'] = $lrs['u_level'];
                $data['ceng'] = 0;
                
                $farr = $fck->gongpaixt_Two_big_B();
                $data['father_id'] = $farr['father_id'];
                $data['father_name'] = $farr['father_name'];
                $data['treeplace'] = $farr['treeplace'];
                $data['p_level'] = $farr['p_level'];
                $data['p_path'] = $farr['p_path'];
                $data['u_pai'] = $farr['u_pai'];
                $data['is_pay'] = 1;
                $data['pdt'] = time();
                $ress = $fck2->add($data); // 添加
                $ppath = $data['p_path'];
                $inUserID = $data['user_id'];
                $ulevel = $data['u_level'];
                unset($data, $farr);
                if ($ress) {
                    // b网见点
                    $fck->jiandianjiang_bb($ppath, $inUserID, $ulevel);
                }
            }
        }
        unset($fck2, $lrs, $where, $fck);
    }
}

?>