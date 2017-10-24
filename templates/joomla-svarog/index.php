<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.prosto-hostel
 * 
 * @copyright   Copyright (C) 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
JFactory::getDocument()->setGenerator('');
defined('_JEXEC') or die;
// Get params
$color          = $this->params->get('templatecolor');
$logo           = $this->params->get('logo');
$navposition    = $this->params->get('navposition');
$headerImage    = $this->params->get('headerImage');
$doc            = JFactory::getDocument();
$app            = JFactory::getApplication();
$templateparams	= $app->getTemplate(true)->params;
$config         = JFactory::getConfig();
$bootstrap      = explode(',', $templateparams->get('bootstrap'));
$jinput         = JFactory::getApplication()->input;
$option         = $jinput->get('option', '', 'cmd');
if (in_array($option, $bootstrap))
{
	// Load optional rtl Bootstrap css and Bootstrap bugfixes
	JHtml::_('bootstrap.loadCss', true, $this->direction);
}
$doc->addStyleSheet(JUri::base() . 'templates/system/css/system.css');
$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/template.css', $type = 'text/css', $media = 'all');
$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/media.css', $type = 'text/css', $media = 'all');
$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/nature.css', $type = 'text/css', $media = 'screen,projection');
$doc->addStyleSheet(JUri::base() . 'templates/' . $this->template . '/css/' . htmlspecialchars($color) . '.css', $type = 'text/css', $media = 'screen,projection');

if ($this->direction == 'rtl')
{
	$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/template_rtl.css');
	if (file_exists(JPATH_SITE . '/templates/' . $this->template . '/css/' . $color . '_rtl.css'))
	{
		$doc->addStyleSheet($this->baseurl . '/templates/' . $this->template . '/css/' . htmlspecialchars($color) . '_rtl.css');
	}
}
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" >

	
<head>
	<?php require __DIR__ . '/jsstrings.php';?>

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=yes"/>
	<meta name="HandheldFriendly" content="true" />
	<meta name="apple-mobile-web-app-capable" content="YES" />

	<jdoc:include type="head" />

	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,600,700i,800" rel="stylesheet">
	<!--[if IE 7]>
	<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template; ?>/css/ie7only.css" rel="stylesheet" type="text/css" />
	<![endif]-->

	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/animate.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/workspace.css" type="text/css"><!-- После окночначиня работ удалить эту строчку -->
</head>



