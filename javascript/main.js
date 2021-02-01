// CraftedWeb Javascript file.

var mouseY = 0;
var arrow = 0;

$(document).ready(function ()
{
    $('#popup').center();
});

$(".login_input").focus(function ()
{
    if (this.value == "Username...")
    {
        this.value = "";
    }
    else if (this.value = "Password...")
    {
        this.value = "";
    }
});


function vote(siteid, button)
{
    $(button).attr("disabled", "true");
    $.post("includes/scripts/vote.php", {siteid: siteid},
        function (data)
        {
            window.open(data, "_blank");
        });
}

function changeAmount(input, type)
{
    if (type == 'open')
    {
        var amount = document.getElementById("amount");
        amount.value = input.value;
    }
    else
    {
        $.post("includes/scripts/misc.php", {'convertDonationList': input.value},
            function (data)
            {
                var amount = document.getElementById("amount");
                amount.value = data;
            });
    }
}

$(".content_hider").click(function ()
{
    $(this).toggleClass("content_hider_highlight");
    if ($(this).next().is(":hidden"))
    {
        $(this).next().slideDown("fast");
    }
    else
    {
        $(this).next().slideUp("fast");
    }
});

function buyShopItem(type, entry, last_page, account_name)
{
    var character = document.getElementById("character");
    $("#overlay").fadeIn("fast");
    $("#shopContent").html("<b class='yellow_text'>Proccessing...</b>");
    $.post("includes/scripts/sendReward.php", {item_entry: entry, character_realm: character.value, send_mode: type, last_page: last_page},
        function (data)
        {
            popUp("Item Purchased", "物品被购买并通过邮件发送给你的角色。");
            $("#shopContent").html(data);
            $.post("includes/scripts/misc.php", {element: type, account: account_name},
                function (data)
                {
                    $("#" + type).html(data);
                });
        });
}

function enableForumAcc()
{
    $("#forumAcc").fadeIn();
}

function redirect(url)
{
    $("#overlay").fadeIn("fast");
    window.location = url;
}

function confirmItemDelete(url)
{
    var btn = confirm("您真的要删除此物品吗?");
    if (btn == true)
    {
        redirect(url);
    }
    else
    {
        alert("Chicken!");
    }
}

(function ($)
{
    $.fn.extend({
        center: function ()
        {
            return this.each(function ()
            {
                var top = ($(window).height() - $(this).outerHeight()) / 2;
                var left = ($(window).width() - $(this).outerWidth()) / 2;
                $(this).css({position: 'absolute', margin: 0, top: (top > 0 ? top : 0) + 'px', left: (left > 0 ? left : 0) + 'px'});
            });
        }
    });
})(jQuery);

function popUp(title, content)
{
    $("#overlay").fadeIn("fast");
    $("#popup").fadeIn("slow");
    $("#popup_close").fadeIn();
    $("#popup_title").html(title + '<div id="popup_close" onclick="closePopup()"></div>');
    $("#popup_body").html("<span class='yellow_text'>" + content + "</span>");
    $('#popup').center();
    var height = $(document).height();
    $("#overlay").css("height", height + "px");
}

function closePopup()
{
    $("#overlay").fadeOut();
    $("#popup").fadeOut();
}

function register(captchastate)
{
    $("#overlay").fadeIn();
    $('#register').attr('disabled', 'disabled');

    var username        = document.getElementById("username").value;
    var email           = document.getElementById("email").value;
    var password        = document.getElementById("password").value;
    var password_repeat = document.getElementById("password_repeat").value;

    if (captchastate == 1)
    {
        var captcha = document.getElementById("captcha").value;
    }
    else
    {
        var captcha = 0;
    }

    var raf = document.getElementById("referer").value;

    popUp("Account Creation", "您的帐号正在注册中...");

    $.post("includes/scripts/register.php", 
        {
            register: "true", 
            username: username, 
            email: email, 
            password: password,
            password_repeat: password_repeat, 
            captcha: captcha, 
            raf: raf
        },
            function (data)
            {
                if (data == true)
                {
                    popUp("Account Created", "您的帐户已成功创建。您将在5秒内被重定向到帐户页面...");
                    $("#username").val("");
                    $("#email").val("");
                    $("#password").val("");
                    $("#password_repeat").val("");
                    setTimeout("redirect('?page=account')", 5000);
                }
                else
                {
                    popUp("Account Creation", data);
                    $('#register').removeAttr('disabled');
                }
            });
}

