var baseUrl = window.location.protocol + "//" + window.location.host;

function productMiniSlider() {
    var huge = $(this).data('huge'),
        thumb = $(this).data('thumb');
    var mainObj = $('.img:first');
    //destroy the zoomer
    destroyZoome(mainObj);
    //Fadeout the object
    mainObj.fadeOut('1000', function() {
        mainObj.attr('src', thumb);
        mainObj.attr('rel', huge);
        mainObj.fadeIn('1000');
        mainObj.zoome({
            hoverEf: 'transparent',
            zoomRange: [1, 5],
            showZoomState: true,
            magnifierSize: [100, 100]
        });
    });
}

function destroyZoome(obj) {
    if (obj.parent().hasClass('zm-wrap')) {
        obj.unwrap().next().remove();
    }
}

function dropcart() {
    var This = $(this);
    var panel = This.parent().find('.dropcart-panel');
    var url = baseUrl + '/cart';

    var success = function(data) {
        panel.html(data);
    }
    //slide the panel!
    if (!panel.is(":visible")) {
        $.ajax({
            type: "POST",
            url: url,
            success: success,
        });
    }
    panel.animate({
        "height": "toggle"
    }, {
        duration: 400
    });
}

function addtoCart(obj) {
    var url = baseUrl + '/cart/addproduct';
    var stockCount = $('#qty').val();
    $.ajax({
        type: "POST",
        url: url,
        data: 'id=' + obj + '&stock=' + stockCount,
        dataType: "json",
        success: function(data) {
            toastr.success(data.message + " sepete eklendi!", data.product_name)
            dropcart(); //refresh the values
        }
    });
}

function AddToCartBlur() {
    var container = $(this);
    if (container.val() <= 1) {
        container.val(1);
    }
}

function CartInputModifier() {
    var container = $("#qty");
    var value = container.val();

    var result = parseInt(value) + 1;
    container.val(result);
}

function CartInputModifier_Dec() {
    var container = $("#qty");
    var value = container.val();
    if (value == 1) {
        var result = 1;
    } else {
        var result = parseInt(value) - 1;
    }
    container.val(result);
}

function getTowns(obj) {
    var text = $(obj).find(":selected").attr('value');
    $.ajax({
        type: "POST",
        url: "/doajax/user/town",
        dataType: 'json',
        data: 'id=' + text,
        success: function(data) {
            var jsondata = jQuery.parseJSON(data);
            $("#towns").html("");
            $.each(data, function() {
                var html = '<option value="' + this.id + '">' + this.name + '</option>';
                $("#towns").append(html);
            });
        },
    });
}

function showAgreement(obj) {
    $.fn.SimpleModal({
        width: 800,
        model: 'modal-ajax',
        title: 'Üyelik Sözleşmesi',
        param: {
            url: '/doajax/user/agreement',
            onRequestComplete: function() {},
            onRequestFailure: function() {}
        }
    }).addButton('Kapat', 'btn').showModal();
}

function removeCartItem(e, obj) {
    e.preventDefault();
    $obj = $(obj);
    $id = $obj.closest('tr').data("prod");
    console.log($id);
    $.ajax({
        type: "POST",
        url: "/cart/remove",
        data: 'id=' + $id,
        success: function(data) {
            $obj.closest('tr').children('td').wrapInner('<div />').children().slideUp(function() {
                $obj.closest('tr').remove();
            });
        },
    });

    console.log($obj.closest('tr'));
}

