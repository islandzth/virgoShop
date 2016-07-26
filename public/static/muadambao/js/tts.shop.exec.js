TTS.Framework.ListItem = {
    isLoading: false,
    page: 1,
    initPage: 1,
    hasNoMore: false,
    sort: null,
    topContributorSort: null
};


var nua = navigator.userAgent
var isAndroid = (nua.indexOf('Mozilla/5.0') > -1 && nua.indexOf('Android ') > -1 && nua.indexOf('AppleWebKit') > -1 && nua.indexOf('Chrome') === -1)


$(document).ready(function () {

    $("tr[id=product-item-manage]").click(function () {
        var productId = $(this).data("product-id");
        $("tr[class=product_tool_mobile]").fadeOut();
        $("tr[data-tool-for-id=" + productId + "]").fadeIn();
    });

    $("input[id=input-editable]").change(function (e) {
        e.preventDefault();
        var inputValue = $(this).val();
        var productId = $(this).data("id");
        var productName = $(this).data("name");
        var inputType = $(this).data("type");
        if (inputValue && inputType == "product-price") {
            inputValue = parseInt(inputValue.replace(/,/g, ""));
            if (!isNaN(inputValue) && inputValue < 10) {
                alert("Dữ liệu không hợp lệ");
            } else {
                if (confirm('Bạn có muốn thay đổi giá của sản phẩm ' + productName + '?')) {
                    $.post(SITE_URL + "ajax/changeProductPrice", {
                        'product_id': productId,
                        'product_price': inputValue,
                        '_token': CSRF_TOKEN
                    }, function (resp) {
                        if (resp.code == 1) {
                            alert('Đã thay đổi!');
                        }
                    }, 'json');
                } else {

                }
            }
        }
    });

    $("input[id=quantity-price-from], input[id=quantity-price-to]").change(function () {
        var price = parseInt($(this).val());

        if (!isNaN(price) && price < 1) {
            alert('Số lượng tối thiểu phải bằng 1');
            $(this).focus();
        }
    });

    $("a[id=request-to-buy]").click(function (e) {
        e.preventDefault();
        var productName = $(this).data('product-name');
        $("#requestToBuyModal #product-name").text(productName);
        $("#requestToBuyModal").modal("show");
    });

    $("#submit-request-buy").click(function (e) {
        $(".request-to-buy-form").hide();
        $(".request-to-buy-form-success").fadeIn();
    });

    $("#request-product-quantity").keyup(function () {
        var quantity = $(this).val();
        if (quantity != "") {
            if (isNaN(quantity) || quantity <= 0) {
                alert("Vui lòng chỉ nhập số lớn hơn 0");
                $(this).val('');
                $("#request-product-quantity").focus();
            }
        }
    });

    $("#request-product-price").keyup(function () {
        var quantity = $(this).val();
        if (quantity != "") {
            if (isNaN(quantity) || quantity <= 0) {
                alert("Vui lòng chỉ nhập số lớn hơn 0");
                $(this).val('');
                $("#request-product-price").focus();
            }
        }
    });


    $("body").on("keyup", ".cart-product-quantity", function () {
        var productId = $(this).data("id");
        var quantity = $(this).val();
        var productPrice = $(this).data("product-price");
        if (productId && quantity != "") {

            if (!isNaN(quantity) && quantity > 0) {
                TTS.Framework.Cart.ChangeQuantity(quantity, productId, productPrice, $(this));
            } else {
                alert("Vui lòng chỉ nhập số lớn hơn 0");
            }
        }
    });

    $("#add-product-to-cart").click(function (e) {
        e.preventDefault();
        var productId = $(this).data("product-id");
        if (productId) {
            TTS.Framework.Cart.Add(productId);
        } else {
            alert("Lỗi! Vui lòng F5 lại trang");
        }
    });

    $("a[id=show-product-statistic]").click(function (e) {
        e.preventDefault();
        var productElement = $(this).parents("tr");
        var productName = productElement.data("product-name");
        var productId = productElement.data("product-id");
        $("#statisticProductName").text(productName);
        $("#productStatisticModal").modal("show");
        $("#product-statistic-views").html("Đang tải dữ liệu...");
        renderProductChart(productId);
    });


    $("#view-shop-view-statistic").click(function (e) {
        e.preventDefault();
        var shopId = $(this).data("id");
        $("#shopStatisticModal").modal("show");
        $("#shop-statistic-views").html("Đang tải dữ liệu...");
        renderShopViewChart(shopId);
    });

    $("input[name=selected_product]").click(function () {
        var selectedCount = $("input[name=selected_product]:checked").length;

        if (selectedCount > 0) {
            $("#delete-selected-product, #pause-selected-product, #run-selected-product").removeAttr('disabled');
        } else {
            $("#delete-selected-product, #pause-selected-product, #run-selected-product").attr('disabled', 'disabled');
        }
    });

    $("#selectallproduct").click(function () {
        if (this.checked) {
            $('input[name=selected_product]').each(function () {
                this.checked = true;
            });
            $("#delete-selected-product, #pause-selected-product, #run-selected-product").removeAttr('disabled');
        } else {
            $('input[name=selected_product]').each(function () {
                this.checked = false;
            });
            $("#delete-selected-product, #pause-selected-product, #run-selected-product").attr('disabled', 'disabled');
        }
    });

    $("#select-all").click(function (e) {
        e.preventDefault();
        $('input[name=selected_product]').each(function () {
            this.checked = true;
        });
        $("#delete-selected-product, #pause-selected-product, #run-selected-product").removeAttr('disabled');
    });


    $("#submit-reply-sys").click(function () {
        var replyContent = $("#reply-content").val();
        var sysId = $(this).data('sys-id');

        TTS.Framework.SysNotiAddReply(sysId, replyContent, function (resp) {
            if (resp.code == 1) {
                $("#reply-content").val('');
                $("#resp-status").text('Đã gửi phản hồi!').parent().show();
                $(".sys-reply-list > ul").prepend('<li> <strong class="name">Tôi</strong> <span class="resp-text">phản hồi</span> <span class="reply-content">' + replyContent + '</span> <span class="time">vừa gửi</span> </li>');
            } else {
                $("#resp-status").text('Gửi lên thất bại. Vui lòng thử lại!').parent().removeClass('alert-success').addClass('alert-danger').show();
            }

        });
    });

    $("#deselect-all").click(function () {
        $.each($("input[name=selected_product]:checked"), function () {
            $(this).attr('checked', false);
        });
        $("#delete-selected-product, #pause-selected-product, #run-selected-product").attr('disabled', 'disabled');
    });

    $("#delete-selected-product, #pause-selected-product, #run-selected-product").click(function () {
        var productlist = "";
        $.each($("input[name=selected_product]:checked"), function () {
            productlist += "pid[]=" + $(this).val() + '&';
        });
        if (productlist) {
            window.location = SITE_URL + "shop/" + $(this).data('action') + "/?" + productlist;
        } else {
            alert('Chưa chọn sản phẩm để xóa');
        }
    });

    $("#hour-autoup").change(function () {
        var hour = $(this).val();
        var current_hour = $(this).data('current-hour');
        var current_min = $("#minute-autoup").data('current-min');

        $("#minute-autoup").html('');

        if (hour == current_hour) {
            for (i = current_min; i <= 59; i++) {
                $("#minute-autoup").append('<option value="' + i + '">' + i + '</option>');
            }
        } else {
            for (i = 1; i <= 59; i++) {
                $("#minute-autoup").append('<option value="' + i + '">' + i + '</option>');
            }
        }
    });

    $("a[id=delete-queue]").click(function (e) {
        e.preventDefault();
        var productId = $(this).data('product-id');
        var queueId = $(this).data('queue-id');
        var element = $(this);
        if (productId && queueId) {
            $.post(SITE_URL + "shop/deletequeue", {
                'product_id': productId,
                'queue_id': queueId,
                '_token': CSRF_TOKEN
            }, function (resp) {
                $(element).parents('tr').fadeOut();
            });
        }
    });

    $("input[name=vip_period]").change(function () {
        $(".checkout-method").fadeIn();
    });

    $("input[name=checkout_method]").change(function () {
        var method = $(this).val();
        if (method == 1) {
            $("#transfer").hide();
            $("#office").hide();
            $("#thutien").fadeIn();
            $("#method_name").focus();
        } else if (method == 2) {
            $("#transfer").fadeIn();
            $("#office").hide();
            $("#thutien").hide();
        } else if (method == 3) {
            $("#office").fadeIn();
            $("#thutien").hide();
            $("#transfer").hide();
        }
        $(".method_list").fadeIn();
        $("#vip-do-register").removeAttr('disabled');
    });

    $("#vip-do-register").click(function () {
        var vip_period = $("input[name=vip_period]:checked").val();
        var checkout_method = $("input[name=checkout_method]:checked").val();
        var method_name = $("#method_name").val();
        var method_address = $("#method_address").val();
        var method_phone = $("#method_phone").val();
        var flag = true;
        if (checkout_method == 1) {
            if (!method_name || !method_address || !method_phone) {
                flag = false;
                alert('Vui lòng nhập đầy đủ thông tin người trả');
            }
        }

        if (flag != false) {
            $.post(SITE_URL + 'ajax/vippreregister', {
                'period': vip_period,
                'checkout_method': checkout_method,
                'method_name': method_name,
                'method_address': method_address,
                'method_phone': method_phone,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code == 1) {
                    $(".vip-register").html('<h2 style="color:white;">Chúc mừng bạn đã đăng ký thành công!<br />Chúng tôi sẽ có những thông tin sớm nhất về tính năng shop VIP đến bạn nếu có.<br />Mọi chi tiết thắc mắc vui lòng liên hệ email: support@thitruongsi.com hoặc số điện thoại 1900 6074 để được hỗ trợ<br />Xin cám ơn!</h2>');
                } else if (resp.code == 2) {
                    alert('Có lỗi xảy ra, vui lòng F5 lại trang và thử lại');
                } else if (resp.code == 3) {
                    alert('Bạn không phải là nhà cung cấp, vui lòng sử dụng tài khoản nhà cung cấp để đăng ký');
                }
            }, 'json');
        }

    });

    $("input[id=add-to-up]").click(function () {
        var product_schedule_count = $("input[id=add-to-up]:checked").length;
        if (product_schedule_count > UP_AVAIL) {
            alert('Vượt quá giới hạn số lượt UP mỗi ngày');
            $(this).attr('checked', false);
            product_schedule_count--;
        }
        if (product_schedule_count > 0) {
            $("#product-schedule-count").text(product_schedule_count);
            $("#do-schedule").removeAttr('disabled');
        } else {
            $("#product-schedule-count").text(0);
            $("#do-schedule").attr('disabled', 'disabled');
        }
    });

    $("#do-schedule").click(function (e) {
        e.preventDefault();
        var productlist = new Array();
        var hour = $("#hour-autoup").val();
        var minute = $("#minute-autoup").val();

        if (productlist && hour && minute) {
            $.each($("input[id=add-to-up]:checked"), function () {
                productlist.push($(this).val());
            });
            $.post(SITE_URL + 'ajax/addtoautoup', {
                'product': productlist,
                'hour': hour,
                'minute': minute,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code == 1) {
                    alert('Lên lịch UP thành công');
                    var queueCountCurrent = parseInt($("#autoupqueuecount").text());
                    $("#autoupqueuecount").text(queueCountCurrent + productlist.length);
                    ga('send', 'event', 'AutoUp', 'do', 'success');
                } else if (resp.code == 2) {
                    alert('Vượt quá giới hạn số lượt UP mỗi ngày');
                    ga('send', 'event', 'AutoUp', 'do', 'over-up-count');
                } else if (resp.code == 3) {
                    alert('Số lượt UP vượt quá giới hạn. Lượt UP còn lại để đặt lịch: ' + resp.count + ' lượt');
                    ga('send', 'event', 'AutoUp', 'do', 'semi-over-up-count');
                }
            }, 'json');
        } else {
            ga('send', 'event', 'AutoUp', 'do', 'failed');
            alert('Dữ liệu không hợp lệ');
        }
    });


    $('#pricefrom, #priceto').keydown(function (event) {
        if (event.keyCode == 13) {
            TTS.Framework.iFindDo();
        }
    })

    $("#ifind-do").click(function () {
        TTS.Framework.iFindDo();
    });


    $("a[id=ifind-choose-by]").on('click', function () {
        var key = $(this).attr('rel-key');
        var val = $(this).attr('val');
        var valText = $(this).text();
        if (key == 'category') {
            $.get(SITE_URL + 'ajax/getchildcategory?parent=' + val, function (resp) {
                if (resp.data) {
                    $("#child-category").val(resp.data[0].category_id);
                    $(".child-category").parents('.select').find('#textval').text(resp.data[0].category_name);
                }
                $(".child-category").html('<img id="ifind_category_loading" src="' + STATIC_URL + 'images/loading16.gif" />');
                $.each(resp.data, function (k, v) {
                    $(".child-category").append('<li class="' + (v.level == 2 ? 'primary-category' : '') + '"><a onclick="TTS.Framework.iFindSetVal(\'child-category\', ' + v.category_id + ', $(this));" id="ifind-choose-by" href="#" val="' + v.category_id + '" rel-key="child-category">' + v.category_name + '</a><li>');
                    $.each(v.child, function (a, b) {
                        $(".child-category").append('<li><a onclick="TTS.Framework.iFindSetVal(\'child-category\', ' + b.category_id + ', $(this));" id="ifind-choose-by" href="#" val="' + b.category_id + '" rel-key="child-category">' + b.category_name + '</a><li>');
                    });
                });
                $("img[id=ifind_category_loading]").hide();
            }, 'json');
        }
        TTS.Framework.iFindSetVal(key, val);
        $("ul[class=list-option]").hide();
        $(this).parents('.select').find('#textval').fadeOut(10).text(valText).fadeIn(10);
    });


    if (typeof(USER_LOGIN) == "undefined" && CURRENT_PAGE == "index" && !WAP) {
        $(window).scroll(function () {
            if ($(window).scrollTop() + $(window).height() > 500) {
                var stat_suplier = $("#stat-suplier").offset();

                $("#stat-register-retailer").css({
                    'top': stat_suplier.top + 20,
                    'left': stat_suplier.left - 40
                }).fadeIn(800);

                var stat_retailer = $("#stat-retailer").offset();

                $("#stat-register-suplier").css({
                    'top': stat_retailer.top + 20,
                    'left': stat_retailer.left - 40
                }).fadeIn(800);
            }
        });
    }

    $("a[id=wili]").click(function (e) {
        $("#loading-collections").show();
        $(".dropdown-menu").hide();
        $("#collection_item_id").val($(this).data("item-id"));
    });

    $(".select-collection").click(function () {
        $("#loading-collections").show();
        $(".products-collection-dropdown > .dropdown-menu").toggle();
        var c = $(".products-collections li").length;
        if (!c) {
            $.get(SITE_URL + "wili/getGroupList", function (resp) {
                $("#loading-collections").hide();
                if (resp.code == 1) {
                    $.each(resp.data, function (k, v) {
                        $(".products-collections").append('<li id="choose-collection" data-collection_id="' + v.id + '"><a href="#">' + v.name + '</a></li>');
                    });
                }
            }, 'json');
        } else {
            $("#loading-collections").hide();
        }
    });

    $(".products-select-collection-add-collection-name").keyup(function () {
        var name = $(this).val();
        if (name) {
            $(".products-select-collection-add-collection-button").removeAttr('disabled');
        } else {
            $(".products-select-collection-add-collection-button").attr('disabled', 'disabled');
        }
    });

    $(".products-select-collection-add-collection-button").click(function (e) {
        e.preventDefault();
        var name = $(".products-select-collection-add-collection-name").val();
        if (name) {
            $.post(SITE_URL + "wili/createGroup", {
                'groupname': name,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code == '1') {
                    $(".products-collections").prepend('<li id="choose-collection"  data-collection_id="' + resp.id + '"><a href="#">' + name + '</a></li>');
                    $(".products-select-collection-add-collection-name").val('');
                    $(".products-select-collection-add-collection-button").attr('disabled', 'disabled');
                    if (resp.id) {
                        $("#collection_id").val(resp.id);
                        $(".products-selected-collection-name").text(name);
                        $(".products-collection-dropdown > .dropdown-menu").toggle();
                    }
                    ga('send', 'event', 'Create Collection', 'create', 'success');
                } else if (resp.code == '2') {
                    var searchResult = $('li#choose-collection:contains("' + name + '")');
                    if (searchResult.length > 0) {
                        $("#collection_id").val($(searchResult).data('collection_id'));
                        $(".products-selected-collection-name").text(name);
                        $(".products-collection-dropdown > .dropdown-menu").toggle();
                    }
                    ga('send', 'event', 'Create Collection', 'create', 'failed');
                }
            }, 'json');
        }
    });

    $(".products-collections").on('click', 'li', function (e) {
        e.preventDefault();
        var collectionId = $(this).data('collection_id');
        var collectionName = $(this).text();
        if (collectionId) {
            $("#collection_id").val(collectionId);
            $(".products-selected-collection-name").text(collectionName);
            $(".products-collection-dropdown > .dropdown-menu").toggle();
        }
    });

    $(".dowili").click(function (e) {
        var collectionId = $("#collection_id").val();
        var itemId = $("#collection_item_id").val();
        if (itemId && collectionId) {
            $.post(SITE_URL + "wili/addWishList", {
                'productid': itemId,
                'groupid': collectionId,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code == 1 || resp.code == 2) {
                    $("#wiliModal").modal("hide");
                    $("#wili > .glyphicon").removeClass('glyphicon-plus-sign').addClass('glyphicon-ok-sign ok-color');

                    $(".add-success").show().fadeOut(500);

                    ga('send', 'event', 'Do Wishlist', 'click', 'success');
                } else {
                    alert("Lỗi rồi :(, vui lòng F5 lại trình duyệt và thử lại");
                }
            }, 'json');
        } else {
            alert("Lỗi rồi :(, vui lòng F5 lại trình duyệt và thử lại");
        }
    })

    $("a[id=up-button]").click(function (e) {
        e.preventDefault();
        var productId = $(this).attr("data-product-id");
        if (productId) {
            if (confirm('Bạn có thực sự muốn UP sản phẩm này?')) {
                $.get(SITE_URL + "ajax/up_product", {
                    'product_id': productId
                }, function (resp) {
                    if (resp.code == '1') {
                        ga('send', 'event', 'UpToTop', 'click', 'success');
                        alert('Đã UP sản phẩm này!');
                    } else if (resp.code == '3') {
                        if (resp.time_require > 60) {
                            resp.time_require = Math.floor(resp.time_require / 60) + ' phút';

                        } else {
                            resp.time_require = resp.time_require + ' giây';
                        }
                        ga('send', 'event', 'UpToTop', 'click', 'wait');
                        alert('Bạn cần phải đợi thêm ' + resp.time_require + ' để UP sản phẩm');
                    } else if (resp.code == '2') {
                        alert('Có lỗi xảy ra, vui lòng thử lại');
                        ga('send', 'event', 'UpToTop', 'click', 'error');
                    } else if (resp.code == '4') {
                        ga('send', 'event', 'UpToTop', 'click', 'over');
                        alert('Bạn đã hết lượt UP sản phẩm, hãy chờ sang ngày mai!');
                    }
                }, "json");
            }
        } else {
            alert('Lỗi, vui lòng thử lại');
        }
    });

    $("#remove-cover").click(function () {
        $.get(SITE_URL + 'ajax/removeshopcover', function () {
            $("#cover-photo").attr('src', STATIC_URL + 'images/nocover.png');
            alert('Đã xóa cover!');
        });
    });

    $("input[id=liabilitiesSelect]").click(function () {
        var answer = $(this).attr('data');
        $.post(SITE_URL + 'ajax/liabilitiesAnswer', {
            'answer': answer,
            '_token': CSRF_TOKEN
        }, function () {
            $("#shopLiabilitiesModal").modal("hide");
        });
    });


    $(document).on('click', function () {
        $(".notification-center").hide();
        $('#navbar-shop-control').hide();
        $('#navbar-user-control').hide();
    });
    $("#notification-bell").click(function (e) {
        e.stopPropagation();
        $('#navbar-shop-control').hide();
        $('#navbar-user-control').hide();
        $.get(SITE_URL + 'ajax/clearUnread', function (resp) {
        });
        $(".notification-count").removeClass('show');
        $(".notification-center").toggle();

    });

    $(".navbar-shop").click(function (e) {
        e.stopPropagation();
        $(".notification-center").hide();
        $('#navbar-user-control').hide();
        $("#navbar-shop-control").toggle();

    });

    $(".usHover").click(function (e) {
        e.stopPropagation();
        $(".notification-center").hide();
        $('#navbar-shop-control').hide();
        $("#navbar-user-control").toggle();

    });

    $("#filter-choose-shop-city").click(function (e) {
        e.preventDefault();
        var catid = $(this).attr('data-cat-id');
        var sort = $(this).attr('data-sort');
        var priceto = $(this).attr('data-price-to');
        var pricefrom = $(this).attr('data-price-from');
        var filterBy = $(this).attr('filter-by');
        var filterVip = $(this).data('fiter-vip');
        var list = "";
        var searchQuery = $(this).data('search-query');
        $("#filterShopCityModal").modal({
            keyboard: true
        });
        $("#filterShopCityModal").modal("show");
        $("#filterShopCityModal .modal-body").html('<img src="' + STATIC_URL + 'images/loading16.gif" /> Đang tải danh sách...');
        $.get(SITE_URL + "ajax/getShopCityList", {
            "catid": catid,
            "sort": sort,
            "pricefrom": pricefrom,
            "priceto": priceto,
            "filterby": filterBy,
            "filtervip": filterVip,
            "q": searchQuery
        }, function (resp) {
            list += '<ul class="filter-list-shop-city">';
            $.each(resp.data, function (k, v) {
                list += '<li><a href="' + v.href + '">' + v.name + (v.count > 0 ? ' (' + v.count + ')' : '') + '</a></li>';
            });
            list += '</ul><div class="clear"></div>';
            if (list) {
                $("#filterShopCityModal .modal-body").html(list);
            }
        }, "json");
    });

    $("#pricefrom").keyup(function () {
        var pricefrom = $(this).val();
        if ($.isNumeric(pricefrom)) {
            $("#submit-filter-price").removeAttr("disabled");
            $("#filter-price-status").hide();
        } else {
            $(this).val(0);
            $("#filter-price-status").text("Giá không hợp lệ").show();
            $("#submit-filter-price").attr("disabled", "disabled");
        }
    });

    $("#priceto").keyup(function () {
        var pricefrom = $("#pricefrom").val();

        if (!pricefrom) {
            $("#pricefrom").val(0);
        }

        var priceto = $(this).val();
        if (notEmpty(priceto)) {
            if ($.isNumeric(priceto) && $.isNumeric(pricefrom)) {
                console.log(pricefrom);
                console.log(priceto);
                priceto = parseInt(priceto);
                pricefrom = parseInt(pricefrom);
                if (priceto < pricefrom) {
                    console.log("a");
                    $("#filter-price-status").text("Giá không hợp lệ").show();

                    $("#submit-filter-price").attr("disabled", "disabled");
                } else {
                    //ok
                    $("#submit-filter-price").removeAttr("disabled");
                    $("#filter-price-status").hide();
                }
            } else {
                $("#filter-price-status").text("Giá không hợp lệ").show();

                $("#submit-filter-price").attr("disabled", "disabled");
            }
        } else {
            $("#submit-filter-price").removeAttr("disabled");
            $("#filter-price-status").hide();
        }
    });


    $("#filter-price").click(function (e) {
        e.preventDefault();
        $("#filterPriceModal").modal({
            keyboard: true
        });
        $("#filterPriceModal").modal("show");

    });

    $("#retailer-phone-submit").click(function (e) {
        var phone = $("#retailerphone").val();
        if (notEmpty(phone)) {
            $.post(SITE_URL + "ajax/setretailerphone", {
                'phone': phone,
                '_token': CSRF_TOKEN
            }, function (resp) {
                $("#retailerPhoneZeroModal").modal("hide");
            });
        } else {
            alert("Vui lòng nhập số điện thoại");
        }
    });


    $("#retailer-address-submit").click(function (e) {
        e.preventDefault();
        var address = $("input[name=retailer_address]").val();
        var city = $("select[name=retailer_city]").val();
        var district = $("select[name=retailer_district]").val();
        if (notEmpty(address) && notEmpty(city) && notEmpty(district)) {
            $.post(SITE_URL + "ajax/setretaileraddress", {
                'address': address,
                'city': city,
                'district': district,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code == '1') {
                    $("#retailerAddressModal").modal("hide");
                    ga('send', 'event', 'RetailerAddressSubmit', 'input', address);
                } else {
                    alert("Vui lòng nhập đầy đủ thông tin");
                }
            }, 'json');
        } else {
            alert("Vui lòng nhập đầy đủ thông tin");
        }
    });


    $("a[rel=dovote]").click(function (e) {
        e.preventDefault();

        if (typeof(USER_LOGIN) == "undefined") {
            alert("Vui lòng đăng nhập để sử dụng chức năng này");
        } else {

            var voteAction = $(this).attr("data-action");
            var objId = $(this).attr("data-id");

            $(this).toggleClass("voted");

            TTS.Framework.Vote(objId, voteAction, function (resp) {
                if (resp.code == 1 || resp.code == 2) {
                    var totalVote = resp.data.good + resp.data.bad;
                    var goodPercent = resp.data.good / totalVote * 100;
                    var badPercent = resp.data.bad / totalVote * 100;
                    $("#goodpercent").css("width", goodPercent + "%");
                    $("#badpercent").css("width", badPercent + "%");

                    $("span[rel=good-num]").text(resp.data.good);
                    $("span[rel=bad-num]").text(resp.data.bad);
                    $("#stat-area").show();
                    $("#vote-stat-none").hide();

                    if (voteAction == '1') {
                        $(".vote-action .glyphicon-thumbs-up, .vote-action .glyphicon-remove").toggleClass("glyphicon-remove").toggleClass("glyphicon-thumbs-up");
                    }

                    if (voteAction == '2') {
                        $(".vote-action .glyphicon-thumbs-down, .vote-action .glyphicon-remove").toggleClass("glyphicon-remove").toggleClass("glyphicon-thumbs-down");
                    }
                } else {
                    alert("Có lỗi xảy ra, vui lòng thử lại");
                }
            });
        }

        ga('send', 'event', 'Vote', $(this).attr("data-action"), $(this).attr("data-id"));

    });

    $("a[id=survey-choose]").click(function (e) {
        e.preventDefault();
        var thisElement = $(this);
        var surveyId = $(this).attr('data-id');
        var choose = $(this).attr('data-choose');
        var modalElement = $(this).parents(".modal");
        var surveyDisplayType = $(this).data("survey-display-type");
        if (notEmpty(surveyId) && notEmpty(choose)) {
            TTS.Framework.Survey(surveyId, choose, function () {

                if (typeof(surveyDisplayType) != "undefined" && surveyDisplayType == "inpage") {
                    thisElement.parents(".panel-body").html("Cám ơn bạn đã tham gia khảo sát!");
                } else {

                    $(modalElement).find(".modal-body").html("Cám ơn bạn đã tham gia khảo sát thông tin!");

                    $("#survey-action").fadeOut(300);
                    $("#survey-success").fadeIn(300);

                    setTimeout(function () {
                        $(modalElement).modal("hide");
                    }, 3000);

                }
            });
        }
    });


    $("li[rel=follow-category]").click(function (e) {
        var topic = $(this).find(".topic");
        topic.toggleClass("deselected");


        if (!topic.hasClass("deselected")) {
            var action = "follow";
        } else {
            var action = "unfollow";
        }

        $.post(SITE_URL + 'follow/' + action, {
            'followid': topic.attr("data-id"),
            'type': 3,
            '_token': CSRF_TOKEN
        }, function (resp) {
        }, 'json');
        ga('send', 'event', 'Follow', action, topic.attr("data-id"));
    });

    $("#follow-cat-done").click(function () {
        var selectedCategory = $(".topic:not(.deselected)");
        var selected = new Array();

        if (selectedCategory.length > 0) {
            $.post(SITE_URL + "ajax/gettingstarted", {
                'id': 'gt_follow_category',
                '_token': CSRF_TOKEN
            }, function (resp) {

            }, "json");
            $("#followcategorymodal").modal("hide");
        } else {
            alert("Vui lòng chọn ít nhất 1 danh mục để quan tâm");
        }
    });

    $('a[rel=new-list]').click(function (e) {
        e.preventDefault();
        var catId = $(this).attr('data-id');
        $("a[id=new-more-link]").attr('href', $(this).attr('href'));
        $("#loading_new_list").show();
        if (catId) {
            $.get(SITE_URL + 'ajax/getProductListByCat', {
                'sort': 'new',
                'id': catId
            }, function (resp) {
                setTimeout(function () {
                    $("#loading_new_list").hide();
                }, 1000);
                $("#new-list-content").html("");
                $.each(resp.data, function (k, v) {
                    if (typeof(USER_LOGIN) == "undefined") {
                        v.product_price = "Đăng nhập để xem giá";
                    }
                    $("#new-list-content").append('<li class="product-item"><div class="item"> <a href="' + v.product_url + '"><img alt="' + v.product_name + '" src="' + v.product_image + '"></a> <h2><a href="' + v.product_url + '">' + v.product_name + '</a></h2> <p>' + v.product_price + '</p> <div class="product-more-info"> <div class="pull-left"> <span class="shop-name"><a href="' + v.shop.url + '">' + v.shop.name + '</a></span> <span class="shop-address">' + v.province_text + '</span> </div> <div class="shop-chat pull-right"><a href="http://message.thitruongsi.com/' + v.shop.user_code + '" target="_blank"><span class="glyphicon glyphicon-comment"></span> Chat</a></div> </div> </div></li>');
                });
            }, 'json');
        }
    });

    $('#report-form').on('submit', function (e) {
        e.preventDefault();
        var reportType = $("input:radio[name=report_type]:checked").val();
        var refId = $(this).attr("data-id");
        var refUserId = $(this).attr("data-ref-id");
        if (reportType && refId && refUserId) {
            $.post(SITE_URL + 'ajax/report', {
                'refid': refId,
                'type': reportType,
                'refuser': refUserId,
                '_token': CSRF_TOKEN
            }, function (resp) {
                $("#reportModal").modal("hide");
            }, 'json');
        }
    });

    $('#viewlistfollower').click(function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var type = $(this).attr('data-type');

        $.get(SITE_URL + 'ajax/getFollower', {
            'id': id,
            'type': type
        }, function (resp) {
            if (resp.code == 1) {
                $("#followerlist").html("");
                $.each(resp.data, function (k, v) {
                    if (v.first_name == '') v.first_name = v.email;
                    $("#followerlist").append('<li class="item"><div class="pull-left"><a class="followname" href="' + SITE_URL + 'u/' + v.user_code + '">' + v.first_name + ' ' + v.last_name + '</a></div><div class="pull-right"><a target="_blank" href="http://message.thitruongsi.com/' + v.user_code + '" class="btn btn-primary btn-small">Chat ngay</a></div></li>');
                });
                $("#followerModal").modal({
                    keyboard: true
                });
                $("#followerModal").modal("show");
                TTS.Framework.loadMoreDiv('follower');
            }
        }, 'json');
    });

    $('#savename').click(function () {
        var firstname = $("#firstname").val();
        var lastname = $("#lastname").val();

        if (notEmpty(firstname) && notEmpty(lastname)) {
            $.post(SITE_URL + 'ajax/setUserName', {
                'firstname': firstname,
                'lastname': lastname,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp == '1') {
                    $("#setnamemodal").modal("hide");
                }
            });
        } else {
            alert('Chưa điền đủ thông tin');
        }
    });

    $('.category-keyword').each(function () {
        var plugin = $(this);
        var list = $('.category-keyword-list', plugin);
        var maxHeight = 22;

        if (list.height() > maxHeight) {
            list.originHeight = plugin.height();
            list.height(maxHeight);
            var controls = $('.category-keyword-control');
            controls.show();
            $('.expand', controls).click(function () {
                if ($(this).hasClass('collapse')) { // Thu gọn
                    $(this).removeClass('collapse');
                    list.height(maxHeight);
                } else {
                    $(this).addClass('collapse');
                    list.height(list.originHeight);
                }
            });
        }
    });

    $('.product-desc').each(function () {
        var plugin = $(this);
        var list = $('.product-desc-content', plugin);
        var maxHeight = 100;

        if (list.height() > maxHeight) {
            list.originHeight = plugin.height();
            list.height(maxHeight);

            var controls = $('.product-desc-expand');
            controls.show();
            $('.expand', controls).click(function (e) {
                e.preventDefault();
                if ($(this).hasClass('collapse')) { // Thu gọn
                    $(this).removeClass('collapse');
                    list.height(maxHeight);
                } else {
                    $(this).addClass('collapse');
                    list.height(list.originHeight);
                }
            });
        }
    });


    $("select[id=productstatus]").change(function (e) {
        e.preventDefault();
        var element = $(this);
        var productId = $(this).attr('data-id');
        var productStatus = $(this).val();

        if (productId && productStatus) {
            $.post(SITE_URL + 'ajax/changeProductStatus', {
                'pid': productId,
                'status': productStatus,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code == 1) {
                    $("#product-enable-count").text(resp.productenable);
                    showAlert('Đã thay đổi tình trạng sản phẩm', 1);
                } else if (resp.code == 2) {
                    element.val(0);
                    showAlert('Số lượng sản phẩm đang chạy đã đến giới hạn. Vui lòng tạm ngưng hoặc xóa bỏ những sản phẩm hết hàng hoặc không hiệu quả để chạy sản phẩm này.<br />Hãy gọi đến 1900 6074 để được hỗ trợ tăng giới hạn nếu cần thiết.', 0, true);
                }
            }, 'json');
        }
    });

    $("select[id=product_availability]").change(function (e) {
        e.preventDefault();
        var element = $(this);
        var productId = $(this).data('id');
        var productAvailability = $(this).val();
        if (productId && productAvailability) {
            $.post(SITE_URL + 'ajax/changeProductAvailability', {
                'pid': productId,
                'status': productAvailability,
                '_token': CSRF_TOKEN
            }, function (resp) {
                if (resp.code != 1) {
                    alert('Có lỗi :(, vui lòng F5 lại trang');
                }
            }, 'json');
        }
    });


    $(".categorylist > .catitem").mouseenter(function (e) {
        $(this).find('.sub-cate').show();
    }).mouseleave(function () {
        $(this).find('.sub-cate').hide();
    });

    $(".category-menu").mouseenter(function (e) {
        $(this).find('.navbar-category').show();
    }).mouseleave(function () {
        $(this).find('.navbar-category').hide();
    });

    $("a[rel=viewImage]").mouseover(function (e) {
        var imgElement = $(".bigimg > img");
        $(".ui-image-viewer-loading").show();
        $(".image-nav-item.current").removeClass('current');
        $(this).parent().parent().addClass('current');
        $(imgElement).attr("src", $(this).attr('data-src'));
        $(imgElement).data("zoom-image", $(this).data('src-original'));
        if (notEmpty($(this).attr('data-desc'))) {
            $(".bigimg > .desc").text($(this).attr('data-desc')).show();
        } else {
            $(".bigimg > .desc").hide();
        }

        new imagesLoaded($(imgElement), function () {
            $(".ui-image-viewer-loading").hide();
        });

        $("#zoom_image").elevateZoom({
            cursor: "crosshair",
            zoomWindowFadeIn: 100,
            zoomWindowFadeOut: 100,
            easing: true
        });

        e.preventDefault();
    });

    $("#search-button").click(function (e) {
        var keyword = $('.searchinputcontainer').val();
        if (notEmpty(keyword)) {
            $(this).parent().submit();
        } else {
            alert('Bạn chưa nhập từ khóa');
        }
        e.preventDefault();
    });

    $("#searchform").submit(function (e) {
        var keyword = $('.searchinputcontainer').val();
        if (!notEmpty(keyword)) {
            alert('Bạn chưa nhập từ khóa');
            e.preventDefault();
        }
    });

    //WILI MODULE
    $("a[id=wili-button]").click(function (e) {
        $('.addtudo').show();
        $('div[id=wili-modal-alert]').hide();
        product_id = $(this).attr("data-id");
        TTS.Closet.GetClosetList();
        console.log('a');
        e.preventDefault();
    });

    $("#addToCloset").click(function (e) {
        TTS.Wili.DoWili($(this).attr("data-id"), $("#selected_closet").val());
        e.preventDefault();
    });

    $("a[id=wili-button]").click(function (e) {
        image_src = $(this).attr('data-img-src');
        product_name = $(this).attr('data-product-name');
        product_price = $(this).attr('data-price');
        product_id = $(this).attr('data-id');
        $("#item-image").attr('src', image_src);
        $("#wili_product_name").text(product_name);
        $("#product_price").text(product_price);
        $("#addToCloset").attr('data-id', product_id);
    });


    //CLOSET MODULE
    $("#addcloset").submit(function (e) {
        closetName = $("#addcloset_name").val();
        //if closet is exists
        $.get(SITE_URL + "closet/searchCloset", {
            'name': closetName
        }, function (resp) {
            if (resp.code == 1) {
                $("#addcloset_name").val('');
                TTS.Closet.SelectCloset(resp.data[0]);
            } else if (resp.code == 0) {
                //not exists
                TTS.Closet.AddCloset(closetName, true);

            }
        }, 'json');

        e.preventDefault();
    });


    //PURCHASE MODULE
    $('#selectcity').change(function () {
        $("#district").attr('disabled', 'disabled');
        TTS.Framework.District($(this).val());
    });

    //FOLLOW MODULE
    $("body").on('click', '.theodoi[rel=follow]', function (e) {
        e.preventDefault();
        TTS.Framework.Follow($(this));
    });

    //SHOP CATEGORY USING
    $("#shopcategory > li > a[id=all]").click(function (e) {
        $("ul[id=all]").fadeIn();
        $("ul[id=male], ul[id=female]").hide();


        $(this).parent().addClass('active');
        $("#shopcategory > li > a[id=male], #shopcategory > li > a[id=female]").parent().removeClass('active');
        e.preventDefault();
    });

    $("#shopcategory > li > a[id=male]").click(function (e) {
        $("ul[id=male]").fadeIn();
        $("ul[id=all], ul[id=female]").hide();

        $(this).parent().addClass('active');
        $("#shopcategory > li > a[id=all], #shopcategory > li > a[id=female]").parent().removeClass('active');

        e.preventDefault();
    });

    $("#shopcategory > li > a[id=female]").click(function (e) {
        $("ul[id=female]").fadeIn();
        $("ul[id=all], ul[id=male]").hide();

        $(this).parent().addClass('active');

        $("#shopcategory > li > a[id=male], #shopcategory > li > a[id=all]").parent().removeClass('active');

        e.preventDefault();
    });


    $('#datepicker, #datepicker2').datepicker({
        'format': 'dd/mm/yyyy'
    });

    $('#newMessageButton').click(function (e) {
        e.preventDefault();
        $('#message-list, #message-thread-detail').hide();
        TTS.Framework.Message.RenderNewMessage();
    });

    $('#newmessageform').submit(function (e) {
        e.preventDefault();
        messageBody = $('#messagebody').val();
        toId = $('#messagetoid').val();
        threadId = $('#messagethreadid').val();

        if (notEmpty(messageBody) && notEmpty(toId)) {
            $('#messagebody').val('');
            $('#ms-send-bt').attr('disabled', 'disabled');
            $('#message-body > ul').append('<li><img class="avatar" width="32" height="32" src="' + AVATAR + '" /> <span class="username"><a href="#">' + NAME + '</a></span>: <span class="message">' + messageBody + '</span></li>');
            TTS.Framework.Message.doNewMessage(toId, messageBody, threadId);
        }
    });

    $('#messagebody').keyup(function () {
        if (notEmpty($(this).val())) {
            $('#ms-send-bt').removeAttr('disabled');
        } else {
            $('#ms-send-bt').attr('disabled', 'disabled');
        }
    });

    $('.mes').click(function (e) {
        TTS.Framework.Message.LoadListMessage();
        e.preventDefault();
    });

    $('#message-index').click(function (e) {
        $('#newmessage-form, #message-thread-detail').hide();
        $('#message-list').show();
    });

    $("#add-image-by-url").click(function (e) {
        e.preventDefault();
        $(".uploadbyurl").show();
    });

    $(".do-add-image").click(function (e) {
        e.preventDefault();
        var imageUrl = $("#imageurl").val();
        if (notEmpty(imageUrl)) {
            $("#imageurl, .do-add-image").attr('disabled', 'disabled');
            TTS.Framework.GrabImage.Grab(imageUrl);
        } else {
            alert('Bạn chưa nhập URL hình ảnh.\nURL hình ảnh có dạng: http://thitruongsi.com/vidu.jpg');
            $("#imageurl").focus();
        }
    });

    $(".choosecategory").click(function (e) {
        e.preventDefault();
        var categoryId = $(this).attr('category-id');
        $("a.choosecategory").removeAttr('style');
        $(this).css('font-weight', 'bold');

        //showcategory
        $("div.category-list").hide();
        $(".category-list[category-id='" + categoryId + "']").show();

    });


    //RETAILER
    $("a[class=add-more]").click(function (e) {
        e.preventDefault();
        $(this).parent().find('#clone-temp').clone().removeAttr('id').appendTo($(this).parent().find('.more-area')).focus();
    });

    $("select[id=approve-action]").change(function (e) {
        e.preventDefault();
        var retailerId = $(this).attr('data-id');
        var status = $(this).val();

        TTS.Framework.Retailer.Approve(retailerId, status);
        if (status == 1) {
            $(this).parent().parent().addClass('success');
        } else {
            $(this).parent().parent().removeClass('success');
        }
    });

    $("input[id=productprice]").each(function () {
        if ($(this).val() != '') {
            TTS.Framework.formatNumberVND($(this));
        }
    });

    $("input[id=productprice]").keydown(function (e) {
        handleKeyDown(e);
    }).keyup(function (e) {
        handleKeyUp(e);
        if (!ignoreEvent(e)) TTS.Framework.formatNumberVND($(this));
    });

    //register
    $("#email").change(function () {

        if ($("#shop_email").val() == '') {
            $("#shop_email").val($(this).val());
        }

        if ($("#contacter_email").val() == '') {
            $("#contacter_email").val($(this).val());
        }
    });

    //product
    $("#delete_product").click(function () {
        var productId = $(this).attr('data-id');
        var dataAction = $(this).attr('data-action');

        if (dataAction == 'delete') {
            $.get(SITE_URL + 'ajax/deleteproduct', {
                'productId': productId
            }, function (resp) {
                if (resp.code == '1') {
                    $("#productdetail").css('opacity', "0.3");
                    $("#delete_product").html('<i class="glyphicon glyphicon-repeat"></i> Khôi phục lại').attr('data-action', 'restore');
                } else if (resp.code == '9') {
                    alert('Bạn không có quyền xóa sản phẩm này');
                }
            }, 'json');
        } else {
            $.get(SITE_URL + 'ajax/restoreproduct', {
                'productId': productId
            }, function (resp) {
                if (resp.code == '1') {
                    $("#productdetail").css('opacity', "1");
                    $("#delete_product").html('<i class="glyphicon glyphicon-remove"></i> Xóa sản phẩm này').attr('data-action', 'delete');
                } else if (resp.code == '9') {
                    alert('Bạn không có quyền khôi phục sản phẩm này');
                }
            }, 'json');
        }
    });

    $("#edit_product").click(function (e) {
        e.preventDefault();
        $("#productname, p#productpriceedit").attr('contenteditable', 'true');
    });

    $("p#productpriceedit").keyup(function (e) {
        alert($(this).text());
    });
});

