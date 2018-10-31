<?php

class YouZiAction extends CommonAction
{

    function _initialize()
    {
        // $this->_inject_check(1);//调用过滤函数
        $this->_inject_check(0); // 调用过滤函数
        $this->_checkUser();
        $this->_Admin_checkUser(); // 后台权限检测
        $this->_Config_name(); // 调用参数
        header("Content-Type:text/html; charset=utf-8");
    }
    // ================================================二级验证
    public function cody()
    {
        $UrlID = (int) $_GET['c_id'];
        if (empty($UrlID)) {
            $this->error('二级密码错误!');
            exit();
        }
        if (! empty($_SESSION['user_pwd2'])) {
            $url = __URL__ . "/codys/Urlsz/$UrlID";
            $this->_boxx($url);
            exit();
        }
        $cody = M('cody');
        $list = $cody->where("c_id=$UrlID")->field('c_id')->find();
        if ($list) {
            $this->assign('vo', $list);
            $this->display('Public:cody');
            exit();
        } else {
            $this->error('二级密码错误!');
            exit();
        }
    }
    // ====================================二级验证后调转页面
    public function codys()
    {
        $Urlsz = $_POST['Urlsz'];
        if (empty($_SESSION['user_pwd2'])) {
            $pass = $_POST['oldpassword'];
            $fck = M('fck');
            if (! $fck->autoCheckToken($_POST)) {
                $this->error('页面过期请刷新页面!');
                exit();
            }
            if (empty($pass)) {
                $this->error('二级密码错误!');
                exit();
            }
            $where = array();
            $where['id'] = $_SESSION[C('USER_AUTH_KEY')];
            $where['passopen'] = md5($pass);
            $list = $fck->where($where)
                ->field('id')
                ->find();
            if ($list == false) {
                $this->error('二级密码错误!');
                exit();
            }
            $_SESSION['user_pwd2'] = 1;
        } else {
            $Urlsz = $_GET['Urlsz'];
        }
        switch ($Urlsz) {
            case 1:
                $_SESSION['UrlPTPass'] = 'MyssShenShuiPuTao';
                $bUrl = __URL__ . '/auditMenber'; // 审核会员
                $this->_boxx($bUrl);
                break;
            case 2:
                $_SESSION['UrlPTPass'] = 'MyssGuanShuiPuTao';
                $bUrl = __URL__ . '/adminMenber'; // 会员管理
                $this->_boxx($bUrl);
                break;
            case 3:
                $_SESSION['UrlPTPass'] = 'MyssPingGuoCP';
                $bUrl = __URL__ . '/setParameter'; // 参数设置
                $this->_boxx($bUrl);
                break;
            case 4:
                $_SESSION['UrlPTPass'] = 'MyssPingGuo';
                $bUrl = __URL__ . '/adminParameter'; // 比例设置
                $this->_boxx($bUrl);
                break;
            case 5:
                $_SESSION['UrlPTPass'] = 'MyssMiHouTao';
                $bUrl = __URL__ . '/adminFinance'; // 拨出比例
                $this->_boxx($bUrl);
                break;
            case 6:
                $_SESSION['UrlPTPass'] = 'MyssGuanPaoYingTao';
                $bUrl = __URL__ . '/adminCurrency'; // 提现管理
                $this->_boxx($bUrl);
                break;
            case 7:
                $_SESSION['UrlPTPass'] = 'MyssHaMiGua';
                $bUrl = __APP__ . '/Backup/'; // 数据库管理
                $this->_boxx($bUrl);
                break;
            case 8:
                $_SESSION['UrlPTPass'] = 'MyssPiPa';
                $bUrl = __URL__ . '/adminFinanceTableShow'; // 奖金查询
                $this->_boxx($bUrl);
                break;
            case 9:
                $_SESSION['UrlPTPass'] = 'MyssQingKong';
                $bUrl = __URL__ . '/delTable'; // 清空数据
                $this->_boxx($bUrl);
                break;
            case 10:
                $_SESSION['UrlPTPass'] = 'MyssGuanXiGua';
                $bUrl = __URL__ . '/adminAgents'; // 复投审核
                $this->_boxx($bUrl);
                break;
            case 11:
                $_SESSION['UrlPTPass'] = 'MyssBaiGuoJS';
                $bUrl = __URL__ . '/adminClearing'; // 奖金结算
                $this->_boxx($bUrl);
                break;
            case 12:
                $_SESSION['UrlPTPass'] = 'MyssGuanMangGuo';
                $bUrl = __URL__ . '/adminCurrencyRecharge'; // 充值管理
                $this->_boxx($bUrl);
                break;
            case 13:
                $_SESSION['UrlPTPass'] = 'MyssGuansingle';
                $bUrl = __URL__ . '/adminsingle'; // 加单管理
                $this->_boxx($bUrl);
                break;
            case 17:
                $_SESSION['UrlPTPass'] = 'MyssGuancash';
                $bUrl = __URL__ . '/adminCash'; // 加单管理
                $this->_boxx($bUrl);
                break;
            case 18:
                $_SESSION['UrlPTPass'] = 'MyssMoneyFlows';
                $bUrl = __URL__ . '/adminmoneyflows'; // 财务流向管理
                $this->_boxx($bUrl);
                break;
            case 19:
                $_SESSION['UrlPTPass'] = 'MyssadminMenberJL';
                $bUrl = __URL__ . '/adminMenberJL';
                $this->_boxx($bUrl);
                break;
            case 21:
                $_SESSION['UrlPTPass'] = 'MyssGuanXiGuaUp';
                $bUrl = __URL__ . '/adminUserUp'; // 升级管理
                $this->_boxx($bUrl);
                break;
            case 22:
                $_SESSION['UrlPTPass'] = 'MyssPingGuoCPB';
                $bUrl = __URL__ . '/setParameter_B';
                $this->_boxx($bUrl);
                break;
            case 23:
                $_SESSION['UrlPTPass'] = 'MyssOrdersList';
                $bUrl = __URL__ . '/OrdersList'; // 加单管理
                $this->_boxx($bUrl);
                break;
            case 24:
                $_SESSION['UrlPTPass'] = 'MyssWuliuList';
                $bUrl = __URL__ . '/adminLogistics'; // 物流管理
                $this->_boxx($bUrl);
                break;
//             case 25:
//                 $_SESSION['UrlPTPass'] = 'MyssGuanXiGuaJB';
//                 $bUrl = __URL__ . '/adminJB'; // 金币中心管理
//                 $this->_boxx($bUrl);
//                 break;
            case 25:
                $_SESSION['UrlPTPass'] = 'bonusCheck';
                $bUrl = __URL__ . '/bonusCheck';
                $this->_boxx($bUrl);
                break;
            case 26:
                $_SESSION['UrlPTPass'] = 'MyssGuanChanPin';
                $bUrl = __URL__ . '/pro_index'; // 产品管理
                $this->_boxx($bUrl);
                break;
            case 27:
                $_SESSION['UrlPTPass'] = 'MyssGuanzy';
                $bUrl = __URL__ . '/admin_zy'; // 专营店管理
                $this->_boxx($bUrl);
                break;
            case 28:
                $_SESSION['UrlPTPass'] = 'MyssShenqixf';
                $bUrl = __URL__ . '/adminXiaofei'; // 消费申请
                $this->_boxx($bUrl);
                break;
            case 29:
                $_SESSION['UrlPTPass'] = 'MyssJinji';
                $bUrl = __URL__ . '/adminmemberJJ'; // 晋级
                $this->_boxx($bUrl);
                break;
            case 30:
                $_SESSION['UrlPTPass'] = 'Myssadminlookfhall';
                $bUrl = __URL__ . '/adminlookfhall';
                $this->_boxx($bUrl);
                break;
            case 31:
                $_SESSION['UrlPTPass'] = 'MyssadminMenberXls';
                $bUrl = __URL__ . '/adminMenberXls';
                $this->_boxx($bUrl);
                break;
            default:
                $this->error('二级密码错误!');
                break;
        }
    }
    
    // =====================================================奖金查询(所有期所有会员)
    public function adminFinanceTable()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssPiPa') {
            
            $this->check_prem('adminFinanceTable');
            $bonus = M('bonus'); // 奖金表
            $fee = M('fee'); // 参数表
            $times = M('times'); // 结算时间表
            
            $fee_rs = $fee->field('s18,s13')->find();
            $fee_s7 = explode('|', $fee_rs['s13']);
            $this->assign('fee_s7', $fee_s7); // 输出奖项名称数组
            
            $where = array();
            $sql = '';
            if (isset($_REQUEST['FanNowDate'])) { // 日期查询
                if (! empty($_REQUEST['FanNowDate'])) {
                    $time1 = strtotime($_REQUEST['FanNowDate']); // 这天 00:00:00
                    $time2 = strtotime($_REQUEST['FanNowDate']) + 3600 * 24 - 1; // 这天 23:59:59
                    $sql = "where e_date >= $time1 and e_date <= $time2";
                }
            }
            
            $sql2 = "where 1";
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = count($bonus->query("select id from __TABLE__ " . $sql . " group by did")); // 总记录数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'FanNowDate=' . $_REQUEST['FanNowDate']; // 分页条件
            if (! empty($page_where)) {
                $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            } else {
                $Page = new ZQPage($count, $listrows, 1, 0, 3);
            }
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $status_rs = ($Page->getPage() - 1) * $listrows;
            $list = $bonus->query("select e_date,did,sum(b0) as b0,sum(b8) as b8,sum(b9) as b9,sum(b10) as b10,sum(b11) as b11,sum(b12) as b12,sum(b13) as b13,sum(b14) as b14,sum(b15) as b15,sum(b16) as b16,sum(b17) as b17,sum(b18) as b18,sum(b19) as b19,sum(b20) as b20 from __TABLE__ " . $sql2 . " group by did  order by did desc limit " . $status_rs . "," . $listrows);
            
            foreach ($list as $key => $value) {
                for ($i = 8; $i < 50; $i ++) {
                    if ($value['b' . $i] != 0) {
                        $this->assign('b' . $i, $value['b' . $i]);
                        $this->assign('id', $value['did']);
                    }
                }
            }
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
                                         
            // 各项奖每页汇总
            $count = array();
            foreach ($list as $vo) {
                for ($b = 0; $b <= 100; $b ++) {
                    $count[$b] += $vo['b' . $b];
                    $count[$b] = $this->_2Mal($count[$b], 2);
                }
            }
            
            // 奖项名称与显示
            
            $this->assign('b_b', $b_b);
            $this->assign('c_b', $c_b);
            $this->assign('count', $count);
            
            // 输出扣费奖索引
            $this->assign('ind', 7); // 数组索引 +1
            
