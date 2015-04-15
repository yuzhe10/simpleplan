/*
 * Project:simpleplan
 * File:foot.js
 * Date:Mar 11, 2013
 * Time:16:41:32 PM
 * Encoding:UTF-8
 * Author:Wang.Yuzhe <yuzhewong@gmail.com>
 * Version:1.00
 * Description:
 */

function footerInit() {
    $("#footerLinks").append('<span id="blog">'+lang.get('blog')+'</span>');
    $("#blog").click(function(){
        window.open(lang.getConfig('blog'));
    });
}

$(document).ready(footerInit);