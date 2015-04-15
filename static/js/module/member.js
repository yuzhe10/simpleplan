/*
 * Project: simpleplan
 * File: member.js
 * Date: 21:47:24  2013-2-25
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

function memberInit() {
    memberDialogInit();
    memberCheck();
}

function memberCheck() {
    //Check session
    $.post(
            "action/controller.php?module=member&action=check",
            null,
            function(result) {
                if (result['result'] < 0) {
                    $("#logWin").dialog("open");
                } else if (result['result'] === 1) {
                    landing(result['member']);
                }
            },
            "json"
            );
}

function memberLogin() {
    $.post(
            "action/controller.php?module=member&action=log",
            {
                email: $("#logEmail").val(),
                password: $("#logPassword").val()
            },
    function(result) {
        if (result['result'] === 1) {
            $("#logWin").dialog("close");
            landing(result['member']);
        } else if (result['result'] === -1) {
            updateTips(lang.get('logError'));
        }
    },
            "json"
            );
}

function memberRegister() {
    $.post(
            "action/controller.php?module=member&action=reg",
            {
                name: userName.val(),
                email: regEmail.val(),
                password: regPassword.val()
            },
    function(result) {
        if (result['result'] === 1) {
            $("#regWin").dialog("close");
            landing(result['member']);
        } else if (result['result'] === -3) {
            updateTips(lang.get('maintaince'));
        } else {
            updateTips(lang.get('regError'));
        }
    },
            "json"
            );
}

function logOut() {
    $.post(
            "action/controller.php?module=member&action=logout",
            null,
            function(result) {
                if (result['result'] === 1) {
                    self.location = 'index.html';
                }
            },
            "json"
            );
}

function checkEmailExists(email) {
    $.post(
            "action/controller.php?module=member&action=check_email",
            {
                Email: email
            },
            function () {
                return function(result) {
                        if (result['result'] === 1) {
                            regEmail.addClass("ui-state-error");
                            updateTips(lang.get('emailExists'));
                        } else {
                            memberRegister();
                        }
                    };
            }(),
            "json"
            );
}

function updateProfile() {
    $.post(
            "action/controller.php?module=member&action=update_member",
            {
                profileUserName: profileUserName.val(),
                profileMSISDN: profileMSISDN.val(),
                originalPassword: originalPassword.val(),
                newPassword: newPassword.val()
            },
            function (result) {
                $("#profileWin").dialog("close");
                if (result['result'] === 1) {
                    if ($('#profile').text() !== result['member']['UserName']) {
                        $('#profile').text(result['member']['UserName']);
                    }
                } else if(result['result'] === -4) {
                    showMessage(lang.get('incorrectPassword'));
                } else {
                    showMessage(lang.get('updatedFailed'));
                }
            },
            "json"
            );
}

function landing(member) {
    $("#logOut").fadeIn('fast', function() {
        $(this).click(logOut);
        loadModule('plan');
    });
    profileUserName.val(member['UserName']);
    profileMSISDN.val(member['MSISDN']);
    profileEmail.val(member['Email']);
    $('#profile').fadeIn('fast', function() {
        $(this).text(member['UserName']);
        $(this).click(function() {
            $("#profileWin").dialog("open");
        });
    });
}

function memberDialogInit() {
    // log window
    var logTitle = lang.get('logTitle');
    $("#logWin").dialog({
        title:logTitle,
        dialogClass: "no-close",
        autoOpen: false,
        height: "auto",
        width: 300,
        modal: true,
        closeOnEscape: false,
        buttons: [{
                text: logTitle,
                click: function() {
                    var bValid = true;
                    allFields.removeClass( "ui-state-error" );
                    bValid = bValid && checkLength( logEmail, $("#logEmailLable").text(), 6, 60 );
                    bValid = bValid && checkLength( logPassword, $("#logPasswordLable").text(), 1, 40 );
                    bValid = bValid && checkRegexp( logEmail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, lang.get('wrongEmailFormat') );
                    if ( bValid ) {
                        memberLogin();
                    }
                }
            },
            {
                text: lang.get('regTitle'),
                click: function() {
                    $(this).dialog("close");
                    $("#regWin").dialog("open");
                }
            }],
        close: function() {
            cleanTips();
            allFields.val("").removeClass( "ui-state-error" );
        }
    });
    // register window
    var regTitle = lang.get('regTitle');
    $("#regWin").dialog({
        title: regTitle,
        dialogClass: "no-close",
        autoOpen: false,
        height: "auto",
        width: 300,
        modal: true,
        closeOnEscape: false,
        buttons: [{
                text: regTitle,
                click: function() {
                    var bValid = true;
                    allFields.removeClass( "ui-state-error" );
                    bValid = bValid && checkLength( userName, $("#userNameLabel").text(), 1, 20 );
                    bValid = bValid && checkLength( regEmail, $("#regEmailLabel").text(), 6, 60 );
                    bValid = bValid && checkLength( regPassword, $("#regPasswordLabel").text(), 1, 40 );
                    bValid = bValid && checkRegexp( regEmail, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, lang.get('wrongEmailFormat') );
                    bValid = bValid && checkConsistency(regPassword,regConfirmPassword,$("#regPasswordLabel").text());
                    if ( bValid ) {
                        checkEmailExists(regEmail.val());
                    }
                }
            },
            {
                text: logTitle,
                click: function() {
                    $(this).dialog("close");
                    $("#logWin").dialog("open");
                }
            }],
        close: function() {
            cleanTips();
            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });
    // profile window
    var profileTitle = lang.get('profileTitle');
    $("#profileWin").dialog({
        title: profileTitle,
        dialogClass: "no-close",
        autoOpen: false,
        height: "auto",
        width: 300,
        modal: true,
        buttons: [{
                text: lang.get('updateProfile'),
                click: function() {
                    var bValid = true;
                    allFields.removeClass( "ui-state-error" );
                    bValid = bValid && checkLength( profileUserName, $("#profileUserNameLabel").text(), 1, 20 );
                    if (profileMSISDN.val() !== '') {
                        bValid = bValid && checkLength( profileMSISDN, $("#profileMSISDNLabel").text(), 11, 11 );
                    }
                    if (newPassword.val() !== '' || confirmNewPassword.val() !== '') {
                        bValid = bValid && checkLength( originalPassword, $("#originalPasswordLabel").text(), 1, 40 );
                        bValid = bValid && checkLength( newPassword, $("#newPasswordLabel").text(), 1, 40 );
                        bValid = bValid && checkConsistency(newPassword,confirmNewPassword,$("#newPasswordLabel").text());
                    }
                    if ( bValid ) {
                        updateProfile();
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
        }
    });

    $("#changePasswordText").click(function() {
        $("#changePassword").slideToggle('normal', function() {
            $("#originalPassword").val('');
            $('#newPassword').val('');
            $('#confirmNewPassword').val('');
        });
    });
}

$(document).ready(memberInit);