            $this->display('adminFinanceTable');
        } else {
            $this->error('错误');
            exit();
        }
    }
    
    // =====================================================奖金检测(所有期所有会员)
    public function bonusCheck()
    {
        // 奖金检测
        if ($_SESSION['UrlPTPass'] == 'bonusCheck') {
            // 会员表
            $fck = M('fck');
            // 奖金历史记录表
            $history = M('history');
            // 开始日期
            $sDate = $_REQUEST['S_Date'];
            // 结束日期
            $eDate = $_REQUEST['E_Date'];
            // 用户名
            $UserID = $_REQUEST['UserID'];
            // tp为奖金筛选条件
            $ss_type = (int) $_REQUEST['tp'];
            $map['_string'] = "1=1";
            // **************日期判断开始************************
            $s_Date = 0;
            $e_Date = 0;
            if (!empty($sDate)) {
                $s_Date = strtotime($sDate);
            } else {
                $sDate = "2000-01-01";
            }
            if (!empty($eDate)) {
                $e_Date = strtotime($eDate);
            } else {
                $eDate = date("Y-m-d");
            }
            if ($s_Date > $e_Date && $e_Date > 0) {
                $temp_d = $s_Date;
                $s_Date = $e_Date;
                $e_Date = $temp_d;
            }
            if ($s_Date > 0) {
                $map['_string'] .= " and pdt>=" . $s_Date;
            }
            if ($e_Date > 0) {
                $e_Date = $e_Date + 3600 * 24 - 1;
                $map['_string'] .= " and pdt<=" . $e_Date;
            }
            // **************日期判断结束************************
            // 判断筛选何种奖金
            if ($ss_type > 0) {
                if ($ss_type == 15) {
                    $map['action_type'] = array('lt',7);
                } else {
                    $map['action_type'] = array('eq',$ss_type);
                }
            }
            if ($ss_type == 1) {
                // 静态奖检测
                
            } else if ($ss_type == 2) {
                // 直推奖检测
                $fee = M('fee'); // 参数表
                $fee_rs = $fee->field('s11')->find();
                // 直推奖比例
                $fee_s11 = explode('|', $fee_rs['s11']);
                // 检索全部历史数据
                $map['action_type'] = $ss_type;
                if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                
                unset($KuoZhan);
                $history_rs = $history->where('action_type = 2')->field('user_id')->select();
                foreach ($history_rs as $historyValue){
                    // 历史记录表的注册会员
                    $historyUserId = $historyValue['user_id'];
                    $where = array();
                    if (!empty($UserID)) {
                        $where['user_id'] = array('eq',$UserID);
                    } else {
                        $where['user_id'] = array('eq',$historyUserId);
                    }
                    $usrs = $fck->where($where)->field('id,user_id,re_id,cpzj')->find();
                    if ($usrs) {
                        $usid = $usrs['id'];
                        $usuid = $usrs['user_id'];
                        // 推荐人ID
                        $reid = $usrs['re_id'];
                        // 投资金额
                        $cpzj = $usrs['cpzj'];
                        // 直推奖
                        $directBonus = $cpzj * $fee_s11[0];
                        $map['action_type'] = $ss_type;
                        $map['_string'] .= " and (uid=" . $usid . " or user_id='" . $usuid . "')";
                    } else {
                        $map['_string'] .= " and id=0";
                    }
                    unset($where, $usrs);
                    $UserID = urlencode($UserID);
                }
            }
            $this->assign('S_Date', $sDate);
            $this->assign('E_Date', $eDate);
            $this->assign('ry', $ss_type);
            $this->assign('UserID', $UserID);
                // 查询字段
                $field = '*';
                // =====================分页开始==============================================
                import("@.ORG.ZQPage"); // 导入分页类
                $count = $history->where($map)->count(); // 总页数
                $listrows = 20; // 每页显示的记录数
                $page_where = 'UserID=' . $UserID . '&S_Date=' . $sDate . '&E_Date=' . $eDate . '&tp=' . $ss_type; // 分页条件
                $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
                // ===============(总页数,每页显示记录数,css样式 0-9)
                $show = $Page->show(); // 分页变量
                $this->assign('page', $show); // 分页变量输出到模板
                $list = $history->where($map)->field($field)->order('pdt desc,id desc')->page($Page->getPage() . ',' . $listrows)->select();
                
                $this->assign('list', $list); // 数据输出到模板
                // =======================分页结束===========================================
                
                $fee = M('fee'); // 参数表
                $fee_rs = $fee->field('s18')->find();
                // 静态奖|直推奖|推荐奖|隔推奖|管理奖|领导奖|报单奖|董事分红|管理费
                $fee_s7 = explode('|', $fee_rs['s18']);
                $this->assign('fee_s7', $fee_s7); // 输出奖项名称数组
                $this->display();
            } else if ($ss_type == 3) {
                // 推荐奖检测
            } else if ($ss_type == 4) {
                // 隔推奖检测
                
            } else if ($ss_type == 5) {
                // 管理奖检测
            } else if ($ss_type == 6) {
                // 领导奖检测
            } else if ($ss_type == 7) {
                //报单奖检测
            } else if ($ss_type == 8) {
                // 董事分红检测
            } else if ($ss_type == 9) {
                // 管理费检测
            }
            
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // =====================================================查询这一期得奖会员资金
    public function adminFinanceTableShow()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao') {
            $this->check_prem('adminFinanceTableShow');
            $bonus = M('bonus'); // 奖金表
            $fee = M('fee'); // 参数表
            $times = M('times'); // 结算时间表
            
            $fee_rs = $fee->field('s18,s13')->find();
            $fee_s7 = explode('|', $fee_rs['s13']);
            $this->assign('fee_s7', $fee_s7); // 输出奖项名称数组
            
            $UserID = $_REQUEST['UserID'];
            
            $where = array();
            $sql = '';
            
            $did = (int) $_REQUEST['did'];
            
            $field = '*';
            
            // if($UserID !=""){
            // $sql =" and user_id like '%".$UserID."%'";
            // }
            $sql = "b8<0";
            $sql2 = "where b8<0";
            // =====================分页开始==============================================92607291105
            import("@.ORG.ZQPage"); // 导入分页类
            $count = count($bonus->query("select id from __TABLE__ where did= " . $did . $sql)); // 总记录数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'did/' . $_REQUEST['did']; // 分页条件
            if (! empty($page_where)) {
                $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            } else {
                $Page = new ZQPage($count, $listrows, 1, 0, 3);
            }
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $status_rs = ($Page->getPage() - 1) * $listrows;
            $list = $bonus->query("select * from __TABLE__ where b8<0  order by did desc limit " . $status_rs . "," . $listrows);
            // $list = $bonus -> query("select e_date,did,sum(b0) as b0,sum(b8) as b8,sum(b9) as b9,sum(b10) as b10,sum(b11) as b11,sum(b12) as b12,sum(b13) as b13,sum(b14) as b14,sum(b15) as b15,sum(b16) as b16,sum(b17) as b17,sum(b18) as b18,sum(b19) as b19,sum(b20) as b20 from __TABLE__ ". $sql2 ." group by did order by did desc limit ". $status_rs .",". $listrows);
            
            foreach ($list as $key => $value) {
                for ($i = 8; $i < 50; $i ++) {
                    if ($value['b' . $i] != 0) {
                        $this->assign('b' . $i, $value['b' . $i]);
                    }
                }
            }
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            $this->assign('did', $did);
            // 查看的这期的结算时间
            $this->assign('confirm', $list[0]['e_date']);
            
            $count = array();
            foreach ($list as $vo) {
                for ($b = 0; $b <= 100; $b ++) {
                    $count[$b] += $vo['b' . $b];
                    $count[$b] = $this->_2Mal($count[$b], 2);
                }
            }
            
            // 奖项名称与显示
            
            $this->assign('b_b', $b_b);
            $this->assign('c_b', $c_b);
            $this->assign('count', $count);
            
            $this->assign('int', 7);
            
            $this->display('adminFinanceTableShow');
        } else {
            $this->error('错误');
            exit();
        }
    }

    public function adminFinanceTableList()
    {
        $this->check_prem('adminFinanceTableList');
        // 奖金明细
        if ($_SESSION['UrlPTPass'] == 'MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao') { // MyssShiLiu
            $times = M('times');
            $history = M('history');
            
            $UID = (int) $_GET['uid'];
            $did = (int) $_REQUEST['did'];
            
            $where = array();
            if (! empty($did)) {
                $rs = $times->find($did);
                if ($rs) {
                    $rs_day = $rs['benqi'];
                    $where['pdt'] = array(
                        array(
                            'gt',
                            $rs['shangqi']
                        ),
                        array(
                            'elt',
                            $rs_day
                        )
                    ); // 大于上期,小于等于本期
                } else {
                    $this->error('错误!');
                    exit();
                }
            }
            $where['uid'] = $UID;
            $where['type'] = 1;
            
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $history->where($where)->count(); // 总页数
                                                       // dump($history);exit;
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'did=' . (int) $_REQUEST['did']; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $history->where($where)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $fee = M('fee'); // 参数表
            $fee_rs = $fee->field('s18,s13')->find();
            $fee_s7 = explode('|', $fee_rs['s13']);
            $this->assign('fee_s7', $fee_s7); // 输出奖项名称数组
            
            $this->display('adminFinanceTableList');
        } else {
            $this->error('错误!');
            exit();
        }
    }
    
    // ============================================会员升级页面显示
    public function admin_level($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssGuanUplevel') {
            $fck = M('fck');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            $map['sel_level'] = array(
                'lt',
                90
            );
            
            // 查询字段
            $field = 'id,user_id,nickname,bank_name,bank_card,user_name,user_address,user_tel,rdt,f4,cpzj,pdt,u_level,sel_level';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('rdt desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $this->display('admin_level');
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // ========================================数据库管理
    public function adminManageTables()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssHaMiGua') {
            $Url = __ROOT__ . '/HaMiGua/';
            $_SESSION['shujukuguanli!12312g@#$%^@#$!@#$~!@#$'] = md5("^&%#hdgfhfg$@#$@gdfsg13123123!@#!@#");
            $this->_boxx($Url);
        }
    }
    // ============================================审核会员
    public function auditMenber($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            $fck = M('fck');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            
            $ID = $_SESSION[C('USER_AUTH_KEY')];
            
            $map['is_pay'] = array(
                'eq',
                0
            );
            
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('is_pay,id,rdt desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $this->display('auditMenber');
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function auditMenberData()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            // 查看会员详细信息
            $fck = M('fck');
            $ID = (int) $_GET['PT_id'];
            // 判断获取数据的真实性 是否为数字 长度
            if (strlen($ID) > 11) {
                $this->error('数据错误!');
                exit();
            }
            $where = array();
            $where['id'] = $ID;
            $field = '*';
            $vo = $fck->where($where)
                ->field($field)
                ->find();
            if ($vo) {
                $this->assign('vo', $vo);
                $this->display();
            } else {
                $this->error('数据错误!');
                exit();
            }
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // 复投审核
    public function adminAgents($GPid = 0)
    {
        $jiadan = M('jiadan');
        $UserID = $_POST['UserID'];
        if (! empty($UserID)) {
            import("@.ORG.KuoZhan"); // 导入扩展类
            $KuoZhan = new KuoZhan();
            if ($KuoZhan->is_utf8($UserID) == false) {
                $UserID = iconv('GB2312', 'UTF-8', $UserID);
            }
            unset($KuoZhan);
            $where['nickname'] = array(
                'like',
                "%" . $UserID . "%"
            );
            $where['user_id'] = array(
                'like',
                "%" . $UserID . "%"
            );
            $where['_logic'] = 'or';
            $map['_complex'] = $where;
            $UserID = urlencode($UserID);
        }

        $ID = $_SESSION[C('USER_AUTH_KEY')];

        $map['action_type'] = array( 'eq',0);

        // 查询字段
        $field = '*';
        // =====================分页开始==============================================
        import("@.ORG.ZQPage"); // 导入分页类
        $count = $jiadan->where($map)->count(); // 总页数
        $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
        $page_where = 'UserID=' . $UserID; // 分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        // ===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); // 分页变量
        $this->assign('page', $show); // 分页变量输出到模板
        $list = $jiadan->where($map)->field($field)->order('is_pay,id,adt desc')->page($Page->getPage() . ',' . $listrows)->select();

        $HYJJ = '';
        $this->_levelConfirm($HYJJ, 1);
        $this->assign('voo', $HYJJ); // 会员级别
        $this->assign('list', $list); // 数据输出到模板
        // =================================================

        $this->display('adminAgents');
    }
    public function adminAgentsAC()
    {
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $PTid = $_POST['tabledb'];
        if (! isset($PTid) || empty($PTid)) {
            $bUrl = __URL__ . '/adminAgents';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '确认':
                $this->_adminAgentsOpenUser($PTid);
                break;
            case '删除':
                $this->_adminAgentsDelUser($PTid);
                break;
            default:
                $bUrl = __URL__ . '/adminAgents';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }
    // 复投审核确认
    private function _adminAgentsOpenUser($PTid = 0)
    {
        $fck = D('fck');
        $jiadan = M('jiadan');
        $history = M('history');
        foreach ($PTid as $voo) {
            $jiadan_rs = $jiadan->find($voo);
            if ($jiadan_rs && $jiadan_rs['is_pay'] == 0) {
                $result = $jiadan->query("update xt_jiadan set is_pay=1 where uid = ".$jiadan_rs['uid']." and action_type != 3 and ftMonth=" . $jiadan_rs['ftMonth']);
//                 if ($jiadan_rs['action_type'] == 0) {
//                     // 3.推荐奖金
//                     $mrs = $fck->where('id=' . $jiadan_rs['uid'])->find();
//                     // 开启分红
//                     $result = $fck->query("update xt_fck set is_day_active=0 where id = ".$jiadan_rs['uid']);
//                     if ($mrs) {
//                     $data = array();
//                     $data['uid'] = $mrs['re_id'];
//                     $data['user_id'] = $mrs['re_name'];
//                     // 复投时间
//                     $data['adt'] = $jiadan_rs['adt'];
//                     // 出局时间
//                     $data['pdt'] = 0;
//                     // 已分红金额
//                     $data['money'] = 0;
//                     // 应分红金额
//                     $data['fhMoney'] = $jiadan_rs['fhMoney']*0.1*24;
//                     // 复投月份
//                     $data['ftMonth'] = $jiadan_rs['ftMonth'];
//                     // 分红天数
//                     $data['day'] = 0;
//                     $data['action_type'] = 3;
//                     $data['is_pay'] = 1;
                    
//                     $result = $jiadan->add($data);
//                     }
//                 }
                
                $bUrl = __URL__ . '/adminAgents';
                $this->_box(1, '复投审核成功！', $bUrl, 1);
            } else {
                $this->error('复投审核失败!');
            }
        }
    }
    
    // 复投审核删除
    private function _adminAgentsDelUser($PTid = 0)
    {
        $fck = D('fck');
        $jiadan = M('jiadan');
        foreach ($PTid as $voo) {
            $jiadan_rs = $jiadan->find($voo);
            if ($jiadan_rs && $jiadan_rs['is_pay'] == 0) {
                $result = $jiadan->query("delete from xt_jiadan where uid = ".$jiadan_rs['uid']." and action_type != 3 and ftMonth=" . $jiadan_rs['ftMonth']);
                if ($jiadan_rs['action_type'] == 0) {
                    $result1 = $fck->query("update __TABLE__ set is_cc=is_cc-1". ",agent_use=agent_use+{$jiadan_rs['fhMoney']} where id=" . $jiadan_rs['uid']);
                }
                $bUrl = __URL__ . '/adminAgents';
                $this->_box(1, '复投删除成功！', $bUrl, 1);
            } else {
                $this->error('复投删除失败!');
            }
        }
    }

    public function auditMenberData2()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            // 查看会员详细信息
            $fck = M('fck');
            $ID = (int) $_GET['PT_id'];
            // 判断获取数据的真实性 是否为数字 长度
            if (strlen($ID) > 11) {
                $this->error('数据错误!');
                exit();
            }
            $where = array();
            $where['id'] = $ID;
            $field = '*';
            $vo = $fck->where($where)
                ->field($field)
                ->find();
            if ($vo) {
                $this->assign('vo', $vo);
                $this->display();
            } else {
                $this->error('数据错误!');
                exit();
            }
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function auditMenberData2AC()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            
            $fck = M('fck');
            $data = array();
            
            $where['id'] = (int) $_POST['id'];
            $rs = $fck->where('is_pay = 0')->find($where['id']);
            if (! $rs) {
                $this->error('非法操作!');
                exit();
            }
            
            $data['nickname'] = $_POST['NickName'];
            $rs = $fck->where($data)->find();
            if ($rs) {
                if ($rs['id'] != $where['id']) {
                    $this->error('该会员名已经存在!');
                    exit();
                }
            }
            
            $data['bank_name'] = $_POST['BankName'];
            $data['bank_card'] = $_POST['BankCard'];
            $data['user_name'] = $_POST['UserName'];
            $data['bank_province'] = $_POST['BankProvince'];
            $data['bank_city'] = $_POST['BankCity'];
            $data['user_code'] = $_POST['UserCode'];
            $data['bank_address'] = $_POST['BankAddress'];
            $data['user_address'] = $_POST['UserAddress'];
            $data['user_post'] = $_POST['UserPost'];
            $data['user_tel'] = $_POST['UserTel'];
            $data['bank_province'] = $_POST['BankProvince'];
            $data['is_lock'] = $_POST['isLock'];
            
            $fck->where($where)
                ->data($data)
                ->save();
            $bUrl = __URL__ . '/auditMenberData2/PT_id/' . $where['id'];
            $this->_box(1, '修改会员信息！', $bUrl, 1);
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function auditMenberAC()
    {
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $PTid = $_POST['tabledb'];
        if (! isset($PTid) || empty($PTid)) {
            $bUrl = __URL__ . '/auditMenber';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '开通会员':
                $this->_auditMenberOpenUser($PTid);
                break;
            case '设为空单':
                $this->_auditMenberOpenNull($PTid);
                break;
            case '删除会员':
                $this->_auditMenberDelUser($PTid);
                break;
            case '申请通过':
                $this->_AdminLevelAllow($PTid);
                break;
            case '拒绝通过':
                $this->_AdminLevelNo($PTid);
                break;
            default:
                $bUrl = __URL__ . '/auditMenber';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }
    
    // 审核会员升级-通过
    private function _AdminLevelAllow($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanUplevel') {
            $fck = M('fck');
            $where = array();
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['sel_level'] = array(
                'lt',
                90
            );
            $vo = $fck->where($where)
                ->field('id,sel_level')
                ->select();
            foreach ($vo as $voo) {
                $where = array();
                $data = array();
                $where['id'] = $voo['id'];
                $data['u_level'] = $voo['sel_level'];
                $data['sel_level'] = 98;
                $fck->where($where)
                    ->data($data)
                    ->save();
            }
            
            $bUrl = __URL__ . '/admin_level';
            $this->_box(1, '会员升级通过！', $bUrl, 1);
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // 审核会员升级-拒绝
    private function _AdminLevelNo($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanUplevel') {
            $fck = M('fck');
            $where = array();
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['sel_level'] = array(
                'lt',
                90
            );
            $vo = $fck->where($where)
                ->field('id')
                ->select();
            foreach ($vo as $voo) {
                $where = array();
                $data = array();
                $where['id'] = $voo['id'];
                $data['sel_level'] = 97;
                $fck->where($where)
                    ->data($data)
                    ->save();
            }
            
            $bUrl = __URL__ . '/admin_level';
            $this->_box(1, '拒绝会员升级！', $bUrl, 1);
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // ===============================================设为空单
    private function _auditMenberOpenNull($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            $fck = D('Fck');
            $where = array();
            if (! $fck->autoCheckToken($_POST)) {
                $this->error('页面过期，请刷新页面！');
                exit();
            }
            $ID = $_SESSION[C('USER_AUTH_KEY')];
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = 0;
            $field = "id,u_level,re_id,cpzj,re_path,user_id,p_path,p_level,shop_id,f4";
            $vo = $fck->where($where)
                ->order('rdt asc')
                ->field($field)
                ->select();
            $nowdate = strtotime(date('c'));
            $nowday = strtotime(date('Y-m-d'));
            $nowmonth = date('m');
            
            foreach ($vo as $voo) {
                $ppath = $voo['p_path'];
                // 上级未开通不能开通下级员工
                $frs_where['is_pay'] = array(
                    'eq',
                    0
                );
                $frs_where['id'] = $voo['father_id'];
                $frs = $fck->where($frs_where)->find();
                if ($frs) {
                    $this->error('开通失败，上级未开通');
                    exit();
                }
                
                $nnrs = $fck->where('is_pay>0')
                    ->field('n_pai')
                    ->order('n_pai desc')
                    ->find();
                $mynpai = ((int) $nnrs['n_pai']) + 1;
                
                $data = array();
                $data['is_pay'] = 2;
                $data['pdt'] = $nowdate;
                $data['open'] = 1;
                $data['get_date'] = $nowday;
                $data['fanli_time'] = $nowday;
                $data['n_pai'] = $mynpai;
                
                // $data['n_pai'] = $max_p;
                // $data['x_pai'] = $myppp;
                // 开通会员
                $result = $fck->where('id=' . $voo['id'])->save($data);
                unset($data, $varray);
            }
            unset($fck, $where, $field, $vo, $nowday);
            $bUrl = __URL__ . '/auditMenber';
            $this->_box(1, '设为空单！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误！');
            exit();
        }
    }
    
    // ===============================================开通会员
    private function _auditMenberOpenUser($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            
            $fck = D('Fck');
            $shouru = M('shouru');
            $blist = M('blist');
            $gouwu = D('Gouwu');
            $fee = M('fee');
            $Guzhi = A('Guzhi');
            $fee_rs = $fee->field('s3')->find();
            $s3 = explode("|", $fee_rs['s3']);
            $where = array();
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = 0;
            $field = "*";
            $vo = $fck->where($where)
                ->field($field)
                ->order('id asc')
                ->select();
            $nowdate = strtotime(date('c'));
            $nowday = strtotime(date('Y-m-d'));
            $nowmonth = date('m');
            $fck->emptyTime();
            
            foreach ($vo as $voo) {
                // 给推荐人添加推荐人数或单数
                $fck->query("update __TABLE__ set `re_nums`=re_nums+1,re_f4=re_f4+" . $voo['f4'] . " where `id`=" . $voo['re_id']);
                // 购物车管理
//                 $gouwu->query("update __TABLE__ set `lx`=1 where `uid`=" . $voo['id']);
                
                $nnrs = $fck->where('is_pay>0')
                    ->field('n_pai')
                    ->order('n_pai desc')
                    ->find();
                $mynpai = ((int) $nnrs['n_pai']) + 1;
                
                $data = array();
                $data['is_pay'] = 1;
                $data['pdt'] = $nowdate;
                $data['open'] = 1;
                $data['get_date'] = $nowday;
                $data['fanli_time'] = $nowday - 1; // 当天没有分红奖
                $data['n_pai'] = $mynpai;
                if ($voo['f4'] == 50) {
                    $data['idt'] = $nowdate;
                    $data['adt'] = $nowdate;
                    $data['is_agent'] = 1;
                }
                $data['is_zy'] = $voo['id'];
                $data['kt_id'] = 1;
                $r_id = 1;
                $data['re_pathb'] = $r_id . ','; // 开通路径
                                               // 开通会员
                $result = $fck->where('id=' . $voo['id'])->save($data);
                //统计单数
                $fck->xiangJiao($voo['id'], 1);
                unset($data, $varray);
                
                $data = array();
                $data['uid'] = $voo['id'];
                $data['user_id'] = $voo['user_id'];
                $data['in_money'] = $voo['cpzj'];
                $data['in_time'] = time();
                $data['in_bz'] = "新会员加入";
                $shouru->add($data);
                unset($data);
                
                // 统计单数
//                 $fck->xiangJiao($voo['id'], 1);
                // 分红包记录表
                $fck->jiaDan($voo['id'], $voo['user_id'], $nowdate, 0, 0, $voo['f4'], 0, 0);
//                 // 算出奖金
//                 $fck->getusjj($voo['id'], 1, $voo['cpzj']);
            }
            unset($fck, $field, $where, $vo);
            $bUrl = __URL__ . '/auditMenber';
            $this->_box(1, '开通会员成功！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误！');
            exit();
        }
    }

    private function _auditMenberDelUser($PTid = 0)
    {
        // 删除会员
//         if ($_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            $fck = M('fck');
            $ispay = M('ispay');
            $where['is_pay'] = 0;
            // $where['id'] = array ('in',$PTid);
            foreach ($PTid as $voo) {
                $rs = $fck->find($voo);
                if ($rs) {
                    $whe['father_name'] = $rs['user_id'];
                    $rss = $fck->where($whe)->find();
                    if ($rss) {
                        $bUrl = __URL__ . '/auditMenber';
                        $this->error('该 ' . $rs['user_id'] . ' 会员有下级会员，不能删除！');
                        exit();
                    } else {
                        $where['id'] = $voo;
                        $a = $fck->where($where)->delete();
                        $bUrl = __URL__ . '/auditMenber';
                        $this->_box(1, '删除会员！', $bUrl, 1);
                    }
                } else {
                    $this->error('错误!');
                }
            }
            
            // $rs = $fck->where($where)->delete();
            // if ($rs){
            // $bUrl = __URL__.'/auditMenber';
            // $this->_box(1,'删除会员！',$bUrl,1);
            // exit;
            // }else{
            // $bUrl = __URL__.'/auditMenber';
            // $this->_box(0,'删除会员！',$bUrl,1);
            // exit;
            // }
//         } else {
//             $this->error('错误!');
//         }
    }

    public function adminMenber($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $UserID = $_REQUEST['UserID'];
            $ss_type = (int) $_REQUEST['type'];
            
            $map = array();
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            $uulv = (int) $_REQUEST['ulevel'];
            if (! empty($uulv)) {
                $map['u_level'] = array(
                    'eq',
                    $uulv
                );
            }
            $map['is_pay'] = array(
                'egt',
                1
            );
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $listrows = 20; // 每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&ulevel=' . $uulv; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('pdt desc,id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $f4_count = $fck->where($map)->sum('cpzj');
            $this->assign('f4_count', $f4_count);
            $f4_count22 = $fck->where('id>1')->sum('cpzj');
            $this->assign('f4_count22', $f4_count22);
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            
            $getLev = "";
            $this->_getLevelConfirm($getLev, 1);
            $this->assign('gvoo', $getLev); // 会员团队级别
            
            $level = array();
            for ($i = 0; $i < count($HYJJ); $i ++) {
                $level[$i] = $HYJJ[$i + 1];
            }
            $this->assign('level', $level);
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $title = '会员管理';
            $this->assign('title', $title);
            $this->display('adminMenber');
            return;
//         } else {
//             $this->error('数据错误!');
//             exit();
//         }
    }
    
    public function adminMenberXls($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssadminMenberXls') {
    
            $title = '报表管理';
            $this->assign('title', $title);
            $this->display('adminMenberXls');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminlookfh()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            
            $uid = (int) $_GET['uid'];
            if (empty($uid)) {
                $this->error('数据错误!');
                exit();
            }
            $fenhong = M('fenhong');
            $where = array();
            $where['uid'] = array(
                'eq',
                $uid
            );
            
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fenhong->where($where)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = ''; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fenhong->where($where)
                ->field($field)
                ->order('f_num asc,id asc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            $this->display();
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminlookfhall()
    {
        if ($_SESSION['UrlPTPass'] == 'Myssadminlookfhall') {
            
            $fenhong = M('fenhong');
            $where = array();
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fenhong->where($where)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = ''; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fenhong->where($where)
                ->field($field)
                ->order('f_num asc,id asc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            $this->display();
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function premAdd()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $id = (int) $_GET['id'];
            $table = M('fck');
            $rs = $table->field('id,is_boss,prem,user_id')->find($id);
            if ($rs) {
                $ars = array();
                $arr = explode(',', $rs['prem']);
                for ($i = 1; $i <= 30; $i ++) {
                    if (in_array($i, $arr)) {
                        $ars[$i] = "checked";
                    } else {
                        $ars[$i] = "";
                    }
                }
                $this->assign('ars', $ars);
                $this->assign('rs', $rs);
                $title = '修改权限';
            } else {
                $title = '添加权限';
            }
            
            $this->assign('title', $title);
            $this->display('premAdd');
        } else {
            $this->error('权限错误!');
        }
    }

    public function premAddSave()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $id = (int) $_POST['id'];
            if ($id == 1 && $_SESSION[C('USER_AUTH_KEY')] != 1) {
                $this->error('不能修改该会员的权限!');
                exit();
            }
            $table = M('fck');
            $is_boss = $_POST['is_boss'];
            $boss = $_POST['isBoss'];
            $arr = ',';
            if (is_array($is_boss)) {
                foreach ($is_boss as $vo) {
                    $arr .= $vo . ',';
                }
            }
            $data = array();
            $data['is_boss'] = $boss;
            $data['prem'] = $arr;
            $data['id'] = $id;
            // if ($id == 1){
            // $this->error('不能修改最高会员！');
            // }
            $table->save($data);
            $title = '修改权限';
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, $title, $bUrl, 2);
        } else {
            $this->error('权限错误!');
        }
    }
    
    // 显示劳资详细
    public function BonusShow($GPid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $hi = M('history');
            
            $where = array();
            $where['Uid'] = $_REQUEST['PT_id'];
            $where['type'] = 19;
            
            $list = $hi->where($where)->select();
            $this->assign('list', $list);
            $this->display('BonusShow');
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminuserData()
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao' || $_SESSION['UrlPTPass'] == 'MyssGuanXiGua' || $_SESSION['UrlPTPass'] == 'MyssGuansingle' || $_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            // 查看会员详细信息
            $fck = M('fck');
            $ID = (int) $_GET['PT_id'];
            // 判断获取数据的真实性 是否为数字 长度
            if (strlen($ID) > 15) {
                $this->error('数据错误!');
                exit();
            }
            $where = array();
            // 查询条件
            // $where['ReID'] = $_SESSION[C('USER_AUTH_KEY')];
            $where['id'] = $ID;
            $field = '*';
            $vo = $fck->where($where)
                ->field($field)
                ->find();
            if ($vo) {
                $this->assign('vo', $vo);
                $voo = 0;
                $this->_levelConfirm($voo);
                
                $level = array();
                for ($i = 1; $i <= count($voo); $i ++) {
                    $level[$i] = $voo[$i];
                }
                $this->assign('level', $level);
                
                $fee = M('fee');
                $fee_s = $fee->field('str24,str25,str29')->find();
                $lang = explode('|', $fee_s['str24']);
                $countrys = explode('|', $fee_s['str25']);
                
                $bank = explode('|', $fee_s['str29']);
                $this->assign('bank', $bank);
                $this->assign('b_bank', $vo);
                
                $this->assign('lang', $lang);
                $this->assign('countrys', $countrys);
                
                $this->display();
            } else {
                $this->error('数据错误!');
                exit();
            }
//         } else {
//             $this->error('数据错误!');
//             exit();
//         }
    }

    /* --------------- 修改保存会员信息 ---------------- */
    // public function adminuserDataSave(){
    // if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao' || $_SESSION['UrlPTPass'] == 'MyssGuanXiGua' || $_SESSION['UrlPTPass'] == 'MyssGuansingle' || $_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao'){
    // $fck = M('fck');
    //
    // // $_POST['shop_name'] = trim($_POST['shopname']);
    // // $whe = array();
    // // $whe['user_id'] = $_POST['shop_name'];
    // // $whe['is_agent'] = 2;
    // // $shop_rs = $fck -> where($whe) -> field('id,user_id') -> find();
    // // if(!$shop_rs){
    // // $this->error ('没有该报单中心!');
    // // exit;
    // // }
    //
    //
    // //$_POST['NickName'] = $this->nickname($_POST['NickName'],$_POST['ID']); //检测昵称
    // $_POST['BankName'] = $this->bank_name($_POST['BankName']); //检测银行
    //
    //
    // $data = array();
    // // $data['shop_id'] = $shop_rs['id']; //所属报单中心ID
    // // $data['shop_name'] = $shop_rs['user_id']; //所属报单中心user_id
    // $data['pwd1'] = trim($_POST['pwd1']); //一级密码不加密
    // $data['pwd2'] = trim($_POST['pwd2']);
    // $data['pwd3'] = trim($_POST['pwd3']);
    // $data['password'] = md5(trim($_POST['pwd1'])); //一级密码加密
    // $data['passopen'] = md5(trim($_POST['pwd2']));
    // $data['passopentwo'] = md5(trim($_POST['pwd3']));
    // $data['nickname'] = $_POST['NickName']; //会员昵称
    // $data['bank_name'] = $_POST['BankName']; //银行名称
    // $data['bank_card'] = $_POST['BankCard']; //银行卡号
    // $data['user_name'] = $_POST['UserName']; //开户姓名
    //
    // $data['bank_province'] = $_POST['BankProvince']; //省份
    // $data['bank_city'] = $_POST['BankCity']; //城市
    // $data['bank_address'] = $_POST['BankAddress']; //开户地址
    // $data['user_code'] = $_POST['UserCode']; //身份证号码
    // $data['user_address'] = $_POST['UserAddress']; //联系地址
    // $data['email'] = $_POST['email']; //电子邮箱
    // $data['user_tel'] = $_POST['UserTel']; //联系电话
    // $data['qq'] = $_POST['qq']; //联系电话
    // $data['id'] = $_POST['ID']; //要修改资料的AutoId
    // $data['agent_use'] = $_POST['AgentUse']; //K币账户
    // $data['agent_cash'] = $_POST['AgentCash']; //注册币
    //
    // $rs = $fck->save($data);
    // if($rs){
    // $bUrl = __URL__.'/adminuserData/PT_id/'.$_POST['ID'];
    // $this->_box(1,'修改成功！',$bUrl,1);
    // }else{
    // $this->error('修改错误!');
    // exit;
    // }
    // }else{
    // $this->error('操作错误!');
    // exit;
    // }
    // }
    public function adminuserDataSave()
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao' || $_SESSION['UrlPTPass'] == 'MyssGuanXiGua' || $_SESSION['UrlPTPass'] == 'MyssGuansingle' || $_SESSION['UrlPTPass'] == 'MyssShenShuiPuTao') {
            $fck = M('fck');
            if (! $fck->autoCheckToken($_POST)) {
                $this->error('页面过期，请刷新页面！');
            }
            $ID = (int) $_POST['ID'];
            $data = array();
            $data['pwd1'] = trim($_POST['pwd1']); // 一级密码不加密
            $data['pwd2'] = trim($_POST['pwd2']);
            $data['pwd3'] = trim($_POST['pwd3']);
            $data['password'] = md5(trim($_POST['pwd1'])); // 一级密码加密
            $data['passopen'] = md5(trim($_POST['pwd2']));
            $data['passopentwo'] = md5(trim($_POST['pwd3']));
            
            $wenti = trim($_POST['wenti']);
            $wenti_dan = trim($_POST['wenti_dan']);
            if (! empty($wenti)) {
                $data['wenti'] = $wenti;
            }
            if (! empty($wenti_dan)) {
                $data['wenti_dan'] = $wenti_dan;
            }
            
            $data['nickname'] = $_POST['NickName'];
            
            $data['lang'] = $_POST['Lang'];
            $data['countrys'] = $_POST['Countrys'];
            
            $data['bank_name'] = $_POST['BankName'];
            $data['bank_card'] = $_POST['BankCard'];
            $data['user_name'] = $_POST['UserName'];
            $data['bank_province'] = $_POST['BankProvince'];
            $data['bank_city'] = $_POST['BankCity'];
            $data['bank_address'] = $_POST['BankAddress'];
            $data['user_code'] = $_POST['UserCode'];
            // $data['user_address'] = $_POST['UserAddress'];
            // $data['user_post'] = $_POST['UserPost'];
            // $data['user_phone'] = $_POST['user_phone'];//邮编
            $data['user_tel'] = $_POST['UserTel'];
            // $data['is_lock'] = $_POST['isLock'];
            $data['qq'] = $_POST['qq'];
            $data['email'] = $_POST['email'];
            $data['agent_use'] = $_POST['AgentUse'];
            $data['agent_cash'] = $_POST['AgentCash'];
            $data['zjj'] = $_POST['zjj'];
            $data['id'] = $_POST['ID'];
            
            $data['agent_kt'] = $_POST['AgentKt'];
            $data['agent_xf'] = $_POST['AgentXf'];
            $data['agent_gp'] = $_POST['AgentGp'];
            $data['agent_cf'] = $_POST['agent_cf'];
            $data['agent_zc'] = $_POST['agent_zc'];
            $data['gp_num'] = (int) $_POST['gp_num'];
            
            $data['wang_j'] = (int) $_POST['wang_j'];
            $data['wang_t'] = (int) $_POST['wang_t'];
            
            // $data['u_level'] = $_POST['uLevel'];
            // if ($_POST['ID'] == 1){
            // $data['is_boss'] = 1;
            // }else{
            // $data['is_boss'] = $_POST['isBoss'];
            // }
            // $data['agent_use'] = $_POST['AgentUse'];
            // $data['agent_cash'] = $_POST['AgentCash'];
            $ReName = $_POST['ReName'];
            $re_where = array();
            $where = array();
            $where['nickname'] = $ReName;
            $where['user_id'] = $ReName;
            $where['_logic'] = 'or';
            $re_where['_complex'] = $where;
            $re_fck_rs = $fck->where($re_where)
                ->field('id,nickname,user_id')
                ->find();
            if ($re_fck_rs) {
                if ($ID == 1) {
                    $data['re_id'] = 0;
                    $data['re_name'] = 0;
                } else {
                    $data['re_id'] = $re_fck_rs['id'];
                    $data['re_name'] = $re_fck_rs['user_id'];
                }
            } else {
                if ($ID != 1) {
                    $this->error('推荐人不存在，请重新输入！');
                    exit();
                }
            }
            
            $p_shop = $_POST['p_shop'];
            $c_shop = $_POST['c_shop'];
            $a_shop = $_POST['a_shop'];
            $p_shop_id = 0;
            if (! empty($p_shop)) {
                $p_where = array();
                $p_where['nickname'] = $p_shop;
                $p_where['is_agent'] = 2;
                $p_where['shoplevel'] = 3;
                $p_rs = $fck->where($p_where)
                    ->field('id,nickname,shop_path')
                    ->find();
                if (! $p_rs) {
                    $this->error('省级代理不存在，请重新输入！');
                    exit();
                }
                $p_shop_id = $p_rs['id'];
            }
            $c_shop_id = 0;
            if (! empty($c_shop)) {
                $p_where = array();
                $p_where['nickname'] = $c_shop;
                $p_where['is_agent'] = 2;
                $p_where['shoplevel'] = 2;
                $p_rs = $fck->where($p_where)
                    ->field('id,nickname,shop_path')
                    ->find();
                if (! $p_rs) {
                    $this->error('市级代理不存在，请重新输入！');
                    exit();
                }
                $c_shop_id = $p_rs['id'];
            }
            $a_shop_id = 0;
            if (! empty($a_shop)) {
                $p_where = array();
                $p_where['nickname'] = $a_shop;
                $p_where['is_agent'] = 2;
                $p_where['shoplevel'] = 1;
                $p_rs = $fck->where($p_where)
                    ->field('id,nickname,shop_path')
                    ->find();
                if (! $p_rs) {
                    $this->error('县级代理不存在，请重新输入！');
                    exit();
                }
                $a_shop_id = $p_rs['id'];
            }
            // $where_nic = array();
            // $where_nic['nickname'] = $data['nickname'];
            // $rs = $fck -> where($where_nic) -> find();
            // if($rs){
            // if($rs['id'] != $data['id']){
            // $this->error ('该会员编号已经存在!');
            // exit;
            // }
            // }
            $where = array();
            $id = $_SESSION[C('USER_AUTH_KEY')];
            $where['id'] = $data['id'];
            $frs = $fck->where($where)
                ->field('id,user_id,password,passopen,p_shop,c_shop,a_shop')
                ->find();
            if ($frs) {
                if ($frs['p_shop'] != $p_shop_id) {
                    $data['p_shop'] = $p_shop_id;
                }
                if ($frs['c_shop'] != $c_shop_id) {
                    $data['c_shop'] = $c_shop_id;
                }
                if ($frs['a_shop'] != $a_shop_id) {
                    $data['a_shop'] = $a_shop_id;
                }
                //
                // if ($_POST['Password']!= $frs['password']){
                // $data['password'] = md5($_POST['Password']);
                // if ($id == $data['id']){
                // $_SESSION['login_sf_list_u'] = md5($frs['user_id']. ALL_PS .$data['password'].$_SERVER['HTTP_USER_AGENT']);
                // }
                // }
                // if ($_POST['PassOpen'] != $frs['passopen']){
                // $data['passopen'] = md5($_POST['PassOpen']);
                // }
            }
            
            $newlv = (int) $_POST['newulevel'];
            $oldlv = (int) $_POST['oldulevel'];
            
            $result = $fck->save($data);
            unset($data);
            if ($result || $newlv != $oldlv) {
                if ($newlv != $oldlv) {
                    
                    $promo = M('promo');
                    
                    $myrs = $fck->where('id=' . $ID)
                        ->field('id,user_id,bank_name')
                        ->find();
                    
                    $content = " <font color=red>後台升降級</font>";
                    
                    $wdata = array();
                    $wdata['money'] = 0;
                    $wdata['u_level'] = $oldlv;
                    $wdata['uid'] = $myrs['id'];
                    $wdata['user_id'] = $myrs['user_id'];
                    $wdata['create_time'] = time();
                    $wdata['pdt'] = time();
                    $wdata['up_level'] = $newlv;
                    $wdata['danshu'] = 0;
                    $wdata['is_pay'] = 1;
                    $wdata['user_name'] = $content;
                    $wdata['u_bank_name'] = $myrs['bank_name'];
                    $wdata['type'] = 0;
                    $promo->add($wdata);
                    
                    $newmo = $s3[$newlv - 1];
                    $newdl = $s2[$newlv - 1];
                    
                    $fck->query("update __TABLE__ set u_level=" . $newlv . ",cpzj=" . $newmo . ",f4=" . $newdl . " where `id`=" . $myrs['id']);
                    
                    unset($promo, $wdata, $myrs, $fee, $fee_rs, $s3, $s2);
                }
                
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '资料修改成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '资料修改失败！', $bUrl, 1);
            }
//         } else {
//             $bUrl = __URL__ . '/adminMenber';
//             $this->_box(0, '数据错误！', $bUrl, 1);
//             exit();
//         }
    }

    public function slevel()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao' || $_SESSION['UrlPTPass'] == 'MyssGuanXiGua' || $_SESSION['UrlPTPass'] == 'MyssGuansingle') {
            // 查看会员详细信息
            $fck = M('fck');
            $ID = (int) $_GET['PT_id'];
            // 判断获取数据的真实性 是否为数字 长度
            if (strlen($ID) > 15) {
                $this->error('数据错误!');
                exit();
            }
            $where = array();
            // 查询条件
            // $where['ReID'] = $_SESSION[C('USER_AUTH_KEY')];
            $where['id'] = $ID;
            $field = '*';
            $vo = $fck->where($where)
                ->field($field)
                ->find();
            if ($vo) {
                $this->assign('vo', $vo);
                $this->display();
            } else {
                $this->error('数据错误!');
                exit();
            }
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function slevelsave()
    { // 升级保存数据
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao' || $_SESSION['UrlPTPass'] == 'MyssGuanXiGua' || $_SESSION['UrlPTPass'] == 'MyssGuansingle') {
            // 查看会员详细信息
            $fck = D('Fck');
            $fee = M('fee');
            $ID = (int) $_POST['ID'];
            $slevel = (int) $_POST['slevel']; // 升级等级
                                              
            // 判断获取数据的真实性 是否为数字 长度
            if (strlen($ID) > 15 or $ID <= 0) {
                $this->error('数据错误!');
                exit();
            }
            
            $fee_rs = $fee->find(1);
            if ($slevel <= 0 or $slevel >= 7) {
                $this->error('升级等级错误！');
                exit();
            }
            
            $where = array();
            // 查询条件
            // $where['ReID'] = $_SESSION[C('USER_AUTH_KEY')];
            $where['id'] = $ID;
            $field = '*';
            $vo = $fck->where($where)
                ->field($field)
                ->find();
            if ($vo) {
                switch ($slevel) { // 通过注册等级从数据库中找出注册金额及认购单数
                    case 1:
                        $cpzj = $fee_rs['uf1']; // 注册金额
                        $F4 = $fee_rs['jf1']; // 自身认购单数
                        break;
                    case 2:
                        $cpzj = $fee_rs['uf2'];
                        $F4 = $fee_rs['jf2'];
                        break;
                    case 3:
                        $cpzj = $fee_rs['uf3'];
                        $F4 = $fee_rs['jf3'];
                        break;
                    case 4:
                        $cpzj = $fee_rs['uf4'];
                        $F4 = $fee_rs['jf4'];
                        break;
                    case 5:
                        $cpzj = $fee_rs['uf5'];
                        $F4 = $fee_rs['jf5'];
                        break;
                    case 6:
                        $cpzj = $fee_rs['uf6'];
                        $F4 = $fee_rs['jf6'];
                        break;
                }
                
                $number = $F4 - $vo['f4']; // 升级所需单数差
                $data = array();
                $data['u_level'] = $slevel; // 升级等级
                $data['cpzj'] = $cpzj; // 注册金额
                $data['f4'] = $F4; // 自身认购单数
                $fck->where($where)
                    ->data($data)
                    ->save();
                
                $fck->xiangJiao_lr($ID, $number); // 住上统计单数
                
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '会员升级！', $bUrl, 1);
                exit();
            } else {
                $this->error('数据错误!');
                exit();
            }
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminMenberAC()
    {
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $PTid = $_POST['tabledb'];
        if (! isset($PTid) || empty($PTid)) {
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(0, '请选择会员！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '开启会员':
                $this->_adminMenberOpen($PTid);
                break;
            case '锁定会员':
                $this->_adminMenberLock($PTid);
                break;
            case '奖金提现':
                $this->adminMenberCurrency($PTid);
                break;
            case '开启奖金':
                $this->adminMenberFenhong($PTid);
                break;
            case '删除会员':
                $this->adminMenberDel($PTid);
                break;
            case '关闭奖金':
                $this->_Lockfenh($PTid);
                break;
            case '开启分红':
                $this->_isDayActiveOpen($PTid);
                break;
            case '关闭分红':
                $this->_isDayActiveLock($PTid);
                break;
            case '设为转账管理员':
                $this->_treasureManager($PTid);
                break;
            case '解除转账管理员':
                $this->_treasureManagerCancel($PTid);
                break;
            case '设为物流管理员':
                $this->_relAgent($PTid);
                break;
            case '解除物流管理员':
                $this->_relAgentCancel($PTid);
                break;
            case '设为服务中心管理员':
                $this->_setAgentManager($PTid);
                break;
            case '解除服务中心管理员':
                $this->_cancelAgentManager($PTid);
                break;
            case '开启现金积分':
                $this->_openAgentUse($PTid);
                break;
            case '锁定现金积分':
                $this->_cancelAgentUse($PTid);
                break;
            case '开启期限':
                $this->_OpenQd($PTid);
                break;
            case '关闭期限':
                $this->_LockQd($PTid);
                break;
            case '开启分红奖':
                $this->_OpenFh($PTid);
                break;
            case '关闭分红奖':
                $this->_LockFh($PTid);
                break;
            case '奖金转注册币':
                $this->adminMenberZhuan($PTid);
                break;
            case '设为报单中心':
                $this->_adminMenberAgent($PTid);
                break;
            case '设为代理商':
                $this->_adminMenberJB($PTid);
            case '取消代理商':
                $this->adminMenberJBcancel($PTid);
                break;
            case '设为物流管理':
                $this->_adminMenberWL($PTid);
            case '设为财务管理':
                $this->_adminMenberCw($PTid);
            case '取消管理员':
                $this->adminMenberWLcancel($PTid);
                break;
            default:
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '没有该会员！', $bUrl, 1);
                break;
        }
    }

    public function adminMenberDL()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $result = $fck->execute('update __TABLE__ set agent_cash=agent_cash+agent_use,agent_use=0 where is_pay>0');
            
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, '转换会员奖金为注册币！', $bUrl, 1);
        } else {
            $this->error('错误!');
        }
    }

    public function adminMenberZhuan($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $rs = $fck->where($where)
                ->field('id')
                ->select();
            foreach ($rs as $vo) {
                $myid = $vo['id'];
                $fck->execute('update __TABLE__ set agent_cash=agent_cash+agent_use,agent_use=0 where is_pay>0 and id=' . $myid . '');
            }
            unset($fck, $where, $rs, $myid, $result);
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, '转换会员奖金为注册币！', $bUrl, 1);
        } else {
            $this->error('错误!');
        }
    }

    private function _adminMenberJB($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = array(
                'gt',
                0
            );
            $where['is_jb'] = array(
                'eq',
                0
            );
            $rs = $fck->where($where)->setField('is_jb', '1');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '设为代理商成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '设为代理商失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误！');
            exit();
        }
    }

    public function adminMenberJBcancel($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $rs = $fck->where($where)
                ->field('id')
                ->select();
            foreach ($rs as $vo) {
                $myid = $vo['id'];
                $fck->execute('update __TABLE__ set is_jb=0 where is_pay>0 and is_jb>0 and id=' . $myid . '');
            }
            unset($fck, $where, $rs, $myid, $result);
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, '取消代理商成功！', $bUrl, 1);
        } else {
            $this->error('错误2!');
        }
    }

    private function _adminMenberCw($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = array(
                'gt',
                0
            );
            $where['is_aa'] = array(
                'eq',
                0
            );
            $rs = $fck->where($where)->setField('is_aa', '1');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '设为物流管理成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '设为物流管理失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误！');
            exit();
        }
    }

    private function _adminMenberWL($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = array(
                'gt',
                0
            );
            $where['is_aa'] = array(
                'eq',
                0
            );
            $rs = $fck->where($where)->setField('is_aa', '2');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '设为物流管理成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '设为物流管理失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误！');
            exit();
        }
    }

    public function adminMenberWLcancel($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $rs = $fck->where($where)
                ->field('id')
                ->select();
            foreach ($rs as $vo) {
                $myid = $vo['id'];
                $fck->execute('update __TABLE__ set is_aa=0 where is_pay>0 and is_aa>0 and id=' . $myid . '');
            }
            unset($fck, $where, $rs, $myid, $result);
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, '取消管理员成功！', $bUrl, 1);
        } else {
            $this->error('错误2!');
        }
    }

    private function adminMenberDel($PTid = 0)
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $times = M('times');
            $bonus = M('bonus');
            $history = M('history');
            $chongzhi = M('chongzhi');
            $gouwu = M('gouwu');
            $jiadan = M('jiadan');
            $tiqu = M('tiqu');
            $zhuanj = M('zhuanj');
            
            foreach ($PTid as $voo) {
                $rs = $fck->find($voo);
                if ($rs) {
                    $id = $rs['id'];
                    $whe['id'] = $rs['father_id'];
                    $con = $fck->where($whe)->count();
                    if ($id == 1) {
                        $bUrl = __URL__ . '/adminMenber';
                        $this->error('该 ' . $rs['user_id'] . ' 不能删除！');
                        exit();
                    }
                    if ($con == 2) {
                        $bUrl = __URL__ . '/adminMenber';
                        $this->error('该 ' . $rs['user_id'] . ' 会员有下级会员，不能删除！');
                        exit();
                    }
                    if ($con == 1) {
                        $this->set_Re_Path($id);
                        $this->set_P_Path($id);
                    }
                    $where = array();
                    $where['id'] = $voo;
                    $map['uid'] = $voo;
                    $bonus->where($map)->delete();
                    $history->where($map)->delete();
                    $chongzhi->where($map)->delete();
                    $times->where($map)->delete();
                    $tiqu->where($map)->delete();
                    $zhuanj->where($map)->delete();
                    $gouwu->where($map)->delete();
                    $jiadan->where($map)->delete();
                    $fck->where($where)->delete();
                    $bUrl = __URL__ . '/adminMenber';
                    $this->_box(1, '删除会员成功！', $bUrl, 1);
                }
            }
