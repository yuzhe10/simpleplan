/*
 * Project: simpleplan
 * File: lib.js
 * Date: 14:44:56  2013-2-24
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

function identifyBrowser() {
    var agent = navigator.userAgent.toLowerCase();

    if (typeof navigator.vendor !== 'undefined' && navigator.vendor === 'KDE' && typeof window.sidebar !== 'undefined') {
        return "kde";
    } else if (typeof window.opera !== "undefined") {
        var version = parseFloat(agent.replace(/.*opera[\/]([^ $]+).*/, "$1"));
        if (version >= 7) {
            return "opera7";
        } else if (version >= 5) {
            return "opera5";
        }
        return false;
    } else if (typeof document.all !== 'undefined') {
        if (typeof document.getElementById !== 'undefined') {
            var browser = agent.replace(/.*ms(ie[\/ ][^ $]+).*/, "$1").replace(/ /, "");
            if (typeof document.uniqueID !== 'undefined') {
                if (browser.indexOf("5.5") !== -1) {
                    return browser.replace(/(.*\.5).*/, "$1");
                } else {
                    return browser.replace(/(.*)\..*/, "$1");
                }
            } else {
                return "ie5mac";
            }
        }
        return false;
    } else if (typeof document.getElementById !== 'undefined') {
        if (navigator.vendor.indexOf("Apple Computer,Inc.") !== -1) {
            if (typeof window.XMLHttpRequest !== 'undefined') {
                return "safari1.2";
            }
            return "safari1";
        } else if (agent.indexOf("gecko") !== -1) {
            return "mozilla";
        }
    }
    return false;
}

function identifyOS() {
    var agent = navigator.userAgent.toLowerCase();
    if (agent.indexOf("win") !== -1) {
        return "win";
    } else if (agent.indexOf("mac") !== -1) {
        return "mac";
    } else {
        return "unix";
    }
    return false;
}

function identifyBrowserLanguage() {
    return ((navigator.language || navigator.browserLanguage).toLowerCase()).replace(/-/g, '');
}

function getHttpRequest() {
    if (window.XMLHttpRequest) // Gecko
        return new XMLHttpRequest();
    else if (window.ActiveXObject) // IE
        return new ActiveXObject("MsXml2.XmlHttp");
}

function ajaxLoad(sId, url) {
    var oXmlHttp = getHttpRequest();
    oXmlHttp.onreadystatechange = function() {
        if (oXmlHttp.readyState === 4) {
            if (oXmlHttp.status === 200 || oXmlHttp.status === 304) {
                includeJS(sId, url, oXmlHttp.responseText);
            } else {
                alert('XML request error: ' + oXmlHttp.statusText + ' (' + oXmlHttp.status + ')');
            }
        }
    };
    oXmlHttp.open('GET', url, true);
    oXmlHttp.send(null);
}

function includeJS(sId, fileUrl, source) {
    if ((source !== null) && (!document.getElementById(sId))) {
        var oHead = document.getElementsByTagName('HEAD').item(0);
        var oScript = document.createElement("script");
        oScript.language = "javascript";
//        oScript.type = "text/javascript";
        oScript.id = sId;
//        oScript.defer = true;
        oScript.text = source;
        oScript.src = fileUrl;
        oHead.appendChild(oScript);
    }
}

function loadModule(name) {
    if (name !== '') {
        var path = './static/js/module/' + name + '.js';
        ajaxLoad(name, path);
    }
}

function displayLoading() {
    $("#loading").fadeIn('fast');
}

function hideLoading() {
    $("#loading").fadeOut('fast');
}

function setTitle(title) {
    var _title = 'Simple Plan';
    if (typeof title !== 'undefined') {
        _title = _title + ' - ' + title;
    }
    document.title = _title;
}

function monitoringScroll() {
    $(document).scroll(function() {
        if ($(this).scrollTop() >= ($(this).height() - $(window).height())) {
            $("#footer").slideUp();
        } else {
            $("#footer").slideDown();
        }
    });

}

function checkEmail(email) {
    var checkMail = /\w@\w*\.\w/;
    if (checkMail.test(email)) {
        return true;
    }
    return false;
}


function updateTips(t) {
    tips
            .text(t)
            .addClass("ui-state-highlight");
    setTimeout(function() {
        tips.removeClass("ui-state-highlight", 1500);
    }, 500);
}
function cleanTips() {
    tips.removeClass();
    tips.text('');
}
function checkLength(o, n, min, max) {
    if (o.val().length > max || o.val().length < min) {
        o.addClass("ui-state-error");
        updateTips(n + lang.get('lengthTip') + min + '～' + max);
        return false;
    } else {
        return true;
    }
}
function checkRegexp(o, regexp, n) {
    if (!(regexp.test(o.val()))) {
        o.addClass("ui-state-error");
        updateTips(n);
        return false;
    } else {
        return true;
    }
}
function checkConsistency(o1, o2, n) {
    if (o1.val() !== o2.val()) {
        o1.addClass("ui-state-error");
        o2.addClass("ui-state-error");
        updateTips(n + lang.get('inconformity'));
        return false;
    } else {
        return true;
    }
}


function checkEmpty(o, n) {
    if (o.val().length === 0) {
        o.addClass("ui-state-error");
        updateTips(n + lang.get('noEmpty'));
        return false;
    } else {
        return true;
    }
}

function isChinese(str) {
    var lst = /[u00-uFF]/;
    return !lst.test(str);
}

function showMessage(message,title) {
    $( "#message" ).text(message);
    $( "#message" ).dialog( "option", "title", title );
    $( "#message" ).dialog("open");
}

function strlen(str) {
    var strlength = 0;
    for (i = 0; i < str.length; i++) {
        if (isChinese(str.charAt(i)) === true)
            strlength = strlength + 2;
        else
            strlength = strlength + 1;
    }
    return strlength;
}

function getTimesByElapsed(start, end) {
    var elapsed = parseInt(typeof end === 'undefined' ? start : end - start); // start is time elapsed when just passed in on param
    var secPerMin = 60;
    var secPerHour = secPerMin * 60;
    var secPerDay = secPerHour * 24;
    var days = parseInt(elapsed / secPerDay);
    var hours = parseInt((elapsed % secPerDay) / secPerHour);
    var minutes = parseInt(((elapsed % secPerDay) % secPerHour) / secPerMin);
    var times = [
        ['days',days],
        ['hours',hours],
        ['minutes',minutes]
    ];
    return function(key) {
        for (index in times) {
            if (times[index][0] === key) {
                return times[index][1];
            }
        }
    };
}
