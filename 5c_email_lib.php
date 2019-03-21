<?php

//header('Access-Control-Request-Method: *');
//header('Access-Control-Allow-Origin:'.$_SERVER['HTTP_ORIGIN']);

function send_message($SendMailFrom, $SendMailTo, $SMTPHost, $SMTPPort, $SMTPUser, $SMTPPassword, $SMTPSecure, $SMTPDebug, $subject, $body) {

	$result='1';

	if( !isset($subject) ) $subject='';
	if( !isset($body) ) $body='';


	# Send message
	require_once('class.phpmailer.php');
	require_once('class.smtp.php');

	try{
		$mail=new PHPMailer(true);
		$mail->IsSMTP();

		$mail->SMTPAuth = true;                 
		$mail->Host = $SMTPHost;
		$mail->SMTPSecure = $SMTPSecure;
		$mail->SMTPDebug = $SMTPDebug;
		$mail->Port = $SMTPPort;	
		$mail->Username = $SMTPUser;
		$mail->Password = $SMTPPassword;            
		 
		$mail->From = $SendMailFrom;
		$mail->FromName = $SendMailFrom;

		$mail->Subject = $subject;

		$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
		$mail->CharSet = 'UTF-8';
		$mail->MsgHTML($body);

		if( is_array($SendMailTo) ) {
			foreach($SendMailTo as $CurrentAddressKey => $CurrentAddressValue) {
				$mail->AddAddress($CurrentAddressValue);
			}
		}
		elseif( is_string($SendMailTo) ) {
			$mail->AddAddress($SendMailTo);
		}

		$mail->Send();
		$result='0';		
	}
	
	catch(Exception $e){
		$result=($e->getMessage());
	}

	return($result);
}

?>