//         } else {
//             $this->error('错误!');
//         }
    }
    
    // 修复推荐路径
    public function set_Re_Path($id)
    {
        $fck = M("fck");
        // 根据选择ID查询会员表
        $frs = $fck->find($id);
        // 根据ID向下查询
        $fck_rs = $fck->where("re_id=" . $id)->select();
        foreach ($fck_rs as $xr_vo) {
            // ID
            $id = $xr_vo['id'];
            // 推荐人ID
            $re_id = $xr_vo['re_id'];
            // 推荐路径
            $re_path = $xr_vo['re_path'];
            // 正确的推荐路径
            $path = $frs['re_path'] . $re_id . ',';
            // 如果路径正确继续查询
            if ($re_path != $path) {
                $this->error($id);
                // 执行路径更新
                $fck->execute("UPDATE __TABLE__ SET re_path=" . $path . " where id= " . $id);
            }
            // 递归更新数据
            $this->set_Re_Path($id);
        }
    }

    public function set_P_Path($id)
    {
        $fck = M("fck");
        $frs = $fck->find($id);
        
        $r_rs = $fck->find($frs['father_id']);
        $xr_rs = $fck->where("father_id=" . $id)->find();
        if ($xr_rs) {
            $p_level = $r_rs['p_level'] + 1;
            $p_path = $r_rs['p_path'] . $r_rs['id'] . ',';
            $fck->execute("UPDATE __TABLE__ SET treeplace=" . $frs['treeplace'] . ",father_id=" . $r_rs['id'] . ",father_name='" . $r_rs['user_id'] . "',p_path='" . $p_path . "',p_level=" . $p_level . " where `id`= " . $xr_rs['id']);
            // 修改节点路径
            $f_where = array();
            $f_where['p_path'] = array(
                'like',
                '%,' . $xr_rs['id'] . ',%'
            );
            $ff_rs = $fck->where($f_where)
                ->order('p_level asc')
                ->select();
            $r_where = array();
            foreach ($ff_rs as $fvo) {
                $r_where['id'] = $fvo['father_id'];
                $sr_rs = $fck->where($r_where)->find();
                $p_level = $sr_rs['p_level'] + 1;
                $p_path = $sr_rs['p_path'] . $sr_rs['id'] . ',';
                $fck->execute("UPDATE __TABLE__ SET p_path='" . $p_path . "',p_level=" . $p_level . " where `id`= " . $fvo['id']);
            }
        }
    }

    public function jiandan($Pid = 0, $DanShu = 1, $pdt, $t_rs)
    {
        // ========================================== 往上统计单数
        $fck = M('fck');
        $where = array();
        $where['id'] = $Pid;
        $field = 'treeplace,father_id,pdt';
        $vo = $fck->where($where)
            ->field($field)
            ->find();
        if ($vo) {
            $Fid = $vo['father_id'];
            $TPe = $vo['treeplace'];
            if ($pdt > $t_rs) {
                if ($TPe == 0 && $Fid > 0) {
                    $fck->execute("update __TABLE__ Set `l`=l-$DanShu, `benqi_l`=benqi_l-$DanShu where `id`=" . $Fid);
                } elseif ($TPe == 1 && $Fid > 0) {
                    $fck->execute("update __TABLE__ Set `r`=r-$DanShu, `benqi_r`=benqi_r-$DanShu  where `id`=" . $Fid);
                }
            } else {
                if ($TPe == 0 && $Fid > 0) {
                    $fck->execute("update __TABLE__ Set `l`=l-$DanShu where `id`=" . $Fid);
                } elseif ($TPe == 1 && $Fid > 0) {
                    $fck->execute("update __TABLE__ Set `r`=r-$DanShu  where `id`=" . $Fid);
                }
            }
            
            if ($Fid > 0)
                $this->jiandan($Fid, $DanShu, $pdt, $t_rs);
        }
        unset($where, $field, $vo, $pdt, $t_rs);
    }

    private function adminMenberFenhong($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_fenh'] = array(
                'gt',
                0
            );
            $rs = $fck->where($where)->setField('is_fenh', '0');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '开启奖金成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '开启奖金失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误！');
            exit();
        }
    }
    
    private function _Lockfenh($PTid = 0)
    {
        // 关闭奖金
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['is_fenh'] = array('eq',0);
            $where['id'] = array('in',$PTid);
            $rs = $fck->where($where)->setField('is_fenh', '1');
            
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '关闭奖金成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '关闭奖金失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    // 开启分红
    private function _isDayActiveOpen($PTid = 0)
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array('in',$PTid);
            $where['is_day_active'] = array('gt',0);
            $rs = $fck->where($where)->setField('is_day_active', '0');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '开启分红成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '开启分红失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误！');
//             exit();
//         }
    }
    // 关闭分红
    private function _isDayActiveLock($PTid = 0)
    {
        // 锁定会员
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['is_day_active'] = array( 'eq',0);
            $where['id'] = array('in',$PTid);
            $rs = $fck->where($where)->setField('is_day_active', '1');
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '关闭分红成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '关闭分红失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误!');
//         }
    }
    
    // 转账管理员设置
    private function _treasureManager($PTid = 0)
    {
        // 设置物流管理员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array('in',$PTid);
            $varray = array(
                'is_treasure_manager' => '1',
                'is_fenh' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '转账管理员设置成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '转账管理员设置失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    
    // 转账管理员解除
    private function _treasureManagerCancel($PTid = 0)
    {
        // 设置物流管理员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['is_treasure_manager'] = array('egt',1);
            $where['id'] = array('in',$PTid);
            $varray = array(
                'is_treasure_manager' => '0',
                'is_fenh' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '转账管理员解除成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '转账管理员解除失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    
    // 物流管理员设置
    private function _relAgent($PTid = 0)
    {
        // 设置物流管理员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array('in',$PTid);
            $varray = array(
                'is_aa' => '1',
                'is_fenh' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '物流管理员设置成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '物流管理员设置失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    
    // 物流管理员解除
    private function _relAgentCancel($PTid = 0)
    {
        // 设置物流管理员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['is_aa'] = array('egt',1);
            $where['id'] = array('in',$PTid);
            $varray = array(
                'is_aa' => '0',
                'is_fenh' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '物流管理员解除成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '物流管理员解除失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    
    // 服务中心管理员设置
    private function _setAgentManager($PTid = 0)
    {
        // 设置服务中心管理员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array('in',$PTid);
            $varray = array(
                'remark' => '1',
                'is_fenh' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '服务中心管理员设置成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '服务中心管理员设置失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    
    // 服务中心管理员解除
    private function _cancelAgentManager($PTid = 0)
    {
        // 解除服务中心管理员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['remark'] = array('egt',1);
            $where['id'] = array('in',$PTid);
            $varray = array(
                'remark' => '0',
                'is_fenh' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '服务中心管理员解除成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '服务中心管理员解除失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }
    
    // 开启现金积分
    private function _openAgentUse($PTid = 0)
    {
        // 设置服务中心管理员
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array('in',$PTid);
            $varray = array(
                'is_lock_use' => '0'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '现金积分开启成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '现金积分开启失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误!');
//         }
    }
    
    // 锁定现金积分
    private function _cancelAgentUse($PTid = 0)
    {
        // 解除服务中心管理员
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array('in',$PTid);
            $varray = array(
                'is_lock_use' => '1'
            );
            $rs = $fck->where($where)->setField($varray);
    
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '现金积分锁定成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '现金积分锁定失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误!');
//         }
    }
    
    // 开启会员
    private function _adminMenberOpen($PTid = 0)
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $data['is_pay'] = 1;
            $rs = $fck->where($where)->setField('is_lock', '0');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '开启会员！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '开启会员！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误！');
//             exit();
//         }
    }

    private function _adminMenberLock($PTid = 0)
    {
        // 锁定会员
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['is_pay'] = array(
                'egt',
                1
            );
            $where['is_boss'] = 0;
            $where['id'] = array(
                'in',
                $PTid
            );
            $rs = $fck->where($where)->setField('is_lock', '1');
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '锁定会员！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '锁定会员！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误!');
//         }
    }
    
    // 设为报单中心
    private function _adminMenberAgent($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            
            $fck = M('fck');
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_agent'] = array(
                'lt',
                2
            );
            $rs2 = $fck->where($where)->setField('adt', mktime());
            
            foreach ($PTid as $key => $value) {
                
                $list = $fck->where('id=' . $value)
                    ->field('*')
                    ->find();
                
                if ($list['is_agent'] >= 2) {
                    
                    $da['re_pathb'] = $list['re_pathb'] . $list['id'] . ','; // 开通路径
                    $fck->where('id=' . $value)->save($da);
                } else {
                    
                    $kt_id = $list['kt_id'];
                    
                    $one = $this->cate($kt_id);
                    $two = $fck->where('id=' . $one)
                        ->field('*')
                        ->find();
                    
                    $da['re_pathb'] = $two['re_pathb'] . $two['id'] . ','; // 开通路径
                    $fck->where('id=' . $value)->save($da);
                }
            }
            $rs1 = $fck->where($where)->setField('is_agent', '2');
            
            if ($rs1) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '设置报单中心成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '设置报单中心失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误！');
            exit();
        }
    }

    public function cate($id = 0)
    {
        $fck = M('fck');
        $res = $fck->where('id=' . $id)
            ->field('id,kt_id,is_agent')
            ->find();
        // print_r($res);die;
        
        if ($res) {
            
            if ($res['is_agent'] == 2) {
                
                $arr = $res['id'];
            } else {
                $ar = $res['kt_id'];
                $arr = $res['id'];
                $arr = $this->cate($ar);
            }
            
            return $arr;
        }
    }
    
    // 开启分红奖
    private function _OpenFh($PTid = 0)
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $nowday = strtotime(date('Y-m-d'));
            $where['is_lockfh'] = array(
                'egt',
                1
            );
            $where['_string'] = 'id>1';
            $where['id'] = array(
                'in',
                $PTid
            );
            $varray = array(
                'is_lockfh' => '0',
                'fanli_time' => $nowday
            );
            $rs = $fck->where($where)->setField($varray);
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '开启分红奖成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '开启分红奖失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误!');
//         }
    }
    // 关闭分红奖
    private function _LockFh($PTid = 0)
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where['is_lockfh'] = array(
                'egt',
                0
            );
            $where['_string'] = 'id>1';
            $where['id'] = array(
                'in',
                $PTid
            );
            $rs = $fck->where($where)->setField('is_lockfh', '1');
            
            if ($rs) {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(1, '关闭分红奖成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminMenber';
                $this->_box(0, '关闭分红奖失败！', $bUrl, 1);
                exit();
            }
//         } else {
//             $this->error('错误!');
//         }
    }

    public function adminMenberUP()
    {
        // 会员晋级
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $PTid = (int) $_GET['UP_ID'];
            $rs = $fck->find($PTid);
            
            if (! $rs) {
                $this->error('非法操作！');
                exit();
            }
            
            switch ($rs['u_level']) {
                case 1:
                    $fck->query("UPDATE `xt_fck` SET u_level=2,b12=2000 where id=" . $PTid);
                    break;
                case 2:
                    $fck->query("UPDATE `xt_fck` SET u_level=3,b12=4000 where id=" . $PTid);
                    break;
            }
            
            unset($fck, $PTid);
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, '晋升！', $bUrl, 1);
        } else {
            $this->error('错误!');
        }
    }
    
    // =================================================管理员帮会员提现处理
    public function adminMenberCurrency($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $where = array(); //
            $tiqu = M('tiqu');
            // 查询条件
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['agent_use'] = array(
                'egt',
                100
            );
            $field = 'id,user_id,agent_use,bank_name,bank_card,user_name';
            $fck_rs = $fck->where($where)
                ->field($field)
                ->select();
            
            $data = array();
            $tiqu_where = array();
            $eB = 0.02; // 提现税收
            $nowdate = strtotime(date('c'));
            foreach ($fck_rs as $vo) {
                $is_qf = 0; // 区分上次有没有提现
                $ePoints = 0;
                $ePoints = $vo['agent_use'];
                
                $tiqu_where['uid'] = $vo['id'];
                $tiqu_where['is_pay'] = 0;
                $trs = $tiqu->where($tiqu_where)
                    ->field('id')
                    ->find();
                if ($trs) {
                    $is_qf = 1;
                }
                // 提现税收
                // if ($ePoints >= 10 && $ePoints <= 100){
                // $ePoints1 = $ePoints - 2;
                // }else{
                // $ePoints1 = $ePoints - $ePoints * $eB;//(/100);
                // }
                
                if ($is_qf == 0) {
                    $fck->query("UPDATE `xt_fck` SET `zsq`=zsq+agent_use,`agent_use`=0 where `id`=" . $vo['id']);
                    // 开始事务处理
                    $data['uid'] = $vo['id'];
                    $data['user_id'] = $vo['user_id'];
                    $data['rdt'] = $nowdate;
                    $data['money'] = $ePoints;
                    $data['money_two'] = $ePoints;
                    $data['is_pay'] = 1;
                    $data['user_name'] = $vo['user_name'];
                    $data['bank_name'] = $vo['bank_name'];
                    $data['bank_card'] = $vo['bank_card'];
                    $tiqu->add($data);
                }
            }
            unset($fck, $where, $tiqu, $field, $fck_rs, $data, $tiqu_where, $eB, $nowdate);
            $bUrl = __URL__ . '/adminMenber';
            $this->_box(1, '奖金提现！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误!');
            exit();
        }
    }
    
    // ===============================================消费管理
    public function adminXiaofei()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenqixf') {
            $xiaof = M('xiaof');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                $map['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
            }
            
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $xiaof->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $xiaof->where($map)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $this->display('adminXiaofei');
        } else {
            $this->error('错误!');
            exit();
        }
    }
    // 处理消费
    public function adminXiaofeiAC()
    {
        // 处理提交按钮
        $action = $_POST['action'];
        // 获取复选框的值
        $PTid = $_POST['tabledb'];
        $fck = M('fck');
        // if (!$fck->autoCheckToken($_POST)){
        // $this->error('页面过期，请刷新页面！');
        // exit;
        // }
        if (empty($PTid)) {
            $bUrl = __URL__ . '/adminXiaofei';
            $this->_box(0, '请选择！', $bUrl, 1);
            exit();
        }
        switch ($action) {
            case '确认':
                $this->_adminXiaofeiConfirm($PTid);
                break;
            case '删除':
                $this->_adminXiaofeiDel($PTid);
                break;
            default:
                $bUrl = __URL__ . '/adminXiaofei';
                $this->_box(0, '没有该记录！', $bUrl, 1);
                break;
        }
    }
    
    // ====================================================确认消费
    private function _adminXiaofeiConfirm($PTid)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenqixf') {
            $xiaof = M('xiaof');
            $fck = M('fck'); //
            $where = array();
            $where['is_pay'] = 0; // 未审核的申请
            $where['id'] = array(
                'in',
                $PTid
            ); // 所有选中的会员ID
            $rs = $xiaof->where($where)->select(); // tiqu表要通过的申请记录数组
            $history = M('history');
            $data = array();
            $fck_where = array();
            $nowdate = strtotime(date('c'));
            foreach ($rs as $rss) {
                // 开始事务处理
                $fck->startTrans();
                // 明细表
                $data['uid'] = $rss['uid'];
                $data['user_id'] = $rss['user_id'];
                $data['action_type'] = '重复消费';
                $data['pdt'] = $nowdate;
                $data['epoints'] = - $rss['money'];
                $data['bz'] = '重复消费';
                $data['did'] = 0;
                $data['allp'] = 0;
                $history->create();
                $rs1 = $history->add($data);
                if ($rs1) {
                    // 提交事务
                    $xiaof->execute("UPDATE __TABLE__ set `is_pay`=1 where `id`=" . $rss['id']);
                    $fck->commit();
                } else {
                    // 事务回滚：
                    $fck->rollback();
                }
            }
            unset($xiaof, $fck, $where, $rs, $history, $data, $nowdate, $fck_where);
            $bUrl = __URL__ . '/adminXiaofei';
            $this->_box(1, '确认消费成功！', $bUrl, 1);
        } else {
            $this->error('错误!');
            exit();
        }
    }
    // 删除消费
    private function _adminXiaofeiDel($PTid)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssShenqixf') {
            $xiaof = M('xiaof');
            $where = array();
            $where['is_pay'] = 0;
            $where['id'] = array(
                'in',
                $PTid
            );
            $trs = $xiaof->where($where)->select();
            $fck = M('fck');
            foreach ($trs as $vo) {
                $fck->execute("UPDATE __TABLE__ SET agent_cash=agent_cash+{$vo['money']} WHERE id={$vo['uid']}");
            }
            $rs = $xiaof->where($where)->delete();
            if ($rs) {
                $bUrl = __URL__ . '/adminXiaofei';
                $this->_box(1, '删除成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminXiaofei';
                $this->_box(1, '删除成功！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
            exit();
        }
    }

    public function financeDaoChu_ChuN()
    {
        // 导出excel
        set_time_limit(0);
        
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=Cashier.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $m_page = (int) $_GET['p'];
        if (empty($m_page)) {
            $m_page = 1;
        }
        
        $times = M('times');
        $Numso = array();
        $Numss = array();
        $map = 'is_count=0';
        // 查询字段
        $field = '*';
        import("@.ORG.ZQPage"); // 导入分页类
        $count = $times->where($map)->count(); // 总页数
        $listrows = C('PAGE_LISTROWS'); // 每页显示的记录数
        $s_p = $listrows * ($m_page - 1) + 1;
        $e_p = $listrows * ($m_page);
        
        $title = "当期出纳 第" . $s_p . "-" . $e_p . "条 导出时间:" . date("Y-m-d   H:i:s");
        
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="6"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>期数</td>";
        echo "<td>结算时间</td>";
        echo "<td>当期收入</td>";
        echo "<td>当期支出</td>";
        echo "<td>当期盈利</td>";
        echo "<td>拨出比例</td>";
        echo '</tr>';
        // 输出内容
        
        $rs = $times->where($map)
            ->order(' id desc')
            ->find();
        $Numso['0'] = 0;
        $Numso['1'] = 0;
        $Numso['2'] = 0;
        if ($rs) {
            $eDate = strtotime(date('c')); // time()
            $sDate = $rs['benqi']; // 时间
            
            $this->MiHouTaoBenQi($eDate, $sDate, $Numso, 0);
        }
        
        $page_where = ''; // 分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        // ===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); // 分页变量
        $list = $times->where($map)
            ->field($field)
            ->order('id desc')
            ->page($Page->getPage() . ',' . $listrows)
            ->select();
        
        // dump($list);exit;
        
        $occ = 1;
        $Numso['1'] = $Numso['1'] + $Numso['0'];
        $Numso['3'] = $Numso['3'] + $Numso['0'];
        $maxnn = 0;
        foreach ($list as $Roo) {
            
            $eDate = $Roo['benqi']; // 本期时间
            $sDate = $Roo['shangqi']; // 上期时间
            $Numsd = array();
            $Numsd[$occ][0] = $eDate;
            $Numsd[$occ][1] = $sDate;
            
            $this->MiHouTaoBenQi($eDate, $sDate, $Numss, 1);
            // $Numoo = $Numss['0']; //当期收入
            $Numss[$occ]['0'] = $Numss['0'];
            $Dopp = M('bonus');
            $field = '*';
            $where = " s_date>= '" . $sDate . "' And e_date<= '" . $eDate . "' ";
            $rsc = $Dopp->where($where)
                ->field($field)
                ->select();
            $Numss[$occ]['1'] = 0;
            $nnn = 0;
            foreach ($rsc as $Roc) {
                $nnn ++;
                $Numss[$occ]['1'] += $Roc['b0']; // 当期支出
                $Numb2[$occ]['1'] += $Roc['b1'];
                $Numb3[$occ]['1'] += $Roc['b2'];
                $Numb4[$occ]['1'] += $Roc['b3'];
                // $Numoo += $Roc['b9'];//当期收入
            }
            $maxnn += $nnn;
            $Numoo = $Numss['0']; // 当期收入
            $Numss[$occ]['2'] = $Numoo - $Numss[$occ]['1']; // 本期赢利
            $Numss[$occ]['3'] = substr(floor(($Numss[$occ]['1'] / $Numoo) * 100), 0, 3); // 本期拔比
            $Numso['1'] += $Numoo; // 收入合计
            $Numso['2'] += $Numss[$occ]['1']; // 支出合计
            $Numso['3'] += $Numss[$occ]['2']; // 赢利合计
            $Numso['4'] = substr(floor(($Numso['2'] / $Numso['1']) * 100), 0, 3); // 总拔比
            $Numss[$occ]['4'] = substr(($Numb2[$occ]['1'] / $Numoo) * 100, 0, 4); // 小区奖金拔比
            $Numss[$occ]['5'] = substr(($Numb3[$occ]['1'] / $Numoo) * 100, 0, 4); // 互助基金拔比
            $Numss[$occ]['6'] = substr(($Numb4[$occ]['1'] / $Numoo) * 100, 0, 4); // 管理基金拔比
            $Numss[$occ]['7'] = $Numb2[$occ]['1']; // 小区奖金
            $Numss[$occ]['8'] = $Numb3[$occ]['1']; // 互助基金
            $Numss[$occ]['9'] = $Numb4[$occ]['1']; // 管理基金
            $Numso['5'] += $Numb2[$occ]['1']; // 小区奖金合计
            $Numso['6'] += $Numb3[$occ]['1']; // 互助基金合计
            $Numso['7'] += $Numb4[$occ]['1']; // 管理基金合计
            $Numso['8'] = substr(($Numso['5'] / $Numso['1']) * 100, 0, 4); // 小区奖金总拔比
            $Numso['9'] = substr(($Numso['6'] / $Numso['1']) * 100, 0, 4); // 互助基金总拔比
            $Numso['10'] = substr(($Numso['7'] / $Numso['1']) * 100, 0, 4); // 管理基金总拔比
            $occ ++;
        }
        
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            echo '<tr align=center>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . date("Y-m-d H:i:s", $row['benqi']) . '</td>';
            echo '<td>' . $Numss[$i][0] . '</td>';
            echo '<td>' . $Numss[$i][1] . '</td>';
            echo '<td>' . $Numss[$i][2] . '</td>';
            echo '<td>' . $Numss[$i][3] . ' % </td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function financeDaoChu_JJCX()
    {
        // 导出excel
        set_time_limit(0);
        
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=Bonus-query.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $m_page = (int) $_REQUEST['p'];
        if (empty($m_page)) {
            $m_page = 1;
        }
        $fee = M('fee'); // 参数表
        $times = M('times');
        $bonus = M('bonus'); // 奖金表
        $fee_rs = $fee->field('s18')->find();
        $fee_s7 = explode('|', $fee_rs['s18']);
        
        $where = array();
        $sql = '';
        if (isset($_REQUEST['FanNowDate'])) { // 日期查询
            if (! empty($_REQUEST['FanNowDate'])) {
                $time1 = strtotime($_REQUEST['FanNowDate']); // 这天 00:00:00
                $time2 = strtotime($_REQUEST['FanNowDate']) + 3600 * 24 - 1; // 这天 23:59:59
                $sql = "where e_date >= $time1 and e_date <= $time2";
            }
        }
        
        $field = '*';
        import("@.ORG.ZQPage"); // 导入分页类
        $count = count($bonus->query("select id from __TABLE__ " . $sql . " group by did")); // 总记录数
        $listrows = C('PAGE_LISTROWS'); // 每页显示的记录数
        $page_where = 'FanNowDate=' . $_REQUEST['FanNowDate']; // 分页条件
        if (! empty($page_where)) {
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        } else {
            $Page = new ZQPage($count, $listrows, 1, 0, 3);
        }
        // ===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); // 分页变量
        $status_rs = ($Page->getPage() - 1) * $listrows;
        $list = $bonus->query("select e_date,did,sum(b0) as b0,sum(b1) as b1,sum(b2) as b2,sum(b3) as b3,sum(b4) as b4,sum(b5) as b5,sum(b6) as b6,sum(b7) as b7,sum(b8) as b8,max(type) as type from __TABLE__ " . $sql . " group by did  order by did desc limit " . $status_rs . "," . $listrows);
        // =================================================
        
        $s_p = $listrows * ($m_page - 1) + 1;
        $e_p = $listrows * ($m_page);
        
        $title = "奖金查询 第" . $s_p . "-" . $e_p . "条 导出时间:" . date("Y-m-d   H:i:s");
        
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="10"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>结算时间</td>";
        echo "<td>" . $fee_s7[0] . "</td>";
        echo "<td>" . $fee_s7[1] . "</td>";
        echo "<td>" . $fee_s7[2] . "</td>";
        echo "<td>" . $fee_s7[3] . "</td>";
        echo "<td>" . $fee_s7[4] . "</td>";
        echo "<td>" . $fee_s7[5] . "</td>";
        echo "<td>" . $fee_s7[6] . "</td>";
        echo "<td>合计</td>";
        echo "<td>实发</td>";
        echo '</tr>';
        // 输出内容
        
        // dump($list);exit;
        
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $mmm = $row['b1'] + $row['b2'] + $row['b3'] + $row['b4'] + $row['b5'] + $row['b6'] + $row['b7'];
            echo '<tr align=center>';
            echo '<td>' . date("Y-m-d H:i:s", $row['e_date']) . '</td>';
            echo "<td>" . $row['b1'] . "</td>";
            echo "<td>" . $row['b2'] . "</td>";
            echo "<td>" . $row['b3'] . "</td>";
            echo "<td>" . $row['b4'] . "</td>";
            echo "<td>" . $row['b5'] . "</td>";
            echo "<td>" . $row['b6'] . "</td>";
            echo "<td>" . $row['b7'] . "</td>";
            echo "<td>" . $mmm . "</td>";
            echo "<td>" . $row['b0'] . "</td>";
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // 会员表
    public function financeDaoChu_MM()
    {
        // 导出excel
        set_time_limit(0);
        
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=Member.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $fck = M('fck'); // 奖金表
        
        $map = array();
        $map['id'] = array(
            'gt',
            0
        );
        $field = '*';
        $list = $fck->where($map)
            ->field($field)
            ->order('pdt asc')
            ->select();
        
        $title = "会员表 导出时间:" . date("Y-m-d   H:i:s");
        
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="10"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>姓名</td>";
        echo "<td>银行卡号</td>";
        echo "<td>开户行地址</td>";
        echo "<td>联系电话</td>";
        echo "<td>联系地址</td>";
        echo "<td>QQ号</td>";
        echo "<td>身份证号</td>";
        echo "<td>注册时间</td>";
        echo "<td>开通时间</td>";
        echo "<td>总奖金</td>";
        echo "<td>剩余奖金</td>";
        echo "<td>剩余注册币</td>";
        echo '</tr>';
        // 输出内容
        
        // dump($list);exit;
        
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . sprintf('%s', (string) chr(28) . $row['bank_card'] . chr(28)) . "</td>";
            echo "<td>" . $row['bank_province'] . $row['bank_city'] . $row['bank_address'] . "</td>";
            echo "<td>" . $row['user_tel'] . "&nbsp;</td>";
            echo "<td>" . $row['user_address'] . "</td>";
            echo "<td>" . $row['qq'] . "</td>";
            echo "<td>" . sprintf('%s', (string) chr(28) . $row['user_code'] . chr(28)) . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['rdt']) . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['pdt']) . "</td>";
            echo "<td>" . $row['zjj'] . "</td>";
            echo "<td>" . $row['agent_use'] . "</td>";
            echo "<td>" . $row['agent_cash'] . "</td>";
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // 会员基本信息导出
    public function memberInfoDaochu()
    {
        // 导出excel
        set_time_limit(0);
    
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=会员基础统计信息".date("Y-m-d   H:i:s").".xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        $fck = M('fck'); // 会员表
        $jiadan = M('jiadan'); // A网分红包表
        $jiadanb = M('jiadanb'); // B网红包表
        $zhuanj = M('zhuanj'); // 转账表
        $chongzhi = M('chongzhi'); // 充值表
        $history = M('history'); // 历史记录表
        $promo = M('promo'); // 原点升级表
        $netb = M('netb'); // B网表
        $map = array();
        $map['id'] = array('gt',0);
        $map['is_pay'] = array('eq',1);
        $field = '*';
        $list = $fck->where($map)->field($field)->order('pdt desc')->select();
    
        $title = "会员基础统计信息 导出时间:" . date("Y-m-d   H:i:s");
    
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="10"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>注册金额</td>";
        echo "<td>注册单数</td>";
        echo "<td>复投单数</td>";
        echo "<td>现金积分余额</td>";
        echo "<td>电子积分余额</td>";
        echo "<td>A网复投积分余额</td>";
        echo "<td>B网复投积分余额</td>";
        echo "<td>B网注册单数</td>";
        echo "<td>B网复投单数</td>";
        echo "<td>激活积分余额</td>";
        echo "<td>注册时间</td>";
        echo "<td>开通时间</td>";
        echo "<td>是否为报单中心</td>";
        echo "<td>网体总业绩</td>";
        echo "<td>销售总业绩</td>";
        echo "<td>推荐人编号</td>";
        echo "<td>所属报单中心</td>";
        echo "<td>领导人级别</td>";
        echo "<td>A网总分红包</td>";
        echo "<td>A网未出局分红包</td>";
        echo "<td>B网总分红包</td>";
        echo "<td>B网未出局分红包</td>";
        echo "<td>提现金额</td>";
        echo "<td>累计转出电子积分</td>";
        echo "<td>累计转入电子积分</td>";
        echo "<td>累计转出现金积分</td>";
        echo "<td>累计转入现金积分</td>";
        echo "<td>累计转出激活积分</td>";
        echo "<td>累计转入激活积分</td>";
        echo "<td>累计转入复投积分</td>";
        echo "<td>累计充入现金积分</td>";
        echo "<td>累计充入电子积分</td>";
        echo "<td>累计充入激活积分</td>";
        echo "<td>个人累计电子积分复投单数</td>";
        echo "<td>个人累计电子积分复投金额</td>";
        echo "<td>销售团队累计电子币复投单数</td>";
        echo "<td>销售团队累计电子币复投金额</td>";
        echo "<td>个人累计现金积分复投单数</td>";
        echo "<td>个人累计现金积分复投金额</td>";
        echo "<td>个人累计复投积分复投单数</td>";
        echo "<td>个人累计复投积分复投金额</td>";
        echo "<td>个人原点升级金额</td>";
        echo "<td>销售团队原点升级金额</td>";
        echo '</tr>';
        // 输出内容
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['cpzj'] . "</td>";
            echo "<td>" . $row['f4'] . "</td>";
            echo "<td>" . $row['is_cc'] . "</td>";
            echo "<td>" . $row['agent_use'] . "</td>";
            echo "<td>" . $row['agent_cash'] . "</td>";
            echo "<td>" . $row['agent_xf'] . "</td>";
            $netb_rs = $netb->where("uid = ".$row['id'])->field("agent_futou,register_danshu,futou_danshu")->find();
            if ($netb_rs == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $netb_rs['agent_futou'] . "</td>";
            }
            if ($netb_rs == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $netb_rs['register_danshu'] . "</td>";
            }
            if ($netb_rs == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $netb_rs['futou_danshu'] . "</td>";
            }
            echo "<td>" . $row['agent_active'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['rdt']) . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['pdt']) . "</td>";
            if ($row['is_agent'] == 2) {
                echo "<td>" . "是" . "</td>";
            } else{
                echo "<td>" . "否" . "</td>";
            }
            $treeAch = $fck->where("p_path like '%,".$row['id'].",%'")->sum('cpzj');
            if ($treeAch == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $treeAch . "</td>";
            }
            $recommandAch = $fck->where("re_path like '%,".$row['id'].",%'")->sum('cpzj');
            if ($recommandAch == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $recommandAch . "</td>";
            }
            
            echo "<td>" . $row['re_name'] . "</td>";
            echo "<td>" . $row['shop_name'] . "</td>";
            if ($row['sh_level'] == 0) {
                echo "<td>" . "普通会员" . "</td>";
            } else if ($row['sh_level'] == 1) {
                echo "<td>" . "一星" . "</td>";
            } else if ($row['sh_level'] == 2) {
                echo "<td>" . "二星" . "</td>";
            } else if ($row['sh_level'] == 3) {
                echo "<td>" . "三星" . "</td>";
            } else if ($row['sh_level'] == 4) {
                echo "<td>" . "四星" . "</td>";
            } else if ($row['sh_level'] == 5) {
                echo "<td>" . "五星" . "</td>";
            }
            $danshuAll = $jiadan->where("user_id = '".$row['user_id']."'")->sum("danshu");
            $moneyAll = $jiadan->where("user_id = '".$row['user_id']."'")->sum("money");
            $danshuIn = $danshuAll - floor(bcdiv($moneyAll, 1000,5));
            if ($danshuAll == null) {
                echo "<td>" . 0 . "</td>";
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $danshuAll . "</td>";
                echo "<td>" . $danshuIn . "</td>";
            }
            $danshuAll = $jiadanb->where("user_id = '".$row['user_id']."'")->sum("danshu");
            $moneyAll = $jiadanb->where("user_id = '".$row['user_id']."'")->sum("money");
            $danshuIn = $danshuAll - floor(bcdiv($moneyAll, 1000,5));
            if ($danshuAll == null) {
                echo "<td>" . 0 . "</td>";
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $danshuAll . "</td>";
                echo "<td>" . $danshuIn . "</td>";
            }
            echo "<td>" . $row['shang_ach'] . "</td>";
            
            $epoint = $zhuanj->where("out_userid ='".$row['user_id']."' and (type =1 or type =4)")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $zhuanj->where("(in_userid ='".$row['user_id']."' and type =2) or (in_userid ='".$row['user_id']."' and type =4)")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $zhuanj->where("out_userid ='".$row['user_id']."' and (type =2 or type =3)")->sum('epoint');
            if ($epoint == null) {
                echo "<td>". 0 ."</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $zhuanj->where("in_userid ='".$row['user_id']."' and type =1")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
                
            $epoint = $zhuanj->where("out_userid ='".$row['user_id']."' and type =5")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
                
            $epoint = $zhuanj->where("in_userid ='".$row['user_id']."' and type =5")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
                
            $epoint = $zhuanj->where("in_userid ='".$row['user_id']."' and type =3")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $chongzhi->where("user_id ='".$row['user_id']."' and stype =1 and is_pay = 1")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $chongzhi->where("user_id ='".$row['user_id']."' and stype =0 and is_pay = 1")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $chongzhi->where("user_id ='".$row['user_id']."' and stype =2 and is_pay = 1")->sum('epoint');
            if ($epoint == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $history->where("user_id ='".$row['user_id']."' and action_type =29")->sum('epoints');
            
            if ($epoint == null || $epoint == 0) {
                echo "<td>" . 0 . "</td>";
                echo "<td>" . 0 . "</td>";
            } else {
                $ftdanshu = $epoint/800;
                echo "<td>" . $ftdanshu . "</td>";
                echo "<td>" . $epoint . "</td>";
            }
            $idArray = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1")->field("id")->select();
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29")->sum('epoints');
            }
            if ($cashMoney == null || $cashMoney == 0) {
                echo "<td>" . 0 . "</td>";
                echo "<td>" . 0 . "</td>";
            } else {
                $ftdanshu = $cashMoney/800;
                echo "<td>" . $ftdanshu . "</td>";
                echo "<td>" . $cashMoney . "</td>";
            }
            $epoint = $history->where("user_id ='".$row['user_id']."' and action_type =25")->sum('epoints');
            
            if ($epoint == null || $epoint == 0) {
                echo "<td>" . 0 . "</td>";
                echo "<td>" . 0 . "</td>";
            } else {
                $ftdanshu = $epoint/800;
                echo "<td>" . $ftdanshu . "</td>";
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $history->where("user_id ='".$row['user_id']."' and action_type =24")->sum('epoints');
            
            if ($epoint == null || $epoint == 0) {
                echo "<td>" . 0 . "</td>";
                echo "<td>" . 0 . "</td>";
            } else {
                $ftdanshu = $epoint/800;
                echo "<td>" . $ftdanshu . "</td>";
                echo "<td>" . $epoint . "</td>";
            }
            $epoint = $promo->where("user_id ='".$row['user_id']."' and is_pay =1")->sum('money');
            
            if ($epoint == null || $epoint == 0) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $epoint . "</td>";
            }
            $idArray = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1")->field("id")->select();
            $promoMoney = 0;
            foreach ($idArray as $key =>$value) {
                $promoMoney += $promo->where("uid = ".$value['id']."and is_pay =1")->sum('money');
            }
            if ($promoMoney == null || $promoMoney == 0) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $promoMoney . "</td>";
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // 转账数据导出
    public function transferDataDaochu()
    {
        // 导出excel
        set_time_limit(0);
    
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=转账数据信息".date("Y-m-d   H:i:s").".xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $fck = M('fck'); // 会员表
        $zhuanj = M('zhuanj'); // 转账表
    
        $field = 'in_userid,out_userid,epoint,rdt,type';
        $list = $zhuanj->field($field)->order('rdt desc')->select();
    
        $title = "转账数据信息 导出时间:" . date("Y-m-d   H:i:s");
    
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="5"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>转出会员ID</td>";
        echo "<td>转入会员ID</td>";
        echo "<td>转账金额</td>";
        echo "<td>转账时间</td>";
        echo '</tr>';
        // 输出内容
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['out_userid'] . "</td>";
            echo "<td>" . $row['in_userid'] . "</td>";
            echo "<td>" . $row['epoint'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['rdt']) . "</td>";
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // 个人基本信息导出
    public function memberDataDaochu()
    {
        // 导出excel
        set_time_limit(0);
    
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=会员个人信息".date("Y-m-d   H:i:s").".xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
    
        $relation = M('relation'); // 会员个人信息
        $field = 'user_id_encrypt,user_name,nickname,user_code,user_tel,bank_name,bank_card,bank_province,bank_city,bank_address';
        $list = $relation->field($field)->order('id desc')->select();
    
        $title = "会员个人信息 导出时间:" . date("Y-m-d   H:i:s");
    
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="10"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>用户姓名</td>";
        echo "<td>昵称</td>";
        echo "<td>身份证号</td>";
        echo "<td>手机号</td>";
        echo "<td>开户银行</td>";
        echo "<td>银行卡号</td>";
        echo "<td>开户省份</td>";
        echo "<td>开户城市</td>";
        echo "<td>具体开户行地址</td>";
        echo '</tr>';
        // 输出内容
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id_encrypt'] . "</td>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['nickname'] . "</td>";
            echo "<td>" . sprintf('%s', (string) chr(28) . $row['user_code'] . chr(28)) . "</td>";
            echo "<td>" . $row['user_tel'] . "</td>";
            echo "<td>" . $row['bank_name'] . "</td>";
            echo "<td>" . sprintf('%s', (string) chr(28) . $row['bank_card'] . chr(28)) . "</td>";
            echo "<td>" . $row['bank_province'] . "</td>";
            echo "<td>" . $row['bank_city'] . "</td>";
            echo "<td>" . $row['bank_address'] . "</td>";
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // 个人基本信息删除
    public function memberDataDel()
    {
        $relation = M('relation');
        $rs = $relation->where('ID > 0')->delete();
        
        $bUrl = __URL__ . '/adminMenber';
        $this->_box(1, '个人基本信息清空完毕！', $bUrl, 1);
        exit();
    }
    
    // 提现已确认信息导出
    public function withdrawDataDaochu()
    {
        // 导出excel
        set_time_limit(0);
    
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=提现已确认信息".date("Y-m-d   H:i:s").".xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $fck = M('fck'); // 会员表
        $tiqu = M('tiqu'); // 提现已确认信息
        $field = 'user_id,money,money_two,rdt,is_pay';
        $map = array();
        $map['is_pay'] = array('eq',1);
        $list = $tiqu->where($map)->field($field)->order('rdt desc')->select();
    
        $title = "提现已确认信息 导出时间:" . date("Y-m-d   H:i:s");
    
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="6"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>注册金额</td>";
        echo "<td>提现金额</td>";
        echo "<td>实发金额</td>";
        echo "<td>提现时间</td>";
        echo "<td>确认状态</td>";
        echo '</tr>';
        // 输出内容
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            $fck_rs = $fck->where("user_id = '".$row['user_id']."'")->find();
            echo "<td>" . $fck_rs['cpzj'] . "</td>";
            echo "<td>" . $row['money'] . "</td>";
            echo "<td>" . $row['money_two'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['rdt']) . "</td>";
            if ($row['is_pay'] == 1) {
                echo "<td>已确认</td>";
            } else {
                echo "<td>未确认</td>";
            }
            
            echo '</tr>';
        }
        echo '</table>';
    }
    
    // 提现未确认信息导出
    public function withdrawData2Daochu()
    {
        // 导出excel
        set_time_limit(0);
    
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=提现未确认信息".date("Y-m-d   H:i:s").".xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
    
        $fck = M('fck'); // 会员表
        $tiqu = M('tiqu'); // 提现已确认信息
        $field = 'user_id,money,money_two,rdt,is_pay';
        $map = array();
        $map['is_pay'] = array('eq',0);
        $list = $tiqu->field($field)->where($map)->order('rdt desc')->select();
    
        $title = "提现未确认信息 导出时间:" . date("Y-m-d   H:i:s");
    
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="6"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>注册金额</td>";
        echo "<td>提现金额</td>";
        echo "<td>实发金额</td>";
        echo "<td>提现时间</td>";
        echo "<td>确认状态</td>";
        echo '</tr>';
        // 输出内容
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            $fck_rs = $fck->where("user_id = '".$row['user_id']."'")->find();
            echo "<td>" . $fck_rs['cpzj'] . "</td>";
            echo "<td>" . $row['money'] . "</td>";
            echo "<td>" . $row['money_two'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['rdt']) . "</td>";
            if ($row['is_pay'] == 1) {
                echo "<td>已确认</td>";
            } else {
                echo "<td>未确认</td>";
            }
    
            echo '</tr>';
        }
        echo '</table>';
    }
    // 业绩月统计数据导出
    public function monthDataDaochu($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssGuanShuiPuTao') {
            $fck = M('fck');
            $history = M('history');
            $promo = M('promo');
            $UserID = $_REQUEST['UserID'];
            $map = array();
            if (! empty($UserID)) {
                $fck_rs = $fck->where("user_id=".$UserID)->field("*")->find();
            } else {
                $fck_rs = $fck->where("id > 0")->field("*")->select();
            }
        set_time_limit(0);
    
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=业绩月统计数据.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
    
        $title = "业绩月统计数据 导出时间:" . date("Y-m-d   H:i:s");
    
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="6"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>注册金额</td>";
        echo "<td>注册时间</td>";
        echo "<td>销售网体注册会员以及原点升级1月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级2月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级3月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级4月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级5月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级6月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级7月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级8月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级9月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级10月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级11月份统计</td>";
        echo "<td>销售网体注册会员以及原点升级12月份统计</td>";
        echo "<td>销售网体电子币复投1月份统计</td>";
        echo "<td>销售网体电子币复投2月份统计</td>";
        echo "<td>销售网体电子币复投3月份统计</td>";
        echo "<td>销售网体电子币复投4月份统计</td>";
        echo "<td>销售网体电子币复投5月份统计</td>";
        echo "<td>销售网体电子币复投6月份统计</td>";
        echo "<td>销售网体电子币复投7月份统计</td>";
        echo "<td>销售网体电子币复投8月份统计</td>";
        echo "<td>销售网体电子币复投9月份统计</td>";
        echo "<td>销售网体电子币复投10月份统计</td>";
        echo "<td>销售网体电子币复投11月份统计</td>";
        echo "<td>销售网体电子币复投12月份统计</td>";
        echo '</tr>';
        // 输出内容
        $i = 0;
        foreach ($fck_rs as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['cpzj'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['rdt']) . "</td>";
            // 1月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =1")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 2月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =2")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 3月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =3")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 4月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =4")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 5月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =5")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 6月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =6")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 7月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =7")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 8月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =8")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 9月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =9")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 10月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =10")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 11月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =11")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 12月份
            $money = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1 and MONTH(FROM_UNIXTIME(rdt)) =12")->sum('cpzj');
            if ($money == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $money . "</td>";
            }
            // 一月份
            $idArray = $fck->where("re_path like '%,".$row['id'].",%' and is_pay = 1")->field("id")->select();
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =1")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            
            // 二月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =2")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            
            // 三月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =3")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            
            // 四月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =4")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 五月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =5")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 六月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =6")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 七月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =7")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 八月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =8")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 九月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =9")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 十月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =10")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 十一月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =11")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            // 十二月份
            $cashMoney = 0;
            foreach ($idArray as $key =>$value) {
                $cashMoney += $history->where("uid =".$value['id']."' and action_type =29 and MONTH(FROM_UNIXTIME(rdt)) =12")->sum('epoints');
            }
            if ($cashMoney == null) {
                echo "<td>" . 0 . "</td>";
            } else {
                echo "<td>" . $cashMoney . "</td>";
            }
            
    
            echo '</tr>';
        }
        echo '</table>';
        } else {
            $this->error('数据错误!');
            exit();
        }
    }
    
    // 报单中心表
    public function financeDaoChu_BD()
    {
        // 导出excel
        set_time_limit(0);
        
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=Member-Agent.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        
        $fck = M('fck'); // 奖金表
        
        $map = array();
        $map['id'] = array(
            'gt',
            0
        );
        $map['is_agent'] = array(
            'gt',
            0
        );
        $field = '*';
        $list = $fck->where($map)
            ->field($field)
            ->order('idt asc,adt asc')
            ->select();
        
        $title = "报单中心表 导出时间:" . date("Y-m-d   H:i:s");
        
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="9"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>序号</td>";
        echo "<td>会员编号</td>";
        echo "<td>姓名</td>";
        echo "<td>联系电话</td>";
        echo "<td>申请时间</td>";
        echo "<td>确认时间</td>";
        echo "<td>类型</td>";
        echo "<td>报单中心区域</td>";
        echo "<td>剩余注册币</td>";
        echo '</tr>';
        // 输出内容
        
        // dump($list);exit;
        
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            } else {
                $num = $i;
            }
            if ($row['shoplx'] == 1) {
                $nnn = '报单中心';
            } elseif ($row['shoplx'] == 2) {
                $nnn = '县/区代理商';
            } else {
                $nnn = '市级代理商';
            }
            
            echo '<tr align=center>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo "<td>" . $row['user_id'] . "</td>";
            echo "<td>" . $row['user_name'] . "</td>";
            echo "<td>" . $row['user_tel'] . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['idt']) . "</td>";
            echo "<td>" . date("Y-m-d H:i:s", $row['adt']) . "</td>";
            echo "<td>" . $nnn . "</td>";
            echo "<td>" . $row['shop_a'] . " / " . $row['shop_b'] . "</td>";
            echo "<td>" . $row['agent_cash'] . "</td>";
            echo '</tr>';
        }
        echo '</table>';
    }

    public function financeDaoChu()
    {
        // 导出excel
        // if ($_SESSION['UrlPTPass'] =='MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
        $title = "数据库名:test,   数据表:test,   备份日期:" . date("Y-m-d   H:i:s");
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=test.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="3"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>银行卡号</td>";
        echo "<td>姓名</td>";
        echo "<td>银行名称</td>";
        echo "<td>省份</td>";
        echo "<td>城市</td>";
        echo "<td>金额</td>";
        echo "<td>所有人的排序</td>";
        echo '</tr>';
        // 输出内容
        $did = (int) $_GET['did'];
        $bonus = M('bonus');
        $map = 'xt_bonus.b0>0 and xt_bonus.did=' . $did;
        // 查询字段
        $field = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
        $field .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
        $field .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
        $field .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
        $field .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
        import("@.ORG.ZQPage"); // 导入分页类
        $count = $bonus->where($map)->count(); // 总页数
        $listrows = 1000000; // 每页显示的记录数
        $page_where = ''; // 分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        // ===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); // 分页变量
        $this->assign('page', $show); // 分页变量输出到模板
        $join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id'; // 连表查询
        $list = $bonus->where($map)
            ->field($field)
            ->join($join)
            ->Distinct(true)
            ->order('id asc')
            ->page($Page->getPage() . ',' . $listrows)
            ->select();
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            }
            echo '<tr align=center>';
            echo '<td>' . sprintf('%s', (string) chr(28) . $row['bank_card'] . chr(28)) . '</td>';
            echo '<td>' . $row['user_name'] . '</td>';
            echo "<td>" . $row['bank_name'] . "</td>";
            echo '<td>' . $row['bank_province'] . '</td>';
            echo '<td>' . $row['bank_city'] . '</td>';
            echo '<td>' . $row['b0'] . '</td>';
            echo '<td>' . chr(28) . $num . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        // }else{
        // $this->error('错误!');
        // exit;
        // }
    }

    public function financeDaoChuTwo1()
    {
        // 导出WPS
        if ($_SESSION['UrlPTPass'] == 'MyssGuanPaoYingTao' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao') {
            $title = "数据库名:test,   数据表:test,   备份日期:" . date("Y-m-d   H:i:s");
            header("Content-Type:   application/vnd.ms-excel");
            header("Content-Disposition:   attachment;   filename=test.xls");
            header("Pragma:   no-cache");
            header("Content-Type:text/html; charset=utf-8");
            header("Expires:   0");
            echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
            // 输出标题
            echo '<tr   bgcolor="#cccccc"><td   colspan="3"   align="center">' . $title . '</td></tr>';
            // 输出字段名
            echo '<tr  align=center>';
            echo "<td>会员编号</td>";
            echo "<td>开会名</td>";
            echo "<td>开户银行</td>";
            echo "<td>银行账户</td>";
            echo "<td>提现金额</td>";
            echo "<td>提现时间</td>";
            echo "<td>所有人的排序</td>";
            echo '</tr>';
            // 输出内容
            $did = (int) $_GET['did'];
            $bonus = M('bonus');
            $map = 'xt_bonus.b0>0 and xt_bonus.did=' . $did;
            // 查询字段
            $field = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
            $field .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
            $field .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
            $field .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
            $field .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $bonus->where($map)->count(); // 总页数
            $listrows = 1000000; // 每页显示的记录数
            $page_where = ''; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id'; // 连表查询
            $list = $bonus->where($map)
                ->field($field)
                ->join($join)
                ->Distinct(true)
                ->order('id asc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $i = 0;
            foreach ($list as $row) {
                $i ++;
                $num = strlen($i);
                if ($num == 1) {
                    $num = '000' . $i;
                } elseif ($num == 2) {
                    $num = '00' . $i;
                } elseif ($num == 3) {
                    $num = '0' . $i;
                }
                $date = date('Y-m-d H:i:s', $row['rdt']);
                
                echo '<tr align=center>';
                echo "<td>'" . $row['user_id'] . '</td>';
                echo '<td>' . $row['user_name'] . '</td>';
                echo "<td>" . $row['bank_name'] . "</td>";
                echo '<td>' . $row['bank_card'] . '</td>';
                echo '<td>' . $row['money'] . '</td>';
                echo '<td>' . $date . '</td>';
                echo "<td>'" . $num . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            $this->error('错误!');
            exit();
        }
    }

    public function financeDaoChuTwo()
    {
        // 导出WPS
        // if ($_SESSION['UrlPTPass'] =='MyssGuanPaoYingTao' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao'){
        $title = "数据库名:test,   数据表:test,   备份日期:" . date("Y-m-d   H:i:s");
        header("Content-Type:   application/vnd.ms-excel");
        header("Content-Disposition:   attachment;   filename=test.xls");
        header("Pragma:   no-cache");
        header("Content-Type:text/html; charset=utf-8");
        header("Expires:   0");
        echo '<table   border="1"   cellspacing="2"   cellpadding="2"   width="50%"   align="center">';
        // 输出标题
        echo '<tr   bgcolor="#cccccc"><td   colspan="3"   align="center">' . $title . '</td></tr>';
        // 输出字段名
        echo '<tr  align=center>';
        echo "<td>银行卡号</td>";
        echo "<td>姓名</td>";
        echo "<td>银行名称</td>";
        echo "<td>省份</td>";
        echo "<td>城市</td>";
        echo "<td>金额</td>";
        echo "<td>所有人的排序</td>";
        echo '</tr>';
        // 输出内容
        $did = (int) $_GET['did'];
        $bonus = M('bonus');
        $map = 'xt_bonus.b0>0 and xt_bonus.did=' . $did;
        // 查询字段
        $field = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
        $field .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
        $field .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
        $field .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
        $field .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
        import("@.ORG.ZQPage"); // 导入分页类
        $count = $bonus->where($map)->count(); // 总页数
        $listrows = 1000000; // 每页显示的记录数
        $page_where = ''; // 分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        // ===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); // 分页变量
        $this->assign('page', $show); // 分页变量输出到模板
        $join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id'; // 连表查询
        $list = $bonus->where($map)
            ->field($field)
            ->join($join)
            ->Distinct(true)
            ->order('id asc')
            ->page($Page->getPage() . ',' . $listrows)
            ->select();
        $i = 0;
        foreach ($list as $row) {
            $i ++;
            $num = strlen($i);
            if ($num == 1) {
                $num = '000' . $i;
            } elseif ($num == 2) {
                $num = '00' . $i;
            } elseif ($num == 3) {
                $num = '0' . $i;
            }
            echo '<tr align=center>';
            echo "<td>'" . sprintf('%s', (string) chr(28) . $row['bank_card'] . chr(28)) . '</td>';
            echo '<td>' . $row['user_name'] . '</td>';
            echo "<td>" . $row['bank_name'] . "</td>";
            echo '<td>' . $row['bank_province'] . '</td>';
            echo '<td>' . $row['bank_city'] . '</td>';
            echo '<td>' . $row['b0'] . '</td>';
            echo "<td>'" . $num . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        // }else{
        // $this->error('错误!');
        // exit;
        // }
    }

    public function financeDaoChuTXT()
    {
        // 导出TXT
        if ($_SESSION['UrlPTPass'] == 'MyssPiPa' || $_SESSION['UrlPTPass'] == 'MyssMiHouTao') {
            // 输出内容
            $did = (int) $_GET['did'];
            $bonus = M('bonus');
            $map = 'xt_bonus.b0>0 and xt_bonus.did=' . $did;
            // 查询字段
            $field = 'xt_bonus.id,xt_bonus.uid,xt_bonus.did,s_date,e_date,xt_bonus.b0,xt_bonus.b1,xt_bonus.b2,xt_bonus.b3';
            $field .= ',xt_bonus.b4,xt_bonus.b5,xt_bonus.b6,xt_bonus.b7,xt_bonus.b8,xt_bonus.b9,xt_bonus.b10';
            $field .= ',xt_fck.user_id,xt_fck.user_tel,xt_fck.bank_card';
            $field .= ',xt_fck.user_name,xt_fck.user_address,xt_fck.nickname,xt_fck.user_phone,xt_fck.bank_province,xt_fck.user_tel';
            $field .= ',xt_fck.user_code,xt_fck.bank_city,xt_fck.bank_name,xt_fck.bank_address';
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $bonus->where($map)->count(); // 总页数
            $listrows = 1000000; // 每页显示的记录数
            $page_where = ''; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $join = 'left join xt_fck ON xt_bonus.uid=xt_fck.id'; // 连表查询
            $list = $bonus->where($map)
                ->field($field)
                ->join($join)
                ->Distinct(true)
                ->order('id asc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $i = 0;
            $ko = "";
            $m_ko = 0;
            foreach ($list as $row) {
                $i ++;
                $num = strlen($i);
                if ($num == 1) {
                    $num = '000' . $i;
                } elseif ($num == 2) {
                    $num = '00' . $i;
                } elseif ($num == 3) {
                    $num = '0' . $i;
                }
                $ko .= $row['bank_card'] . "|" . $row['user_name'] . "|" . $row['bank_name'] . "|" . $row['bank_province'] . "|" . $row['bank_city'] . "|" . $row['b0'] . "|" . $num . "\r\n";
                $m_ko += $row['b0'];
                $e_da = $row['e_date'];
            }
            $m_ko = $this->_2Mal($m_ko, 2);
            $content = $num . "|" . $m_ko . "\r\n" . $ko;
            
            header('Content-Type: text/x-delimtext;');
            header("Content-Disposition: attachment; filename=xt_" . date('Y-m-d H:i:s', $e_da) . ".txt");
            header("Pragma: no-cache");
            header("Content-Type:text/html; charset=utf-8");
            header("Expires: 0");
            echo $content;
            exit();
        } else {
            $this->error('错误!');
            exit();
        }
    }
    
    // 参数设置
    public function setParameter()
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssPingGuoCP') {
            $fee = M('fee');
            $fee_rs = $fee->find();
            $fee_s1 = $fee_rs['s1'];
            $fee_s2 = $fee_rs['s2'];
            $fee_s3 = $fee_rs['s3'];
            $fee_s4 = $fee_rs['s4'];
            $fee_s5 = $fee_rs['s5'];
            $fee_s6 = $fee_rs['s6'];
            $fee_s7 = $fee_rs['s7'];
            $fee_s8 = $fee_rs['s8'];
            $fee_s9 = $fee_rs['s9'];
            $fee_s10 = $fee_rs['s10'];
            $fee_s11 = $fee_rs['s11'];
            $fee_s12 = $fee_rs['s12'];
            $fee_s13 = $fee_rs['s13'];
            $fee_s14 = $fee_rs['s14'];
            $fee_s15 = $fee_rs['s15'];
            $fee_s16 = $fee_rs['s16'];
            $fee_s17 = $fee_rs['s17'];
            $fee_s18 = $fee_rs['s18'];
            
            $fee_str1 = $fee_rs['str1'];
            $fee_str2 = $fee_rs['str2'];
            $fee_str3 = $fee_rs['str3'];
            $fee_str4 = $fee_rs['str4'];
            $fee_str5 = $fee_rs['str5'];
            $fee_str6 = $fee_rs['str6'];
            $fee_str7 = $fee_rs['str7'];
            $fee_str9 = $fee_rs['str9'];
            
            $fee_str10 = $fee_rs['str10'];
            $fee_str11 = $fee_rs['str11'];
            
            $fee_str17 = $fee_rs['str17'];
            $fee_str18 = $fee_rs['str18'];
            $fee_str19 = $fee_rs['str19'];
            
            $fee_str21 = $fee_rs['str21'];
            $fee_str22 = $fee_rs['str22'];
            $fee_str23 = $fee_rs['str23'];
            $fee_str24 = $fee_rs['str24'];
            $fee_str25 = $fee_rs['str25'];
            
            $fee_str27 = $fee_rs['str27'];
            $fee_str28 = $fee_rs['str28'];
            $fee_str29 = $fee_rs['str29'];
            
            $fee_str99 = $fee_rs['str99'];
            
            $a_money = $fee_rs['a_money'];
            $b_money = $fee_rs['b_money'];
            
            // $fee_s20 = explode('|',$fee_rs['s20']);
            $this->assign('fee_s1', $fee_s1);
            $this->assign('fee_s2', $fee_s2);
            $this->assign('fee_s3', $fee_s3);
            $this->assign('fee_s4', $fee_s4);
            $this->assign('fee_s5', $fee_s5);
            $this->assign('fee_s6', $fee_s6);
            $this->assign('fee_s7', $fee_s7);
            $this->assign('fee_s8', $fee_s8);
            $this->assign('fee_s9', $fee_s9);
            $this->assign('fee_s10', $fee_s10);
            $this->assign('fee_s11', $fee_s11);
            $this->assign('fee_s12', $fee_s12);
            $this->assign('fee_s13', $fee_s13);
            $this->assign('fee_s14', $fee_s14);
            $this->assign('fee_s15', $fee_s15);
            $this->assign('fee_s16', $fee_s16);
            $this->assign('fee_s17', $fee_s17);
            $this->assign('fee_s18', $fee_s18);
            // $this -> assign('fee_s20',$fee_s20);
            $this->assign('fee_i1', $fee_rs['i1']);
            $this->assign('fee_i2', $fee_rs['i2']);
            $this->assign('fee_i3', $fee_rs['i3']);
            $this->assign('fee_i4', $fee_rs['i4']);
            $this->assign('fee_i9', $fee_rs['i9']);
            $this->assign('fee_id', $fee_rs['id']); // 记录ID
            
            $this->assign('b_money', $fee_rs['b_money']);
            
            $this->assign('fee_str1', $fee_str1);
            $this->assign('fee_str2', $fee_str2);
            $this->assign('fee_str3', $fee_str3);
            $this->assign('fee_str4', $fee_str4);
            $this->assign('fee_str5', $fee_str5);
            $this->assign('fee_str6', $fee_str6);
            $this->assign('fee_str7', $fee_str7);
            $this->assign('fee_str9', $fee_str9);
            
            $this->assign('fee_str10', $fee_str10);
            $this->assign('fee_str11', $fee_str11);
            
            $this->assign('fee_str17', $fee_str17);
            $this->assign('fee_str18', $fee_str18);
            $this->assign('fee_str19', $fee_str19);
            
            $this->assign('fee_str21', $fee_str21);
            $this->assign('fee_str22', $fee_str22);
            $this->assign('fee_str23', $fee_str23);
            $this->assign('fee_str24', $fee_str24);
            $this->assign('fee_str25', $fee_str25);
            
            $this->assign('fee_str27', $fee_str27);
            $this->assign('fee_str28', $fee_str28);
            $this->assign('fee_str29', $fee_str29);
            $this->assign('fee_str99', $fee_str99);
            
            $this->assign('a_money', $a_money);
            $this->assign('b_money', $b_money);
            
            $this->display('setParameter');
//         } else {
//             $this->error('错误!');
//             exit();
//         }
    }
    
    public function setPrice(){
    	$price = M('price');
    	$rs = $price ->order('id desc')->select();
    	$this->assign("rs", $rs);
    	$this->display('setPrice');
    	unset($price, $rs);
    }
    
    public function setPriceSave(){
    	$p = $_POST['price'];
    	
    	$price = M('price');
    	$data = array();
    	$data['price'] = $p;
    	$data['priceDate'] = date("Y-m-d H:i:s");
    	$result = $price->add($data);
    	if($result !==false ){
    		$this->success("修改成功");
    	}else{
    		$this->error("修改失败");
    	}
    	unset($price);
    }

    public function setParameterSave()
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssPingGuoCP') {
            $fee = M('fee');
            $fck = M('fck');
            $rs = $fee->find();
            
            $i1 = $_POST['i1'];
            $i2 = $_POST['i2'];
            $i3 = $_POST['i3'];
            $i4 = $_POST['i4'];
            $a_money = $_POST['a_money'];
            $b_money = $_POST['b_money'];
            $where = array();
            $where['id'] = 1;
            $data = array();
            if (empty($a_money) == false || strlen($a_money) > 0) {
                $data['a_money'] = trim($a_money);
            }
            if (empty($b_money) == false || strlen($b_money) > 0) {
                $data['b_money'] = trim($b_money);
            }
            
            for ($j = 1; $j <= 10; $j ++) {
                $arr_rs[$j] = $_POST['i' . $j];
            }
            
            $s_sql2 = "";
            for ($j = 1; $j <= 10; $j ++) {
                if ($arr_rs[$j] != '') {
                    if (empty($s_sql2)) {
                        $s_sql2 = 'i' . $j . "='{$arr_rs[$j]}'";
                    } else {
                        $s_sql2 .= ',i' . $j . "='{$arr_rs[$j]}'";
                    }
                }
            }
            
            for ($i = 1; $i <= 35; $i ++) {
                $arr_s[$i] = $_POST['s' . $i];
            }
            
            $s_sql = "";
            for ($i = 1; $i <= 35; $i ++) {
                if (empty($arr_s[$i]) == false || strlen($arr_s[$i]) > 0) {
                    if (empty($s_sql2)) {
                        $s_sql = 's' . $i . "='{$arr_s[$i]}'";
                    } else {
                        $s_sql .= ',s' . $i . "='{$arr_s[$i]}'";
                    }
                }
            }
            
            for ($i = 1; $i <= 40; $i ++) {
                $arr_sts[$i] = $_POST['str' . $i];
            }
            $str_sql = "";
            for ($i = 1; $i <= 40; $i ++) {
                if (strlen(trim($arr_sts[$i])) > 0) {
                    if (empty($s_sql2) && empty($s_sql)) {
                        $str_sql = 'str' . $i . "='{$arr_sts[$i]}'";
                    } else {
                        $str_sql .= ',str' . $i . "='{$arr_sts[$i]}'";
                    }
                }
            }
            
            $str99 = trim($_POST['str99']);
            $ttst_sql = ',str99="' . $str99 . '"';
            
            $fee->execute("update __TABLE__ SET " . $s_sql2 . $s_sql . $str_sql . $ttst_sql . "  where `id`=1");
            $fee->where($where)
                ->data($data)
                ->save();
            $this->success('参数设置！');
            exit();
//         } else {
//             $this->error('错误!'); // 12345678901112131417181920s3
//             exit();
//         }
    }
    
    // 参数设置
    public function setParameter_B()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssPingGuoCPB') {
            $fee = M('fee');
            $fee_rs = $fee->find();
            
            $fee_str21 = $fee_rs['str21'];
            $fee_str22 = $fee_rs['str22'];
            $fee_str23 = $fee_rs['str23'];
            
            $this->assign('fee_str21', $fee_str21);
            $this->assign('fee_str22', $fee_str22);
            $this->assign('fee_str23', $fee_str23);
            
            $this->display();
        } else {
            $this->error('错误!');
            exit();
        }
    }

    public function setParameterSave_B()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssPingGuoCPB') {
            $fee = M('fee');
            $fck = M('fck');
            $rs = $fee->find();
            
            $where = array();
            $where['id'] = (int) $_POST['id'];
            for ($i = 1; $i <= 40; $i ++) {
                $arr_sts[$i] = $_POST['str' . $i];
            }
            $str_sql = "";
            for ($i = 1; $i <= 40; $i ++) {
                if (strlen(trim($arr_sts[$i])) > 0) {
                    if (empty($str_sql)) {
                        $str_sql = 'str' . $i . "='{$arr_sts[$i]}'";
                    } else {
                        $str_sql .= ',str' . $i . "='{$arr_sts[$i]}'";
                    }
                }
            }
            
            $fee->execute("update __TABLE__ SET " . $str_sql . "  where `id`=1");
            $this->success('首页图片设置！');
            exit();
        } else {
            $this->error('错误!');
            exit();
        }
    }

    public function MenberBonus()
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssPingGuoCP') {
            $fck = M('fck');
            $UserID = trim($_REQUEST['UserID']);
            $ss_type = (int) $_REQUEST['type'];
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['nickname'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            $map['is_pay'] = 1;
            // 查询字段
            $field = 'id,user_id,nickname,bank_name,bank_card,user_name,user_address,user_tel,rdt,f4,cpzj,pdt,u_level,zjj,agent_use,is_lock,f3,b3';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&type=' . $ss_type; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('pdt desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            foreach ($list as $vo) {
                $voo[$vo['id']] = $HYJJ[$vo['u_level']];
            }
            $this->assign('voo', $voo); // 会员级别
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $title = '会员管理';
            $this->assign('title', $title);
            $this->display('MenberBonus');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function MenberBonusSave()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssPingGuoCP') {
            $fck = M('fck');
            $fee_rs = M('fee')->find();
            $fee_s7 = explode('|', $fee_rs['s7']);
            
            $date = strtotime($_POST['date']);
            $lz = (int) $_POST['lz'];
            $lzbz = $_POST['lzbz'];
            
            $userautoid = (int) $_POST['userautoid'];
            
            if ($lz <= 0) {
                $this->error('请录入正确的劳资金额!');
                exit();
            }
            
            $rs = $fck->field('user_id,id')->find($userautoid);
            if ($rs) {
                $fck->query("update __TABLE__ set b3=b3+$lz where id=" . $userautoid);
                $this->input_bonus_2($rs['user_id'], $rs['id'], $fee_s7[2], $lz, $lzbz, $date); // 写进明细
                
                $bUrl = __URL__ . '/MenberBonus';
                $this->_box(1, '劳资录入！', $bUrl, 1);
            } else {
                $this->error('数据错误!');
                exit();
            }
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function delTable()
    {
        // 清空数据库===========================
        $this->display();
    }

    public function delTableExe()
    {
        $fck = M('fck');
        if (! $fck->autoCheckToken($_POST)) {
            $this->error('页面过期，请刷新页面！');
            exit();
        }
        unset($fck);
        $this->_delTable();
        exit();
    }

    public function adminClearing()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssBaiGuoJS') {
            $times = M('times');
            $trs = $times->where('type=0')
                ->order('id desc')
                ->find();
            if (! $trs) {
                $trs['benqi'] = strtotime('2010-01-01');
            }
            if ($trs['benqi'] == strtotime(date("Y-m-d"))) {
                $isPay = 1;
            } else {
                $isPay = 0;
            }
            $this->assign('is_pay', $isPay);
            $this->assign('trs', $trs);
            
            $fee = M('fee');
            $fee_rs = $fee->field('a_money,b_money')->find();
            $a_money = $fee_rs['a_money'];
            $this->assign('a_money', $a_money);
            $b_money = $fee_rs['b_money'];
            $this->assign('b_money', $b_money);
            
            $this->display();
        } else {
            $this->error('错误!');
        }
    }

    public function adminClearingSave()
    { // 资金结算
        if ($_SESSION['UrlPTPass'] == 'MyssBaiGuoJS') {
            set_time_limit(0); // 是页面不过期
            $times = M('times');
            $fck = D('Fck');
            $ydate = mktime();
            
            $a1 = $_GET['a1'];
            // if(empty($a1)){
            // $this->error("请输入分红比例");
            // exit;
            // }
            
            // 结算分红
            $fck->mr_fenhong(1);
            
            // $gp = M('gp');
            // $Guzhi = A('Guzhi');
            // $gp->query("UPDATE __TABLE__ set opening=".$a1." where id=1");
            // $Guzhi->stock_past_due();
            
            sleep(1);
            $this->success('日利息结算完成！');
            // $bUrl = __URL__.'/adminClearing';
            // $this->_box(1,'结算分红完成！',$bUrl,1);
            exit();
        } else {
            $this->error('错误!');
        }
    }

    public function adminsingle($GPid = 0)
    {
        // ============================================审核会员加单
        if ($_SESSION['UrlPTPass'] == 'MyssGuansingle') {
            $jiadan = M('jiadan');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                $map['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
            }
            
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $jiadan->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $jiadan->where($map)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $this->display('adminsingle');
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminsingleAC()
    {
        // 处理提交按钮
        $fck = M('fck');
        $action = $_POST['action'];
        // 获取复选框的值
        $PTid = $_POST['tabledb'];
        if (! $fck->autoCheckToken($_POST)) {
            $this->error('页面过期，请刷新页面！');
            exit();
        }
        if (! isset($PTid) || empty($PTid)) {
            $bUrl = __URL__ . '/adminsingle';
            $this->_box(0, '请选择！', $bUrl, 1);
            exit();
        }
        unset($fck);
        switch ($action) {
            case '确认':
                $this->_adminsingleConfirm($PTid);
                break;
            case '删除':
                $this->_adminsingleDel($PTid);
                break;
            default:
                $bUrl = __URL__ . '/adminsingle';
                $this->_box(0, '没有该注册！', $bUrl, 1);
                break;
        }
    }

    private function _adminsingleConfirm($PTid = 0)
    {
        // ===============================================确认加单
        if ($_SESSION['UrlPTPass'] == 'MyssGuansingle') {
            $fck = D('Fck');
            $jiadan = M('jiadan');
            $fee = M('fee');
            $fee_rs = $fee->find(1);
            $where = array();
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = 0;
            $field = '*';
            $vo = $jiadan->where($where)
                ->field($field)
                ->select();
            $fck_where = array();
            $nowdate = strtotime(date('c'));
            foreach ($vo as $voo) {
                $fck->xiangJiao($voo['uid'], $voo['danshu']); // 统计单数
                $fck_where['id'] = $voo['uid'];
                $fck_rs = $fck->where($fck_where)
                    ->field('user_id,re_id,f5')
                    ->find();
                if ($fck_rs) {
                    // 给推荐人添加推荐人数
                    $fck->query("update `xt_fck` set `re_nums`=re_nums+" . $voo['danshu'] . " where `id`=" . $fck_rs['re_id']);
                    $fck->upLevel($fck_rs['re_id']); // 晋级
                }
                $fck->userLevel($voo['uid'], $voo['danshu']); // 自己晋级
                                                              
                // 加上单数到自身认购字段
                $money = 0;
                $money = $fee_rs['uf1'] * $voo['danshu']; // 金额
                $fck->xsjOne($fck_rs['re_id'], $fck_rs['user_id'], $money, $fck_rs['f5']); // 销售奖第一部分中的第二部分
                $fck->query("update `xt_fck` set `f4`=f4+" . $voo['danshu'] . ",`cpzj`=cpzj+" . $money . " where `id`=" . $voo['uid']);
                // 改变状态
                $jiadan->query("UPDATE `xt_jiadan` SET `pdt`=$nowdate,`is_pay`=1 where `id`=" . $voo['id']);
            }
            unset($jiadan, $where, $field, $vo, $fck, $fck_where);
            $bUrl = __URL__ . '/adminsingle';
            $this->_box(1, '确认！', $bUrl, 1);
        } else {
            $this->error('错误！');
            exit();
        }
    }

    private function _adminsingleDel($PTid = 0)
    {
        // ====================================删除加单
        if ($_SESSION['UrlPTPass'] == 'MyssGuansingle') {
            $jdan = M('jiadan');
            // $fck->query("UPDATE `xt_fck` SET `single_ispay`=0,`single_money`=0 where `ID` in (".$PTid.")");
            $jwhere['id'] = array(
                'in',
                $PTid
            );
            $jwhere['is_pay'] = 0;
            $jdan->where($jwhere)->delete();
            $bUrl = __URL__ . '/adminsingle';
            $this->_box(1, '删除！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误!');
        }
    }

    private function _delTableBonus()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssQingKong') {
            // 删除指定记录
            $model = M('fck');
            $model2 = M('bonus');
            $model3 = M('history');
            $model4 = M('bonushistory');
            $model5 = M('times');
            $model6 = M('cash');
            
            $sql = "`agent_cash`=0,`zjj`=0";
            $model->execute("UPDATE __TABLE__ SET " . $sql);
            $model6->execute("UPDATE __TABLE__ SET x1=0");
            
            $model2->where('id > 0')->delete();
            $model3->where('id > 0')->delete();
            $model4->where('id > 0')->delete();
            $model5->where('id > 0')->delete();
            
            $bUrl = __URL__ . '/delTable';
            $this->_box(1, '部分清空数据完成！', $bUrl, 1);
            exit();
        } else {
            $bUrl = __URL__ . '/delTable';
            $this->_box(0, '清空数据失败！', $bUrl, 1);
            exit();
        }
    }

    private function _delTable()
    {
//         if ($_SESSION['UrlPTPass'] == 'MyssQingKong') {
            // 删除指定记录
            $model = M('fck');
            $model2 = M('bonus');
            $model41 = M('bonus1');
            $model3 = M('history');
            $model4 = M('msg');
            $model5 = M('times');
            $model40 = M('times1');
            $model6 = M('tiqu');
            $model7 = M('zhuanj');
            $model8 = M('shop');
            $model9 = M('jiadan');
            $model10 = M('chongzhi');
            
            $model12 = M('orders');
            $model13 = M('huikui');
            // $model14 = M ('product');
            $model15 = M('gouwu');
            $model16 = M('xiaof');
            $model17 = M('promo');
            $model18 = M('fenhong');
            $model19 = M('peng');
            $model20 = M('ulevel');
            $model21 = M('address');
            $model22 = M('shouru');
            $model23 = M('remit');
            $model24 = M('cash');
            $model25 = M('xfhistory');
            $model26 = M('game');
            $model27 = M('gupiao');
            $model28 = M('hgupiao');
            $model29 = M('gp_sell');
            
            $model30 = M('gp');
            $model31 = M('blist');
            $model32 = M('cashhistory');
            $model33 = M('bonushistory');
            $model34 = M('cashpp');
            $model35 = M('netb');
            $model36 = M('jiadan');
            $model37 = M('jiadanb');
            $model38 = M('aorb');
            $model39 = M('relation');
            $model42 = M('price');
            
            $model->where("id > 1")->delete();
            $model2->where('id > 0')->delete();
            $model3->where('id > 0')->delete();
            $model4->where('id > 0')->delete();
            $model5->where('id > 0')->delete();
            $model6->where('id > 0')->delete();
            $model7->where('id > 0')->delete();
            $model8->where('id > 0')->delete();
            $model9->where('id > 0')->delete();
            $model10->where('id > 0')->delete();
            
            $model12->where('id > 0')->delete();
            $model13->where('id > 0')->delete();
            // $model14->where('id > 0')->delete();
            $model15->where('ID > 0')->delete();
            $model16->where('ID > 0')->delete();
            $model17->where('ID > 0')->delete();
            $model18->where('id > 0')->delete();
            $model19->where('id > 0')->delete();
            $model20->where('id > 0')->delete();
            $model21->where('id > 1')->delete();
            $model22->where('id > 0')->delete();
            $model23->where('id > 0')->delete();
            $model24->where('id > 0')->delete();
            $model25->where('id > 0')->delete();
            $model26->where('id > 0')->delete();
            $model27->where('id > 0')->delete();
            $model28->where('id > 0')->delete();
            $model29->where('id > 0')->delete();
            $model31->where('id > 0')->delete();
            $model32->where('id > 0')->delete();
            $model33->where('id > 0')->delete();
            $model34->where('id > 0')->delete();
            $model35->where('id > 0')->delete();
            $model36->where('id > 0')->delete();
            $model37->where('id > 0')->delete();
            $model38->where('id > 0')->delete();
            $model39->where('id > 0')->delete();
            $model40->where('id > 0')->delete();
            $model41->where('id > 0')->delete();
            $model42->where('id > 0')->delete();
            
            $nowdate = time();
            // 数据清0
            
            $nowday = strtotime(date('Y-m-d'));
            // $nowday=strtotime(date('Y-m-d H:i:s')); //测试 使用
            $have_gp = 100000;
            $fh_gp = 10000;
            $fx_numb = $fh_gp / 10;
            $open_pri = 1;
            
            $model30->execute("UPDATE __TABLE__ SET opening=" . $open_pri . ",buy_num=0,sell_num=0,turnover=0,yt_sellnum=0,gp_quantity=0");
            
            $sql .= "`l`=0,`r`=0,`shangqi_l`=0,`shangqi_r`=0,`idt`=0,";
            $sql .= "`benqi_l`=0,`benqi_r`=0,`lr`=0,`shangqi_lr`=0,`benqi_lr`=0,";
            $sql .= "`agent_max`=0,`lssq`=0,`agent_use`=0,`f4`=0,`agent_active`=0,`is_agent`=2,`agent_cash`=100000000,`is_day_active`=0";
            $sql .= "`u_level`=1,`zjj`=0,`wlf`=0,`zsq`=0,`re_money`=0,";
            $sql .= "`cz_epoint`=0,b0=0,b1=0,b2=0,b3=0,b4=0,";
            $sql .= "`b5`=0,b6=0,b7=0,b8=0,b9=0,b10=0,b11=0,b12=0,re_nums=0,man_ceng=0,";
            $sql .= "re_peat_money=0,cpzj=0,duipeng=0,_times=0,fanli=0,fanli_time=$nowday,fanli_num=0,day_feng=0,get_date=$nowday,get_numb=0,";
            $sql .= "get_level=0,is_xf=0,xf_money=0,is_zy=0,zyi_date=0,zyq_date=0,down_num=0,agent_xf=0,agent_kt=0,agent_gp=0,gp_num=0,xy_money=0,";
            $sql .= "peng_num=0,re_f4=0,agent_cf=0,is_aa=0,is_bb=0,is_cc=0,is_fh=0,ach=0,tz_nums=0,shangqi_use=0,shangqi_tz=0,gdt=0,re_pathb=0,kt_id=0,pg_nums=0,fh_nums=0,is_cha=0,tx_num=0,xx_money=0,x_pai=1,is_pp=0,is_p=0,x_out=1,x_num=0,agent_sfw=0,agent_sf=1000,agent_sfo=2000,fanli_money=0,wlf_money=0,";
            $sql .= "re_nums_b=0,vip4=0,vip5=0,vip6=0,zdt=0,shang_l=0,shang_r=0,shang_nums=0,shang_ach=0,z_date=0,c_date=0,jia_nums=0,re_nums_l=0,re_nums_r=0,";
            $sql .= "buy_gupiao=0,ls=0,rs=0,l_nums=0,r_nums=0,email=456,p_nums=0,sh_level=0,agent_zc=0,in_gupiao=0,out_gupiao=0,flat_gupiao=0,give_gupiao=0";
            
            $model->execute("UPDATE __TABLE__ SET " . $sql);
            
            for ($i = 1; $i <= 2; $i ++) { // fck1 ~ fck5 表 (清空只留800000)
                $fck_other = M('fck' . $i);
                $fck_other->where('id > 1')->delete();
            }
            $nowweek = date("w");
            if ($nowweek == 0) {
                $nowweek = 7;
            }
            $kou_w = $nowweek - 1;
            $weekday = $nowday - $kou_w * 24 * 3600;
            
            // fee表,记载清空操作的时间(时间截)
            $fee = M('fee');
            $fee_rs = $fee->field('id')->find();
            $where = array();
            $data = array();
            $data['id'] = $fee_rs['id'];
            $data['create_time'] = time();
            $data['f_time'] = $weekday;
            $data['us_num'] = 1;
            // 提现手续费清零
            $data['s16'] = 0;
            // $data['a_money'] = 0;
            // $data['b_money'] = 0;
            $data['ff_num'] = 1;
            $data['gp_one'] = $open_pri;
            $data['gp_fxnum'] = $fx_numb;
            $data['gp_senum'] = 0;
            $data['gp_cnum'] = 0;
            $rs = $fee->save($data);
            
            $card = M('card');
            $card->query("update __TABLE__ set is_sell=0,bid=0,buser_id='',b_time=0");
            
            $bUrl = __URL__ . '/delTable';
            $this->_box(1, '清空完毕！', $bUrl, 1);
            exit();
//         } else {
//             $bUrl = __URL__ . '/delTable';
//             $this->_box(0, '清空数据失败！', $bUrl, 1);
//             exit();
//         }
    }

    public function menber()
    {
        
        // 列表过滤器，生成查询Map对象
        $fck = M('fck');
        $map = array();
        $id = $PT_id;
        $map['re_id'] = (int) $_GET['PT_id'];
        // $map['is_pay'] = 0;
        $UserID = $_POST['UserID'];
        if (! empty($UserID)) {
            $map['user_id'] = array(
                'like',
                "%" . $UserID . "%"
            );
        }
        
        // 查询字段
        $field = 'id,user_id,nickname,bank_name,bank_card,user_name,user_address,user_tel,rdt,f4,cpzj,is_pay';
        // =====================分页开始==============================================
        import("@.ORG.ZQPage"); // 导入分页类
        $count = $fck->where($map)->count(); // 总页数
        $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
        $page_where = 'UserID=' . $UserID; // 分页条件
        $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
        // ===============(总页数,每页显示记录数,css样式 0-9)
        $show = $Page->show(); // 分页变量
        $this->assign('page', $show); // 分页变量输出到模板
        $list = $fck->where($map)
            ->field($field)
            ->order('rdt desc')
            ->page($Page->getPage() . ',' . $listrows)
            ->select();
        $this->assign('list', $list); // 数据输出到模板
                                     // =================================================
        
        $where = array();
        $where['id'] = $id;
        $fck_rs = $fck->where($where)
            ->field('agent_cash')
            ->find();
        $this->assign('frs', $fck_rs); // 注册币
        $this->display('menber');
        exit();
    }

    public function adminmoneyflows()
    {
        // 货币流向
//         if ($_SESSION['UrlPTPass'] == 'MyssMoneyFlows') {
            $fck = M('fck');
            $history = M('history');
            $sDate = $_REQUEST['S_Date'];
            $eDate = $_REQUEST['E_Date'];
            $UserID = $_REQUEST['UserID'];
            
            $ss_type = (int) $_REQUEST['tp'];
            $map['_string'] = "1=1";
            $s_Date = 0;
            $e_Date = 0;
            if (! empty($sDate)) {
                $s_Date = strtotime($sDate);
            } else {
                $sDate = "2000-01-01";
            }
            if (! empty($eDate)) {
                $e_Date = strtotime($eDate);
            } else {
                $eDate = date("Y-m-d");
            }
            if ($s_Date > $e_Date && $e_Date > 0) {
                $temp_d = $s_Date;
                $s_Date = $e_Date;
                $e_Date = $temp_d;
            }
            if ($s_Date > 0) {
                $map['_string'] .= " and pdt>=" . $s_Date;
            }
            if ($e_Date > 0) {
                $e_Date = $e_Date + 3600 * 24 - 1;
                $map['_string'] .= " and pdt<=" . $e_Date;
            }
            if ($ss_type > 0) {
                if ($ss_type == 15) {
                    $map['action_type'] = array('lt',7);
                } else if ($ss_type > 15) {
                    $map['action_type'] = $ss_type;
                } else {
                    $map['action_type'] = array('eq',$ss_type);
                }
            }
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                
                unset($KuoZhan);
                $where = array();
                $where['user_id'] = array('eq',$UserID);
                $usrs = $fck->where($where)
                    ->field('id,user_id')
                    ->find();
                if ($usrs) {
                    $usid = $usrs['id'];
                    $usuid = $usrs['user_id'];
                    $map['_string'] .= " and (uid=" . $usid . " or user_id='" . $usuid . "')";
                } else {
                    $map['_string'] .= " and id=0";
                }
                unset($where, $usrs);
                $UserID = urlencode($UserID);
            }
            $this->assign('S_Date', $sDate);
            $this->assign('E_Date', $eDate);
            $this->assign('ry', $ss_type);
            $this->assign('UserID', $UserID);
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $history->where($map)->count(); // 总页数
            $listrows = 20; // 每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&S_Date=' . $sDate . '&E_Date=' . $eDate . '&tp=' . $ss_type; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $history->where($map)
                ->field($field)
                ->order('pdt desc,id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
                                         // dump($history);
            
            $fee = M('fee'); // 参数表
            $fee_rs = $fee->field('s18')->find();
            $fee_s7 = explode('|', $fee_rs['s18']);
            $this->assign('fee_s7', $fee_s7); // 输出奖项名称数组
            
            $this->display();
//         } else {
//             $this->error('数据错误!');
//             exit();
//         }
    }
    
    // 会员升级
    public function adminUserUp($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGuaUp') {
            $ulevel = M('ulevel');
            $UserID = $_POST['UserID'];
            if (! empty($UserID)) {
                $map['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
            }
            
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $ulevel->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $page_where = 'UserID=' . $UserID; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $ulevel->where($map)
                ->field($field)
                ->order('id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $title = '会员升级管理';
            $this->display('adminuserUp');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function adminUserUpAC($GPid = 0)
    {
        // 列表过滤器，生成查询Map对象
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGuaUp') {
            // 处理提交按钮
            $action = $_POST['action'];
            // 获取复选框的值
            $PTid = $_POST['tabledb'];
            if (! isset($PTid) || empty($PTid)) {
                $bUrl = __URL__ . '/adminUserUp';
                $this->_box(0, '请选择会员！', $bUrl, 1);
                exit();
            }
            switch ($action) {
                case '确认升级':
                    $this->_adminUserUpOK($PTid);
                    break;
                case '删除':
                    $this->_adminUserUpDel($PTid);
                    break;
                default:
                    $bUrl = __URL__ . '/adminUserUp';
                    $this->_box(0, '没有该会员！', $bUrl, 1);
                    break;
            }
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    private function _adminUserUpOK($PTid = 0)
    {
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGuaUp') {
            $fck = D('Fck');
            $ulevel = M('ulevel');
            $where = array();
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = 0;
            $field = '*';
            $vo = $ulevel->where($where)
                ->field($field)
                ->select();
            $fck_where = array();
            $nowdate = strtotime(date('c'));
            foreach ($vo as $voo) {
                $ulevel->query("UPDATE `xt_ulevel` SET `pdt`=$nowdate,`is_pay`=1 where `id`=" . $voo['id']);
                $money = 0;
                $money = $voo['money']; // 金额
                $fck->query("update `xt_fck` set `cpzj`=cpzj+" . $money . ",u_level=" . $voo['up_level'] . "  where `id`=" . $voo['uid']);
            }
            unset($fck, $where, $field, $vo);
            $bUrl = __URL__ . '/adminUserUp';
            $this->_box(1, '升级会员成功！', $bUrl, 1);
            exit();
        } else {
            $this->error('错误！');
            exit();
        }
    }

    private function _adminUserUpDel($PTid = 0)
    {
        // 删除会员
        if ($_SESSION['UrlPTPass'] == 'MyssGuanXiGuaUp') {
            $fck = M('fck');
            $ispay = M('ispay');
            $ulevle = M('ulevel');
            $where['id'] = array(
                'in',
                $PTid
            );
            $where['is_pay'] = array(
                'eq',
                0
            );
            $rss1 = $ulevle->where($where)->delete();
            
            if ($rss1) {
                $bUrl = __URL__ . '/adminUserUp';
                $this->_box(1, '删除升级申请成功！', $bUrl, 1);
                exit();
            } else {
                $bUrl = __URL__ . '/adminUserUp';
                $this->_box(0, '删除升级申请失败！', $bUrl, 1);
                exit();
            }
        } else {
            $this->error('错误!');
        }
    }

    public function adminMenberJL()
    {
        if ($_SESSION['UrlPTPass'] == 'MyssadminMenberJL') {
            $fck = M('fck');
            $UserID = $_REQUEST['UserID'];
            $ss_type = (int) $_REQUEST['type'];
            
            $map = array();
            if (! empty($UserID)) {
                import("@.ORG.KuoZhan"); // 导入扩展类
                $KuoZhan = new KuoZhan();
                if ($KuoZhan->is_utf8($UserID) == false) {
                    $UserID = iconv('GB2312', 'UTF-8', $UserID);
                }
                unset($KuoZhan);
                $where['user_name'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['user_id'] = array(
                    'like',
                    "%" . $UserID . "%"
                );
                $where['_logic'] = 'or';
                $map['_complex'] = $where;
                $UserID = urlencode($UserID);
            }
            $uulv = (int) $_REQUEST['ulevel'];
            if (! empty($uulv)) {
                $map['u_level'] = array(
                    'eq',
                    $uulv
                );
            }
            $map['is_pay'] = array(
                'egt',
                1
            );
            $map['u_level'] = array(
                'egt',
                4
            );
            // 查询字段
            $field = '*';
            // =====================分页开始==============================================
            import("@.ORG.ZQPage"); // 导入分页类
            $count = $fck->where($map)->count(); // 总页数
            $listrows = C('ONE_PAGE_RE'); // 每页显示的记录数
            $listrows = 20; // 每页显示的记录数
            $page_where = 'UserID=' . $UserID . '&ulevel=' . $uulv; // 分页条件
            $Page = new ZQPage($count, $listrows, 1, 0, 3, $page_where);
            // ===============(总页数,每页显示记录数,css样式 0-9)
            $show = $Page->show(); // 分页变量
            $this->assign('page', $show); // 分页变量输出到模板
            $list = $fck->where($map)
                ->field($field)
                ->order('pdt desc,id desc')
                ->page($Page->getPage() . ',' . $listrows)
                ->select();
            
            $HYJJ = '';
            $this->_levelConfirm($HYJJ, 1);
            $this->assign('voo', $HYJJ); // 会员级别
            $level = array();
            for ($i = 0; $i < count($HYJJ); $i ++) {
                $level[$i] = $HYJJ[$i + 1];
            }
            $this->assign('level', $level);
            $this->assign('list', $list); // 数据输出到模板
                                         // =================================================
            
            $title = '会员管理';
            $this->assign('title', $title);
            $this->display('adminMenberJL');
            return;
        } else {
            $this->error('数据错误!');
            exit();
        }
    }

    public function upload_fengcai_aa()
    {
        if (! empty($_FILES)) {
            // 如果有文件上传 上传附件
            $this->_upload_fengcai_aa();
        }
    }

    protected function _upload_fengcai_aa()
    {
        header("content-type:text/html;charset=utf-8");
        // 文件上传处理函数
        
        // 载入文件上传类
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        
        // 设置上传文件大小
        $upload->maxSize = 1048576 * 20; // TODO 50M 3M 3292200 1M 1048576
                                           
        // 设置上传文件类型
        $upload->allowExts = explode(',', 'flv');
        
        // 设置附件上传目录
        $upload->savePath = './Public/Uploads/media/';
        
        // 设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = false;
        
        // 设置需要生成缩略图的文件前缀
        $upload->thumbPrefix = 'm_'; // 生产2张缩略图
                                        
        // 设置缩略图最大宽度
        $upload->thumbMaxWidth = '800';
        
        // 设置缩略图最大高度
        $upload->thumbMaxHeight = '600';
        
        // 设置上传文件规则
        $upload->saveRule = date("Y") . date("m") . date("d") . date("H") . date("i") . date("s") . rand(1, 100);
        
        // 删除原图
        $upload->thumbRemoveOrigin = true;
        
        if (! $upload->upload()) {
            // 捕获上传异常
            $error_p = $upload->getErrorMsg();
            echo "<script>alert('" . $error_p . "');history.back();</script>";
        } else {
            // 取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            $U_path = $uploadList[0]['savepath'];
            $U_nname = $uploadList[0]['savename'];
            $U_inpath = (str_replace('./Public/', '__PUBLIC__/', $U_path)) . $U_nname;
            
            echo "<script>window.parent.myform.str21.value='" . $U_inpath . "';</script>";
            echo "<span style='font-size:12px;'>上传完成！</span>";
            exit();
        }
    }

    public function upload_fengcai_bb()
    {
        if (! empty($_FILES)) {
            // 如果有文件上传 上传附件
            $this->_upload_fengcai_bb();
        }
    }

    protected function _upload_fengcai_bb()
    {
        header("content-type:text/html;charset=utf-8");
        // 文件上传处理函数
        
        // 载入文件上传类
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        
        // 设置上传文件大小
        $upload->maxSize = 1048576 * 2; // TODO 50M 3M 3292200 1M 1048576
                                          
        // 设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        
        // 设置附件上传目录
        $upload->savePath = './Public/Uploads/';
        
        // 设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = false;
        
        // 设置需要生成缩略图的文件前缀
        $upload->thumbPrefix = 'm_'; // 生产2张缩略图
                                        
        // 设置缩略图最大宽度
        $upload->thumbMaxWidth = '800';
        
        // 设置缩略图最大高度
        $upload->thumbMaxHeight = '600';
        
        // 设置上传文件规则
        $upload->saveRule = date("Y") . date("m") . date("d") . date("H") . date("i") . date("s") . rand(1, 100);
        
        // 删除原图
        $upload->thumbRemoveOrigin = true;
        
        if (! $upload->upload()) {
            // 捕获上传异常
            $error_p = $upload->getErrorMsg();
            echo "<script>alert('" . $error_p . "');history.back();</script>";
        } else {
            // 取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            $U_path = $uploadList[0]['savepath'];
            $U_nname = $uploadList[0]['savename'];
            $U_inpath = (str_replace('./Public/', '__PUBLIC__/', $U_path)) . $U_nname;
            
            echo "<script>window.parent.myform.str22.value='" . $U_inpath . "';</script>";
            echo "<span style='font-size:12px;'>上传完成！</span>";
            exit();
        }
    }

    public function upload_fengcai_cc()
    {
        if (! empty($_FILES)) {
            // 如果有文件上传 上传附件
            $this->_upload_fengcai_cc();
        }
    }

    protected function _upload_fengcai_cc()
    {
        header("content-type:text/html;charset=utf-8");
        // 文件上传处理函数
        
        // 载入文件上传类
        import("@.ORG.UploadFile");
        $upload = new UploadFile();
        
        // 设置上传文件大小
        $upload->maxSize = 1048576 * 2; // TODO 50M 3M 3292200 1M 1048576
                                          
        // 设置上传文件类型
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        
        // 设置附件上传目录
        $upload->savePath = './Public/Uploads/';
        
        // 设置需要生成缩略图，仅对图像文件有效
        $upload->thumb = false;
        
        // 设置需要生成缩略图的文件前缀
        $upload->thumbPrefix = 'm_'; // 生产2张缩略图
                                        
        // 设置缩略图最大宽度
        $upload->thumbMaxWidth = '800';
        
        // 设置缩略图最大高度
        $upload->thumbMaxHeight = '600';
        
        // 设置上传文件规则
        $upload->saveRule = date("Y") . date("m") . date("d") . date("H") . date("i") . date("s") . rand(1, 100);
        
        // 删除原图
        $upload->thumbRemoveOrigin = true;
        
        if (! $upload->upload()) {
            // 捕获上传异常
            $error_p = $upload->getErrorMsg();
            echo "<script>alert('" . $error_p . "');history.back();</script>";
        } else {
            // 取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
            $U_path = $uploadList[0]['savepath'];
            $U_nname = $uploadList[0]['savename'];
            $U_inpath = (str_replace('./Public/', '__PUBLIC__/', $U_path)) . $U_nname;
            
            echo "<script>window.parent.myform.str23.value='" . $U_inpath . "';</script>";
            echo "<span style='font-size:12px;'>上传完成！</span>";
            exit();
        }
    }
}
?>