TTS.Framework.Follow = function (followElement) {

    var shopId = followElement.attr('data-id');
    var followType = followElement.attr('data-type');
    var action = followElement.attr('data-action');

    followElement.find('.glyphicon').removeClass(function (index, css) {
        return (css.match(/\bglyphicon-\S+/g) || []).join(' ');
    }).append('<img src="' + STATIC_URL + 'images/loading16.gif" style="width: 12px; position:relative; top: -2px" />');

    $.post(SITE_URL + 'follow/' + action, {
        'followid': shopId,
        'type': followType,
        '_token': CSRF_TOKEN
    }, function (resp) {
        if (resp.code == '1') {

            if (action == 'follow') {
                if (followType == 4) {
                    buttonText = '<i class="glyphicon glyphicon-remove"></i> Bỏ đã liên hệ';
                } else {
                    buttonText = '<i class="glyphicon glyphicon-remove"></i> Bỏ theo dõi';
                    $(".count[rel=follow]").text(parseInt($(".count[rel=follow]").text()) + 1);
                }
                data_action = 'unfollow';


                if (followType == 3) {
                    $("#follow-response").html("Cám ơn bạn đã quan tâm.");
                    setTimeout(function () {
                        $("div[rel=follow-box]").fadeOut();
                    }, 3000);
                }

            } else if (action == 'unfollow') {
                if (followType == 4) {
                    buttonText = '<i class="glyphicon glyphicon-ok"></i> Tôi đã liên hệ';
                } else {
                    buttonText = '<i class="glyphicon glyphicon-heart"></i> Theo dõi';
                    $(".count[rel=follow]").text(parseInt($(".count[rel=follow]").text()) - 1);
                }
                data_action = 'follow';
            } else if (action == 'nofollow') {
                $("div[rel=follow-box]").fadeOut();
            }
            $(followElement).attr('data-action', data_action).html(buttonText);
            if (followType != 4) {
                $("a[id=follow-product-page]").attr('data-action', data_action).html(buttonText);
            }
        }

    }, 'json');
    ga('send', 'event', 'Follow', action, shopId);
}


