//checks if string ends with suffix
String.prototype.endsWith = function (suffix) {
    return this.indexOf(suffix, this.length - suffix.length) !== -1;
};

//gets outerHTML of node
jQuery.fn.outerHTML = function () {
    return jQuery('<div />').append(this.eq(0).clone()).html();
};

//gets base url (no slash at end)
function getbaseurl() {
    if (!window.location.origin)
        window.location.origin = window.location.protocol + "//" + window.location.host;
    return window.location.origin;
}

//converts byte (as int) to readable string (e. g. 32 mb)
function formatBytes(bytes) {
    if (bytes == 0) return '0 Byte';
    var k = 1000;
    var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
    var i = Math.floor(Math.log(bytes) / Math.log(k));
    return (bytes / Math.pow(k, i)).toPrecision(3) + ' ' + sizes[i];
}

//displays time nicely
function formatTime(time) {
    var values = [d.getHours(), d.getMinutes(), d.getSeconds()];
    for (var i = 0; i < values.length; i++) {
        while (values[i].toString().length < 2) {
            values[i] = "0" + values[i];
        }
    }
    return values[0] + "h" + values[1] + "m" + values[2] + "s";
}

function formatSeconds(sec_num) {
    var days = Math.floor(sec_num / (3600 * 24));
    var hours = Math.floor(sec_num / 3600) % 24;
    var minutes = Math.floor(sec_num / 60) % 60;
    var seconds = sec_num % 60;

    var time = "gerade eben";

    if (hours < 10) {
        hours = "0" + hours;
    }
    if (minutes < 10) {
        minutes = "0" + minutes;
    }
    if (seconds < 10) {
        seconds = "0" + seconds;
    }
    if (days > 0)
        time = days + "d " + hours + 'h ' + minutes + 'm ' + seconds + "s";
    else if (hours > 0)
        time = hours + 'h ' + minutes + 'm ' + seconds + "s";
    else if (minutes > 0)
        time = minutes + 'm ' + seconds + "s";
    else if (seconds > 3)
        time = seconds + "s";

    return time;
}

//searches table, hides row without matches
function searchTable(inputVal, tableid) {
    var table = $('#' + tableid);
    table.find('tr').each(function (index, row) {
        var allCells = $(row).find('td');
        if (allCells.length > 0) {
            var found = false;
            allCells.each(function (index, td) {
                var regExp = new RegExp(inputVal, 'i');
                if (regExp.test($(td).text())) {
                    found = true;
                    return false;
                }
            });
            if (found == true) $(row).show(); else $(row).hide();
        }
    });
}

//the next to functions display the loading bar
var loadingbarInterval;
function StartLoadingBar() {
    var bar = $("#loadingbar");
    bar.addClass("active");

    bar.css("width", "5%");
    loadingbarInterval = setInterval(function () {
        var currentWidth = bar.width();
        var stillavailable = $(document).width() - currentWidth;
        var newwidth = (stillavailable / 20) + currentWidth;
        bar.css("width", newwidth + "px");
    }, 200);
}

function StartPercentageLoadingBar() {
    var bar = $("#loadingbar");
    bar.addClass("active");

    bar.css("width", "0%");
}

function UpdatePercentageLoadingBar(percentage) {
    $("#loadingbar").css("width", percentage + "%");
}

function EndLoadingBar() {
    try {
        clearInterval(loadingbarInterval);
    }
    catch (e) {
    }

    var bar = $("#loadingbar");
    bar.addClass("finished");

    setTimeout(function () {
        var bar = $("#loadingbar");
        bar.removeClass("active");
        bar.removeClass("finished");
    }, 300);
}

//replaces html inside button to show a nice loading symbol
function makeLoadingButton($button) {
    $button.addClass("disabled");
    $button.prop("disabled", true);
}

//reverses makeLoadingButton()
function reverseMakeLoadingButton($button) {
    $button.removeClass("disabled");
    $button.prop("disabled", false);
}

//counts up till the element reached the max number defined in a data attribute
function countIt($element) {
    $element.html(parseInt($element.html()) + 1);
    if ($element.html() == $element.data("count")) {
        console.log("nextvalue == maxvalue " + $element.html() + "  " + $element.data("count"));
        return;
    }
    var nextcount = 1000 / ($element.data("count") - $element.html());
    if (nextcount < 1)
        nextcount = 1;
    setTimeout(function () {
        countIt($element);
    }, nextcount)
}


function optimizeModals() {
    //optimization for Modals
    if ($(".modal-backdrop.fade.in").length > 0 && $(".modal-dialog").length > 0) {
        var height = parseInt($(".modal-dialog").css("margin-top"), 10);
        height += parseInt($(".modal-dialog").css("margin-bottom"), 10);
        height += parseInt($(".modal-dialog").css("height"), 10);
        if (height > parseInt($(".modal-backdrop.fade.in").css("height"), 10)) {
            $(".modal-backdrop.fade.in").css("height", height + "px");
        }
    }
}

var MSG_TYPE = {
    USER_INFO: 1,
    USER_ERROR: 2,
    SYSTEM_ERROR: 3
};

function displayMessage(msg, type) {
    $.post( getbaseurl() + logUrl, { message: msg, loglevel: type })
        .done(function( data ) {
            var container = $('#tab-content');
            container.prepend(data);
            registerComponents(false);
        });
}

function checkLog() {
    $.ajax({
        method: "GET",
        url: getbaseurl() + logUrl,
        cache: false,
        success: function (data) {
            if (data != "") {
                var container = $('#tab-content');
                container.prepend(data);
                registerComponents(false);
            }
        }
    });
}