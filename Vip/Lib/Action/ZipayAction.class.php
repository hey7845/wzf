<?php
class ZipayAction extends Action{
	
    /**
     * 智付第三方
     * str17 商户号参数
     * str18 密钥参数
     * **/
    public function zi_pay($orderid,$amount)
    {
    	
    	$fee = M('fee');
    	$fee_rs = $fee->field('str17,str18')->find();
    	$fee_rs17 = $fee_rs['str17'];//商户号
    	$fee_rs18 = $fee_rs['str18'];//密钥
    	unset($fee,$fee_rs);
    	
       //参数编码字符集(必选)
		$input_charset = 'UTF-8';

		//接口版本(必选)固定值:V3.0
		$interface_version = 'V3.0';

		//商家号（必填）
		$merchant_code = $fee_rs17;

		//后台通知地址(必填)
		$see = $_SERVER['HTTP_HOST'];
		$notify_url = "http://".$see."".__APP__."/Callback/receive/";

		//定单金额（必填）
		$order_amount = $amount;

		//商家定单号(必填)
		$order_no = $orderid;

		//商家定单时间(必填)
		$order_time = date('Y-m-d H:i:s');

		//签名方式(必填)
		$sign_type = 'MD5';

		//商品编号(选填)
		$product_code = 'N10000001';

		//商品描述（选填）
		$product_desc = 'N10000001';

		//商品名称（必填）
		$product_name = 'N10000001';

		//端口数量(选填)
		$product_num = '';

		//页面跳转同步通知地址(选填)
		$return_url = "http://".$see."".__APP__."/Callback/receive/";

		//业务类型(必填)
		$service_type = 'direct_pay';

		//商品展示地址(选填)
		$show_url = '';

		//公用业务扩展参数（选填）
		$extend_param = '';

		//公用业务回传参数（选填）
		$extra_return_param = '';

		// 直联通道代码（选填）
		$bank_code = $_POST['bank_code'];

		//客户端IP（选填）
		$client_ip = '';

		// 注  new String(参数.getBytes("UTF-8"),"此页面编码格式"); 若为GBK编码 则替换UTF-8 为GBK 
		
		if($product_name != "") {
		  $product_name = mb_convert_encoding($product_name, "UTF-8", "UTF-8");
		}
		if($product_desc != "") {
		  $product_desc = mb_convert_encoding($product_desc, "UTF-8", "UTF-8");
		}
		if($extend_param != "") {
		  $extend_param = mb_convert_encoding($extend_param, "UTF-8", "UTF-8");
		}
		if($extra_return_param != "") {
		  $extra_return_param = mb_convert_encoding($extra_return_param, "UTF-8", "UTF-8");
		}
		if($product_code != "") {
		  $product_code = mb_convert_encoding($product_code, "UTF-8", "UTF-8");
		}
		if($return_url != "") {
		  $return_url = mb_convert_encoding($return_url, "UTF-8", "UTF-8");
		}
		if($show_url != "") {
		  $show_url = mb_convert_encoding($show_url, "UTF-8", "UTF-8");
		}
    	
		/*
		**
		 ** 签名顺序按照参数名a到z的顺序排序，若遇到相同首字母，则看第二个字母，以此类推，同时将商家支付密钥key放在最后参与签名，
		 ** 组成规则如下：
		 ** 参数名1=参数值1&参数名2=参数值2&……&参数名n=参数值n&key=key值
		 **/
		$signSrc= "";
	
		//组织订单信息
		if($bank_code != "") {
			$signSrc = $signSrc."bank_code=".$bank_code."&";
		}
		if($client_ip != "") {
	                $signSrc = $signSrc."client_ip=".$client_ip."&";
		}
		if($extend_param != "") {
			$signSrc = $signSrc."extend_param=".$extend_param."&";
		}
		if($extra_return_param != "") {
			$signSrc = $signSrc."extra_return_param=".$extra_return_param."&";
		}
		if($input_charset != "") {
			$signSrc = $signSrc."input_charset=".$input_charset."&";
		}
		if($interface_version != "") {
			$signSrc = $signSrc."interface_version=".$interface_version."&";
		}
		if($merchant_code != "") {
			$signSrc = $signSrc."merchant_code=".$merchant_code."&";
		}
		if($notify_url != "") {
			$signSrc = $signSrc."notify_url=".$notify_url."&";
		}
		if($order_amount != "") {
			$signSrc = $signSrc."order_amount=".$order_amount."&";
		}
		if($order_no != "") {
			$signSrc = $signSrc."order_no=".$order_no."&";
		}
		if($order_time != "") {
			$signSrc = $signSrc."order_time=".$order_time."&";
		}
		if($product_code != "") {
			$signSrc = $signSrc."product_code=".$product_code."&";
		}
		if($product_desc != "") {
			$signSrc = $signSrc."product_desc=".$product_desc."&";
		}
		if($product_name != "") {
			$signSrc = $signSrc."product_name=".$product_name."&";
		}
		if($product_num != "") {
			$signSrc = $signSrc."product_num=".$product_num."&";
		}
		if($return_url != "") {
			$signSrc = $signSrc."return_url=".$return_url."&";
		}
		if($service_type != "") {
			$signSrc = $signSrc."service_type=".$service_type."&";
		}
		if($show_url != "") {
			$signSrc = $signSrc."show_url=".$show_url."&";
		}
	    //设置密钥
		$key = $fee_rs18;
		$signSrc = $signSrc."key=".$key;
	
		$singInfo = $signSrc;
	
		//签名
		$sign = md5($singInfo);
	
		
		$this->assign('sign',$sign);
		$this->assign('input_charset',$input_charset);
		$this->assign('interface_version',$interface_version);
		$this->assign('merchant_code',$merchant_code); //商家号
		$this->assign('notify_url',$notify_url);
		$this->assign('order_amount',$order_amount);
		$this->assign('order_no',$order_no);
		$this->assign('order_time',$order_time);
		$this->assign('sign_type',$sign_type);
		$this->assign('product_code',$product_code);
		$this->assign('product_desc',$product_desc);
		$this->assign('product_name',$product_name);
		$this->assign('product_num',$product_num);
		$this->assign('return_url',$return_url);
		$this->assign('service_type',$service_type);
		$this->assign('show_url',$show_url);
		$this->assign('extend_param',$extend_param);
		$this->assign('extra_return_param',$extra_return_param);
		$this->assign('bank_code',$bank_code);
		$this->assign('client_ip',$client_ip);
		
    }
	

	
}


?>