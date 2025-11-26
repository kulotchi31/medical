<?php
	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	require 'PHPMailer/Exception.php';
	require 'PHPMailer/PHPMailer.php';
	require 'PHPMailer/SMTP.php';
		
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'pmsneust2024@gmail.com';
        $mail->Password = 'osmgjidjlaumnskx';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;
        $mail->setFrom('pmsneust2024@gmail.com', 'Procurement Management System');
		$mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'NEW USER CREDENTIALS';


        $mail->Body = '
        *** PLEASE DO NOT REPLY OR SEND MESSAGE TO THIS EMAIL *** <br><br>
        
        Good day ' . htmlspecialchars($name) . ',<br><br>
        
        Below are your login credentials:<br>
        Email: ' . htmlspecialchars($email) . '<br>
        Password: ' . htmlspecialchars($Password) . '<br><br>
        
        Click the link below to log in:<br>
        
        Please do not share this link with anyone.<br><br>
        <a href="http://192.168.50.27:444/pms/index?Email=' . urlencode($email) . '&Password=' . urlencode($Password) . '&loginauth=">Login</a><br><br>
        Thank you.<br><br>
        
       Procurement Management System
        ';
        
	
        
        try {
            $mail->send();
			echo '';
			echo '';
			
        } catch (Exception $e) {
            echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
        }

$conn->close();

?>
