var product;
var catalog = {};

catalog.page                            = '.product-list';
catalog.productListContainerClass       = '.js-product-list';
catalog.productDetailContainerClass     = '.product-detail-page';
catalog.productItemClass                = '.js-product-item';

catalog.changeCountProductClass         = '.count-select-button';
catalog.inputBlockClass                 = '.product-control-buttons';

catalog.propertiesBlockClass            = '.tag-value-list';
catalog.propertyBlockClass              = '.tag-value-group-items';
catalog.propertyBlockValueClass         = '.tag-value-group-item';
catalog.inputBlockVariantClass          = '.js-control-buttons-for-variant';
catalog.changeCountButton               = '.js-count-button';

catalog.changeCountProduct = function(action,parent,basketId,count){
    shop.changeCountProduct(action,parent,basketId,count,catalog.page);
    parent.find(catalog.changeCountProductClass).data('current-count',count).siblings('.num').html(count + ' шт.');
}

catalog.closeProductProperties = function(productBlock){    // получаем продукт

    var resultJson = '',
        delimiter = ',';
    if(productBlock.find(catalog.propertyBlockClass).length > 0){   // если свойств больше одного
        productBlock.find($(catalog.propertyBlockValueClass)).removeClass('disabled');   // разблокируем все всойства

        productBlock.find(catalog.propertyBlockClass).each(function(i,element){ // перебираем свойства
            var currentPropertyId = $(element).data('tag-group-id');    // ID свойства
            var currentPropertyActive = $(element).find('.active').data('tag-id');  //ID варианта активного свойства
            var currentPropertySimplex = {};
            if((i+1) < productBlock.find(catalog.propertyBlockClass).length){
                delimiter = ',';
            }else{
                delimiter = '';
            }

            resultJson += '"'+currentPropertyId+'":'+currentPropertyActive+delimiter;

            productBlock.find(catalog.inputBlockVariantClass).each(function(j,inputBlock){  // перебираем блоки покупки варианта товара
                var property = $(inputBlock).data('json');  // свойства варианта товара
                if(property == undefined){
                    console.log(productBlock.data('product-id') + ' error!');
                    return false;
                }
                if(property[currentPropertyId] == currentPropertyActive){   // получаем все свойства, которые встречаются с активным свойством

                    $.each(property,function(k,prop){
                        if(currentPropertySimplex[k] != undefined){
                        }else{
                            currentPropertySimplex[k] = {};
                        }
                        if(currentPropertySimplex[k][prop] != undefined){

                        }else{
                            currentPropertySimplex[k][prop] = 1;
                        }
                    });
                }
            });

            $.each(currentPropertySimplex,function(l,one){
                if(l != currentPropertyId){
                    $.each(productBlock.find(catalog.propertyBlockClass + "[data-tag-group-id=" + l + "] " + catalog.propertyBlockValueClass),function(m,tagValueElement){
                        if(currentPropertySimplex[l][$(tagValueElement).data('tag-id')] == undefined){
                            $(tagValueElement).addClass('disabled');    // блокируем свойство - нет варианта с данным и активным свойством
                        }
                    });
                }
            });
        });
    }
    //console.log('resultJson = ');
    //console.log(resultJson);
    return '{'+ resultJson +'}';
}

catalog.reloadProductItem = function(product){
    var currentPath = catalogAjaxPath + 'get-catalog-list-product';
    if(product.data('view-type') == 'detail'){
        currentPath = catalogAjaxPath + 'get-catalog-detail-product';
    }
    $.ajax({
        method    :   'POST',
        url       :   currentPath,
        data      :   {
            'productId' : product.data('product-id')
        },
        success   :   function(response){
            product.after(response);
            catalog.closeProductProperties(product.next());
            product.remove();
            shop.reloadBasketSmall();
        }
    });
}

catalog.getBasketVariantIds = function(){
    $.ajax({
        method    :   'POST',
        url       :   catalogAjaxPath + 'get-basket-variant-ids',
        dataType  :   'json',
        success   :   function(response){
            if(response.length > 0){
                $.each(response,function(i,element){
                    if(catalog.page.find('.js-control-buttons-for-variant[data-variant='+element+']').length > 0){
                        catalog.reloadProductButton(element);
                    }
                });
            }
        }
    });
}

