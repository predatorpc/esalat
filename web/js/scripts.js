
//var versionScripts = "Scripts.js version: 1.0.0@31-08-2016";
var versionScripts = "Scripts.js version: 1.1.2@20112016";

console.warn(versionScripts);

$(document).ready( function(){

    $(document).on('click','.basket_button_repeat',function(){
        var product =[];
        //console.log('repeat');
        var tableid = $(this).parents('table').attr('id');
        //console.log(tableid);
        //product = new Array();
        for(var i=$(this).attr('data-min-prod'); i < $(this).attr('data-max-prod'); i++){
            if($('#'+tableid+'-'+i).is(':checked')){
                product.push(JSON.stringify({
                    'variationId':$('#'+tableid+'-'+i).val(),
                    'count': $('#'+tableid+'-'+i).attr('count'),
                }));
                //var index = product.length;
                //product[index] = new Array();
                //product[index]['variationId']=$('#'+tableid+'-'+i).val();
                //product[index]['count']=$('#'+tableid+'-'+i).attr('count');
            }
        }
        //console.log(product);
        //  Положить товар в корзину
        if($(this).data('action') == 'bay'){
            $.ajax({
                method    :   'POST',
                url       :   basketAjaxPath + 'repeat-order',
                data      :   {'items[]': product},
                dataType  : "JSON",
                success   :   function(response){
                    //console.log(response);
                    //$(this).hide();
                    shop.reloadBasketSmall();
                }
            });
        }
        return false;
    });
    // Инилизация функция Masonry;
   // masonry('div.posters div.items',17,3);
    // Слайдер;
    if($('div.slides div.items').length > 0){
        $('div.slides div.items').slick({
            dots: true,
            autoplay: true,
            autoplaySpeed: 6000,
            mobileFirst:true,
            speed: 1000,
            slidesToShow: 1,
            prevArrow: '',
            nextArrow: '',
            slidesToScroll: 1
        });
    }
    // Карусель;
    $('div.goods-carousel div.items').slick({
        arrows: true,
        dots: false,
        slidesToShow: 6,
        slidesToScroll: 6,
        infinite : true,
        centerMode: true,
        responsive: [
            {
                breakpoint: 360,
                settings: {
                    slidesToShow: 1,
                    arrows: false
                }
            },
            {
                breakpoint: 580,
                settings: {
                    slidesToShow: 2,
                    arrows: false
                }
            },
            {
                breakpoint: 695,
                settings: {
                    slidesToShow: 3

                }
            },
            {
                breakpoint: 900,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1198,
                settings: {
                    slidesToShow: 5
                }
            }
        ]
    });


    // Инициализация скрипт;
    if (typeof init_ajax == 'function') {
        init_ajax();
    }
    // Инициализация календар;
    if($("input.date-input").length > 0){
        console.log('count = '+$("input.date-input").length);
        $("input.date-input").date_input();
    }else{

    }
    // Инилизация карта;
    if($('#poligon').length > 0){
        ymaps_re();
    }

    // Диапозон цены:
    if($( "#slider-price").length > 0){
        $( "#slider-price" ).slider({
            range: true,
            min: 0,
            max: 50000,
            values: [1000,20000],
            slide: function( event, ui ) {
                //Поле минимального значения
                $("div.form__filter.prices input.min" ).val(ui.values[ 0 ]);
                //Поле максимального значения
                $("div.form__filter.prices input.max").val(ui.values[1]);			}
        });
        //Записываем значения ползунков в момент загрузки страницы
        //То есть значения по умолчанию
        $( "div.form__filter.prices input.min" ).val($("#slider-price").slider( "values", 0 ));
        $("div.form__filter.prices input.max").val($("#slider-price").slider( "values", 1 ));
    }

    // Смена позиция товара;
    $("#sort").sortable({

        // Область перемещения;
        handle: "div.manager .js-position",
        items: ".sort_item .item",
        // Прозрачность элементов при перетаскивании;
        opacity: 0.5,
        // При перемещения удаляем div.item-space и добавляем оступ для div.item;
        start : function(event,ui) {
            var position = [$('#sort ').sortable("toArray",{attribute : 'data-position'})];
            $("#toArraySet").attr('data-array',position);
           // console.log();
          //  $("div.goods div.items div.item").css("margin", "0px 5px 0px 0px");
          ///  $("div.goods  div.items div.item-space").remove();
        },

        update: function(event, ui) {
            // Вовремя обработке откл плагин;
            $( "#sort" ).sortable('disable');
           // event.response
            var position = $("#toArraySet").data('array').split(',');
            var id = $('#sort').sortable("toArray");
            var dataArray = {
                sortable: []
            };
            for (var i = 0; i < id.length; i++) {
                dataArray.sortable.push({good_id: id[i], pos: position[i]});
            }
            loading('show');
            $.ajax({
                url: window.location.href,
                type: 'POST',
                scriptCharset: "utf-8",
                data: dataArray,
                success: function(data) {
                    $("#toArraySet").attr('data-array','');
                    loading('hide');
                    // После завершения включаем функц-ть сортировка;
                    $( "#sort" ).sortable('enable');
                },
                error: function(data){
                    alert('Ошибка сервера! ' + data.statusText);
                    loading('hide');
                }
            });
        },


    });

    // Запрет выделения;
    $("#sort").disableSelection();

    // Обновления позиция;
    $(document).on('click','.js-position-update',function(){
        var id = $(this).data('id');
        var position = $(this).siblings('input').val();
        $(".manager___shop div.button_load").show();
        $(".manager___shop .res").hide();
        $.ajax({
            url: window.location.pathname,
            type: 'POST',
            data: {sortable:true,  position_update : true, good_id: id, position :position},
            success: function(data) {
                $(".manager___shop div.button_load").hide();
                $(".manager___shop .res").show();
            },
            error: function(){
                alert('Ошибка сервера');
            }
        });
    });
    var inter =0;
    // Активация;
    $(document).on('click','div.manager___shop .js-position-option',function(){
        inter++;
        if(inter >= 3) {
            $("div.manager___shop .option").removeClass('hidden');
            $("div.manager___shop").css({'display': 'block', 'top': '0', 'left': '-4px'});
            $( "#sort" ).sortable('disable');
            // Обнуляем счетчик;
            inter = 0;
        }
        if(inter >= 1) {
            var TimeOut = setTimeout(function () {
                inter = 0;
            }, 2000);
        }

    });
    // Информер;
    $('#header .user-profile span[rel="popover"]').hover( function(){
        $(this).popover('show');
    },function(){
        $(this).popover('hide');
    });

    // Проверка товара история заказа;
    $('div.my-orders table,.my-orders-m table').each(function(index, element) {
        var id = $(this).attr('id');
        //console.log(id);
        if($("#" + id + " input[type='checkbox']:checked").length) {
            $("#" + id + " div.button input[type='button']").prop("disabled", false);
        }else{
            $("#" + id + " div.button input[type='button']").prop("disabled", true);
        }
    });
    $('div.my-orders table,.my-orders-m table').on('click','input[type="checkbox"]',function(index, element) {
        var id = $(this).parents('table').attr('id');
        console.log(id);
        var i = $("#" + id + " input[type='checkbox']:checked").length;
        if(i){
            $("#" + id + " div.button input[type='button']").prop("disabled", false);
        }else{
            $("#" + id + " div.button input[type='button']").prop("disabled", true);
        }
    });

   $("#ak-modal").modal('show');

    // Прогресс бар;
    $('#indicatorContainer').radialIndicator({
        barColor: '#87CEEB',
        barWidth: 5,
        initValue: $("#initValue").val(),
        radius : 40,
        roundCorner: true,
        percentage: true
    });

    // Запускаем одинь раз;
    /*
    if($("#star_time").length) {
        $.post('/ajax/timer-ajax', {
            'time_start': true
        },function(data){
            $("#timer__w").html(data);
            timer_set();
        });
    }
    if($('#begin_time').length) {
        timer_set();
    }*/

    if($('#goods-main-all').length > 0) {

        var limit_g = 0;
        var couts_category;
        var cat_col = 0;

        $("#loadAjaxContent").show();

        // Загрузка контент;
        $.post('/ajax/main-all-goods', {'goods': true}, function (html) {
            if(html.length > 0) {
                $('#goods-main-all').html(html);
                $("#loadAjaxContent").hide();
                shop.reloadBasketSmall(2);
                var inProcessMain = true;
                $(window).scroll(function () {
                    if ($('#goods-main-all .more__load_js').length > 0 && $(window).scrollTop() + $(window).height() >= $(document).height() - 600 && inProcessMain) {

                            inProcessMain = false;
                            $("div.content-load").show();

                            console.log(inProcessMain);

                            var couts_category = $('.more__load_js').attr('data-count');
                            limit_g += 1;
                            couts_category--;

                            if(couts_category < 1) {
                                cat_col += 1;
                                $('#goods-main-all').attr('data-cat-count',cat_col);
                                limit_g = 0;
                            }

                            $('.more__load_js').remove();
                                console.log(couts_category);

                                $.post('/ajax/main-load-goods', {
                                    'goodsLoad': true,
                                    'col': (limit_g ? limit_g : 0),
                                    'cat_col': $('#goods-main-all').attr('data-cat-count')
                                }, function (response) {
                                    if (response.length > 0) {
                                        $('.content-load').remove();
                                        $('#goods-main-all').append(response);
                                        //
                                        if (couts_category < 1) {
                                            couts_category = $('.more__load_js').attr('data-all-cont');
                                        }
                                        $('.more__load_js').attr('data-count', couts_category);
                                        console.log('Loade Content');
                                        inProcessMain = true;
                                    }
                                });


                    }
                });
            }
        });
    }
});

