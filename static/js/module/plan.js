/*
 * Project: simpleplan
 * File: plan.js
 * Date: 23:11:02  2013-2-26
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

var lastDetached, currentCategory;

function planInit() {
    planDialogInit();
    planWidgetInit();
    loadMenu();
    loadCategory();
    loadPlan('init');
    recycleInit();
}

function recycleInit() {
    $("#recycle").fadeIn('fast', function() {
        $(this).attr('title', lang.get('recycle'));
    });
    $("#recycle").tooltip({
        show: {
            effect: "slideDown",
            delay: 200
        },
        position: {
            my: "left top",
            at: "left-25% top-35%"
        }
    });
    $("#recycle").droppable({
        accept: ".group",
        tolerance: "pointer",
        drop: function(event, ui) {
            ui.draggable.fadeOut('fast');
            recyclePlan(ui.draggable.attr('id'));
        }
    });
    $("#recycle").click(function() {
        setCurrentCategory('none');
        displayLoading();
        $.post(
                "action/controller.php?module=plan&action=load_recycles",
                null,
                function(result) {
                    if (result['result'] === 1) {
                        displayPlans(result['plans'], result['page'], result['total_pages']);
                    } else if (result['result'] === -1) {
                        cleanContents();
                    }
                    hideLoading();
                },
                "json"
                );
    });
}

function recyclePlan(PID) {
    if (typeof PID === 'undefined') {
        alert('recycle plan error');
        return;
    }
    var _PID = parseInt(PID);
    displayLoading();
    $.post(
            "action/controller.php?module=plan&action=recycle",
            {
                PID: _PID
            },
    function(result) {
        if (result['result'] === 1) {
            lastDetached = $("#" + _PID).detach();
        } else {
            $("#" + _PID).fadeIn('fast', function() {
                alert('recycle error');
            });
        }
        hideLoading();
    },
            "json"
            );
}

function loadMenu() {
    setCurrentCategory('undone');
    $("#menu").fadeIn('fast');
    $("#undone").click(function() {
        setCurrentCategory('undone');
        loadPlan('undone');
    });
    $("#done").click(function() {
        setCurrentCategory('done');
        loadPlan('done');
    });
}

function setCurrentCategory(category) {
    currentCategory = category;
    $("#menu > ul > li").each(function(index, element) {
        if ($(this).attr('id') === currentCategory || $(this).attr('title') === currentCategory) {
            $(this).css({
                'background-color': '#005EAC',
                'color': '#FFFFFF'
            });
        } else {
            $(this).css({
                'background-color': '',
                'color': ''
            });
        }
    });
}

/**
 * Load plans by specified param action
 * @param {string} action  it can be one of below list:
 * 1.init
 * 2.undone
 * 3.done
 * @returns {array} plans
 */
function loadPlan(action) {
    displayLoading();
    $.post(
            "action/controller.php?module=plan&action=load_" + action,
            null,
            function(result) {
                if (result['result'] === 1) {
                    displayPlans(result['plans'], result['page'], result['total_pages']);
                } else if (result['result'] === -1) {
                    cleanContents();
                }
                hideLoading();
            },
            "json"
            );
}

function displayPlans(plans, page, totalPages) {
    $("#content").empty();
    $('#pageNav').empty();
    var index, page = parseInt(page), totalPages = parseInt(totalPages);
    for (index in plans) {
        appendPlan(plans[index]);
    }
    // previous pager
    if (page > 1) {
        var prevPage = '<button id="prev_page">' + lang.get('prevPage') + '</button>';
        $('#pageNav').append(prevPage);
        $("#prev_page").button({text: true});
        paging('prev_page', page - 1);
    }
    // all pagers
    if (totalPages > 1) {
        for (var i = 1; i <= totalPages; i++) {
            var pager = '<button id="page_' + i + '">' + i + '</button>';
            $('#pageNav').append(pager);
            if (page === i) {
                $('#page_' + i).button({text: true, disabled: true});
            } else {
                $('#page_' + i).button({text: true});
            }
            paging('page_' + i, i);
        }
        $('#pageNav').show('fast');
    }
    // next pager
    if (page < totalPages) {
        var nextPage = '<button id="next_page">' + lang.get('nextPage') + '</button>';
        $('#pageNav').append(nextPage);
        $("#next_page").button({text: true});
        paging('next_page', page + 1);
    }
    resortPlans();
    $("#content").accordion("refresh");
}

