<?php
// 用户模型
class UserModel extends Model {
	public $_validate	=	array(
		array('shopid','require','所属会员中心必须填写'),
		array('reman','require','推荐人会员名必须填写'),
		array('UserID','/^[a-z]\w{3,}$/i','帐号格式错误'),
		array('Password','require','一级密码必须填写'),
		array('rePassword','require','确认一级密码必须填写'),
		array('rePassword','Password','确认一级密码不一致',self::EXISTS_VAILIDATE,'confirm'),
		array('PassOpen','require','二级密码必须填写'),
		array('rePassOpen','require','确认二级密码必须填写'),
		array('rePassOpen','PassOpen','确认二级密码不一致',self::EXISTS_VAILIDATE,'confirm'),
		array('UserName','require','开户姓名必须填写'),
		array('UserCode','require','身份证号必须填写'),
		array('UserTel','require','联系电话必须填写'),
		array('UserID','','帐号已经存在',self::EXISTS_VAILIDATE,'unique',self::MODEL_INSERT),
		);
	
	public $_auto		=	array(
		//array('Password','pwdHash',self::MODEL_BOTH,'callback'),
		//array('PassOpen','pwdHash_pass',self::MODEL_BOTH,'callback'),
		//array('create_time','time',self::MODEL_INSERT,'function'),
		//array('update_time','time',self::MODEL_UPDATE,'function'),
		);
	protected function pwdHash() {
		if(isset($_POST['Password'])) {
			return pwdHash($_POST['Password']);
		}else{
			return false;
		}
	}
	
}
?>