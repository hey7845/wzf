<?php
// 本类由系统自动生成，仅供测试用途
class CallbackAction extends Action {
	
	function getMerInfo($mercode)
   	{
      $arrayMer=array();
      switch ($mercode)
     {
        case "175462":
           $arrayMer=array (
                             'mername'=>'深圳市瑞韵利宝科技有限公司',
                             'mercert'=>'QuHAlWymZ29HGrnRDs2HpO8hQ951vuEByewCWmVubhl79x6fcvtTmJMTHLrL46tLhuGEjvlnDluDzTQSUSjcQ6z0txkhMPYn4FCeKWoBqLtXGhsFROBBaj2NaSgyN8bU',
                             'acccode'=>'1754620019'
                           );
            break;
     }
     return $arrayMer;
   	}
	
	//接收_智付
    public function receive()
    {
    	$fee = M('fee');
    	$fee_rs = $fee->field('str17,str18')->find();
    	$fee_rs17 = $fee_rs['str17'];//商户号
    	$fee_rs18 = $fee_rs['str18'];//密钥
    	unset($fee,$fee_rs);
    	
		$merchant_code	= $_POST["merchant_code"];

		//通知类型
		$notify_type = $_POST["notify_type"];
	
		//通知校验ID
		$notify_id = $_POST["notify_id"];
	
		//接口版本
		$interface_version = $_POST["interface_version"];
	
		//签名方式
		$sign_type = $_POST["sign_type"];
	
		//签名
		$dinpaySign = $_POST["sign"];
	
		//商家订单号
		$order_no = $_POST["order_no"];
	
		//商家订单时间
		$order_time = $_POST["order_time"];
	
		//商家订单金额
		$order_amount = $_POST["order_amount"];
	
		//回传参数
		$extra_return_param = $_POST["extra_return_param"];
	
		//智付交易定单号
		$trade_no = $_POST["trade_no"];
	
		//智付交易时间
		$trade_time = $_POST["trade_time"];
	
		//交易状态 SUCCESS 成功  FAILED 失败
		$trade_status = $_POST["trade_status"];
	
		//银行交易流水号
		$bank_seq_no = $_POST["bank_seq_no"];
	
		
		//组织订单信息
		$signStr = "";
		if($bank_seq_no != "") {
			$signStr = $signStr."bank_seq_no=".$bank_seq_no."&";
		}
		if($extra_return_param != "") {
			$signStr = $signStr."extra_return_param=".$extra_return_param."&";
		}
		$signStr = $signStr."interface_version=V3.0&";
		$signStr = $signStr."merchant_code=".$merchant_code."&";
		if($notify_id != "") {
			$signStr = $signStr."notify_id=".$notify_id."&notify_type=".$notify_type."&";
		}
		
		$signStr = $signStr."order_amount=".$order_amount."&";
		$signStr = $signStr."order_no=".$order_no."&";
		$signStr = $signStr."order_time=".$order_time."&";
		$signStr = $signStr."trade_no=".$trade_no."&";
		$signStr = $signStr."trade_status=".$trade_status."&";
		
		if($trade_time != "") {
			$signStr = $signStr."trade_time=".$trade_time."&";
		}
		$key = $fee_rs18;
		
		$signStr = $signStr."key=".$key;
		$signInfo = $signStr;
		//将组装好的信息MD5签名
		$sign = md5($signInfo);
		//echo "sign=".$sign."<br>";
		
		//比较智付返回的签名串与商家这边组装的签名串是否一致
		if($dinpaySign==$sign) {
			//验签成功
			
//			$order_no = "10020131203180056992";
			
			$remit = M('remit');
			$chongzhi = M('chongzhi');
			$history = M('history');
			$fck = M('fck');
			$where = array();
			$where['orderid'] = array('eq',$order_no);
			$where['is_pay'] = array('eq',0);
			$ors = $remit->where($where)->find();
			if($ors){
				$tid = $ors['id'];
				$uid = $ors['uid'];
				$usid = $ors['user_id'];
				$money = $ors['amount'];

				$oresult = $remit->execute("update __TABLE__ set is_pay=1,ok_time=".mktime()." where is_pay=0 and id=".$tid);
				if($oresult){

					$data = array();
					$data['uid']			= $uid;
					$data['user_id']		= $usid;
					$data['action_type']	= 21;
					$data['pdt']			= mktime();
					$data['epoints']		= $money;
					$data['did']			= 0;
					$data['allp']			= 0;
					$data['bz']				= '21';
					$history->add($data);
					unset($data);
					
					$data = array();
					$data['uid']	= $uid;
					$data['user_id']= $usid;
					$data['rdt']	= time();
					$data['pdt']	= time();
					$data['epoint']	= $money;
					$data['is_pay']	= 1;
					$data['stype']	= 0;
					$chongzhi->add($data);
					unset($data);

					$fck->query("update __TABLE__ set agent_cash=agent_cash+".$money." where id=".$uid);
				}
			}else{
				$whe = array();
				$whe['orderid'] = array('eq',$order_no);
				$urs = $remit->where($whe)->field('id')->find();
				if(!$urs){
					$ctdir = "./ErrorLog";
					$ctname = "zi_pay_".date("Y").date("m").date("d");
					$daytime = date("Y-m-d H:i:s");
					$errdata = "时间：".$daytime."。订单号：".$order_no."支付成功，支付金额：".$order_amount."，但充值失败。原因：没有对应充值订单号记录。";

					$this->create_txt($ctdir,$ctname,$errdata);
				}
				unset($urs);
			}
			unset($remit,$fck,$history,$chongzhi,$where,$ors);

			$zf_ok = 1;
			$zf_re = "充值支付成功。";
			$zf_or = "订单号：".$order_no;
			$zf_am = "支付金额：".$order_amount;
		}else
		{
			$zf_re = "支付失败。";
		}
		$this->assign('zf_re',$zf_re);
		$this->assign('zf_or',$zf_or);
		$this->assign('zf_am',$zf_am);
		if($notify_type=="offline_notify"){
			echo "SUCCESS";
		}
		$this->display();
    }
    
