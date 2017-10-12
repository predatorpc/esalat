
var versionBasket = "Basket.js version: 5.0.2@30092016";
console.warn(versionBasket);

var basket = {};
basket.pageIndex                        = '#basket';
basket.setOrderButtonClass              = '.order-success';
basket.generalForm                      = '.basket-general-form';

basket.addressIndex                     = '#address';
basket.deliveryAddress                  = '#delivery-type-and-address-select-block';

basket.resultBlockID                    = '#basket-page-result-data';
basket.paymentBlockID                   = '#basket-page-payments';
basket.dateTimeBlockID                  = '#basket-page-date-time-data';
basket.basketProductBlock               = '#basket-product-block';
basket.basketProductBlockPath           = '#basket-product-block';

basket.basketProductBlock               = '.js-basket-item';
basket.changeCountProductClass          = '.product-list-plus-minus';
basket.basketChangeProductVariantClass  = '.tag-value-group-item';
basket.propertyBlockClass               = '.tag-value-group-items';
basket.inputBlockVariantClass           = '.js-control-buttons-for-variant';

basket.productTypeBlockClass            = '.product-type-block';
basket.productTypeBigBlockClass         = '.product-type-big-block';

basket.deliveryAddressBlock             = '#delivery-type-and-address-select-block';
basket.findStoreBlockId                 = '#basket-find-store-block';
//basket.addressInput                     = basket.deliveryAddressBlock + ' input[name=address-id]';
basket.removeAllClass                   = '.remove-all-basket-products',

basket.noDisplayClass                   = '.not-display';
basket.okParamResult                    = 0;

//---------------------

basket.reloadBasketResult = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-result',
        success   :   function(response){
            $(basket.resultBlockID).html(response);
        }
    });
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-store-block',
        success   :   function(response){
            $(basket.findStoreBlockId).html(response);
        }
    });
}

basket.reloadPayment = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-payment',
        success   :   function(response){
            $(basket.paymentBlockID).html(response);
        }
    });
}

basket.reloadDateTime = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-date-time',
        success   :   function(response){
            $(basket.dateTimeBlockID).html(response);

        }
    });
}

basket.reloadAddressList = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-address-list',
        success   :   function(response){
            // Обновления страниы ;
            $("#address-modal").html($(response).find("#address-modal").html());
            // Инилизация карта;
            if (typeof ymaps_re == 'function') {
                ymaps_re();
            }

        }
    });
}

basket.reloadAddressNotification = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-address-list',
        success   :   function(response){
            // Обновления страниы ;
            $("#basket-page-deliveries").empty();// html($(response).find("#basket-page-deliveries").html());
            $("#basket-page-deliveries").html(response);
            // Инилизация карта;
            if (typeof ymaps_re == 'function') {
                ymaps_re();
            }
        }
    });
}

basket.reloadBasketProductList = function(){

    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-product-list',
        success   :   function(response){
            $(basket.basketProductBlock).html(response);
        }
    });
}

basket.reloadBasketProductListNew = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-product-list',
        success   :   function(response){
            $(basket.basketProductBlockPath).html(response);
        }
    });
}

basket.reloadProductItem = function(product){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-basket-product',
        data      :   {
            'basketItemId' : product.data('basket-item')
        },
        success   :   function(response){
            product.after(response);
            basket.closeProductProperties(product.next());
            product.remove();
        }
    });
}

//---------------------

basket.closeProductProperties = function(productBlock){    // получаем продукт
    var currentActiveVariant = productBlock.data('basket-item');
    if($('div'+basket.basketChangeProductVariantClass+'[data-variant-id='+currentActiveVariant+']').length > 1){
        $('div'+basket.basketChangeProductVariantClass+'[data-variant-id=' + currentActiveVariant + ']').not(productBlock).remove();
    }
    if(productBlock.find(basket.propertyBlockClass).length > 1){   // если свойств больше одного
        productBlock.find($(basket.basketChangeProductVariantClass)).removeClass('disabled');   // разблокируем все всойства

        productBlock.find(basket.propertyBlockClass).each(function(i,element){ // перебираем свойства
            var currentPropertyId = $(element).data('tag-group-id');    // ID свойства
            var currentPropertyActive = $(element).find('.open').data('tag-id');  //ID варианта активного свойства
            var currentPropertySimplex = {};

            productBlock.find(basket.inputBlockVariantClass).each(function(j,inputBlock){  // перебираем блоки покупки варианта товара
                var property = $(inputBlock).data('json');  // свойства варианта товара
                //console.log('property');
                //console.log(inputBlock);
                //console.log(property);
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
                    $.each(productBlock.find(basket.propertyBlockClass + "[data-tag-group-id=" + l + "] " + basket.basketChangeProductVariantClass),function(m,tagValueElement){
                        if(currentPropertySimplex[l][$(tagValueElement).data('tag-id')] == undefined){
                            $(tagValueElement).addClass('disabled');    // блокируем свойство - нет варианта с данным и активным свойством
                        }
                    });
                }
            });
        });
    }
    //console.log('property END');

}

