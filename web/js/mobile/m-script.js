$(document).ready(function() {
    // Обработка полей ввода (input) для моб версия;
    $("div.form-m").on("click", "div.input", function() {
        $("input, textarea", $(this)).focus();
    }).on("focus", "div.input input, div.input textarea", function() {
        if ($(this).val() == '') {
            $("div.label", $(this).parents("div.input")).hide();
            $("span", $(this).parents("div.input")).show();
        }
    }).on("blur", "div.input input, div.input textarea", function() {
        if ($(this).val() == '') {
            $("div.label", $(this).parents("div.input")).show();
            $("span", $(this).parents("div.input")).hide();
        }
    }).on("change", "div.input select", function() {
        if ($(this).val() == '') {
            $("div.label", $(this).parents("div.input")).show();
            $("span", $(this).parents("div.input")).hide();
        } else {
            $("div.label", $(this).parents("div.input")).hide();
            $("span", $(this).parents("div.input")).show();
        }
    });
    // Вкладки;
    $(document).on("click", "div.tabs-items div.item span", function() {
        var i = $(this).parents("div.item").index();
        $("div.tabs-items div.item", $(this).parents("div.tabs")).removeClass("open");
        $("div.tabs-items div.item", $(this).parents("div.tabs")).eq(i).addClass("open");
        $("div.tabs-contents div.item", $(this).parents("div.tabs")).removeClass("open");
        $("div.tabs-contents div.item", $(this).parents("div.tabs")).eq(i).addClass("open");
    });
    // Аккардион;
    $(document).on('click','div.accordion div.title', function(e) {
        var $this = $(this);
        $this.parent('.accordion-item').toggleClass('open');
        $this.next('.accordion-content').toggle();
        $this.parent('.accordion-item').siblings('.accordion-item').removeClass('open').find('.accordion-content').hide();
        e.stopPropagation();
    });
    // Слайдер;
    $('div.m-slides div.items').slick({
        dots: true,
        autoplay: false,
        mobileFirst:true,
        speed: 500,
        slidesToShow: 1,
        prevArrow: '',
        nextArrow: '',
        slidesToScroll: 1
    });
    // Карусель;
    $('div.goods-carousel div.items').slick({
        arrows: false,
        dots: false,
        slidesToShow: 4,
        slidesToScroll: 4,
        infinite : true,
        centerMode: true,
        responsive: [
            {
                breakpoint: 530,
                settings: {
                    slidesToShow: 1
                }
            },
            {
                breakpoint: 725,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 955,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 1070,
                settings: {
                    slidesToShow: 4
                }
            }
        ]
    });
    // Закрыть затемненный область;
    $("#br-show").click(function(){
        $("div.container-menu,#br-show,div.m-navigation").hide();
        $("#header div.user").removeClass('open');
        return false;
    });
    // Адаптация таблиц;
    if(window.matchMedia('(max-width: 768px)').matches) {
        // Обход цикл ячейка th;
        var string = $("table.mob-table tr.res th").map(function(key, value){
            return $(value).text();
        });
        // Обходим цикл строку;
        $("table.mob-table tr").each(function(index, element){
            if(!$(this).filter('.no-res').length) {
                // Обходим ячейку и добавляем title;
                $('td', element).each(function (k, v) {
                    $(v).prepend('<b>' + string[k] + '</b> ');
                    if ($(this).filter('.ac').length) {
                        $(this).replaceWith("<th>" + '<b>' + $(v).text() + '</b> ' + "</th>");
                    }
                });
            }
        });
    }
    // Маска телефон;
    //$('.phone_mask').mask("+7 (999) 999-9999");

    // Дерево под категория скрыть раскрыть;
    $("#menu-top").on('click','.main-menu',function(){
        $("#menu-top div.container-menu").toggle();
        $("#br-show").toggle();
        return false;
    }).on('click','a.groups',function(){
        var itemId = $(this).attr('rel');
        $(this).toggleClass('open');
        $("#menu-top div.cell.i-" + itemId).toggle();
        return false;
    });

    //Профиль и авторизация;
    $("#header").on('click','div.user',function() {
        $(this).toggleClass('open');
        $(this).parents("div.top").children("#header div.m-navigation").toggle();
        $("#br-show").toggle();
        return false;
    });

    // Проверка товара история заказа;
    $('div.my-orders-m table').each(function(index, element) {
        var id = $(this).attr('id');
        if($("#" + id + " input[type='checkbox']:checked").length) {
            $("#" + id + " div.button input[type='button']").prop("disabled", false);
        }else{
            $("#" + id + " div.button input[type='button']").prop("disabled", true);
        }
    });
    $('div.my-orders-m table').on('click','input[type="checkbox"]',function(index, element) {
        var id = $(this).parents('table').attr('id');
        var i = $("#" + id + " input[type='checkbox']:checked").length;
        if(i){
            $("#" + id + " div.button input[type='button']").prop("disabled", false);
        }else{
            $("#" + id + " div.button input[type='button']").prop("disabled", true);
        }
    });


    // Фиксация контент инфо о корзине;
    fix_top('#basket-total-info');

    // Инициализация календар;
    if($("input.date-input").length > 0){
        console.log('count = '+$("input.date-input").length);
        $("input.date-input").date_input();
    }else{

    }

});

