<?php
// 本类由系统自动生成，仅供测试用途
class BackupAction extends CommonAction {
	public function _initialize() {
		$this->_checkUser();
		$this->_Admin_checkUser();//后台权限检测
		header("Content-type: text/html; charset=utf-8");
	}
	public function Check_C_config() {
		//默认备份配置
		$bpath = C('BAK_Data_Path');
		$zpath = C('BAK_Zip_Path');
		$epath = C('BAK_Error_Path');
		if(empty($bpath)){
			$bpath	= "Bak_data";
		}
		if(empty($zpath)){
			$zpath	= "Bak_zip";
		}
		if(empty($epath)){
			$epath	= "ErrorLog";
		}
		$array[1] = $bpath;
		$array[2] = $zpath;
		$array[3] = $epath;
		return $array;
	}

	public function index(){
		$C_path = $this->Check_C_config();
		$dir = $C_path[1]; //备份目录
		$list = $this->list_file($dir);
		sort($list);
		$ficount = count($list);
		$this->assign('list',$list);
		$this->assign('count',$ficount);
		$this->display();
	}

	//遍历
	public function list_file($dir){

		import ( "@.ORG.KuoZhan" );  //导入扩展类
        $KuoZhan = new KuoZhan();
		$arraylist = array();
		$list = scandir($dir); // 得到该文件下的所有文件和文件夹
		$i=0;

		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		        	if ($KuoZhan->is_utf8($file) == false){
		                $file = iconv('GB2312','UTF-8',$file);
		            }
		        	if($file=="." ||$file==".."){ //判断是不是文件夹
						continue;
					}
					$file_location = $dir ."/". $file;
					$arraylist[$i]['name'] = $file;
					$arraylist[$i]['time'] = $this->selecttime($file_location);
					$arraylist[$i]['path'] = $file_location;
					$arraylist[$i]['getpath'] = str_replace("/","|",$file_location);
					$i++;
		        }
		        closedir($dh);
		    }
		}

		return $arraylist;
	}

	public function selecttime($file){
		import ( "@.ORG.KuoZhan" );  //导入扩展类
        $KuoZhan = new KuoZhan();
        if ($KuoZhan->is_utf8($file) == true){
            $file = iconv('UTF-8','GB2312',$file);
        }
        $retime = filectime($file);
		return $retime;
	}

	//处理
	public function indexAC(){
		$action = $_REQUEST['action'];
		if ($action == "删除") {
			$this->index_del();
		}
		if ($action == "还原") {
			$this->DBHuanYuan();
		}
		if ($action == "下载") {
//			$this->DBDoZip();
			$path = $_POST['mname'];
			$backulr = __URL__."/DBDoZip/mname/".$path;
			echo "<script>window.location='".$backulr."';</script>";
			echo "数据下载...下载完成后请点击 [<a href='".__URL__."'>返回</a>]";
			exit;
		}
		$this->error('参数错误！');
		exit;
	}

	//删除文件
	public function index_del(){

		$fieldname = $_POST['fname'];
		import ( "@.ORG.KuoZhan" );  //导入扩展类
        $KuoZhan = new KuoZhan();
		if ($KuoZhan->is_utf8($fieldname) == true){
            $fieldname = iconv('UTF-8','GB2312',$fieldname);
        }
		if(empty($fieldname)){
			$this->error('参数错误1！');
			exit;
		}

		$now_path = $_SERVER["DOCUMENT_ROOT"];
		$web_path = __ROOT__.'/';
//		$web_path = substr($web_path,1);
		$all_path = $now_path.$web_path.$fieldname;
		if(is_dir($all_path)){
			$errmsg = "";
			$this->deldir($all_path,$errmsg);
		}else{
			unlink($all_path);//删除
		}
		if($errmsg){
			$this->error("删除失败，错误：".$errmsg);
			exit;
		}else{
			$bUrl = __URL__.'/index/';
			$this->success('删除成功！');
			exit;
		}
	}

	public function deldir($dir,&$errmsg){

		//先删除目录下的文件：
		$dh=opendir($dir);
		while ($file=readdir($dh)) {
			if($file!="." && $file!="..") {
				$fullpath=$dir."/".$file;
				if(!is_dir($fullpath)) {
					unlink($fullpath);
				} else {
					$this->deldir($fullpath,$errmsg);
				}
			}
		}
		closedir($dh);
		//删除当前文件夹：
		if(rmdir($dir)){

		}else{
			$errmsg .= "<li>删除".$dir."失败！</li>";
		}
	}

	//还原
	public function DBHuanYuan(){
		//读取文件
		set_time_limit(0);
		$fieldname = $_REQUEST['fname'];
		$fieldname = str_replace("|","/",$fieldname);
		import ( "@.ORG.KuoZhan" );  //导入扩展类
        $KuoZhan = new KuoZhan();
		if ($KuoZhan->is_utf8($fieldname) == true){
            $fieldname = iconv('UTF-8','GB2312',$fieldname);
        }
		if(empty($fieldname)){
			$this->error('参数错误1！');
			exit;
		}
		$dir = $fieldname;
		$filearray = array();
		if (is_dir($dir)) {//目录
		    if ($dh = opendir($dir)) {
		    	$i = 0;
		        while (($file = readdir($dh)) !== false) {
		        	if($file=="." ||$file==".."){ //判断是不是文件夹
						continue;
					}
					if ($KuoZhan->is_utf8($file) == true){
		                $file = iconv('UTF-8','GB2312',$file);
		            }
					$file_location = $dir ."/". $file;
					if(is_dir($file_location)){//不包含文件夹内

					}else{
						if ($KuoZhan->is_utf8($file) == false){
			                $file = iconv('GB2312','UTF-8',$file);
			            }
			            $file_location = $dir ."/". $file;
						$filearray[$i] = $file_location;
					}
					$i++;
		        }
		        closedir($dh);
		    }
		}else{//文件
			if ($KuoZhan->is_utf8($dir) == false){
	            $dir = iconv('GB2312','UTF-8',$dir);
	        }
			$filearray[] = $dir;
		}
		sort($filearray);//重新排序
		$errmsg = "";
		$msg = "";
		$maxx = count($filearray);
		$thnn = 0;
		ob_start(); //打开输出缓冲区
		ob_end_flush();
		ob_implicit_flush(1); //立即输出
		foreach($filearray as $vo){
			$thnn++;
			$this->sql_query($vo,$errmsg,$msg,$thnn,$maxx);
		}
		if($errmsg){

			echo $errmsg;
			exit;
		}else{
			echo $msg;
			exit;
		}

	}

	public function sql_query($dir="",&$errmsg="",&$msg="",$n=0,$maxt=0){

		import ( "@.ORG.KuoZhan" );  //导入扩展类
        $KuoZhan = new KuoZhan();
		if ($KuoZhan->is_utf8($dir) == true){
            $dir = iconv('UTF-8','GB2312',$dir);
        }
		$sql=file_get_contents($dir);
		$sql=mb_convert_encoding($sql, "UTF-8", "auto");	//自动转码
		$arr=explode('-- <fen> --', $sql);
		if(count($arr)>1){
			array_pop($arr);	//删除最后一个空元素
		}
		$err=0;

		$tablerows = count($arr);
		if($tablerows>=1000){
			$wanlen=(int)($tablerows/1000).'000';
		}else{
			$wanlen=$tablerows;
		}
		$ti = 0;
		$titt = $ti+1;
		echo str_repeat(" ",4096); //确保足够的字符
		echo '<script language="javascript">'.
		'window.parent.restorepresent("'.$n.'","'.$maxt.'","'.$titt.'","'.$wanlen.'");'.
		'</script>';
		$cp = 1;
		foreach ($arr as $value) {
			$ti++;
			$ttt = $ti;
			$re=M()->query($value);
			if (!is_array($re)){
				$err++;
				$tt = M()->getLastSql();
				$mssg .= "-- 错误： --\r\n".$tt."\r\n";
			}

			if($tablerows>=1000){
				$want=1;
			}else{
				$want=0;
			}
			$tdt=1000*$cp;
			$modd=(int)($ttt/$tdt);
			if($modd>0){
				$cp++;
				echo str_repeat(" ",4096); //确保足够的字符
				echo '<script language="javascript">'.
				'window.parent.restorepresent("'.$n.'","'.$maxt.'","'.$ti.'","'.$wanlen.'");'.
				'</script>';
			}elseif($ttt==$tablerows){
				if($want==0){
					echo str_repeat(" ",4096); //确保足够的字符
					echo '<script language="javascript">'.
					'window.parent.restorepresent("'.$n.'","'.$maxt.'","'.$ti.'","'.$wanlen.'");'.
					'</script>';
				}
			}
		}
		usleep(100000);//0.5秒

		if ($err>0){
			$C_path = $this->Check_C_config();
			$ctdir = "./".$C_path[3];
			if(!is_dir($ctdir)){
				mkdir($ctdir, 0777);	//创建文件夹
			}
			else {
				chmod($ctdir, 0777);	//改变文件模式
			}
			$data .= "-- ".$err."条错误语句未执行！ --\r\n".$mssg;

			$date = date("Y-m-d H-i-s");
			$ctname = "error ".$date;
			$this->createtable_txt($ctdir,$ctname,$data,$err,1);
			$wronglog = $ctdir."/".$ctname.".log";
			$errmsg = "包含错误，保存文档：".$wronglog.' 。';
		}else{
			$msg = "还原成功！";
		}

	}

	//备份
	public function DBBeiFen(){
		set_time_limit(0);
		$C_path = $this->Check_C_config();
		//写入文件
		$date=Date("Y-m-d-H-i-s");	//不能用冒号，否则创建文件失败！
		$fdirmk="./".$C_path[1];
		if(!is_dir($fdirmk)){
			mkdir($fdirmk, 0777);	//创建文件夹
		}
		else {
			chmod($fdirmk, 0777);	//改变文件模式
		}
		$randmk = "BAK-".$date."-".rand(1000,9999);
		$dir = $fdirmk."/".$randmk;
		if(!is_dir($dir)){
			mkdir($dir, 0777);	//创建文件夹
		}
		else {
			chmod($dir, 0777);	//改变文件模式
		}

		$err = "";

		$this->getDBQk($dir,$err);//清空表
		$this->getField($dir,$err);//创建表
		$this->getData($dir,$err);//插入表


		if($err){
			$errdir = $dir."/error.sql";
			$err=mb_convert_encoding($err, "UTF-8", "auto");//自动转码
			$handle = fopen($errdir, "w");
			if (!$handle){
				$this->error("".$err."","__URL__");
			}
			if (!fwrite($handle, $err)){
				$this->error("".$err."","__URL__");
			}
			fclose($handle);
		}

		echo "备份成功!";
		exit;
	}

	public function DBDoZip(){
		set_time_limit(0);
		$path = $_GET['mname'];
		$C_path = $this->Check_C_config();
		import ( "@.ORG.KuoZhan" );  //导入扩展类
        $KuoZhan = new KuoZhan();
		if ($KuoZhan->is_utf8($path) == true){
            $path = iconv('UTF-8','GB2312',$path);
        }
		if(empty($path)){
			$this->error('参数错误！');
			exit;
		}
		if(strstr($path,"..")){
			$this->error('参数错误！');
			exit;
		}

	    $now_path = $_SERVER["DOCUMENT_ROOT"];
		$web_path = __ROOT__.'/';
//		$web_path = substr($web_path,1);
		$bak_path = $C_path[1]."/";
		$all_path = $now_path.$web_path.$bak_path.$path;
		if(!file_exists($all_path))
		{
			$this->error('路径错误！');
			exit;
		}
		$zipname=$path.".zip";
		$this->ZipFile($path,$zipname);

		$gourl = __URL__."/DownZip/f/".$zipname."/p/".$path;
		$backulr = __URL__;
		echo"<script>window.location='".$gourl."';</script>";

	}

	public function DownZip(){

		$C_path = $this->Check_C_config();
		$file_name = $_GET['f'];
		$file_name = str_replace("/","",$file_name);
		$file_name = str_replace("\\","",$file_name);
		$file_name = str_replace("..","",$file_name);
		if(empty($file_name)){
			$this->error('路径错误！');
			exit;
		}
		$downfile = $C_path[2]."/".$file_name;

		$now_path = $_SERVER["DOCUMENT_ROOT"];
		$web_path = __ROOT__.'/';
//		$web_path = substr($web_path,1);
		$all_path = $now_path.$web_path.$downfile;
		if(!$handle=fopen($all_path,'rw')){
			$this->error('文件找不到，或文件正在使用中，无法打开缓存文件.');
			exit;
		}else{
			// 输入文件标签
			Header("Content-type: application/octet-stream");
			Header("Accept-Ranges: bytes");
			Header("Accept-Length: ".filesize($all_path));
			Header("Content-Disposition: attachment; filename=" . $file_name);
			// 输出文件内容
			echo fread($handle,filesize($all_path));
			fclose($handle);
		}

	}

	//清空数据库
	function DBQk(){
		$Tlist=$this->getTables();
		foreach ($Tlist as $value) {
			$sql="DROP TABLE `$value`";
			$re=M()->query($sql);
		}
	}

	//获取所有表名称
	function getTables(){
		$database=C('DB_NAME');	// 数据库名
		$sql="SHOW TABLES FROM `$database`";
		$re=M()->query($sql);

		$arr=array();
		foreach ($re as $v) {
			$arr[]=$v["Tables_in_$database"];
		}
		return $arr;
	}

	//清空数据库
	function getDBQk($ctdir="./DB",&$err){
		$Tlist=$this->getTables();
		foreach ($Tlist as $value) {

			$sql = "DROP TABLE `$value`";
			$str = "-- 清空表的: $v --\r\n";
			$data .= $str.$sql.";\r\n-- <fen> --\r\n";

		}

		$ctname = "a drop tables";
		$this->createtable_txt($ctdir,$ctname,$data,$err);
	}

	//表的结构
	function getField($ctdir="./DB",&$err){
		$list=$this->getTables();
		$arr=array();
		$data = "";
		foreach ($list as $v) {
			$sql="SHOW CREATE TABLE `$v`";
			$re=M()->query($sql);

			$str="-- 表的结构: $v --\r\n";
			$data .= $str.$re[0]['Create Table'].";\r\n-- <fen> --\r\n";

		}
		$ctname = "create tables";
		$this->createtable_txt($ctdir,$ctname,$data,$err);
	}

	//表的数据
	function getData($ctdir="./DB",&$err){
		ob_start(); //打开输出缓冲区
		ob_end_flush();
		ob_implicit_flush(1); //立即输出
		$list=$this->getTables();
		$maxt = count($list);
		$ccc = 0;
		foreach ($list as $v) {
			$n = $ccc+1;
			$cnsql = "select count(0) as tp from `$v`";
			$str="-- 表的数据: $v --\r\n";
			$tb_rsc = M()->query($cnsql);//总页数
			$tablerows = $tb_rsc[0]['tp'];
			$wanlen=$tablerows;
			$ti = 0;
			$titt = $ti+1;
			echo str_repeat(" ",4096); //确保足够的字符
			echo '<script language="javascript">'.
			'window.parent.checkpresent("'.$n.'","'.$maxt.'","'.$titt.'","'.$wanlen.'");'.
			'</script>';
	        $one_p = 1000;
	        $pagee = ceil($tablerows/$one_p);
	        for($pp=0;$pp<$pagee;$pp++){
	        	$mysql='';
	        	$s_p = $pp*$one_p;
	        	$limits = $s_p.",".$one_p;
	        	$scsql = "select * from `$v` limit ".$limits;
	        	$re = M() ->query($scsql);
				if ($re){
					for ($i = 0; $i < count($re); $i++) {
						$ti++;
						$ttt = $ti;
						$mysql.="INSERT INTO `$v` VALUES (";
						foreach ($re[$i] as $value) {
							if (gettype($value)=='string'){
								//$value=mysql_real_escape_string($value);//转义
								$mysql.="'$value',";
							}
							elseif (empty($value)) {
								$mysql.="NULL,";
							}
							else {
								$mysql.="$value,";
							}
						}
						$mysql=substr($mysql, 0, strlen($mysql)-1);//去除","
						$mysql.=");-- <fen> --\r\n";
					}
					echo '<script language="javascript">'.
					'window.parent.checkpresent("'.$n.'","'.$maxt.'","'.$ti.'","'.$wanlen.'");'.
					'</script>';
				}
				$data = $str.$mysql;
				$tb_nn = $pp+1;
				$ctname = "insert ".$v."_".$tb_nn;
				$this->createtable_txt($ctdir,$ctname,$data,$err);
	        }
			$ccc++;
			usleep(100000);//0.5秒
		}
	}

	//压缩
	function ZipFile($path,$zipname){
		@include("class/phpzip.inc.php");
		$C_path = $this->Check_C_config();
		$z=new PHPZip();
		$bak_data_path = $C_path[1];
		$bak_zip_path = $C_path[2];
		if(!is_dir($bak_zip_path)){
			mkdir($bak_zip_path, 0777);	//创建文件夹
		}
		else {
			chmod($bak_zip_path, 0777);	//改变文件模式
		}
	    $z->Zip($bak_data_path."/".$path,$bak_zip_path."/".$zipname,0); //ָĿ¼
		unset($z);
	}

	//建立文件
	function createtable_txt($ctdir="./DB",$ctname="",$data="",&$err,$type=0){

		if($type==1){
			$hz = "log";
		}else{
			$hz = "sql";
		}
		$dir=$ctdir."/".$ctname.".".$hz;
		$sql=mb_convert_encoding($data, "UTF-8", "auto");//自动转码
		$handle = fopen($dir, "w");
		if (!$handle){
			$err = "<li>打开文件".$dir."失败!</li>";
		}
		if (!fwrite($handle, $sql)){
			$err = "<li>写入文件".$dir."失败!</li>";
		}
		fclose($handle);
	}

	//完成
	public function endgookweb(){

		$tttt=20;
		for($o=0;$o<$tttt;$o++){
			usleep(100000);
		}
		$cc="操作完成";
		echo $cc;
		exit;

	}

}
?>