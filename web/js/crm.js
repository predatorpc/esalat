/**
 * Created by Никита on 17.02.2017.
 */
var order,order_id, order_group_id, order_item_id, order_item_status, newOrderItem, count_new_variation;

$(document).ready( function() {

    $(".show-filter").on('click',function(){
        $(".content-f").toggle();
    });



    $('.form_user').click(function(){
        var id = $(this).attr('id');
        var name = $(this).text();
        $('#search_user').modal('hide');
        $('#user_name').append($('<option selected></option>').val(id).html(name));
        $('#id_changed').val(1);

    });

    $('.readComment').click(function(){
        var id = $(this).attr('id');
        var check = 0;
        if ( this.checked ) {
            check =1;
        }
        $.ajax({
            url: "/crm/read?id="+id+"&check="+check,
            success: function() {
                console.log('done');
            }
        });
    });

    $('a.goodsMetro').click(function(){
        var order_id = $(this).attr('id');
        // $.ajax({
        //     url: '/ajax-reports/metro?order_id='+order_id,
        //     success: function() {
        //         location.reload()
        //     }
        // });
        $.getJSON('/ajax-reports/metro?order_id='+order_id, function(data) {
            $.each(data, function(key, val) {
                $('span#'+order_id+"_"+val).text('Принят поставщиком').addClass('btn-info').removeClass('btn-danger');
            });
        });
        $(this).hide();

    });
    // Заменит товар;
    $('.js-change-good').click(function(){
            var btn = $(this);
            btn.hide();
            var order = $(this).parents('.content-goods').find('input[name="order-data"]');
            var order_id = order.data('order-id');
            var order_group_id = order.data('order-group-id');
            var order_item_id = order.data('order-item-id');
            var order_item_status = order.data('order-item-status');

            var newOrderItem = order.data('new-variation-id');
            var count_new_variation= $(this).parents('.content-goods').find('input[name="number"]').val();
           console.log(' order_id=' + order_id + ' order_group_id=' + order_group_id + ' order_item_id='+ order_item_id + ' order_item_status=' + order_item_status + '--newOrderItem=' + newOrderItem + ' count_new_variation='+count_new_variation);
            $.ajax({
                type: "POST",
                url: "/ajax-management/order-item-check-avalible",
                data: 'order=' + order_id + '&newOrderItem=' + newOrderItem + '&newOrderItemCount=' +count_new_variation+'&orderItem='+order_item_id,
                success: function(data) {
                    console.log(data);
                    if(data == 'not enough'){
                        alert('Нет требуемого количества на складе');
                        btn.show();
                        return false;
                    }else if(data == 'expensive'){
                        alert('У пользователя не хватит средств');
                        btn.show();
                        return false;
                    }else if(data == 'more than was'){
                        alert('Сумма новой позиции, превышает стоймость заменяемой');
                        btn.show();
                        return false;
                    }else{
                        $.ajax({
                            type: "POST",
                            url: "/ajax-management/orders-item-add",
                            data: 'order=' + order_id + '&newOrderItem=' + newOrderItem + '&orderGroup='  +order_group_id + '&newOrderItemCount=' + count_new_variation+'&orderItemCur='+order_item_id,
                            success: function(data) {
                                if(data == 'success'){
                                    alert('Товар добавлен');
                                    var modal_id = $('.fade.modal.in').attr('id');
                                    $('#'+modal_id).modal('hide');
                                    $('#btn-change_'+order_item_id).hide();
                                    $('#'+order_id+'_'+order_item_id).text('Товар заменен');
                                    return false;
                                }
                            }
                        });
                    }
                }
            });
    });

    // Выбор Количество товара;
    $('div.goods-list-modal input.js-count-num').change(function(){
        var id = $(this).attr('id');
        var max = $(this).attr('max');
        var count = $(this).val();
        if(count>max){
            count = max;
            $(this).val(max);
        }
        var price = $(this).parents('.content-goods').find('input[name="price"]').val();

        $('#'+id+' div.sum span').text(price * count);
    });

    $('.btn.changeOrderTime').click(function(){
        var orderId = $(this).data('order-id');
        var orderGroupId = $(this).data('order-group-id');
        var orderTime = $('input[name="order_date_'+orderGroupId+'"]').val();
        console.log(orderTime);
        $.ajax({
            url: "/reports/change-order-time?orderId="+orderId+"&newTime="+orderTime+"&orderGroupId="+orderGroupId,
            success: function(data) {
                alert(data);
            }
        });
    });

    $('.data-slice').click(function(){
        var filter   = $('.shops-search #w1').serialize();
        $.ajax({
            type: 'POST',
            url: '/reports/order-new-data-slice',
            data: filter,
            success: function(data) {
                $("#windows .modal-title").text('Срез');
                $('#windows .modal-body').html(data);
                $('#windows').modal({show:true});
            }
        });
    });
});