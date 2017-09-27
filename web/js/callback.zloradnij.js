/*
 *
 * Мини скрипт для работы с картами и зонами доставки
 *
 * ver. 1.0.0
 *
 */

//var versionCallback = 'Callback version: 1.1.2@31102016';
var versionCallback = 'Callback.js version: 12.6.1@20112016z';

//Город пользователя по умолчанию
var userCity = 'Новосибирск, ';

var storesList;
var clientAddress;

var routerStore = {};
routerStore.store = {};
routerStore.storeResult = {};
routerStore.countShopStores = {};
routerStore.count = 0;
routerStore.status = 0;

routerStore.storeMetro = {};
routerStore.storeResultMetro = {};
routerStore.countMetro = 0;
routerStore.statusMetro = 0;

routerStore.getClientAddress = function(){
    if($('input[name=delivery-address-select]:checked').length > 0){
        return $('input[name=delivery-address-select]:checked').data('address-clear');
    }else{
        return $('.delivery-club-address-list option:selected').data('address-clear');
    }
}

routerStore.checkStores = function(statusAjax){
        console.log(routerStore.status + ' ' + routerStore.statusMetro);
        if (routerStore.status == 1 && routerStore.statusMetro == 1) {
            setTimeout(function () {
                if (statusAjax) {
                    $(document).find('.order-success').parents('.button_success').removeClass('hidden');
                    console.log("END" + statusAjax);
                }else{
                    $(document).find('.order-success').parents('.button_success').removeClass('hidden');
                    console.log("+++END+++");
                }
                console.log("TIME");
            },1000);
            console.log("STATUS" + statusAjax);
            console.warn(versionCallback);
        }
}

routerStore.metroCountDown = function(){
    routerStore.countMetro--;

    if(routerStore.countMetro == 0){
            routerStore.metroEnd();
    }
}

routerStore.countDown = function(){
    routerStore.count--;
    if(routerStore.count == 0){
        routerStore.end();
    }
}

routerStore.getMetroSmall = function(store,length){
    if(routerStore.storeMetro['metro'] == undefined){
        routerStore.storeMetro['metro'] = -1000;
    }

    if(routerStore.storeMetro['metro'] < 0 || routerStore.storeMetro['metro'] > length){
        routerStore.storeMetro['metro'] = length;
        routerStore.storeResultMetro['metro'] = store;
    }
    routerStore.metroCountDown();
}

routerStore.getSmall = function(store,length){
    var curLength = routerStore.store[store.shop_group_id];
    if(curLength == undefined || curLength > length){
        routerStore.store[store.shop_group_id] = length;
        routerStore.storeResult[store.shop_group_id] = store.store_id;
    }
    routerStore.countShopStores[store.shop_group_id]--;

    if(routerStore.countShopStores[store.shop_group_id] == 0){
        routerStore.countDown();
    }
}

routerStore.end = function(){
    if(routerStore.count == 0){
        // console.log(routerStore.storeResult);

        jQuery.each(routerStore.storeResult,function(i,element){
            $('.all-products-input-for-set-store-params').find('input[data-shop-group-id='+i+']').val(element);
        });

        routerStore.store = {};
        routerStore.storeResult = {};
        routerStore.countShopStores = {};
        routerStore.status = 1;
        routerStore.checkStores();
    }
}

routerStore.metroEnd = function(){
    if(routerStore.countMetro == 0){
        // console.log(routerStore.storeResultMetro);

        jQuery.each(routerStore.storeResultMetro,function(i,element){
            $('.all-products-input-for-set-store-params').siblings('input[name=currentClub]').val(metroClubIndex[element]);
        });

        routerStore.storeMetro = {};
        routerStore.storeResultMetro = {};
        routerStore.statusMetro = 1;
        routerStore.checkStores();
    }
}

routerStore.countOfObject = function(obj) {
    var t = typeof(obj);
    var i=0;
    if (t!="object" || obj==null) return 0;
    for (x in obj) i++;
    return i;
}

