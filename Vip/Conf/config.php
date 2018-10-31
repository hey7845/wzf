<?php
if (!defined('THINK_PATH')) exit();
$config	=	require './config.php';
$array=array(
	'USER_AUTH_ON'=>true,
	'USER_AUTH_TYPE'		=>2,		// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY' => 'authId_N_bvip', // 用户认证SESSION标记
    'ADMIN_AUTH_KEY'		=>'administrator',
	'USER_AUTH_MODEL'		=>'fck',	// 默认验证数据表模型
	'AUTH_PWD_ENCODER'		=>'md5',	// 用户认证密码加密方式
	'NOT_AUTH_MODULE'		=>'Public,Reg',		// 默认无需认证模块
	'REQUIRE_AUTH_MODULE'	=>'',		// 默认需要认证模块
	'NOT_AUTH_ACTION'		=>'',		// 默认无需认证操作
	'REQUIRE_AUTH_ACTION'	=>'',		// 默认需要认证操作
    'GUEST_AUTH_ON'			=>false,    // 是否开启游客授权访问
    'GUEST_AUTH_ID'			=>0,     // 游客的用户ID
	'SHOW_RUN_TIME'			=>false,			// 运行时间显示
	'SHOW_ADV_TIME'			=>false,			// 显示详细的运行时间
	'SHOW_DB_TIMES'			=>false,			// 显示数据库查询和写入次数
	'SHOW_CACHE_TIMES'		=>false,		// 显示缓存操作次数
	'SHOW_USE_MEM'			=>false,			// 显示内存开销
	//'SHOW_PAGE_TRACE' 		=>true,			//开启页面Trace
	'ONE_PAGE_RE'			=> 12,           // 每页显示记录数
	'PAGE_LISTROWS'			=> 10,
	'PAGE_ROLLPAGE'			=> 5,
    'DB_LIKE_FIELDS'		=>'title|remark',
	'RBAC_ROLE_TABLE'		=>'think_role',
	'RBAC_USER_TABLE'		=>'think_role_user',
	'RBAC_ACCESS_TABLE'		=>'think_access',
	'RBAC_NODE_TABLE'		=>'think_node',
	'USER_AUTH_GATEWAY'		=>'/Public/login',	// 默认认证网关
	
	'TMPL_ACTION_ERROR'		=> 'Public:error', // 默认错误跳转对应的模板文件
	
	'VAR_PAGE'	=>	'p',	//分页传递参数

	//=======奖金项名称============
	'Bonus_B1'   => '奖金',
	'Bonus_B1c'  => '',   //空则显示, style="display:none;"则不显示
	'Bonus_B2'   => '服务补贴',
	'Bonus_B2c'  => '',
	'Bonus_B3'   => '推荐补贴',
	'Bonus_B3c'  => '',
	'Bonus_B4'   => '个人所得税',
	'Bonus_B4c'  => '',
	'Bonus_B5'   => '报单费',
	'Bonus_B5c'  => '',
	'Bonus_B6'   => '分红',
	'Bonus_B6c'  => '',
	'Bonus_B7'   => '理财金',
	'Bonus_B7c'  => '',
	'Bonus_B8'   => '税收',
	'Bonus_B8c'  => '',
	'Bonus_B9'   => '直推奖',
	'Bonus_B9c'  => '',
	'Bonus_B10'  => '直推奖',
	'Bonus_B10c' => 'style="display:none;"',
	'Bonus_HJ'   => '合计',
	'Bonus_HJc'  => 'style="display:none;"',
	'Bonus_Bb0'  => '合计',
	'Bonus_Bb0c' => 'style="display:none;"',
	'Bonus_B0'   => '合计',
	'Bonus_B0c'  => '',
	'Bonus_XX'   => '详细',
	'Bonus_XXc'  => '',

	//=======系统参数=========
	'System_namex'  => '会员管理系统',     				 //系统名字
	'System_bankx'  => '农业银行|工商银行',      //银行名字
	//'System_bankx'  => '财付通',      //银行名字
	'User_namex'    => '会员账户',
	'Nick_namex'    => '昵称',
	'Agent_Us_Name'  => '省代理|市代理|县代理',    //会员级别名称
	'Member_Level'  => '普通会员|经销商',    //会员级别名称
	'Member_Money'  => '6600|1000|3000|9000|18000|36000',             //注册金额
	'Member_Single' => '1|2|6|18|36|72',                      //会员级别单数
	
	'BAK_Data_Path'	=> 'Bak_data',	//备份数据路径
	'BAK_Zip_Path'	=> 'Bak_zip',	//压缩数据路径
	'BAK_Error_Path'=> 'ErrorLog',	//还原错误文档存储路径
	'System_TYPE' => 0, // 0:太阳制度  1:双规 
	

);
return array_merge($config,$array);
?>