TTS.Framework.iFindSetVal = function (k, v, t) {
    $("input[id=" + k + "]").val(v);
    if (typeof(t) != 'undefined') {
        var valText = t.text();
        t.parents('.select').find('#textval').fadeOut(10).text(valText).fadeIn(10);
    }
    $("#pricefrom").focus();
};

TTS.Framework.iFindDo = function () {
    var searchBy = $("#searchby").val();
    var childCat = $("#child-category").val();
    var cat = $("#category").val();
    var priceFrom = $("#pricefrom").val();
    var priceTo = $("#priceto").val();
    var location = $("#location").val();

    if (!childCat) {
        childCat = cat;
    }

    if (searchBy == 'suplier') {
        window.location = SITE_URL + "shop/shoplist?pricefrom=" + priceFrom + "&priceto=" + priceTo + "&c=" + childCat + (location == 'toan-quoc' ? '&do=clearfilter' : '&sc=' + location);
    } else {
        window.location = SITE_URL + "category/" + childCat + "-filter.html?pricefrom=" + priceFrom + "&priceto=" + priceTo + (location == 'toan-quoc' ? '&do=clearfilter' : '&sc=' + location);
    }
    ga('send', 'event', 'IFind', 'dofind', searchBy);
}


TTS.Framework.loadMoreDiv = function (page) {
    $(".followerlist").scroll(function () {
        if ($(".followerlist").scrollTop() + $(".followerlist").height() >= $(".followerlist").height() - 50) {

            var currentpage = $('#currentpage').val();
            var sortby = $('#sortby').val();
            var nextpage = parseInt(currentpage) + 1;

            if (TTS.Framework.ListItem.isLoading || TTS.Framework.ListItem.hasNoMore) return;
            TTS.Framework.ListItem.isLoading = true;
            $(".loading").show();
            if (page == 'follower') {
                var followerId = $('#follow-shop-id').val();
                if (followerId) {
                    request_url = SITE_URL + 'ajax/getFollower?type=2&id=' + followerId + '&p=' + nextpage;
                }
            }
            $.ajax({
                url: request_url,
                dataType: "json",
                success: function (b) {
                    if (b.status == 0 || b.code == 0) {
                        TTS.Framework.ListItem.hasNoMore = true;
                    } else {
                        $.each(b.data, function (key, val) {
                            if (page == 'follower') {
                                if (val.first_name == '') val.first_name = val.email;
                                $("#followerlist").append('<li class="item"><div class="pull-left"><a class="followname" href="' + SITE_URL + 'u/' + val.user_code + '">' + val.first_name + ' ' + val.last_name + '</a></div><div class="pull-right"><a target="_blank" href="http://message.thitruongsi.com/' + val.user_code + '" class="btn btn-primary btn-small">Chat với người này</a></div></li>');
                            }
                        });
                        $('#currentpage').val(nextpage);
                    }
                },
                error: function () {
                },
                complete: function () {
                    TTS.Framework.ListItem.isLoading = false;
                    $(".loading").hide();
                }
            });
        }
    });
};

