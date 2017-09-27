var shop = {};

shop.pageClass                       = '.shop-container';
shop.headerId                        = '#header';
shop.smallBasketClass                = '.small-basket-block';
shop.modalWindow                     = '#windows';

shop.addNewAddressElement            = '.add-new-address';

shop.changeCountProductPlusClass     = '.plus';
shop.changeCountProductMinusClass    = '.minus';
shop.actionPlus                      = 'plus';
shop.actionMinus                     = 'minus';

shop.shopGeneralCatalogMenuId        = '#menu-top';

shop.changeProductVariant = function(parentElement,element,basketItemId,productId,variantId,tagIds,page,count_item){

   // alert(basketItemId +'---'+productId+'---'+variantId+ '---'+tagIds+'---'+page);
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'change-product-variant',
        data      :   {
            'basketItemId' : basketItemId,
            'productId'    : productId,
            'variantId'    : variantId,
            'tagIds'       : tagIds,
            'count_pack'       : count_item,
        },
        success   :   function(response){
            if(page == '#basket'){
                console.log('basket!!!');
                basket.reloadProductItem(parentElement);
                basket.reloadPayment();
                basket.reloadBasketResult();
                shop.reloadBasketSmall();
            }else if(page == '#catalog-product-list-view'){
                console.log('list!!!');
                catalogList.reloadProductItem(parentElement);
                catalogList.reloadBasketResult();
            }else{
                shop.reloadBasketSmall();
            }
            //
            //
            //parentElement.after(response);
            //if(page == basket.pageIndex){
            //    basket.closeProductProperties(parentElement.next('div'));
            //}
            //parentElement.remove();
            //
            //basket.reloadPayment();
            //basket.reloadBasketResult();
            //shop.reloadBasketSmall();
        }
    });
}

shop.changeCountProduct = function(action,parent,basketId,count,page){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'change-count-product',
        data      :   {
            'basketId'     : basketId,
            'action'       : action,
            'count'        : count,
        },
        success   :   function(response){
            if(page == '#basket'){
                //basket.reloadProductItem(parent);
                basket.reloadPayment();
                basket.reloadBasketResult();
                shop.reloadBasketSmall();
                basket.reloadBasketProductListNew();
            }else{
                shop.reloadBasketSmall();
            }
        }
    });
}

shop.reloadBasketSmall = function(flag){
    $.ajax({
        method    :   'POST',
        url       :   basketAjaxPath + 'get-small-basket',
        success   :   function(response){
            $(shop.smallBasketClass).html(response);
            // Мобильная версия;
            if($('#basket-total-info').length) {
                if($(response).find('span.has').has('a.no-border').length > 0) {
                    var html = $(response).find('.total-top-info').html();
                    $('#header').css('min-height', '140px');
                    $('#basket-total-info').html('<div class="total-top-info">' + html + '</div>');
                }
            }
            if($("#header div.fix-content-panel").length) {
                var html = $(response).find('.has').html();
                $('#header div.fix-content-panel div.total').html(html);
            }

            // Преолдер для список товары;
            loading('hide');
            if($("div.goods-list").length && flag === 1) alert_show('Товары добавлены в корзину!');
            // Вызов модалка акция;
            if($('#stockModal').length && flag === 2) {
                window_show('basket/default/stock-modal','Товар добавлен в корзину!');
            }
        }
    });
}

// Открытие окон;
shop.windowShow = function(url,title,size,ymapasFlag) {
    var modalContainer = $('#windows');
    var statusAjax = false;
    var modalBody = modalContainer.find('.modal-body');
    var textComments = ($("#basket div.comments textarea").val() != '' ? $("#basket div.comments textarea").val() : 'Нет');

    // Размер окно;
    if(size == 'mid') {
        $("#windows .modal-dialog").addClass('modal-max').removeClass('modal-min');
    }else if(size == 'max') {
        $("#windows .modal-dialog").addClass('modal-big').removeClass('modal-min');
    }else if(size == 'lg') {
        $("#windows .modal-dialog").addClass('modal-lg').removeClass('modal-min');
    }
    modalContainer.modal({show:true});
    if(title){
        $("#windows .modal-title").text(title);
    }else{
        $("#windows .modal-title").text('');
    }

    $.ajax({
        url: url,
        type: "POST",
        data: {/*'userid':UserID*/},
        success: function (data) {
            $('#windows .modal-body').html(data);
            $('#comments-modal').text(textComments);

            modalContainer.modal({show:true});
            if (ymapasFlag === true && typeof ymaps_re == 'function') {
                ymaps_re();
            }
            statusAjax = true;

            routerStore.checkStores(statusAjax);
            console.log('END__');
        }
    });
    return false;
}