	public function hx_receive(){
		//网关地址
		$payWayurl="http://newpay.ips.com.cn/psfp-entry/gateway/payment.html";
		$paytestWayurl="http://bankbackuat.ips.com.cn/psfp-entry/gateway/payment.html";
		//定义日志路径
		define('BASE_PATH',str_replace( '\\' , '/' , realpath(dirname(__FILE__).'/../'))."/log");
		define('PATH_LOG_FILE',BASE_PATH.'/newPayDemo-'.date('Y-m-j').'.log');
		//创建日志存储目录
		if (!is_dir(BASE_PATH)) mkdir(BASE_PATH);
		define('Mode', "1");//0#测试环境 1#生产环
		
		$paymentResult = $_POST["paymentResult"];//获取信息
		file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S接收到的报文信息:".$paymentResult."\r\n",FILE_APPEND);
		$xml=simplexml_load_string($paymentResult,'SimpleXMLElement', LIBXML_NOCDATA); 

  		//读取相关xml中信息
		$ReferenceIDs = $xml->xpath("GateWayRsp/head/ReferenceID");//关联号
		//var_dump($ReferenceIDs); 
		$ReferenceID = $ReferenceIDs[0];//关联号
		$RspCodes = $xml->xpath("GateWayRsp/head/RspCode");//响应编码
		$RspCode=$RspCodes[0];
		$RspMsgs = $xml->xpath("GateWayRsp/head/RspMsg"); //响应说明
		$RspMsg=$RspMsgs[0];
		$ReqDates = $xml->xpath("GateWayRsp/head/ReqDate"); // 接受时间
		$ReqDate=$ReqDates[0];
		$RspDates = $xml->xpath("GateWayRsp/head/RspDate");// 响应时间
		$RspDate=$RspDates[0];
		$Signatures = $xml->xpath("GateWayRsp/head/Signature"); //数字签名
		$Signature=$Signatures[0];
		$MerBillNos = $xml->xpath("GateWayRsp/body/MerBillNo"); // 商户订单号
		$MerBillNo=$MerBillNos[0];
		$CurrencyTypes = $xml->xpath("GateWayRsp/body/CurrencyType");//币种
		$CurrencyType=$CurrencyTypes[0];
		$Amounts = $xml->xpath("GateWayRsp/body/Amount"); //订单金额
		$Amount=$Amounts[0];
		$Dates = $xml->xpath("GateWayRsp/body/Date");    //订单日期
		$Date=$Dates[0];
		$Statuss = $xml->xpath("GateWayRsp/body/Status");  //交易状态
		$Status=$Statuss[0];
		$Msgs = $xml->xpath("GateWayRsp/body/Msg");    //发卡行返回信息
		$Msg=$Msgs[0];
		$Attachs = $xml->xpath("GateWayRsp/body/Attach");    //数据包
		$Attach=$Attachs[0];
		$IpsBillNos = $xml->xpath("GateWayRsp/body/IpsBillNo"); //IPS订单号
		$IpsBillNo=$IpsBillNos[0];
		$IpsTradeNos = $xml->xpath("GateWayRsp/body/IpsTradeNo"); //IPS交易流水号
		$IpsTradeNo=$IpsTradeNos[0];
		$RetEncodeTypes = $xml->xpath("GateWayRsp/body/RetEncodeType");    //交易返回方式
		$RetEncodeType=$RetEncodeTypes[0];
		$BankBillNos = $xml->xpath("GateWayRsp/body/BankBillNo"); //银行订单号
		$BankBillNo=$BankBillNos[0];
		$ResultTypes = $xml->xpath("GateWayRsp/body/ResultType"); //支付返回方式
		$ResultType=$ResultTypes[0];
		$IpsBillTimes = $xml->xpath("GateWayRsp/body/IpsBillTime"); //IPS处理时间
		$IpsBillTime=$IpsBillTimes[0];
	
		$resParam="关联号:"
			.$ReferenceID
			."响应编码:"
			.$RspCode
			."响应说明:"
			.$RspMsg
			."接受时间:"
			.$ReqDate
			."响应时间:"
			.$RspDate
			."数字签名:"
			.$Signature
			."商户订单号:"
			.$MerBillNo
			."币种:"
			.$CurrencyType
			."订单金额:"
			.$Amount
			."订单日期:"
			.$Date
			."交易状态:"
			.$Status
			."发卡行返回信息:"
			.$Msg
			."数据包:"
			.$Attach
			."IPS订单号:"
			.$IpsBillNo
			."交易返回方式:"
			.$RetEncodeType
			."银行订单号:"
			.$BankBillNo
			."支付返回方式:"
			.$ResultType
			."IPS处理时间:"
			.$IpsBillTime;
		file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'S2S新系统获取参数信息:'.$resParam."\r\n",FILE_APPEND);
		