TTS.Framework.loadMore = function (page) {
    $(window).scroll(function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
            var currentpage = $('#currentpage').val();
            var sortby = $('#sortby').val();
            var nextpage = parseInt(currentpage) + 1;

            if (TTS.Framework.ListItem.isLoading || TTS.Framework.ListItem.hasNoMore) return;
            TTS.Framework.ListItem.isLoading = true;
            $(".loading").show();

            if (page == 'newsfeed' || page == 'newsfeed.mobile') {
                request_url = SITE_URL + 'newsfeed/?ajax=1&p=' + nextpage;
            }
            $.ajax({
                url: request_url,
                dataType: "json",
                success: function (b) {
                    if (b.status == 0 || b.code == 0) {
                        TTS.Framework.ListItem.hasNoMore = true;
                    } else {
                        $.each(b.data, function (key, val) {
                            if (page == 'newsfeed') {
                                console.log(val);
                                $("#feed-items").append('<li data-product-id="' + val.objectdata.product_id + '" data-owner-userid="' + val.objectdata.shop.user_id + '" class="product-item"><div class="item"> <a href="' + val.objectdata.url + '"><img alt="' + val.objectdata.product_name + '" src="' + val.objectdata.product_image + '"></a> <h2><a href="' + val.objectdata.url + '">' + val.objectdata.product_name + '</a></h2> <p>' + val.objectdata.product_price + '</p> <div class="product-more-info"> <div class="pull-left"> <span class="shop-name">' + (val.objectdata.shop.vip == 1 ? '<img class="vip-icon" src="' + STATIC_URL + 'images/vip.png?v=2" />' : '') + '<a href="' + val.objectdata.shop.url + '">' + val.objectdata.shop.name + '</a></span> <span class="shop-address">' + val.objectdata.province_text + '</span> </div> <div class="shop-chat pull-right"> <a href="http://message.thitruongsi.com/' + val.objectdata.shop.user_code + '" target="_blank"><span class="glyphicon glyphicon-comment ' + (val.objectdata.shop.online ? 'online' : '') + '"></span> Chat</a> </div> <div class="time-ago">' + val.objectdata.timeago + '</div> </div> </div></li>');
                            } else if (page == 'newsfeed.mobile') {
                                $("#feed-items").append('<div class="product-item col-xs-4 col-sm-4 col-md-3"> <a href="' + val.objectdata.url + '"><img alt="' + val.objectdata.product_name + '" src="' + val.objectdata.product_image + '"/></a> <div class="product-name"><a href="' + val.objectdata.url + '">' + val.objectdata.product_name + '</a></div> <div class="product-price">' + val.objectdata.product_price + '</div> </div>');
                            }
                        });
                        $('#currentpage').val(nextpage);
                    }
                },
                error: function () {
                },
                complete: function () {
                    TTS.Framework.ListItem.isLoading = false;
                    $(".loading").hide();
                }
            });
        }
    });
};

