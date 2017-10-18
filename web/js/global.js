
var versionGlobal = "Global.js version: 10.2.3@20102016";
console.warn(versionGlobal);

/*Preloder*/

 setTimeout(function () {
     $("#preloader").delay(100).fadeOut('show');
     $("#preloader .loading-data").fadeOut();
 },100);




$(document).ready( function(){
    // Ввод цифр;
    $(document).on("keypress", "input.number", function(e) {
        var charCode = (e.which) ? e.which : event.keyCode;
        if (charCode != 8 && (charCode < 48 || charCode > 57)) return false;
        return true;
    });
    // Ввод денег;
    $(document).on("keypress", "input.money", function(e) {
        var charCode = (e.which) ? e.which : event.keyCode;
        if (charCode != 8 && charCode != 43 && charCode != 46 && (charCode < 48 || charCode > 57)) return false;
        return true;
    });
    // Обработка нажатия Enter;
    $(document).on("keypress", "div.window div.form input", function(e) {
        var charCode = (e.which) ? e.which : event.keyCode;
        if (charCode == 13) {
            $("div.window div.form div.button").click();
            return false;
        }
    });
    // Переключение фотографии;
    $(document).on("click", "div.carousel_item", function(e) {
        var tag_id = $(this).data('tag-id');
        // выбор вариация;
        if(e.bubbles){
            $('div.good .tag-value-group-item[data-tag-id="'+tag_id+'"]').click();
        }
        $("img", "div.good").removeClass("open");
        $(this).children('div.images a').children('div.images img').addClass("open");
        var imgHref = $("img", $(this)).attr("src").replace("_min", "");
        $("div.image img", "div.good").attr("src", imgHref);
        // Зум;
        if($("div.image", "div.good").has('.cloud-zoom').length) {
            var imgHrefMax = $("img", $(this)).attr("src").replace("_min", "_max");
            $("div.image a", "div.good").attr("href", imgHrefMax);
            // Инициализация зум;
            $(".cloud-zoom").CloudZoom();
        }
        return false;
    });
    // Переключения фотграфия в каруселе;
    $(document).on("click", ".goods-carousel-min .js_carousel", function(e) {
        var tag_id = $(this).parent('.item').data('tag-id');
        console.log(tag_id);
        // выбор вариация;
        if(e.bubbles){
            $('div.good .tag-value-group-item[data-tag-id="'+tag_id+'"]').click();
        }
        // Замена изображения;
        var imgHref = $(this).attr("src").replace("_min", "");
        console.log(imgHref);
        $("div.image img", "div.good").attr("src", imgHref);
        // Текущий бордер;
        $(".goods-carousel-min div.item img").removeClass('open');
        $(this).addClass('open');
        return false;
    });


    /*Меню каталог*/
    $("#menu-top").on('mouseenter','.catalog-goods',function(){
        $("#menu-top div.container-menu").show();
    }).on('mouseenter','.catalog-goods a.menu-top',function(){
        var items = $(this).siblings('.container-catalog');
        items.show();
        var rel_id = items.attr('rel');
        var itemCounts = $('div.container-catalog[rel="'+ rel_id +'"] a.main').size();
        var i = 1;
        $(this).addClass('open');
        if (itemCounts > 6) {
            i = 2;
        } else if (itemCounts > 11) {
            i = 3;
        } else if (itemCounts > 21) {
            i = 4;
        }
        // Обертываем блоки Категория до 21 шт.;
        while (($blok = $("div.container-catalog[rel="+ rel_id +"] div.block:not(div.cell) > div.row-container:lt(" + i + ")")).length) {
            $blok.wrapAll($('<div class="cell"></div>'));
        }
    }).on('mouseleave','.catalog-goods ',function(){
        $("#menu-top div.container-menu").hide();
    }).on('mouseleave','.item-menu',function(){
        $("#menu-top div.container-catalog").hide();
        $('.container-menu a.menu-top').removeClass('open');
    });

    // Каталог меню выбранные;
    $("#menu-top").on('mouseenter','div.selected',function(){
     $.data(this,'times', setTimeout($.proxy(function() {
        var items = $(this).children('.groups');
        items.show();
        var rel_id = items.attr('id');
        rel_id = Number(rel_id.match(/\d+/));
        var itemCounts = $('#nav-' + rel_id + ' a.main').size();
        var i = 1;
        if (itemCounts > 6) {
            i = 2;
        } else if (itemCounts > 14) {
            i = 3;
        } else if (itemCounts > 23) {
            i = 4;
        }
        // Обертываем блоки Категория до 23 шт.;
        while (($blok = $("#nav-"+ rel_id +" div.block:not(div.cell) > div.row-container:lt(" + i + ")")).length) {
            $blok.wrapAll($('<div class="cell"></div>'));
        }
        // Определяем размер и позиция;
        // var menuTop = $('#menu-top').width();
        var winDoc = $(window).width();
        var i = $('#nav-' + rel_id,'#menu-top');

        var r = i.offset().left + i.width();
        if(r > winDoc - 30) {
            i.css("right", '0');
        }
     }, this), 200));
    }).on('mouseleave','div.item.selected',function(){
        // Удаляем интервал;
        clearTimeout($.data(this,'times'));
        $("#menu-top div.groups").hide();
    });

    /*Меню каталог левый*/
    $("#left-menu").on('mouseenter','.catalog-goods',function(){
        $("#left-menu div.container-menu").show();
    }).on('mouseenter','a.menu-top',function(){
            var items = $(this).siblings('.container-catalog');
            items.show();
            var rel_id = items.attr('rel');
            var itemCounts = $('div.container-catalog[rel="' + rel_id + '"] a.main').size();
            var itemCountsAll = $('div.container-catalog[rel="' + rel_id + '"] a.all').size();

            var i = 1;
            $(this).addClass('open');
            if (itemCounts >= 6) {
                i = 2;
            } else if (itemCounts > 11) {
                i = 3;
            } else if (itemCounts > 21) {
                i = 4;
            }
            //
            if (itemCountsAll >= 7) {
                //$('div.container-catalog[rel="' + rel_id + '"] .row-container').addClass('all');
            }

            // Обертываем блоки Категория до 21 шт.;
            while (($blok = $("div.container-catalog[rel=" + rel_id + "] div.block:not(div.cell) > div.row-container:lt(" + i + ")")).length) {
                $blok.wrapAll($('<div class="cell"></div>'));
            }
            $('#left-menu div.items div.container-catalog').css('width', ($(".container.shop-container").width() - 255));
    }).on('mouseleave','.item-menu',function(){
           $("#left-menu div.container-catalog").hide();
           $('.container-menu a.menu-top').removeClass('open');
    }).on('mouseenter','.bg_panel.left',function(){
           clearTimeout($.data(this,'times1'));
          $("#left-menu div.container-menu.open").show();
    }).on('mouseleave',this,function(){
       $.data(this,'times1', setTimeout($.proxy(function() {
            $("#left-menu div.container-menu.open").hide();
       }, this), 500));
    });

    //  Какого хрена дублируется функция, а Русланчик??????
    //  predator_pc
    // Нужен было для ЛК адресс:а там уже отключен basket.js!

    $(document).on('click','div.add_address div.form div.button',function(){
       //alert($('#delivery_id').val());
        $.ajax({
            method    :   'POST',
            url       :  '/ajax-basket/add-new-address',
            data      :   {
                'city' : $('#address').find('.city[name=city]').val(),
                'street' : $('#address').find('.street[name=street]').val(),
                'house' : $('#address').find('.house[name=house]').val(),
                'room' : $('#address').find('.room[name=room]').val(),
                'delivery_id' : $('#delivery_id').val(),
                'district' : $('#address').find('.district[name=district]').val(),
                'delivery' : $('#delivery_id').val(),
                'comments' : $('#address').find('[name=comments]').val(),
                'phone' : $('#address').find('.number[name=phone]').val()
            },
            success   :   function(response){
                if(response == 'OK') {
                  //  console.log(response);
                    $('#address-modal').modal('hide');
                    window.location.reload();
                }else {
                    $("#address div.error").text(response).show();
                }
            }
        });
        return false;
    });
    // Закрыть окно;
    $('#windows').on('hidden.bs.modal', function () {
        $('div.modal-body').html('');
        return false;
    });
    $('.mod__variations-box').on('click','.close',function(){
        $(this).parent('.mod__variations-box').hide();
    });

    // Скроллинг вверх;
    scroll_top("to-top");

    // Закрыть box вариации;
    $(document).on('click','.mod__variations-box .close',function(){
        $("div.mod__variations-box").hide();
    });

    // Фиксир. шапка;
    $('#header div.header-content.desktop div.fix-content-panel').affix({
        offset: {
            top: 100
        }
    });
    //
    $('#stock .content-stock').tooltip();

    //window_show('basket/default/stock-modal','Title');
    $('#windows').on('hide.bs.modal', function (e) {
        $.post(basketAjaxPath+ 'modal-stock', {
            'ModalStock': true
        });
    });

    //
    $(document).on('click','.js-save-price',function(){
       var variation_id = $(this).data('variation-id');
       var price = $(this).siblings('._price').val();

        $.post(catalogAjaxPath+ 'change-price', {
            'variation_id': variation_id,
            'new_price': price,
            'lenta': $("div.action-edit-good input.checkbox:checked").val()
        },function(response){
            if(response.success) {
                console.log(response.price);
                console.log(variation_id);
                $(".row-container[data-variant="+variation_id+"] .variation-price").html(response.price + '<small class="rubznak">p.</small>');
            }else{
                console.log(response);
            }
        });
        $("._icon").show();
        $(".js-content-edit").hide();

    });




});

