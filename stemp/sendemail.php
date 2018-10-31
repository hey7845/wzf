<?php
require_once "../stemp/class.phpmailer.php";
require_once "../stemp/class.smtp.php";
function send_email($username,$useremail,$userid)
{
	$mail = new PHPMailer();
	//$address = $_POST['address'];
	//$address = "119515301@qq.com";
	$mail    = new PHPMailer();  
	//$mail->IsSMTP();      IsSendmail            // send via SMTP  
	$mail->IsSMTP();                  // send via SMTP  
	$mail->Host  = "smtp.163.com";   // SMTP servers  
	$mail->SMTPAuth = true;           // turn on SMTP authentication  
	$mail->Username = "chenfu1211";     // SMTP username     注意：普通邮件认证不需要加 @域名 
	$mail->Password = "27301450";          // SMTP password  
	$mail->From  = "chenfu1211@163.com";        // 发件人邮箱 
	$mail->FromName =  "ING Office";    // 发件人 
	$mail->CharSet  = "utf-8";              // 这里指定字符集！ 
	$mail->Encoding = "base64"; 
	$mail->AddAddress("".$useremail."","".$useremail."");    // 收件人邮箱和姓名
	//$mail->AddAddress("119515301@qq.com","text");    // 收件人邮箱和姓名
	$mail->AddReplyTo("".$useremail."","163.com"); 
	$mail->IsHTML(true);    // send as HTML             
	$mail->Subject  = '感谢您使用ING Office 密码找回'; // 邮件主题 
		$body="<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"font-size:12px; line-height:24px;\">";
		$body=$body."<tr>";
		$body=$body."<td height=\"30\">你的新密码是:".$username."</td>";
		$body=$body."</tr>";
		$body=$body."此邮件由系统发出，请勿直接回复。<br>";
		$body=$body."</td></tr>";
		$body=$body."<tr>";
		$body=$body."<td height=\"30\" align=\"right\">".date("Y-m-d H:i:s")."</td>";
		$body=$body."</tr>";
		$body=$body."</table>";
	$mail->Body = "".$body."";// 邮件内容	
	$mail->AltBody ="text/html";  
	$mail->Send();
	
	if(!$mail->Send())
	{
	echo "Message could not be sent. <p>";
	echo "Mailer Error: " . $mail->ErrorInfo;
	exit;
	}
	//echo "Message has been sent";
}

send_email("CF","chenfu1211@qq.com","3");
?>

