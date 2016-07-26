var hexTemp;
$(function () {
    $('#changecolor').ColorPicker({
        color: '#b2eced',
        onChange: function (hsb, hex, rgb) {
            $('div[id=themecolor]').css({'background-color': '#' + hex});
            $("#changecolor > i").css({'color': '#' + hex});
            hexTemp = hex;
        },
        onHide: function () {
            $.post(SITE_URL + 'ajax/shopthemecolor', {
                'shopid': $(".shop-page").attr('shop-id'),
                'attribute': 'color',
                '_token': CSRF_TOKEN,
                'value': '#' + hexTemp
            }, function (resp) {
                if (resp.code == '1') {
                    showNotification('Đã lưu thay đổi');
                }
            }, 'json');
        }

    });

    $('#changecolor1').ColorPicker({
        color: '#000000',
        onChange: function (hsb, hex, rgb) {
            $('div[id=themecolor]').css({'color': '#' + hex});
            $("#changecolor1 > i").css({'color': '#' + hex});
            hexTemp = hex;
        },
        onHide: function () {
            $.post(SITE_URL + 'ajax/shopthemecolor', {
                'shopid': $(".shop-page").attr('shop-id'),
                'attribute': 'color1',
                '_token': CSRF_TOKEN,
                'value': '#' + hexTemp
            }, function (resp) {
                if (resp.code == '1') {
                    showNotification('Đã lưu thay đổi');
                }
            }, 'json');
        }
    });

    $('#changecolor2').ColorPicker({
        color: '#000000',
        onChange: function (hsb, hex, rgb) {
            $('#brandcolor > a').css({'color': '#' + hex});
            $("#changecolor2 > i").css({'color': '#' + hex});
            hexTemp = hex;
        },
        onHide: function () {
            $.post(SITE_URL + 'ajax/shopthemecolor', {
                'shopid': $(".shop-page").attr('shop-id'),
                'attribute': 'color2',
                '_token': CSRF_TOKEN,
                'value': '#' + hexTemp
            }, function (resp) {
                if (resp.code == '1') {
                    showNotification('Đã lưu thay đổi');
                }
            }, 'json');
        }
    });

});