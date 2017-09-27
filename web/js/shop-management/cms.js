
var versionScripts = "Cms.js version: 3.4.2@20112016";
console.log(versionScripts);

$(document).ready(function() {

    //Для accordion что отключить автосворачивание
    $('#accordion').collapse({
        toggle: true
    })


    // Изменение цены (вариант);
    $("div#cms-goods, .product-form").on("keyup", "input.price_in, input.comission", function() {
        var curDataVariant = $(this).parents('div[data-variant]').data('variant');
        var price_in = parseFloat($(this).val());
        if($(this).parents("div.variants").find("input.price_in").length > 0){
            var price_in = parseFloat($(this).parents("div.variants").find("input.price_in").val());
        }else{
            var price_in = parseFloat($("div[data-variant="+curDataVariant+"]").find("input.price_in").val());
        }
        var price_out = 0;
        if($(this).parents("div.variants").find("input.comission").length > 0){
            var comission = parseFloat($(this).parents("div.variants").find("input.comission").val());
        }else{
            var comission = parseFloat($("div[data-variant="+curDataVariant+"]").find("input.comission").val());
        }
        var comission_id = 1002;//$("#comission_id").val();
        var count_pack = $("#count_pack").val() * 1;
        if(count_pack <=0){
            count_pack = 1;
        }
        if (comission_id == 1001) price_out = Math.ceil(price_in * count_pack);
        if (comission_id == 1002) price_out = Math.ceil((price_in + price_in * comission / 100) * count_pack);
        if($(this).parents("div.variants").find("input.price_out").length > 0){
            $(this).parents("div.variants").find("input.price_out").val(number_format(price_out, 2, '.', ''));
        }else{
            $("div[data-variant="+curDataVariant+"]").find("input.price_out").val(number_format(price_out, 2, '.', ''));
        }
    }).on("keyup", "input.price_out", function() {
        var curDataVariant = $(this).parents('div[data-variant]').data('variant');
        var price_in = parseFloat($("input.price_in", $(this).parents("div.variants")).val());

        if($(this).parents("div.variants").find("input.price_out").length > 0){
            var price_out = parseFloat($(this).parents("div.variants").find("input.price_out").val());
        }else{
            var price_out = parseFloat($("div[data-variant="+curDataVariant+"]").find("input.price_out").val());
        }

        var comission_id = 1002;//$("#comission_id").val();
        var count_pack = $("#count_pack").val();
        if (comission_id == 1001) price_in = price_out;

        if($(this).parents("div.variants").find("input.comission").length > 0){
            var comission = parseFloat($(this).parents("div.variants").find("input.comission").val());
        }else{
            var comission = parseFloat($("div[data-variant="+curDataVariant+"]").find("input.comission").val());
        }

        price_in = parseFloat((100*price_out)/(100+comission));

        $(this).parents("div.variants").find("input.price_in").val(number_format(price_in, 2, '.', ''));

        if($(this).parents("div.variants").find("input.price_in").length > 0){
            $(this).parents("div.variants").find("input.price_in").val(number_format(price_in, 2, '.', ''));
        }else{
            $("div[data-variant="+curDataVariant+"]").find("input.price_in").val(number_format(price_in, 2, '.', ''));
        }
    });

    //$("div.variants").on("keyup", "input.price_in, input.comission", function() {
    //    var price_in = parseFloat($("input.price_in", $(this).parents("div.variants")).val());
    //    var price_out = 0;
    //    var comission = parseFloat($("input.comission", $(this).parents("div.variants")).val());
    //    var comission_id = 1002;//$("#comission_id").val();
    //    var count_pack = $("#count_pack").val();
    //    if (comission_id == 1001) price_out = Math.ceil(price_in * count_pack);
    //    if (comission_id == 1002) price_out = Math.ceil((price_in + price_in * comission / 100) * count_pack);
    //    $("input.price_out", $(this).parents("div.variants")).val(number_format(price_out, 2, '.', ''));
    //    console.log('price_in = '+price_in);
    //    console.log('comission_id = '+comission_id);
    //    console.log('count_pack = '+count_pack);
    //    console.log('price_out = '+price_out);
    //}).on("keyup", "input.price_out", function() {
    //    var price_in = parseFloat($("input.price_in", $(this).parents("div.variants")).val());
    //    var price_out = parseFloat($("input.price_out", $(this).parents("div.variants")).val());
    //    var comission = 0;
    //    var comission_id = 1002;//$("#comission_id").val();
    //    var count_pack = $("#count_pack").val();
    //    if (comission_id == 1001) price_in = price_out;
    //    if (comission_id == 1002){
    //        comission = parseFloat($("input.comission", $(this).parents("div.variants")).val());
    //        price_in = price_out/(1+(comission/100));
    //    }//comission = Math.ceil((price_out - price_in) * 100 / price_in);
    //    $("input.price_in", $(this).parents("div.variants")).val(number_format(price_in, 2, '.', ''));
    //    $("input.comission", $(this).parents("div.variants")).val(number_format(comission, 2, '.', ''));
    //});

    //Подгрузка commision_id
    $('select.shop').on('change',function(){
        var id = $(this).val();
        $.post(window.location.href, {
                'get_comission': true,
                'shop_id': id
            }, function (data) {
                if(data.status == 'ok'){
                    $("#comission_id").val(data.comission_id);
                    $('.comission').val(data.comission_value);
                }
            }, 'JSON');
    });

    //Обработка неопзнаных тегов
    $('.button_action').on('click',function(){
        var group_id = $(this).parent().find('.choise_group_tag option:selected').val();
        var tag = $(this).parent().find('.unknowtag').text().trim();
        var action = $(this).attr('data-action');
        var variation_id = $(this).closest('tr').attr('variation');
        var el = $(this);

        if(action != '' && tag != ''){
             $.post(window.location.href, {
                'set_unknow_tag': true,
                'action': action,
                'tag_value': tag,
                'group_id': group_id
            }, function (data) {
                if (data.status == 'ok') {
                    if (action == 'add') {
                        $(el).parent().find('.add_unknow_tag').remove();
                        $(el).parent().append('<input type="hidden" class="add_unknow_tag" name="variations['+variation_id+'][tags]['+data.tag_id+']" value="'+data.tag_id+'">');
                    }
                    if (action == 'delete') {
                        $(el).parent().remove();
                    }
                    var id = $(el).attr('data-id');
                    $('.unknow_tag_id_'+id).remove();
                }
            }, 'JSON');
        }
    });

    // Поиск тегов;
    $(document).on("keyup", "div.options input", function() {
        var input = $(this);
        var inputDouble = $(this);
        var index = $(this).parents("div.variation").index("div.variation");
        var tag_group = $(this).attr("group");
        var tag = $(this).val();
        // - - > Evgeniy
        var tag_check = $("div.options").hasClass("tag_check");
        // < - - Evgeniy
        if (tag.length >= 1) {
            $("div.load", input.parents("div.options")).show();
            // - - > Evgeniy
            $.post(baseAjaxPath+'/ajax/get-tags-groups-value-list', {
                // < - - Evgeniy
                'tag_group': tag_group,
                'tag': tag
            }, function (data) {
                //console.log(data);
                $("div.values", input.parents("div.options")).html('').show();
                if (data.length) {
                    for (var i in data) {
                        $("div.values", input.parents("div.options")).append('<div onclick="tag_add(\'' + index + '\', \'' + tag_group + '\', \'' + data[i].id + '\', \'' + data[i].value + '\','+tag_check+');">' + data[i].value + '</div>');
                    }
                    $("div.values", input.parents("div.options")).append('<div onclick="tag_new(\'' + index + '\', \'' + tag_group + '\', \'' + tag + '\');">добавить</div>');
                } else {
                    // - - > Evgeniy
                    if(tag_check == false){
                        // < - - Evgeniy
                        $("div.values", input.parents("div.options")).append('<div onclick="tag_new(\'' + index + '\', \'' + tag_group + '\', \'' + tag + '\');">добавить</div>');
                    }
                }
                $("div.load", input.parents("div.options")).hide();
            }, 'JSON');
        } else {
            $("div.values", input.parents("div.options")).hide();
            $("div.load", input.parents("div.options")).hide();
        }
    }).on("click", "input", function() {
        if ($(this).val() != '') {
            $("div.values", $(this).parents("div.options")).toggle();
        }
    });

    //$(document).on("keyup", "div.options input", function() {
    //    var input = $(this);
    //    var index = $(this).parents("div.variation").index("div.variation");
    //    var tag_group = $(this).attr("group");
    //    var tag = $(this).val();
    //    // - - > Evgeniy
    //    var tag_check = $("div.options").hasClass("tag_check");
    //    // < - - Evgeniy
    //    if (tag.length >= 1) {
    //        $("div.load", input.parents("div.options")).show();
		//	 // - - > Evgeniy
    //        $.post('/cms/goods/', {
		//	// < - - Evgeniy
    //            'tag_group': tag_group,
    //            'tag': tag
    //        }, function (data) {
    //            console.log(data);
    //            $("div.values", input.parents("div.options")).html('').show();
    //            if (data.length) {
    //                for (var i in data) {
    //                    $("div.values", input.parents("div.options")).append('<div onclick="tag_add(\'' + index + '\', \'' + tag_group + '\', \'' + data[i].id + '\', \'' + data[i].value + '\','+tag_check+');">' + data[i].value + '</div>');
    //                }
    //                $("div.values", input.parents("div.options")).append('<div onclick="tag_new(\'' + index + '\', \'' + tag_group + '\', \'' + tag + '\');">добавить</div>');
    //            } else {
    //                // - - > Evgeniy
    //                if(tag_check == false){
    //                    // < - - Evgeniy
    //                    $("div.values", input.parents("div.options")).append('<div onclick="tag_new(\'' + index + '\', \'' + tag_group + '\', \'' + tag + '\');">добавить</div>');
    //                }
    //            }
    //            $("div.load", input.parents("div.options")).hide();
    //        }, 'JSON');
    //    } else {
    //        $("div.values", input.parents("div.options")).hide();
    //        $("div.load", input.parents("div.options")).hide();
    //    }
    //}).on("click", "input", function() {
    //    if ($(this).val() != '') {
    //        $("div.values", $(this).parents("div.options")).toggle();
    //    }
    //});

    /* Отчет */
    $("#cms-transactions").on("click", "span.label", function() {
        $(this).hide();
        $("input", $(this).parents("div.input")).focus();
    }).on("focus", "input", function() {
        $("span.label", $(this).parents("div.input")).hide();
    }).on("blur", "input", function() {
        if ($(this).val() == '') $("span.label", $(this).parents("div.input")).show();
    });

    /*Раздвежно меню*/
    $(document).on('click','.arrow-menu',function(){
        $("div.sidebar-js").toggle(0);
        $(this).toggle();
        $('#br-shadow ').toggle();
        return false;
    });
    $(document).on('click','#br-shadow',function(){
        $('#br-shadow,div.sidebar-js').hide();
        $('.arrow-menu ').show();
    });

    //Преолдер для отчета;
    $("#w1").submit(function(){
        loading('show');
    });

});
function setDateRange(start, end, start_val, end_val){
    ///console.log(start);
    //console.log(end);
    $('#date_begin').val(start);
    $('#date_begin').next('div').empty().append(start_val);
    $('#date_end').val(end);
    $('#date_end').next('div').empty().append(end_val);
    orders_items();
}
// Загрузка визуального редактора;
function wysiwyg(textarea, height) {
    var oFCKeditor = new FCKeditor(textarea);
    oFCKeditor.BasePath = '/systems/fckeditor/';
    oFCKeditor.Height = height;
    oFCKeditor.ReplaceTextarea();
}

