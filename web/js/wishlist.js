$(document).ready(function () {
    $('.wishlist_btn').click(function(){
        var good_id =$(this).data('product');

        $(this).removeClass('glyphicon-plus-sign').removeClass('oran');
        $(this).addClass("glyphicon-ok-sign").addClass('success');

        $.ajax({
            url: "/ajax-wish-list/add-to-wish-list?good_id="+good_id,
            success: function(data) {
                console.log('data');

            }
        });
    });

    $('.remFromWishlist_btn').click(function(){
        var good_id =$(this).data('product');
        $.ajax({
            url: "/ajax-wish-list/remove-from-wish-list?good_id="+good_id,
            success: function(data) {
                $("div#"+good_id).parent().remove();
                console.log(data);
            }
        });
    });

    console.log('wishlist.js OK');
});