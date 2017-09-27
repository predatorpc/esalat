<?php

namespace app\components\html;

use Yii;
use yii\base\Widget;
/**
 * @var object $model
 * @var object $variation
 * @var array $image
 * @var array $sticker
 * @var string $alias
 * @var string $url
 *
 */
// Класс родительского блока "product-item" - якорь для javascript. НЕ УДАЛЯТЬ и не навешивать на него стили !!!!
class WCounters extends Widget
{

    public function run()
    {?>
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript">
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function() {
                    try {
                        w.yaCounter30719268 = new Ya.Metrika({
                            id:30719268,
                            clickmap:true,
                            trackLinks:true,
                            accurateTrackBounce:true,
                            webvisor:true,
                            ecommerce:"dataShop"
                        });
                    } catch(e) { }
                });

                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";

                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else { f(); }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/30719268" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->

        <!-- Google Analytics (69832280) -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
            ga('create', 'UA-69832280-1', 'auto');
            ga('send', 'pageview');
            ga('require', 'ecommerce');
        </script>
        <!-- Google Code for Conversion Page -->
        <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = 957141220;
            var google_conversion_language = "en";
            var google_conversion_format = "3";
            var google_conversion_color = "ffffff";
            var google_conversion_label = "mnTTCM3d8WEQ5KGzyAM";
            var google_remarketing_only = false;
            /* ]]> */
        </script>
        <script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>
        <script type="text/javascript">(window.Image ? (new Image()) : document.createElement('img')).src = 'https://vk.com/rtrg?r=jEcOZ7aYu3us234XpOvn0r*XY/b5V9XT5WJJs6Qf6qO2HZG/vBx6E1qOB/DEksy7fpoQfYyonBONlnwmWLXjlB*VdgZl9d1u3IGYd5uZBE1IJDtiSEeP0PwjUTrn4mrCoYMaANAig3BoVF48b5aXLzT334QlYjLr7V9c65mBnTA-&pixel_id=1000022724';</script>
        <!-- Код тега ремаркетинга Google -->
        <script type="text/javascript">
            /* <![CDATA[ */
            var google_conversion_id = 862067589;
            var google_custom_params = window.google_tag_params;
            var google_remarketing_only = true;
            /* ]]> */
        </script>
        <script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>
        <noscript>
            <div style="display:inline;">
                <img height="1" width="1" style="border-style:none;" alt="" src="https://googleads.g.doubleclick.net/pagead/viewthroughconversion/862067589/?guid=ON&amp;script=0"/>
            </div>
        </noscript>


<!-- Pixel -->
<!--<script type="text/javascript">
    (function (d, w) {
            var n = d.getElementsByTagName("script")[0],
                        s = d.createElement("script"),
                                    f = function () { n.parentNode.insertBefore(s, n); };
                                            s.type = "text/javascript";
                                                    s.async = true;
                                                            s.src = "https://sas-pro.ru/pixel/index.php?img=XFZDGFdAXxVFRFRaU1ZDHEFH&nid=167853&uid=1744&ref="+d.referrer+"&cookie=" + encodeURIComponent(document.cookie);
                                                            
                                                                    if (w.opera == "[object Opera]") {
                                                                                d.addEventListener("DOMContentLoaded", f, false);
                                                                                        } else { f(); }
                                                                                            })(document, window);
                                                                                            </script>-->
                                                                                            <!-- /Pixel -->
                                                                                            
 <?php
    }
}