catalog.reloadProductButton = function(elementId){
    $('div.button-ajax[data-id="' + elementId +'"] div.load').show();
    $.ajax({
        method    :   'POST',
        url       :   catalogAjaxPath + 'get-basket-variant-button',
        data      :   {variantId : elementId},
        success   :   function(response){
            if(response.length > 0){
                $('div.button-ajax[data-id="' + elementId +'"] div.load').hide();
                findBasketVariant = catalog.page.find('.js-control-buttons-for-variant[data-variant='+elementId+']');
                findBasketVariant.html(response);
                shop.reloadBasketSmall();
            }
        }
    });
}
// Подгрзука контент с под категорией;
function loadGoodsCategory(type) {
    console.log("loadGoodsCategory + режим мастер");
    var inProcess = false;
    var nextCategoryHref = $("#help-master a.good.next").attr("href");
    var id = $("#help-master a.good.next").data("id");


      if($('div.last-element').length > 0 && $('#list-wrapper div.next_item').length > 0 && type) {
          console.log("Попал");
          $(window).scroll(function (e) {
              if ($('#list-wrapper div.next_item').length > 0 && $(window).scrollTop() + $(window).height() >= $('#footer').offset().top && !inProcess) {
                  console.log('Cat ' + type);
                  console.log("loadGoodsCategory аякс");
                  //Аякс запрос;
                  inProcess = true;
                  $.ajax({
                      url: nextCategoryHref,
                      method: 'POST',
                      cache: false,
                      beforeSend: function () {

                          $("#list-wrapper").append("<div class='content-load'></div>");
                          $("div.content-load").show();
                      }
                  }).done(function (html) {
                      $("div.content-load").remove();
                      if (html.length > 0) {
                          console.log("Подгрузка категория + режим мастера");

                          $("#sort:last").append($(html).find("#sort").html());

                           if($(html).find("#sort div.groups:last").length > 0)  {
                               // Раскрываем блок;
                               $(html).find('div.groups').each(function (index, item) {
                                   var group_id = $(this).data('group-category');
                                   var countSize = $("#list-wrapper div.groups[data-group-category='" + group_id + "'] div.sort_item").size();
                                   $("#list-wrapper div.groups[data-group-category='" + group_id + "'] div.sort_item").slice(0, 5).removeClass('hidden');
                                   if (countSize > 5) {
                                       $("#list-wrapper div.groups[data-group-category='" + group_id + "']").append('<div class="sort_item next_item" rel="5"><div class="item"> <div class="block "><div class="next next-icon"></div></div></div></div>');
                                   }
                               });
                               var catalog_id = $("#help-master a.good.next.js-value-master").data("catalog_id");
                               // Обновления Мастер покупка;
                               $("#help-master").load(generalAjaxPath + 'master-help #help-master div.body-master', {
                                   'helperBasketUpdate': true,
                                   'id': id,
                                   'catalog_id' : catalog_id
                               }, function (data) {
                                   nextCategoryHref = $("#help-master a.good.next").attr("href");
                                   id = $("#help-master a.good.next").data("id");
                                   inProcess = false;
                               });

                           }
                      }
                  });
              }
          });
      }
}
// Подгрзука контент;
function loadGoods(type) {
    if($('#center').has('.goods').length && type){
        // Ленивая загрузка товаров;
        var inProcess = false;
            $(window).scroll(function () {
                console.log('loadGoods ++ ' + $('#list-wrapper').has('.more').length);
                if ($('#list-wrapper').has('.more').length > 0 && $(window).scrollTop() + $(window).height() >= $('.more').offset().top - 600 && !inProcess) {
                    console.log('loadGoods Аякс ');
                    $.ajax({
                        url: catalogAjaxPath + 'get-product-page',
                        method: 'POST',
                        data: {
                            'page-id': catalog.page.find('.more').data('page-id'),
                            'category-id': catalog.page.find('.more').data('category-id')
                        },
                        cache: false,
                        beforeSend: function () {
                            //Аякс запрос;
                            inProcess = true;
                            $("div.content-load").show();
                            //$("div.goods div.more a").hide();
                        }
                    }).done(function (html) {
                        $("div.content-load").hide();
                        //$("div.goods div.more a").show();
                       // console.log(html);
                        if (html) {
                            catalog.page.find('.more').data('page-id', catalog.page.find('.more').data('page-id') + 1);
                            // Добавляем список товаров;
                            catalog.page.find('#sort').append(html);
                            $("div.sort_item").removeClass('hidden');
                            //Стоп аякс запрос
                            inProcess = false;
                        }
                    });
                }
            });
        }
}
$(document).ready(function(){

    if($(catalog.productListContainerClass).length > 0){
        catalog.page = $(catalog.productListContainerClass);
        catalog.getBasketVariantIds();
    }else{
        catalog.page = $(catalog.productDetailContainerClass);
    }


    catalog.page.find(catalog.productItemClass).each(function(i,element){
        catalog.closeProductProperties($(element));
    });
    //Инилизация подгрузка контент;
    if(!$("#list-wrapper div.js-disabled-page").length > 0 && $('#list-wrapper').has('.more').length > 0) {
        console.log("ALERT");
        loadGoods(true);

    }

    // catalog.page выбор вариация;
    $(document).on('click',catalog.propertyBlockValueClass,function(e){
       // alert("+");
        product = $(this).parents(catalog.productItemClass);
        if($(this).hasClass('disabled')) {
            //alert('disabled');
        }else if($(this).hasClass('active') && e.bubbles){
            // Присвоения аттрибут data-text-select;
            $(this).parents('.select__form').children('.container-select').children('.option-text').text($(this).parents('.select__form').children('.container-select').children('.option-text').attr('data-text-select'));
            //console.log($(this));
            $(this).removeClass('active');
            catalog.closeProductProperties(product);
            product.find(catalog.inputBlockVariantClass).removeClass('active');
            $(this).parents('div.content-good').children('.row-container').children('.block-js').children('.control-buttons-for-variant.active').parents(".row-container").show();
            $('div.row-container','div.good').not($(this).parents('div.content-good').children('.row-container').children('.block-js').children('.active').parents(".row-container")).hide();
        }else{
            // Добавоения текст в option-text;
            $(this).parents('.select__form').children('.container-select').children('.option-text').text($(this).text());
            $(this).siblings(catalog.propertyBlockValueClass).removeClass('active');
            $(this).addClass('active');
            product = $(this).parents(catalog.productItemClass);
            var resultJson = catalog.closeProductProperties(product);
            product.find(catalog.inputBlockVariantClass + '.active').removeClass('active');
            product.find(catalog.inputBlockVariantClass+'[data-json=\''+resultJson+'\']').addClass('active');
            var changeVariant = product.find(catalog.inputBlockVariantClass+'[data-json=\''+resultJson+'\']').data('variant');
            product.find('.price-block-list').addClass('hidden');
            product.find('.price-block-list[data-variant-id='+changeVariant+']').removeClass('hidden');


            // Скрываем карточка товара массив цены - лечим баг;
            $(this).parents('div.content-good').children('.row-container').children('.block-js').children('.control-buttons-for-variant.active').parents(".row-container").show();
            $('div.row-container','div.good').not($(this).parents('div.content-good').children('.row-container').children('.block-js').children('.active').parents(".row-container")).hide();
            //console.log('resultJson');
            //console.log(resultJson);
        }

        var tag_id = $(this).data('tag-id');
        //console.log(e);

        // Активность картина;
        if(e.bubbles) {
            $('div.carousel_item[data-tag-id="'+tag_id+'"]').click();
            $('div.item[data-tag-id="'+tag_id+'"] .js_carousel').click();
           // console.log(e);
        }

    });
    // Плюс минус;
    $(document).on('click',catalog.changeCountProductClass,function(){
        var countMax    = $(this).data('max');
        var countMin      = $(this).data('count-min');
        var countPack   = $(this).data('count-pack');
        var count       = $(this).data('current-count');
        var action      = $(this).data('action');


        if(action == shop.actionPlus && (count + countPack) > countMax){

        }else{
            if(action == shop.actionMinus){
                count -= countPack;
            }else{
                count += countPack;
            }
            if(count >= (countMin ? countMin : 0)){

                product         = $(this).parents(catalog.productItemClass);
                var basketId    = $(this).data('basket');
                catalog.changeCountProduct(action,product,basketId,count);
            }else{
               // Удаляем товар;
            }
        }
        // Счетчик;
        if(!$(this).parents('.control-buttons-for-variant').children('.count-basket-icon ').length) {
            $(this).parents('.control-buttons-for-variant').children('.js-count-button').addClass('count-basket-icon').removeClass('success-basket-icon');
        }
         $(this).parents('.control-buttons-for-variant').children('.js-count-button').html('<div>' + $(this).siblings('.num').text().replace(/\D+/g, "") + '</div>');

    });

    // События четчик;
    $(document).on('click',catalog.changeCountButton,function(){
        console.log('Star' + $(this).parent('.control-buttons-for-variant.active').attr('data-first'));
       $(catalog.changeCountProductClass + '.plus',$(this).parent('.control-buttons-for-variant.active')).click();
    });

    // Добавить в корзину; catalog.page;
    $(document).on('click','.basket_button',function(){
        product = $(this).parents('.product-item');
        var currentProduct = $(this);
        // Анимация полет товар в корзину;
       if($('#header div.header-content.desktop div.small-basket-block.desktop:visible').length) {
        var obj = $("div.item.item-" + currentProduct.data('product') + ' div.block div.images');
           if(obj.length) obj.clone().css({'position' : 'absolute', 'top' : Math.ceil(obj.offset().top),'left' : Math.ceil(obj.offset().left),'z-index' : '350','background': '#ffffff'}).appendTo('body').animate({opacity: 0.5, top : $("#header div.small-basket-block.desktop").offset().top, left: $("#header div.small-basket-block.desktop").offset().left, width: 100}, 700, function() {$(this).remove();});
        }

        //  Положить товар в корзину
        if(currentProduct.data('action') == 'bay'){
            $('div.button-ajax[data-id="' + currentProduct.parent().data('variant') +'"] div.load').show();
            $.ajax({
                method    :   'POST',
                url       :   basketAjaxPath + 'add-in-basket',
                data      :   {
                    'id'            : currentProduct.data('product'),
                    'count'         : currentProduct.data('count'),
                    'variant'       : currentProduct.parent().data('variant'),
                },
                success   :   function(response){
                    //catalog.reloadProductItem(product);
                    //console.log('variant');
                    //console.log(currentProduct.parent().data('variant'));
                    $('div.button-ajax[data-id="' + currentProduct.parent().data('variant') +'"] div.load').hide();
                    if(catalog.page.find('.js-control-buttons-for-variant[data-variant='+currentProduct.parent().data('variant')+']').length > 0){
                        catalog.reloadProductButton(currentProduct.parent().data('variant'));
                    }
                    shop.reloadBasketSmall(2);

                }
            });
            //  Удалить товар из корзины
        }else if(currentProduct.data('action') == 'remove'){
            $.ajax({
                method    :   'POST',
                url       :   basketAjaxPath + 'remove-basket',
                data      :   {
                    'basketItemid'  : currentProduct.data('basket'),
                    'id'            : currentProduct.data('product'),
                    'count'         : currentProduct.data('count'),
                    'variant'       : currentProduct.data('variant'),
                },
                success   :   function(response){
                    catalog.reloadProductItem(product);
                }
            });
        }

        return false;
    });

    $('.all-product-list-control').click(function(){
        if($(this).prop('checked')){
            $(this).parents('table').find('tbody tr input').click()//prop('checked');
        }else{
            $(this).parents('table').find('tbody tr input').click()//prop('checked','false');
        }
    });

    //$('.all-product-list-in-basket').click(function(){
    //    if($('.all-product-list-table').find('tr input').length > 0){
    //        $.each($('.all-product-list-table tbody').find('tr input:checked'),function(i,element){
    //            console.log('i = '+i);
    //            console.log($(element).parents('tr').data());
    //
    //            $.ajax({
    //                method    :   'POST',
    //                url       :   basketAjaxPath + 'add-in-basket',
    //                data      :   {
    //                    'id'            : $(element).parents('tr').data('product-id'),
    //                    'count'         : $(element).parents('tr').data('count'),
    //                    'variant'       : $(element).parents('tr').data('variant-id'),
    //                },
    //                success   :   function(response){
    //
    //                }
    //            });
    //        });
    //        shop.reloadBasketSmall();
    //    }
    //});

    $('.all-product-list-in-basket').click(function(){

        //console.log('1');

        if($('.all-product-list-table').find('tr input').length > 0){
            loading('show');
            $.each($('.all-product-list-table tbody').find('tr input:checked'),function(i,element){
                //console.log('i = '+i);
                //console.log($(element).parents('tr').data());

                $.ajax({
                    method    :   'POST',
                    url       :   basketAjaxPath + 'add-in-basket',
                    data      :   {
                        'id'            : $(element).parents('tr').data('product-id'),
                        'count'         : $(element).parents('tr').data('count'),
                        'variant'       : $(element).parents('tr').data('variant-id'),
                    },
                    success   :   function(response){

                    }
                });
            });
            shop.reloadBasketSmall(1);
        }

        if($('.all-product-list-table1').find('tr input').length > 0){
            //console.log('2');
            loading('show');
            $.each($('.all-product-list-table1 tbody').find('tr input:checked'),function(i,element){
                console.log('i = '+i);
                console.log($(element).parents('tr').data());

                $.ajax({
                    method    :   'POST',
                    url       :   basketAjaxPath + 'add-in-basket',
                    data      :   {
                        'id'            : $(element).parents('tr').data('product-id'),
                        'count'         : $(element).parents('tr').data('count'),
                        'variant'       : $(element).parents('tr').data('variant-id'),
                    },
                    success   :   function(response){

                    }
                });
            });
            shop.reloadBasketSmall(1);


        }
    });


});


console.log( "Catalog.js Ok");