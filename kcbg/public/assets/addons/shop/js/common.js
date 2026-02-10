$(function () {
    window.isMobile = !!("ontouchstart" in window);

    //new LazyLoad({elements_selector: ".lazy"});

    if (!isMobile) {
        // 搜索框
        $("input[name='search']").on("focus", function () {
            $(this).closest(".form-search").addClass("focused");
        }).on("blur", function (e) {
            var that = this;
            setTimeout(function () {
                $(that).closest(".form-search").removeClass("focused");
            }, 500);
        });
    }

    // 点击收藏
    $(".addbookbark").attr("rel", "sidebar").click(function () {
        var url = location.href;
        var title = $(this).attr("title") || document.title;
        if (/firefox/i.test(navigator.userAgent)) {
        } else if (window.external && window.external.addFavorite) {
            window.external.addFavorite(url, title);
        } else if (window.sidebar && window.sidebar.addPanel) {
            window.sidebar.addPanel(title, url, "");
        } else {
            var touch = (navigator.userAgent.toLowerCase().indexOf('mac') != -1 ? 'Command' : 'CTRL');
            layer.msg('请使用 ' + touch + ' + D 添加到收藏夹.');
        }
        return false;
    });

    // 点击收藏
    $(document).on('click', '.btn-collect', function (e) {
        var that = this;
        SHOP.api.ajax({
            url: "/addons/shop/ajax/collect",
            data: {goods_id: $(this).data("id")}
        }, function (data, ret) {
            $("span", that).text("已收藏");
            layer.msg(ret.msg);
            return false;
        });
    });

    if (typeof wx !== 'undefined') {

        //分享参数配置
        var shareConfig = {
            title: $("meta[property='og:title']").attr("content") || document.title,
            description: $("meta[property='og:description']").attr("content") || "",
            url: $("meta[property='og:url']").attr("content") || location.href,
            image: $("meta[property='og:image']").attr("content") || ""
        };

        //微信公众号内分享
        shareConfig.url = location.href;
        SHOP.api.ajax({
                url: "/addons/shop/ajax/share",
                data: {url: shareConfig.url},
                loading: false
            }, function (data, ret) {
                try {
                    wx.config({
                        appId: data.appId,
                        timestamp: data.timestamp,
                        nonceStr: data.nonceStr,
                        signature: data.signature,
                        jsApiList: [
                            'checkJsApi',
                            'updateAppMessageShareData',
                            'updateTimelineShareData',
                        ]
                    });
                    var shareData = {
                        title: shareConfig.title,
                        desc: shareConfig.description,
                        link: shareConfig.url,
                        imgUrl: shareConfig.image,
                        success: function () {
                            layer.closeAll();
                        },
                        cancel: function () {
                            layer.closeAll();
                        }
                    };
                    wx.ready(function () {
                        wx.updateAppMessageShareData(shareData);
                        wx.updateTimelineShareData(shareData);
                    });

                } catch (e) {
                    console.log(e);
                }
                return false;
            }, function () {
                return false;
            }
        );
    }

    // 点击分享
    $(document).on('click', '.btn-share', function (e) {
        var that = this;
        var data = $(that).data();
        if (typeof wx != 'undefined') {
            layer.msg("请点击右上角的●●●进行分享");
        } else {
            layer.open({
                title: '分享',
                content: '<div class="social-share text-center mt-2 mb-1"><div class="share-qrcode"></div><p class="small">请使用微信扫一扫进行分享</p></div>',
                btn: false,
                success: function (layero) {
                    $(".share-qrcode").qrcode({text: $(that).data("url") || location.href});
                    $('.social-share', layero).data(data).share({sites: 'qzone,qq,weibo,douban'});
                }
            });
        }
        return false;
    });

    //领取优惠券
    $(document).on('click', '.btn-coupon', function () {
        let id = $(this).data('name');
        SHOP.api.ajax({
            url: "/addons/shop/coupon/drawCoupon",
            data: {id: id}
        }, function (data, ret) {
            setTimeout(function () {
                window.location.reload();
            }, 1500);
        }, function (data, ret) {
            if (ret.msg.indexOf("请登录") > -1) {
                layer.alert("请登录后再进行操作", {
                    title: "温馨提示", icon: 0, btn: ["去登录"]
                }, function () {
                    location.href = ret.url;
                });
                return false;
            }
        });
    });

    // 倒计时
    $('[data-countdown]').each(function () {
        var that = this;
        var $this = $(this), finalDate = parseInt($(this).data('countdown'));
        if (finalDate > 0) {
            finalDate = isNaN(finalDate) ? finalDate : new Date().getTime() + finalDate * 1000;
            var format = $(that).data("format");
            $this.on('finish.countdown', function () {
                if (typeof $this.data("finish") == 'function') {
                    $this.data("finish").call($this);
                } else {
                    location.reload();
                }
            }).countdown(finalDate, function (event) {
                $this.html(event.strftime(format) || event.strftime('%D天%H时%M分%S秒'));
            });
        }
    });

    var backtotop = $('#back-to-top');
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 200) {
            backtotop.fadeIn();
        } else {
            backtotop.fadeOut();
        }
    });
    $(window).trigger("scroll");

    // 回到顶部
    backtotop.on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });

    // 如果是PC则移除navbar的dropdown点击事件
    if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobi/i.test(navigator.userAgent)) {
        $(".nav-bar [data-toggle='dropdown']").removeAttr("data-toggle");
    } else {
        $(".navbar-nav ul li:not(.dropdown-submenu):not(.dropdown) a").removeAttr("data-toggle");
    }

    $.fn.typeahead.Constructor.prototype.click = function (e) {

    };
    if (!isMobile) {
        // 搜索自动完成
        $("#searchinput").typeahead({
            onSelect: function (item) {
                location.href = item.value.url;
            },
            grepper: function (data) {
                return data;
            },
            render: function (items) {
                var that = this;
                items = $(items).map(function (i, item) {
                    var i = $(that.options.item);
                    i.data("value", item);
                    i.find('a').attr('href', item.url);
                    i.find('a').html('<h5>' + item.title + '</h5>');
                    return i[0];
                });
                items.first().addClass('active');
                that.$menu.css("width", "250px");
                that.$menu.html(items);
                return that;
            },
            alignWidth: false,
            ajax: {
                url: $("#searchinput").data("typeahead-url"),
                valueField: "url",
                method: "post",
                dataType: "JSON",
                preDispatch: function (query) {
                    return {
                        search: query
                    };
                },
                preProcess: function (data) {
                    return data;
                }
            }
        });
    }

    // 点击支付
    $(document).on("click", ".btn-paynow", function () {
        layer.confirm("请根据支付状态选择下面的操作按钮", {title: "温馨提示", icon: 0, btn: ["支付成功", "支付失败"]}, function () {
            location.reload();
        });
    });

    // 导航事件
    var st;
    $(document).on("mouseenter", "li.category,.nav-item-topmenu", function () {
        clearTimeout(st);
        $(".top-menu").addClass("open");
    });
    $(document).on("mouseleave", "li.category,.nav-item-topmenu", function () {
        st = setTimeout(function () {
            if (!$(".top-menu").hasClass("opened")) {
                $(".top-menu").removeClass("open");
            }
        }, 150);
    });

    //顶部菜单
    $(document).on("mouseenter mouseleave", ".top-menu", function (e) {
        if (e.type === 'mouseenter') {
            $(this).addClass("opened");
        } else {
            $(this).removeClass("open opened");
        }
    });

    // 顶部浮动导航
    var headerAction = $(".header-menu");
    if (headerAction.length > 0 && $(window).width() > 1200) {
        var actionOffset = headerAction.offset().top;
        var inNav = false;
        var hasBanner = headerAction.hasClass("hasbanner");
        var check_action = function () {
            if ($(window).scrollTop() >= actionOffset) {
                headerAction.addClass("fixed");
                if (hasBanner) {
                    headerAction.removeClass("hasbanner");
                }
                inNav = true;
            } else {
                headerAction.removeClass("fixed");
                if (hasBanner) {
                    headerAction.addClass("hasbanner");
                }
                inNav = false;
            }
        };
        $(window).scroll(function () {
            check_action();
        });
        check_action();
    }
    if (headerAction.length > 0) {
        $("a[data-toggle='dropdown']", headerAction).on("click", function () {
            $(this).next("ul").find("li:first-child a.btn-download").trigger("click");
        });
    }
});
