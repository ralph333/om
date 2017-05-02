<?php
class Sendmail
{
	public function mail_send($mailto, $title, $text)
	{
		$mail = new Mail();
		$mail->IsSMTP();
		$mail->IsHTML(true);
		$mail->Host = "smail.yoho.cn";
		$mail->SMTPAuth = true;
		$mail->Username = "om";
		$mail->Password = "yoho@9646";
		$mail->From = "om@yoho.cn";
		$mail->FromName = "om";
		$mail->AddAddress("$mailto");
		//$mail->AddReplyTo("", "");
		//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
		//$mail->IsHTML(true); // set email format to HTML //是否使用HTML格式
		$mail->Subject = $title;
		$mail->Body = $text;
		//$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
		//echo $mailto;
		//echo $title;
		//echo $text;
		if(!$mail->Send())
		{
			echo "邮件发送失败. <p>";
			echo "错误原因: " . $mail->ErrorInfo;
			return false;
		}
		return true;
		//echo "邮件发送成功";
	}
}


