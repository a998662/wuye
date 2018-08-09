/*
 *  Document   :
 *  Author     : 胡卫兵 <6599986622@qq.com>
 */

/**
 * 处理ajax方式的post提交
 * @author 胡卫兵 <6599986622@qq.com>
 */
$('.ajax-post').click( function () {
    var msg, self = jQuery(this), ajax_url = self.attr("href") || self.data("url");
    var target_form = self.attr("target-form");
    var text = self.data('tips');
    var title = self.data('title') || '确定要执行该操作吗？';
    var confirm_btn = self.data('confirm') || '确定';
    var cancel_btn = self.data('cancel') || '取消';
    var form = jQuery('form[name=' + target_form + ']');
    if (form.length === 0) {
        form = jQuery('.' + target_form);
    }
    var form_data = form.serialize();

    if ("submit" === self.attr("type") || ajax_url) {
        // 不存在“.target-form”元素则返回false
        if (undefined === form.get(0)) return false;
        // 节点标签名为FORM表单
        if ("FORM" === form.get(0).nodeName) {
            ajax_url = ajax_url || form.get(0).action;

            // 提交确认
            if (self.hasClass('confirm')) {
                swal({
                    title: title,
                    text: text || '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d26a5c',
                    confirmButtonText: confirm_btn,
                    cancelButtonText: cancel_btn,
                    closeOnConfirm: true,
                    html: false
                }, function () {
                    pageLoader();
                    self.attr("autocomplete", "off").prop("disabled", true);

                    // 发送ajax请求
                    jQuery.post(ajax_url, form_data).success(function (res) {
                        pageLoader('hide');
                        msg = res.msg;
                        if (res.code) {
                            if (res.url && !self.hasClass("no-refresh")) {
                                msg += " 页面即将自动跳转~";
                            }
                            tips(msg, 'success');
                            setTimeout(function () {
                                self.attr("autocomplete", "on").prop("disabled", false);
                                // 关闭弹出框
                                if (res.data === '_close_pop' || res.data._close_pop) {
                                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                    parent.layer.close(index);
                                    return false;
                                }
                                // 刷新父窗口
                                if (res.data === '_parent_reload' || res.data._parent_reload) {
                                    parent.location.reload();
                                    return false;
                                }
                                return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                            }, 4000);
                        } else {
                            jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                            tips(msg, 'danger');
                            self.attr("autocomplete", "on").prop("disabled", false);
                        }
                    }).fail(function () {
                        pageLoader('hide');
                        tips('服务器发生错误~', 'danger');
                        self.attr("autocomplete", "on").prop("disabled", false);
                    });
                });
                return false;
            } else {
                self.attr("autocomplete", "off").prop("disabled", true);
            }
        } else if ("INPUT" === form.get(0).nodeName || "SELECT" === form.get(0).nodeName || "TEXTAREA" === form.get(0).nodeName) {
            // 如果是多选，则检查是否选择
            if (form.get(0).type === 'checkbox' && form_data === '') {
                Dolphin.notify('请选择要操作的数据', 'warning');
                return false;
            }

            // 提交确认
            if (self.hasClass('confirm')) {
                swal({
                    title: title,
                    text: text || '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d26a5c',
                    confirmButtonText: confirm_btn,
                    cancelButtonText: cancel_btn,
                    closeOnConfirm: true,
                    html: false
                }, function () {
                    pageLoader();
                    self.attr("autocomplete", "off").prop("disabled", true);

                    // 发送ajax请求
                    jQuery.post(ajax_url, form_data).success(function (res) {
                        pageLoader('hide');
                        msg = res.msg;
                        if (res.code) {
                            if (res.url && !self.hasClass("no-refresh")) {
                                msg += " 页面即将自动跳转~";
                            }
                            tips(msg, 'success');
                            setTimeout(function () {
                                self.attr("autocomplete", "on").prop("disabled", false);
                                // 关闭弹出框
                                if (res.data === '_close_pop' || res.data._close_pop) {
                                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                    parent.layer.close(index);
                                    return false;
                                }
                                // 刷新父窗口
                                if (res.data === '_parent_reload' || res.data._parent_reload) {
                                    parent.location.reload();
                                    return false;
                                }
                                return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                            }, 4000);
                        } else {
                            jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                            tips(msg, 'danger');
                            self.attr("autocomplete", "on").prop("disabled", false);
                        }
                    }).fail(function () {
                        pageLoader('hide');
                        tips('服务器发生错误~', 'danger');
                        self.attr("autocomplete", "on").prop("disabled", false);
                    });
                });
                return false;
            } else {
                self.attr("autocomplete", "off").prop("disabled", true);
            }
        } else {
            if (self.hasClass("confirm")) {
                swal({
                    title: title,
                    text: text || '',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d26a5c',
                    confirmButtonText: confirm_btn,
                    cancelButtonText: cancel_btn,
                    closeOnConfirm: true,
                    html: false
                }, function () {
                    pageLoader();
                    self.attr("autocomplete", "off").prop("disabled", true);
                    form_data = form.find("input,select,textarea").serialize();

                    // 发送ajax请求
                    jQuery.post(ajax_url, form_data).success(function (res) {
                        pageLoader('hide');
                        msg = res.msg;
                        if (res.code) {
                            if (res.url && !self.hasClass("no-refresh")) {
                                msg += " 页面即将自动跳转~";
                            }
                            tips(msg, 'success');
                            setTimeout(function () {
                                self.attr("autocomplete", "on").prop("disabled", false);
                                // 关闭弹出框
                                if (res.data === '_close_pop' || res.data._close_pop) {
                                    var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                                    parent.layer.close(index);
                                    return false;
                                }
                                // 刷新父窗口
                                if (res.data === '_parent_reload' || res.data._parent_reload) {
                                    parent.location.reload();
                                    return false;
                                }
                                return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                            }, 4000);
                        } else {
                            jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                            tips(msg, 'danger');
                            self.attr("autocomplete", "on").prop("disabled", false);
                        }
                    }).fail(function () {
                        pageLoader('hide');
                        tips('服务器发生错误~', 'danger');
                        self.attr("autocomplete", "on").prop("disabled", false);
                    });
                });
                return false;
            } else {
                form_data = form.find("input,select,textarea").serialize();
                self.attr("autocomplete", "off").prop("disabled", true);
            }
        }

        // 直接发送ajax请求
        pageLoader();
        jQuery.post(ajax_url, form_data).success(function (res) {
            pageLoader('hide');
            msg = res.msg;

            if (res.code) {
                if (res.url && !self.hasClass("no-refresh")) {
                    msg += "， 页面即将自动跳转~";
                }
                tips(msg, 'success');
                setTimeout(function () {
                    self.attr("autocomplete", "on").prop("disabled", false);
                    // 关闭弹出框
                    if (res.data === '_close_pop' || res.data._close_pop) {
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);
                        return false;
                    }
                    // 刷新父窗口
                    if (res.data === '_parent_reload' || res.data._parent_reload) {
                        parent.location.reload();
                        return false;
                    }
                    return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                }, 4000);
            } else {
                jQuery(".reload-verify").length > 0 && jQuery(".reload-verify").click();
                tips(msg, 'danger');
                self.attr("autocomplete", "on").prop("disabled", false);
            }
        }).fail(function () {
            pageLoader('hide');
            tips('服务器发生错误~', 'danger');
            self.attr("autocomplete", "on").prop("disabled", false);
        });
    }

    return false;
});


