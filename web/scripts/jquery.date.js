/*
 * Дата 1.1.0 (2013.10.04);
 * Copyright (c) Nullweb (http://www.nullweb.ru/projects/date_input/);
 */
(function($) {
    $.fn.date_input = function(o) {
        return this.each(function() {
            new $.date_input(this, o);
        });
    };
    // Настройка по умолчанию;
    var defaults = {
        time: false
    };
    var months = new Array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь');
    var week = new Array('Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
    var days1 = new Array('31', '28', '31', '30', '31', '30', '31', '31', '30', '31', '30', '31');
    var days2 = new Array('31', '29', '31', '30', '31', '30', '31', '31', '30', '31', '30', '31');
    $.date_input = function(e, o) {
        // Настройки;
        this.options = $.extend(defaults, o);
        var input = $(e);
        // Переменные;
        var options_time = this.options.time;
        var get_date = $(input).val().split(/[- :]/);
        var date = get_date ? new Date(get_date[0], get_date[1] - 1, get_date[2], (get_date[3] ? get_date[3] : 0), (get_date[4] ? get_date[4] : 0), (get_date[5] ? get_date[5] : 0)) : new Date();
        var year = date.getFullYear();
        var month = date.getMonth();
        var day = date.getDate();
        var hour = date.getHours();
        var minute = date.getMinutes();
        var second = date.getSeconds();
        // Сетка календаря;
        var table_week = '';
        table_week += '<tr>';
        for (i=0; i<=6; i++) {
            table_week += '<td>' + week[i] + '</td>';
        }
        table_week += '</tr>';
        var n = 0;
        var table_calendar = '';
        for (i=1; i<=6; i++) {
            table_calendar += '<tr>';
            for (j=1; j<=7; j++) {
                n = 7 * (i-1) - (-j);
                table_calendar += '<td style="color:#ffffff;" id="' + $(input).attr('name').replace(/[\[\]]/g,"") + '_day_' + n + '"></td>';
            }
            table_calendar += '</tr>';
        }
        // Описание блоков;
        var container = $('<div class="date-input"></div>');
        var value = $('<span><a href="/" class="dotted" onclick="return false;">' + ((day < 10) ? '0' : '') + day + '.' + ((month  < 9) ? '0' : '') + (month + 1) + '.' + year + (options_time ? (' ' + ((hour  < 9) ? '0' : '') + hour + ':' + ((minute  < 9) ? '0' : '') + minute) : '') + '</a></span>');
        var container_years = $('<div><span class="arrow-left">&laquo;</span><span class="value">' + year + '</span><span class="arrow-right">&raquo;</span></div>');
        var container_months = $('<div><span class="arrow-left">&laquo;</span><span class="value fix_w">' + months[month] + '</span><span class="arrow-right">&raquo;</span></div>');
        var container_week = $('<table cellpadding="0" cellspacing="2" border="0">' + table_week + '</table>');
        var container_days = $('<table cellpadding="0" cellspacing="2" border="0">' + table_calendar + '</table>');
        var container_time = $('<div><input type="text" value="' + hour + '" maxlength="2" class="hour" /> : <input type="text" value="' + minute + '" maxlength="2" class="minute" /> : <input type="text" value="' + second + '" maxlength="2" class="second" /></div>');
        var details = $('<div></div>');
        // Формирование оболочки;
        details.append(container_years);
        details.append(container_months);
        details.append(container_week);
        details.append(container_days);
        details.append(container_time);
        container.append(value);
        container.append(details);
        input.after(container).detach().prependTo(container).hide();
        // Оформление блоков;
        container.css({
            'padding': '0px 6px 0px 6px',
            'position': 'relative',
            'display': 'inline'
        });
        value.css({
            'cursor': 'pointer'
        });
        details.css({
            'width': '218px',
            'height': '235px',
            'background':'rgb(141, 106, 136)',
            'background':'rgba(141, 106, 136, 0.8)',
            'position': 'absolute',
            'border-radius': '3px',
            'top': '16px',
            'left': '-2px',
            'z-index': '99',
            'display': 'none'
        });
        container_years.css({
            'position': 'absolute',
            'top': '8px',
            'right': '4px'
        });
        container_months.css({
            'position': 'absolute',
            'top': '8px',
            'left': '4px'
        });
        $('.fix_w').css('width','100px');
        $("span.arrow-left, span.arrow-right", container).css({
            'font-weight': 'bold',
            'color': '#ffffff',
            'padding': '0px 4px 0px 4px',
            'cursor': 'pointer'
        });
        $("span.value", container).css({
            'color': '#ffffff',
            'padding': '0px 4px 0px 4px',
            'position': 'relative',
            'font-weight': 'bold',
            'top': '1px'
        });
        container_week.css({
            'padding': '1px 3px 0px 3px',
            'margin': '30px 0px 0px 0px',
            'position': 'relative',
            'top': '2px'
        });
        $("td", container_week).css({
            'width': '28px',
            'height': '26px',
            'color' : '#d2d2d2',
            'text-align': 'center',
            'vertical-align': 'middle',
            'font-weight': 'bold'
        });
        container_days.css({
            'padding': '0px 3px 3px 3px'
        });
        $("td", container_days).css({
            'width': '28px',
            'height': '26px',
            'text-align': 'center',
            'vertical-align': 'middle',
            'cursor': 'pointer',
            'font-weight': 'bold',
            'color': '#fff !important'
        });
        container_time.css({
            'padding': '2px 0px 0px 0px',
            'text-align': 'center',
            'display': 'none'
        });
        $("input.hour, input.minute, input.second", container_time).css({
            'width': '28px',
            'padding': '2px 0px 2px 0px',
            'text-align': 'center'
        });
        // Открыть календарь;
        value.click(function() {
            details.toggle();
        });
        // Скрыть календарь;
        details.hover(function() {
            // ;
        }, function() {
           $(this).hide();
        });
        $("span.arrow-left", container_months).click(function() {
            if (date.getMonth() == 0) {
                calendar((date.getFullYear() - 1) + '-12-01');
            } else {
                calendar(date.getFullYear() + '-' + ((date.getMonth() < 10) ? '0' : '') + date.getMonth() + '-01');
            }
        });
        $("span.arrow-right", container_months).click(function() {
            if (date.getMonth() == 11) {
                calendar((date.getFullYear() + 1) + '-01-01');
            } else {
                calendar(date.getFullYear() + '-' + (((date.getMonth() + 2) < 10) ? '0' : '') + (date.getMonth() + 2) + '-01');
            }
        });
        $("span.arrow-left", container_years).click(function() {
            calendar((date.getFullYear() - 1) + '-' + (((date.getMonth() + 1) < 10) ? '0' : '') + (date.getMonth() + 1) + '-01');
        });
        $("span.arrow-right", container_years).click(function() {
            calendar((date.getFullYear() + 1) + '-' + (((date.getMonth() + 1) < 10) ? '0' : '') + (date.getMonth() + 1) + '-01');
        });
        // Ввод времени;
        container_time.on("keypress", "input.hour, input.minute, input.second", function(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode != 8 && (charCode < 48 || charCode > 57)) return false;
            return true;
        });
        if (options_time) {
            container_time.show();
            details.height('265px');
        }
        // Формирование таблицы дней;
        function calendar(new_date) {
        	var n_date = new Date(new_date);
            date = n_date;
            n_date.setDate(1);
            var n_year = n_date.getFullYear();
            var n_month = n_date.getMonth();
            var n_day = n_date.getDay();
            if (n_day == 0) n_day = 6; else n_day--;
            $("span.value", container_months).html(months[n_month]);
            $("span.value", container_years).html(n_year);
        	var marr = ((n_year % 4) == 0 || (n_year % 100) != 0 || (n_year % 1000) == 0) ? days2 : days1;
            $("td", container_days).html('').unbind("click");
        	for (var i=1; i<=42; i++) {
        		if ((i >= (n_day -(-1))) && (i <= n_day-(-marr[n_month]))) {
        			$('#' + input.attr('name').replace(/[\[\]]/g,"") + '_day_' + i).html(i - n_day).bind("click", function() {
                        var n_hour = $("input.hour", container_time).val();
                        var n_minute = $("input.minute", container_time).val();
                        var n_second = $("input.second", container_time).val();
                        date = new Date(n_year, n_month, $(this).html(), n_hour, n_minute, n_second, 0);
                        input.val(n_year + '-' + ((n_month < 9) ? '0' : '') + (n_month + 1) + '-' + (($(this).html() < 10) ? '0' : '') + $(this).html() + (options_time ? (' ' + ((n_hour < 9) ? '0' : '') + n_hour + ':' + ((n_minute < 9) ? '0' : '') + n_minute + ':' + ((n_second < 9) ? '0' : '') + n_second) : ''));
                        value.html('<a href="/" class="dashed" onclick="return false;">' + (($(this).html() < 10) ? '0' : '') + $(this).html() + '.' + ((n_month < 9) ? '0' : '') + (n_month + 1) + '.' + n_year + (options_time ? (' ' + ((n_hour < 9) ? '0' : '') + n_hour + ':' + ((n_minute < 9) ? '0' : '') + n_minute) : '') + '</a>');
                        details.hide();
                        $("td", container_days).css({'background':'','color': '#fff'});
                        $(this).css('background', '#785573');
                    });
                    if ((i - n_day) == day && n_month == month && n_year == year) $('#' + input.attr('name').replace(/[\[\]]/g,"") + '_day_' + i).css('background', '#785573');
        		}
        	}
        	/*лауреат номинации "костыль года"*/
            details.toggle();
            details.toggle();
            /**/
        }
        // Вывод таблицы дней;
        calendar(date);
    }
})(jQuery);