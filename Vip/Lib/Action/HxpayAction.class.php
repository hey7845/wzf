<?php
class HxpayAction extends Action{
	/**
     * 环讯第三方
     * str17 商户号参数
     * str18 密钥参数
     * 注意修改Zhuan文件夹内config配置
     * **/
    public function Hx_pay($orderid,$payamount){
		
		$fee = M('fee');
		$fee_rs = $fee->field('str17,str18,str19')->find();
		$fee_rs17 = $fee_rs['str17'];//商户号
		$fee_rs18 = $fee_rs['str18'];//商户名
		$fee_rs19 = $fee_rs['str19'];//账户号
		unset($fee,$fee_rs);
		
		//网关地址
   		$payWayurl="http://www.hongjiudianzi.cn/ips.php";
   		$paytestWayurl="http://bankbackuat.ips.com.cn/psfp-entry/gateway/payment.html";
  
		//定义日志路径
		define('BASE_PATH',str_replace( '\\' , '/' , realpath(dirname(__FILE__).'/../'))."/log");
		define('PATH_LOG_FILE',BASE_PATH.'/newPayDemo-'.date('Y-m-j').'.log');
		//创建日志存储目录
		if (!is_dir(BASE_PATH)) mkdir(BASE_PATH);
		define('Mode', "1");//0#测试环境 1#生产环境
		
		$pMerCode 			= '175462';//$fee_rs19;
		$pMerName 			= '深圳市瑞韵利宝科技有限公司';//$fee_rs18;
		$pAccount 			= '1754620019';//$fee_rs17;
		$pMsgId 			= '';
		$pReqDate 			= date("Y").date("m").date("d").date("H").date("i").date("s");
		$pMerBillNo 		= $orderid;
		$payamount 			= number_format($payamount,2,".","");
		$pAmount 			= $payamount;
		$pDate 				= date('Ymd');
		$pCurrencyType 		= "RMB";
		$pGatewayType 		= "01";
		$pLang 				= "GB";
		
		$see = $_SERVER['HTTP_HOST'];
		$Https_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
		$pMerchanturl 		= $Https_url.$see."".__APP__."/Callback/hx_receive/";
		$pFailUrl 			= '';
		$pAttach 			= '![CDATA[]';
		$pOrderEncodeTyp	= '5';	
		$pRetEncodeType		= '17';	
		$pRetType			= '1';
		$pServerUrl			= $Https_url.$see."".__APP__."/Callback/hx_receive/";	
		$pBillEXP			= '';	
		$pGoodsName			= $orderid;	
		$pIsCredit			= '';
		$pBankCode			= '';
		$pProductType		= '';
				
		$reqParam="商户号".$pMerCode
          ."商户名".$pMerName
          ."账户号".$pAccount
          ."消息编号".$pMsgId
          ."商户请求时间".$pReqDate
          ."商户订单号".$pMerBillNo
          ."订单金额".$pAmount
          ."订单日期".$pDate
          ."币种".$pCurrencyType
          ."支付方式".$pGatewayType
          ."语言".$pLang
          ."支付结果成功返回的商户URL".$pMerchanturl
          ."支付结果失败返回的商户URL".$pFailUrl
          ."商户数据包".$pAttach
          ."订单支付接口加密方式".$pOrderEncodeTyp
          ."交易返回接口加密方式".$pRetEncodeType
          ."返回方式".$pRetType
          ."Server to Server返回页面 ".$pServerUrl
          ."订单有效期".$pBillEXP
          ."商品名称".$pGoodsName
          ."直连选项".$pIsCredit
          ."银行号".$pBankCode
          ."产品类型".$pProductType;
	
 	file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'提交参数信息:'.$reqParam."\r\n",FILE_APPEND);

	if($pIsCredit==0)
	 {
		 $pBankCode="";
		 $pProductType='';
	 }

   	//请求报文的消息体
	$strbodyxml= "<body>"
	         ."<MerBillNo>".$pMerBillNo."</MerBillNo>"
	         ."<Amount>".$pAmount."</Amount>"
	         ."<Date>".$pDate."</Date>"
	         ."<CurrencyType>".$pCurrencyType."</CurrencyType>"
	         ."<GatewayType>".$pGatewayType."</GatewayType>"
             ."<Lang>".$pLang."</Lang>"
	         ."<Merchanturl>".$pMerchanturl."</Merchanturl>"
	         ."<FailUrl>".$pFailUrl."</FailUrl>"
			 ."<Attach>".$pAttach."</Attach>"
			 ."<OrderEncodeType>".$pOrderEncodeTyp."</OrderEncodeType>"
			 ."<RetEncodeType>".$pRetEncodeType."</RetEncodeType>"
			 ."<RetType>".$pRetType."</RetType>"
			 ."<ServerUrl>".$pServerUrl."</ServerUrl>"
			 ."<BillEXP>".$pBillEXP."</BillEXP>"
			 ."<GoodsName>".$pGoodsName."</GoodsName>"
			 ."<IsCredit>".$pIsCredit."</IsCredit>"
			 ."<BankCode>".$pBankCode."</BankCode>"
			 ."<ProductType>".$pProductType."</ProductType>"
	      ."</body>";
  	
	$pMerCert = 'QuHAlWymZ29HGrnRDs2HpO8hQ951vuEByewCWmVubhl79x6fcvtTmJMTHLrL46tLhuGEjvlnDluDzTQSUSjcQ6z0txkhMPYn4FCeKWoBqLtXGhsFROBBaj2NaSgyN8bU';
	$Sign=$strbodyxml.$pMerCode.$pMerCert;//签名明文
	file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'签名明文:'.$Sign."\r\n",FILE_APPEND);
  
	$pSignature = md5($strbodyxml.$pMerCode.$pMerCert);//数字签名 
	//请求报文的消息头
	$strheaderxml= "<head>"
                   ."<Version>".$pVersion."</Version>"
                   ."<MerCode>".$pMerCode."</MerCode>"
                   ."<MerName>".$pMerName."</MerName>"
                   ."<Account>".$pAccount."</Account>"
                   ."<MsgId>".$pMsgId."</MsgId>"
                   ."<ReqDate>".$pReqDate."</ReqDate>"
                   ."<Signature>".$pSignature."</Signature>"
              ."</head>";
 
	//提交给网关的报文
	$strsubmitxml =  "<Ips>"
					."<GateWayReq>"
					.$strheaderxml
					.$strbodyxml
					."</GateWayReq>"
					."</Ips>";
	
	
	//提交给网关明文
	file_put_contents(PATH_LOG_FILE,date('y-m-d h:i:s').'提交给网关明文:'.$strsubmitxml."\r\n",FILE_APPEND);
	 
	 
	$this->assign('payWayurl',$payWayurl);
	$this->assign('paytestWayurl',$paytestWayurl);
	$this->assign('strsubmitxml',$strsubmitxml);
 
    }
	
}


?>