/**
 * 处理ajax方式的get提交
 * @author 胡卫兵 <6599986622@qq.com>
 */
$('.ajax-get').click(function () {
    var msg, self = $(this), text = self.data('tips'), ajax_url = self.attr("href") || self.data("url");
    var title = self.data('title') || '确定要执行该操作吗？';
    var confirm_btn = self.data('confirm') || '确定';
    var cancel_btn = self.data('cancel') || '取消';
    // 执行确认
    if (self.hasClass('confirm')) {
        swal({
            title: title,
            text: text || '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d26a5c',
            confirmButtonText: confirm_btn,
            cancelButtonText: cancel_btn,
            closeOnConfirm: true,
            html: false
        }, function () {
            pageLoader();
            self.attr("autocomplete", "off").prop("disabled", true);

            // 发送ajax请求
            jQuery.get(ajax_url).success(function (res) {
                pageLoader('hide');
                msg = res.msg;
                if (res.code) {
                    if (res.url && !self.hasClass("no-refresh")) {
                        msg += " 页面即将自动跳转~";
                    }
                    window.top.tips(msg, 'success');
                    setTimeout(function () {
                        self.attr("autocomplete", "on").prop("disabled", false);
                        // 关闭弹出框
                        if (res.data === '_close_pop' || res.data._close_pop) {
                            var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                            parent.layer.close(index);return false;
                        }
                        // 刷新父窗口
                        if (res.data === '_parent_reload' || res.data._parent_reload) {
                            parent.location.reload();return false;
                        }
                        return self.hasClass("no-refresh") ? false : void(res.url && !self.hasClass("no-forward") ? location.href = res.url : location.reload());
                    }, 4000);
                } else {
                    tips(msg, 'danger');
                    self.attr("autocomplete", "on").prop("disabled", false);
                }
            }).fail(function () {
                pageLoader('hide');
                tips('服务器发生错误~', 'danger');
                self.attr("autocomplete", "on").prop("disabled", false);
            });
        });
    } else {
        pageLoader();
        self.attr("autocomplete", "off").prop("disabled", true);

        // 发送ajax请求
        jQuery.get(ajax_url).success(function (res) {
            pageLoader('hide');
            msg = res.msg;
            if (res.code) {
                if (res.url && !self.hasClass("no-refresh")) {
                    msg += " 页面即将自动跳转~";
                }
                tips(msg, 'success');
                setTimeout(function () {
                    self.attr("autocomplete", "on").prop("disabled", false);
                    // 关闭弹出框
                    if (res.data === '_close_pop' || res.data._close_pop) {
                        var index = parent.layer.getFrameIndex(window.name); //获取窗口索引
                        parent.layer.close(index);return false;
                    }
                    // 刷新父窗口
                    if (res.data === '_parent_reload' || res.data._parent_reload) {
                        parent.location.reload();return false;
                    }
                    if(self.hasClass("no-refresh") == false){
                        if(res.url && !self.hasClass("no-forward")){
                            return location.href = res.url;
                        }else {
                            return location.reload();
                        }
                    }

                }, 4000);
            } else {
                tips(msg, 'danger');
                self.attr("autocomplete", "on").prop("disabled", false);
            }
        }).fail(function () {
            pageLoader('hide');
            tips('服务器发生错误~', 'danger');
            self.attr("autocomplete", "on").prop("disabled", false);
        });
    }

    return false;
});