// Вывод опций варианта товара;
function variation(variation_id) {
    $("#variation-" + variation_id).slideToggle();
}

// Вывод описания варианта товара;
function variation_description(variation_id) {
    $("#variation-" + variation_id + " div.variation-description div.text").slideToggle();
}

// Вывод фотографий варианта товара;
function variation_images(variation_id) {
    $("#variation-" + variation_id + " div.variation-images div.images").slideToggle();
}

// Удаление фотографии;
function image_delete(image_id) {
    if (confirm('Удалить фотографию?')) {
        $("#image-" + image_id).css("opacity", "0.5");
        $.post(window.location.href, {
            'image_delete': image_id
        }, function () {
            $("#image-" + image_id).remove();
        });
    }
    return false;
}

// Создание тега;
function tag_new(index, tag_group, tag_name) {
    /*
     $.ajax({
     method      : 'POST',
     data        : {'key':key},
     url         : baseAjaxPath+'/ajax/get-variant-form-for-provider',
     dataType    : 'json',
     success     : function(responce){
     if(responce.status == 'OK'){
     $('.blockForNewVariantsForms').append(responce.value);
     }
     }
     });
     return false;
     */
    $.post(baseAjaxPath+'/ajax/set-new-tag-variant', {
        'tag_group': tag_group,
        'tag_name': tag_name
    }, function (tag_id) {
        tag_add(index, tag_group, tag_id, tag_name);
    });
}
//// Создание тега;
//function tag_new(index, tag_group, tag_name) {
//    $.post(window.location.href, {
//        'tag_group': tag_group,
//        'tag_name': tag_name
//    }, function (tag_id) {
//        tag_add(index, tag_group, tag_id, tag_name);
//    });
//}

// Добавление тега;
function tag_add(index, tag_group, tag_id, tag_name,check) {
    if(check == true){
        $('.gl_good_list').append('<option value="' + tag_id +'">' + tag_name +'</option>');
        $('#hidden_lb_tags').append('<input type = "hidden" name = "lb[tag][' + tag_id + ']" value = "' + tag_id +'">');

        $("div.values", $("div.variation").eq(index)).hide();
    }else{
        var variation_id = $("tr.variation").eq(index).attr("variation");
        var variationForOwner = false;


        if($("tr.variation").eq(index).length > 0){
            variationForOwner = $("tr.variation").eq(index);
            variationForOwner = variationForOwner.data('variationforowner');
        }
        if($('.variants[data-key]').eq(index).length > 0){
            variationForOwner = $('.variants').eq(index).data('key');
            //variationForOwner = variationForOwner.data('variationforowner');
        }

        if (variation_id) {
            var variation = 'variations[' + variation_id + ']';
        }else if(variationForOwner){
            var variation = 'variations_add[' + variationForOwner + ']';
        } else {
            var variation = 'variations_add[0]';
        }
        $("div.value-" + tag_group, $("div.variation").eq(index)).append('<span class="tag"><input type="hidden" name="' + variation + '[tags][' + tag_id + ']" value="' + tag_name + '" /> ' + tag_name + ' <a href="/" title="Удалить тег" onclick="$(this).parent().remove(); return false;">X</a></span>');
        $("div.values", $("div.variation").eq(index)).hide();
    }
}

function clearInputOrTextArea(check,dest){
    var current = $('.' + dest + ' option:selected').val();
    if(current){
        //Удаляем нужный select
        $('.' + dest + ' option:selected').remove();
        //alert(current);
        //Удаляем скрытые инпут
        if(typeof current == Array){
            for(var i = 0;i<length.current; i++){
                $('.' + dest + ' input[type="hidden"][value="'+current[i]+'"]').remove();
            }
        }
        else{
            $('.' + dest + ' input[type="hidden"][value="'+current+'"]').remove();
        }

    }
}
/*----Интсрумент список товаров-----*/
function addChain(){
    if ($('.gl2 option:selected').val() != '') {
        $(".gl_chain").append($('<option value="' + $(".gl2").val() + '">' + $(".gl2 :selected").text() + '</option>'));
        $('#counts').prepend('<input name="gl[chain_id][' + $(".gl2").val() + ']" type="hidden" value="' + $(".gl2").val() + '" />');
        $('.add_chain_h').hide();
        return true;
    }else{
        $(".gl_chain").append($('<option value="' + $(".gl1").val() + '">' + $(".gl1 :selected").text() + '</option>'));
        $('#counts').prepend('<input name="gl[chain_id][' + $(".gl1").val() + ']" type="hidden" value="' + $(".gl1").val() + '" />');
        $('.add_chain_h').hide();
        return true;
    }
}

function showGL_goods(){
    $('.showGL_goods').hide();
    $('.show_add_good_id').show();
}

function getValue(check){
    var main;
    $('.gl_chain option:selected').each(function(){
        this.selected=true;
    });
    if(check) {
        if ($('.gl2 option:selected').val() != '') {
            $(".gl_chain").append($('<option value="' + $(".gl2").val() + '">' + $(".gl2 :selected").text() + '</option>'));
            return true;
        }else{
            $(".gl_chain").append($('<option value="' + $(".gl1").val() + '">' + $(".gl1 :selected").text() + '</option>'));
            return true;
        }
    }else{
        $(".gl_chain :selected").remove();
    }
}
function showLevel(){
    $('.gl2').empty();
    $.post(window.location.href, {
        'goods_list': true,
        'gl1' : $('.gl1').val()
    },function(data) {
        var seloption = '<option value="">---</option>';
        //arr.forEach(function(data, i, arr) {
        $.each(data, function (key, value) {
            seloption += '<option value="' + key + '"> - ' + value + '</option>';
        });
        //});
        $('.gl2').append(seloption);
    },'JSON');
    return false;
}

