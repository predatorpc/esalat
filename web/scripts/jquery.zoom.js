(function ($) {
    $(document).ready(function () {
        //$('.cloud-zoom, .cloud-zoom-gallery').CloudZoom();
    });

    function format(str) {
        for (var i = 1; i < arguments.length; i++) {
            str = str.replace('%' + (i - 1), arguments[i]);
        }
        return str;
    }

    function CloudZoom(jWin, opts) {
        var sImg = $('img', jWin);
        var	img1;
        var	img2;
        var zoomDiv = null;
        var	$mouseTrap = null;
        var	lens = null;
        var	$tint = null;
        var	softFocus = null;
        var	$ie6Fix = null;
        var	zoomImage;
        var controlTimer = 0;
        var cw, ch;
        var destU = 0;
        var	destV = 0;
        var currV = 0;
        var currU = 0;
        var filesLoaded = 0;
        var mx,
            my;
        var ctx = this, zw;
        setTimeout(function () {
            //<img src="/images/loading.gif"/>
            if ($mouseTrap === null) {
                var w = jWin.width();
                jWin.parent().append(format('<div style="width:%0px;position:absolute;top:75%;left:%1px;text-align:center" class="cloud-zoom-loading" >Загрузка...</div>', w / 3, (w / 2) - (w / 6))).find(':last').css('opacity', 0.5);
            }
        }, 200);
        var ie6FixRemove = function () {

            if ($ie6Fix !== null) {
                $ie6Fix.remove();
                $ie6Fix = null;
            }
        };
        this.removeBits = function () {
            //$mouseTrap.unbind();
            if (lens) {
                lens.remove();
                lens = null;
            }
            if ($tint) {
                $tint.remove();
                $tint = null;
            }
            if (softFocus) {
                softFocus.remove();
                softFocus = null;
            }
            ie6FixRemove();

            $('.cloud-zoom-loading', jWin.parent()).remove();
        };
        this.destroy = function () {
            jWin.data('zoom', null);

            if ($mouseTrap) {
                $mouseTrap.unbind();
                $mouseTrap.remove();
                $mouseTrap = null;
            }
            if (zoomDiv) {
                zoomDiv.remove();
                zoomDiv = null;
            }
            //ie6FixRemove();
            this.removeBits();
        };
        this.fadedOut = function () {
            if (zoomDiv) {
                zoomDiv.remove();
                zoomDiv = null;
            }
            this.removeBits();
            //ie6FixRemove();
        };

        this.controlLoop = function () {
            if (lens) {
                var x = (mx - sImg.offset().left - (cw * 0.5)) >> 0;
                var y = (my - sImg.offset().top - (ch * 0.5)) >> 0;

                if (x < 0) {
                    x = 0;
                }
                else if (x > (sImg.outerWidth() - cw)) {
                    x = (sImg.outerWidth() - cw);
                }
                if (y < 0) {
                    y = 0;
                }
                else if (y > (sImg.outerHeight() - ch)) {
                    y = (sImg.outerHeight() - ch);
                }

                lens.css({
                    left: x,
                    top: y
                });
                lens.css('background-position', (-x) + 'px ' + (-y) + 'px');

                destU = (((x) / sImg.outerWidth()) * zoomImage.width) >> 0;
                destV = (((y) / sImg.outerHeight()) * zoomImage.height) >> 0;
                currU += (destU - currU) / opts.smoothMove;
                currV += (destV - currV) / opts.smoothMove;

                zoomDiv.css('background-position', (-(currU >> 0) + 'px ') + (-(currV >> 0) + 'px'));
            }
            controlTimer = setTimeout(function () {
                ctx.controlLoop();
            }, 30);
        };
        this.init2 = function (img, id) {
            filesLoaded++;
            // console.log(img.src + ' ' + id + ' ' + img.width);
            if (id === 1) {
                zoomImage = img;
            }
            //this.images[id] = img;
            if (filesLoaded === 2) {
                this.init();

            }
        };


        this.init = function () {
            //alert(zoomImage.src);
            $('.cloud-zoom-loading', jWin.parent()).remove();
            $mouseTrap = jWin.parent().append(format("<div class='mousetrap' style='background-image:url(\""+ opts.transparentImage +"\");z-index:79;position:absolute;width:%0px;height:%1px;left:%2px;top:%3px;\'></div>", sImg.outerWidth(), sImg.outerHeight(), 0, 0)).find(':last');

            //////////////////////////////////////////////////////////////////////
            /* Do as little as possible in mousemove event to prevent slowdown. */
            $mouseTrap.bind('mousemove', this, function (event) {
                // Just update the mouse position
                mx = event.pageX;
                my = event.pageY;
            });

            //////////////////////////////////////////////////////////////////////
            $mouseTrap.bind('mouseleave', this, function (event) {
                jWin.trigger('cloudzoom_end_zoom');
                clearTimeout(controlTimer);
                //event.data.removeBits();
                if(lens) { lens.fadeOut(299); }
                if($tint) { $tint.fadeOut(299); }
                if(softFocus) { softFocus.fadeOut(299); }
                zoomDiv.fadeOut(300, function () {
                    ctx.fadedOut();
                });

                return false;
            });
            //////////////////////////////////////////////////////////////////////
            $mouseTrap.bind('mouseenter', this, function (event) {
                jWin.trigger('cloudzoom_start_zoom');
                mx = event.pageX;
                my = event.pageY;
                zw = event.data;
                if (zoomDiv) {
                    zoomDiv.stop(true, false);
                    zoomDiv.remove();
                }

                var xPos = opts.adjustX,
                    yPos = opts.adjustY;

                var siw = sImg.outerWidth();
                var sih = sImg.outerHeight();

                var w = opts.zoomWidth;
                var h = opts.zoomHeight;
                if (opts.zoomWidth == 'auto') {
                    w = siw;
                }
                if (opts.zoomHeight == 'auto') {
                    h = sih;
                }
                //$('#info').text( xPos + ' ' + yPos + ' ' + siw + ' ' + sih );

                var appendTo = jWin.parent(); // attach to the wrapper
                switch (opts.position) {
                    case 'top':
                        yPos -= h; // + opts.adjustY;
                        break;
                    case 'right':
                        xPos += siw; // + opts.adjustX;
                        break;
                    case 'bottom':
                        yPos += sih; // + opts.adjustY;
                        break;
                    case 'left':
                        xPos -= w; // + opts.adjustX;
                        break;
                    case 'inside':
                        w = siw;
                        h = sih;
                        break;
                    default:
                        appendTo = $('#' + opts.position);
                        if (!appendTo.length) {
                            appendTo = jWin;
                            xPos += siw; //+ opts.adjustX;
                            yPos += sih; // + opts.adjustY;
                        } else {
                            w = appendTo.innerWidth();
                            h = appendTo.innerHeight();
                        }
                }
                zoomDiv = appendTo.append(format('<div id="cloud-zoom-big" class="cloud-zoom-big" style="display:none;position:absolute;left:%0px;top:%1px;width:%2px;height:%3px;background-image:url(\'%4\');z-index:79;"></div>', xPos, yPos, w, h, zoomImage.src)).find(':last');

                if (sImg.attr('title') && opts.showTitle) {
                    zoomDiv.append(format('<div class="cloud-zoom-title">%0</div>', sImg.attr('title'))).find(':last').css('opacity', opts.titleOpacity);
                }

                // Fix ie6 select elements wrong z-index bug. Placing an iFrame over the select element solves the issue...
                var browserCheck = /(msie) ([\w.]+)/.exec( navigator.userAgent );
                if (browserCheck) {
                    if ((browserCheck[1] || "") == 'msie' && (browserCheck[2] || "0" ) < 7) {
                        $ie6Fix = $('<iframe frameborder="0" src="#"></iframe>').css({
                            position: "absolute",
                            left: xPos,
                            top: yPos,
                            zIndex: 99,
                            width: w,
                            height: h
                        }).insertBefore(zoomDiv);
                    }
                }
                zoomDiv.fadeIn(500);

                if (lens) {
                    lens.remove();
                    lens = null;
                } /* Work out size of cursor */
                cw = (sImg.outerWidth() / zoomImage.width) * zoomDiv.width();
                ch = (sImg.outerHeight() / zoomImage.height) * zoomDiv.height();

                lens = jWin.append(format("<div class = 'cloud-zoom-lens' style='display:none;z-index:78;position:absolute;width:%0px;height:%1px;'></div>", cw, ch)).find(':last');

                $mouseTrap.css('cursor', lens.css('cursor'));

                var noTrans = false;

                if (opts.tint) {
                    lens.css('background', 'url("' + sImg.attr('src') + '")');
                    $tint = jWin.append(format('<div style="display:none;position:absolute; left:0px; top:0px; width:%0px; height:%1px; background-color:%2;" />', sImg.outerWidth(), sImg.outerHeight(), opts.tint)).find(':last');
                    $tint.css('opacity', opts.tintOpacity);
                    noTrans = true;
                    $tint.fadeIn(500);
                }
                if (opts.softFocus) {
                    lens.css('background', 'url("' + sImg.attr('src') + '")');
                    softFocus = jWin.append(format('<div style="position:absolute;display:none;top:2px; left:2px; width:%0px; height:%1px;" />', sImg.outerWidth() - 2, sImg.outerHeight() - 2, opts.tint)).find(':last');
                    softFocus.css('background', 'url("' + sImg.attr('src') + '")');
                    softFocus.css('opacity', 0.5);
                    noTrans = true;
                    softFocus.fadeIn(500);
                }
                if (!noTrans) {
                    lens.css('opacity', opts.lensOpacity);
                }
                if ( opts.position !== 'inside' ) { lens.fadeIn(500); }
                zw.controlLoop();

                return;
            });
            jWin.trigger('cloudzoom_ready');
        };

        img1 = new Image();
        $(img1).load(function () {
            ctx.init2(this, 0);
        });
        img1.src = sImg.attr('src');

        img2 = new Image();
        $(img2).load(function () {
            ctx.init2(this, 1);
        });
        img2.src = jWin.attr('href');
        //Флаг;
        img2.onerror = function(){ img2.src = jWin.attr('href').replace("_max", "")};
    }

    $.fn.CloudZoom = function (options) {
        // IE6 background image flicker fix
        try {
            document.execCommand("BackgroundImageCache", false, true);
        } catch (e) {}
        this.each(function () {
            var	relOpts, opts;
            eval('var	a = {' + $(this).attr('rel') + '}');
            relOpts = a;
            if ($(this).is('.cloud-zoom')) {
                opts = $.extend({}, $.fn.CloudZoom.defaults, options);
                opts = $.extend({}, opts, relOpts);

                $(this).css({
                    'position': 'relative',
                    'display': 'block'
                });
                $('img', $(this)).css({
                    'display': 'block'
                });
                if (!$(this).parent().hasClass('cloud-zoom-wrap') && opts.useWrapper) {
                    $(this).wrap('<div class="cloud-zoom-wrap"></div>');
                }
                $(this).data('zoom', new CloudZoom($(this), opts));

            } else if ($(this).is('.cloud-zoom-gallery')) {
                opts = $.extend({}, relOpts, options);
                $(this).data('relOpts', opts);
                $(this).bind('click', $(this), function (event) {
                    var data = event.data.data('relOpts');
                    $('#' + data.useZoom).data('zoom').destroy();
                    $('#' + data.useZoom).attr('href', event.data.attr('href'));
                    $('#' + data.useZoom + ' img').attr('src', event.data.data('relOpts').smallImage);
                    $('#' + event.data.data('relOpts').useZoom).CloudZoom();
                    return false;
                });
            }
        });
        return this;
    };

    $.fn.CloudZoom.defaults = {
        zoomWidth: '400',
        zoomHeight: '400',
        position: 'right',
        transparentImage: '.',
        useWrapper: true,
        tint: false,
        tintOpacity: 0.5,
        lensOpacity: 0.5,
        softFocus: false,
        smoothMove: 2,
        showTitle: false,
        titleOpacity: 0.5,
        adjustX: 14,
        adjustY: 0
    };

})(jQuery);