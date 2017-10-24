/**
 * @package     Joomla.Site
 * @subpackage  Templates.protostar
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @since       3.2
 */
//В данном файле содержиться множество скриптов применяемых в шаблоне элементов. По окончанию работ ненужные скрипты можно удалить. 
//Все скрипты в самом начале имеют комментарий для чего нужен данный скрипт.


//Скрипт проверки на мобильность устройства
var isMobile = {
	Android: function() {
		return navigator.userAgent.match(/Android/i);
	},
	BlackBerry: function() {
		return navigator.userAgent.match(/BlackBerry/i);
	},
	iOS: function() {
		return navigator.userAgent.match(/iPhone|iPad|iPod/i);
	},
	Opera: function() {
		return navigator.userAgent.match(/Opera Mini/i);
	},
	Windows: function() {
		return navigator.userAgent.match(/IEMobile/i);
	},
	any: function() {
		return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
	}
};

if(isMobile.any()){/* Если устройство мобильно, то выполняем одно действие */
	jQuery("body").removeClass("big-monitor");
	jQuery("body").addClass("body-mobil");
}

jQuery(window).resize(function(){
	var mbWidth = jQuery(document).outerWidth(true);
	if (mbWidth <=768) {
		jQuery("body").removeClass("big-monitor");
		jQuery("body").addClass("body-mobil");			
	}
	else if (mbWidth >768) {
		jQuery("body").addClass("big-monitor");
		jQuery("body").removeClass("body-mobil");			
	}
});

jQuery(document).ready(function() {
	var mbWidth = jQuery(document).outerWidth(true);
	if (mbWidth <=768) {
		jQuery("body").removeClass("big-monitor");
		jQuery("body").addClass("body-mobil");			
	}
});




jQuery(document).ready(function(){

	/*---------- Скрипт адаптивного меню ----------*/
	jQuery(".block-menu").prepend("<div class='menu-icon'><span class='mi_layer'></span><span class='icon-m icon1'></span><span class='icon-m icon2'></span><span class='icon-m icon3'></span></div>");
	jQuery(".block-menu").after("<div class='bg_menu'></div>");

	//Разворачивание меню при наведении на иконку мышью
	jQuery(".menu-icon").on("mouseenter touchstart", function (event) {
		jQuery("ul.nav-child").css("display", "none");
		jQuery(this).addClass("mi-active");
		jQuery(this).removeClass("menu-icon");
		setTimeout(function () {
			jQuery(".moduletable_menu").addClass("bmi-active bmi-width");
			jQuery(".bg_menu").addClass("bg_menu-active");
		}, 1);
		jQuery(".icon-m").addClass("active");
		jQuery("body").addClass("overflow");
		jQuery(document).bind("touchmove", false);
		return false;
	});

	function collapse_menu() {
		jQuery(".mi-active").addClass("menu-icon");
		jQuery(".mi-active").removeClass("mi-active");
		jQuery(".icon-m").removeClass("active");
		jQuery(".moduletable_menu").removeClass("bmi-active");
		jQuery(".bg_menu").removeClass("bg_menu-active");
		jQuery("body").removeClass("overflow");
		jQuery(document).unbind("touchmove", false);
		return false;
	}

	//Сворачивание меню при клике на фон и по закрывающей кнопке
	jQuery(".bg_menu, .mi_layer").on("click touchstart", collapse_menu);
	//Сворачивание меню при уводе мыши с области документа (браузера)
	jQuery("body").on("mouseleave", collapse_menu);
	

	//Убираем некоторые стили при растягивание браузера (при изменении разрешения)
	jQuery(window).resize(function(){
		var bmiWidth = jQuery(document).outerWidth(true);
		if (bmiWidth >=100) {//По сути значение равное любому маленькому числу, но можно указывать значение равное минимальному разрешению экрана либо же максимальному max-width в файле адаптации стилей минус (-) ширина вертикальной полосы прокрутки (в стандартном исполнении = 18px). 
			jQuery(".mi-active").addClass("menu-icon");
			jQuery(".mi-active").removeClass("mi-active");
			jQuery(".icon-m").removeClass("active");
			jQuery(".moduletable_menu").removeClass("bmi-active bmi-width");
			jQuery(".bg_menu").removeClass("bg_menu-active");
			jQuery("body").removeClass("overflow");
			jQuery(document).unbind("touchmove", false);
			jQuery("ul.nav-child").css("display", "block");
		}
	});

	//Аккордеон
	jQuery(".parent a, .parent .separator, .parent .nav-header").click(function(){
		jQuery(".parent .nav-child").slideUp(500);
		if(jQuery(this).parent(".parent").find(".nav-child").is(":visible")) {
			jQuery(this).parent(".parent").find(".nav-child").slideUp(500);
		}
		else {
			jQuery(this).parent(".parent").find(".nav-child").slideDown(500);
		}
	});
	/*---------- Конец скрипта адаптивного меню ----------*/

	/*---------- Скрипт фиксации верхнего меню при прокрутке страницы ----------*/
	jQuery(window).scroll(function(){
		if (jQuery(this).scrollTop() > 350) {
			jQuery(".block-menu.fixed-absolut").addClass("fixed");
			jQuery(".block-menu").removeClass("fixed-absolut");

		} else if (jQuery(this).scrollTop() < 1){
			jQuery(".block-menu").addClass("fixed-absolut");
			jQuery(".block-menu").removeClass("fixed");
		}
	});
	/*---------- Конец скрипта фиксации верхнего меню при прокрутке страницы ----------*/


});




