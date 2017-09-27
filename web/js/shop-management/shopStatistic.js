var variant = 'Price';
    period = 'year';

function printGraph(){
    google.charts.load('current', {'packages':['line']});
    google.charts.setOnLoadCallback(drawChart);

}
    function drawChart() {
        var dataNoSort = $('span#shopStatisticData').data('value');
        var bigTitle = dataNoSort.title;
            smallTitle = dataNoSort.subTitle;
            
        if(variant == 'Price'){
            smallTitle = 'Доход / руб.';
        }else{
            smallTitle = 'Количество / шт.';
        }
            
        if(period == 'year'){
            finalList = dataNoSort.valueList[period+variant];
            bigTitle = ' за год';
        }else{
            finalList = dataNoSort.valueList['dey'+variant][period];
            bigTitle = ' за месяц';
        }
        
        var data = google.visualization.arrayToDataTable(finalList);
        var options = {
            chart: {
                title: 'Статистика '+bigTitle,
                subtitle: smallTitle
            },
            curveType: 'function',
            width: $('div#shop-statistics').width()-100,
            height: 360,
            legend: { position: 'right' }
        };

        var chart = new google.charts.Line(document.getElementById('shopStatisticCanvas'));
        chart.draw(data, options);
    }

$(document).ready(function(){
    if($('span#shopStatisticData').length > 0){
        printGraph('yearPrice');
        
        $('.monthTitle').click(function(){
            $('.monthTitle .monthVariantSimbil').remove();
            if($(this).siblings('.monthStatisticValue').find('.active').length > 0){
                if($(this).siblings('.monthStatisticValue').find('.active').data('variant') == 'Price'){
                    $(this).parent().find('.getNewValue[data-variant=Count]').click();
                    $(this).append('<span class="monthVariantSimbil">C</span>');
                }else{
                    $(this).parent().find('.getNewValue[data-variant=Price]').click();
                    $(this).append('<span class="monthVariantSimbil">$</span>');
                }
            }else{
                $(this).append('<span class="monthVariantSimbil">$</span>');
                $(this).parent().find('.getNewValue[data-variant=Price]').click();
            }
        });
        
        $('.getNewValue').click(function(){
            if($(this).data('period') == 'year'){
                $('.monthTitle .monthVariantSimbil').remove();
            }
            if($(this).hasClass('active')){
            
            }else{
                $('.getNewValue.active').removeClass('active');
                $('.monthVariant').removeClass('active');
                $(this).addClass('active');
                $(this).parent().parent().addClass('active');
                
                $('#shopStatisticCanvas').html('');
                variant = $(this).data('variant');
                period = $(this).data('period');
                
                drawChart();
            }
        });
        $('.arrowMonthLeft').click(function(){
            if($('.monthVariant.active').length > 0 && $('.monthVariant.active').prev().find('.monthTitle').length > 0){
                $('.monthVariant.active').prev().find('.monthTitle').click();
            }else{
                $('.monthVariant .monthTitle:last').click();
            }
        });
        $('.arrowMonthRight').click(function(){
            if($('.monthVariant.active').length > 0 && $('.monthVariant.active').next().find('.monthTitle').length > 0){
                $('.monthVariant.active').next().find('.monthTitle').click();
            }else{
                $('.monthVariant .monthTitle:first').click();
            }
        });
    }
});