$(document).ready(function(){
    shop.reloadBasketSmall();
    $(shop.addNewAddressElement)

    $('.fixed-admin-block .clear-cash-button').click(function(){
        $.ajax({
            method    :   'POST',
            url       :   generalAjaxPath + 'clear-cache',
            //data      :   {
            //    'cacheKey' : $(this).data('cache-key'),
            //},
            success   :   function(response){
                location.reload();
            }

        });
    });

    $(shop.pageClass).on('click',shop.smallBasketClass + ' .delete',function(){
        var clickElement = $(this);
        $.ajax({
            method    :   'POST',
            url       :   basketAjaxPath + 'remove-basket-product',
            data      :   {
                'data' : $(this).data()
            },
            success   :   function(response){
                if($(shop.pageClass).data('page') == 'basket'){
                    var basketItem = basket.page.find(basket.basketProductBlock + '[data-basket-item='+clickElement.data('basket-item')+']');
                    basketItem.remove();
                    basket.emptyProductBlocksRemove();
                    basket.reloadPayment();
                    basket.reloadDateTime();
                    basket.reloadBasketResult();
                    shop.reloadBasketSmall();
                }else{
                    shop.reloadBasketSmall();
                }
            }
        });
        return false;
    })

    $('.order-report-filter-shop-list').on('keyup','#shops-id',function(){
        var shopName = $(this);
        if(shopName.val().length > 2){
            $.ajax({
                method    :   'POST',
                url       :   reportsAjaxPath + 'get-shop',
                data      :   {'shopName':shopName.val()},
                success   :   function(response){
                    shopName.siblings('.help-block').append(response);
                }
            });
        }
    });

    $('.order-report-filter-shop-list').on('click','.shop-list-fixed-position div',function(){
        var shopId = $(this);
        $.ajax({
            method    :   'POST',
            url       :   reportsAjaxPath + 'add-shop',
            data      :   {'shopId':shopId.data('shop-id'),'i':$('.order-report-filter-shop-list').find('.order-report-filter-shop-element').length},
            success   :   function(response){
                $('.order-report-filter-shop-children').append(response);
                $('.order-report-filter-shop-list').find('.shop-list-fixed-position').remove();
            }
        });
    });

    $('.order-report-filter-shop-list').on('click','.order-report-filter-shop-element',function(){
        var shopId = $(this);
        $.ajax({
            method    :   'POST',
            url       :   reportsAjaxPath + 'remove-shop',
            data      :   {'shopId':shopId.data('shop-id')},
            success   :   function(){
                shopId.remove();
            }
        });
    });
    //----------------------
    $('.order-report-filter-client-list').on('keyup','#client-id',function(){
        var clientName = $(this);
        if(clientName.val().length > 2){
            $.ajax({
                method    :   'POST',
                url       :   reportsAjaxPath + 'get-client',
                data      :   {'clientName':clientName.val()},
                success   :   function(response){
                    clientName.siblings('.help-block').html(response);
                }
            });
        }
    });

    $('.order-report-filter-client-list').on('click','.client-list-fixed-position div',function(){
        var clientId = $(this);
        $.ajax({
            method    :   'POST',
            url       :   reportsAjaxPath + 'add-client',
            data      :   {'clientId':clientId.data('client-id'),'i':$('.order-report-filter-client-list').find('.order-report-filter-client-element').length},
            success   :   function(response){
                $('.order-report-filter-client-children').append(response);
                $('.order-report-filter-client-list').find('.client-list-fixed-position').remove();
            }
        });
    });

    $('.order-report-filter-client-list').on('click','.order-report-filter-client-element',function(){
        var clientId = $(this);
        $.ajax({
            method    :   'POST',
            url       :   reportsAjaxPath + 'remove-client',
            data      :   {'clientId':clientId.data('client-id')},
            success   :   function(){
                clientId.remove();
            }
        });
    });
    //------------------
    $('.product-status-list').on('click','input[type=checkbox]',function(){
        $.ajax({
            method    :   'POST',
            url       :   '/ajax-management/change-product-param',
            data      :   {'productId':$(this).parents('tr').data('key'),'params':$(this).attr('name'),'value':$(this).val()},
            success   :   function(){

            }
        });
    });

    $('.product-status-list').on('keyup','input[type=text]',function(){
        $.ajax({
            method    :   'POST',
            url       :   '/ajax-management/change-product-param',
            data      :   {'productId':$(this).parents('tr').data('key'),'params':$(this).attr('name'),'value':$(this).val()},
            success   :   function(){

            }
        });
    });
    //------------------
    $('.order-element').on('click','.order-item-status .residence-status',function(){
        var btn = $(this).parent();
        $.ajax({
            method    :   'POST',
            url       :   reportsAjaxPath + 'get-order-item-data',
            data      :   {'itemId':btn.data('order-item-id'),'groupId':btn.data('order-group-id'),'orderId':btn.data('order-id')},
            success   :   function(response){
                btn.find('.shop-list-fixed-position').remove();
                btn.append(response);
            }
        });
    });

    $('.order-element').on('click','.change-status',function(){
        var btn = $(this).parents('.order-item-status');
        $.ajax({
            method    :   'POST',
            url       :   reportsAjaxPath + 'change-order-item-status',
            data      :   {'itemId':btn.data('order-item-id'),'groupId':btn.data('order-group-id'),'orderId':btn.data('order-id'),'status':$(this).data('new-status-id')},
            success   :   function(response){
                btn.find('.shop-list-fixed-position').remove();
                btn.html(response);
            }
        });
    });

    $('.order-element').on('click','.btn-success',function(){
        var btn = $(this).parents('.order-item-status');
        btn.find('.shop-list-fixed-position').remove();
    })

    // $('#ownerorderfilter-producttype').addClass('hidden');
    // $('.field-ownerorderfilter-producttype .control-label').click(function(){
    //     if($('#ownerorderfilter-producttype').hasClass('hidden')){
    //         $('#ownerorderfilter-producttype').removeClass('hidden');
    //     }else{
    //         $('#ownerorderfilter-producttype').addClass('hidden');
    //     }
    // });

    $('.add-action-param').click(function(){
        $.ajax({
            method    :   'POST',
            url       :   actionsAjaxPath + 'add-action-param',
            data      :   {'i':$('.action-param-list > hr').length,'paramId':$('.action-param-select').val()},
            success   :   function(response){
                $('.action-param-list').append(response);
            }
        });
    });

    /*-------------------------МАСТЕР ПОМОЩНИК---------------------------------*/

    // Открыть;
    $(document).on('click','#help-master div.help',function(){
        var widthSize = $("#help-master .content-master");

        $(this).parents('#help-master .body-master').addClass('open');
        $(this).parents('#help-master').css('right','0');
        // Сколько кликов совершили метрика;
        yaCounter30719268.reachGoal('masterclick');

        // Инилизация верстка товара;
        if($(window).width() <= 991) {
            widthSize.show();
            $('div.goods .js-shadow').addClass('br-shadow-goods');
        }else{
            widthSize.animate({left:'50%', marginLeft: - widthSize.width()/2}, 300).show();
            $("#menu-top,.category___sidebar.category-list").css('display','none');
            if($('#center').has('.goods').length) $('div.sidebar').css('width','100%');
            if(!$('div.goods.goods-new').length) $('div.goods').css('width','100%').add('#list-wrapper').removeClass('goods-list').addClass('goods-top');
        }
        $(this).hide();

        if($('#help-master .text-content:visible').length) {
            $('#br-show').show();
        }
        $.post(shopAjaxPath + 'set-master-status', {'status':1,'category':$(this).siblings('.content-master').data('id')},function(data){

        });
        // Разделяем блоки по категориям;
        if($("#list-wrapper").length && !$('div.goods.goods-new').length) {
            loading('show');
            $("#list-wrapper").load(window.location.href + ' #sort',function() {
                loading('hide');
                if ($("#list-wrapper").has(".js-disabled-page").length) {
                     $('#sort div.groups').each(function (index, item) {
                        var group_id = $(this).data('group-category');
                        var countSize = $("#list-wrapper div.groups[data-group-category='" + group_id + "'] div.sort_item").size();
                        $("#list-wrapper div.groups[data-group-category='" + group_id + "'] div.sort_item").slice(0, 5).removeClass('hidden');
                        if(countSize > 5) {
                           $("#list-wrapper div.groups[data-group-category='" + group_id + "']").append('<div class="sort_item next_item" rel="5"><div class="item"> <div class="block "><div class="next next-icon"></div></div></div></div>');
                        }
                    });
                    if (typeof loadGoodsCategory == 'function') {
                        loadGoodsCategory(true);
                    }
                }else{
                    $("#list-wrapper div.sort_item").removeClass('hidden');
                }
            });
            $("#list-wrapper .content-load").remove();
        }

        if (typeof loadGoods == 'function') {
             loadGoods == null;
            console.log('loadGoods');
        }

    });

    // Раскрываем блоки;
    $(document).on('click','div.next_item',function(countGoods){
        var countGoods = parseInt($(this).attr('rel'));
        var countSizeHidden = $("div.sort_item:hidden",$(this).parents('.groups')).size();
        countGoods +=5;
        $("div.sort_item",$(this).parents('.groups')).slice(0, countGoods).removeClass('hidden');
        $(this).attr('rel',countGoods);
        countSizeHidden -= 5;
        if(countSizeHidden <= 0) {
            $(this).remove();
        }
    });

    // Состояние сессия;
    if($("#help-master .body-master").filter('.open').length) {
        $("#menu-top,.category___sidebar.category-list").css('display','none');
        $('div.goods').css('width','100%').add('#list-wrapper').removeClass('goods-list').addClass('goods-top');
        if($('#center').has('.goods').length) {
            $('div.goods .js-shadow').addClass('br-shadow-goods');
            $('div.sidebar').css('width','100%');
        }
        $('#help-master').css('right','0');
        if($('#help-master .text-content:visible').length) {
            $('#br-show').show();
        }

        // Разделяем блоки по категориям;
        if(!$("#list-wrapper").has(".js-disabled-page").length > 0) {
            $("#list-wrapper div.next_item").remove();
            $("div.sort_item").removeClass('hidden');
        }else{
            $("div.groups","#list-wrapper").each(function(index,item){
                var group_id = $(this).data('group-category');
                var countSize = $("#list-wrapper div.groups[data-group-category='" + group_id + "'] div.sort_item").size();
               // console.log(group_id);
                $("#list-wrapper div.groups[data-group-category='" + group_id + "'] div.sort_item").slice(0, 5).removeClass('hidden');
                if(countSize > 5) {
                    $("#list-wrapper div.groups[data-group-category='" + group_id + "']").append('<div class="sort_item next_item" rel="5"><div class="item"> <div class="block "><div class="next next-icon"></div></div></div></div>');
                }
            });
            $("#list-wrapper .content-load").remove();
        }
        if (typeof loadGoodsCategory == 'function') {
            loadGoodsCategory(true);
        }
    }
    // Закрыть;
    $(document).on('click','#help-master button.close,#br-show',function() {
        var widthSize = $("#help-master .content-master");

        $.post(shopAjaxPath + 'set-master-status', {'status': 0},function(data){
            // Разделяем блоки по категориям;
            if($("#list-wrapper").length && !$('div.goods.goods-new').length) {
                loading('show');
                window.location.reload();
               // $("#list-wrapper div.items").html($(data).find('#list-wrapper div.items').html());
                /*
                 $("#list-wrapper").load(window.location.href + ' #list-wrapper',function() {
                 loading('hide');
                 //Инилизация подгрузка контент;
                 if(!$("#list-wrapper").has(".js-disabled-page").length) {
                 loadGoods(true);
                 }
                 });*/
                // window.location.reload();
               // $("#list-wrapper div.sort_item").removeClass('hidden');
               // $("#list-wrapper div.next_item").remove();
            }

        });
        $('#br-show').hide();
        $('#help-master .body-master').removeClass('open');
        if($(window).width() <= 991) {
            widthSize.hide();
            $('div.goods .js-shadow').removeClass('br-shadow-goods');
            $("#help-master div.help").show();
        }else {
            $("#menu-top,.category___sidebar.category-list").fadeIn();
            widthSize.animate({left:'0',marginLeft: -750}, 300, function () {
                $("#help-master div.help").fadeIn();
                $('#help-master').css('right','auto');
                widthSize.hide();
            });
            if(!$('div.goods.goods-new').length) $('div.goods').css('width','75%').add('#list-wrapper').removeClass('goods-top').addClass('goods-list');
            if($('#center').has('.goods').length) $('div.sidebar').css('width','25%');
        }

        if (typeof loadGoodsCategory == 'function') {
            delete loadGoodsCategory(false);
        }

    });

});