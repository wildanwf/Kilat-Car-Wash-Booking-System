<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp3\htdocs\test\vendor\phpmailer/phpmailer\src\Exception.php';
require 'C:\xampp3\htdocs\test\vendor\phpmailer/phpmailer\src\PHPMailer.php';
require 'C:\xampp3\htdocs\test\vendor\phpmailer/phpmailer\src\SMTP.php';
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

//Load Composer's autoloader
require 'C:\xampp3\htdocs\kilatcarwash\vendor\autoload.php';

//Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = 0;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'wildan.wafi@gmail.com';                     //SMTP username
    $mail->Password   = 'Wildan123!';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
    $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

    //Recipients
    $mail->setFrom('wildan.wafi@gmail.com', 'KilatCarWash');
    $mail->addAddress('wildan.cstnb@gmail.com@yahoo.com', 'Wildan');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    $swap_var = array(
		"{SITE_ADDR}" => "https://www.heytuts.com",
		
		"{EMAIL_TITLE}" => "Send custom HTML emails with a PHP script!",
		"{CUSTOM_URL}" => "https://www.heytuts.com/web-dev/php/send-emails-with-php-from-localhost-with-sendmail",
		"{CUSTOM_IMG}" => "https://i1.wp.com/www.heytuts.com/wp-content/uploads/2019/05/thumbnail_Send-emails-with-php-from-localhost-with-SendMail.png",
		"{TO_NAME}" => "Wildan",
		"{TO_EMAIL}" => "wildan.cstnb@gmail.com@yahoo.com"
	);

    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Announcement : Appointment details updated';
    $mail->Body    = '
    <style>
body
{
background-color:blue;
}
</style>
<body style="color: #000; font-size: 16px; text-decoration: none; background-color: #efefef;">
		
		<div id="wrapper" style="max-width: 600px; margin: auto auto; padding: 0px;">
			
			<div id="logo" style="">
				<center><h1 style="margin: 0px;"><a href="{SITE_ADDR}" target="_blank"><img style="max-height: 100px; max-width: 600px;margin: auto auto; padding: 20px;" src="data:image/png;base64,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"></a></h1></center>
			</div>
				
			<!-- <div id="logo" style="">
				<center><h1 style="margin: 0px;"><a href="{SITE_ADDR}" target="_blank"><img style="max-height: 100px; max-width: 600px;margin: auto auto; padding: 20px;" src=""></a></h1></center>
			</div> -->

			<div id="content" style="font-size: 16px; padding: 25px; background-color: #fff;
				moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; -khtml-border-radius: 10px;
				border-color: #7667B8; border-width: 4px 1px; border-style: solid;">

				<h1 style="font-size: 22px;"><center>GoVets</center></h1>
				
				<p style="color: white;">The veterinarian of your appointment has been re-assigned due 
				</p>

				<p>Dear customer,</p>

				<p>The veterinarian of your appointment has been re-assigned due to unforeseen circumstances. Kindly login and check the updated appointment details.</p>

				<p style="color: white;">The veterinarian of your appointment has been re-assigned due 
				</p>
				</br>
				</br>
					
				<p>We are truly sorry for the inconvenience caused. Thank you for your time and consideration. See you later!</p>
				<p style="color: white;">The veterinarian of your </p>
				<p>Sincerely,</p>

				<p><i>GoVets team.</i></p>
				</br>
				<p style="color: white;">The veterinarian of your appointment has been re-assigned due 
				</p>
				<table align="center" style="">
    				<tr style="margin:0px;">
						<td style="padding-left:30px; padding-bottom:5px;"><img src="https://www.freeiconspng.com/thumbs/phone-icon/office-phone-icon--25.png" style="width:12px; height:12px;"/></td>
     					<td style="padding-left:60px; padding-bottom:5px;"><img src="https://www.vhv.rs/dpng/d/4-42555_transparent-background-white-email-icon-png-png-download.png" style="width:17px; height:12px;"/></td>
        				<td style="padding-left:50px; padding-bottom:5px;"><img src="https://www.freeiconspng.com/uploads/location-icon-24.png" style="width:25px; height:20px;"/></td>
        				<td style="padding-left:50px; padding-bottom:5px;"><img src="https://img.pngio.com/facebook-icon-png-facebook-icon-png-transparent-free-for-download-facebook-icon-black-png-1600_1600.png" style="width:20px; height:20px;"/></td>
        				<td style="padding-left:20px; padding-bottom:5px;"><img src="https://image.similarpng.com/very-thumbnail/2020/06/Black-icon-Instagram-logo-transparent-PNG.png" style="width:20px; height:20px;"/></td>
    				</tr>
						<td style="padding:0px; font-size: 12px;">+60136815848</td>
						<td style="padding-left:5px; font-size: 12px;">felinebrotherz@gmail.com</td>
						<td style="padding-left:5px; font-size: 12px;">34-1, Jalan Elektron F U16/F, Denai Alam, 40170 Shah Alam, Selangor</td>
						<td style="padding-left:5px; font-size: 12px;">vetforyoudenaialam</td>
    					<td style="padding-left:5px; font-size: 12px;">vet.for.you</td>
					<tr>
					</tr>
  				</table>
  			</div>
		</div>
		</br>
		<p style="color: white;">The veterinarian of your appointment has been re-assigned due to </p>
		<div id="footer" style="margin-bottom: 20px; padding: 0px 8px; text-align: center;">
		Copyright &copy; 2021 GoVets
		</div>
		</div>
	</body>
    ';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