function checkStrength(password) {
    var strength = 0;
    var block = $("#passwordStrBlock");
    var bar = $(".bar", '.passwordStr');

    if (password.length < 6) {
        block.removeClass();
        block.addClass('text-error');
        bar.width("0%");
        return 'Çok Kısa!';
    }
    if (password.length > 7) strength += 1;
    if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1;
    if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
    if (password.match(/(.*[!,%,&,@,#,$,^,*,?,_,~].*[!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;
    if (strength < 2) {
        block.removeClass()
        block.addClass('muted')
        bar.width('20%');
        return 'Zayıf';
    } else if (strength == 2) {
        block.removeClass();
        block.addClass('text-warning');
        bar.width('50%');
        return 'Orta';
    } else {
        block.removeClass()
        block.addClass('text-info')
        bar.width('100%');
        return 'Güçlü!';
    }
}
//Top Navigation!
var $mainmenu = $(".navigation-main");
var fired2 = false;
/*$mainmenu.menuAim({
    activate: activateMainSubmenu, // fired on row activation
    deactivate: deactivateMainSubmenu, // fired on row deactivation
    exitMenu: MainexitOut
});*/

function activateMainSubmenu(row) {
    var $row = $(row),
        $submenu = $('.overlay-nav-main', $row);
    if (!fired) {
        $submenu.height(0).show().animate({
            height: "305px"
        }, 1000, "easeOutElastic");
        fired2 = true;
    } else {
        $submenu.show();
    }
    return true;
}

function deactivateMainSubmenu(row) {
    var $row = $(row),
        $submenu = $('.overlay-nav-main', $row);
    if (fired2) {
        $submenu.hide();
    }
    return true;
}

function MainexitOut(row) {
    var $row = $(row),
        $submenu = $('.overlay-nav-main', $row);

    $submenu.hide();
    fired = false;
    return true;
}
//Left Menu :)
var $menu = $(".nav-ul");
var fired = false;
var timer;

$menu.menuAim({
    activate: activateSubmenu, // fired on row activation
    deactivate: deactivateSubmenu, // fired on row deactivation
    enter: enterMenu,
    exitMenu: exitOut
});

function activateSubmenu(row) {
    var $row = $(row),
        $submenu = $('.nav-dropdown', $row);
    $(".nav-container").addClass("nav-enabled");
    $row.find("a:first").addClass("maintainHover");
    $row.addClass("activateD");
    $allmenus = $('.nav-drowdown');

    if (!fired) {
        $submenu.width(0).show().animate({
            width: "520px"
        }, 1000, "easeOutElastic");
        fired = true;
    } else {
        $submenu.show();
    }
    return true;
}

function deactivateSubmenu(row) {
    var $row = $(row),
        $submenu = $('.nav-dropdown', $row);
    if (fired) {
        $submenu.hide();
    }
    $row.find("a:first").removeClass("maintainHover");
    $row.removeClass("activateD");
    return true;
}

function enterMenu(row) {
    clearTimeout(timer);
}

function exitOut(row) {
    var $row = $(row),
        $submenu = $('.nav-dropdown', $row);

    $submenu.hide();
    fired = false;
    $(".nav-container").removeClass("nav-enabled");
    return true;
}
var $_Position;
var $mainCont;
var $mainPos;
var $subContainer;
var $subwidth = 220;

function subCategoryEnable(row) {
    $subContainer = $(row).parent().find(".sub-categories"); //itself
    $_Position = $subContainer.parent(); //dd
    $mainCont = $subContainer.parent().parent().parent(); //dummy-container
    $mainPos = $subContainer.parent().parent().parent().parent(); //main-left

    //hide Dummy
    $mainCont.stop().animate({
        width: 0
    }, 300, function() {
        $(this).hide()
        $subContainer.appendTo($mainPos);
        $subContainer.width(0).show().animate({
            width: $subwidth
        }, 1000);
    });
}

function subCategoryDisable() {
    //hide Dummy
    $subContainer.stop().animate({
        width: 0
    }, 300, function() {
        $(this).hide()
        $subContainer.appendTo($_Position);
        $mainCont.width(0).show().animate({
            width: $subwidth
        }, 1000);
    });
}

function getObjectSize(obj) {
    var len = 0,
        key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) len++;
    }
    return len;
}

function linkRemoveAttribute(e) {
    e.preventDefault();
    var $obj = $(this);
    var $attrGroup = $obj.data("attrClass");
    var $attrValue = $obj.data("value");
    var $type = $obj.data("type");
    switch ($type) {
        case 0:
            $param = {
                "Filter[]": $attrGroup + "[-]" + $attrValue
            };
            break;
        case 1:
            $param = {
                "Price Range": $attrValue
            };
            break;
    }
    var uri = new URI();
    uri.removeSearch($param);
    document.location = uri;
}

function addAttribute() {
    var $obj = $(this);
    var $attrGroup = $obj.data("attrClass");
    var $attrValue = $obj.data("value");
    var $type = $obj.data("type");
    var $title = 'Filter[]';
    switch ($type) {
        case 0:
            $param = {
                "Filter[]": $attrGroup + "[-]" + $attrValue
            };
            break;
        case 1:
            $param = {
                "Price Range": $attrValue
            };
            break;
    }

    var uri = new URI();

    //check if the same group exist.
    if (uri.hasQuery("Filter[]")) {
        var query = uri.query(true);
        var i;
        $.each(query["Filter[]"], function(i, obj) {
            if (query["Filter[]"][i].indexOf($attrGroup) > -1) {
                $_temp = {
                    "Filter[]": query["Filter[]"][i]
                };
                uri.removeSearch($_temp);
            }
        });
    }
    if ($(this).is(':checked')) {
        uri.addSearch($param);
        document.location = uri;
    } else {
        uri.removeSearch($param);
        document.location = uri;
    }
    return true;
}

function setAddress(e) {
    var $target = e.target;
    var $obj = $target.find('a');
    console.log('$obj');
    var $type = $obj.data('type');
    var $addrId = $obj.data('addr');
    var $body = $obj.parent().next();
    if ($body.hasClass("in")) {
        $.ajax({
            type: "POST",
            url: "/checkout/setAddress",
            data: 'type=' + $type + '&addrID=' + $addrId,
            success: function(data) {
                $obj.parent().css("background-color", "khaki");
            },
        });
    } else {
        $obj.parent().css("background-color", "transparent");
    }

}

function MainContainerToggler() {
    $containerElem = $(".overlay-nav-main");
    $containerElem.animate({
        height: "toggle"
    }, {
        duration: 1000,
        specialEasing: {
            width: "linear",
            height: "easeOutBounce"
        },
    });
}
$(document).on('keyup', '#inputPassword', function() {
    $("#passwordStrBlock").html(checkStrength($('#inputPassword').val()));
});
$(document).on('blur', '#qty', AddToCartBlur);
$(document).on('change', '.attribute-checkbox', addAttribute);
$(document).on('click', '.attribute-link', linkRemoveAttribute);
$(document).on('click', '.thumbs img', productMiniSlider);
$(document).on('click', '#dropcart', dropcart);
$(document).on('shown', "#accordion1", setAddress);
$(document).on('click', '.navigation-main h3', MainContainerToggler);
$(document).ready(function() {
    $('.contact-map').width($(window).width());
    $('.map_dummy').width("100%");
    $('.map_dummy').height(400);
    $('.contact-map').height(421);

    $(".slider-box").layerSlider({
        skinsPath: './js/layerslider/skins/',
        skin: 'borderlesslight3d',
        thumbnailNavigation: 'hover',
        hoverPrevNext: true,
        autoPlayVideos: false
    });
});