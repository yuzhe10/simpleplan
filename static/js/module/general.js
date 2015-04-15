/*
 * Project: simpleplan
 * File: general.js
 * Date: 2:11:20  2013-3-24
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

/*
 * general variables
 */
var userName = $("#userName"),
        logEmail = $("#logEmail"),
        logPassword = $("#logPassword"),
        regEmail = $("#regEmail"),
        regPassword = $("#regPassword"),
        regConfirmPassword = $("#regConfirmPassword"),
        planTitle = $("#planTitle"),
        planNote = $("#planNote"),
        categoryValue = $("#categoryValue"),
        profileUserName = $("#profileUserName"),
        profileEmail = $("#profileEmail"),
        profileMSISDN = $("#profileMSISDN"),
        originalPassword = $("#originalPassword"),
        newPassword = $("#newPassword"),
        confirmNewPassword = $("#confirmNewPassword"),
        allFields = $([]).add(userName).add(logEmail).add(logPassword).add(regEmail).add(regPassword).add(regConfirmPassword).add(planTitle).add(planNote).add(categoryValue).add(profileUserName).add(profileEmail).add(profileMSISDN).add(originalPassword).add(newPassword).add(confirmNewPassword),
        tips = $(".validateTips");

/*
 * general widget
 */
$("#message").dialog({
    title: lang.get('message'),
    autoOpen: false,
    dialogClass: "no-close",
    buttons: [{
        text: lang.get('close'),
        click: function() {
            $(this).dialog("close");
        }}],
    modal: true,
    show: "clip"
});