// Таймер обратного отчета;
function timer_set() {
    var beginTime = $("#begin_time").attr('data-time');
    var intervalTime = $("#begin_time").attr('data-interval');
    // Обновляем время;
    var intervalId = setInterval(function () {
        $("#test_time").html(time_begin(true));
        if (beginTime < time_begin() - intervalTime) {
            clearInterval(intervalId);
            AskQuestion('catalog');
        }
    }, 1000);
    // Обработка таймер;
    function time_begin(type) {
        var timeTime = Math.floor(new Date().getTime() / 1000);
        var time_set = intervalTime - (timeTime - beginTime);
        time_set = (time_set > 0 ? time_set : 0);
        return type ? time_set : timeTime;
    }
}

// Инициализация после ответа Ajax;
function init_ajax() {
    // Зум;
    $('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();

}
// Hover - блок для список товаров;
/*
$(document).on('mouseenter','div.mod___goods_list div.block',function() {
    if(!$(this).parents('.mod___goods_list').filter('.goods-carousel').length) {
        $(this).addClass("max_block");
        $(this).children('.row-private').show();
        $(this).children('.prices').css('position','static');
    }
}).on('mouseleave','.block',function() {
    $(this).removeClass("max_block");
    $(this).children('.row-private').hide();
    $(this).children('.prices').css('position','absolute');
});*/
// Показывает быстрый просмотра;
$(document).on('mouseenter','div.goods div.max_block div.images',function() {
    $(this).children('div.images div.compact').show();
}).on('mouseleave','.images',function() {
    $(this).children('div.images div.compact').hide();
});
// Masonry Настройки pad - "int"(Оступ блоки),cols-"int"(Кол.колонок);
function masonry(id,pad,cols){
    var blocks = $(id).children();
    var pad = pad ? pad : 10, cols = cols ? cols : 3, newleft, newtop;
    for(var i = 1; i < blocks.length; i++) {
        if (i % cols == 0) {
            newtop = (blocks[i - cols].offsetTop + blocks[i - cols].offsetHeight) + pad;
            blocks[i].style.top = newtop + "px";
        } else {
            if (blocks[i - cols]) {
                newtop = (blocks[i - cols].offsetTop + blocks[i - cols].offsetHeight) + pad;
                blocks[i].style.top = newtop + "px";
            }
            newleft = (blocks[i - 1].offsetLeft + blocks[i - 1].offsetWidth) + pad;
            blocks[i].style.left = newleft + "px";
        }
    }
}

/*История заказа*/
function orders_open(key){
    return $('#key' + key + ' .groups, #key' + key + ' span.total,#key' + key + ' a.ver_pdf').toggle();
}

// Подключение акции для товара;
function good_discount(good_id) {
    $("div.goods div.item-" + good_id + " div.manager div.discount").toggleClass("disabled");
    $.post(window.location.href, {
        'good_discount': true,
        'good_id': good_id
    });
    return false;
}

// Скрытие товара;
function good_delete(good_id) {
    if (confirm('Скрыть товар?')) {
        $("div.goods div.item-" + good_id).hide();
        $.post(window.location.href, {
            'good_delete': true,
            'good_id': good_id
        });
    }
    return false;
}
// поиск тернера;
$(document).on('click','#searchGoUser',function(){
    $("div.form .alert").html('');
    $.post(window.location.href,{'phone':$("#searchUser").val()},function(response){
        $("div.form .alert").html('<b>ФИО: '+ response.name+'</b><br><b>Промо-код: '+ response.code+'</b><br>').show();
        $("#searchCodeUser").val(response.code);
    },'JSON');
    return false;
});





console.log('scripts.js -- Ok');