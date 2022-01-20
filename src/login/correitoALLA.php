<?php
//$para = 'pipo@ddddd.com.ar';
//$paraN = "Pipo Perex";
//$motivo = "1 2 3 probando";
//$cuerpo = <<<'EOT'
//a ver que bonda <br> 
//https://www.eventleaf.com/ReunionCITGSJ2017
//EOT;
//
//correito($para,$paraN,$motivo,$cuerpo);

function correito ($to,$toN,$subject,$body) {
	
	require_once 'phpmailer/PHPMailerAutoload.php';
		
	$results_messages = array();
	$body .="<p>Saludos!</p>".
			'<img src="cid:logocesimar" alt="CESIMAR-CONICET">';

	$mail = new PHPMailer(true);
	$mail->CharSet = 'utf-8';
	ini_set('default_charset', 'UTF-8');
	 
	class phpmailerAppException extends phpmailerException {}
	 
	try {
		//$to = 'marin@cenpat-conicet.gob.ar';
		if(!PHPMailer::validateAddress($to)) {
		  throw new phpmailerAppException("Email " . $to . " es inv&aacute;lido -- env&iacute;o cancelado!");
	}
	$mail->isSMTP();
	
	$mail->SMTPDebug  = 0;    
	$mail->Host       = "smtp.gmail.com";
	$mail->Port       = 587;    /*"465";*/
	$mail->SMTPSecure = "tls";     /*"ssl";*/
	$mail->SMTPAuth   = true;
	$mail->Username   = "grupete.mirounga@gmail.com";
	$mail->Password   = "nomeolvides";
	
		/* SOLO LOCAL */
			$mail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);	
		/* */
	
	$mail->setFrom("grupete.mirounga@gmail.com", siglaGrupe);
	//$mail->addAddress($to, "gru pe te");
	$mail->addAddress($to,$toN);
	$mail->Subject  = $subject;
	
	$mail->AddEmbeddedImage("figus/logocesimar.PNG",'logocesimar');

	$mail->WordWrap = 78;
	$mail->msgHTML($body, dirname(__FILE__), true); //Create message bodies and embed images
	 
	try {
	  $mail->send();
	  $results_messages[] = "Mensaje enviado";
	}
	catch (phpmailerException $e) {
	  throw new phpmailerAppException('No se puede enviar email a: ' . $to. ': '.$e->getMessage());
	}
	}
	catch (phpmailerAppException $e) {
	  $results_messages[] = $e->errorMessage();
	}
	 
//	if (count($results_messages) > 0) {
//	  echo "<h2>Run results</h2>\n";
//	  echo "<ul>\n";
//	foreach ($results_messages as $result) {
//	  echo "<li>$result</li>\n";
//	}
//	echo "</ul>\n";
//	}
	
	return $results_messages;
}
?>