function addGLGood(check){
    if(check){
        $(".gl_good_list").append($('<option value="' + $(".add_good_id_list").val() + '">' + $(".add_good_id_list").val() + '</option>'));
        $('#counts2').prepend('<input class="good_id_' + $(".add_good_id_list").val() + '" name="gl[good_id][' + $(".add_good_id_list").val() + ']" type="hidden" value="' + $(".add_good_id_list").val() + '" />');
        $('.add_good_id_list').val('');
        $('.show_add_good_id').hide();
        $('.showGL_goods').show();

    }else{
        var ert = $('.gl_good_list :selected').val();
        $('.good_id_' + ert +'').remove();
        //alert($('.good_id_' + ert +''));
        $('.gl_good_list :selected').remove();
    }
}
function delGL(list_id){
    var link_to = '/cms/goods_list/del/';
    $.post(link_to, {
        'ajax_post': true,
        'list_id' : list_id
    },function(response) {
        $('.gl_id_' + list_id).remove();
    });
    return false;
}

/* REPORTS AND TOOLS BEGIN */

$(document).ready(function() {
    // Поиск записей для фильтра;
    $("#cms-reports div.filter div.auto-search").on("keyup", "input.search", function() {
        var search_name = $(this).attr("search");
        var search_value = $(this).val();
        auto_search(search_name, search_value, 1500);
    }).on("click", "input.search", function() {
        var search_name = $(this).attr("search");
        var search_value = $(this).val();
        auto_search(search_name, search_value, 0);
    }).on("click", "div.auto-search-label", function() {
        $(this).hide();
        $("input.search", $(this).parents("div.auto-search")).focus();
    }).on("focus", "input.search", function() {
        $("div.auto-search-label", $(this).parents("div.auto-search")).hide();
    }).on("blur", "input.search", function() {
        if ($(this).val() == '') {
            $("div.auto-search-label", $(this).parents("div.auto-search")).show();
        }
    }).on("click", "div.auto-search-all", function() {
        var search_name = $("input.search", $(this).parents("div.auto-search")).attr("search");
        $("input.search", $(this).parents("div.auto-search")).val('');
        $("div.auto-search-label", $(this).parents("div.auto-search")).show();
        auto_search_all(search_name);
    });
});

