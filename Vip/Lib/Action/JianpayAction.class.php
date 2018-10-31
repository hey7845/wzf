<?php
class JianpayAction extends Action{
	
    /**
     * 简付宝第三方
     * str17 商户号参数
     * str18 密钥参数
     * 注意修改Zhuan文件夹内config配置
     * **/
    public function Jian_pay($orderid,$payamount)
    {
    	
    	$fee = M('fee');
    	$fee_rs = $fee->field('str17,str18')->find();
    	$fee_rs17 = $fee_rs['str17'];//商户号
    	$fee_rs18 = $fee_rs['str18'];//密钥
    	unset($fee,$fee_rs);

		//提交地址
		
// 		$form_url = 'http://testpay.easy8pay.net/gateway.aspx'; //测试环境
		
		$form_url = 'https://payment.easy8pay.net/gateway.aspx'; //正式环境
		
		
		//交易账户号
		$Mer_code = $fee_rs17;
		
		//商户证书：登陆http://merchant.ips.com.cn/商户后台下载的商户证书内容
		$Mer_key = $fee_rs18;
		
		//商户订单编号
		$Billno = $orderid;
		
		//订单金额(保留2位小数)
		$Amount = number_format($payamount, 2, '.', '');
		
		//订单日期
		$Date = date('Ymd');
		
		//币种
		$Currency_Type = "RMB";
		
		//支付卡种
		$Gateway_Type = '01';
		
		//语言
		$Lang = 'GB';
		
		//商户返回地址
		$see = $_SERVER['HTTP_HOST'];
		$Https_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
// 		$url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']) . $_SERVER['SCRIPT_NAME'];
// 		$returnurl = str_ireplace('orderpay', 'Callback/orderReturn', $url);
// 		$serverurl = str_ireplace('orderpay', 'Callback/severReturn', $url);

		$returnurl = $Https_url.$see."".__APP__."/Callback/orderReturn/";
		$serverurl = $Https_url.$see."".__APP__."/Callback/severReturn/";
		
		//支付结果成功返回的商户URL
		$Merchanturl = $returnurl;
		
		//商户数据包
		$Attach = "";
		
		//订单支付接口加密方式
		$OrderEncodeType =2;
		
		//交易返回接口加密方式 
		$RetEncodeType = 12;
		
		//返回方式
		$Rettype = 0;
		
		//Server to Server 返回页面URL
		$ServerUrl =$serverurl;
		
		//订单支付接口的Md5摘要，原文=订单号+金额+日期+支付币种+商户证书 
		$SignMD5 = md5($Billno . $Amount . $Date . $Currency_Type . $Mer_key);


		$this->assign('Mer_code',$Mer_code);
		$this->assign('Billno',$Billno);
		$this->assign('Amount',$Amount);//商家号
		$this->assign('Date',$Date);
		$this->assign('Currency_Type',$Currency_Type);
		$this->assign('Gateway_Type',$Gateway_Type);
		$this->assign('Lang',$Lang);
		$this->assign('Merchanturl',$Merchanturl);
		$this->assign('Attach',$Attach);
		$this->assign('OrderEncodeType',$OrderEncodeType);
		$this->assign('RetEncodeType',$RetEncodeType);
		$this->assign('Rettype',$Rettype);
		$this->assign('ServerUrl',$ServerUrl);
		$this->assign('SignMD5',$SignMD5);
		$this->assign('form_url',$form_url);
		


    }
	

	
}


?>