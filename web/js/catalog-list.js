
var versionCatalogList = "Catalog-list.js version: 1.0.0@31-08-2016";
console.warn(versionCatalogList);

var catalogList = {};
catalogList.pageIndex                        = '#catalog-product-list-view';
catalogList.setOrderButtonClass              = '.order-success';
catalogList.generalForm                      = '.basket-general-form';

catalogList.resultBlockID                    = '#basket-page-result-data';
catalogList.paymentBlockID                   = '#basket-page-payments';
catalogList.dateTimeBlockID                  = '#basket-page-date-time-data';
//catalogList.basketProductBlock               = '#basket-product-block';

catalogList.basketProductBlock               = '.js-basket-item';
catalogList.changeCountProductClass          = '.product-list-plus-minus';
catalogList.basketChangeProductVariantClass  = '.tag-value-group-item';
catalogList.propertyBlockClass               = '.tag-value-group-items';
catalogList.inputBlockVariantClass           = '.js-control-buttons-for-variant';
catalogList.priceCountBlockClass             = '.price-count-block';
catalogList.productTypeBlockClass            = '.product-type-block';
catalogList.productTypeBigBlockClass         = '.product-type-big-block';

catalogList.deliveryAddressBlock             = '#delivery-type-and-address-select-block';
catalogList.removeAllClass                   = '.remove-all-basket-products';
catalogList.productItemClass                 = '.js-product-item';

catalogList.noDisplayClass                   = '.not-display';
catalogList.okParamResult                    = 0;

//---------------------

catalogList.reloadBasketResult = function(){
    $.ajax({
        method    :   'POST',
        url       :   catalogListAjaxPath + 'get-basket-result',
        success   :   function(response){
            $(catalogList.resultBlockID).html(response);
        }
    });
}

catalogList.reloadBasketProductList = function(){

    $.ajax({
        method    :   'POST',
        url       :   catalogListAjaxPath + 'get-basket-product-list',
        success   :   function(response){
            $(catalogList.basketProductBlock).html(response);
        }
    });
}

catalogList.reloadProductItem = function(product){
    $.ajax({
        method    :   'POST',
        url       :   catalogListAjaxPath + 'get-basket-product',
        data      :   {
            'basketItemId' : product.data('basket-item')
        },
        success   :   function(response){
            product.after(response);
            catalogList.closeProductProperties(product.next());
            product.remove();
        }
    });
}

//---------------------

catalogList.closeProductProperties = function(productBlock){    // получаем продукт
    var resultJson = '',
        delimiter = ',';
    var currentActiveVariant; //productBlock.data('variant-id');

    if($('div'+catalogList.basketChangeProductVariantClass+'[data-variant-id='+currentActiveVariant+']').length > 0){
        $('div'+catalogList.basketChangeProductVariantClass+'[data-variant-id=' + currentActiveVariant + ']').not(productBlock).remove();
    }
    console.log(productBlock.find(catalogList.propertyBlockClass).length);
    if(productBlock.find(catalogList.propertyBlockClass).length >= 1){   // если свойств больше одного
       // alert('B');
        //console.log(resultJson);
        productBlock.find($(catalogList.basketChangeProductVariantClass)).removeClass('disabled');   // разблокируем все всойства
        productBlock.find(catalogList.propertyBlockClass).each(function(i,element){ // перебираем свойства
            var currentPropertyId = $(element).data('tag-group-id');    // ID свойства
            var currentPropertyActive = $(element).find('.open').data('tag-id');  //ID варианта активного свойства
            var currentPropertySimplex = {};
            if((i+1) < productBlock.find(catalogList.propertyBlockClass).length){
                delimiter = ',';
            }else{
                delimiter = '';
            }

            resultJson += '"'+currentPropertyId+'":'+currentPropertyActive+delimiter;

            productBlock.find(catalogList.priceCountBlockClass).each(function(j,inputBlock){  // перебираем блоки покупки варианта товара
                var property = $(inputBlock).data('json');  // свойства варианта товара
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
                    $.each(productBlock.find(catalogList.propertyBlockClass + "[data-tag-group-id=" + l + "] " + catalogList.basketChangeProductVariantClass),function(m,tagValueElement){
                        if(currentPropertySimplex[l][$(tagValueElement).data('tag-id')] == undefined){
                            $(tagValueElement).addClass('disabled');    // блокируем свойство - нет варианта с данным и активным свойством
                        }
                    });
                }
            });
        });

    }
    console.log(resultJson);
    return '{'+ resultJson +'}';
}

catalogList.changeCountProduct = function(action,parent,basketId,count){
    shop.changeCountProduct(action,parent,basketId,count,catalogList.pageIndex);
}

catalogList.isEmptyBasket = function(){
    if($(catalogList.basketProductBlock).length > 0){

    }else{
        window.location = "/basket/";
    }
}

