<?php
	if (isset($_POST['yournameTwo'])) {$yournameTwo = $_POST['yournameTwo'];}
	if (isset($_POST['yourephoneTwo'])) {$yourephoneTwo = $_POST['yourephoneTwo'];}
				
	$sub="Сообщение №2 с сайта СВАРОГ";
	$address = 'svarog@svarog-web.ru';
	$mes = "
	Имя: $yournameTwo \n
	E-mail:  $yourephoneTwo \n
	";
	$verify = mail($address, $sub ,$mes, "Content-type:text/plain; charset = utf-8\r\nFrom:$address");

	if ($verify == 'true') {
		echo "<html><head><meta http-equiv='refresh' content='3; URL=http://svarog-web.ru'/></head><body><div class='otpravleno1'>Благодарим Вас! <label>Ваше сообщение отправлено.</label></div></body></html>";
	}

?>