basket.changeCountProduct = function(action,parent,basketId,count){
    shop.changeCountProduct(action,parent,basketId,count,basket.pageIndex);
}

basket.emptyProductBlocksRemove = function(){
    if($(basket.productTypeBlockClass).length > 0){
        $(basket.productTypeBlockClass).each(function(i,element){
            if($(element).find(basket.basketProductBlock).length > 0){

            }else{
                if($(element).parents(basket.productTypeBigBlockClass).length > 0){
                    $(element).parents(basket.productTypeBigBlockClass).remove();
                }
            }
        });
    }
    basket.isEmptyBasket();
}

basket.isEmptyBasket = function(){
    if($(basket.basketProductBlock).length > 0){

    }else{
        window.location = "/basket/";
    }
}

basket.sendGeneralFormData = function(){
    var storeList = {};
    $('.all-products-input-for-set-store-params input').each(function(i,element){
        storeList[$(element).data('product-id')] = $(element).val();
    });

    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'set-empty-basket-params',
        data      :   {
            'basket-id'        : $(basket.generalForm).find('[name=basket-id]').val(),
            'StoreList'        : storeList,
            'metro-club-index' : $(basket.generalForm).find('[name=currentClub]').val(),
        },
        success   :   function(response){
            $(basket.generalForm).attr('action', "/my/order-report").submit();
        }
    });
}

basket.sendGeneralFormDataInPaymentCenter = function(){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'set-empty-basket-params',
        data      :   {
            'params'        : $(basket.generalForm).serialize(),
        },
        success   :   function(response){

        }
    });
}

// Вызов модального окно;
basket.modalDeliveryFree = function(){
    return false;
    /*if($("#delivery-type-and-address-select-block div.my-deliveries").filter('.no').length) return false;
    $.post(window.location.href, {'modalDeliveryFree': true});
    return window_show('basket/default/modal','Акция Gold на 1 месяц');*/
}