/**
 * 处理普通方式的get提交
 * @author 胡卫兵 <6599986622@qq.com>
 */
jQuery(document).delegate('.js-get', 'click', function () {
    var self = $(this), text = self.data('tips'), url = self.attr("href") || self.data("url");
    var target_form = self.attr("target-form");
    var form = jQuery('form[name=' + target_form + ']');
    var form_data = form.serialize() || [];
    var title = self.data('title') || '确定要执行该操作吗？';
    var confirm_btn = self.data('confirm') || '确定';
    var cancel_btn = self.data('cancel') || '取消';

    if (form.length === 0) {
        form = jQuery('.' + target_form + '[type=checkbox]:checked');
        form.each(function () {
            form_data.push($(this).val());
        });
        form_data = form_data.join(',');
    }

    if (form_data === '') {
        tips('请选择要操作的数据', 'warning');
        return false;
    }

    if (url.indexOf('?') !== -1) {
        url += '&' + target_form + '=' + form_data;
    } else {
        url += '?' + target_form + '=' + form_data;
    }

    // 执行确认
    if (self.hasClass('confirm')) {
        swal({
            title: title,
            text: text || '',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d26a5c',
            confirmButtonText: confirm_btn,
            cancelButtonText: cancel_btn,
            closeOnConfirm: true,
            html: false
        }, function () {
            location.href = url;
        });
    } else {
        location.href = url;
    }

    return false;
});


$('.pop').click(function () {
    var $url = $(this).attr('href');
    var $title = $(this).attr('title') || $(this).data('original-title');
    var width = $(this).data('width');
    var height = $(this).data('height');

    width = width ? width : '80%';
    height = height ? height : '90%';

    layer.open({type: 2, title: $title, area: [width, height], content: $url});
    return false;
});


function in_array(search,array){
    for(var i in array){
        if(array[i]==search){
            return true;
        }
    }
    return false;
}
