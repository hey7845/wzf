<?php
class CheFieldAction extends Action {
	
	//检测昵称(昵称是唯一的) [修改时调用。注意：注册调用时$AutoId写0]
	protected function nickname($Value,$AutoId){  //昵称值,须检测的AutoId
		$Value = trim($Value);
		if(empty($Value)){
			$this->error('名称不能为空!');
			exit;
		}
		
		$where = array();
		$where['nickname'] = array('eq',$Value);
		$where['id']       = array('neq',$AutoId);
		
		$rs = M('fck')->where($where)->find();
		if(!$rs){
			return $Value;  //返回值
		}else{
			$this->error('该名称已经存在!');
			exit;
		}
	}
	
	
	//检测银行
	protected function bank_name($Value){  //银行索引
		$Value = (int) $Value;
		$fee_rs = M('fee')->field('s19')->find();
		$fee_s19 = explode('|',$fee_rs['s19']);
		if($Value <= -1 or $Value >= count($fee_s19)){
			$this->error('银行选择错误！');
			exit;
		}else{
			return $Value;
		}
	}
	
	
	
	
	
	
	
}
?>