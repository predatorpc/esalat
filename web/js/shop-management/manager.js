/*
$(document).ready(function() {
    // Смена позиция товара;
    $( "#sort" ).sortable({
        // Область перемещения;
        handle: "div.manager div.position",
        items: ".item",
        // Прозрачность элементов при перетаскивании;
        opacity: 0.5,
        // При перемещения удаляем div.item-space и добавляем оступ для div.item;
        activate : function() {
            $("div.goods div.items div.item").css("margin", "0px 5px 0px 0px");
            $("div.goods  div.items div.item-space").remove();
        },
        stop: function(){
            // Вовремя обработке откл плагин;
            $( "#sort" ).sortable('disable');
            // Получаем массив  id - товаров;
            var id = $('#sort').sortable("toArray");
            loading('show');
            $.ajax({
                url: window.location.pathname,
                type: 'POST',
                data: {sortable : true, position : id},
                success: function(data) {
                    loading('hide');
                    // После завершения включаем функц-ть сортировка;
                    $( "#sort" ).sortable('enable');
                },
                error: function(){
                    alert('Ошибка сервера');
                }
            });
        }
    });
    // Запрет выделения;
    $("#sort").disableSelection();
});


// Редактирование наименования товара;
function good_edit(good_id) {
    window.open('/cms/goods/' + good_id);
    return false;
}

// Подключение акции для товара;
function good_discount(good_id) {
    $("div.goods div.item-" + good_id + " div.manager div.discount").toggleClass("disabled");
    $("div.goods div.item-" + good_id + " div.stickers div.discount").toggle();
    $.post('/catalog/', {
        'good_discount': true,
        'good_id': good_id
    });
    return false;
}

// Скрытие товара;
function good_delete(good_id) {
    if (confirm('Скрыть товар?')) {
        $("div.goods div.item-" + good_id).hide();
        $.post('/catalog/', {
            'good_delete': true,
            'good_id': good_id
        });
    }
    return false;
}
*/