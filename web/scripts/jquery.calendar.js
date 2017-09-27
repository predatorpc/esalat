(function($) {
    $.fn.calendar = function(o) {
        return this.each(function() {
            new $.calendar($("div.table", this), o);
        });
    };
    // Настройка по умолчанию;
    var defaults = {};
    // Создание таблицы календаря;
    $.calendar = function(e, o) {
        // Настройки;
        this.options = $.extend(defaults, o);
        // Справочник;
        var months = new Array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентрябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
        var week = new Array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
        var days = new Array('31', '28', '31', '30', '31', '30', '31', '31', '30', '31', '30', '31');
        // Обработка даты;
        var date = new Date($("input.value", $(e).parents("div.calendar")).val() * 1000);
        var day = date.getDate();
        var month = date.getMonth() + 1;
        var year = date.getFullYear();
        // Установка даты;
        days_show(year, month, day);
        // Формирование календаря;
        function days_show(year, month, day) {
            // Обработка данных;
            var date = new Date(year, month - 1, day);
            // Обработка весокосного года;
            if (((year % 4 == 0) && (year % 100 != 0)) || (year % 400 == 0)) days[1] = 29; else days[1] = 28;
            // Обработка границ месяца;
            var date = new Date(year, month - 1, 1);
            var day_first = date.getDay();
            var day_last = days[month - 1];
            // Обработка воскресенья;
            if (day_first == 0) day_first = 7;
            // Формирование меню;
            var calendar_months = '';
            calendar_months += '<div class="month">' + months[month - 1] + '</div>';
            calendar_months += '<div class="month-left">«</div>';
            calendar_months += '<div class="month-right">»</div>';
            calendar_months += '<div class="year">' + year + '</div>';
            calendar_months += '<div class="year-left">«</div>';
            calendar_months += '<div class="year-right">»</div>';
            // Формирование дней недели;
            var calendar_week = '';
            calendar_week += '<tr>';
            for (i = 0; i <= 6; i++) {
                calendar_week += '<th><div>' + week[i] + '</div></th>';
            }
            calendar_week += '</tr>';
            // Формирование дней;
            var calendar_days = '';
            for (i = 1; i <= 6; i++) {
                calendar_days += '<tr>';
                for (j = 1; j <= 7; j++) {
                    var n = 7 * (i - 1) + j;
                    if (n < day_first || (n - day_first + 1) > day_last) {
                        calendar_days += '<td><div>&nbsp;</div></td>';
                    } else {
                        calendar_days += '<td class="day' + (day == (n - day_first + 1) ? ' open' : '') + '"><div>' + (n - day_first + 1) + '</div></td>';
                    }
                }
                calendar_days += '</tr>';
            }
            // Вывод календаря;
            $(e).html('<div class="months">' + calendar_months + '</div><div class="days"><table cellpadding="0" cellspacing="0" border="0">' + calendar_week + calendar_days + '</table></div>');
            // Переключение месяца;
            $("div.month-left", $(e)).off().on("click", function() {
                days_show(((month > 1) ? year : year - 1), ((month > 1) ? (month - 1) : 12), 1);
            });
            $("div.month-right", $(e)).off().on("click", function() {
                days_show(((month < 12) ? year : year + 1), ((month < 12) ? (month + 1) : 1), 1);
            });
            // Переключение года;
            $("div.year-left", $(e)).off().on("click", function() {
                days_show(year - 1, month, 1);
            });
            $("div.year-right", $(e)).off().on("click", function() {
                days_show(year + 1, month, 1);
            });
            // Выбор даты;
            $("td.day", $(e)).off().on("click", function() {
                var day = $(this).text();
                // Переключение даты;
                $("div.value", $(e).parents("div.calendar")).html(str_pad(day, 2) + '.' + str_pad(month, 2) + '.' + year);
                var datetime = parseInt(new Date(year, month - 1, day).getTime() / 1000);
                $("input.value", $(e).parents("div.calendar")).val(datetime);
                $("td.day", $(e)).removeClass("open");
                $(this).addClass("open");
                $(e).hide();
            });
            // Переключение календаря;
            $("div.date-button", $(e).parent("div")).off().on("click", function() {
                $(e).toggle();
            });
        }
    }
})(jQuery);

$(document).ready(function() {
    // Вывод календаря;
    $("div.calendar").calendar();
});