		//验签明文
		//billno+【订单编号】+currencytype+【币种】+amount+【订单金额】+date+【订单日期】+succ+【成功标志】+ipsbillno+【IPS订单编号】+retencodetype +【交易返回签名方式】+【商户内部证书】
		
		$pmercode 			= '175462';
		$arrayMer=$this->getMerInfo($pmercode);
		 $sbReq = "<body>"
			. "<MerBillNo>" . $MerBillNo . "</MerBillNo>"
			. "<CurrencyType>" . $CurrencyType . "</CurrencyType>"
			. "<Amount>" . $Amount . "</Amount>"
			. "<Date>" . $Date . "</Date>"
			. "<Status>" . $Status . "</Status>"
			. "<Msg><![CDATA[" . $Msg . "]]></Msg>"
			. "<Attach><![CDATA[" . $Attach . "]]></Attach>"
			. "<IpsBillNo>" . $IpsBillNo . "</IpsBillNo>"
			. "<IpsTradeNo>" . $IpsTradeNo . "</IpsTradeNo>"
			. "<RetEncodeType>" . $RetEncodeType . "</RetEncodeType>"
			. "<BankBillNo>" . $BankBillNo . "</BankBillNo>"
			. "<ResultType>" . $ResultType . "</ResultType>"
			. "<IpsBillTime>" . $IpsBillTime . "</IpsBillTime>"
		 . "</body>";
		$sign=$sbReq.$pmercode.$arrayMer['mercert'];
		file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'S2S验签明文:'.$sign."\r\n",FILE_APPEND);
		$md5sign=  md5($sign);
		file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'S2S验签密文:'.$md5sign."\r\n",FILE_APPEND);
		
		//判断签名
		if($Signature==$md5sign){
			
			file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S验签成功.\r\n",FILE_APPEND);
			if($RspCode=='000000'){
								
				$remit = M('remit');
				$chongzhi = M('chongzhi');
				$history = M('history');
				$fck = M('fck');
				$where = array();
				$where['orderid'] = array('eq',$MerBillNo);
				$where['is_pay'] = array('eq',0);
				$ors = $remit->where($where)->find();
				if($ors){
					$tid = $ors['id'];
					$uid = $ors['uid'];
					$usid = $ors['user_id'];
					$money = $ors['amount'];
	
					$oresult = $remit->execute("update __TABLE__ set is_pay=1,ok_time=".mktime()." where is_pay=0 and id=".$tid);
					if($oresult){
						$data = array();
						$data['uid']			= $uid;
						$data['user_id']		= $usid;
						$data['action_type']	= 21;
						$data['pdt']			= mktime();
						$data['epoints']		= $money;
						$data['did']			= 0;
						$data['allp']			= 0;
						$data['bz']				= '21';
						$history->add($data);
						unset($data);
						
						$data = array();
						$data['uid']	= $uid;
						$data['user_id']= $usid;
						$data['rdt']	= time();
						$data['pdt']	= time();
						$data['epoint']	= $money;
						$data['is_pay']	= 1;
						$data['stype']	= 0;
						$chongzhi->add($data);
						unset($data);
	
						$fck->query("update __TABLE__ set agent_kt=agent_kt+".$money." where id=".$uid);
					}
				}else{
					$whe = array();
					$whe['orderid'] = array('eq',$MerBillNo);
					$urs = $remit->where($whe)->field('id')->find();
					if(!$urs){
						$ctdir = "./ErrorLog";
						$ctname = "hx_pay_".date("Y").date("m").date("d");
						$daytime = date("Y-m-d H:i:s");
						$errdata = "时间：".$daytime."。订单号：".$MerBillNo."支付成功，支付金额：".$Amount."，但充值失败。原因：没有对应充值订单号记录。";
	
						$this->create_txt($ctdir,$ctname,$errdata);
					}
					unset($urs);
				}
				unset($remit,$fck,$history,$chongzhi,$where,$ors);
	
				$zf_ok = 1;
				$zf_re = "充值支付成功。";
				$zf_or = "订单号：".$MerBillNo;
				$zf_am = "支付金额：".$Amount;
				
				file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S订单支付成功.\r\n",FILE_APPEND);	
			}
			
		}else{
			 
		 	file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s')."S2S验签失败.\r\n",FILE_APPEND);
			echo "订单签名错误";
			$zf_re = "订单签名错误。";
		}
		
		$this->assign('zf_re',$zf_re);
		$this->assign('zf_or',$zf_or);
		$this->assign('zf_am',$zf_am);
		
		$this->display("receive");
	}
	
	//接收_环讯
    public function old_hx_receive()
    {
    	$fee = M('fee');
    	$fee_rs = $fee->field('str17,str18')->find();
    	$fee_rs17 = $fee_rs['str17'];//商户号
    	$fee_rs18 = $fee_rs['str18'];//密钥
    	unset($fee,$fee_rs);
    	
    	$billno = $_GET['billno'];
    	$order_no = $billno;//支付订单号
		$amount = $_GET['amount'];
		$order_amount = $amount;//支付金额
		$mydate = $_GET['date'];
		$succ = $_GET['succ'];
		$msg = $_GET['msg'];
		$attach = $_GET['attach'];
		$ipsbillno = $_GET['ipsbillno'];
		$retEncodeType = $_GET['retencodetype'];
		$currency_type = $_GET['Currency_type'];
		$signature = $_GET['signature'];
		
		$content = 'billno'.$billno.'currencytype'.$currency_type.'amount'.$amount.'date'.$mydate.'succ'.$succ.'ipsbillno'.$ipsbillno.'retencodetype'.$retEncodeType;
		
		$cert = $fee_rs18;//密钥
		$signature_1ocal = md5($content . $cert);
		
		//比较签名串是否一致
		if ($signature_1ocal == $signature){
			//验签成功
			if ($succ == 'Y'){//支付成功
			
//				$order_no = "10020131203180056992";
			
				$remit = M('remit');
				$chongzhi = M('chongzhi');
				$history = M('history');
				$fck = M('fck');
				$where = array();
				$where['orderid'] = array('eq',$order_no);
				$where['is_pay'] = array('eq',0);
				$ors = $remit->where($where)->find();
				if($ors){
					$tid = $ors['id'];
					$uid = $ors['uid'];
					$usid = $ors['user_id'];
					$money = $ors['amount'];
	
					$oresult = $remit->execute("update __TABLE__ set is_pay=1,ok_time=".mktime()." where is_pay=0 and id=".$tid);
					if($oresult){
	
						$data = array();
						$data['uid']			= $uid;
						$data['user_id']		= $usid;
						$data['action_type']	= 21;
						$data['pdt']			= mktime();
						$data['epoints']		= $money;
						$data['did']			= 0;
						$data['allp']			= 0;
						$data['bz']				= '21';
						$history->add($data);
						unset($data);
						
						$data = array();
						$data['uid']	= $uid;
						$data['user_id']= $usid;
						$data['rdt']	= time();
						$data['pdt']	= time();
						$data['epoint']	= $money;
						$data['is_pay']	= 1;
						$data['stype']	= 0;
						$chongzhi->add($data);
						unset($data);
	
						$fck->query("update __TABLE__ set agent_kt=agent_kt+".$money." where id=".$uid);
					}
				}else{
					$whe = array();
					$whe['orderid'] = array('eq',$order_no);
					$urs = $remit->where($whe)->field('id')->find();
					if(!$urs){
						$ctdir = "./ErrorLog";
						$ctname = "hx_pay_".date("Y").date("m").date("d");
						$daytime = date("Y-m-d H:i:s");
						$errdata = "时间：".$daytime."。订单号：".$order_no."支付成功，支付金额：".$order_amount."，但充值失败。原因：没有对应充值订单号记录。";
	
						$this->create_txt($ctdir,$ctname,$errdata);
					}
					unset($urs);
				}
				unset($remit,$fck,$history,$chongzhi,$where,$ors);
	
				$zf_ok = 1;
				$zf_re = "充值支付成功。";
				$zf_or = "订单号：".$order_no;
				$zf_am = "支付金额：".$order_amount;
			}else
			{
				$zf_re = "支付失败。";
			}
		}else
		{
			$zf_re = "支付失败。";
		}
		$this->assign('zf_re',$zf_re);
		$this->assign('zf_or',$zf_or);
		$this->assign('zf_am',$zf_am);
		
		$this->display("receive");
    }
    
	//建立文件
	private function create_txt($ctdir="./ErrorLog",$ctname="",$data="",$err){

		$hz = "txt";
		$dir = $ctdir."/".$ctname.".".$hz;
		$data = $data."\r\n";
		$sql=mb_convert_encoding($data, "UTF-8", "auto");//自动转码
		if(!is_dir($ctdir)){
			mkdir($ctdir, 0777);//创建文件夹
		}
		$oldsql = file_get_contents($dir);
		$newsql = $oldsql.$sql;
		$handle = fopen($dir, "w");
		if (!$handle){
			$err .= "<li>打开文件".$dir."失败!</li>";
		}
		if (!fwrite($handle, $newsql)){
			$err .= "<li>写入文件".$dir."失败!</li>";
		}
		fclose($handle);
	}
	
	
	//接收 简付宝
	public function orderReturn(){
		
		$billno = $_GET['MerOrderNo'];
		$amount = $_GET['Amount'];
		$mydate = $_GET['OrderDate'];
		$succ = $_GET['Succ'];
		$msg = $_GET['Msg'];
		$attach = $_GET['GoodsInfo'];
		$ipsbillno = $_GET['SysOrderNo'];
		$retEncodeType = $_GET['RetencodeType'];
		$currency_type = $_GET['Currency'];
		$signature = $_GET['Signature'];
		
		
		$fee = M('fee');
		$fee_rs = $fee->field('str17,str18')->find();
		$fee_rs17 = $fee_rs['str17'];//商户号
		$fee_rs18 = $fee_rs['str18'];//密钥
		unset($fee,$fee_rs);
		

		//'----------------------------------------------------
		//'   Md5摘要认证
		//'   verify  md5
		//'----------------------------------------------------
		$content = $billno . $amount . $mydate . $succ . $ipsbillno . $currency_type;
		//请在该字段中放置商户登陆mer.easy8pay.com下载的证书
		$cert = $fee_rs18;
		$signature_1ocal = md5($content . $cert);
		
		if ($signature_1ocal == $signature)
		{
			//----------------------------------------------------
			//  判断交易是否成功
			//  See the successful flag of this transaction
			//----------------------------------------------------
			if ($succ == 'Y')
			{
			  
				$remit = M('remit');
				$chongzhi = M('chongzhi');
				$history = M('history');
				$fck = M('fck');
				$where = array();
				$where['orderid'] = array('eq',$billno);
				$where['is_pay'] = array('eq',0);
				$ors = $remit->where($where)->find();
				if($ors){
					$tid = $ors['id'];
					$uid = $ors['uid'];
					$usid = $ors['user_id'];
					$money = $ors['amount'];
	
					$oresult = $remit->execute("update __TABLE__ set is_pay=1,ok_time=".mktime()." where is_pay=0 and id=".$tid);
					if($oresult){
	
						$data = array();
						$data['uid']			= $uid;
						$data['user_id']		= $usid;
						$data['action_type']	= 21;
						$data['pdt']			= mktime();
						$data['epoints']		= $money;
						$data['did']			= 0;
						$data['allp']			= 0;
						$data['bz']				= '21';
						$history->add($data);
						unset($data);
						
						$data = array();
						$data['uid']	= $uid;
						$data['user_id']= $usid;
						$data['rdt']	= time();
						$data['pdt']	= time();
						$data['epoint']	= $money;
						$data['is_pay']	= 1;
						$data['stype']	= 0;
						$chongzhi->add($data);
						unset($data);
	
						$fck->query("update __TABLE__ set agent_cash=agent_cash+".$money." where id=".$uid);
					}
				}else{
					$whe = array();
					$whe['orderid'] = array('eq',$billno);
					$urs = $remit->where($whe)->field('id')->find();
					if(!$urs){
						$ctdir = "./ErrorLog";
						$ctname = "zi_pay_".date("Y").date("m").date("d");
						$daytime = date("Y-m-d H:i:s");
						$errdata = "时间：".$daytime."。订单号：".$billno."支付成功，支付金额：".$money."，但充值失败。原因：没有对应充值订单号记录。";
	
						$this->create_txt($ctdir,$ctname,$errdata);
					}
					unset($urs);
				}
				unset($remit,$fck,$history,$chongzhi,$where,$ors);
	
				$zf_ok = 1;
				$zf_re = "充值支付成功。";
				$zf_or = "订单号：".$billno;
				$zf_am = "支付金额：".$money;
		
			}
			else
			{
				$zf_re = "支付失败。";
				//#################################################
				//交易失败，此处可增加商户逻辑
				//#################################################
		
			}
		}
		else
		{
			$zf_re = "签名不正确。";
			//#################################################
			//签名不正确，此处可增加商户逻辑
			//#################################################
		
		}
		
		$this->assign('zf_re',$zf_re);
		$this->assign('zf_or',$zf_or);
		$this->assign('zf_am',$zf_am);

		$this->display('receive');
		
		
	}
	
	
	//接收 简付宝
	public function severReturn(){
	
		$billno = $_POST['MerOrderNo'];
		$amount = $_POST['Amount'];
		$mydate = $_POST['OrderDate'];
		$succ = $_POST['Succ'];
		$msg = $_POST['Msg'];
		$attach = $_POST['GoodsInfo'];
		$ipsbillno = $_POST['SysOrderNo'];
		$retEncodeType = $_POST['RetencodeType'];
		$currency_type = $_POST['Currency'];
		$signature = $_POST['Signature'];
	
	
		$fee = M('fee');
		$fee_rs = $fee->field('str17,str18')->find();
		$fee_rs17 = $fee_rs['str17'];//商户号
		$fee_rs18 = $fee_rs['str18'];//密钥
		unset($fee,$fee_rs);
	
	
		//'----------------------------------------------------
		//'   Md5摘要认证
		//'   verify  md5
		//'----------------------------------------------------
		$content = $billno . $amount . $mydate . $succ . $ipsbillno . $currency_type;
		//请在该字段中放置商户登陆mer.easy8pay.com下载的证书
		$cert =$fee_rs18;
		$signature_1ocal = md5($content . $cert);
	
		if ($signature_1ocal == $signature)
		{
			//----------------------------------------------------
			//  判断交易是否成功
			//  See the successful flag of this transaction
			//----------------------------------------------------
			if ($succ == 'Y')
			{
					
				$remit = M('remit');
				$chongzhi = M('chongzhi');
				$history = M('history');
				$fck = M('fck');
				$where = array();
				$where['orderid'] = array('eq',$billno);
				$where['is_pay'] = array('eq',0);
				$ors = $remit->where($where)->find();
				if($ors){
					$tid = $ors['id'];
					$uid = $ors['uid'];
					$usid = $ors['user_id'];
					$money = $ors['amount'];
	
					$oresult = $remit->execute("update __TABLE__ set is_pay=1,ok_time=".mktime()." where is_pay=0 and id=".$tid);
					if($oresult){
	
						$data = array();
						$data['uid']			= $uid;
						$data['user_id']		= $usid;
						$data['action_type']	= 21;
						$data['pdt']			= mktime();
						$data['epoints']		= $money;
						$data['did']			= 0;
						$data['allp']			= 0;
						$data['bz']				= '21';
						$history->add($data);
						unset($data);
	
						$data = array();
						$data['uid']	= $uid;
						$data['user_id']= $usid;
						$data['rdt']	= time();
						$data['pdt']	= time();
						$data['epoint']	= $money;
						$data['is_pay']	= 1;
						$data['stype']	= 0;
						$chongzhi->add($data);
						unset($data);
	
						$fck->query("update __TABLE__ set agent_cash=agent_cash+".$money." where id=".$uid);
					}
				}else{
					$whe = array();
					$whe['orderid'] = array('eq',$billno);
					$urs = $remit->where($whe)->field('id')->find();
					if(!$urs){
						$ctdir = "./ErrorLog";
						$ctname = "zi_pay_".date("Y").date("m").date("d");
						$daytime = date("Y-m-d H:i:s");
						$errdata = "时间：".$daytime."。订单号：".$billno."支付成功，支付金额：".$money."，但充值失败。原因：没有对应充值订单号记录。";
	
						$this->create_txt($ctdir,$ctname,$errdata);
					}
					unset($urs);
				}
				unset($remit,$fck,$history,$chongzhi,$where,$ors);
	
				$zf_ok = 1;
				$zf_re = "充值支付成功。";
				$zf_or = "订单号：".$billno;
				$zf_am = "支付金额：".$money;
	
			}
			else
			{
				$zf_re = "支付失败。";
				//#################################################
				//交易失败，此处可增加商户逻辑
				//#################################################
	
			}
		}
		else
		{
			$zf_re = "签名不正确。";
			//#################################################
			//签名不正确，此处可增加商户逻辑
			//#################################################
	
		}
	
		$this->assign('zf_re',$zf_re);
		$this->assign('zf_or',$zf_or);
		$this->assign('zf_am',$zf_am);
	
		$this->display('receive');
	
	
	}
	
}
?>