TTS.Framework.SysNotiAddReply = function (sysid, content, callback) {
    if (sysid && content) {
        $.post(SITE_URL + "ajax/addSysReply", {
            'sysid': sysid,
            'content': content,
            '_token': CSRF_TOKEN
        }, function (resp) {
            callback(resp);
        }, 'json');
    }
}

TTS.Framework.MessageSetOnline = function () {
    ttsMessageSocketIO.emit('restartOnline', USER_ID);
    //$.get("http://message.thitruongsi.com/online/setTimeOnline?t=" + new Date().getTime() + '&sid=' + SESSION_ID, function(resp) {});
    try {
        $.get("http://online.api.thitruongsi.com:7070/?ssid=" + SESSION_ID);
        setTimeout(function () {
            TTS.Framework.MessageSetOnline();
        }, 20000);
    } catch (err) {
        console.log(err);
    }
}

TTS.Framework.Vote = function (objId, action, callback) {
    $.post(SITE_URL + 'ajax/vote', {
        'id': objId,
        'action': action,
        '_token': CSRF_TOKEN
    }, function (resp) {
        callback(resp);
    }, 'json');
}

TTS.Framework.Survey = function (surveyId, choose, callback) {
    $.post(SITE_URL + 'ajax/survey', {
        'id': surveyId,
        'choose': choose,
        '_token': CSRF_TOKEN
    }, function (resp) {
        callback();
    });
}