//Фиксировать контент при скролле .
function fix_top(name) {
    $(window).scroll(function () {
        if ($(window).scrollTop() == "0") {
            $(name).removeClass("fix");
        } else {
            $(name).addClass("fix");
        }
    });
}
// Прелоадер;
/*
 function loading(name) {
 if(name === 'show') {
 $("#ajax-loader").show();
 }else if(name === 'hide') {
 $("#ajax-loader").hide();
 }
 }
 */
// Отправка данных формы (Старый);
function window_action_m(name, url, close) {
    if (!url) url = window.location.href;
    var inputs = $("form", "#" + name).serialize();
    $("div.error", "#" + name).html('').hide();
    $("#" + name + " div.button, #pay div.close").hide();
    $("#" + name + " div.button_load").show();
    $.post(url, inputs, function(error) {
        if (error) {
            $("div.error", "#" + name).html(error).show();
        } else {
            if(!close) {
                window.location.reload();
            } else{
                $("#shadow").hide();
                $("div.window", "#shadow").hide();
                $("div.load", "#shadow").hide();
            }
        }
        $("#" + name + " div.button_load").hide();
        $("#" + name + " div.button, #pay div.close").show();
    });
}
// Отправка данных формы на следующий шаг;
function window_action_step_m(name, url) {
    if (!url) url = window.location.href;
    var inputs = $("form", "#" + name).serialize();
    // console.log(inputs);
    $("div.error", "#" + name).html('').hide();
    $("#" + name + " div.button, #pay div.close").hide();
    $("#" + name + " div.button_load").show();
    $.post(url, inputs, function(error) {
        if (error) {
            $("div.error", "#" + name).html(error).show();
        } else {
            $.post(url, {}, function(html) {
                $("div.form-m", "#" + name).html($(html).find("div.form-m", "#" + name).html())

                ;});
        }
        $("#" + name + " div.button, #pay div.close").show();
        $("#" + name + " div.button_load").hide();
    });
    return false;
}
function orders_open(key){
    return $('#key' + key + ' .hidden_r').toggle();
}
// Открытие окон;
function show_modal_compact(url,title) {
    var modalContainer = $('#windows');
    var modalBody = modalContainer.find('.modal-body');
    // Размер окно;
    $(".modal-dialog",modalContainer).addClass('modal-max');
    // Показать модальная окно;
    modalContainer.modal('show');
    if(title){
        $(".modal-title",modalContainer).text(title);
    }else{
        $(".modal-title",modalContainer).text('');
    }
    // Загрузка контент;
    modalBody.load(url + ' div.good', function () {

    });
    return false;
}

//alert(1);