// Выпадашка Корзина;
$(document).on('mouseenter','#header div.bottom div.basket',function(event) {
   if((this === event.target)) {
    // Удаляем интервал;
    clearTimeout($.data(this, 'timer'));
    if ($('#header div.bottom div.basket').has('.box-container').length)
        $(this).addClass('active');
    $('#header div.bottom div.basket .box-container').stop(true, true).slideDown(100);
      }
}).on('mouseleave','#header div.bottom div.basket',function() {
    // Интервал закрытия блок;
    $.data(this, 'timer', setTimeout($.proxy(function () {
        $('#header div.bottom div.basket').removeClass('active');
        $('#header div.bottom div.basket div.box-container').stop(true, true).slideUp(100);
    }, this), 100));
});

// Выпадашка меню ЛК;
$(document).on('mouseenter','#header .user-profile a.user',function() {

    // Удаляем интервал;
    clearTimeout($.data(this, 'timer'));
    $(this).addClass('active');
    $(this).siblings('#header .user-profile .box-container').stop(true, true).slideDown(100);
    $("#header div.top  div.user a.user").css({'color':'#22980b','text-decoration':'none','background-position':'5px -12px'});
}).on('mouseleave','.user-container',function() {
    // Интервал закрытия блок;
    $.data(this, 'timer', setTimeout($.proxy(function () {
        $('#header .user-profile a.user').removeClass('active');
        $('#header .user-profile div.box-container').stop(true, true).slideUp(100);
        $("#header div.top  div.user a.user").css({'color':'#fff','text-decoration':'underline','background-position':'5px 7px'});
    }, this), 100));
});