TTS.Framework.ChatUserOnlineStatus = function (userid) {
    $.get('http://online.api.thitruongsi.com:7070/ajax/isOnline', {
        'userid': userid,
        't': new Date().getTime()
    }, function (resp) {
        if (resp == '1') {
            $("#user-online-status").removeClass("offline");
            $("#user-online-status").addClass("online");
        } else {
            $("#user-online-status").removeClass("online");
            $("#user-online-status").addClass("offline");
        }
    });

    setTimeout(function () {
        TTS.Framework.ChatUserOnlineStatus(userid);
    }, 10000);
}


TTS.Framework.ChatNotification = function () {
    ttsMessageSocketIO.emit('initSubcribe', USER_ID);
    ttsMessageSocketIO.emit('getListNewMessage', USER_CODE);
    ttsMessageSocketIO.on('responseNewMessage', function (rawResp) {
        var resp = JSON.parse(rawResp);

        if (resp.code == "1") {
            var countNewMessage = resp.count;
            if (countNewMessage > 0) {
                $("#newmessagecount, .notificationbar").show();
                $("#newmessagecount > #count, .notificationbar > #count").text(countNewMessage);
                $("#message-with-userid").attr('href', 'http://message.thitruongsi.com/' + resp.list[0]);
                document.title = "(" + countNewMessage + " tin nhắn) " + DOCUMENT_TITLE;
            } else {
                $("#newmessagecount, .notificationbar").hide();
                document.title = DOCUMENT_TITLE;
            }
        }
    });
}

TTS.Framework.TopInHour = function () {
    $.get(SITE_URL + 'ajax/topproductinhour', function (resp) {
        var exists = false;

        $.each($("li[rel=topinhouritem]"), function (k, v) {
            var id = $(v).attr('data-id');
            if (resp.data.product_id == id) {
                exists = true;
            }
        });

        if (exists == false) {
            if (USER_LOGIN) {
                var price = resp.data.product_price;
            } else {
                var price = 'Đăng nhập để xem giá';
            }
            $('<li class="item" style="display:none;"><a href="' + SITE_URL + resp.data.rewriteurl + '"><img src="' + UPLOAD_URL + 'product_images/thumbs/' + resp.data.product_image + '"></a><h2><a href="' + SITE_URL + resp.data.rewriteurl + '">' + resp.data.product_name + '</a></h2><p><strong>' + price + '</strong></p></li>').prependTo('#topinhourlist').fadeIn();
            if ($('#topinhourlist li').size() >= 4) {
                $('#topinhourlist li:last-child').remove();
            }

        }

    }, 'json');

    setTimeout(function () {
        TTS.Framework.TopInHour();
    }, 5000);
}

TTS.Framework.ChooseCategory = function (categoryId, level) {
    var optionHtml = '';
    $.get(SITE_URL + "ajax/getchildcategory", {
        'parent': categoryId,
        'level': 1
    }, function (resp) {
        if (resp.code == '1') {
            $.each(resp.data, function (k, v) {
                optionHtml += '<option value="' + v.category_id + '">' + v.category_name + '</option>';
            });
            if (level == '1') {
                $("#category3").html('');


                if (isAndroid) {
                    $("#category2").html('<select name="categorylv2" onchange="TTS.Framework.ChooseCategory(this.value, 2)" onclick="TTS.Framework.ChooseCategory(this.value, 2)">' + optionHtml + '</select>');
                } else {
                    $("#category2").html('<select name="categorylv2" onchange="TTS.Framework.ChooseCategory(this.value, 2)" onclick="TTS.Framework.ChooseCategory(this.value, 2)" size="10">' + optionHtml + '</select>');

                }

            } else if (level == '2') {
                $("#category3").html('<select name="categorylv3" size="10">' + optionHtml + '</select>');
            }
        }
        if (level == 1) {
            if (categoryId == 1 || categoryId == 7 || categoryId == 2) {
                $("#fashion-attr").fadeIn();
            } else {
                $("#fashion-attr").fadeOut();
            }
        }
    }, 'json');
}


TTS.Framework.Retailer.Approve = function (retailerId, status) {
    $.get(SITE_URL + 'retailer/approve', {
        'id': retailerId,
        'status': status
    }, function (resp) {
        if (resp.code != 1) {
            alert("Có lỗi xảy ra, vui lòng F5 lại trang");
        }
    }, 'json');
}


TTS.Framework.DistrictChange = function (element) {
    var city = $(element).val();
    var districtElement = $(element).parent().find('#district');

    districtElement.attr('disabled', 'disabled').html('');

    $.get(SITE_URL + 'district', {
        'id': city
    }, function (resp) {
        if (resp) {
            districtElement.append('<option value="" selected>Chọn quận/huyện</option>');
            $.each(resp, function (key, value) {
                districtElement.append('<option value="' + value.id + '">' + value.name + '</option>');
            });
            districtElement.removeAttr('disabled').focus();
        }
    }, 'json');
}

TTS.Framework.Product.deleteProductImage = function (element) {
    $(element).parent().parent().remove();
}

TTS.Framework.Product.deleteProductImageAjax = function (imageId, productId, element) {
    event.preventDefault();
    var r = confirm("Bạn có thực sự muốn xóa hình ảnh này?");
    if (r == true) {
        $.post(SITE_URL + 'ajax/deleteProductImage', {
            'id': imageId,
            'pid': productId,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.code == 1) {
                $(element).parent().parent().remove();
            }
        }, 'json');
    }
}

TTS.Framework.GrabImage.Grab = function (imageUrl) {
    $.get(SITE_URL + 'ajax/fetchImage', {
        'url': imageUrl,
        't': Math.random() * 99999999
    }, function (resp) {
        if (resp.code == 1) {
            $("#imageurl").val('');
            $("#imageurl, .do-add-image").removeAttr('disabled');
            $("#image-upload-list").append('<li><input type="hidden" name="image[frompc][]" value="0" /> <img src="' + resp.resizepath + '" /> <input type="hidden" name="image[file][]" value="' + resp.file + '" /> <div class="image-description"> <textarea name="image[description][]" rows="4" maxlength="300" class="form-control" placeholder="Nhập mô tả hình ảnh"></textarea> <input type="radio" name="feature_image" class="feature-image" checked="checked" value="' + resp.file + '" /> Chọn ảnh này làm ảnh đại diện sản phẩm <a href="#" onclick="TTS.Framework.Product.deleteProductImage(this);"><i class="glyphicon glyphicon-remove"></i> Xóa ảnh</a></div> </li>');
        } else {
            alert('Link ảnh này không thể lấy được. Vui lòng thử lại.');

            $("#imageurl").val('');
            $("#imageurl, .do-add-image").removeAttr('disabled');
        }

    }, 'json');
}

