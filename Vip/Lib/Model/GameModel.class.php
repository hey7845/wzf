<?php
class GameModel extends CommonModel {

   //游戏币写入表
   public function setGameCash($uid=0,$gm=0){
   		if(!empty($_SESSION[C("USER_AUTH_KEY")])){
			$data	= array();
			$t		= strtotime(date("Y-m-d"));//时间,只精确到天
			$data['uid']	    = $uid;
			$data['g_money']	= $gm;
			$data['used_money']	= 0;
			
			$map['uid']	= $uid;
			$map['get_time'] = $t;
			$rs		= $this->where($map)->find();
			
			if($rs){
				$data['g_money']	= $rs['g_money'] + $gm;
				$data['used_money']	= $rs['used_money'];
				$this->where($map)->save($data);
			}else{
				$data['get_time']	= $t;
				$this->add($data);
			}
		}
   }
   
   //清空到期还没有使用的游戏币
   public function clearTimeoutCash(){
   		$fck	= M('fck');
   		$fee_str4	= M('fee')->where("id=1")->field("str4")->find();
		$str4	= $fee_str4['str4'];//规定超期的时间
		
		$data	= array();
		$t		= strtotime(date("Y-m-d")) - $str4*3600*24;//小于这个时间的就是超期

		$map	= "get_time<=$t and used_money<g_money";
		$field	= "sum(g_money-used_money) as lm,id,uid,used_money,g_money";
		$rs		= $this->where($map)->field($field)->select();
		
		foreach($rs as $vo){
			$lm	= $vo['lm'];
			$uid = $vo['uid'];
			$fck->execute("update __TABLE__ set game_cash=game_cash-$lm where id=$uid");
		}
		$this->execute("update __TABLE__ set used_money=g_money where get_time<=$t and used_money<g_money");//这条记录没有可以使用的K币了
		$fck->execute("update __TABLE__ set game_cash=0 where id>0 and game_cash<0");//防止出现小于0的情况
		
		unset($fck,$fee_str4,$rs);
   }
   
   //更新使用过的游戏币
	public function updateGameCash($uid=0,$gm=0){
   		if(!empty($_SESSION[C("USER_AUTH_KEY")])){
			
			$field	= "*";
			$map	= "uid=$uid and g_money>used_money";
			$rs		= $this->where($map)->field($field)->order("get_time asc")->select();
			$cgm	= $gm;//使用的游戏币
			
			foreach($rs as $vo){
				if($cgm<=0){
					break;
				}
				$gid	= $vo['id'];
				$lm	= $vo['g_money']	- $vo['used_money'];
				if($lm <= $cgm){
					$this->execute("update __TABLE__ set used_money=g_money where id=$gid");//使用的钱比这条记录的钱大，那么这个K币就使用完了
					$cgm	= $cgm - $lm;//余留的钱
				}else{
					$this->execute("update __TABLE__ set used_money=used_money+$cgm where id=$gid");//还有可以使用的K币
					$cgm	= 0;//余留的钱
				}
			}
		}
   }
}
?>