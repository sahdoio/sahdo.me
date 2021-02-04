<!doctype html>

<html>
	<head>
		<title>Enviando email...</title>
		<meta charset="utf-8">

		<link rel="stylesheet" href="sendemail.css">
  		<link rel="stylesheet" href="../sweet-alert/sweet-alert.css">

	  	<script src="../js/jquery-2.1.1.js"></script>
	    <script src="../sweet-alert/sweet-alert.js"></script>
	    <script src="sendemail.js"></script>
	</head>


	<body data-spy="scroll" data-target="#main-navbar">
        <!-- Exibe imagem de carregamento enquanto pagina carrega -->
        <div class="email-sending"></div>

        <?php
			//      Formulário
			// =================================================== //

			// general
			$date                  	= date("d/m/Y - H:i");
			$name                   = $_POST['name'];
			$email                  = $_POST['email'];
			$info                   = $_POST['message'];
			$subject								= $_POST['subject'];
			// company info
			$site										= "www.onedesire.co";
			$company								= "One Desire";
			$to           		    	= "cafe@onedesire.co";
			//smtp
			$user_name							= "One Desire";
			$user										= "cafe@onedesire.co";
			$password								= "....ods2016";
			$port										= "25";
			$host 									= "onedesire.co";


			//      Email que chega até você
			// =================================================== //
			$assunto        = "Contato :: $site";
			$header         = "
			<b>name:</b>    $name<br>
			<b>Email:</b>   $email<br>
			<b>Assunto:</b> $subject<br>
			<br><br>
			<b>Mensagem:</b>
			<br><br>
			$info
			<br><br>

			==============================================<br>
			        $date <br>
			==============================================<br>
			";

			// Função HTML :)
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/html;charset=iso-8859-1\r\n";
			$headers .= "From: $name <$email>\r\n";

			//      Resposta que vai ao Cliente/Visitante
			// =================================================== //

			$resp_assunto   = "Contato :: $site";
			$header2        = "
			Olá <b>$name</b>,
			<br><br>
			Obrigado pelo seu contato.<br>
			Recebemos sua solicitação e brevemente entraremos em contato.
			<br><br><br>

			==============================================<br>
			        Atenciosamente,<br>
			        Suporte - $company<br>
			==============================================<br>
			";

			// Função HTML
			$headers2 .= "MIME-Version: 1.0\r\n";
			$headers2 .= "Content-type: text/html; charset=iso-8859-1\r\n";
			$headers2 .= "From: $company <$to>\r\n";

			// Envia to você
			//mail($to, $assunto, $header, $headers);

			//smtp begin =============================================

			if (isset($_POST['submit'])) {
				//SMTP needs accurate times, and the PHP time zone MUST be set
				//This should be done in your php.ini, but this is how to do it if you don't have access to that
				date_default_timezone_set('Etc/UTC');

				require 'php-mailer/PHPMailerAutoload.php';

				//Create a new PHPMailer instance
				$mail = new PHPMailer;
				//Tell PHPMailer to use SMTP
				$mail->isSMTP();
				//Enable SMTP debugging
				// 0 = off (for production use)
				// 1 = client messages
				// 2 = client and server messages
				$mail->SMTPDebug = 2;
				//Ask for HTML-friendly debug output
				$mail->Debugoutput = 'html';
				//Set the hostname of the mail server
				$mail->Host = $host;
				//Set the SMTP port number - likely to be 25, 465 or 587
				$mail->Port = $port;
				//Whether to use SMTP authentication
				$mail->SMTPAuth = true;
				//Username to use for SMTP authentication
				$mail->Username = $user;
				//Password to use for SMTP authentication
				$mail->Password = $password;
				//For brasilian characters
				$mail->Encoding = "base64";
				$mail->CharSet = 'UTF-8';
				//Set who the message is to be sent from
				$mail->setFrom($email, $name);
				//Set an alternative reply-to address
				$mail->addReplyTo($email, $name);
				//Set who the message is to be sent to
				$mail->addAddress($to, $user_name);
				//Set the subject line
				$mail->Subject = $assunto;
				//Read an HTML message body from an external file, convert referenced images to embedded,
				//convert HTML into a basic plain-text alternative body
				$mail->msgHTML($header);


				//send the message, check for errors
				if (!$mail->send()) {
				    echo "Mailer Error: " . $mail->ErrorInfo;
				    echo "<script>alert('Cheque seus dados e tente novamente.');</script>";
					echo "<script>window.location='../index.html'</script>";
				} else {
					// Envia para quem preencheu o form
					mail($email, $resp_assunto, $header2,$headers2);
					echo "<script>alert('Email enviado com sucesso!');</script>";
				    echo "<script>window.location='../index.html'</script>";
				}
			}
			else {
				//echo "<script>alert('Cheque seus dados e tente novamente.');</script>";
				echo "<script>window.location='../index.html'</script>";
			}

		?>
    </body>
</html>