//---------- Скрпит оформления select (выпадающего списка)
//Код с тремя строками ниже нужен для того, что бы при загрузке нашего выпадающего списка через ajax он работал корректно
jQuery(document).ready(function(){
	viewSelects();
});


function viewSelects(){
	// Select
	jQuery('.slct').click(function(){
		/* Заносим выпадающий список в переменную */
		var dropBlock = jQuery(this).parent().find('.drop');


		/* Делаем проверку: Если выпадающий блок скрыт то делаем его видимым*/
		if(dropBlock.is(':hidden')) {

			var dropBlockOut = jQuery('.drop');
			if (dropBlock!=dropBlockOut) {
				jQuery('.slct-2').css('display', 'none');
				jQuery('.drop').slideUp();
			}

			dropBlock.slideDown();
			jQuery(this).siblings(".slct-2").css('display', 'block');

			jQuery('.slct-search').css('display', 'block');
			/* Выделяем ссылку открывающую select */
			jQuery(this).addClass('active');

			jQuery('.drop').find('li').click(function(pageCounterChanged){
				var selectResult = jQuery(this).html();
				var selectResultId = jQuery(this).attr('data-id');

				jQuery(this).parent().parent().find('input').val(selectResult);
				jQuery(this).parent().parent().find('input').attr('data-id', selectResultId);

				jQuery(this).parent().siblings(".slct").removeClass('active').html(selectResult);
				
				jQuery(this).parent().slideUp();
				jQuery(this).parent().siblings(".slct-2").css('display', 'none');

				var elid = jQuery(this).parent().parent().find('input').attr('id');

				jQuery(function() {
					if (typeof window[elid+"Changed"] == 'function'){
						window[elid+"Changed"]();
					}
				});

			});

			/* Продолжаем проверку: Если выпадающий блок не скрыт то скрываем его */
		} else {
					jQuery(this).removeClass('active');
				}

		/* Предотвращаем обычное поведение ссылки при клике */
		return false;
	});


	jQuery('.slct-2').click(function(){ 
		jQuery(this).css('display', 'none');
		jQuery(this).siblings('.drop').slideUp();
		jQuery(this).siblings(".slct").removeClass('active');			
	});

}

//Код ниже необходим для скрытия выпадающих списков при клике вне области списков
jQuery(document).click( function(event){
	if( jQuery(event.target).closest(".drop").length ) 
		return;
	jQuery(".drop").slideUp("slow");
	jQuery('.slct-2').css('display', 'none');
	jQuery('.slct').removeClass('active');
	event.stopPropagation();
});

//----------Конец скрипта выпадающего списка


//Скрипт показывающий счётчик яндекса в подвале при клике в левом нижнем углу сайта
jQuery(".butt-yandex").click(function(){
	jQuery(".yandex-counter").toggleClass("yc-active");
});


//Скрипты открытия и закрытия модальных окон, а так же скрипты применяющиеся в этих окнах	
//.fadeOut(300); - Скорость закрытия окна и фона
//Функция отображения PopUp
/*-----*/
function PopUpShow(){
	jQuery(".dark").fadeIn();
	jQuery("#popup").fadeIn();
	jQuery("header, main, footer").addClass("blurriness");
	jQuery("#popup2, #popup3").fadeOut(300);
}