// Выпадашка меню fix ЛК;
$(document).on('mouseenter','#header .js-user-menu',function() {
    // Удаляем интервал;
    clearTimeout($.data(this, 'timer'));
    $(this).addClass('active');
    $(this).siblings('#header .user-container .box-container').stop(true, true).slideDown(100);
    $(this).css({'color':'#22980b','text-decoration':'none','background-position':'0px -18px'});
}).on('mouseleave','.user-container',function() {
    // Интервал закрытия блок;

    $.data(this, 'timer', setTimeout($.proxy(function () {
        $('#header .user-container a.js-user-menu').removeClass('active');
        $('#header .user-container div.box-container').stop(true, true).slideUp(100);
        $("#header .user-container a.js-user-menu").css({'color':'#fff','text-decoration':'underline','background-position':'0px 1px'});
    }, this), 100));
});
// Дополнить строку нулями;
function str_pad(value, length) {
    var string = value.toString();
    while (string.length < length) {
        string = "0" + string;
    }
    return string;
}
// Обработка полей;
$(document).on('focus','input.placeholder[placeholder]',function(){
    $(this).attr('placeholder','');
    return false;
});
$(document).on('blur','input.placeholder[placeholder]',function(){
    $(this).attr('placeholder',$(this).attr('data-text'));
    return false;
});
// +7
$(document) .on('focus','input.phone',function() {
    $(this).parents('.form-group').siblings('span.phone').show();
    $(this).siblings('span.phone').show();
    $(this).css("padding-left","25px");
}).on('blur','input.phone',function() {
    if($(this).val() == '') {
        $(this).parents('.form-group').siblings('span.phone').hide();
        $(this).siblings('span.phone').hide();
        $(this).css("padding-left","12px");
    }
});
//// Переключатель вариация;
//$(document).on('click','#basket div.tags-items.select',function(){
//    $(this).siblings('#basket div.variations-select').toggle();
//    $('#basket div.variations-select').not($(this).siblings('#basket div.variations-select')).hide();
//});

