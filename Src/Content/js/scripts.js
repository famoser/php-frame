var debug = true;
var modifiedContentInDialog = false;
var activeDialogId = null;
var logUrl = "/api/log";

function register() {
    if (debug) console.log("register() called");

    //register all components
    registerComponents();
}

function registerComponents(doCheckLog) {
    if (debug) console.log("registerComponents() called");

    if (doCheckLog != undefined || doCheckLog == true)
        checkLog();

    //AJAX Script für RefreshButtons
    $('.replacebutton').unbind('click').bind("click", function (e) {
        if (debug) console.log("'.refreshbutton' " + e.type + " event fired");
        e.preventDefault();

        var $button = $(this);
        if (button.hasClass("disabled"))
            return;
        makeLoadingButton($button);

        //get infos
        var id = '#' + $(this).data("replace-id");
        var link = $(this).data("replace-href") || $(this).attr('href');

        StartLoadingBar();
        $.ajax({
            url: link,
            cache: false,
            success: function (data) {
                EndLoadingBar();
                checkLog();
                if (data !== false) {
                    $(id).html(data);
                    registerComponents();
                }
                reverseMakeLoadingButton($button);
            },
            fail: function (e) {
                displayMessage(e.status + ': ' + e.statusText + '. Request for ' + link + ' failed', MSG_TYPE["SYSTEM_ERROR"]);
                EndLoadingBar();
                checkLog();
                reverseMakeLoadingButton($button);
            }
        });
    });

    //AJAX für ActionButtons
    $('.actionbutton').unbind('click').bind("click", function (e) {
        if (debug) console.log("'.actionbutton' " + e.type + " event fired");
        e.preventDefault();

        var button = $(this);
        makeLoadingButton($button);


        var link = button.attr("href") || button.data("href");
        if (activeDialogId != null)
            modifiedContentInDialog = true;

        StartLoadingBar();
        $.ajax({
            url: link,
            cache: false,
            success: function (data) {
                EndLoadingBar();
                checkLog();
            },
            fail: function (e) {
                displayMessage(e.status + ': ' + e.statusText + '. Request for ' + link + ' failed', MSG_TYPE["SYSTEM_ERROR"]);
                EndLoadingBar();
                checkLog();
            }
        });

        reverseMakeLoadingButton(button);
    });

    $(".menu-toggle").unbind('click').bind("click", function (e) {
        e.preventDefault();
        $("body").toggleClass("menu-expanded");
    });

    //remove element
    $('.removebutton').unbind('click').bind("click", function (e) {
        if (debug) console.log("'.removebutton' " + e.type + " event fired");
        e.preventDefault();
        var removeId = $(this).data("remove-id");
        if (removeId != undefined && removeId != "")
            $("#" + removeId).remove();

        var removeparent = $(this).data("remove-parent");
        if (removeparent != undefined && removeparent != "") {
            $item = $(this);
            for (i = 0; i < removeparent; i++) {
                $item = $item.parent();
            }
            $item.remove();
        }
    });

    //Creates the dialog boxes
    $('.dialogbutton').unbind('click').bind("click", function (e) {
        if (debug) console.log("'.dialogbutton' " + e.type + " event fired");
        e.preventDefault();

        var link = $(this).attr("href") || $(this).data("href");

        //Define Titles & Color
        var dialogtitle = $(this).data("dialog-title");
        var dialogkind = $(this).data("dialog-type");
        var dialogtype = BootstrapDialog.TYPE_DEFAULT;
        if (dialogkind == "success") {
            dialogtype = BootstrapDialog.TYPE_SUCCESS;
        }
        else if (dialogkind == "info") {
            dialogtype = BootstrapDialog.TYPE_INFO;
        }
        else if (dialogkind == "primary") {
            dialogtype = BootstrapDialog.TYPE_PRIMARY;
        }
        else if (dialogkind == "danger") {
            dialogtype = BootstrapDialog.TYPE_DANGER;
        }
        else if (dialogkind == "warning") {
            dialogtype = BootstrapDialog.TYPE_WARNING;
        }

        var dialogsiz = $(this).data("dialog-size");
        var dialogsize = BootstrapDialog.SIZE_NORMAL;
        if (dialogsiz == "large") {
            dialogsize = BootstrapDialog.SIZE_LARGE;
        }
        else if (dialogsiz == "small") {
            dialogsize = BootstrapDialog.SIZE_SMALL;
        }
        else if (dialogsiz == "wide") {
            dialogsize = BootstrapDialog.SIZE_WIDE;
        }

        //Create Buttons by using the properties of the linked button. Maximal number now is only one button, because var idbutton is defined globally.
        //to enable feature for unlimited number of buttons, we must store the id's in the dialog object
        var dialogbuttons = [];
        var counter = 0;
        var nextIdButton = $(this).data("dialog-idbutton" + counter);
        while (nextIdButton != null && nextIdButton != "") {
            var idbutton = $('#' + nextIdButton);
            if (idbutton.length > 0) {
                var buttonlabel = idbutton.text();
                var buttonclass = "btn-default";
                var classes = idbutton.attr('class').split(' ');
                for (var i = 0; i < classes.length; i++) {
                    if (classes[i].indexOf("btn-") === 0) {
                        buttonclass = classes[i];
                        break;
                    }
                }
                dialogbuttons.push({
                    label: buttonlabel,
                    //icon: 'glyphicon glyphicon-th-list',
                    cssClass: buttonclass,
                    clickOn: idbutton.attr("id"),
                    action: function (dialogItself) {
                        var $button = this;
                        dialogItself.close();
                        $("#" + $button.data("click-on")).trigger("click");
                    }
                })
            }
            else if (debug)
                console.log("button not found: " + '#' + idbutton);

            nextIdButton = $(this).data("dialog-idbutton" + ++counter)
        }

        //Close Button
        dialogbuttons.push({
            label: 'schliessen',
            action: function (dialogItself) {
                dialogItself.close();
            }
        });

        //Show BootstrapDialog
        BootstrapDialog.show({
            title: dialogtitle,
            type: dialogtype,
            size: dialogsize,
            //close by clicking on the side
            closeByBackdrop: true,
            //you may want to diable this, but it is awesome
            draggable: true,
            message: function (dialog) {
                var link = dialog.getData('pageToLoad');

                var content = $('<div data-refresh-url="' + link + '">wird geladen...</div>');
                var id = "dialog_0";
                if (activeDialogId != null) {
                    var split = activeDialogId.split("_");
                    id = "dialog_" + ++split[1];
                    content.attr("data-parent-dialog-id", activeDialogId);
                }
                content.attr("id", id);
                activeDialogId = id;

                StartLoadingBar();
                $.ajax({
                    url: link,
                    cache: false,
                    success: function (data) {
                        EndLoadingBar();
                        checkLog();
                        if (data !== false) {
                            var $dialog = $("#" + id);
                            if ($dialog.length > 0) {
                                $dialog.html(data);
                                registerComponents();
                            }
                            else {
                                var waitInterval = setInterval(function () {
                                    var $dialog = $("#" + id);
                                    if ($dialog.length > 0) {
                                        clearInterval(waitInterval);
                                        $dialog.html(data);
                                        registerComponents();
                                    }
                                }, 50);
                            }
                        }
                    },
                    fail: function (e) {
                        displayMessage(e.status + ': ' + e.statusText + '. Request for ' + link + ' failed', MSG_TYPE["SYSTEM_ERROR"]);
                        EndLoadingBar();
                        checkLog();
                    }
                });

                return content;
            },
            onhidden: function (dialogRef) {
                var $dialog = $("#" + activeDialogId);
                var parentid = $dialog.data("data-parent-dialog-id");
                if (parentid != "")
                    activeDialogId = parentid;
                else
                    activeDialogId = null;

                if (modifiedContentInDialog) {
                    if (activeDialogId != null) {
                        var $parentdialog = $("#" + parentid);
                        var link = $parentdialog.data("data-refreh-url");

                        StartLoadingBar();
                        $.ajax({
                            url: link,
                            cache: false,
                            success: function (data) {
                                EndLoadingBar();
                                checkLog();
                                if (data !== false) {
                                    $parentdialog.html(data);
                                    registerComponents();
                                }
                            },
                            fail: function (e) {
                                displayMessage(e.status + ': ' + e.statusText + '. Request for ' + link + ' failed', MSG_TYPE["SYSTEM_ERROR"]);
                                EndLoadingBar();
                                checkLog();
                            }
                        });

                    }
                    else {
                        modifiedContentInDialog = false;
                        location.reload();
                    }
                }
            },
            data: {
                'pageToLoad': link
            },
            buttons: dialogbuttons
        });
    });

    //catches form submit event and replaces the submited form with the response recieved
    $("form:not(.no-ajax)").unbind("submit").bind("submit", function (e) {
        if (debug) console.log("'form' " + e.type + " event fired");
        e.preventDefault();

        var $form = $(this);
        if ($form.hasClass("submitting"))
            return;
        $form.addClass("submitting");
        $(".loading-content", $form.parent()).addClass("show");

        var method = $(this).attr('method');
        var action = $(this).attr('action');
        var formData = new FormData($(this)[0]);

        var noreplace = $(this).hasClass('no-replace');
        if (noreplace)
            formData.append("no-replace", true);

        var clearaftersubmit = $(this).hasClass('clear-after-submit');
        var replaceElement = $form.parent();
        if ($(this).data("replace-self"))
            replaceElement = $form;
        if ($(this).data("replace-id") != undefined) {
            replaceElement = $(this).data("replace-id");
        }

        StartPercentageLoadingBar();
        $.ajax({
            type: method,
            url: action,
            data: formData,
            async: true,
            success: function (data) {
                $form.removeClass("submitting");
                $(".loading-content", $form.parent()).removeClass("show");
                if (!noreplace) {
                    replaceElement.replaceWith(data);
                } else {
                    if (clearaftersubmit) {
                        $(':input', $form)
                            .removeAttr('checked')
                            .removeAttr('selected')
                            .not(':button, :submit, :reset, :hidden, :radio, :checkbox')
                            .val('');
                    }
                }

                EndLoadingBar();
                registerComponents();
                modifiedContentInDialog = true;
            },
            cache: false,
            contentType: false,
            processData: false,
            xhr: function () {
                var xhr = new window.XMLHttpRequest();
                //Upload progress
                xhr.upload.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        UpdatePercentageLoadingBar(evt.loaded / evt.total * 100);
                    }
                }, false);
                //Download progress
                xhr.addEventListener("progress", function (evt) {
                    if (evt.lengthComputable) {
                        UpdatePercentageLoadingBar(evt.loaded / ev.total * 100);
                    }
                }, false);
                return xhr;
            }
        }).fail(function (e) {
            EndLoadingBar();
            displayMessage(e.status + ': ' + e.statusText + '. Submit of form ' + $(this).id + ' Failed', MSG_TYPE["SYSTEM_ERROR"]);

        });

        return false; // avoid to execute the actual submit of the form.
    });

    //make textarea adapt size
    $("textarea.interactive").autoGrow();

    //enable: enbaled and disabled linked element specified in data-id0, data-id1 attributes
    $("input[type=radio].enable, input[type=checkbox].enable").unbind("click").bind("click", function () {
        $("input[type=radio].enable, input[type=checkbox].enable").each(function () {
            var counter = 0;
            var disable = !$(this).is(':checked');
            var nextitem = $(this).data("id" + counter);
            while (nextitem != null && nextitem != "") {
                $("#" + nextitem).prop("disabled", disable);
                nextitem = $(this).data("id" + --counter);
            }
        });
    });

    /* FANCY DISPLAY */
    //counters
    $(".topnumberscounter.notstarted").each(function () {
        $(this).removeClass("notstarted");
        if ($(this).data("count") != "")
            countIt($(this));
    });


    $("input[type=datetime]").datetimepicker({
        lang: "de",
        format: 'd.m.Y H:i'
    });

    /* this is correctly implemented */
    $("input[type=date]").datetimepicker({
        lang: "de",
        format: 'Y-m-d',
        timepicker: false
    });

    $("input[type=time]").datetimepicker({
        lang: "de",
        format: 'H:i',
        datepicker: false
    });

    //initialize selectpickers
    $(".selectpicker").chosen();

    //enable sorting for tables
    $(".sortable").stupidtable();

    //enable searching in tables
    $('.searchinput').keyup(function () {
        var tableid = $(this).data("table-id");
        searchTable($(this).val(), tableid);
        $(this).attr("value", $(this).val());
    });

    /* INPUT CORRECTIONS */
    //correct user input
    $("input.forcelowercase").unbind("keyup").bind('keyup', function (e) {
        $(this).val(($(this).val()).toLowerCase());
    });

    //correct user input
    $("input.forceuppercase").unbind("keyup").bind('keyup', function (e) {
        $(this).val(($(this).val()).toUpperCase());
    });

    //do lazy loading of certain links / buttons
    $(".lazyload").each(function () {
        $(this).removeClass("lazyload");
        $(this).addClass("lazyloaded");
        $(this).trigger("click");
    });

    /* HELPERS */
    //hide or show the "back to top" link
    $(window).scroll(function () {
        ($(this).scrollTop() > 300 ) ? $top_arrow.addClass('arrow-is-visible') : $top_arrow.removeClass('arrow-is-visible arrow-fade-out');
        if ($(this).scrollTop() > 1200) {
            $('.arrow-top').addClass('arrow-fade-out');
        }
    });

    //smooth scroll to top
    $('.arrow-top').unbind('click').bind("click", function (event) {
        $('.arrow-top').removeClass('arrow-is-visible');
        event.preventDefault();
        $('body,html').animate({
                scrollTop: 0
            }, 300
        );
        return false;
    });
}