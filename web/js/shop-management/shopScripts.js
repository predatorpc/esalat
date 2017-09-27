var baseAjaxPath = '';

var timerSlider = 0;
var currentProvider = function(){
    if($('.visibleShopParams[data-param=all]').length > 0){
        this.params = [];

        this.all = $('.visibleShopParams[data-param=all]').html()*1;
        this.accessCount = $('.visibleShopParams[data-param=accessCount]').html()*1;
        this.showcase = $('.visibleShopParams[data-param=showcase]').html()*1;
        this.confirm = $('.visibleShopParams[data-param=confirm]').html()*1;
        this.notConfirm = $('.visibleShopParams[data-param=notConfirm]').html()*1;
        this.blocked = $('.visibleShopParams[data-param=blocked]').html()*1;

        this.setParams = function(){

        }
    }
}

$(document).ready(function() {
    var provider = new currentProvider();

    if ($('#personalShopDateStart').length > 0) {
        $('#personalShopDateStart').datepicker({
            'dateFormat': 'yy-mm-dd',
        });
        $('#personalShopDateStop').datepicker({
            'dateFormat': 'yy-mm-dd',
        });
    }

    $('.addVariantFormForProvider').click(function () {
        console.log('value = ' + $(this).siblings('.value_input').val() + ' - ' + $(this).parents('.visibleShopParams').data('param'));

        var key = 0;
        if ($('.variants[data-key]').length > 0) {
            key = $('.variants[data-key]').last().data('key') * 1 + 1;
        }

        //console.log('key = '+key);

        $.ajax({
            method: 'POST',
            data: {'key': key},
            url: baseAjaxPath + '/ajax/get-variant-form-for-provider',
            dataType: 'json',
            success: function (responce) {
                if (responce.status == 'OK') {
                    $('.blockForNewVariantsForms').append(responce.value);
                }
            }
        });
        return false;
        //var key = 0;
        //if($('.variants[data-key]').length > 0){
        //    key = $('.variants[data-key]').last().data('key');
        //}
        //
        ////console.log('key = '+key);
        //
        //$.ajax({
        //    method      : 'POST',
        //    data        : {'key':key},
        //    url         : baseAjaxPath+'/ajax/get-variant-form-for-provider',
        //    //dataType    : 'json',
        //    success     : function(responce){
        //        if(responce.status == 'OK'){
        //            $('.blockForNewVariantsForms').append(responce.value);
        //        }
        //    }
        //});
        //return false;




    });
    // Скрываем столбец;
    if($(window).width() <= 767) {
        // Скрываем столбец класс, array,col - 1,2,3;
        var listCol = [1, 4, 5, 6, 7, 9];
        table_col_hide('mobile_ad', listCol);
    }
    // Обертка лейбел для таблиц;
    $("table.mobile_ren tr[data-key]").each(function(index,items) {
        $("table.mobile_ren th").each(function (i, item) {
            console.log(item);
            var str = $(item).text();
            $(items).children('td').eq(i).attr('data-label', str);
        });
    });




    // Изменение активности товара в списке товаров
    $('.swithActiveElement').click(function () {
        var switchElement = $(this);

        if (provider.showcase >= provider.accessCount && switchElement.hasClass('switch-danger')) {
            return false;
        } else {
            $.ajax({
                method: 'POST',
                data: {'id': $(this).parents('tr').data('key')},
                url: baseAjaxPath + '/ajax/swith-activ-product',
                success: function (responce) {
                    if (responce == 'OK') {
                        if (switchElement.hasClass('switch-danger')) {
                            switchElement.removeClass('switch-danger').addClass('switch-success');
                            provider.showcase++;
                            $('.visibleShopParams[data-param=showcase]').html(provider.showcase);
                        } else {
                            switchElement.removeClass('switch-success').addClass('switch-danger');
                            provider.showcase--;
                            $('.visibleShopParams[data-param=showcase]').html(provider.showcase);
                        }
                    }
                }
            });
        }
    });


    $('.visibleShopParams .changeFieldUpdate').click(function () {
        $(this).siblings('.value_span').hide();
        $(this).siblings('.field').show();
    });

    $('.visibleShopParams .field .set_ok').click(function () {
        //console.log('value = '+$(this).siblings('.value_input').val()+' - '+$(this).parents('.visibleShopParams').data('param'));

        var containerField = $(this).parents('.visibleShopParams'),
            newValue = $(this).siblings('.value_input').val(),
            updateParam = $(containerField).data('param');

        $.ajax({
            method: 'POST',
            data: {'updateParam': updateParam, 'newValue': newValue},
            url: baseAjaxPath + '/ajax/update-shop-param',
            dataType: 'json',
            success: function (responce) {
                if (responce.status == 'OK') {
                    $(containerField).find('.field').hide();
                    $(containerField).find('.value_span').html(responce.value).show();
                }
            }
        });
    });

    $(document).on('click','.delete-image-block-new',function () {
        element = $(this);

        $.ajax({
            method: 'POST',
            data: element.data(),
            url: baseAjaxPath + '/ajax/delete-image-now',
            success: function (responce) {
                element.parents('div.item-image').remove();
            }
        });
    });


    $(document).on('click', '.variants .variant-title', function () {
        if ($(this).siblings('.variant-body').hasClass('close')) {
            $('.variant-body').addClass('close');
            $(this).siblings('.variant-body').removeClass('close');
        } else {
            $(this).siblings('.variant-body').addClass('close');
        }
    });

    if ($('#dropzone').length > 0) {
    }


    // Открывает галерею для привязки картинок к варианту товара
    $('.shop-form').on('click', '.addVariantImage', function (event) {
        console.log('click');
        event.preventDefault();
        var clickedbtn = $(this);
        var modalContainer = $('#select-image-modal');

        $.ajax({
            method: 'POST',
            data: {'variant': $(this).data('variant')},
            dataType: 'json',
            url: baseAjaxPath + '/ajax/get-gallery-shop',
            success: function (responce) {
                var htmlModalContent = '';
                htmlModalContent += '<div class="dropzone" id="dropzone">Перетащите файлы сюда</div>';

                if (responce.gallery) {
                    htmlModalContent += '<div class="row">';
                    if (responce.gallery.length > 0) {
                        $(responce.gallery).each(function (i, element) {
                            var check = '';
                            var delLink = '<span class="delete">Удалить</span>';
                            var button = '<span class="add">Добавить</span>';
                            if (element.check == 1) {
                                check = '<span class="modal-gallery-check-image">V</span>';
                                button = '<span class="remove">Убрать</span>';
                            }
                            htmlModalContent += '\
                            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2 gallery-image-container">\
                                <img src="' + element.url + '" />\
                                <div class="modal-gallery-buttons" data-product="' + clickedbtn.data('product') + '" data-variant="' + clickedbtn.data('variant') + '" data-image="' + element.id + '">\
                                    ' + button + '\
                                    ' + delLink + '\
                                </div>\
                                ' + check + '\
                            </div>\
                        ';
                        });
                    }

                    htmlModalContent += '</div>';
                }
                modalContainer.find('.modal-body').html(htmlModalContent);

                var dropzone = document.getElementById("dropzone");

                dropzone.ondrop = function (e) {
                    this.className = 'dropzone';
                    this.innerHTML = 'Перетащите файлы сюда';
                    e.preventDefault();
                    uploadGalleryFiles(e.dataTransfer.files);
                };

                var displayUploads = function (data) {
                    var uploads = document.getElementById("uploads"),
                        anchor,
                        x;

                    for (x = 0; x < data.length; x++) {
                        anchor = document.createElement('li');
                        anchor.innerHTML = data[x].name;
                        uploads.appendChild(anchor);
                    }
                };

                var uploadGalleryFiles = function (files) {
                    var formData = new FormData(),
                        xhr = new XMLHttpRequest(),
                        x;

                    for (x = 0; x < files.length; x++) {
                        formData.append('file[]', files[x]);
                    }

                    $.ajax({
                        method: 'POST',
                        data: formData,
                        dataType: 'json',
                        cache: false,
                        processData: false,
                        contentType: false,
                        url: baseAjaxPath + '/ajax/save-files-gallery',
                        success: function (responce) {
                            if (responce.status == 'ok' || responce.status == 'OK') {
                                if (responce.filesPath) {
                                    htmlModalContent = '';
                                    $(responce.filesPath).each(function (i, element) {
                                        var check = '';
                                        var delLink = '<span class="delete">Удалить</span>';
                                        var button = '<span class="add">Добавить</span>';

                                        htmlModalContent += '\
                                            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-2 gallery-image-container">\
                                                <img src="' + element.link + '" />\
                                                <div class="modal-gallery-buttons" data-product="' + clickedbtn.data('product') + '" data-variant="' + clickedbtn.data('variant') + '" data-image="' + element.id + '">\
                                                    ' + button + '\
                                                    ' + delLink + '\
                                                </div>\
                                                ' + check + '\
                                            </div>\
                                        ';
                                    });
                                }
                                modalContainer.find('.modal-body .row').append(htmlModalContent);

                            }
                        }
                    })

                };

                dropzone.ondragover = function () {
                    this.className = 'dropzone dragover';
                    this.innerHTML = 'Отпустите мышку';
                    return false;
                };

                dropzone.ondragleave = function () {
                    this.className = 'dropzone';
                    this.innerHTML = 'Перетащите файлы сюда';
                    return false;
                };

                modalContainer.modal({show: true});
                //modalContainer.css({'display':'block'});
            }
        });
    });

    // Привязывает / отвязывает картинку к варианту товара
    $(document).on('click', '.modal-gallery-buttons span', function () {
        var actionElement = $(this);
        var parentElement = $(this).parent();

        $.ajax({
            method: 'POST',
            data: {
                'action': actionElement.attr('class'),
                'image': parentElement.data('image'),
                'variant': parentElement.data('variant'),
                'product': parentElement.data('product')
            },
            url: baseAjaxPath + '/ajax/add-remove-variant-image',
            success: function (responce) {
                if (responce == 'OK') {
                    if (actionElement.hasClass('add')) {
                        parentElement.html('<span class="remove">Убрать</span>');
                        parentElement.parent().append('<span class="modal-gallery-check-image">V</span>');

                        $('.general-image-variant-block[data-variant=' + parentElement.data('variant') + ']')
                            .append('\
                                <div data-image="' + parentElement.data('image') + '" class="col-xs-4 col-sm-3 col-md-3 col-lg-2 gallery-image-container">\
                                    <img src="' + parentElement.siblings('img').attr('src') + '" />\
                                    <div class="modal-gallery-buttons" data-product="' + parentElement.data('product') + '" data-variant="' + parentElement.data('variant') + '" data-image="' + parentElement.data('image') + '">\
                                        <span class="remove">Убрать</span>\
                                    </div>\
                                    <span class="modal-gallery-check-image">V</span>\
                                </div>\
                            ');
                    } else if (actionElement.hasClass('remove')) {
                        parentElement.html('<span class="add">Добавить</span>');
                        parentElement.parent().find('.modal-gallery-check-image').remove();

                        $('.general-image-variant-block[data-variant=' + parentElement.data('variant') + ']').children('div[data-image=' + parentElement.data('image') + ']').remove();
                    } else {
                        parentElement.parent().remove();
                    }
                }
            }
        });
    });

    $('.button-save-status-order').click(function(){
        var status  = $(this).parent('div').siblings('.seller_status').val();
        if(status != 'empty'){
            var order_item_id = $(this).attr('data-order-item');
            var button  = $(this);

            $.post(baseAjaxPath+'/ajax/sellers', {
                'order_item_id':order_item_id,
                'status': status
            }, function(response) {
                if (response.status == 'OK') {
                    button.parents('td').html(response.statusHtml);
                }
            },'JSON');
        }
    });

    $('.button-save-comment-order').click(function(){
        var order_item_id = $(this).attr('data-order-item');
        var comment = $(this).siblings('textarea').val();
        var button  = $(this);

        $.post(baseAjaxPath+'/ajax/sellers-comment', {
            'order_item_id':order_item_id,
            'comment': comment
        }, function(response) {
            if (response.status == 'OK') {

            }else{
                console.log('error');
            }
        },'JSON');
        $('.order-report-comment-popup').addClass('not-visible');
        $('.order-report-comment-text').html(comment);
    });

    $('.order-report-comment-control').click(function(){
        var comment = $(this).siblings('.order-report-comment-popup');
        if(comment.hasClass('not-visible')){
            comment.removeClass('not-visible');
        }
        return false;
    });

});
// Для PJPAX;
$(document).on('ready pjax:success', function(data, status, xhr, options) {
    // Скрываем столбец;
    if($(window).width() <= 767) {
        // Скрываем столбец класс, array,col - 1,2,3;
        var listCol = [1, 4, 5, 6, 7, 9];
        table_col_hide('mobile_ad', listCol);
    }
});

// Обложка привью для карточкки;
function cover_images(good_id,variant_id,image_id) {
    loading('show');
    $.post('/product/update?id='+good_id,{'coverImages':true,'variant_id':variant_id,'image_id':image_id},function(response){
        if(response) {
            $.post('/product/update?id=' + good_id, {
                'imagesAjaxUpdate': true,
                'variant_id': variant_id
            }, function (html) {
                loading('hide');
                $("div.images_variation-" + variant_id).html(html);
            });
        }
        //$("div.images_variation-"+variant_id).html(response);
    });
    return false;
}