// Стилизация select;
$(document).on('click','.select__form .container-select',function () {
    var elememtMulti = $(this).parents('.select__form_multi');
    if(!$(this).filter('.disabled').length) {
        $(this).siblings('.select__form .row').toggle();
        $('.select__form .row', elememtMulti).not($(this).siblings('.select__form .row')).hide();
        $(this).siblings('.select__form .top.row').css('top',-($(this).siblings('.select__form .row').height() + 8));
    }
}).on('click','.select__form .row div.option',function () {
    var text_option = $(this).text();
    var items = $(this);
    var element = items.parents('.select__form .row');
    if (!items.filter('.disabled').length){
        // Добавляем selected;
        if(!items.filter('.goodVariant').length) items.addClass('selected');
        $('div.option', element).not(items).removeClass('selected');
        if (!items.filter('.goodVariant').length) items.parents('.select__form .row').siblings('.select__form .container-select').children('.select__form div.option-text').text(text_option);
        items.parents('.select__form .row').hide();
    }
}).on('mouseleave','.select__form div.row',function () {
    $(this).hide();
});
// Скроллинг вверх +;
function scroll_top(name) {
    $(window).scroll(function () {
        if ($(window).scrollTop() == "0") {
            $("#" + name).fadeOut("slow");
        } else {
            $("#" + name).fadeIn("slow");
        }
    });
    $("#" + name).on("click", function () {
        $("html, body").animate({
            scrollTop: 0
        }, "slow");
    });
}
// Открытие окон;
// Открытие окон;
function window_show(url,title,size,ymapasFlag,loadPage) {
    var modalContainer = $('#windows');
    var modalBody = modalContainer.find('.modal-body');
    if(loadPage) {
        $("#windows").on('hide.bs.modal', function () {
            window.location.reload();
        });
    }
    // Размер окно;
    if(size == 'mid') {
        $("#windows .modal-dialog").addClass('modal-max');
    }else if(size == 'max') {
        $("#windows .modal-dialog").addClass('modal-big');
    }else{
        $("#windows .modal-dialog").removeClass('modal-big').add('#windows .modal-dialog').removeClass('modal-max');
    }
    modalContainer.modal({show:true});

    if(title){
        $("#windows .modal-title").text(title);
    }else{
        $("#windows .modal-title").text('');
    }

    $.ajax({
        url: '/' + url,
        type: "GET",
        data: {/*'userid':UserID*/},
        async: false,
        success: function (data) {
            $('#windows .modal-body').html(data);

            modalContainer.modal({show:true});

            if (ymapasFlag === true && typeof ymaps_re == 'function') {
                ymaps_re();
            }

            if(url == 'submitsignup')
            {
                console.log('!!!');
                $('#gre').empty();
                grecaptcha.render('gre', {'sitekey': '6LfliiYTAAAAAFYko6YeiAeSK9z9ovddt6ebRuvO'});
            }
        }
    });
    //$('#gre').empty();
    //grecaptcha.render('gre', {'sitekey': '6LcgjCYTAAAAAOuBjKkPmaqKnmgIfHVywPr54BON'});
    return false;
}
// Отправка данных формы;
function modal_form_action(name,url) {
    var result;
    var form = $("form." + name, '#windows').serialize();

    $.ajax({
        url: '/' + url,
        type: "POST",
        scriptCharset: "utf-8",
        data: form,
        success: function (data) {
            console.log(data);
            var modalContainer = $('#windows');
            var modalBody = modalContainer.find('.modal-body');
            var insidemodalBody = modalContainer.find('.gb-user-form');
            if(data == 'asked'){
                $('#windows').modal('hide');
                return false;
            }
            try {
                result = jQuery.parseJSON(data);
                console.log(result);
                if (result.flag == true) {

                    if(window.location.pathname=='/promoes'){
                        return window.location.href='/site/agree';
                    }
                    insidemodalBody.html(result).hide();
                    $('#windows').modal('hide');
                    location.reload();
                }
                else {
                    console.log('Error!');
                }
            }
            catch(e){
             //   console.log('!!!');
              //  setTimeout(2000);
                //modalBody.html(data).hide().fadeIn();
                modalBody.html(data).fadeIn();
                $('#gre').empty();
                $('#errno').text('Заполните поле проверочный код!');
                //grecaptcha.render('gre', {'sitekey': '6LfliiYTAAAAAFYko6YeiAeSK9z9ovddt6ebRuvO'});

            }
        }
    });
    return false;
}