routerStore.initMapsForStore = function(){
    $(document).on('click','.yMapsActive',function(){
        routerStore.status = 0;
        routerStore.statusMetro = 0;

        var zloradnijMap = new ymaps.Map("map3", {
            center:	[55.03023958662654,83.01634839361031],
            zoom: 10,
            controls: []
        });

        var EastSide = new ymaps.Polygon(
            [[
                [54.92272612228649,83.15972860131298], [54.92842869438542,83.16550358710441], [54.92874320859143,83.17514095387723], [54.922768201768655,83.1854835518387], [54.92581142675517,83.195804692128], [54.9271352346926,83.2044843205002], [54.93951002695212,83.21131322465209], [54.946940786214,83.23462162099153], [54.96797721187215,83.2154465087442], [54.971085663792614,83.20105243406577], [54.9678996710751,83.18647395751758], [54.96146692638948,83.16528014771028], [54.972867282608924,83.15742982483788], [54.97905989988238,83.14131670563805], [54.99163216307711,83.12072886552053], [54.99081090445384,83.098590310452], [55.001058765102805,83.09232755147245], [54.99483374242449,83.06928345225612], [55.010666712930565,83.07389757208158], [55.01258644339305,83.0476126440554], [55.02469135410849,83.03105134449304], [55.03951715849148,83.02637558362778], [55.04103166626843,83.0383509763271], [55.06152827643925,83.03479302606887], [55.06346009333896,83.05055670372555], [55.07182717167059,83.05645270205446], [55.090288738943904,83.07330527275214], [55.10886637115798,83.0546090605424], [55.11208756169171,83.04844412885025], [55.11799278854828,83.05166814408615], [55.12861875804831,83.04270420155845], [55.12212715539989,83.01224771819432], [55.15497236419374,82.99809906564072], [55.15340036483438,83.01201704821898], [55.160969503925365,83.01254544339496], [55.19635673280584,82.997242897344], [55.19991331141338,82.97713383273849], [55.198211707201835,82.93526683507899], [55.185477978156364,82.92531966437429], [55.15287182837848,82.91845780387544], [55.14894781559441,82.8933975401299], [55.15760646320798,82.84842455665779], [55.12614434343379,82.82247556850606], [55.09918404056092,82.80366458761381], [55.08678090701828,82.79927316238567], [55.07030927587265,82.80868235160987], [55.05750150836263,82.82494726707604], [55.04964239816581,82.83949556877253], [55.04507465636953,82.84945729305382], [55.00638186854152,82.93648123034441], [54.99778028992387,82.95002491996065], [54.993322294331335,82.96009246669361], [54.98904427585352,82.9701853696078], [54.989223287632974,82.97354836049477], [54.98742814160646,82.97832755774151], [54.98286880712573,82.97795691367976], [54.97493348897381,82.97635731867148], [54.9641014590613,82.98860378581576], [54.95710381202876,83.00554168613596], [54.95481086303656,83.01707176973645], [54.954553472445625,83.03107655763047], [54.94995192803399,83.04261185460354], [54.94475696481948,83.04762401925237], [54.93910911759268,83.05318515274934], [54.93334666972922,83.05744096466516], [54.92638532320417,83.05852847561634], [54.91576871436623,83.05230575070185], [54.90132857825721,83.04312186703486], [54.890219856999956,83.037664912794], [54.870199077157466,83.02602814898287], [54.85152711139647,83.01442759499349], [54.84230241983729,83.00015287861619], [54.839701788459465,83.01417546734605], [54.84361505554518,83.06350665554794], [54.83769547209067,83.0684848354796], [54.82740223716259,83.08633761868273], [54.82434224752916,83.10213046536242], [54.83768308711311,83.12216120228561], [54.84359028924047,83.12777238354484], [54.857816021939286,83.12402801976006], [54.86824378618211,83.11546104416652], [54.873852617154554,83.11795818075936], [54.88239738450221,83.13817533120913], [54.89201251754416,83.13892836139004], [54.90112261723078,83.13372588172952], [54.91477982991975,83.16156821713251], [54.92272612228649,83.15972860131298]
            ]], { balloonContent: 'Левый Берег'        }, {            fillColor: '#ff6e6e',            fillOpacity: 0.3,            strokeColor: '#0000FF',            strokeOpacity: 0.7,            strokeWidth: 1,            zindex: 100,            borderRadius: 12        });
        var WestSide = new ymaps.Polygon([[
            [54.901477204692966,82.96791065624416], [54.90048796755056,82.92465198925252], [54.91423617542615,82.92310703686042], [54.914631737499796,82.86010731151781], [54.89801476788656,82.84912098339282], [54.913939501301186,82.79830921581498], [54.93265039255091,82.80916679790722], [54.943645450625,82.82517422130802], [54.97066365684584,82.76700530133505], [54.967339906969364,82.74207148633258], [54.965284413649066,82.6902297504928], [54.97702337739524,82.68962893567344], [54.97675185250607,82.6638797291305], [54.9814785905724,82.66143355450853], [54.99938699143821,82.66913685879912], [54.998372417137006,82.68577728352743], [55.007979001464705,82.70619962296675], [55.02096834262705,82.71400753340909], [55.02946711793547,82.73151431164925], [55.05016322176157,82.75173230028993], [55.05400277012372,82.75585364017005], [55.06838589635486,82.75413775981716], [55.10191352634667,82.75594057098347], [55.10134707574265,82.77838547937421], [55.08906921680575,82.7803113283662], [55.08141630471131,82.77691567467262], [55.07241058285451,82.7793940816409], [55.06825476388067,82.78380367909992], [55.05587117437787,82.80666687458144], [55.045219464114034,82.82143268666589], [55.03782775793765,82.83793900094348], [55.02796950486025,82.86972317776998], [55.02343934535529,82.87910018048603], [55.01950662666435,82.88824114880872], [55.018269046191726,82.89129137286155], [55.01141338816673,82.90493845232935], [55.00154706615863,82.92605280169447], [54.99838932825117,82.92785524615246], [54.987693103558435,82.93433546313243], [54.98469468595594,82.94021486529307], [54.980869217579375,82.94665216692881], [54.97726553827889,82.94841169604264], [54.975907903612644,82.95504211672744], [54.97371090621528,82.95669435748059], [54.97157161295711,82.95977424589144], [54.97012348242422,82.96010755227108], [54.96431353250293,82.96901391112405], [54.958124515526976,82.98047515788278], [54.95557431740753,82.98537320682004], [54.95290046246448,82.98915545680717], [54.950819237419964,82.99362435230216], [54.949212535984444,83.00497680195768], [54.94827156485913,83.01394879111251], [54.94718004151225,83.02262305506669], [54.94549097782668,83.02726864108045], [54.94033379755381,83.03467153796151], [54.92851935525599,83.03952468620679], [54.91991505765225,83.03691056455463], [54.91071259353228,83.03408558052763], [54.9055577803494,83.02843561247359], [54.896433401691034,83.02468877695148], [54.87679266981941,83.01187360322174], [54.869868561778276,83.00443936171929], [54.86324563949399,82.99884059306981], [54.85587886887641,82.99221185615859], [54.85197745735008,82.98317985997004], [54.84763628338035,82.9753370808105], [54.847868443597065,82.96514468655388], [54.838822489295524,82.93514686093134], [54.844110378639435,82.93214278683467], [54.862135982289104,82.94802146420284], [54.86550250587106,82.93394523129267], [54.87282866956011,82.93463187680045], [54.87530342265842,82.9438157604675], [54.87555088956907,82.97007995114127], [54.875402409607865,82.98329787716672], [54.89356238155182,82.98261123165888], [54.901477204692966,82.96791065624416]
        ]],       {         balloonContent: 'Правый Берег'        },        {                        fillColor: '#32abff',            fillOpacity: 0.3,            strokeColor: '#0000FF',            strokeOpacity: 0.7,            strokeWidth: 1,            zindex: 100,            borderRadius: 12        });
        zloradnijMap.geoObjects.add(WestSide);
        zloradnijMap.geoObjects.add(EastSide);

        clientAddress = routerStore.getClientAddress();

        if(clientAddress != undefined){
            if(clientAddress.indexOf(userCity) == -1){
                clientAddress = userCity + clientAddress;
            }

            storesList = $('.all-products-input-for-set-store-params').data('json');
            routerStore.count = routerStore.countOfObject(storesList);

            jQuery.each(storesList,function(i,stores){
                if(stores.length > 1){
                    jQuery.each(stores,function(j,store){
                        routerStore.countShopStores[store.shop_group_id] = stores.length;
                    });
                    jQuery.each(stores,function(j,store){
                        if(store.store_address.indexOf(userCity) == -1){
                            store.store_address = userCity + store.store_address;
                        }

                        ymaps.route([
                            store.store_address,
                            clientAddress
                        ]).then(function (route) {
                            zloradnijMap.geoObjects.add(route);
                            routerStore.getSmall(store,route.getLength());
                        });
                    });
                }else{
                    routerStore.countDown();
                }
            });

            //  get short rout club # FOR METRO #
            routerStore.countMetro = routerStore.countOfObject(metroClubAddress);

            if(routerStore.countOfObject(metroClubAddress) > 1){
                jQuery.each(metroClubAddress,function(j,store){
                    if(store.indexOf(userCity) == -1){
                        store = userCity + store;
                    }

                    ymaps.route([
                        store,
                        clientAddress
                    ]).then(function (route) {
                        zloradnijMap.geoObjects.add(route);
                        routerStore.getMetroSmall(j,route.getLength());
                    },
                        function (error) {
                            alert('Возникла ошибка: ' + error.message);
                        });
                });
            }else{
                routerStore.metroCountDown();
            }
            //  END >> get short rout club # FOR METRO #
        }
    });
}

$(document).ready(function(){
    ymaps.ready(routerStore.initMapsForStore);
});