TTS.Framework.Cart.Add = function (productId) {

    $.post(SITE_URL + 'cart/add', {
        'productid': productId,
        '_token': CSRF_TOKEN
    }, function (resp) {
        if (resp.code == 1) {
            TTS.Framework.Cart.AjaxGetCart();
        } else {
            alert('Có lỗi xảy ra. Vui lòng F5 lại trang');
        }
    }, 'json');
}

TTS.Framework.Cart.Delete = function (productId) {

    $("#cart-modal-body").html('');
    $("#cart-loading").show();
    $.post(SITE_URL + 'cart/ajaxDelete', {
        'productid': productId,
        '_token': CSRF_TOKEN
    }, function (resp) {
        if (resp.code == 1) {
            TTS.Framework.Cart.AjaxGetCart();
        } else {
            alert('Có lỗi xảy ra. Vui lòng F5 lại trang');
        }
    }, 'json');
}


TTS.Framework.Cart.AjaxGetCart = function () {

    $.get(SITE_URL + 'cart/ajaxGet', function (resp) {
        if (resp.code == 2) {
            $("#cart-modal-footer").hide();
        } else {
            $("#cart-modal-footer").show();
        }
        $("#cart-modal-body").html(resp.body);
        $("#cartModal").modal("show");
        $("#cart-loading").hide();
    }, 'json');

}

TTS.Framework.Cart.ChangeQuantity = function (quantity, productId, productPrice, element) {
    $.post(SITE_URL + "cart/ajaxChangeQuantity", {
        'productid': productId,
        'quantity': quantity,
        '_token': CSRF_TOKEN
    }, function (resp) {
        if (resp.code == 0) {
            alert('Có lỗi xảy ra. Vui lòng F5 lại trình duyệt');
        } else {
            var totalPrice = productPrice * quantity;
            $(element).parents(".modal-body").find("#cart-product-count").text(resp.totalproduct);
            $(element).parents(".modal-body").find("#cart-total-price").text(resp.totalprice);
            $(element).parents("tr").find("#product-total-price").text(numberWithCommas(totalPrice));
        }
    }, 'json');
}


TTS.Framework.Message.ThreadDetail = function (threadId, toUserId) {
    $('#message-thread-detail').show();
    $('#message-list, #newmessage-form').hide();

    if (threadId) {
        $('#messagethreadid').val(threadId);
        $('#messagetoid').val(toUserId);

        $.get(SITE_URL + 'message/getlistmessage/' + threadId, function (resp) {
            $.each(resp.data, function (k, v) {
                $('#message-body > ul').prepend('<li><img class="avatar" width="32" height="32" src="' + resp.user[v.fromid].avatar + '" /> <span class="username"><a href="#">' + resp.user[v.fromid].name + '</a></span>: <span class="message">' + v.body + '</span></li>');
                $('#message-body').scrollTop($("#message-body")[0].scrollHeight);
            });
            $('#message-detail-loading').hide();
        }, 'json');

        TTS.Framework.Message.LongPolling(threadId);
    }
}

TTS.Framework.Message.LoadListMessage = function () {
    $('#message-thread-list').html('');
    $.get(SITE_URL + 'message/getlistthread', function (resp) {
        if (resp.code == 1) {
            $.each(resp.data, function (k, v) {
                $('#message-thread-list').append('<li onclick="TTS.Framework.Message.ThreadDetail(\'' + v.threadid + '\', ' + v.user.user_id + ')" id="mthread" data-thread-id="' + v.threadid + '"><img src="' + v.user.avatar + '" width="32" height="32" /><span class="name">' + v.user.name + '</span><p class="message">' + v.body + '</p><div class="pull-right"><span class="glyphicon glyphicon-chevron-right"></span></div></li>');
            });
            $('#message-loading').hide();
        } else {
            $('#message-thread-list').html('<li>None</li>');
            $('#message-loading').hide();
        }
    }, 'json');

}


TTS.loginFbPopup = function () {
    $.oauthpopup({
        path: SITE_URL + 'user/fblogin/',
        callback: function () {
            $.get(SITE_URL + 'user/status/', function (resp) {
                if (resp.login == true) {
                    location.reload();
                } else {
                    alert('Đăng nhập không thành công, vui lòng thử lại');
                }
            }, 'json');
        }
    });
};


TTS.Purchase.DoPurchase = function (product_id) {
    var name = $('#purchase-name').val();
    var city = $('#selectcity').val();
    var phone = $('#purchase-phone').val();
    var district = $('#district').val();
    var address = $('#purchase-address').val();
    var birthday = $('#purchase-birthday').val();
    var gender = $('#optionsRadios1').val();
    if (!name || !city || !phone || !district || !address) {
        alert('Vui lòng nhập đầy đủ thông tin');
    } else {
        $.post(SITE_URL + 'purchase', {
            'gender': gender,
            'name': name,
            'city': city,
            'phone': phone,
            'district': district,
            'address': address,
            'birthday': birthday,
            'product_id': product_id,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.status == 'success') {
                $('#purchase-form').hide();
                $('#resp-status').html('<div class="alert alert-success"><strong>Thanks you!</strong> Bạn đã đặt hàng thành công, chúng tôi sẽ liên hệ bạn sớm nhất có thể.</div>').show();
            } else if (resp.status == 'error') {
                showAlert('Bạn chưa nhập đầy đủ thông tin', 0);
            } else {
                showAlert('Đặt mua thất bại, vui lòng thử lại', 0);
            }
        }, 'json');
    }
};

TTS.Closet.AddCloset = function (closetName, doSelect) {
    $.post(SITE_URL + 'closet/addCloset', {
        'name': closetName,
        '_token': CSRF_TOKEN
    }, function (resp) {
        if (resp.code == 1) {
            $("#addcloset_name").val('');
            $("#closetlist").prepend('<li onclick="TTS.Closet.SelectCloset(' + resp.id + ')" data-id="' + resp.id + '">' + closetName + '<span>Thêm vô tủ này</span></li>');

            if (doSelect) {
                TTS.Closet.SelectCloset(resp.id);
            }
        } else {
            showAlert('Lỗi. Vui lòng thử lại sau', 0);
        }
    }, 'json');
}

TTS.Closet.GetClosetList = function () {
    console.log('GetClosetList');
    $("#closetlist").html('<li id="loading-icon"><img src="' + STATIC_URL + 'images/loading.gif" /></li>');
    $.get(SITE_URL + 'closet/getCloset', function (resp) {

        $.each(resp.data, function (key, val) {
            $("#closetlist").append('<li onclick="TTS.Closet.SelectCloset(' + val.closet_id + ')" data-id="' + val.closet_id + '">' + val.closet_name + '<span>Thêm vô tủ này</span></li>');
        });
        $("#loading-icon").hide();
    }, 'json');
}

TTS.Closet.SelectCloset = function (closetId) {
    $("#closetlist > li").removeClass("selected");
    $("#closetlist > li[data-id=" + closetId + "]").addClass('selected');
    $("#selected_closet").val(closetId);
    $("#addToCloset").removeAttr('disabled');
}


TTS.Wili.DoWili = function (productId, closetId) {
    if (notEmpty(productId)) {
        $.post(SITE_URL + 'wili/doWili', {
            'product_id': productId,
            'closet_id': closetId,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.code == 1) {
                $("#wili_modal").append('<div id="wili-modal-alert" class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>Thêm vào tủ đồ thành công</div>');
                $(".addtudo").hide();
                $("#wili_count").text(parseInt($("#wili_count").text()) + 1);
            } else {
                showAlert('Lỗi. Vui lòng thử lại sau', 0);
            }
        }, 'json');
    } else {
        showAlert("Lỗi: Chưa chọn được sản phẩm để Wili, vui lòng thử lại.", 0);

    }
}


TTS.Comment.DoComment = function (productId, commentContent, parentId) {
    if (notEmpty(productId) && notEmpty(commentContent)) {


        $.post(SITE_URL + 'comment/addcomment', {
            'product_id': productId,
            'comment': commentContent,
            'parent_id': parentId,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.code == '1') {

            } else {
                showAlert('Lỗi. Vui lòng thử lại sau', 0);
            }
        }, 'json');
    }
}

TTS.Advice.DoAdviceWithMessage = function (adviceId, adviceContent) {
    if (notEmpty(adviceId) && notEmpty(adviceContent)) {
        $.post(SITE_URL + "advice/insertMessage", {
            'advice_id': adviceId,
            'message': adviceContent,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.code == '1') {
                message = 'Bạn đã gửi lời khuyên thành công. Cám ơn bạn.';
                $("#advice_modal").html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' + message + '</div>');
                $("#advice_list").prepend('');
            } else {
                message = 'Có lỗi xảy ra, vui lòng thử lại sau.';
                $("#advice_modal").html('<div class="alert alert-error alert-dismissable"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' + message + '</div>');
            }
        }, 'json');
    } else {
        showAlert('Bạn chưa nhập lời khuyên', 0);
    }
}

TTS.Advice.DoAdvice = function (productId, adviceType) {
    if (notEmpty(productId) && notEmpty(adviceType)) {
        $.post(SITE_URL + 'advice/addAdvice', {
            'product_id': productId,
            'advice_type': adviceType,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.code == '1') {
                if (adviceType == 0) {
                    $(".konenmua").text(parseInt($(".konenmua").text()) + 1);
                } else {
                    $(".nenmua").text(parseInt($(".nenmua").text()) + 1);
                }
                $("input[name=advice_id]").val(resp.id);
            } else {
                showAlert('Lỗi. Vui lòng thử lại sau', 0);
            }
        }, 'json');
    } else {
        alert('Lỗi');
    }
}