// Login;
function modal_form_action_beta(name,url) {
    var result;
    var form = $("form.login-form").serialize();

    $.ajax({
        url: '/' + url,
        type: "POST",
        scriptCharset: "utf-8",
        data: form,
        success: function (data) {
            console.log(data);
            var modalContainer = $('#windows');
            var modalBody = modalContainer.find('.modal-body');
            var insidemodalBody = modalContainer.find('.gb-user-form');
            if(data == 'asked'){
                alert('asked');
                return false;
            }
            try {
                result = jQuery.parseJSON(data);
                console.log(result);
                if (result.flag == true) {

                    if(window.location.pathname=='/promoes'){
                        return window.location.href='/site/agree';
                    }
                    insidemodalBody.html(result).hide();
                    location.reload();
                }
                else {
                    console.log('Error!');
                }
            }
            catch(e){
                //   console.log('!!!');
                //  setTimeout(2000);
                //modalBody.html(data).hide().fadeIn();
                modalBody.html(data).fadeIn();
                $('#gre').empty();
                $('#errno').text('Заполните поле проверочный код!');
                //grecaptcha.render('gre', {'sitekey': '6LfliiYTAAAAAFYko6YeiAeSK9z9ovddt6ebRuvO'});

            }
        }
    });
    return false;
}


// Отправка данных формы ;
function form_action(name, url) {
    if (!url) url = window.location.href;
    var inputs = $("form","div.form___gl." + name).serialize();
    $("div.error", "div.form___gl." + name).html('').hide();
    $("div.form___gl." + name + " .button__a").hide();
    $("div.form___gl." + name + " div.load").show();
    $.post(url, inputs, function(data) {

        if(!$(data).length) {
            $("div.form___gl." + name + " .form-control").val('');
            $("div.form___gl." + name + " .alert-success").show();
            setTimeout(function(){
                $("div.form___gl." + name + " .alert-success").fadeOut();
            }, 6000);
        }else{
            $("div.form___gl." + name).html($(data).find("div.form___gl." + name).html());
        }

        $("div.form___gl." + name + " div.load").hide();
        $("div.form___gl." + name + " .button__a").show();
    });
}

