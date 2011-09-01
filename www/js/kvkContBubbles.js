(function ($) {
    var custOpts        = {};
    var $elem           = {};
    var cbRefs          = [];
    
    var itemDefaults    = {
        pos: 25,
        link: '',
        plusTop: 0,
        plusLeft: 0 //dorovnavacky :)
    };
    
    //
    //
    //
    
    Array.prototype.shuffle = function ()
    {
        //for (var rnd, tmp, i=this.length, lol = 0; i; rnd=parseInt(Math.random()*i), tmp=this[--i], this[i]=this[rnd], this[rnd]=tmp);
        
        for (var rnd, tmp, i=this.length, lol = 0; lol < this.length; lol++)
        {
            rnd         = parseInt(Math.random()*i);
            
            tmp         = this[--i];
            
            this[i]     = this[rnd];
            
            this[rnd]   = tmp;
        };
    };//Array.prototype.shuffle
    
    //
    //
    //
    
    var hide = function ()
    {
        var $cb = $('div.kvkContBubbles.' + custOpts.name + ':first');
        
        $cb.hide();
    };//hide
    
    var show = function (customName)
    {
        var opts = custOpts;
        
        //
        
        var name = (customName)? customName : opts.name;
        
        $elem = cbRefs[name];
        
        //
        
        var $cb = $('div.kvkContBubbles.' + name + ':first');
        
        $cb.css('opacity',0).show();
        
        $cb.find('div.item').each(function () {
            var $this = $(this);
            
            setItemProps($this);
        });
        
        //
        
        $cb.css('opacity',1);
        
        //
        
        var idxs = [];
        
        $cb.find('div.fakeItem').each(function () {
            var $this = $(this);
            var reg = new RegExp('^(.*)fakeItem-([0-9]+)(.*)$');
            
            var idx = $this.get(0).className.replace(reg,'$2');
            
            idxs[idxs.length] = idx;
        });
        
        idxs.shuffle();
        
        var act         = 0;
        var showAction  = setInterval(function () {
            var $this   = $cb.find('div.item' + idxs[act]);
            var $fake   = $cb.find('div.fakeItem-' + idxs[act]);
            
            var factor  = 8;
            var width   = $fake.width();
            var height  = $fake.height();
            var left    = parseInt($fake.css('left'),10);
            var top     = parseInt($fake.css('top'),10);
            
            //
            
            $fake.show().css('opacity',0.9).animate({
                width: width + (factor * 2),
                height: height + (factor * 2),
                left: left - factor,
                top: top - factor
            },50,function () {
                $fake.animate({
                    width: width,
                    height: height,
                    left: left,
                    top: top,
                    opacity: 0
                },100,function () {
                    $fake.hide();
                });
            
                $this.animate({
                    opacity: 1
                },100);
            });
            
            //
            
            act++;
            
            //
            
            if (act == idxs.length)
            {
                clearInterval(showAction);
            }
        },opts.showInterval);
    };//show

    var setItemProps = function ($item)
    {
        var width       = $item.width();
        var height      = $item.height();
        
        var itemOpts    = $.extend({},itemDefaults,custOpts.items[$item.data('optsIdx')]);
        var side        = itemOpts.side;
        
        var pos         = getAbsPos($elem.get(0));
        var w           = $elem.outerWidth();
        var h           = $elem.outerHeight();
        
        //
        
        var baseX       = pos.Left;
        var baseY       = pos.Top;
        
        if (side == 'top')
        {
            baseY -= height;
            
            baseY += 10;
        }//if (side == 'top')
        else if (side == 'right')
        {
            baseX += w;
            
            baseX -= 10;
        }//else if
        else if (side == 'bottom')
        {
            baseY += h;
            
            //baseY += height;
            
            baseY -= 10;
        }//else if
        else if (side == 'left')
        {
            baseX += 10;
        }//else if
        
        //
        
        var contSize    = ((side == 'top') || (side == 'bottom'))? w : h;
        var itemPos     = _getItemPosVal(itemOpts.pos,contSize);
        
        //
        
        var finalPos    = {};
        
        if ((side == 'top') || (side == 'bottom'))
        {
            finalPos.x  = baseX + itemPos + parseInt($item.data('tailLeft'),10);
            finalPos.y  = baseY + parseInt($item.data('tailTop'),10);
        }//if ((side == 'top') || ( ... 
        else
        {
            finalPos.x  = baseX + parseInt($item.data('tailLeft'),10);
            finalPos.y  = baseY + itemPos + parseInt($item.data('tailTop'),10);
        }//else
        
        //
        
        var diffX   = -(width - 63);
        var diffY   = 0;
        
        var pct     = (itemPos / w) * 100;
            
        if (pct > 33)
        {
            diffX = -150;
        }
        
        if ((pct > 66) || (side == 'right'))
        {
            diffX = (width - 63) - custOpts.bubbleWidth;
        }
        
        if ((side == 'left') || (side == 'right'))
        {
            diffY = 55;
        
            var pct = (itemPos / h) * 100;
            
            if (pct > 50)
            {
                diffY = -50;
            }
        }//if ((side == 'left') || ( ... 
        
        //
        
        var left    = finalPos.x + itemOpts.plusLeft + diffX;
        var top     = finalPos.y + itemOpts.plusTop + diffY;
        
        $item.css({
            left: left,
            top: top,
            opacity: 0
        });
        
        //
        
        //alert(pos.Left + ',' + pos.Top);
        //alert(baseX + ',' + baseY);
        //alert($item.data('tailLeft') + ',' + $item.data('tailTop'));
        //alert(finalPos.x + ',' + finalPos.y);
        //alert(diffX + ',' + diffY);
        
        var $fake   = $item.next('.fakeItem:first');
            
        var fWidth  = width - (6 * 2) - (9 * 2); //6 - shadow; 9 - padding
        var fHeight = height - (6 * 2) - (9 * 2); //6 - shadow; 9 - padding
            
        $fake.css({
            display: 'none',
            width: fWidth,
            height: fHeight,
            left: left + 6,
            top: top + 6,
            opacity: 0
        });
    };//setItemProps

    //

    var init = function (opts)
    {
        if (!$('div.kvkContBubbles.' + opts.name).get(0))
        {
            $('body').append('<div class="kvkContBubbles ' + opts.name + '"></div>');
        }//if (!$('div.kvkContBubbles.' + opts.name).get(0))
        
        //
        
        var $cb = $('div.kvkContBubbles.' + opts.name + ':first');
        
        if (!cbRefs[opts.name])
        {
            cbRefs[opts.name] = $elem;
        }
        
        if (!$cb.data('itemIdx'))
        {
            $cb.data('itemIdx',0);
        }
        
        $cb.css('opacity',0);
        
        //
        
        if (opts.items.length > 0)
        {
            for (var i = 0; i < opts.items.length; i++)
            {
                try
                {
                    var idx = $cb.data('itemIdx');
                    
                    idx++;
                    
                    $cb.data('itemIdx',idx);
                    
                    //
                
                    var item        = opts.items[i];
                    
                    var itemOpts    = $.extend({},itemDefaults,item);
                    
                    //var typeCn      = 'right top';
                    var width       = custOpts.bubbleWidth;  //calc spread & stuff ... 
                    var props       = getProps(itemOpts,width);
                    var text        = (itemOpts.link != '')? '<a href="' + itemOpts.link + '">' + item.text + '</a>' : item.text;
                    
                    var cont        = '<div class="item ' + props.typeCn + ' item' + idx + '" style="width: ' + custOpts.bubbleWidth + 'px;">'+
                                        '<div class="corner top left"></div>'+
                                        '<div class="corner top right"></div>'+
                                        '<div class="corner bottom left"></div>'+
                                        '<div class="corner bottom right"></div>'+
                                        
                                        props.top+
                                        
                                        '<div class="fill left">'+
                                            '<div class="fill right">'+
                                                '<div class="cont">'+
                                                    text+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                        
                                        props.bottom+
                                        
                                        props.tail+
                                    '</div>'+
                                    
                                    '<div class="fakeItem fakeItem-' + idx + '"><p>' + item.text + '</p></div>';
                    
                    //
                    
                    $cb.append(cont);
                    
                    $cb.find('.item' + idx).data({
                        'tailLeft': props.tailLeft,
                        'tailTop': props.tailTop,
                        'optsIdx': i
                    });
                }//try
                catch (error) {}
            }//for
        }//if (opts.items.length > 0)
    };//init
    
    var _getItemPosVal = function (raw,contSize)
    {
        var reg = new RegExp('^([0-9]+)%$');
        
        if (raw.toString().match(reg))
        {
            var itemPos = parseInt(raw.replace(reg,'$1'),10);
            
            itemPos     = Math.max(0,itemPos);
            itemPos     = Math.min(100,itemPos);
            
            var val = parseInt((itemPos * contSize) / 100,10);
        }//if (raw.toString().match(reg))
        else
        {
            var itemPos = parseInt(raw,10);
        
            itemPos     = Math.max(0,itemPos);
            itemPos     = Math.min(contSize,itemPos);
            
            var val     = itemPos;
        }//else
        
        //
        
        return val;
    };//_getItemPosVal
    
    var getProps = function (itemOpts,width)
    {
        var w           = $elem.outerWidth();
        var h           = $elem.outerHeight();
        var side        = itemOpts.side;
        
        var contSize    = ((side == 'top') || (side == 'bottom'))? w : h;
        var pos         = _getItemPosVal(itemOpts.pos,contSize);
        //var pos         = parseInt(itemOpts.pos,10);
        var typeCn      = '';
        
        if ((side == 'top') || (side == 'bottom'))
        {
            var pct = (pos / w) * 100;
        
            typeCn = 'left';
            
            if (pct < 66)
            {
                typeCn = 'middle';
            }
            
            if (pct < 33)
            {
                typeCn = 'right';
            }
            
            //
            
            typeCn += (side == 'top')? ' bottom' : ' top';
        }//if ((side == 'top') || ( ... 
        else
        {
            var pct = (pos / h) * 100;
        
            typeCn = 'top';
            
            if (pct > 50)
            {
                typeCn = 'bottom';
            }
            
            //
            
            typeCn += (side == 'left')? ' right' : ' left';
        }//else
    
        //
    
        var props = {typeCn: typeCn};
        var parts = typeCn.split(' ');
        
        if (parts.length > 0)
        {
            var left    = 0;
            var right   = 0;
            var bottom  = 0;
            var top     = 0;
            var middle  = 0;
        
            for (var i = 0; i < parts.length; i++)
            {
                var item = parts[i];
                
                if (item == 'left')
                {
                    left = 1;
                }
                else if (item == 'right')
                {
                    right = 1;
                }
                else if (item == 'bottom')
                {
                    bottom = 1;
                }
                else if (item == 'top')
                {
                    top = 1;
                }
                else if (item == 'middle')
                {
                    middle = 1;
                }
            }//for
            
            //
            
            props.top       = '<div class="fill top"></div>';
            props.bottom    = '<div class="fill bottom"></div>';
            props.tail      = '<div class="tail"></div>';
            props.tailLeft  = 0;
            props.tailTop   = (side == 'bottom')? 28 : -28;
            
            var tailSizes = [];
            
            //corner items
            tailSizes["00110"] = 49;
            tailSizes["01100"] = 49;
            tailSizes["11000"] = 49;
            tailSizes["10010"] = 49;
            
            //horizontal middle items
            tailSizes["10001"] = 32;
            tailSizes["00101"] = 32;
            
            //vertical middle items
            tailSizes["00011"] = 32;
            tailSizes["01001"] = 32;
            
            var arr     = new Array(top,right,bottom,left,middle);
            var space   = width - (2 * 15);   //corners
            var code    = arr.join('');
            var size    = tailSizes[code];
            space      -= size;
            
            if (top || bottom) //horizontal
            {
                var cont    = '';
                var param   = (top)? 'top' : 'bottom';
            
                if (!middle)
                {
                    if (left)
                    {
                        cont += '<div class="fill ' + param + '" style="width: 10px;"></div>';
                        
                        cont += '<div class="fill ' + param + ' rest" style="width: ' + (space - 10) + 'px;"></div>';
                        
                        //
                        
                        props.tailLeft = 32;
                    }//if (left)
                    else
                    {
                        cont += '<div class="fill ' + param + '" style="width: ' + (space - 10) + 'px;"></div>';
                        
                        cont += '<div class="fill ' + param + ' rest" style="width: 10px;"></div>';
                        
                        //
                        
                        props.tailLeft = -32;
                    }//else
                }//if (!middle)
                else
                {
                    var w1 = parseInt(space / 2,10);
                    var w2 = space - w1;
                    
                    cont += '<div class="fill ' + param + '" style="width: ' + w1 + 'px;"></div>';
                        
                    cont += '<div class="fill ' + param + ' rest" style="width: ' + w2 + 'px;"></div>';
                    
                    //
                    
                    props.tail = '<div class="tail" style="left: ' + (w1 + 15) + 'px;"></div>';
                }//else
            
                //
            
                if (top)
                {
                    props.top = cont;
                }
                else
                {
                    props.bottom = cont;
                }
            }//if (top || bottom)
            else //vertical
            {
                //nothin ... for now :D
            }//else
        }//if (parts.length > 0)
        
        //
        
        return props;
    };//getProps
    
    var getAbsPos = function (elem)
    {
        var end  = false;
        var prnt = elem;
        var x    = 0;
        var y    = 0;
        
        while (!end)
        {
            if (prnt != null)
            {
                if (prnt.offsetLeft)
                {
                    x += prnt.offsetLeft;
                }
                
                if (prnt.offsetTop)
                {
                    y  += prnt.offsetTop;
                }
            
                prnt = prnt.offsetParent;
            }
            else
            {
                break;
            }
        }//while
        
        return {Left: x, Top: y};
    };//getAbsPos
    
    var imgsPreload = function (imgs)
    {
        if (imgs.length > 0)
        {
            var count   = $('.imgsPreloadBox').length;
            var id      = 'imgsPreloadBox_' + (count + 1);
        
            $('body').append('<div id="' + id + '" class="imgsPreloadBox kvkContBubbles" style="opacity: 0;"></div>');
        
            for (var i = 0; i < imgs.length; i++)
            {
                $('#' + id).append('<img src="' + imgs[i] + '" alt="image" />');
            }//for
        }//if (imgs.length > 0)
    };//imgsPreload
    
    //
    
    var reset = function ()
    {
        cbRefs = [];
    };//reset
    
    //
    //
    //
    
    $.fn.kvkContBubbles = function (options)
    {
        return $(this).each(function () {
            var el = $(this);
            var kv = this;
        
            kv.options  = {
                bubbleWidth: 300,
                imgFolder: 'img',
                showInterval: 250
            };
            
            kv.opts = $.extend({},kv.options,options);
            
            //
            
            var reg         = new RegExp('([/]+)$');
            var imgFolder   = kv.opts.imgFolder.replace(reg,'') + '/';
            
            var imgs = [];
            
            imgs[0] = imgFolder + 'fill.png';
            imgs[1] = imgFolder + 'fill-top.png';
            imgs[2] = imgFolder + 'fill-right.png';
            imgs[3] = imgFolder + 'fill-bottom.png';
            imgs[4] = imgFolder + 'fill-left.png';
            imgs[5] = imgFolder + 'sprite.png';
            
            if (!$('div.imgsPreloadBox.kvkContBubbles').get(0))
            {
                imgsPreload(imgs);
            }
            
            //
            
            custOpts    = kv.opts;
            $elem       = el;
            
            init(kv.opts);
        });
    };//$.fn.kvkContBubbles
    
    //
    
    $.fn.extend({
		kvkCbShow: show,
		kvkCbHide: hide,
		kvkCbReset: reset,
	});
})(jQuery);