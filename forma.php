<!-- Ниже представлены несколько вариантов скриптов - с проверками и без проверок на определённые поля. Ненужное удалить -->
<!-- Скрипт с проверкой на правильность заполлнения Телефона и E-mail -->
<?php
	if (isset($_POST['yourname'])) {$yourname = $_POST['yourname'];}
	if (isset($_POST['yourephone'])) {$yourephone = $_POST['yourephone'];}
	if (isset($_POST['youremail'])) {$youremail = $_POST['youremail'];}
	if (isset($_POST['messege'])) {$messege = $_POST['messege'];}


	if ($yourname =='' or $youremail =='' or $yourephone =='' or $messege =='')     /* Проверка на пустые поля*/
		{
			echo "<div class='zapolnite'>Заполните пожалуйста все поля</div>";
		}


	else {

			if (eregi("[0-9\-\(]{2}", $yourephone))	{/* Проверка на правильность телефона */
				
				if (eregi("^[._a-zA-Z0-9-]+@[.a-zA-Z0-9-]+.[a-z]{2,6}$", $youremail)) {
					$sub="Сообщение с сайта СВАРОГ";
					$address = 'svarog@svarog-web.ru';
					$mes = "
					Имя: $yourname \n
					E-mail:  $youremail \n
					Сообщение:  $messege \n
					";
					$verify = mail($address, $sub ,$mes, "Content-type:text/plain; charset = utf-8\r\nFrom:$address");
				}
				
				else {
						echo "<div class='zapolnite'>E-mail указан неверно </div>"; /*Выводится данное сообщение, если телефон указан неверно*/
					}
					
			}
			
			else {
					echo "<div class='zapolnite'>Поле телефон может содержать<br />
					 только цифры <span>(не менее 6-ти)</span></div>"; /*Выводится данное сообщение, если телефон указан неверно*/
				}		
						
		}



	if ($verify == 'true') {
		echo "<html><head><meta http-equiv='refresh' content='3; URL=http://svarog-web.ru'/></head><body><div class='otpravleno1'>Благодарим Вас! <label>Ваше сообщение отправлено.</label></div></body></html>";
	}
	else {
		echo "<div class='neotpravleno'>Не отправлено</div>";
	}
?>






<!-- Скрипт с проверкой на правильность заполлнения E-mail 
<?php
	if (isset($_POST['yourname'])) {$yourname = $_POST['yourname'];}
	if (isset($_POST['yourephone'])) {$yourephone = $_POST['yourephone'];}
	if (isset($_POST['youremail'])) {$youremail = $_POST['youremail'];}
	if (isset($_POST['messege'])) {$messege = $_POST['messege'];}

	if ($yourname =='' or $youremail =='' or $yourephone =='')     /* Проверка на пустые поля*/
		{
		echo "<div class='zapolnite'>Заполните пожалуйста все поля</div>";
		}

	else {

		if (eregi("^[._a-zA-Z0-9-]+@[.a-zA-Z0-9-]+.[a-z]{2,6}$", $youremail))   {
				$sub="Сообщение с сайта СВАРОГ";
				$address = 'svarog@svarog-web.ru';
				$mes = "
				Имя: $yourname \n
				E-mail:  $youremail \n
				Сообщение:  $yourephone \n
				";
				$verify = mail($address, $sub ,$mes, "Content-type:text/plain; charset = utf-8\r\nFrom:$address");
			}

			
		else {
				echo "<div class='zapolnite'>E-mail указан неверно </div>"; /*Выводится данное сообщение, если телефон указан неверно*/
			}
					
		}


	if ($verify == 'true')
		{echo "<html><head><meta http-equiv='refresh' content='3; URL=http://svarog-web.ru'/></head><body><div class='otpravleno1'>Благодарим Вас! <label>Ваше сообщение отправлено.</label></div></body></html>";}
	else
		{echo "<div class='neotpravleno'>Не отправлено</div>";}
?>

-->