catalogList.sendGeneralFormData = function(){
    $.ajax({
        method    :   'POST',
        url       :   catalogListAjaxPath + 'set-empty-basket-params',
        data      :   {
            'params'        : $(catalogList.generalForm).serialize(),
            'basket-id'        : $(catalogList.generalForm).find(['[name=basket-id]']).val(),
            'StoreList'        : $(catalogList.generalForm).find(['[name=StoreList]']).val(),
        },
    });
    $(catalogList.generalForm).attr('action', "/my/order-report").submit();
}

$(document).ready(function(){
    catalogList.page = $(catalogList.pageIndex);

    catalogList.page.find(catalogList.basketProductBlock).each(function(i,element){
        catalogList.closeProductProperties($(element));
    });

    //------------------------------------

    catalogList.page.on('click',catalogList.basketChangeProductVariantClass,function(){
        var product = $(this).parents(catalogList.basketProductBlock);
        var oldVariantId = product.find(catalogList.priceCountBlockClass + '.open').data('variant');

        if($(this).hasClass('disabled')) {

        }else if($(this).hasClass('open')){
            $(this).removeClass('open');
            catalogList.closeProductProperties(product);
            product.find(catalogList.priceCountBlockClass).removeClass('open');
        }else{
            $(this).siblings(catalogList.propertyBlockValueClass).removeClass('open');
            $(this).addClass('open');
            var resultJson = catalogList.closeProductProperties(product);

            product.find(catalogList.priceCountBlockClass + '.open').removeClass('open');
            product
                .find(catalogList.priceCountBlockClass+'[data-json=\''+resultJson+'\']')
                .addClass('open')
                .find('.num').html('1 шт.');

            var changedVariantId = product.find(catalogList.priceCountBlockClass+'[data-json=\''+resultJson+'\']').data('variant');
           // console.log(changedVariantId);
           // alert(resultJson);
            $.ajax({
                method    :   'POST',
                url       :   catalogListAjaxPath + 'change-variant-list-product',
                data      :   {
                    'list' : product.data('list-id'),
                    'category' : product.data('category-title'),
                    'old-variant-id' : oldVariantId,
                    'new-variant-id' : changedVariantId,
                },
            });

            var resultVariantTitle = '';
            $.each(product.find('.tag-value-group-item.open'),function(i,element){
                resultVariantTitle += $(element).html().trim();
                if(product.find('.tag-value-group-item.open').length  != (i+1)){
                    resultVariantTitle += ' / ';
                }
            });
            product.find('.tags-item.variation-tags').html(resultVariantTitle);
        }
    });

    catalogList.page.on('click',catalogList.changeCountProductClass,function(){
        var countMax    = $(this).data('max');
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
            if(count > 0){
                var product = $(this).parents(catalogList.basketProductBlock);

                $.ajax({
                    method    :   'POST',
                    url       :   catalogListAjaxPath + 'change-count-variant-list-product',
                    data      :   {
                        'list' : product.data('list-id'),
                        'category' : product.data('category-title'),
                        'variant-id' : $(this).parents('.block-2').data('variant'),
                        'count' : count,
                    },
                });
                product.find('.block-2.open .num').html(count+' шт.');
                product.find('.product-list-plus-minus').data('current-count',count);

            }
        }
    });

    //------------------------------------

    // Окошко с выбором вариаций;
    catalogList.page.on('click','div.tags-items.select',function(){
        $(this).siblings('div.variations-select').toggle();
        $('div.variations-select').not($(this).siblings('div.variations-select')).hide();
    });

    /*-----------------------------*/

    catalogList.page.on('click','.close-change',function(){
        $(this).parent('.change-product-container-block').hide();
        $("#br-show").hide();
    });

    catalogList.page.on('click','.close-variant',function(){
        $(this).parent('.variations-select').hide();
    });

    /*-----------------------------*/

    catalogList.page.on('click','.small-product-list-packet-block .small-product',function(){
        var action = $(this).parents('.change-product-container-block').data('action');
        var currentProduct = $(this);
        var changedProduct,
            categoryId;
        if(action == 'change'){
            changedProduct = $(this).parents('.js-basket-item');
            categoryId = '-';
            $("#br-show").hide();
            loading('show');
        }else{
            changedProduct = $(this).parents('.append-category-product');
            categoryId = $(this).parents('.change-product-container-block').data('category');
        }
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'get-list-product-change-item',
            data      :   {
                'variant' : changedProduct.data('variant-id'),
                'count' : currentProduct.data('count'),
                'change-variant' : currentProduct.data('variant'),
                'list' : changedProduct.data('list-id'),
                'category' : categoryId,
            },
            success   :   function(response){
                if(action == 'change'){
                    changedProduct.after(response).remove();
                    $("#br-show").hide();
                }else{
                    $('.small-product[data-variant='+currentProduct.data('variant') +']').remove();
                    changedProduct.siblings('.content-list-goods[data-variant-id='+ changedProduct.data('item-id') +']').after(response);
                }
               // alert(response);

                loading('hide');
            }
        });
    });

    catalogList.page.on('click','.change-product-button, .append-category-product > span',function(){
            $("#br-show").show();
        $(this).siblings('.change-product-container-block').toggle("slow", function() {

            if($( this ).is( ":visible" ) && $(this).children('img').length > 0){
                var containerBlock = $(this);
                containerBlock.find('.small-product-list-packet-block').remove();
                containerBlock.find('.preload-image').show();
                $.ajax({
                    method    :   'POST',
                    url       :   catalogListAjaxPath + 'get-list-product',
                    data      :   {
                        'category' : $(this).data('category'),
                        'product' : $(this).data('product'),
                        'list' : $(this).data('list'),
                    },
                    success   :   function(response){
                        containerBlock.append(response).find('.preload-image').hide();
                    }
                });
            }
        });
        catalogList.page.find('.change-product-container-block').not($(this).siblings('.change-product-container-block')).hide();
    });

    catalogList.page.on('click','.append-category > span',function(){
        $(this).siblings('.change-category-container-block').toggle("slow", function() {
            if($( this ).is( ":visible" ) && $(this).children('img').length > 0){
                var containerBlock = $(this);
                containerBlock.find('.small-product-list-packet-block').remove();
                containerBlock.find('.preload-image').show();
                $.ajax({
                    method    :   'POST',
                    url       :   catalogListAjaxPath + 'get-list-category',
                    data      :   {
                        'list' : $(this).parents('.append-category').data('list-id'),
                    },
                    success   :   function(response){
                        containerBlock.append(response).find('.preload-image').hide();
                        $("#br-show").hide();
                    }
                });
            }
        });
        catalogList.page.find('.change-category-container-block').not($(this).siblings('.change-category-container-block')).hide();

    });

    catalogList.page.on('click','.delete-list-product',function(){
        var containerBlock = $(this).parents(catalogList.basketProductBlock);
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'remove-list-product',
            data      :   {
                'list' : $(this).data('list-id'),
                'variant' : $(this).data('variation-id'),
            },
            success   :   function(response){
                if(containerBlock.next().hasClass('js-basket-item') || containerBlock.prev().hasClass('js-basket-item')){

                }else{
                    containerBlock.prev('.head-product-list-view').remove();
                }

                containerBlock.remove();
            }
        });
    });

    /*-----------------------------*/

    catalogList.page.on('click','.save-product-list div',function(){

        if($(this).parents('.save-product-list-block').find('input[name=product-list-name]').val() == ''){
            return alert('Введите название для списка товаров');
        }
        loading('show');
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'save-product-list',
            data      :   {
                'name' : $(this).parents('.save-product-list-block').find('input[name=product-list-name]').val(),
                'list-id' : $(this).parents('.save-product-list-block').find('input[name=list-id]').val(),
            },
            success   :   function(response){
                if(response > 0){
                    // Всё ок, список сохранён
                    window.location = domain+'/catalog/product-list/'+response;
                }else{

                }
                loading('hide');
            }
        });
    });

    catalogList.page.on('click','.buy-product-list div',function(){
        loading('show');
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'buy-product-list',
            data      :   {
                'list-id' : $(this).parents('.save-product-list-block').find('input[name=list-id]').val(),
            },
            success   :   function(response){
                if(response == 1){
                    alert_show('Товары добавлены в корзину!');
                    shop.reloadBasketSmall();
                    // Всё ок, список добавлен в корзину
                }else{

                }
                loading('hide');
            }
        });
    });

    /*-----------------------------*/

    catalogList.page.on('click','.delete-basket-product',function(){
        var basketItem = $(this).parents(catalogList.basketProductBlock);
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'remove-basket-product',
            data      :   {
                'data' : $(this).data()
            },
            success   :   function(response){
                basketItem.remove();
                catalogList.emptyProductBlocksRemove();
                catalogList.reloadBasketResult();
            }
        });
        return false;
    });

    catalogList.page.on('click',catalogList.removeAllClass,function(){
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'remove-all-basket-product',
            success   :   function(response){
                window.location.reload();
            }
        });
        return false;
    });

    catalogList.page.on('click','.small-product-list-packet-block-arrow-left',function(){
        $(this).parent('.small-product-list-packet-block').hide().removeClass('open').removeAttr('style').prev('.small-product-list-packet-block').addClass('open');
    });

    catalogList.page.on('click','.small-product-list-packet-block-arrow-right',function(){
        $(this).parent('.small-product-list-packet-block').hide().removeClass('open').removeAttr('style').next('.small-product-list-packet-block').addClass('open');
    });


    //if($("#basket").length) {
    //    $("#basket div.details").affix({
    //        offset: {
    //            bottom: function () {
    //                return (this.bottom = ($("#basket .payments").outerHeight(true) - 300))
    //            }
    //        }
    //    });
    //    if($("#basket div.details").filter('.affix').length) {
    //        $('#basket div.button_pay').addClass('affix');
    //    }
    //    $("#basket div.details").on('affix-bottom.bs.affix',function(){
    //        console.log('bottom');
    //        $('#basket div.button_pay').removeClass('affix');
    //    });
    //    $("#basket div.details").on('affix.bs.affix',function(){
    //        console.log('top');
    //        $('#basket div.button_pay').addClass('affix');
    //    });
    //}

});