// Итог
function addAvgTable() {
    var t_sum = 0;
    var p_sum = 0;
    var s_sum = 0;
    var h_sum = 0;
    var c_sum = 0;
    var sport_sum = 0;
    var p_other = 0;
    var s_sum_b = 0;
    var c_sum_b = 0;
    var ii = 0;
    var all_sum_count = 0;
    var all_prod_count = 0;
    var all_home_count = 0;
    var all_other_count = 0;
    var all_sport_count = 0;

    $('.buy_all_sum').each(function (i) {
        t_sum += parseInt($(this).text().replace(/\s+/g, ''));
        ii += 1;
    });
    $('.s1').text(numeral(t_sum / ii).format('0,0 $'));
    $('.buy_prod').each(function () {
        p_sum += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.buy_home').each(function () {
        h_sum += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.buy_other').each(function () {
        p_other += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.s2').text(numeral(p_sum / ii).format('0,0 $'));
    $('.buy_staff').each(function () {
        s_sum += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.buy_staff_b').each(function () {
        s_sum_b += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.s-home').text(numeral(h_sum / ii).format('0,0 $'));

    $('.buy_sport').each(function () {
        sport_sum += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.s-sport').text(numeral(sport_sum / ii).format('0,0 $'));


    $('.buy_client').each(function () {
        c_sum += parseInt($(this).text().replace(/\s+/g, ''));
    });
    $('.buy_client_b').each(function () {
        c_sum_b += parseInt($(this).text().replace(/\s+/g, ''));
    });
    /*$('.s4').text(numeral(c_sum / ii).format('0,0 $'));*/

    /* Итог кол-во заказов */
    $('.buy_all_sum').find('span').each(function () {
        all_sum_count += parseInt($(this).text().replace(/\s+|\(|\)/g, ''));
    });
    $('.buy_prod').find('span').each(function () {
        all_prod_count += parseInt($(this).text().replace(/\s+|\(|\)/g, ''));
    });
    $('.buy_home').find('span').each(function () {
        all_home_count += parseInt($(this).text().replace(/\s+|\(|\)/g, ''));
    });
    $('.buy_sport').find('span').each(function () {
        all_sport_count += parseInt($(this).text().replace(/\s+|\(|\)/g, ''));
    });
    $('.buy_other').find('span').each(function () {
        all_other_count += parseInt($(this).text().replace(/\s+|\(|\)/g, ''));
    });

    $('#sum1').text(numeral(t_sum).format('0,0') + ' ('+all_sum_count+')'); // Всего
    $('#sum2').text(numeral(p_sum).format('0,0') + ' ('+all_prod_count+')'); // Продукты
    $('#sum2_1').text(numeral(h_sum).format('0,0') + ' ('+all_home_count+')'); // Товары для дома
    $('#sum_sport').text(numeral(sport_sum).format('0,0') + ' ('+all_sport_count+')'); // Товары для дома
    $('#sum3').text(numeral(p_other).format('0,0') + ' ('+all_other_count+')'); // Прочее
    /*$('#sum4').text(numeral(s_sum).format('0,0')); // Продукты (сотруд.)
    $('#sum5').text(numeral(s_sum_b).format('0,0')); // Продукты (сотруд./бонусы)
    $('#sum6').text(numeral(c_sum).format('0,0')); // Продукты (клиенты)
    $('#sum7').text(numeral(c_sum_b).format('0,0')); // Продукты (клиенты./бонусы)*/

}

function strDate(d) {
    var weekDays = ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'];
    var isoDateStr = d.split('.').reverse().join('-');
    var date_str = new Date(isoDateStr);
    return weekDays[date_str.getDay()];
}

function appendDataTable(date, sum, sum_count, prod, sum_prod_count, home, home_count, money_sportpit, money_sportpit_count, other, sum_count_other, r_staff, r_bonus, r_client, r_client_b) {
    numeral.language('ru');
    var sum_all = numeral(sum).format('0,0');
    var prod1 = numeral(prod).format('0,0');
    var home = numeral(home).format('0,0');
    var money_sportpit = numeral(money_sportpit).format('0,0');
    var other1 = numeral(other).format('0,0');
    var r_staff1 = numeral(r_staff).format('0,0');
    var r_bonus1 = numeral(r_bonus).format('0,0');
    var r_client1 = numeral(r_client).format('0,0');
    var r_client_b1 = numeral(r_client_b).format('0,0');

    var html = '';
    var nDate = date.replace(/\.20/g, '.');

    html += (strDate(date) == 'Сб' || strDate(date) == 'Вс') ? '<tr class="warning">' : '<tr>';
    html += '<th>' + nDate + '<span> ' + strDate(date) + '</span></th>';
    html += '<td class="buy_all_sum">' + sum_all + '<span> (' + sum_count + ')</span></td>';
    html += '<td class="buy_prod">' + prod1 + '<span> (' + sum_prod_count + ')</span></td>';
    html += '<td class="buy_home">' + home + '<span> (' + home_count + ')</span></td>';
    html += '<td class="buy_sport">' + money_sportpit + '<span> (' + money_sportpit_count + ')</span></td>';
    html += '<td class="buy_other">' + other1 + '<span> (' + sum_count_other + ')</span></td>';
    /*html += '<td class="buy_staff">' + r_staff1 + '</td>';
    html += '<td class="buy_staff_b">' + r_bonus1 + '</td>';
    html += '<td class="buy_client">' + r_client1 + '</td>';
    html += '<td class="buy_client_b">' + r_client_b1 + '</td>';*/
    html += '</tr>';
    $('#main-info').append(html);
}

function clearData() {
    $('#main-info').empty();
}

/*
function testGraph(arr_date, staff, client) {
    $('#container_chart').highcharts({
        chart: {
            type: 'bar'
        },
        title: {
            text: 'График продаж с ' + $('#date1').val() + ' по ' + $('#date2').val()
        },
        xAxis: {
            categories: arr_date
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Сумма, руб.'
            }
        },
        legend: {
            reversed: true
        },
        plotOptions: {
            series: {
                stacking: 'normal',
                dataLabels: {
                    enabled: true,
                    color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white'
                }
            }
        },
        series: [{
            name: 'Сотрудники',
            data: staff
        }, {
            name: 'Клиенты',
            data: client
        }]
    });
}*/


function drawStacked(arr_date, staff, client) {
    var dataArray = [['Дата', 'Клиенты', {role: 'annotation'}, 'Сотрудники', {role: 'annotation'}]];
    for (var n = 0; n < arr_date.length; n++) {
        dataArray.push([arr_date[n] + ' ' + strDate(arr_date[n]), client[n], client[n], staff[n], staff[n]]);
    }

    var data = new google.visualization.arrayToDataTable(dataArray);
    var paddingHeight = 60;
    var rowHeight = data.getNumberOfRows() * 25;
    var chartHeight = rowHeight + paddingHeight;


    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1,
        { calc: "stringify",
            sourceColumn: 1,
            type: "string",
            role: "annotation" },
        2]);

    var options = {
        title: 'График продаж с ' + $('#date1').val() + ' по ' + $('#date2').val(),
        chartArea: {width: '70%', height: '85%'},
        height: chartHeight,
        width: '100%',
        isStacked: true,
        backgroundColor: { fill: "#f2f2f2" },
        annotations: {
            highContrast: true
        },
        hAxis: {
            title: 'Сумма, руб.',
            minValue: 0,
        },
        vAxis: {
            title: 'Дата'
        }
    };
    var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
    chart.draw(data, options);
}

function sales_report() {
    $.ajax({
        type: 'POST',
        url: '/reports/sales-report',
        data: jQuery('#getForm').serialize(),
        dataType: 'JSON',
        beforeSend: function () {
            $('#formPost').attr('disabled', 'disabled');
            $('.loader').css('display', 'inline-block');
            $('#report-sales').find('#table1').fadeOut();
            $('#report-sales').find('#table2').fadeOut();
            $('#report-sales').find('#chart_div').css('opacity', '0');
        },
        success: function (data) {
            var arr_date = [];
            var arr_staff = [];
            var arr_client = [];

            clearData();
            jQuery.each(data.response, function (k, val) {
                appendDataTable(val.date_sum, val.money, val.sum_count, val.money_products, val.sum_prod_count, val.money_products_home, val.sum_home_count, val.money_sportpit, val.money_sportpit_count, val.money_other, val.sum_count_other, val.money_products_staff, val.bonus_products_staff, val.money_products_client, val.bonus_products_client);
                // Добавление в массив (для графика)
                arr_date.push(val.date_sum);
                arr_staff.push(val.money_products_staff);
                arr_client.push(val.money_products_client);
            });
            addAvgTable();
            google.charts.setOnLoadCallback(drawStacked(arr_date, arr_staff, arr_client));
            /*testGraph(arr_date, arr_staff, arr_client);*/
        },
        error: function (request, status, error) {
            console.log(request.status);
        },
        complete: function () {
            $('#formPost').removeAttr('disabled');
            $('.loader').hide();
            $('#report-sales #table1').fadeIn();
            $('#report-sales #table2').fadeIn();
            $('#report-sales #chart_div').css('opacity', '1');
        }
    });
}

// Раскрыть/Закрыть;
$(document).on('click','#cms-reports .toggleClose',function(){
    $(this).parents('tbody').children('tr.toggle').toggle();
});
if($(window).width() <= 1198) {
    $(document).on('click','#cms-reports div.items div.item',function(){
        var id= $(this).children('table').attr('id');
        $('#' + id + ' tr.toggle').toggle();
        var toggleWidth = $(this).children('table').width() == 1198 ? "100%" : "1198px";
        $('#' + id).animate({ width: toggleWidth });
        $('#' + id + ' tr.delivery').add('#' + id + ' tr.total').add('#' + id + ' th.tog').toggle();
    });
}
// Раскрыть все;
$(document).on('click','#cms-reports .toggleCloseAll',function(){
    $("tr.toggle").toggle();
    $(this).text(function(i, text){
        return text === "Закрыть все" ? "Открыть все" : "Закрыть все";
    });
    return false;
});
// Раскрыть все;
$(document).on('click','#panel-content .toggleCloseAllNew',function(){
    $('#repost_order_list .goods-item').toggleClass('hidden');
    $("#repost_order_list .order-info .glyphicon").toggleClass('glyphicon-chevron-up').toggleClass('glyphicon-chevron-down');
    $(this).text(function(i, text){
        return text === "Закрыть все" ? "Открыть все" : "Закрыть все";
    });
    return false;
});

// Загрузка продаж;
function orders_items() {

    var inputs = $("#cms-reports form").serialize();
    $("#cms-reports div.info").html('Загрузка...');
    $("#cms-reports div.items").html('');
    $("#cms-reports div.filter div.label").show();
    $("#cms-reports div.filter input.search").val('');
    $("#cms-reports div.filter div.auto-search-items").hide();
    $("#cms-reports div.filter div.filter-load").show();
    var typeUser = {
        1: "Администратор клуба",
        2: "Персональный тренер",
        3: "Тренер групповых тренеровок",
        4: "Управление",
        5: "Фитнес консультант"
    }
//    $.post(window.location.href, {
    $.post('/reports/orders_items', {
        'orders_items': inputs
    }, function(data) {
        $("#cms-reports div.info").html('');
        $("#cms-reports div.info").append('<div class="info-count">' + (data.goods ? 'Товаров' : 'Заказов') + '<br /><b>' + data.info.count + '</b></div>');
        $("#cms-reports div.info").append('<div class="info-goods">Полная стоимость = <b>' + data.info.goods.price + '</b><br />Скидка = <b>' + data.info.goods.discount + '</b><br />Расчет бонусами = <b>' + data.info.goods.bonus + '</b><br />Прибыль = <b>' + data.info.comissions.sum + '</b><br/>Выручка = <b>' + data.info.goods.sum + '</b><br/>Доставка = <b>' + data.info.goods.delivery + '</b></div>');
        $("#cms-reports div.info").append('<div class="info-pays">Выплаты поставщикам = <b>' + data.info.pays.goods + '</b><br />Кэшбэк = <b>' + data.info.pays.fee + '</b><br />Себестоимость = <b>' + data.info.pays.sum + '</b><br/>Комиссия за товар = <b>' + data.info.comissions.goods + '</b><br /><div class="info-cancel">Отмены = <b>' + data.info.goods.cancel + '</b></div></div>');
       // $("#cms-reports div.info").append('<div class="info-comission">Комиссия за товар = <b>' + data.info.comissions.goods + '</b><br />Удержания = <b>' + data.info.comissions.minus + '</b><span class="info" title="Скидки по промо-кодам, оплаты бонусами и выплаты таксистам">?</span><br /><br /><br><div class="info-cancel">Отмены = <b>' + data.info.goods.cancel + '</b><span class="info" title="Сумма отмененных заказов">?</span></div></div>');
        $("#cms-reports div.info").append('<div class="info-goods-all">Продукты = <b>'+data.info.sales.food+' р.</b><br>Спортивное питание = <b>'+data.info.sales.sportpit+' р.</b><br> Товары для дома = <b>'+data.info.sales.home_goods+' р.</b><br>Средняя комиссия = <b>'+Math.round((data.info.sales.comission/data.info.sales.count_goods)*100)/100+' %</b><br>Средняя стоимость 1 кг = <b>'+data.info.sales.kg_cost+' р.</b><br><a href="#" class="dashed window" onclick="$(\'.window-content\').toggle(); return false">Средний процент <div class="window-content"><div class="item">Продукты = '+data.info.sales.real_food+' % </div><br><div class="item">Товары для дома = '+data.info.sales.real_home+' % </div><br><div class="item">Спортпит = '+data.info.sales.real_sportpit+' % </div></div></a> <b></b>');

//        $("#cms-reports div.info").append('<div class="clear">Data begin: '+data.debug.info.date_begin+'<br>'+'Data end: '+data.debug.info.date_end);
//        $("#cms-reports div.info").append('<br>SQL: '+data.debug.sql+'<br>');
//        $("#cms-reports div.info").append('<br>SQL: '+data.debug.sql1+'<br>');
//        $("#cms-reports div.info").append('<br>SQL: '+data.debug.sql2+'<br></div>');
        $("#cms-reports div.info").append('<div class="clear"></div>');
        $("#cms-reports div.info").append('<div class="exports-xls" onclick="return exports_xls(\'orders\');"></div>');
        $("#cms-reports div.info").append('<div class="exports-xml" onclick="return exports_xml(\'orders\');"></div>');
        $("#cms-reports div.info").append('<div class="toggleCloseAll">Открыть все</div>');
        var template = '';
        if (data.goods) {
            template += '<table cellpadding="0" cellspacing="0" border="0" class="reports">';
            template += '<tr>';
            template += '<th colspan="2"></th>';
            template += '<th class="money tog">Сумма входная</th>';
            template += '<th class="money tog">Сумма выходная</th>';
            template += '<th class="comission tog">Наценка</th>';
            template += '<th class="count tog">Количество (текущее)</th>';
            template += '<th class="money tog">Скидки</th>';
            template += '<th class="money tog">Бонусы</th>';
            template += '<th class="money tog">Итого</th>';
            template += '<th class="count tog">Остаток</th>';
            template += '</tr>';
            for (var i in data.goods) {
                template += '<tr class="+ order-good-' + data.goods[i].good_id + ((k % 2 == 0) ? '' : ' grey') + '">';
                template += '<td class="image"><div class="variation-id">V' + data.goods[i].variation_id + '' +
                    //'</div><a href="/catalog/' + data.goods[i].good_id + '" target="_blank"><img src="' + data.goods[i].good_image + '" alt="" /></a></td>';
                    '</div><a href="/product/update?id=' + data.goods[i].good_id + '" target="_blank"><img src="' + data.goods[i].good_image + '" alt="" /></a></td>';
                template += '<td class="name">';
                template += '<div class="shop">' + data.goods[i].shop_name + '</div>';
                template += '<div class="good-name">' + data.goods[i].good_name + '</div>';
                template += '<div class="tags">' + data.goods[i].tags + '</div>';
                template += '</td>';
                template += '<td class="money">' + data.goods[i].price_in + ' руб.</td>';
                template += '<td class="money">' + data.goods[i].price_out + ' руб.</td>';
                template += '<td class="comission">' + data.goods[i].comission + ' руб. <br /><span class="comission-percent">'+data.goods[i].comission_percent+'%</span></td>';
                template += '<td class="count">' + data.goods[i].count + ' шт.</td>';
                template += '<td class="money">' + data.goods[i].discount + ' руб.</td>';
                template += '<td class="bonus">' + data.goods[i].bonus + ' β.</td>';
                template += '<td class="money">' + data.goods[i].money + ' руб.</td>';
                template += '<td class="count">' + (data.goods[i].count_all ? data.goods[i].count_all + ' шт.' : '') + '</td>';
                template += '</tr>';
            }
            template += '</table>';
        }
        else {
            for (var i in data.orders) {

                template += '<div class="item">';
                template += '<table cellpadding="0" cellspacing="0" border="0" class="reports" id="' + data.orders[i].order_id + '">';
                template += '<tr>';
                template += '<th colspan="3">';
                template += '<div class="order"><span>#' + data.orders[i].order_id + '</span>, ' + data.orders[i].date + '   <span class="total">— Сумма: '+ data.orders[i].money +' руб.</span></div>';
                template += '<div class="user i-'+ data.orders[i].order_id +' '+(data.orders[i].negative_review > 0 ? " open": "")+' "><span>Покупатель:</span> ' + data.orders[i].user.name + ', ' + data.orders[i].user.phone + (data.orders[i].user.staff > 0 ? '<span class="staff">сотрудник '+(data.orders[i].user.typeof > 0 ? '(' + typeUser[data.orders[i].user.typeof] + ')' : '')  +'</span>' : '') + '<span title="Есть претензия?" class="button-res'+(data.orders[i].negative_review > 0 ? " open":"")+'" onclick="modal_admin('+data.orders[i].order_id+')">?</span> </div>';
                template += '<div class="code"><span>Промо-код:</span> ' + (data.orders[i].code ? (data.orders[i].code.code + ', ' + data.orders[i].code.name) : 'нет') + '</div>';
                if(data.orders[i].groups[0]) {
                    //console.log(data.orders[i].groups[0]);
                    template += '<div class="code del"><span>' + ((data.orders[i].groups[0]) ? data.orders[i].groups[0].delivery_name : '--') + ':</span> <b>' + data.orders[i].groups[0].delivery_address + ' ' + data.orders[i].groups[0].delivery_date;
                    +'</b></div>';
                }else{
                    template += '<div class="code del"><b>Error - delivery</b></div>';
                }
                template += '<div class="comments"><span>Комментарий:</span> ' + (data.orders[i].comments ? '<span class="comments">' + data.orders[i].comments + '</span>' : 'нет') + '</div>';
                template += '<div class="comments"><span>Комментарий call-центра:</span> ' + (data.orders[i].comments_call_center ? '<b class="text-danger">' + data.orders[i].comments_call_center + '</b>' : 'нет') + '</div>';
                template += '<div class="comments"><span><a href="/reports/profile?id='+data.orders[i].user.id +'">Посмотреть профайл</a></span></div>';
                template +=  '<div class="del"></div>';
                template += '</th>';
                template += '<th class="money tog">Цена входная</th>';
                template += '<th class="comission tog">Наценка</th>';
                template += '<th class="money tog">Цена выходная</th>';
                template += '<th class="count tog">Количество текущее</br>заказано</br>(шт.)</th>';
                template += '<th class="count tog">Вес</th>';
                template += '<th class="money tog">Скидка </th>';

                template += '<th class="money tog">Бонусы</th>';
                template += '<th class="money tog"><div class="toggleClose"></div>Сумма</th>';
                template += '</tr>';
                for (var j in data.orders[i].groups) {
                    for (var k in data.orders[i].groups[j].goods) {
                        template += '<tr class="toggle order-item-' + data.orders[i].groups[j].goods[k].order_item_id + ((k % 2 == 0) ? '' : ' grey') + ((data.orders[i].groups[j].goods[k].status == 1) ? '' : ' disabled') + '" count="'+data.orders[i].groups[j].goods[k].count+'">';
                        template += '<td class="image"><div class="variation-id">V' + data.orders[i].groups[j].goods[k].variation_id + '</div><a href="/product/update?id=' + data.orders[i].groups[j].goods[k].good_id + '" target="_blank"><img src="' + data.orders[i].groups[j].goods[k].good_image + '" alt="" /></a></td>';
                        template += '<td class="name">';
                        template += '<div class="shop">' + data.orders[i].groups[j].goods[k].shop_name + ' (' + data.orders[i].groups[j].goods[k].store_address  +' )</div>';
                        template += '<div class="good-name">' + data.orders[i].groups[j].goods[k].good_name + '</div>';
                        template += '<div class="tags">' + data.orders[i].groups[j].goods[k].tags + '</div>';
                        if (data.orders[i].groups[j].goods[k].status == 1 && data.orders[i].groups[j].goods[k].status_id != 1001) {
                            template += '<span class="cancel" title="Возврат средств за товар" onclick="order_item_cancel_now(\'' + data.orders[i].groups[j].goods[k].order_item_id + '\',\'' + data.orders[i].groups[j].goods[k].order_group_id + '\',\'' + data.orders[i].groups[j].goods[k].order_id + '\');">отмена</span>'
                        }
                        /*else {
                            if (data.orders[i].groups[j].goods[k].status_id != 1008) {
                                template += '<span class="return" title="Возврат товара поставщику" onclick="order_item_return(\'' + data.orders[i].groups[j].goods[k].order_item_id + '\');">возврат</span>'
                            }
                        }*/
                        template += '</td>';
                        template += '<td class="status">';
                        template += '<span' + (data.orders[i].groups[j].goods[k].status_id ? ' class="status" onclick="return order_item_status(\'' + data.orders[i].groups[j].goods[k].order_item_id + '\');"' : ' class="status-no"') + '>' + data.orders[i].groups[j].goods[k].status_name + '</span>';
                        //template += '<span class="status-edit">...</span>';
                        if (data.orders[i].groups[j].goods[k].status == 0) {
                            template += '<br /><span class="status-cancel">отменен</span>';
                        }
                        template += '<div id="item-status-' + data.orders[i].groups[j].goods[k].order_item_id + '" class="item-status"></div>';
                        template += '</td>';
                        template += '<td class="money">' + data.orders[i].groups[j].goods[k].price_in + ' руб.</td>';
                        template += '<td class="comission">' + data.orders[i].groups[j].goods[k].comission + ' руб.<br /><span class="comission-percent">' + data.orders[i].groups[j].goods[k].comission_percent + '%</span></td>';
                        template += '<td class="money">' + data.orders[i].groups[j].goods[k].price_out + ' руб.</td>';
                        template += '<td class="count order-item-' + data.orders[i].groups[j].goods[k].order_item_id+'-count">' + data.orders[i].groups[j].goods[k].count + ' шт.<br /><span class="count-save">'+data.orders[i].groups[j].goods[k].count_save+' шт.</span></td>';
                        template += '<td class="count">' + data.orders[i].groups[j].goods[k].weight + ' г.</td>';
                        template += '<td class="money">' + data.orders[i].groups[j].goods[k].discount + ' руб.</td>';

                        template += '<td class="bonus">' + data.orders[i].groups[j].goods[k].bonus + ' β.</td>';
                        template += '<td class="money">' + data.orders[i].groups[j].goods[k].money + ' руб.</td>';
                        template += '</tr>';
                    }
                    template += '<tr class="delivery delivery-item-' + data.orders[i].groups[j].order_group_id + '"' + ((data.orders[i].groups[j].delivery_price > 0) ? '' : ' disabled') + '>';
                    template += '<td class="description" colspan="9">';
                    template += data.orders[i].groups[j].delivery_name + ' – ' + data.orders[i].groups[j].delivery_address + '<br />' + data.orders[i].groups[j].delivery_date;
                    if (data.orders[i].groups[j].delivery_price > 0) {
                        template += '<span class="delivery-cancel" title="Возврат средств покупателю за доставку" onclick="order_delivery_cancel(\'' + data.orders[i].groups[j].order_group_id + '\');">отмена доставки</span>';
                        template += '<span class="delivery-double" title="Двойная доставка (списать средства с покупателя и начислить курьеру)" onclick="order_delivery_double(\'' + data.orders[i].groups[j].order_group_id + '\');">двойная доставка</span>';
                    }
                    template += '</td>';
                    template += '<td></td>';
                    template += '<td class="money">' + data.orders[i].groups[j].delivery_price + ' руб.</td>';
                    template += '</tr>';
                }
                template += '<tr class="total">';
                template += '<td colspan="3" style="padding-right: 12px; text-align: right;">Итого:</td>';
                template += '<td class="money">' + data.orders[i].price_in + ' руб.</td>';
                template += '<td class="comission">' + data.orders[i].comission + ' руб.</td>';
                template += '<td class="money">' + data.orders[i].price_out + ' руб.</td>';
                template += '<td class="count">' + data.orders[i].count + ' шт.<br /><span class="count-save">'+data.orders[i].count_save+' шт.</span></td>';
                template += '<td class="count">' + data.orders[i].weight + ' г.</td>';
                template += '<td class="money">' + data.orders[i].discount + ' руб.</td>';

                template += '<td class="bonus">' + data.orders[i].bonus + ' β.</td>';
                template += '<td class="money">' + data.orders[i].money + ' руб.</td>';
                template += '</tr>';
                template += '</table>';
                template += '</div>';
            }
        }

        $("#cms-reports div.items").append(template);
        // Права доступа;
        if(!$("#can").length) $("#cms-reports .label-default").remove();

        $("#cms-reports div.filter div.filter-load").hide();
    }, 'JSON');
}
// Открытие окон;
function modal_admin(paramets) {
    //$("#orders input.order-id").val(paramets);
    loading('show');
    $.post(window.location.href, {'modal_order':true,'order_id':paramets}, function(data) {
        $("#orders div.modal-body").html($(data).find("#orders div.modal-body").html());
        $('#orders').modal({show:true});
        loading('hide');
    });

    return false;
}


// Добавить в негатив;
function addNegative(order_id) {
    var order_id = $("#orders input.order-id").val();
    var negative_status = $("#orders input.negative_status:checked").length;
    var comments = $("#orders textarea.comments").val();
    var btn = $("#orders button.btn");
    var elemeint = $('#repost_order_list div.items[data-key="'+order_id+'"]');
    console.log(order_id + '  ' + negative_status + '  ' + comments);

    if(!comments) {
        $("#orders .alert").text('Нужно заполнить коммент!').show();
        return false;
    }
    if(negative_status) {
        elemeint.find('.order-info').addClass('open');
        elemeint.find('.button-res').addClass('text-danger');
    }else{
        elemeint.find('.order-info').removeClass('open');
        elemeint.find('.button-res').removeClass('text-danger');
    }
    btn.button('loading');
    $.post('/ajax-reports/negative-status', {'negative':true,'order_id':order_id, 'negative_status':negative_status,'comments': comments}, function(data) {
        console.log(data);
        btn.button('reset');
        $('#orders').modal('hide');
        $("#orders .alert").text('').hide();

    });
    return false;
}

// Загрузка доставок;
function orders_delivery() {
    var inputs = $("#cms-reports form").serialize();
    $("#cms-reports div.info").html('Загрузка...');
    $("#cms-reports div.items").html('');
    $("#cms-reports div.filter div.label").show();
    $("#cms-reports div.filter input.search").val('');
    $("#cms-reports div.filter div.auto-search-items").hide();
    $("#cms-reports div.filter div.filter-load").show();
    $.post('/reports/orders_delivery', {
        'orders_delivery': inputs
    }, function(data) {
        $("#cms-reports div.info").html('');
        $("#cms-reports div.info").append('<div class="info-count">Заказов<br /><b>' + data.info.count + '</b></div>');
        $("#cms-reports div.info").append('<div class="info-delivery">Оплата покупателями = <b>' + data.info.price + '</b><span class="info" title="Оплаты доставки заказов принятых поставщиками">?</span><br />Начислено таксистам = <b>' + data.info.delivery_price + '</b><span class="info" title="Сумма может корректироваться в процессе работы курьерской службы">?</span><br />Доплаты таксистам = <b>' + data.info.delivery_surcharge + '</b><br />Итого = <b>' + data.info.sum + '</b></div>');
        $("#cms-reports div.info").append('<div class="info-pays">Фактические выплаты = <b>' + data.info.delivery_pays + '</b><span class="info" title="Сумма фактических выплат за выбранный период">?</span></div>');
        $("#cms-reports div.info").append('<div class="clear"></div>');
        var template = '';
        template += '<table cellpadding="0" cellspacing="0" border="0" class="reports">';
        template += '<tr>';
        template += '<th class="datetime">Дата доставки</th>';
        template += '<th class="order-id">Заказ</th>';
        template += '<th class="money">Стоимость</th>';
        template += '<th class="user-name">Водитель</th>';
        template += '<th class="datetime">Дата приема заявки</th>';
        template += '<th class="datetime">Дата выдачи товара</th>';
        template += '<th class="count">Рейтинг</th>';
        template += '<th class="money">Выплата</th>';
        template += '<th class="money">Доплата</th>';
        template += '<th class="status">Статус</th>';
        template += '</tr>';
        for (var i in data.orders) {
            template += '<tr class="item' + ((i % 2 == 0) ? '' : ' grey') + '">';
            if (data.orders[i].select && data.orders[i].select.date_end) {
                template += '<td class="datetime">' + data.orders[i].delivery_date + '</td>';
            } else {
                template += '<td class="datetime">[' + data.orders[i].delivery_date + ']</td>';
            }
            template += '<td class="order-id">' + data.orders[i].order_id + '</td>';
            template += '<td class="money min">' + data.orders[i].delivery_price + ' руб.</td>';
            template += '<td class="user-name">';
            template += '<select class="drivers" onchange="driver_set(\'' + data.orders[i].order_group_id + '\', $(this).val());"' + ((data.orders[i].select && data.orders[i].select.date_end) ? ' disabled' : '') + '>';
            template += '<option value="">--</option>';
            for (var j in data.drivers) {
                template += '<option value="' + data.drivers[j].id + '"' + (data.orders[i].select ? ((data.drivers[j].id == data.orders[i].select.user_id) ? ' selected' : '') : '') + '>' + data.drivers[j].name + '</option>';
            }
            template += '</select>';
            template += '</td>';
            if (data.orders[i].select) {
                template += '<td class="datetime">' + data.orders[i].select.date_begin + '</td>';
                template += '<td class="datetime">' + (data.orders[i].select.date_end ? data.orders[i].select.date_end : '<span class="error">заявка открыта</span>') + ' </td>';
                template += '<td class="count">' + data.orders[i].select.driver + '</td>';
                template += '<td class="money min">' + data.orders[i].select.price + ' руб.</td>';
                template += '<td class="money min"><input type="text" value="' + data.orders[i].delivery_surcharge + '" disabled /> руб.</td>';
            } else {
                template += '<td colspan="3"></td>';
                template += '<td class="money min">' + data.orders[i].price + ' руб.</td>';
                template += '<td id="delivery-surcharge-' + data.orders[i].order_group_id + '" class="money min"><input type="text" value="' + data.orders[i].delivery_surcharge + '" maxlength="3" class="number" onclick="$(\'div\', $(this).parents(\'td\')).show();" /> руб.<div class="save" onclick="delivery_surcharge(\'' + data.orders[i].order_group_id + '\', $(\'#delivery-surcharge-' + data.orders[i].order_group_id + ' input\').val());">OK</div></td>';
            }
            template += '<td class="status">' + data.orders[i].status_name + '</td>';
            template += '</tr>';
        }
        template += '</table>';
        $("#cms-reports div.items").append(template);
        $("#cms-reports div.filter div.filter-load").hide();
    }, 'JSON');
}

// Доплата за доставку;
function delivery_surcharge(order_group_id, money) {
    if (confirm('Изменить данные?')) {
        $.post('/reports/delivery-plus-money', {
            'delivery_surcharge': true,
            'order_group_id': order_group_id,
            'money': money
        }, function (data) {
            alert(data);
        });
        $("#delivery-surcharge-" + order_group_id + " div.save").hide();
    }
    return false;
}

// Смена курьера на доставку;
function driver_set(order_group_id, driver_id) {
    if (confirm('Изменить данные?')) {
        $.post('/reports/orders_driver_set', {
            'driver_set': true,
            'order_group_id': order_group_id,
            'driver_id': driver_id
        }, function (data) {
            alert(data);
        });
    }
    return false;
}

// Поиск водителей;
function data_list(type) {
    var inputs = $("#cms-data form").serialize();
    $("#cms-data div.load").show();
    $("#cms-data div.items").html('');
    $("#cms-data div.form").html('');
    $.post(window.location.href, {
        'users': inputs
    }, function(data) {
        var template = '';
        template += '<table cellpadding="0" cellspacing="0" border="0" class="transactions">';
        template += '<tr>';
        template += '<th class="name">ФИО</th>';
        template += '<th class="phone">Телефон</th>';
        template += '<th class="money">Баланс</th>';
        template += '<th class="bonus">Бонусы</th>';
        template += '</tr>';
        for (var i in data.users) {
            template += '<tr class="item' + ((i % 2 == 0) ? '' : ' grey') + '" onclick="return pays_search(\'' + data.users[i].id + '\');">';
            template += '<td class="name">' + data.users[i].name + ' </td>';
            template += '<td class="phone">' + data.users[i].phone + ' </td>';
            template += '<td class="money">' + data.users[i].money + ' </td>';
            template += '<td class="bonus">' + data.users[i].bonus + ' </td>';
            template += '</tr>';
        }
        template += '</table>';
        $("#cms-transactions div.users div.items").append(template);
        $("#cms-transactions div.load").hide();
    }, 'JSON');
}

// Поиск пользователей;
function users_search() {
    var inputs = $("#cms-transactions form").serialize();
    $("#cms-transactions div.load").show();
    $("#cms-transactions div.users div.items").html('');
    $("#cms-transactions div.user-data").html('');
    $.post(window.location.href, {
        'users': inputs
    }, function(data) {
        var template = '';
        template += '<table cellpadding="0" cellspacing="0" border="0" class="transactions">';
        template += '<tr>';
        template += '<th class="name">ФИО</th>';
        template += '<th class="phone">Телефон</th>';
        template += '<th class="money">Баланс</th>';
        template += '<th class="bonus">Бонусы</th>';
        template += '</tr>';
        for (var i in data.users) {
            template += '<tr class="item' + ((i % 2 == 0) ? '' : ' grey') + '" onclick="return pays_search(\'' + data.users[i].id + '\');">';
            template += '<td class="name">' + data.users[i].name + ' </td>';
            template += '<td class="phone">' + data.users[i].phone + ' </td>';
            template += '<td class="money">' + data.users[i].money + ' </td>';
            template += '<td class="bonus">' + data.users[i].bonus + ' </td>';
            template += '</tr>';
        }
        template += '</table>';
        $("#cms-transactions div.users div.items").append(template);
        $("#cms-transactions div.load").hide();
    }, 'JSON');
}

// Поиск платежей;
function pays_search(user_id) {
    var inputs = $("#cms-transactions form").serialize();
    $("#cms-transactions div.load").show();
    $("#cms-transactions div.user-data").html('');
    $.post(window.location.href, {
        'user_pays': true,
        'user_id': user_id
    }, function(data) {
        var template = '';
        template += '<div class="info">';
        template += '<div class="item">ФИО: <b>' + data.user_info.name + '</b></div>';
        template += '<div class="item">Телефон: <b>' + data.user_info.phone + '</b></div>';
        template += '<div class="item">E-mail: <b>' + data.user_info.email + '</b></div>';
        template += '<div class="item">Уровень доступа: <b>' + data.user_info.level + '</b></div>';
        template += '<div class="item">Рейтинг таксиста: <b>' + data.user_info.driver + '</b></div>';
        template += '<div class="item">Бонусы: <b>' + data.user_info.bonus + '</b> β.</div>';
        template += '<div class="item">Баланс: <b>' + data.user_info.money + '</b> р.</div>';
        template += '<div class="item">Сумма транзакций: <b>' + data.user_info.pays + '</b> р.</div>';
        template += '<div class="item">Регистрация: <b>' + data.user_info.registration + '</b></div>';
        template += '<div class="item">Вход: <b>' + data.user_info.enter + '</b></div>';
        template += '</div>';
        template += '<div class="requests">';
        if (data.user_requests.length > 0) {
            for (var i in data.user_requests) {
                template += '<div id="request-' + data.user_requests[i].id + '" class="item">';
                template += '<div class="date">' + data.user_requests[i].date + '</div>';
                template += '<div class="money"><b>' + data.user_requests[i].money + '</b> р.</div>';
                template += '<div class="actions"><span class="button" onclick="return request_ok(\'' + data.user_requests[i].id + '\', \'' + data.user_info.id + '\');">исполнить</span><span class="button" onclick="return request_cancel(\'' + data.user_requests[i].id + '\', \'' + data.user_info.id + '\');">отмена</span></div>';
                template += '<div class="clear"></div>';
                template += '</div>';
            }
        } else {
            template += '<div class="empty">Заявок на вывод средств нет</div>';
        }
        template += '</div>';
        template += '<div class="money-out">';
        template += '<div class="input money"><span class="label">Сумма</span><input type="text" name="money" value="" maxlength="8" class="money" /></div>';
        template += '<div class="input comments"><span class="label">Комментарий</span><input type="text" name="comments" value="" maxlength="128" class="comments" /></div>';
        template += '<div class="button" onclick="return money_out(\'' + data.user_info.id + '\');">Вывод</div>';
        template += '<div class="clear"></div>';
        template += '</div>';
        template += '<div class="pays">';
        if (data.user_pays.length > 0) {
            template += '<table cellpadding="0" cellspacing="0" border="0" class="transactions">';
            template += '<tr>';
            template += '<th class="datetime">Дата</th>';
            template += '<th class="order-id">Заказ</th>';
            template += '<th class="type">Операция</th>';
            template += '<th class="money">Сумма</th>';
            template += '</tr>';
            for (var i in data.user_pays) {
                template += '<tr class="item' + ((i % 2 == 0) ? '' : ' grey') + '">';
                template += '<td class="datetime">' + data.user_pays[i].date + ' </td>';
                template += '<td class="order-id">' + data.user_pays[i].order_id + ' </td>';
                template += '<td class="type">' + data.user_pays[i].type + ' </td>';
                template += '<td class="money">' + data.user_pays[i].money + ' </td>';
                template += '</tr>';
            }
            template += '</table>';
        }
        template += '</div>';
        $("#cms-transactions div.user-data").append(template);
        $("#cms-transactions div.load").hide();
    }, 'JSON');
}

// Вывод средств;
function money_out(user_id) {
    $.post(window.location.href, {
        'money_out': true,
        'user_id': user_id,
        'money': $("div.money-out div.money input").val(),
        'comments': $("div.money-out div.comments input").val()
    }, function(message) {
        if (message == 'OK') {
            message = 'Средства выведены';
            pays_search(user_id);
        }
        if (message) alert(message);
    });
    return false;
}

// Принят заявку на вывод средств;
function request_ok(request_id, user_id) {
    $.post(window.location.href, {
        'request_ok': true,
        'request_id': request_id,
        'user_id': user_id
    }, function(message) {
        if (message == 'OK') {
            message = 'Заявка выполнена';
            pays_search(user_id);
        }
        alert(message);
    });
    return false;
}

// Отменить заявку на вывод средств;
function request_cancel(request_id, user_id) {
    $.post(window.location.href, {
        'request_cancel': true,
        'request_id': request_id
    }, function(message) {
        if (message == 'OK') {
            message = 'Заявка отменена';
            pays_search(user_id);
        }
        alert(message);
    });
    return false;
}

// Поиск записей для фильтра;
var search_timer;
function auto_search(search_name, search_value, search_delay) {
    if (search_value.length >= 3) {
        if (search_timer) clearTimeout(search_timer);
        search_timer = setTimeout(function() {
            $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").hide();
            $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-load").show();
			//
            $.post('/reports/orders_search', {
                'search_name': search_name,
                'search_value': search_value
            }, function (data) {
                $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").html('');
                if (data.length > 0) {
                    for (var i in data) {
                        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").append('<div class="item item-' + data[i].id + '" onclick="search_items_add(\'' + search_name + '\', \'' + data[i].id + '\');">' + data[i].name + '</div>');
                    }
                    $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").show();
                    $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").addClass("open");
                } else {
                    $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").hide();
                }
                $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-load").hide();
                $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").show();
            }, 'JSON');
        }, search_delay);
    } else {
        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").hide();
    }
}

// Поиск всех записей для фильтра;
function auto_search_all(search_name) {
    if ($("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").hasClass("open")) {
        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").removeClass("open").show();
        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-load").hide();
        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").html('').hide();
    } else {
        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").hide();
        $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-load").show();
        $.post('/reports/orders_search', {
            'search_name': search_name,
            'search_value': ''
        }, function (data) {
            $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").html('');
            if (data.length > 0) {
                for (var i in data) {
                    $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").append('<div class="item item-' + data[i].id + '" onclick="search_items_add(\'' + search_name + '\', \'' + data[i].id + '\');">' + data[i].name + '</div>');
                }
                $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").show();
                $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").addClass("open");
            } else {
                $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-items").hide();
            }
            $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-all").show();
            $("#cms-reports div.filter div.auto-search.filter-" + search_name + " div.auto-search-load").hide();
        }, 'JSON');
    }
}

//new comment

// Добавление записей в выборку;
function search_items_add(name, item_id) {
    //if($("#cms-reports div.filter div.filter-" + name + " div.auto-search-items").length > 0){
    //    console.log('1111');
    //}else{
    //    console.log('2222');
    //}
    //
    //if($("#cms-reports div.filter div.filter-" + name + " div.auto-search-items div.item-" + item_id).length > 0){
    //    console.log('333');
    //}else{
    //    console.log('444');
    //}

    $("#cms-reports div.filter div.filter-" + name + " div.auto-search-items").hide();
    $("#cms-reports div.filter div.filter-" + name + " div.auto-search-items div.item-" + item_id).remove();

    $.post('/reports/orders_search_items_add', {
        'search_items_add': true,
        'name': name,
        'item_id': item_id
    }, function(item_name) {
      //  console.log('item_name = ');
      //  console.log(item_name);
        $("#cms-reports div.filter div.filter-" + name + " div.auto-search-values").append('<span class="item item-' + item_id + '" onclick="search_items_delete(\'' + name + '\', \'' + item_id + '\');">' + item_name + '</span>');
        $("#cms-reports div.filter div.filter-" + name + " div.auto-search-items").hide();
    });
}

// Удаление записей из выборки;
function search_items_delete(name, item_id) {
    $.post('/reports/orders_search_items_delete', {
        'search_items_delete': true,
        'name': name,
        'item_id': item_id
    }, function() {
        $("#cms-reports div.filter div.filter-" + name + " div.auto-search-values span.item-" + item_id).remove();
    });
}

// История статусов;
function order_item_status(order_item_id) {
    if ($("#item-status-" + order_item_id).hasClass("open")) {
        $("#item-status-" + order_item_id).removeClass("open").hide();
    } else {
        $("#item-status-" + order_item_id).addClass("open").html('<div>загрузка...</div>').show();
        $.post('/reports/orders_items_status', {
            'order_item_status': order_item_id
        }, function(data) {
            $("#item-status-" + order_item_id).html('');
            if (data.status.length > 0) {
                for (var i in data.status) {
                    $("#item-status-" + order_item_id).append('<div>' + data.status[i].date + ' – ' + data.status[i].status_name + ' (' + (data.status[i].user_name ? '<span>' + data.status[i].user_name + '</span>' : '<span class="auto"></span>') + ')</div>');
                }
            } else {
                $("#item-status-" + order_item_id).html('<div>нет данных</div>');
            }
        }, 'JSON');
    }
}

// Возврат заказа (выбранного товара в заказе);
function order_item_return(order_item_id) {
    if (confirm('Вернуть товар поставщику?')) {
        $.post('/reports/orders_item_return', {
            'order_item_return': order_item_id
        }, function(data) {
            console.log('data = ' + data);
            $("tr.order-item-" + order_item_id).css("opacity", "0.5");
            alert(data);
        });
    }
}

// Отмена заказа (выбранного товара в заказе);
function order_item_cancel_now(order_item_id,order_group_id,order_id){
    if (confirm('Отменить товар и вернуть деньги покупателю?')) {
        $.post('/ajax-management/orders-item-cancel', {
            'orderItem': order_item_id,
            'orderGroup': order_group_id,
            'order': order_id
        }, function(data) {
            if($("tr.order-item-" + order_item_id).attr('count')==1){
                $("tr.order-item-" + order_item_id + " td.name span.cancel").remove();
                $("tr.order-item-" + order_item_id + " td.name").append('<span class="return" onclick="order_item_return(\'' + order_item_id + '\');">возврат</span>');
                $("tr.order-item-" + order_item_id).css("opacity", "0.5");
            }
            else{
                $("tr.order-item-" + order_item_id).attr('count', $("tr.order-item-" + order_item_id).attr('count')-1);
                $("td.order-item-" + order_item_id + "-count").empty();
                $("td.order-item-" + order_item_id + "-count").append($("tr.order-item-" + order_item_id).attr('count')+' шт.');
            }
            //alert(data);
            console.log($("tr.order-item-" + order_item_id).attr('count'));
        });
    }
}

function order_item_cancel(order_item_id) {
    if (confirm('Отменить товар и вернуть деньги покупателю?')) {
        $.post('/reports/orders_item_cancel', {
            'order_item_cancel': order_item_id
        }, function(data) {
            $("tr.order-item-" + order_item_id + " td.name span.cancel").remove();
            $("tr.order-item-" + order_item_id + " td.name").append('<span class="return" onclick="order_item_return(\'' + order_item_id + '\');">возврат</span>');
            $("tr.order-item-" + order_item_id).css("opacity", "0.5");
            alert(data);
        });
    }
}

// Отмена доставки;
function order_delivery_cancel(order_group_id) {
    if (confirm('Вернуть деньги за доставку покупателю?')) {
        $.post('/reports/orders_delivery_cancel', {
            'order_delivery_cancel': order_group_id
        }, function (data) {
            $("tr.delivery-item-" + order_group_id + "span.cancel").remove();
            $("tr.delivery-item-" + order_group_id).addClass("disabled");
            $("tr.delivery-item-" + order_group_id + " td.money").html('0.00 руб.');
            alert(data);
        });
    }
}

// Двойная доставка;
function order_delivery_double(order_group_id) {
    if (confirm('Списать средства с покупателя и начислить курьеру?')) {
        $.post('/reports/orders_delivery_double', {
            'order_delivery_double': order_group_id
        }, function(data) {
            $("tr.delivery-item-" + order_group_id + "span.cancel").remove();
            $("tr.delivery-item-" + order_group_id).addClass("disabled");
            $("tr.delivery-item-" + order_group_id + " td.money").html('0.00 руб.');
            alert(data);
        });
    }
}

// Экспорт данных в Excel;
function exports_xls() {
    window.location.href = '/reports/xls';
    /*
    $.post(window.location.href, {
        'exports_xls': true
    }, function(data) {
        window.location.href = '/reports/xls';
    });*/
    return false;
}

// Экспорт данных в 1C;
function exports_xml() {
    window.location.href = '/reports/xml'/*
    $.post(window.location.href, {
        'exports_xml': true
    }, function() {
        window.location.href = '/reports/xml'
    });*/
    return false;
}

/* REPORTS AND TOOLS END */
/*-----------------Техподержка-----------------*/
var timerid;
$(document).ready(function() {
    // Обновления страница интервал 7 сек.;
    if($("#cms-feedback").has('table.phone').length){
        timerid = setInterval(timerInterval,7000);
    }else{
        clearInterval(timerid);
    }
});

// Обновления страница;
function timerInterval() {
    $.post(location.href, {}, function (html) { $("table.phone", "#cms-feedback").html($(html).find("table.phone", "#cms-feedback").html())});
}
// Редактировать Сообщения;
function edit_feedback(id) {
    loading('show');
    $('.container').load(window.location.href, {'form': true,'id': id}, function () {
        loading('hide');
    });
    return false;
}
// Удалить Сообщения;
function delete_feedback(id) {
    if (confirm('Удалить Сообщения')) {
        loading('show');
        $.post(location.href, {'delete': true, 'id': id}, function (response) {
            location.reload();
        });
    }
    return false;
}
// Пометить статус;
function status_check(id) {
    loading('show');
    var status = $("#cms-feedback input.check.i-" + id).filter('input:checked').length;
    $.post(location.href, {'status': status, 'id': id}, function (response) {
        $('.list-table','#cms-feedback').html($(response).find('.list-table','#cms-feedback').html());
        $('#cms-feedback div.nav').html($(response).find('#cms-feedback div.nav').html());
        loading('hide');
    });
}
// Раскрыть товар;
$(document).on('click','#repost_order_list .js-goods-order',function(){

    $(this).siblings('.goods-item').toggleClass('hidden');
    $(this).children('.glyphicon').toggleClass('glyphicon-chevron-up').toggleClass('glyphicon-chevron-down');


    return false;
});


console.log('cms---OK');