function checkUsername()
{
    var username = document.getElementById("username").value;

    $("#username_check").fadeIn();
    $("#username_check").html("Checking for availability...");

    $.post("includes/scripts/register.php", 
        {check: "username", value: username},
        function (data)
        {
            $("#username_check").html(data);
        });
}

function acct_services(service)
{
    $("#character").hide();
    $("#account").hide();
    $("#settings").hide();

    $("#" + service).fadeIn(400);
}

function unstuck(guid, char_db)
{
    popUp("Proccessing...", "Proccessing...");
    $.post("includes/scripts/character.php", {action: "unstuck", guid: guid, char_db: char_db},
        function (data)
        {
            popUp("Unstucked!", "你的角色成功地摆脱了困境!");
        });
}

function revive(guid, char_db)
{
    popUp("Proccessing...", "Proccessing...");
    $.post("includes/scripts/character.php", {action: "revive", guid: guid, char_db: char_db},
        function (data)
        {
            popUp("Revived!", "你的角色成功复活了!");
        });
}

function confirmService(guid, realm_id, service, title, name)
{
    popUp("Confirm", "Are you sure you wish to perform a " + title + " on " + name + "?<br/><br/>\
	<input type='button' value='Yes' onclick='service(" + guid + "," + realm_id + ",\"" + service + "\")'>  \
	<input type='button' value='No' onclick='closePopup()'>");
}

function nstepService(guid, realm_id, service, title, name)
{
    window.location = '?page=confirmservice&selected=' + service + '&guid=' + guid + '&rid=' + realm_id + '&title=' + title + '&name=' + name;
}

function service(guid, realm_id, service)
{
    popUp("Proccessing...", "Proccessing...");

    $.post("includes/scripts/character.php", {action: "service", guid: guid, realm_id: realm_id, service: service},
        function (data)
        {
            if (data == true)
                window.location = '?page=service&selected=' + service + '&service=applied'
            else
                popUp("Information", data);
        });
}

function removeItemFromCart(cart, entry)
{
    $.post("includes/scripts/shop.php", {action: "removeFromCart", cart: cart, entry: entry},
        function (data)
        {
            window.location = '?page=cart';
        });
}

function addCartItem(entry, cart, shop, button)
{
    $(button).attr("disabled", "true");
    if (arrow == 0)
    {
        $("#cartArrow").fadeIn(400);
        $("#cartArrow").css("top", mouseY - 200 + "px");

        $("#cartArrow").animate({
            top: "35px"
        }, mouseY + 500);
        arrow = 1;
    }

    $.post("includes/scripts/shop.php", {action: "addShopitem", cart: cart, entry: entry, shop: shop},
        function (data)
        {
            loadMiniCart(cart);
            $("#status-" + entry).fadeIn(200).delay(1100).fadeOut(200);
            setTimeout(function ()
            {
                $(button).removeAttr("disabled");
                $("#cartArrow").fadeOut(400);
            }, 1350);
        });
}

function clearCart()
{
    $.post("includes/scripts/shop.php", {action: "clear"},
        function (data)
        {
            window.location = '?page=cart';
        });
}

function loadMiniCart(cart)
{
    $.post("includes/scripts/shop.php", {action: "getMinicart", cart: cart},
        function (data)
        {
            $("#cartHolder").html(data);
        });
}

function saveItemQuantityInCart(cart, entry)
{
    var quantity = document.getElementById(cart + "Quantity-" + entry).value;

    $.post("includes/scripts/shop.php", {action: "saveQuantity", cart: cart, entry: entry, quantity: quantity},
        function (data)
        {
            window.location = '?page=cart'
        });
}