$(document).ready(function(){
    if($('#poligon').length > 0){
        ymaps_re();
    }
    // Прогресс бар;
    $('#indicatorContainer').radialIndicator({
        barColor: '#87CEEB',
        barWidth: 5,
        initValue: $("#initValue").val(),
        radius : 40,
        roundCorner: true,
        percentage: true
    });
    basket.page = $(basket.pageIndex);

    basket.reloadDateTime();

    basket.page.find(basket.basketProductBlock).each(function(i,element){
        basket.closeProductProperties($(element));
    });

    $(shop.modalWindow).on('click',basket.setOrderButtonClass,function(){
        if($(this).hasClass('payment-center')){
            basket.sendGeneralFormDataInPaymentCenter();
            $(this).parents('form').submit();
        }else{
            // basket.sendGeneralFormDataInPaymentCenter();
            basket.sendGeneralFormData();
        }
        $(this).remove();

        try {
            // Отправка данных в Яндекс.Метрику 20160818 - Для сбора данные при нажатие кнопка оформить;
          //  yaCounter30719268.reachGoal('pay_001');
//40642635
        } catch (err) {
            // ;
        }
    });

    basket.page.on('click',basket.basketChangeProductVariantClass,function(){
        var tagElement = $(this);
        var parentElement = tagElement.parents(basket.basketProductBlock);


        if($(this).hasClass('disabled')){
            //
        }else if($(this).hasClass('open')){
            //  $(this).removeClass('open');
            basket.closeProductProperties(parentElement);

        }else{
            var tagsActive = parentElement.find(basket.basketChangeProductVariantClass + '.open');
            var tagIds = [];
            var currentTypeTagId = tagElement.parent().find(basket.basketChangeProductVariantClass + '.open').data('tag-id');
            tagsActive.each(function(i,element){
                if($(element).data('tag-id') != currentTypeTagId){
                    tagIds.push($(element).data('tag-id'));
                }
            });
            tagIds.push(tagElement.data('tag-id'));

            // console.log('C');
            // console.log(parentElement,tagElement,tagElement.data('basket-item-id'),tagElement.data('product-id'),tagElement.data('variant-id'),tagIds,basket.pageIndex);
            shop.changeProductVariant(parentElement,tagElement,tagElement.data('basket-item-id'),tagElement.data('product-id'),tagElement.data('variant-id'),tagIds,basket.pageIndex,tagElement.data('count-item'));
        }
    });

    basket.page.on('change select','[name=delivery-address-select]',function(){

        if($(this).hasClass('select-input')){
            basket.deliveryId = $(this).find('option:selected').data('delivery-id');
            basket.addressId = $(this).find('option:selected').data('address-id');
        }else{
            basket.deliveryId = $(this).data('delivery-id');
            basket.addressId = $(this).data('address-id');
        }
        basket.modalDeliveryFree();
        $("#basket-page-date-time-data").slideDown();
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'change-delivery',
            data      :   {
                'deliveryId'        : basket.deliveryId,
                'addressId'         : basket.addressId,
            },
            success   :   function(response){
                $(basket.deliveryAddressBlock).html(response);
                basket.reloadPayment();
                basket.reloadDateTime();
                basket.reloadBasketResult();
                if (typeof ymaps_re == 'function') {
                    ymaps_re();
                }

            }
        });
    });

    //basket.page.on('click','input[name=delivery-address-select]',function(){
    //    basket.deliveryId = $(this).data('delivery-id');
    //    basket.addressId = $(this).data('address-id');
    //
    //    $.ajax({
    //        method    :   'POST',
    //        url       :   basketAjaxPath + 'change-delivery',
    //        data      :   {
    //            'deliveryId'        : basket.deliveryId,
    //            'addressId'         : basket.addressId,
    //        },
    //        success   :   function(response){
    //            $(basket.deliveryAddressBlock).html(response);
    //            basket.reloadPayment();
    //            basket.reloadDateTime();
    //            basket.reloadBasketResult();
    //            if (typeof ymaps_re == 'function') {
    //                ymaps_re();
    //            }
    //        }
    //    });
    //});

    basket.page.on('click',basket.changeCountProductClass,function(){
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
                product         = $(this).parents(basket.basketProductBlock);
                var basketId    = $(this).data('basket');

                basket.changeCountProduct(action,product,basketId,count);
            }else{
                //удаляем товар с корзины;
                $(this).parents('div.content-goods').children('.delete-basket-product').click();
                console.log('DELETE');
                //basket.page.on('click','.delete-basket-product',function(){
            }
        }
    });

    basket.page.on('click',basket.addressIndex + ' .button',function(){
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'add-new-address',
            data      :   {
                'city' : $(basket.addressIndex).find('.city[name=city]').val(),
                'street' : $(basket.addressIndex).find('.street[name=street]').val(),
                'house' : $(basket.addressIndex).find('.house[name=house]').val(),
                'room' : $(basket.addressIndex).find('.room[name=room]').val(),
                'district' : $(basket.addressIndex).find('.district[name=district]').val(),
                'delivery_id' : $('#delivery_id').val(),
                'comments' : $(basket.addressIndex).find('[name=comments]').val(),
                'phone' : $(basket.addressIndex).find('.number[name=phone]').val()
            },
            success   :   function(response){
                if(response == 'OK') {
                    $('#address-modal').modal('hide');
                    window.location.reload();
                }else {
                    $("#address div.error").text(response).show();
                    // basket.reloadAddressList();
                }
                // $('.modal-backdrop.fade.in').remove();
            }
        });
        return false;
    });

    basket.page.on('change',basket.paymentBlockID + ' input[name=payment_id]',function(){
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'change-payment',
            data      :   {
                'paymentId'        : $(this).val(),
            },
            success   :   function(response){

            }
        });
    });

    basket.page.on('change',basket.paymentBlockID + ' input[name=bonus_pay]',function(){
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'change-bonus-pay',
            data      :   {
                'paymentId'        : $(this).val(),
            },
            success   :   function(response){
                basket.reloadBasketProductListNew();
                basket.reloadAddressNotification();
                basket.reloadPayment();
                basket.reloadDateTime();
                basket.reloadBasketResult();
                shop.reloadBasketSmall();

            }
        });
    });


    //------------------------------------

    basket.page.on('click','.type_name',function(){
        $(this).siblings('.product-list-block').toggle();
        $('.product-list-block').not($(this).siblings('.product-list-block')).hide();
    });

    // Окошко с выбором вариаций;
    basket.page.on('click','div.tags-items.select',function(){
        $(this).siblings('div.variations-select').toggle();
        $('div.variations-select').not($(this).siblings('div.variations-select')).hide();
    });

    basket.page.on('click','.delete-basket-product',function(){
        var basketItem = $(this).parents(basket.basketProductBlock);
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'remove-basket-product',
            data      :   {
                'data' : $(this).data()
            },
            success   :   function(response){
                basketItem.remove();
                basket.reloadBasketProductListNew();
                basket.emptyProductBlocksRemove();
                basket.reloadAddressNotification();
                basket.reloadPayment();
                basket.reloadDateTime();
                basket.reloadBasketResult();
                shop.reloadBasketSmall();
            }
        });
        return false;
    });

    basket.page.on('click',basket.removeAllClass,function(){
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'remove-all-basket-product',
            success   :   function(response){
                window.location.reload();
            }
        });
        return false;
    });

    basket.page.on('focusout, blur','.field-for-promo-insert',function(){
        if($(this).val() != '' && $(this).val().length > 3){
            $.ajax({
                method    :   'POST',
                url       :   basketAjaxPath + 'set-promo',
                data      :   {
                    'promo_code_id' : $(this).val()
                },
                success   :   function(response){
                    basket.page.find('.promo-code-input-block').html(response);
                    basket.reloadPayment();
                    basket.reloadBasketResult();
                }
            });
        }
    });

    basket.page.on('click','.select__form.time_select .option',function(){
        var ajaxPath = '';
        var flag = false;
        if($(this).hasClass('date-variation')){
            ajaxPath = basketAjaxPath + 'change-date';
        }else if($(this).hasClass('time-variation')){
            ajaxPath = basketAjaxPath + 'change-time';
            flag = true;
            $("#pay-order").slideDown();
        }
        $.ajax({
            method    :   'POST',
            url       :   ajaxPath,
            data      :   {
                'data' : $(this).data()
            },
            success   :   function(response){

                $('.date-time-change-block .step-container').html(response);
            }
        });
    });

    basket.page.on('click','.save-product-list-block div div',function(){
        var listName = $(this).parents('.save-product-list-block').find('input.product-list-name-input').val();

        if(listName == ''){
            return alert('Введите название для списка товаров');
        }

        $('.save-product-list-block div').remove();
        $('.save-product-list-block input').remove();
        $.ajax({
            method    :   'POST',
            url       :   catalogListAjaxPath + 'save-product-list-from-basket',
            data      :   {
                'name' : listName,
            },
            success   :   function(response){
                $('.save-product-list-block').html('<p style="text-align: right;">Текущая корзина сохранена как '+listName+'</p>');
            }
        });
    });

    // Фиксированый блок 2016.08.18;
    if($("#basket").length) {
        if($(window).width() >= 1199) {
            $("#basket div.details").affix({
                offset: {
                    bottom: function () {
                        return (this.bottom = ($("#basket .payments").outerHeight(true) - 300))
                    }
                }
            });
            if ($("#basket div.details").filter('.affix').length) {
                $('#basket div.button_pay').addClass('affix');
            }
            $("#basket div.details").on('affix-bottom.bs.affix', function () {
                console.log('bottom');
                $('#basket div.button_pay').removeClass('affix');
            });
            $("#basket div.details").on('affix.bs.affix', function () {
                console.log('top');
                $('#basket div.button_pay').addClass('affix');
            });
        }
    }

});


console.log("basket.js --- Ok");