function paging(bntId, page) {
    $('#' + bntId).click(function() {
        $.post(
                "action/controller.php?module=plan&action=paging",
                {
                    name: currentCategory,
                    page: page
                },
        function(result) {
            if (result['result'] === 1) {
                displayPlans(result['plans'], result['page'], result['total_pages']);
            } else {

            }
        },
                "json"
                );
    });
}

function sortPlan(PID, order) {
    var _order = parseInt(order);
    var _PID = parseInt(PID);
    if (!_PID) {
        return;
    }
    $.post(
            "action/controller.php?module=plan&action=sort",
            {
                PID: _PID,
                order: _order
            },
    function(result) {
        if (result['result'] === 1) {
            $("#order_" + _PID).val(result['Order']);
        } else if (result['result'] === -1) {
            $("#order_" + _PID).val(result['Order']);
        }
    },
            "json"
            );
}

function refresh() {
    $("#category").empty();
    $("#content").empty();
    loadCategory();
    loadPlan();
}

function loadCategory() {
    $.post(
            "action/controller.php?module=plan&action=load_category",
            null,
            function(result) {
                if (result['result'] === 1) {
                    for (index in result['categorys']) {
                        $("#category").append("<option value=\"" + result['categorys'][index]['Name'] + "\">" + result['categorys'][index]['Name'] + "</option>");
                        addCategoryToMenu(result['categorys'][index]['Name']);
                    }
                } else if (result['result'] === -1) {
                    var defaultCate = lang.get('defaultCate');
                    $("#category").append("<option value=\"" + defaultCate + "\">" + defaultCate + "</option>");
                }
            },
            "json"
            );
}

function addCategoryToMenu(name) {
    var display = '', id = $("#menu > ul > li").length + 1;
    if (strlen(name) > 8) {
        var strlength = 0, i = 0;
        while (strlength < 8) {
            if (isChinese(name.charAt(i)) === true) {
                strlength = strlength + 2;
            } else {
                strlength = strlength + 1;
            }
            display = display + name.slice(i, ++i);
        }
        display = display + '...';
    } else {
        display = name;
    }
    $("#menu > ul").append('<li id="cate_' + id + '">' + display + '</li>');
    $("#cate_" + id).attr('title', name);
    $("#cate_" + id).tooltip({
        show: {
            effect: "slideDown",
            delay: 200
        },
        position: {
            my: "left top",
            at: "left+50% top+100%"
        }
    });
    $("#cate_" + id).click(function() {
        displayLoading();
        $.post(
                "action/controller.php?module=plan&action=load_menu_plans",
                {
                    name: name
                },
        function(result) {
            if (result['result'] === 1) {
                displayPlans(result['plans'], result['page'], result['total_pages']);
            } else if (result['result'] === -1) {
                cleanContents();
            }
            setCurrentCategory(name);
            hideLoading();
        },
                "json"
                );
    });
}

function addPlan() {
    displayLoading();
    var category = $("#categoryValue").val() !== '' ? $("#categoryValue").val() : $("#category").val();
    var title = $("#planTitle").val(), note = $("#planNote").val(), eta = $("#eta").val(), order = $('#orderVal').val();
    $("#addPlanWin").dialog("close");
    $.post(
            "action/controller.php?module=plan&action=add",
            {
                title: title,
                note: note,
                category: category,
                eta: eta,
                order: order
            },
    function(result) {
        if (result['result'] === 1) {
            if (checkCategoryExists(category) === false) {
                $("#category").append("<option value=\"" + category + "\">" + category + "</option>");
                addCategoryToMenu(category);
            }
            if (currentCategory === category || currentCategory === 'undone') {
                appendPlan(result['plan']);
            }
            $("#content").accordion("refresh");
        }
        hideLoading();
    },
            "json"
            );
}

function checkCategoryExists(category) {
    var exists = false;
    $("#menu > ul > li").each(function(index, element) {
        return function() {
            if ($(this).text() === category || $(this).attr('title') === category) {
                exists = true;
            }
        };
    }());
    return exists;
}

