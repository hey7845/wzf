<?php
/*
    zy
    2018年3月27日
*/
$con=mysqli_connect("localhost","root","123qwe!@#","myproject");
// 检查连接
if (!$con) {     
       print 'Cant connect to MySQL Server'.mysqli_connect_error();     
    } 
// str1每单分红金额 s5分红倍数
$fee_sql = "select str1,s5 from xt_fee";
$fee_rs=mysqli_fetch_assoc(mysqli_query($con,$fee_sql));
$str1 = $fee_rs['str1'];
$s5 = $fee_rs['s5'];
$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
$nowdate = strtotime ("now");
print 'today is :' + $beginToday;
print "\r\n";
$history_sql = "select * from xt_history where pdt > {$beginToday} and action_type=1";
print 'sql is :';
print "\r\n";
print $history_sql;
print "\r\n";
$history_result = mysqli_query($con,$history_sql);
if (mysqli_num_rows($history_result) < 1) {
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
                    print "fck_updateSql4:{$fck_updateSql4}\r\n";
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
} else {
    print "Today is excuted";
    print "\r\n";
}

?>