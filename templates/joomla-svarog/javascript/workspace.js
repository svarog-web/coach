// Скрпиты рабочей области файла _workspace.shtml. После окончания работа данный файл необходимо удалить

//Скрипт разворачивания и сворачивания рабочей области
	jQuery('.workspace h5').click(function(){ 
		jQuery(this).toggleClass('pointer_bottom');
		jQuery(".workspace-inside").toggleClass('wi_block');
	});



/*	Скрпит для подгрузки анимации на css при выборе в выпадающем списке в рабочей области */
	function testAnim(x) {
		jQuery('#animationSandbox').removeClass().addClass(x + ' animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
			jQuery(this).removeClass();
			});
		};

		jQuery(document).ready(function(){
		jQuery('.js--triggerAnimation').click(function(e){
			e.preventDefault();
			var anim = jQuery('.js--animations').val();
			testAnim(anim);
		});

		jQuery('.js--animations').change(function(){
			var anim = jQuery(this).val();
			testAnim(anim);
		});
	});




/*	Данный скрипт запускается при достижении, при прокрутке страницы, указанного расстояния между верхней границей браузера и блоком с классом .mov.
	Данный скрипт можно использовать для начала выполнения любых эффектов, функций или событий на javascript и jquery при прокрутке страницы.
	В данном примере приведён скрипт подключения анимации css при подгрузке страницы. Для того, чтобы данный скрпит работал - необоходимо вместо класса с названием эффекта добавить к блоку класс .mov.
	Перед удалением данного файла (workspace.js) данный скрипт необходимо скопировать в файл со скриптами.*/

	jQuery(window).scroll(function() {
		jQuery('.mov').each(function(){
			var imagePos = jQuery(this).offset().top;
			var topOfWindow = jQuery(window).scrollTop();
			if (imagePos < topOfWindow+200) {			// 200 - количество пикселей от начала страницы до блока при прокрутке до которого начинается выполнение эффекта
				jQuery(this).addClass('zoomIn');				// zoomIn - название класса-эффекта
			}
		});
	});




/*	Скрипт ниже предназначен для начала выполнения каких-либо эффектов, функций или событий на javascript и jquery при прокрутке страницы.
	Его отличие от скрипта выше в том, что данный скрипт запускается при прокрутке блока к якорю.
	Перед удалением данного файла (workspace.js) данный скрипт необходимо скопировать в файл со скриптами. */

/*
	$(window).scroll(function(){
		// distanceTop = (высота: от начала страницы до эл-та #last) - высота окна браузера
		var distanceTop = $('#last').offset().top - $(window).height();  
		if ($(window).scrollTop() > distanceTop) {
				setTimeout(function () {$('.sidebar').animate({'top':'100px', opacity: 1.0}, 1000); }, 1000);
			}
	});
*/