function appendPlan(plan) {
    var action, PID = plan['PID'], done = parseInt(plan['Done']);
    /*
     *  Content
     */
    var content = '<div class="group" id="' + PID + '"></div>';
    if (done > 0) {
        $("#content").append(content);
    } else {
        $("#content").prepend(content);
    }
    /*
     *  Title
     */
    var titleContent = '', title = '', timeSpend = parseInt(plan['Spend']), status = parseInt(plan['Status']), eta = parseInt(plan['ETA']);
    var leftTitle = '';
    if(strlen(plan['Title']) > 30) {
        title = '<marquee class="tLeftArea" behavior="scroll" scrollamount="1" onmouseover="this.stop()"  onmouseout="this.start()">' + plan['Title'] + '</marquee>';
    } else {
        title = '<span class="tLeftArea">' + plan['Title'] + '</span>';
    }
    leftTitle += title;
    if (done > 0) {
        var allSpend = getTimesByElapsed(timeSpend);
        titleContent = '<h3 class="doneTitle" id="t_id_"' + PID + '">' + leftTitle + '<span class="doneText">' + lang.get('.doneText')
                + ':' + allSpend('days') + lang.get('day') + allSpend('hours') + lang.get('hour') + allSpend('minutes') + lang.get('minute')
                + '</span></h3>';
        action = 'undone';
    } else {
        var spend = getTimesByElapsed(timeSpend);
        var spendText = '';
        if (timeSpend > 0 && status === 1) { // display time elapsed if the status of plan is suspended
            spendText = '<span class="titleRight">' + lang.get('spend')
                    + ':' + spend('days') + lang.get('day') + spend('hours') + lang.get('hour') + spend('minutes') + lang.get('minute') + '</span>';
        }
        var priorityText = '<span class="titleRight">' + lang.get('priority') + ':<input class="priority" id="order_' + PID + '" value="' + plan['Order'] + '"/></span>';
        var rightTitle = '<div class="tRightArea">';
        if (spendText !== '') {
            rightTitle += '<marquee behavior="scroll" scrollamount="1" onmouseover="this.stop()"  onmouseout="this.start()">' + spendText + priorityText + '</marquee>';
        } else {
            rightTitle += priorityText;
        }
        rightTitle += '</div>';
        titleContent = '<h3 class="undoneTitle" id="t_id_' + PID + '">' + leftTitle + rightTitle + '</h3>';
        action = 'done';
    }
    $('#' + PID).append(titleContent);
    // tips
    if (eta > 0) {
        var d = new Date();
        var secRemaining = parseInt(eta - (d.getTime() / 1000));
        var timeRemainingTips = '';
        if (secRemaining >= 0) {
            var timeRemaining = getTimesByElapsed(secRemaining);
            timeRemainingTips = lang.get('timeRemainingTip') + ':' + timeRemaining('days') + lang.get('day') + timeRemaining('hours') + lang.get('hour') + timeRemaining('minutes') + lang.get('minute');
        } else {
            var timeRemaining = getTimesByElapsed(-secRemaining);
            timeRemainingTips = lang.get('timeOverdueTip') + ':' + timeRemaining('days') + lang.get('day') + timeRemaining('hours') + lang.get('hour') + timeRemaining('minutes') + lang.get('minute');
        }
        $('#t_id_' + PID).attr('title', timeRemainingTips);
        $('#t_id_' + PID).tooltip({
            show: {
                effect: "slideDown",
                delay: 200
            },
            position: {
                my: "right top",
                at: "right+25% top+100%"
            }
        });
    }
    $('#order_' + PID).attr('title', lang.get('clickToEdit'));
    $('#order_' + PID).tooltip({
        show: {
            effect: "slideDown",
            delay: 200
        },
        position: {
            my: "right top",
            at: "right+25% top+100%"
        }
    });
    $('#order_' + PID).click(function(event) {
        event.stopPropagation();
    });
    /*
     *  Note
     */
    var noteArea = '<div id="n_id_' + PID + '"></div>';
    $('#' + PID).append(noteArea);
    var note = '<textarea id="note_' + PID + '" class="noteArea" onclick="this.style.height=this.scrollHeight + \'px\'" onpropertychange="this.style.height=this.scrollHeight + \'px\'" oninput="this.style.height=this.scrollHeight + \'px\'">' + plan['Note'] + '</textarea>';
    $('#n_id_' + PID).append(note);
    /*
     *  Button Area
     */
    var btnArea = '<div id="b_a_' + PID + '"></div>';
    $('#n_id_' + PID).append(btnArea);
    /*
     *  Done Button
     */
    var recycle = parseInt(plan['Recycle']);
    if (recycle <= 0) {
        var doneBtn = '';
        if (done > 0) {
            doneBtn = '<button class="unDoneBtn" id="d_btn_' + PID + '">' + lang.get('.unDoneBtn') + '</button>';
        } else {
            doneBtn = '<button class="doneBtn" id="d_btn_' + PID + '">' + lang.get('.doneBtn') + '</button>';
        }
        $('#b_a_' + PID).append(doneBtn);
    }
    /*
     *  Delete Button
     */
    var delBtn = '';
    if (recycle > 0) {
        delBtn = '<button class="deleteBtn" id="del_btn_' + PID + '">' + lang.get('delete') + '</button>';
        $('#b_a_' + PID).append(delBtn);
    }
    /*
     *  ETA Button
     */
    var etaBtn = '';
    if (eta > 0 && recycle <= 0 && done <= 0) {
        var etaText = status === 0 ? lang.get('suspend') : lang.get('start');
        etaBtn = '<button class="etaBtn" id="eta_btn_' + PID + '">' + etaText + '</button>';
        $('#b_a_' + PID).append(etaBtn);
    }

    $("button").button({text: true});
    // response of  done or undone
    $("#d_btn_" + PID).click(function() {
        return function() {
            displayLoading();
            $.post(
                    "action/controller.php?module=plan&action=" + action,
                    {
                        PID: PID
                    },
            function(result) {
                if (result['result'] > 0) {
                    lastDetached = $("#" + PID).detach();
                    if (currentCategory !== 'undone' && currentCategory !== 'done') {
                        appendPlan(result['plan']);
                        $("#content").accordion("refresh");
                    }
                } else {
                    alert('failed');
                }
                hideLoading();
            },
                    "json"
                    );
        };
    }());
    // response of  ETA
    $("#eta_btn_" + PID).click(function() {
        return function() {
            displayLoading();
            $.post(
                    "action/controller.php?module=plan&action=eta",
                    {
                        PID: PID
                    },
            function(result) {
                if (result['result'] > 0) {
                    var etaBtnText = '';
                    if (parseInt(result['Status']) === 0) {
                        etaBtnText = lang.get('suspend');
                        $("#t_id_" + PID + " > .titleRight").remove();
                    } else {
                        etaBtnText = lang.get('start');
                        var spend = getTimesByElapsed(parseInt(result['Spend']));
                        var spendText = '<span class="titleRight">' + lang.get('spend')
                                + ':' + spend('days') + lang.get('day') + spend('hours') + lang.get('hour') + spend('minutes') + lang.get('minute') + '</span>';
                        $("#t_id_" + PID).append(spendText);
                    }
                    $("#eta_btn_" + PID).button("option", "label", etaBtnText);
                    $("#eta_btn_" + PID).button("refresh");
                } else {
                    alert('failed');
                }
                hideLoading();
            },
                    "json"
                    );
        };
    }());
    // response of delete action
    $("#del_btn_" + PID).click(function() {
        return function() {
            displayLoading();
            $.post(
                    "action/controller.php?module=plan&action=delete",
                    {
                        PID: PID
                    },
            function(result) {
                if (result['result'] > 0) {
                    lastDetached = $("#" + PID).detach();
                } else {
                    alert('failed');
                }
                hideLoading();
            },
                    "json"
                    );
        };
    }());
    // response of note editing
    $("#note_" + PID).blur(function() {
        return function() {
            $.post(
                    "action/controller.php?module=plan&action=update_note",
                    {
                        PID: PID,
                        note: $(this).val()
                    },
            function(result) {
                if (result['result'] > 0) {
                    $("#note_" + PID).addClass("updated");
                    setTimeout(function() {
                        $("#note_" + PID).removeClass("updated", 400);
                    }, 200);
                }
            },
                    "json"
                    );
        };
    }());
    // response of priority editing
    $("#order_" + PID).blur(function() {
        return function() {
            $.post(
                    "action/controller.php?module=plan&action=update_order",
                    {
                        PID: PID,
                        order: $(this).val()
                    },
            function(result) {
                if (result['result'] === 1) {
                    $("#order_" + PID).val(result['Order']);
                    sortPlanByPID(PID);
                    $("#content").accordion("refresh");
                } else if (result['result'] === -1) {
                    $("#order_" + PID).val(result['Order']);
                }
            },
                    "json"
                    );
        };
    }());

    sortPlanByPID(PID);
}

