var controlOrders = {};

controlOrders.page                             = {};

controlOrders.ordersGroups = {};
controlOrders.ordersItems = {};
controlOrders.currentOrderItem = {};

controlOrders.pageIndex                        = '.control-order-update';

controlOrders.bonusAll                         = '.order-success';
controlOrders.moneyAll                         = '.basket-general-form';
controlOrders.bonusFree                        = '.order-success';
controlOrders.moneyFree                        = '.basket-general-form';

controlOrders.noDisplayClass                   = '.not-display';
controlOrders.orderItemBlockClass              = '.order-item-element';
controlOrders.checkCountBlockClass             = '.js-control-buttons-for-variant';

//---------------------

controlOrders.setStartParams = function(){
    controlOrders.bonusAll = $('#order-bonus-all').val();
    controlOrders.moneyAll = $('#order-money-all').val();
    controlOrders.bonusFree = $('#order-bonus-free').val();
    controlOrders.moneyFree = $('#order-money-free').val();

    $.each(controlOrders.orderItemBlockClass,function(element){
        controlOrders.ordersItems.push(element);
    });
}

controlOrders.setCurrentOrderItem = function(element){
    controlOrders.currentOrderItem = element.data();
}

controlOrders.changeCountOrderItem = function(element){

}

$(document).ready(function(){
    controlOrders.page = $(controlOrders.pageIndex);
    controlOrders.setStartParams();
    console.log('setStartParams = ');
    console.log(controlOrders.ordersItems);

    controlOrders.page.on('click',controlOrders.checkCountBlockClass + ' .product-list-plus-minus',function(){
        if($(this).hasClass('plus') || $(this).hasClass('minus')){
            controlOrders.setCurrentOrderItem($(this).parents(controlOrders.orderItemBlockClass));
            console.log('controlOrders.currentOrderItem = ');
            console.log(controlOrders.currentOrderItem);

            if($(this).hasClass('plus')){
                controlOrders.currentOrderItem.orderItemCount += controlOrders.currentOrderItem.orderItemProductCountPack;
            }else{
                if(controlOrders.currentOrderItem.orderItemCount > 0){
                    controlOrders.currentOrderItem.orderItemCount -= controlOrders.currentOrderItem.orderItemProductCountPack;
                }
            }
            console.log('controlOrders.currentOrderItem new = ');
            console.log(controlOrders.currentOrderItem);
        }
    })
});