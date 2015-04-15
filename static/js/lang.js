/*
 * Project: simpleplan
 * File: lang.js
 * Date: 15:09:12  2013-2-24
 * Encoding: UTF-8
 * Version: 1.00
 * Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013
 */

// global
var localLang;// lower case
var lang;// language object
// chinese
var zhcn = new Array(
        ['logTitle', '登录'],
        ['#logEmailLable','邮箱'],
        ['#logPasswordLable','密码'],
        ['regTitle', '注册'],
        ['profileTitle', '个人资料'],
        ['#userNameLabel','用户名'],
        ['#regEmailLabel', '邮箱'],
        ['wrongEmailFormat','邮箱地址格式不正确'],
        ['#regPasswordLabel', '密码'],
        ['#regConfirmPasswordLabel', '密码确认'],
        ['#profileUserNameLabel', '用户名'],
        ['#profileEmailLabel', '邮箱'],
        ['#profileMSISDNLabel', '手机号'],
        ['#changePasswordText', '修改密码'],
        ['#originalPasswordLabel', '旧密码'],
        ['#newPasswordLabel', '新密码'],
        ['#confirmNewPasswordLabel', '确认新密码'],
        ['updateProfile', '更新'],
        ['#logOut', '退出'],
        ['#addPlan', '添加计划'],
        ['addPlanBoxTitle', '添加计划'],
        ['#planTitleLable', '计划'],
        ['#planNoteLable', '备注'],
        ['#categoryLabel', '类别'],
        ['#addCategory', '添加类别'],
        ['#addPlanBtn', '添加计划'],
        ['cancelBtn', '取消'],
        ['#selectCategory', '选择类别'],
        ['#undone', '未完成'],
        ['#done', '已完成'],
        ['.unDoneBtn', '未完成'],
        ['.doneBtn', '完成'],
        ['defaultCate', '我的计划'],
        ['.doneText', '总用时'],
        ['day', '天'],
        ['hour', '小时'],
        ['minute', '分钟'],
        ['browserNotSupport', '对不起，暂不支持该浏览器'],
        ['blog', '博客'],
        ['emailExists', '该用户已存在'],
        ['logError', '用户名或密码错误'],
        ['maintaince', '网站正在开发中'],
        ['regError', '注册失败'],
        ['recycle', '回收站'],
        ['delete', '删除'],
        ['lengthTip','长度范围 '],
        ['inconformity','不一致'],
        ['noEmpty','不能为空'],
        ['close','关闭'],
        ['advanced','>>>可选'],
        ['eta','预计完成时间'],
        ['sun','周日'],
        ['mon','周一'],
        ['tue','周二'],
        ['wed','周三'],
        ['thu','周四'],
        ['fri','周五'],
        ['sat','周六'],
        ['Jan','一月'],
        ['Feb','二月'],
        ['Mar','三月'],
        ['Apr','四月'],
        ['May','五月'],
        ['Jun','六月'],
        ['Jul','七月'],
        ['Aug','八月'],
        ['Sep','九月'],
        ['Oct','十月'],
        ['Nov','十一月'],
        ['Dec','十二月'],
        ['nextMonth','下一月'],
        ['prevMonth','上一月'],
        ['suspend','暂停'],
        ['start','开始'],
        ['spend','已用时'],
        ['timeRemainingTip','离预计完成时间'],
        ['timeOverdueTip','超过预期时间'],
        ['priority','优先级'],
        ['clickToEdit','点击编辑'],
        ['prevPage','上一页'],
        ['nextPage','下一页'],
        ['priority','优先级'],
        ['message','消息'],
        ['incorrectPassword','密码错误'],
        ['updatedFailed','更新失败'],
        ['#copyright', 'Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013']
        );

// english
var enus = new Array(
        ['#logTitle', 'Login'],
        ['#logBtn', 'Login'],
        ['#logEmailLable', 'Email'],
        ['#logPasswordLable', 'Password'],
        ['#regTitle', 'Register'],
        ['#regEmailLabel', 'Email'],
        ['#regPasswordLabel', 'Password'],
        ['#regBtn', 'Register'],
        ['#toReg', 'Register'],
        ['#toLog', 'Login'],
        ['#regConfirmPasswordLabel', 'Confirmation'],
        ['#logOut', 'Logout'],
        ['#addPlan', 'New Plan'],
        ['#addPlanBoxTitle', 'New Plan'],
        ['#planTitleLabel', 'Plan'],
        ['#planNoteLabel', 'Note'],
        ['#categoryLabel', 'Category'],
        ['#addCategory', 'Add'],
        ['#addPlanBtn', 'Add Plan'],
        ['#cancelAddPlanBtn', 'Cancel'],
        ['#selectCategory', 'Select'],
        ['#undone', 'Undone'],
        ['#done', 'Done'],
        ['.unDoneBtn', 'Undone'],
        ['.doneBtn', 'Done'],
        ['defaultCate', 'My Plan'],
        ['.doneText', 'Last'],
        ['day', 'Day(s)'],
        ['hour', 'Hour(s)'],
        ['minute', 'Minute(s)'],
        ['browserNotSupport', 'Sorry, IE is not supported'],
        ['blog', 'Blog'],
        ['emailExists', 'Exists'],
        ['recycle', 'Recycle'],
        ['delete', 'Delete'],
        ['#copyright', 'Copyright (C) Wang Yu Zhe <yuzhewong@gmail.com> 2013']
        );

enus = zhcn; // staging, use en instead of chinese while prodution

// config
var config = new Array(
        ['redirection', 'https://www.google.com'],
        ['blog', 'http://swincle.iteye.com/category/270056']
        );

function langInit() {
    localLang = identifyBrowserLanguage();
    lang = new Lang();
    lang.load();
}

var Lang = function() {
    this._local = eval(localLang);
    this._config = config;
};
Lang.prototype = {
    constructor: Lang,
    load: function() {
        if (this._local.length === 0 && typeof this._local !== 'object') {
            alert('Unable recognize local language');
            return;
        }
        for (index in this._local) {
            $(this._local[index][0]).text(this._local[index][1]);
        }
    },
    get: function(key) {
        for (index in this._local) {
            if (this._local[index][0] === key) {
                return this._local[index][1];
            }
        }
        return false;
    },
    getConfig: function(key) {
        for (index in this._config) {
            if (this._config[index][0] === key) {
                return this._config[index][1];
            }
        }
        return false;
    }
};

$(document).ready(langInit);