function sortPlanByPID(PID) {
    var orderSelector = 'h3 > .titleRight > input';
    var order = parseInt($('#' + PID).find(orderSelector).val());
    $('.group').each(function() {
        if ($(this).find(orderSelector).hasClass('priority')) {
            var _tempOrder = parseInt($(this).find(orderSelector).val());
            if (order <= _tempOrder && ($(this).attr('id') !== PID)) {
                $($('#' + PID).detach()).insertBefore($(this));
                return false;
            }
        } else {
            if ($(this).attr('id') !== PID) {
                $($('#' + PID).detach()).insertBefore($(this));
                return false;
            }
        }
    });
}

function resortPlans() {
    $('.group').each(function() {
        sortPlanByPID($(this).attr('id'));
    });
}

function cleanContents() {
    $("#content").empty();
    $('#pageNav').empty();
    $('#pageNav').hide();
}

function planDialogInit() {
    var title = lang.get('addPlanBoxTitle');
    $("#addPlanWin").dialog({
        title: title,
        dialogClass: "no-close",
        autoOpen: false,
        height: "auto",
        width: 350,
        modal: true,
        buttons: [{
                text: title,
                click: function() {
                    var bValid = true;
                    allFields.removeClass("ui-state-error");
                    bValid = bValid && checkEmpty(planTitle, $("#planTitleLable").text());
                    if (bValid) {
                        addPlan();
                    }
                }
            },
            {
                text: lang.get('cancelBtn'),
                click: function() {
                    $(this).dialog("close");
                }
            }],
        close: function() {
            cleanTips();
            allFields.val("").removeClass("ui-state-error");
        }
    });

    $("#addPlan").fadeIn('fast', function() {
        $(this).click(function() {
            $("#addPlanWin").dialog("open");
            $("#addCategory").click(function() {
                $("#category").fadeOut('fast', function() {
                    $("#categoryValue").fadeIn('fast');
                });
                $(this).fadeOut('fast', function() {
                    $("#selectCategory").fadeIn('fast', function() {
                        $(this).click(function() {
                            $("#categoryValue").fadeOut('fast', function() {
                                $(this).val('');
                                $("#category").fadeIn('fast');
                            });
                            $(this).fadeOut('fast', function() {
                                $("#addCategory").fadeIn('fast');
                            });
                        });
                    });
                });
            });
        });
    });

    $("#advancedText").text(lang.get('advanced'));
    $("#advancedText").click(function() {
        $("#advanced").slideToggle('normal', function() {
            $("#eta").val('');
            $('#orderVal').val('');
        });
    });
    $("#etaLable").text(lang.get('eta'));
    $("#eta").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        dayNames: [lang.get('sun'), lang.get('mon'), lang.get('tue'), lang.get('wed'), lang.get('thu'), lang.get('fri'), lang.get('sat')],
        dayNamesMin: [lang.get('sun'), lang.get('mon'), lang.get('tue'), lang.get('wed'), lang.get('thu'), lang.get('fri'), lang.get('sat')],
        monthNames: [lang.get('Jan'), lang.get('Feb'), lang.get('Mar'), lang.get('Apr'), lang.get('May'), lang.get('Jun'), lang.get('Jul'), lang.get('Aug'), lang.get('Sep'), lang.get('Oct'), lang.get('Nov'), lang.get('Dec')],
        monthNamesShort: [lang.get('Jan'), lang.get('Feb'), lang.get('Mar'), lang.get('Apr'), lang.get('May'), lang.get('Jun'), lang.get('Jul'), lang.get('Aug'), lang.get('Sep'), lang.get('Oct'), lang.get('Nov'), lang.get('Dec')],
        nextText: lang.get('nextMonth'),
        prevText: lang.get('prevMonth')
    });
    $('#orderValLable').text(lang.get('priority'));
}

function planWidgetInit() {
    $("#content").accordion({collapsible: true, heightStyle: "content", header: "> div > h3"}).sortable({
        handle: "h3",
        cursor: "move",
        opacity: 0.5,
        tolerance: "intersect",
        stop: function(event, ui) {
            sortPlan(ui.item.attr('id'), ui.offset.top);
        }
    });
}

$(document).ready(planInit);
