
var  enjoyhint = new EnjoyHint({

    onStart:function() {


    },
    onEnd:function() {
        //
        $.post('/ajax/master-help-ajax',{'master_help':true},function(response){

        });
        console.info('master_help END');
    },
    onScipt:function() {
        ////
        console.info('master_help STATUS');
    }
});
// Для декстоп;
if($(window).width() >= 1200) {
// Пошаговая подсказка;
    var array_enjoy = [

        {
            selector: '._master_user',
            description: 'Тут ЛК',
            showNext: true,
            showSkip: false,
            // timeout:2000,
            nextButton: {
                className: 'button_master',
                text: 'Далее'
            },
        },
        {
            selector: '._master_h_stock',
            description: 'Ну типа тут Акция у нас! Все давай дальше',
            showNext: true,
            showSkip: false,
            nextButton: {
                className: 'button_master',
                text: 'Далее'
            },

        },
        {
            selector: '#basketDesktop',
            description: 'Тут ваша корзина Понял да? идем дальше',
            showNext: true,
            showSkip: false,
            nextButton: {
                className: 'button_master',
                text: 'Далее'
            },
            shape: 'circle',
        },
        {
            'click ._master_h_m_1': 'Нажми на мастер покупку',
            showSkip: false,
            showNext: false
        },
        {
            selector: '#help-master',
            description: 'Тут выбираем категории и переходим',
            event_selector: 'js_master_close',
            showNext: true,
            showSkip: false,
            nextButton: {
                className: 'button_master js_master_close',
                text: 'Далее',
            },
        },
        {
            selector: '._master_feed',
            description: 'Оставляй отзывы Все спасибо за внимание пока :)',
            showNext: true,
            showSkip: false,
            nextButton: {
                className: 'button_master',
                text: 'Закрой!'
            },

        },

    ];

    $(document).on('click', '.js_master_close', function () {
        $("#help-master button.close,#br-show").click();
    });
    //  Пока страница
    window.onload = function () {
        enjoyhint.set(array_enjoy);
        enjoyhint.run();
    }
}