function checkout()
{
    var values = document.getElementById("checkout_values").value;

    popUp("Proccessing...", "处理您的付款和发送物品...");
    $.post("includes/scripts/shop.php", {action: "checkout", values: values},
        function (data)
        {
            if (data == true)
            {
                window.location = '?page=cart&return=true'
            }
            else
            {
                window.location = '?page=cart&return=' + data;
            }
        });
}

function viewTos()
{
    $.post("includes/scripts/misc.php", {getTos: true},
        function (data)
        {
            popUp("Terms of Service", data);
        });
}

function viewRefundPolicy()
{
    $.post("includes/scripts/misc.php", {getRefundPolicy: true},
        function (data)
        {
            popUp("Refund Policy", data);
        });
}

/* Teleportation system */
var selected_char = 0;
var box_char = 0;
function selectChar(values, box)
{
    $(".charBox").fadeOut('fast');
    $(box).fadeIn('fast');
    $("#choosechar").html("Selected Character:");
    if (selected_char != 0)
    {
        box_char = document.getElementById(selected_char);
        $(box_char).removeClass("charBox").addClass("charBoxHighlight");
    }
    selected_char = values;
    box_char = document.getElementById(selected_char);
    $(box_char).removeClass("charBox").addClass("charBoxHighlight");

    $("#teleport_to").fadeIn("slow");
    $("#teleport_to").html("Loading...");


    $.post("includes/scripts/character.php", {action: "getLocations", values: values},
        function (data)
        {
            $("#teleport_to").html(data);
        });
}

function portTo(locationTo, char_db, character)
{
    popUp("Confirm Teleport", "你确定要传送这个角色到 " + locationTo + "?<br/><br/>\
	<input type='button' value='Yes' onclick='portNow(\"" + character + "\",\"" + locationTo + "\",\"" + char_db + "\")'> \
	<input type='button' value='No' onclick='closePopup()'>");
}

function portNow(character, location, char_db)
{
    $.post("includes/scripts/character.php", {action: "teleport", character: character, location: location, char_db: char_db},
        function (data)
        {
            popUp("Character Teleport", data);
        });
}

function removeNewsComment(id)
{
    popUp("Remove comment", "您确定要删除此评论吗?<br/><br/>\
	<input type='button' value='Yes' onclick='removeNewsCommentNow(" + id + ")'> \
	<input type='button' value='No' onclick='closePopup()'>");
}

function removeNewsCommentNow(id)
{
    popUp("Remove comment", "Removing...");
    $.post("includes/scripts/misc.php", {action: "removeComment", id: id},
        function (data)
        {
            closePopup()
            $("#comment-" + id).fadeOut();
        });

}

function removeShopItem(entry, shop)
{
    popUp("Remove item", "您确定要删除该物品吗?<br/><br/>\
	<input type='button' value='Yes' onclick='removeShopItemNow(" + entry + ",\"" + shop + "\")'> \
	<input type='button' value='No' onclick='closePopup()'>");
    $("#popup").css("top", mouseY - 150);

}

function removeShopItemNow(entry, shop)
{
    popUp("Remove item", "Removing...");
    $.post("includes/scripts/shop.php", {action: "removeItem", entry: entry, shop: shop},
        function (data)
        {
            closePopup()
            $("#item-" + entry).fadeOut();
        });
}

function editShopItem(entry, shop, price)
{
    popUp("Edit item", "Price<br/><input type='text' id='edititem_price' value='" + price + "'><br/><br/>\
	<input type='submit' value='Save' onclick='editShopItemNow(" + entry + ",\"" + shop + "\")'");
    $("#popup").css("top", mouseY - 150);
}

function editShopItemNow(entry, shop)
{
    var price = document.getElementById("edititem_price").value;
    popUp("Edit item", "Saving...");

    $.post("includes/scripts/shop.php", {action: "editItem", entry: entry, shop: shop, price: price},
        function (data)
        {
            popUp("Edit item", "得救了!刷新页面以查看结果。");
            $("#popup").css("top", mouseY - 150);
        });
}