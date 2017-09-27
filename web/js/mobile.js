/*----Мобильная версия скрипт Version 1.2.2 2016-03-06 (Везде подключаем скрипт!!!)---*/

var versionScripts = "Mobile.js version: 1.2.2@2016-03-06";

console.log(versionScripts);


/*----Раздвежной меню каталог-----*/
(function ($) {
	// Параметры css разметки;
	var pushy = $('.m-navigation'),
		body = $('body'),
		container = $('#center,#header,#footer'),
		push = $('.push'),
		pushyLeft = 'pushy-left',
		pushyOpenLeft = 'pushy-open-left',// слайд в лево;
		pushyOpenRight = 'pushy-open-right', // слайд в право;
		siteOverlay = $('.br-shadow'), // Вешаем события затемнения;
		menuBtn = $('.js-catalog-menu, .pushy-link'), // Вешаем события клика;
		menuSpeed = 200,// Задержка в мл сек.
		menuWidth = pushy.width() + 'px'
	function togglePushy(){
		if( pushy.hasClass(pushyLeft) ){
			body.toggleClass(pushyOpenLeft);
		}else{
			body.toggleClass(pushyOpenRight);
		}
	}
	// Окрыть слайд;
	function openPushyFallback(){
		if( pushy.hasClass(pushyLeft) ){
			body.addClass(pushyOpenLeft);
			pushy.animate({left: "0px"}, menuSpeed);
			container.animate({left: menuWidth}, menuSpeed);
			push.animate({left: menuWidth}, menuSpeed);
		}else{
			body.addClass(pushyOpenRight);
			pushy.animate({right: '0px'}, menuSpeed);
			container.animate({right: menuWidth}, menuSpeed);
			push.animate({right: menuWidth}, menuSpeed);
		}
	}
	// Закрыть слайд;
	function closePushyFallback(){
		if( pushy.hasClass(pushyLeft) ){
			body.removeClass(pushyOpenLeft);
			pushy.animate({left: "-" + menuWidth}, menuSpeed);
			container.animate({left: "0px"}, menuSpeed);
			push.animate({left: "0px"}, menuSpeed);
		}else{
			body.removeClass(pushyOpenRight);
			pushy.animate({right: "-" + menuWidth}, menuSpeed);
			container.animate({right: "0px"}, menuSpeed);
			push.animate({right: "0px"}, menuSpeed);
		}
	}
	var cssTransforms3d = (function csstransforms3d(){
		var el = document.createElement('p'),
		supported = false,
		transforms = {
		    'webkitTransform':'-webkit-transform',
		    'OTransform':'-o-transform',
		    'msTransform':'-ms-transform',
		    'MozTransform':'-moz-transform',
		    'transform':'transform'
		};
		document.body.insertBefore(el, null);
		for(var t in transforms){
		    if( el.style[t] !== undefined ){
		        el.style[t] = 'translate3d(1px,1px,1px)';
		        supported = window.getComputedStyle(el).getPropertyValue(transforms[t]);
		    }
		}
		document.body.removeChild(el);
		return (supported !== undefined && supported.length > 0 && supported !== "none");
	})();

    // Конец анимация;
	function transitionendAnim() {
		if(!$('body').filter('.pushy-open-left').length && !$("body").filter('.no-csstransforms3d').length) {
			pushy.css({'visibility': 'hidden'});
			//$(".zoom-content").css({'transform':'scale(1, 1)'});
		}
	}

    // Время вычисления анимация;
	if($("#slidingMenu").length) {
		var element = document.getElementById("slidingMenu");
		element.addEventListener("transitionend", transitionendAnim, false);
	}

	if(cssTransforms3d){
		//pushy.css({'display': 'block'});
		menuBtn.on('click', function(){
			pushy.css({'visibility': 'visible'});
			$(".zoom-content").css('transform','none');
			$('#basket-total-info').toggle();
			$(this).toggleClass('active');
			togglePushy();

		});
		siteOverlay.on('click', function(){
			$('#basket-total-info').show();
			menuBtn.removeClass('active');
			togglePushy();
		});
		var i=0;
		$("div.m-navigation").on('click','.select-menu.open',function(){
			i++;
			if(i >= 1) {
				$('#basket-total-info').show();
				menuBtn.removeClass('active');
				togglePushy();
				// Обнуляем счетчик;
				i = 0;
			}
		});
	}else{
		body.addClass('no-csstransforms3d');
		if( pushy.hasClass(pushyLeft) ){
			pushy.css({left: "-" + menuWidth});
		}else{
			pushy.css({right: "-" + menuWidth});
		}
		pushy.css({'visibility': 'visible'});
		container.css({"overflow-x": "hidden"});
		var opened = false;

		menuBtn.on('click', function(){
			if (opened) {
				closePushyFallback();
				opened = false;
			} else {
				openPushyFallback();
				opened = true;
			}
		});

		siteOverlay.on('click', function(){
			if (opened) {
				closePushyFallback();
				opened = false;
			} else {
				openPushyFallback();
				opened = true;
			}
		});
	}
	// Дерево под категория скрыть раскрыть;
	$(document).on('click','div.container-menu div.open_plus',function(){
		var itemId = $(this).attr('rel');
		$(this).siblings('a.groups').toggleClass('open');
		$("div.container-menu div.cell.i-" + itemId).toggle();
		return false;
	});
	// Выбор тип меню;
	$(document).on('click','div.m-navigation .select-menu',function() {
		var item = $(this).attr('rel');
		$(this).addClass('open').add("div.m-navigation .select-menu").not(this).removeClass('open');
		// Переключатель меню;
		$("div.container-menu","div.m-navigation").each( function(index,value){
			if($(value).attr('rel') === item) {
				$(value).addClass('open');
			}else{
				$(value).removeClass('open');
			}
		});
	});
}(jQuery));

$(document).ready(function() {

	var shadow = $(".br-shadow-goods");

	// Фиксированый шапка;
	 shadow.affix({
		offset: {
			top: 100,
		}
	});


    if(shadow.filter('.affix-top').length) {
		shadow.removeClass('top');
	}
	shadow.on('affixed-top.bs.affix',function() {
		shadow.removeClass('top');
	});
	shadow.on('affixed.bs.affix',function() {
		shadow.addClass('top').add(shadow).addClass('bottom');

	});
	$(document).ready(function() {
		// Фиксированый шапка;
		$('.header-content.mobile div.fix-content').affix({
			offset: {
				top: 100
			}
		});
	});


	// Масштабирования экран;
	if($(window).width() <= 1190 && typeof PinchZoomTest == 'function' && !$("#secretWord").length && true) {
		var mc = new RTP.PinchZoom($('.zoom-content'), {
			maxZoom: 3,
			minZoom: 0.9,
			//animationDuration: 200,
			zoomOutFactor: 100,
			tapZoomFactor: 3, // Двойное нажатия;
		});
		console.info('Масштаб экран вкл.');
	}

});

function m_orders_open(key){
	return $('#key' + key + ' .hidden_r').toggle();
}

console.log("Mobile.js - OK");