<body class="big-monitor">

	<!--  Start header -->
	<header>
		<div class="header-inside">

			<?php 
				$imja="https://".$_SERVER['SERVER_NAME']."/"; $puti="https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
				if ($imja==$puti) {
					$href_main='';
				}
				else {
					$href_main='href="'.$this->baseurl.'"';
				}
			?>


			<?php if ($this->countModules('position-0')) : ?>
				<jdoc:include type="modules" name="position-0" style="xhtml" />
			<?php endif; ?>

			<?php if ($this->countModules('position-1')) : ?>
				<nav>
					<div class="block-menu fixed-absolut">
						<jdoc:include type="modules" name="position-1" style="xhtml" />
					</div>
				</nav>
			<?php endif; ?>

			<?php if ($this->countModules('lang')) : ?>
				<jdoc:include type="modules" name="lang" style="xhtml" />
			<?php endif; ?>
		
		</div>
	</header>
	<!-- Stop header -->

	<!--  Start main -->
	<main>
		<div class="main-inside" id="ex1">
			
			<!-- Start sidebar -->
			<div class="sidebar">
				<?php if ($this->countModules('position-2')) : ?>
					<jdoc:include type="modules" name="position-2" style="xhtml" />
				<?php endif; ?>

									
				<?php if ($this->countModules('position-3')) : ?>
					<jdoc:include type="modules" name="position-3" style="xhtml" />
				<?php endif; ?>

				
				<?php if ($this->countModules('position-4') or $this->countModules('position-5')) : ?>
					<jdoc:include type="modules" name="position-4" style="xhtml" />
					<jdoc:include type="modules" name="position-5" style="xhtml" />
				<?php endif; ?>
			</div>
			<!-- End sidebar -->


			<div class="main-block">

				<?php if ($this->countModules('position-6')) : ?>
					<jdoc:include type="modules" name="position-6" style="xhtml" />
				<?php endif; ?>

				<?php if ($this->countModules('position-7')) : ?>
					<jdoc:include type="modules" name="position-7" style="xhtml" />
				<?php endif; ?>

				<jdoc:include type="message" />
				<jdoc:include type="component" />

				<?php if ($this->countModules('position-8')) : ?>
					<jdoc:include type="modules" name="position-8" style="xhtml" />
				<?php endif; ?>

				<?php if ($this->countModules('position-9')) : ?>
					<jdoc:include type="modules" name="position-9" style="xhtml" />
				<?php endif; ?>

				<?php if ($this->countModules('position-10')) : ?>
					<jdoc:include type="modules" name="position-10" style="xhtml" />
				<?php endif; ?>

				<?php if ($this->countModules('position-11')) : ?>
					<jdoc:include type="modules" name="position-11" style="xhtml" />
				<?php endif; ?>

				<?php if ($this->countModules('position-12')) : ?>
					<jdoc:include type="modules" name="position-12" style="xhtml"/>
				<?php endif; ?>

				<?php if ($this->countModules('position-13')) : ?>
					<jdoc:include type="modules" name="position-13" style="xhtml"/>
				<?php endif; ?>

				<?php if ($this->countModules('position-14')) : ?>
					<jdoc:include type="modules" name="position-14" style="xhtml"/>
				<?php endif; ?>

				<jdoc:include type="modules" name="debug" />

			</div>
		
		</div>
	</main>
	<!-- Stop main -->

	<!--  Start footer -->
	<footer id="ex2">
		<div class="footer">
			<div class="butt-yandex"></div>
			<div class="yandex-counter">
				<!-- Yandex.Metrika informer -->
				
				<!-- /Yandex.Metrika informer -->
			</div>
			<div class="footer-inside">
				<?php if ($this->countModules('footer')) : ?>
					<jdoc:include type="modules" name="footer" style="xhtml"/>
				<?php endif; ?>
			</div>
		</div>
	</footer>
	<!-- Stop footer -->

	<a href="#" id="Go_Top"></a>
	<a href='#' id="Go_Bottom"></a>

	<!-- Start modal-registration -->
	<div class="b-popup" id="popup">
		<a href="javascript:PopUpHide()" class="close">X</a>
		<form id="myform" action="javascript:send('<?php echo $this->baseurl ?>/forma.php','myform','result');" method="post" class="modal-form">
			<input name="yourname" type="text" placeholder="Ваше имя" onblur="if (this.placeholder=='') this.placeholder='Ваше имя';" onfocus="if (this.placeholder=='Ваше имя') this.placeholder='';">
			<input name="yourephone" id="phone" type="text" placeholder="Ваш телефон" onblur="if (this.placeholder=='') this.placeholder='Ваш телефон';" onfocus="if (this.placeholder=='Ваш телефон') this.placeholder='';">
			<input name="youremail" type="text" placeholder="Ваш E-mail" onblur="if (this.placeholder=='') this.placeholder='Ваш E-mail';" onfocus="if (this.placeholder=='Ваш E-mail') this.placeholder='';">
			<input name="password" type="password" placeholder="Пароль" onblur="if (this.placeholder=='') this.placeholder='Пароль';" onfocus="if (this.placeholder=='Пароль') this.placeholder='';">
			<textarea class="messege" name="messege" placeholder="Сообщение" onblur="if (this.placeholder=='') this.placeholder='Сообщение';" onfocus="if (this.placeholder=='Сообщение') this.placeholder='';"></textarea>	

			<div class="butt">									
				<div onclick="chDiv()">
					<input type="submit" onclick="send('myform','result');" value="Отправить">
				</div>
				<div id="result" class="resi"></div>
			</div>
		</form>
	</div>
	<!-- End modal-registration -->

	<div class="dark"></div>


	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascript/template.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascript/maskedinput.js"></script>
	<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascript/jquery.validate.js"></script>





















	<!-- -------------------- Start workspace -------------------- -->
	<div class="workspace">

		<div class="workspace-line"></div>
		<div class="scissors"></div>

		<h5>Рабочая область</h5>

		<div class="workspace-inside">


			<!-- Start block 'Табы на css' -->
			<div class="block-workspace">
				<h6>Табы на css</h6>
				<section class="tabs">

					<input id="tab_1" type="radio" name="tab" checked="checked"/>
					<input id="tab_2" type="radio" name="tab"/>
					<input id="tab_3" type="radio" name="tab"/>

					<label for="tab_1" id="tab_l1">Таб 1</label>
					<label for="tab_2" id="tab_l2">Таб 2</label>
					<label for="tab_3" id="tab_l3">Таб 3</label>
					<div style="clear:both"></div>

					<div class="tabs_cont">
						<div id="tab_c1" class="deposit">
							Текст 1-го таба
						</div>

						<div id="tab_c2" class="deposit">
							Текст 2-го таба
						</div>

						<div id="tab_c3">
							Текст 3-го таба
						</div>
					</div>

				</section>
			</div>
			<!-- Stop block 'Табы на css' -->

			<!-- Start block 'Кнопка "Заказать звонок"' -->
			<div class="block-workspace">
				<h6>Кнопка "Заказать звонок"</h6>

				<a href="javascript:PopUpShow()">Заказать звонок</a>
			</div>
			<!-- Stop block 'Кнопка "Заказать звонок"' -->

			<!-- Start block 'Выпадающий список' -->
			<div class="block-workspace">
				<h6>Выпадающий список</h6>

				<div class="select">
					<form action="#" method="post" >

						<div class="select">
							<a href="#" class="slct">Успешные</a>
							<a class="slct-2"></a>
							<ul class="drop">
								<li>Успешные</li>
								<li>Отклонённые</li>
								<li>Закрытые</li>
								<li>На рассмотрении</li>
							</ul>
							<input type="hidden" />
						</div>		
						
					</form>
				</div>
			</div>
			<!-- Stop block 'Выпадающий список' -->


			<!-- Start block 'Форма с валидацией полей через js' -->
			<div class="block-workspace">
				<h6>Форма с валидацией полей через js</h6>

				<form id="myform-two" action="javascript:send('forma-two.php','myform-two','result-two');" method="post" class="modal-form">
					<input name="number" id="number" type="text" placeholder="Номер заказа" onblur="if (this.placeholder=='') this.placeholder='Номер заказа';" onfocus="if (this.placeholder=='Номер заказа') this.placeholder='';">
					<input name="yournameTwo" id="yournameTwo" type="text" placeholder="Ваше имя" onblur="if (this.placeholder=='') this.placeholder='Ваше имя';" onfocus="if (this.placeholder=='Ваше имя') this.placeholder='';">
					<input name="yourephoneTwo" id="yourephoneTwo" type="text" placeholder="Ваш E-mail" onblur="if (this.placeholder=='') this.placeholder='Ваш E-mail';" onfocus="if (this.placeholder=='Ваш E-mail') this.placeholder='';">

					<div class="butt">									
						<input type="submit" onclick="send('myform-two','result-two');" value="Отправить">
						<div id="result-two" class="resi"></div>
					</div>
				</form>

				<script type="text/javascript">
					jQuery(document).ready(function(){
						
						jQuery("#myform-two").validate({
							rules: {
								number: {
									required: true,
									digits: true
								},
								yournameTwo: {
									required: true,
								},
								yourephoneTwo: {
									required: true,
									email: true
								}
							},

							messages: {
								number: {
									required: "Введите номер заказа",
									digits: "Укажите числовое значение"
								},
								yournameTwo: {
									required: "Укажите имя"
								},
							  	yourephoneTwo: {
									required: "Укажите сумму заказа",
									email: "E-mail введён неверно"
							  	}
							}
						});
					});
				</script>

			</div>
			<!-- Stop block 'Форма с валидацией полей через js' -->


			<!-- Start block 'Анимация на css' -->
			<div class="block-workspace">
				
				<h6>Анимация на css</h6>

				<div id="animationSandbox">
					Блок с анимацией
				</div>

				<form>
					<select class="input input--dropdown js--animations">

						<optgroup label="Attention Seekers">
							<option value="bounce">bounce</option>
							<option value="flash">flash</option>
							<option value="pulse">pulse</option>
							<option value="rubberBand">rubberBand</option>
							<option value="shake">shake</option>
							<option value="swing">swing</option>
							<option value="tada">tada</option>
							<option value="wobble">wobble</option>
							<option value="jello">jello</option>
						</optgroup>

						<optgroup label="Bouncing Entrances">
							<option value="bounceIn">bounceIn</option>
							<option value="bounceInDown">bounceInDown</option>
							<option value="bounceInLeft">bounceInLeft</option>
							<option value="bounceInRight">bounceInRight</option>
							<option value="bounceInUp">bounceInUp</option>
						</optgroup>

						<optgroup label="Bouncing Exits">
							<option value="bounceOut">bounceOut</option>
							<option value="bounceOutDown">bounceOutDown</option>
							<option value="bounceOutLeft">bounceOutLeft</option>
							<option value="bounceOutRight">bounceOutRight</option>
							<option value="bounceOutUp">bounceOutUp</option>
						</optgroup>

						<optgroup label="Fading Entrances">
							<option value="fadeIn">fadeIn</option>
							<option value="fadeInDown">fadeInDown</option>
							<option value="fadeInDownBig">fadeInDownBig</option>
							<option value="fadeInLeft">fadeInLeft</option>
							<option value="fadeInLeftBig">fadeInLeftBig</option>
							<option value="fadeInRight">fadeInRight</option>
							<option value="fadeInRightBig">fadeInRightBig</option>
							<option value="fadeInUp">fadeInUp</option>
							<option value="fadeInUpBig">fadeInUpBig</option>
						</optgroup>

						<optgroup label="Fading Exits">
							<option value="fadeOut">fadeOut</option>
							<option value="fadeOutDown">fadeOutDown</option>
							<option value="fadeOutDownBig">fadeOutDownBig</option>
							<option value="fadeOutLeft">fadeOutLeft</option>
							<option value="fadeOutLeftBig">fadeOutLeftBig</option>
							<option value="fadeOutRight">fadeOutRight</option>
							<option value="fadeOutRightBig">fadeOutRightBig</option>
							<option value="fadeOutUp">fadeOutUp</option>
							<option value="fadeOutUpBig">fadeOutUpBig</option>
						</optgroup>

						<optgroup label="Flippers">
							<option value="flip">flip</option>
							<option value="flipInX">flipInX</option>
							<option value="flipInY">flipInY</option>
							<option value="flipOutX">flipOutX</option>
							<option value="flipOutY">flipOutY</option>
						</optgroup>

						<optgroup label="Lightspeed">
							<option value="lightSpeedIn">lightSpeedIn</option>
							<option value="lightSpeedOut">lightSpeedOut</option>
						</optgroup>

						<optgroup label="Rotating Entrances">
							<option value="rotateIn">rotateIn</option>
							<option value="rotateInDownLeft">rotateInDownLeft</option>
							<option value="rotateInDownRight">rotateInDownRight</option>
							<option value="rotateInUpLeft">rotateInUpLeft</option>
							<option value="rotateInUpRight">rotateInUpRight</option>
						</optgroup>

						<optgroup label="Rotating Exits">
							<option value="rotateOut">rotateOut</option>
							<option value="rotateOutDownLeft">rotateOutDownLeft</option>
							<option value="rotateOutDownRight">rotateOutDownRight</option>
							<option value="rotateOutUpLeft">rotateOutUpLeft</option>
							<option value="rotateOutUpRight">rotateOutUpRight</option>
						</optgroup>

						<optgroup label="Sliding Entrances">
							<option value="slideInUp">slideInUp</option>
							<option value="slideInDown">slideInDown</option>
							<option value="slideInLeft">slideInLeft</option>
							<option value="slideInRight">slideInRight</option>
						</optgroup>

						<optgroup label="Sliding Exits">
							<option value="slideOutUp">slideOutUp</option>
							<option value="slideOutDown">slideOutDown</option>
							<option value="slideOutLeft">slideOutLeft</option>
							<option value="slideOutRight">slideOutRight</option>
						</optgroup>

						<optgroup label="Zoom Entrances">
							<option value="zoomIn">zoomIn</option>
							<option value="zoomInDown">zoomInDown</option>
							<option value="zoomInLeft">zoomInLeft</option>
							<option value="zoomInRight">zoomInRight</option>
							<option value="zoomInUp">zoomInUp</option>
						</optgroup>

						<optgroup label="Zoom Exits">
							<option value="zoomOut">zoomOut</option>
							<option value="zoomOutDown">zoomOutDown</option>
							<option value="zoomOutLeft">zoomOutLeft</option>
							<option value="zoomOutRight">zoomOutRight</option>
							<option value="zoomOutUp">zoomOutUp</option>
						</optgroup>

						<optgroup label="Specials">
							<option value="hinge">hinge</option>
							<option value="rollIn">rollIn</option>
							<option value="rollOut">rollOut</option>
						</optgroup>
					</select>

					<button class="butt js--triggerAnimation">Animate it</button>
				</form>

				<br><br>
				<div class="workspace-description">
					Для того, что бы на блок навесить эффект - необходимо данному блоку прописать два класса:<br>
					<b>1-ый:</b> animated<br>
					<b>2-ой:</b> 'название эффекта'<br>
					(При написании классов важно придерживаться регистра)<br><br>
					<i>Например:</i><br>
					<xmp>
						<div class="animated wobble">Блок с анимацией</div>
					</xmp>
				</div>

				<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/javascript/workspace.js"></script>

			</div>
			<!-- Stop block 'Анимация на css' -->



		</div>
	</div>
	<!-- End workspace -->

		
</body>
</html>