function form_action_json(name, url) {
    if (!url) url = window.location.href;
    var inputs = $("form","div.form___gl." + name).serialize();
    $("div.error", "div.form___gl." + name).html('').hide();
    $("div.form___gl." + name + " .button__a").hide();
    $("div.form___gl." + name + " div.load").show();
    $.post(url, inputs, function(data) {
        if (data.error) {
            $(".alert-danger","div.form___gl." + name).text(data.error).show();
            setTimeout(function(){
                $(".alert-danger","div.form___gl." + name).fadeOut();
                $(".alert-danger li","div.form___gl." + name).remove();
            }, 6000);
        } else {
            $(".alert-success","div.form___gl." + name).text(data.success).show();
            $(".form-control","div.form___gl." + name).val('');
            setTimeout(function(){
                $(".alert-success","div.form___gl." + name).fadeOut();
                $(".alert-success","div.form___gl." + name).html('');
            }, 6000);
        }
        $("div.form___gl." + name + " div.load").hide();
        $("div.form___gl." + name + " .button__a").show();

    },'JSON');
}
// Открытие окон;
function show_modal_compact(url,title,id) {
    var modalContainer = $('#windows');
    var modalBody = modalContainer.find('.modal-body');
    // Размер окно;
    $(".modal-dialog",modalContainer).addClass('modal-max').addClass('compact-good').removeClass('modal-min');
    // Показать модальная окно;
    //modalContainer.modal('hide');
    if(title){
        $(".modal-title",modalContainer).text(title);
    }else{
        $(".modal-title",modalContainer).text('');
    }
    loading('show');
        // Загрузка контент;
        modalBody.load(url + ' div.good.compact', {'compact': true, 'id': (id ? id : false)}, function (renspose) {
            modalContainer.modal('show');
            modalContainer.on('click', '.basket_button', function () {
                modalContainer.modal('hide');
            });
            $("div.goods-carousel-min div.items").hide();
            $("div.goods-carousel-min .button_load").show();
            // Инилизация карусель;
            setTimeout(function () {
                $("div.goods-carousel-min div.items").show();
                $("div.goods-carousel-min .button_load").hide();
                //
                $('div.goods-carousel-min div.items').slick({
                    infinite: false,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    arrows: true,
                    dots: false,
                    responsive: [
                        {
                            breakpoint: 405,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2,
                            }
                        },
                    ]
                });

            }, 200);
            /*---События скролл---*/
            var tempScrollTop, currentScrollTop = 0;
            var flagUp = false;
            $("#windows div.compact-good div.description").scroll(function () {
                currentScrollTop = $(this).scrollTop();
                var sizeHeight = $(this).height();
                var sizeScroll = $(this).prop('scrollHeight');
                if (tempScrollTop < currentScrollTop) {
                    if ((sizeScroll - currentScrollTop) <= sizeHeight) {
                        $("div.good.compact .subs-string").hide();
                        flagUp = true;
                    }
                } else if (tempScrollTop > currentScrollTop) {
                    if (flagUp) {
                        $("div.good.compact .subs-string").show();
                    }
                    flagUp = false;
                }
                tempScrollTop = currentScrollTop;
            });
            if ($("div.good", modalContainer).has('._tagName').length) {
                $("div.prices.block").addClass("res");
            }
            /*---end События скролл---*/
            loading('hide');
            console.log('compact---OK');

        });

    return false;
}

// Прелоадер;
function loading(name) {
    if(name === 'show') {
        $("#loadAjax").show();
        $("#center").css('opacity','0.50');
    }else if(name === 'hide') {
        $("#loadAjax").hide();
        $("#center").css('opacity','100');
    }
}

// Сплывающий уведомления;
function alert_show(text) {
    var html = '<div class="alert alert-success fade in">' + text +'</div>';
    $('.alert___content').html(html).show();
    setTimeout(function(){
        $('.alert___content').html('').hide();
    }, 6000);
}

// Мастер помощник;
function masterHelp (url) {
    if(!url) return false;
    loading('show');
    $.post(window.location.href, {'masterHelp': true},function(html){
        location.href = url;
        loading('hide');
    });
    return false;
}
function menuListPage(position,url) {
    $.post(window.location.href, {
        'menuList': true,
        'position': position
    });
    return true;
}
function issetJs(variable) {
    return (typeof(variable) != "undefined" && variable !== null);
}
// Скрываем столбец;
function table_col_hide(className,list) {
    for (var i=0, len = list.length; i < len; i++) {
        $('table.'+className+' td:nth-child('+list[i]+'),table.'+className+' th:nth-child('+list[i]+')').hide();
    }
}
$(document).ready(function(){
    var url = window.location.pathname;
    if(url.indexOf('basket')>=0) {
        AskQuestion('basket');
    }else{
        return false;
    }
});

function AskQuestion(section) {

    $.ajax({
        url: '/ajax/check-asked-question?section=' + section,
        type: "GET",
        success: function (data) {
            if(data == 'ask'){
                var url = window.location.pathname;
                if(url.indexOf('catalog')>=0) {
                    var section = 'catalog';
                }else if(url.indexOf('basket')>=0){
                    var section = 'basket';
                }else{
                    return false;
                }
                console.log(data);
                window_show('ajax/ask-question?section=' + section,'Слышь,');
            }
        }
    });
}
// Редактирование наименования товара;
function good_edit(good_id) {
    window.open('/product/update?id=' + good_id);
    return false;
}

console.log("global.js --- Ok");
