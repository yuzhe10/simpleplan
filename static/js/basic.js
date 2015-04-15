/*
 * Project:simpleplan
 * File:basic.js
 * Date:Feb 21, 2013
 * Time:3:11:32 PM
 * Encoding:UTF-8
 * Author:Wang.Yuzhe <yuzhewong@gmail.com>
 * Version:1.00
 * Description:
 */


$(document).ready(ready);

function ready() {
    // Set title
    setTitle();
    monitoringScroll();
    // Identify Browser
//    var browser = identifyBrowser();
//    if (browser.indexOf('ie') !== -1) {
//        alert(lang.get('browserNotSupport'));
//        self.location = lang.getConfig('redirection');
//    }
    // Load language
    lang.load();
    // Load general variable
    loadModule('general');
    // load member module
    loadModule('member');
    //Logo
    $("#logo").bind("click", function() {
        self.location = "index.html";
    });
    // Load footer
    loadModule('foot');
}