//Функция скрытия PopUp при нажатии на закрывающую область
function PopUpHide(){
	jQuery(".dark").fadeOut(300);
	jQuery("#popup").fadeOut(300);
	jQuery("header, main, footer").removeClass("blurriness");
}
/*-----*/



//Функция скрытия PopUp при нажатии на область вне модального окна
jQuery(document).ready(function(){
	jQuery(".dark").click(function(){
		jQuery(".dark").fadeOut(300);
		jQuery("#popup").fadeOut(300);
		jQuery("header, main, footer").removeClass("blurriness");

	});
});



//Скрипт плавной прокрутки страницы к якорю при нажатии на ссылку
jQuery(".moduletable_menu").on("click",".anchor", function (event) {
	//отменяем стандартную обработку нажатия по ссылке
	event.preventDefault();
	//забираем идентификатор блока с атрибута href
	var id  = jQuery(this).attr('href'),
	//узнаем высоту от начала страницы до блока на который ссылается якорь
	top = jQuery(id).offset().top;
	//анимируем переход на расстояние - top за 700 мс
	jQuery('body,html').animate({scrollTop: top}, 700);

	//Сворачивание меню при клике по пункту меню типа "Внешний URL", имеющий селектро класса "anchor", который используется для плавной прокрутки страницы к якорю
	jQuery(".mi-active").addClass("menu-icon");
	jQuery(".mi-active").removeClass("mi-active");
	jQuery(".icon-m").removeClass("active");
	jQuery(".moduletable_menu").removeClass("bmi-active");
	jQuery(".bg_menu").removeClass("bg_menu-active");
	jQuery("body").removeClass("overflow");
	jQuery(document).unbind("touchmove", false);
	return false;

});




//Скрипт маски ввода - обязательно должен подключаться до вывода поля, в котором будет задействована маска ввода
jQuery(function(jQuery){
	jQuery("#phone").mask("(999) 999-99-99");
});

//Скрипт кнопки "Вверх/Вниз"
jQuery(function(){
	jQuery("#Go_Top").hide().removeAttr("href");
	if (jQuery(window).scrollTop()>="250") jQuery("#Go_Top").fadeIn("slow")
		jQuery(window).scroll(function(){
			if (jQuery(window).scrollTop()<="250") jQuery("#Go_Top").fadeOut("slow")
			else jQuery("#Go_Top").fadeIn("slow")
		});

	jQuery("#Go_Bottom").hide().removeAttr("href");
	if (jQuery(window).scrollTop()<=jQuery(document).height()-"999") jQuery("#Go_Bottom").fadeIn("slow")
		jQuery(window).scroll(function(){
			if (jQuery(window).scrollTop()>=jQuery(document).height()-"999") jQuery("#Go_Bottom").fadeOut("slow")
			else jQuery("#Go_Bottom").fadeIn("slow")
		});

	jQuery("#Go_Top").click(function(){
		jQuery("html, body").animate({scrollTop:0},"slow")
	})
	jQuery("#Go_Bottom").click(function(){
		jQuery("html, body").animate({scrollTop:jQuery(document).height()},"slow")
	})
});




//Скрипт отправки формы
function send(url,form_id,result_div) {
	// Отсылаем параметры
	jQuery.ajax({
		type: "POST",
		url:  url,
		data: jQuery("#"+form_id).serialize(),
		// Выводим то что вернул PHP
		success: function(html) {
				jQuery("#"+result_div).empty();
				jQuery("#"+result_div).append(html);
		
				setTimeout(function() {
				jQuery(document).ready(function(){
					jQuery("#"+result_div).fadeOut(1800);
				});
					}, 2200)
		},
		
		error: function() {
			jQuery("#"+result_div).empty();
			jQuery("#"+result_div).append("Ошибка!");
			}
		});

}
	
	//эта часть скрипта отвечает за приданию блоку с id="result.." стиля display="block"
	
	//для первой формы
	function chDiv(){
		document.getElementById("result").style.display="block";
	}
	
	//для второй формы
	function mdDiv(){
		document.getElementById("result2").style.display="block";
	}