TTS.Advice.Vote = function (adviceId, vote) {
    if (notEmpty(adviceId) && notEmpty(vote)) {
        $.post(SITE_URL + 'advice/vote', {
            'vote': vote,
            'advice_id': adviceId,
            '_token': CSRF_TOKEN
        }, function (resp) {
            if (resp.code == '1') {
                $(".huuich[data-id=" + adviceId + "]").hide();
                showAlert('Cám ơn bạn đã đánh giá', 1);
            } else {
                showAlert('Lỗi. Vui lòng thử lại sau', 0);
            }
        }, 'json');
    } else {
        showAlert('Lỗi. Vui lòng thử lại sau', 0);
    }
}


TTS.Framework.District = function (city) {
    $('#district').html('');
    $.get(SITE_URL + 'district', {
        'id': city
    }, function (resp) {
        if (resp) {
            $.each(resp, function (key, value) {
                $('#district').append('<option value="' + value.id + '">' + value.name + '</option>');
            });
            if ($("#district").attr('rel') == 'isall') {
                $('#district').prepend('<option value="0">Tất cả</option>');
            }
            $("#district").removeAttr('disabled');
        }
    }, 'json');
};


TTS.Framework.GetFBFriendList = function () {
    $.get(SITE_URL + "user/fetchFriendOnFb", function (resp) {
        if (resp.code == 1) {
            $.each(resp.data, function (key, value) {
                $("#fb-friend-list").append('<div class="fbf"> <div class="fbfGridItem"> <a href="' + SITE_URL + 'u/' + value.username + '" class="userWrapper"> <h3 class="username"> ' + value.name + ' </h3> <p class="userStats"> ' + value.wili_count + ' Wili • ' + value.follow_count + ' Người theo dõi </p> <div class="userThumbs"> <span class="focusThumbContainer"> <span class="hoverMask"></span> <img src="' + value.avatar + '" class="userFocusImage" style=""> </span> ' + value.product_thumbs + ' </div> </a> <button type="button" class="btn btn-info">Theo dõi</button> </div> </div>');
            });
        } else {
            TTS.loginFbPopup();
        }
        $('#fb-friend-list-loading').hide();
    }, 'json');
}


TTS.Framework.formatNumberVND = function (e) {
    e.parseNumber({
        format: "#,##0",
        locale: "us"
    });
    e.formatNumber({
        format: "#,##0",
        locale: "us"
    });
}

TTS.Framework.OnloadPage = function () {
    if ($("input[type=text]").length == 1) {
        $(".searchinputcontainer").focus();
    }
}

TTS.Framework.SocketClient = function (inputChannel, socketAddr, socketPort) {
    //socket io client

    var socket = io("noti.thitruongsi.com:9193");

    //on connetion, updates connection state and sends subscribe request
    socket.on('connect', function () {
        socket.emit('subscribe', {
            channel: inputChannel
        });
    });

    //when reconnection is attempted, updates status
    socket.on('reconnecting', function () {
        console.log('reconnecting');
    });

    //on new message adds a new message to display
    socket.on('message', function (data) {
        if (data) {
            TTS.Framework.NotificationCount();
            TTS.Framework.AppendNotification(data.text);
        }
    });
}
TTS.Framework.AppendNotification = function (rawData) {
    var data = $.parseJSON(rawData);
    $(".notification-center > ul").prepend('<li class="unread_true"><a href="' + data.href + '">' + data.body + '</a></li>');

    if ($(".notification-center > ul > li").length > 5) {
        $(".notification-center > ul > li:last-child").remove();
    }
}
TTS.Framework.NotificationCount = function () {
    var notificationCount = parseInt($(".notification-count").text()) + 1;
    $(".notification-count").text(notificationCount).addClass("show");
}

TTS.Framework.RecommendationForUser = function () {
    $.get(SITE_URL + 'ajax/recommendationforuser', function (resp) {
        if (resp.data != 'null') {
            $.each(resp.data, function (k, v) {
                if (v.product_name) {
                    $("#recommendation-product-list").prepend('<div class="re-product"> <a href="' + v.href + '"><img src="' + UPLOAD_URL + 'product_images/thumbs/100_' + v.product_image + '" width="80px" class="pull-left"/></a> <div class="pull-left info"> <a href="' + v.href + '" class="product_name">' + v.product_name + '</a><br/> <span class="price">Giá: <strong class="ui-primary-color">' + v.product_price + '</strong></span><br/> <span class="views">Lượt xem: ' + v.views + '</span> </div> </div>');
                }
            });
        }
    }, 'json');
}


TTS.Framework.ShopSponsosedIndex = function (limit, categoryId) {
    $.get(SITE_URL + 'ads/shopsponsored', {
        'limit': limit,
        'catid': categoryId
    }, function (resp) {
        if (resp.code == 1) {
            $("#ads-shop-sponsored").html('');
            $.each(resp.shoplist, function (k, v) {
                if (!v.product_list) {
                    v.product_list = 'Chưa có sản phẩm';
                }
                $("#ads-shop-sponsored").append('<div class="shop-item"><div class="shop-name"><img class="vip-icon" src="' + STATIC_URL + 'images/vip.png?v=2" style="position: relative;top: -1px;" /> <a href="' + v.shop_url + '">' + v.name + '</a></div> <div class="follow-box"><a data-id="' + v.id + '" data-action="' + (v.followed ? 'unfollow' : 'follow') + '" data-type="2" href="' + (typeof(USER_LOGIN) != "undefined" ? '#' : SITE_URL + 'user/login/') + '" rel="follow" class="btn btn-white btn-small ' + (typeof(USER_LOGIN) != "undefined" ? 'theodoi' : '') + '"> ' + (v.followed ? '<span class="glyphicon glyphicon-remove"></span> Bỏ theo dõi' : '<span class="glyphicon glyphicon-heart"></span> Theo dõi') + '</a></div> <div class="shop-category">Chuyên cung cấp ' + v.catusing + ' · ' + v.province_name + '<br/><strong>' + v.product_count + '</strong> sản phẩm · <strong>' + v.follow_count + '</strong> người theo dõi </div> <div class="picture-list"> <ul>' + v.product_list + '</ul> </div> </div>');
            });
            $("#ads-shop-index-area").show();
        }
    }, 'json');
    setTimeout(function () {
        $("#ads-shop-sponsored").fadeOut();
        TTS.Framework.ShopSponsosedIndex(limit, categoryId);
        $("#ads-shop-sponsored").fadeIn();
    }, 60000);
}


var chartingOptions = {
    xAxis: {
        categories: []
    },
    series: [{
        name: 'Total Players',
        data: []
    }]
};

var renderProductChart = function (productId) {
    var chartOptions = {
        chart: {
            renderTo: 'product-statistic-views',
            type: 'line'
        },
        title: {
            text: 'Thống kê lượt xem'
        },
        xAxis: {
            categories: [],
            type: 'datetime',
            minTickInterval: 3,
            title: {
                text: "Ngày"
            }
        },
        yAxis: {
            min: 0,
            title: {
                'text': 'Lượt xem'
            },
            allowDecimals: false
        },
        series: [{
            'name': 'Lượt xem',
        }],
        credits: {
            enabled: false
        }
    };


    $.getJSON(SITE_URL + "ajax/statproductviews", {
        'id': productId
    }, function (resp) {
        if (resp.code == 1) {
            var categories = [];
            var seriesData = [];
            $.each(resp.data, function (k, v) {
                categories.push(v.date);
                seriesData.push(parseInt(v.views));
            });


            chartOptions.xAxis.categories = categories;
            chartOptions.series[0].data = seriesData;
            $("#product-statistic-views").html("");
            var chart = new Highcharts.Chart(chartOptions);

        } else {
            console.log("data error");
        }
    });
};

var renderShopViewChart = function (shopId) {
    var chartOptions = {
        chart: {
            renderTo: 'shop-statistic-views',
            type: 'line'
        },
        title: {
            text: 'Thống kê lượt xem trang shop'
        },
        xAxis: {
            categories: [],
            type: 'datetime',
            minTickInterval: 3,
            title: {
                text: "Ngày"
            }
        },
        yAxis: {
            min: 0,
            title: {
                'text': 'Lượt xem'
            },
            allowDecimals: false
        },
        series: [{
            'name': 'Lượt xem',
        }],
        credits: {
            enabled: false
        }
    };


    $.getJSON(SITE_URL + "ajax/statshopviews", {
        'id': shopId
    }, function (resp) {
        if (resp.code == 1) {
            var categories = [];
            var seriesData = [];
            $.each(resp.data, function (k, v) {
                categories.push(v.date);
                seriesData.push(parseInt(v.views));
            });


            chartOptions.xAxis.categories = categories;
            chartOptions.series[0].data = seriesData;
            $("#shop-statistic-views").html("");
            var chart = new Highcharts.Chart(chartOptions);

        } else {
            console.log("data error");
        }
    });
};


var renderShopProductTotalViewChart = function (shopId) {
    var chartOptions = {
        chart: {
            renderTo: 'shop-product-statistic-views',
            type: 'line'
        },
        title: {
            text: 'Thống kê lượt xem sản phẩm'
        },
        xAxis: {
            categories: [],
            type: 'datetime',
            minTickInterval: 3,
            title: {
                text: "Ngày"
            }
        },
        yAxis: {
            min: 0,
            title: {
                'text': 'Lượt xem'
            },
            allowDecimals: false
        },
        series: [{
            'name': 'Lượt xem',
            color: '#f60'
        }],
        credits: {
            enabled: false
        }
    };


    $.getJSON(SITE_URL + "ajax/stattotalproductviews", {
        'id': shopId
    }, function (resp) {
        if (resp.code == 1) {
            var categories = [];
            var seriesData = [];
            $.each(resp.data, function (k, v) {
                categories.push(v.date);
                seriesData.push(parseInt(v.views));
            });


            chartOptions.xAxis.categories = categories;
            chartOptions.series[0].data = seriesData;
            var chart = new Highcharts.Chart(chartOptions);

        } else {
            console.log("data error");
        }
    });
};
