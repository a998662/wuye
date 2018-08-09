/*
 *  Document   : base_comp_calendar.js
 *  Author     : pixelcave
 *  Description: Custom JS code used in Calendar Page
 */

var BaseCompCalendar = function () {
    // Init drag and drop event functionality
    var initEvents = function () {
        jQuery('.js-events').find('li').each(function () {
            var $event = jQuery(this);

            // create an Event Object
            var $eventObject = {
                title: jQuery.trim($event.text()),
                color: $event.css('background-color')
            };

            // store the Event Object in the DOM element so we can get to it later
            jQuery(this).data('eventObject', $eventObject);

            // make the event draggable using jQuery UI
            jQuery(this).draggable({
                zIndex        : 999,
                revert        : true,
                revertDuration: 0
            });
        });
    };

    // Init FullCalendar
    var initCalendar = function () {
        var $date = new Date();
        var $d = $date.getDate();
        var $m = $date.getMonth();
        var $y = $date.getFullYear();

        jQuery('.js-calendar').fullCalendar({
            customButtons: {
                button1: {
                    text : "新建",
                    click: function () {
                        $(".datepicker").datepicker({
                            language      : "zh-cn",
                            format        : "yyyy-mm-dd",
                            todayHighlight: true,
                            autoclose     : true,
                            weekStart     : 0
                        });
                        $(".timepicki").wickedpicker({
                            title      : '',
                            showSeconds: true,
                            twentyFour : true
                        });
                        $("#isallday").click(function () {
                            if ($("#isallday").prop("checked") == true) {
                                $("#starttime,#endtime").hide();
                            } else {
                                $("#starttime,#endtime").show();
                            }
                            ;
                        });
                        $("#end").click(function () {
                            if ($("#end").prop("checked") == true) {
                                $("#enddate,#endtime,#starttime").show();
                            } else {
                                $("#enddate").hide();
                            }
                            ;
                        });
                        $("#repeat").click(function () {
                            if ($("#repeat").prop("checked") == true) {
                                $("#repeattype,#repeattime").show();
                            } else {
                                $("#repeattype,#repeattime").hide();
                            }
                            ;
                        });
                        $("#repeatselect").change(function () {
                            switch ($("#repeatselect").val()) {
                                case "1":
                                    $("#repeatclock").show();
                                    $("#repeatmonth,#repeatweek,#repeatday").hide();
                                    break;
                                case "2":
                                    $("#repeatmonth,#repeatday").hide();
                                    $("#repeatweek,#repeatclock").show();
                                    break;
                                case "3":
                                    $("#repeatmonth,#repeatweek").hide();
                                    $("#repeatday,#repeatclock").show();
                                    break;
                                case "4":
                                    $("#repeatweek").hide();
                                    $("#repeatmonth,#repeatday,#repeatclock").show();
                                    break;
                                case "5":
                                    $("#repeatclock").show();
                                    $("#repeatmonth,#repeatweek,#repeatday").hide();
                                    break;
                            }
                        });
                        dialog({
                            title      : "新建日程",
                            content    : $("#dialog-form"),
                            okValue    : "确定",
                            ok         : function () {
                                var titledetail = $("#titledetail").val();
                                var startdate = $("#startdate").val();
                                var starttime = $("#starttime").val().split(" ").join("");
                                var enddate = $("#stopdate").val();
                                var endtime = $("#endtime").val().split(" ").join("");
                                var allDay = $("#isallday").val();
                                if (titledetail) {
                                    $.ajax({
                                        url     : edit_url,
                                        data    : {
                                            title : titledetail,
                                            sdate : startdate,
                                            stime : starttime,
                                            edate : enddate,
                                            etime : endtime,
                                            allDay: allDay
                                        },
                                        type    : 'POST',
                                        dataType: 'json',
                                        success : function (data) {
                                            $("#calendar").fullCalendar("renderEvent", data, true);
                                        },
                                        error   : function () {
                                            alert("Failed");
                                        }

                                    });
                                }
                                ;
                            },
                            cancelValue: "关闭",
                            cancel     : function () {
                                //$("#ui-datepicker-div").remove();
                            }
                        }).showModal();
                    }
                },
            },
            locale       : 'zh-cn',
            buttonText   : {
                today    : '今天',
                month    : '月视图',
                week     : '周视图',
                day      : '日视图',
                listMonth: "列表"
            },

            header   : {
                left  : 'prev,next today',
                center: 'title',
                right : 'button1 month,agendaWeek,agendaDay,listMonth'
            },
            firstDay : 1,
            editable : true,
            droppable: true,

            drop      : function ($date, $allDay) { // this function is called when something is dropped
                // retrieve the dropped element's stored Event Object
                var $originalEventObject = jQuery(this).data('eventObject');

                // we need to copy it, so that multiple events don't have a reference to the same object
                var $copiedEventObject = jQuery.extend({}, $originalEventObject);

                // assign it the date that was reported
                $copiedEventObject.start = $date;

                // render the event on the calendar
                // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
                jQuery('.js-calendar').fullCalendar('renderEvent', $copiedEventObject, true);

                // remove the element from the "Draggable Events" list
                jQuery(this).remove();
            },
            dayClick  : function (date, allDay, jsEvent, view) {
                var selDate = $.fullCalendar.formatDate(date, "YYYY-MM-DD");
                $(".datepicker").datepicker({
                    language      : "zh-CN",
                    format        : "yyyy-mm-dd",
                    todayHighlight: true,
                    autoclose     : true,
                    weekStart     : 0
                });

                $(".timepicki").wickedpicker({
                    // now: time,
                    title      : '',
                    showSeconds: true,
                    twentyFour : true
                });
                $("#isallday").click(function () {
                    if ($("#isallday").prop("checked") == true) {
                        $("#starttime,#endtime,#enddate").hide();
                    } else {
                        $("#starttime,#endtime").show();
                    }
                    ;
                });
                $("#end").click(function () {
                    if ($("#end").prop("checked") == true) {
                        $("#enddate,#starttime,#endtime").show();
                    } else {
                        $("#enddate").hide();
                    }
                    ;
                });
                $("#repeat").click(function () {
                    if ($("#repeat").prop("checked") == true) {
                        $("#repeattype,#repeattime").show();
                    } else {
                        $("#repeattype,#repeattime").hide();
                    }
                    ;
                });
                $("#repeatselect").change(function () {
                    switch ($("#repeatselect").val()) {
                        case "1":
                            $("#repeatclock").show();
                            $("#repeatmonth,#repeatweek,#repeatday").hide();
                            break;
                        case "2":
                            $("#repeatmonth,#repeatday,#repeatclock").hide();
                            $("#repeatweek").show();
                            break;
                        case "3":
                            $("#repeatmonth,#repeatweek,#repeatclock").hide();
                            $("#repeatday").show();
                            break;
                        case "4":
                            $("#repeatweek,#repeatclock").hide();
                            $("#repeatmonth,#repeatday").show();
                            break;
                        case "5":
                            $("#repeatclock").show();
                            $("#repeatmonth,#repeatweek,#repeatday").hide();
                            break;
                    }
                });
                var d = dialog({
                    title      : "新建日程",
                    content    : $("#dialog-form"),
                    okValue    : "确定",
                    ok         : function () {
                        var title = $("#calendar_title").val();
                        var allDay = $("#isallday").prop("checked") == true ? 1 : 0;
                        var content = $("#calendar_detail").val();
                        var startdate = $("#startdate").val();
                        var type = $("#type").val();
                        var status = $("#status").val();
                        var is_end = $("#end").prop("checked") == true ? 1 : 0;

                        if (!allDay) {
                            var starttime = allDay ? '00:00:00' : $("#starttime").val().split(" ").join("");
                            var enddate = $("#stopdate").val();
                            var endtime = allDay ? '23:59:59' : $("#endtime").val().split(" ").join("");
                            var end = enddate + ' ' + endtime;
                            var start = startdate + ' ' + starttime;
                        } else {
                            var start = startdate + ' 00:00:00';
                        }

                        if (!title) {
                            tips('标题不能为空~', 'danger');
                            return false;
                        }
                        if (!startdate) {
                            tips('开始时间不能为空~', 'danger');
                            return false;
                        }
                        if (!enddate && is_end) {
                            tips('结束时间不能为空~', 'danger');
                            return false;
                        }
                        if (!title) {
                            tips('内容不能为空~', 'danger');
                            return false;
                        }

                        var data = {
                            title  : title,
                            content: content,
                            start  : start,
                            end    : end,
                            allDay : allDay,
                            is_end : is_end,
                            type   : type,
                            status : status,
                        };
                        console.log(data);

                        $.ajax({
                            url     : add_url,
                            data    : data,
                            type    : 'POST',
                            dataType: 'json',
                            success : function (res) {
                                if (res.code == 1) {
                                    var data = res.data;
                                    $("#calendar").fullCalendar("renderEvent", data, true);
                                } else {
                                    tips(res.msg, 'dangar');
                                }
                            },
                            error   : function () {
                                alert("Failed");
                            }

                        });


                    },
                    cancelValue: "关闭",
                    cancel     : function () {
                    }
                }).showModal();

            },
            eventClick: function (event, jsEvent, view) {

                $(".datepicker").datepicker({
                    language      : "zh-CN",
                    format        : "yyyy-mm-dd",
                    todayHighlight: true,
                    autoclose     : true,
                    weekStart     : 0
                });

                $(".timepicki").wickedpicker({
                    // now: time,
                    title      : '',
                    showSeconds: true,
                    twentyFour : true
                });
                $("#isallday").click(function () {
                    if ($("#isallday").prop("checked") == true) {
                        $('#end').attr('checked', false);
                        $("#starttime,#endtime,#enddate").hide();
                    } else {
                        $("#starttime,#endtime").show();
                    }
                    ;
                });
                $("#end").click(function () {
                    if ($("#end").prop("checked") == true) {
                        $("#starttime,#endtime").show();
                        $("#enddate").show();
                    } else {
                        $("#enddate").hide();
                    };
                });
                $("#repeat").click(function () {
                    if ($("#repeat").prop("checked") == true) {
                        $("#repeattype,#repeattime").show();
                    } else {
                        $("#repeattype,#repeattime").hide();
                    };
                });
                $("#repeatselect").change(function () {
                    switch ($("#repeatselect").val()) {
                        case "1":
                            $("#repeatclock").show();
                            $("#repeatmonth,#repeatweek,#repeatday").hide();
                            break;
                        case "2":
                            $("#repeatmonth,#repeatday,#repeatclock").hide();
                            $("#repeatweek").show();
                            break;
                        case "3":
                            $("#repeatmonth,#repeatweek,#repeatclock").hide();
                            $("#repeatday").show();
                            break;
                        case "4":
                            $("#repeatweek,#repeatclock").hide();
                            $("#repeatmonth,#repeatday").show();
                            break;
                        case "5":
                            $("#repeatclock").show();
                            $("#repeatmonth,#repeatweek,#repeatday").hide();
                            break;
                    }
                });

                var eventid = event.id;
                var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss");
                if (!event.allDay) {
                    var end = $.fullCalendar.formatDate(event.end, "YYYY-MM-DD HH:mm:ss");
                } else {
                    $("#starttime,#endtime,#enddate").hide();
                    $("#isallday").val(event.allDay);
                    $("#isallday").attr('checked', true);
                }


                $("#startdate").val(start.substr(0, 10));
                if (event.end && !event.allDay) {
                    $("#stopdate").val(end.substr(0, 10));
                }
                $("#calendar_title").val(event.title);
                $("#status").val(event.status);
                $("#calendar_detail").html(event.content);
                if (event.is_end && !event.allDay) {
                    $("#end").attr('checked', true);
                    $("#end").val(event.is_end);
                    $("#starttime").val(start.substr(11, 8));
                    $("#stopdate").val(end.substr(0, 10));
                    $("#endtime").val(end.substr(11, 8));
                    $("#starttime,#endtime,#enddate").show();
                }
                //var id = event.id;
                // var time = '19:00:00';
                dialog({
                    title      : "编辑日程",
                    content    : $("#dialog-form"),
                    okValue    : "确定",
                    ok         : function () {
                        var id = event.id;
                        var title = $("#calendar_title").val();
                        var allDay = $("#isallday").prop("checked") == true ? 1 : 0;
                        var is_end = $("#end").prop("checked") == true ? 1 : 0;
                        var content = $("#calendar_detail").val();
                        var startdate = $("#startdate").val();
                        var starttime = allDay ? '00:00:00' : $("#starttime").val().split(" ").join("");
                        var enddate = $("#stopdate").val();
                        var endtime = allDay ? '23:59:59' : $("#endtime").val().split(" ").join("");
                        var type = $("#type").val();
                        var status = $("#status").val();
                        var start = startdate + ' ' + starttime;
                        var end = enddate + ' ' + endtime;

                        if (!title) {
                            tips('标题不能为空~', 'danger');
                            return false;
                        }
                        if (!startdate) {
                            tips('开始时间不能为空~', 'danger');
                            return false;
                        }
                        if (!enddate && is_end) {
                            tips('结束时间不能为空~', 'danger');
                            return false;
                        }
                        if (!title) {
                            tips('内容不能为空~', 'danger');
                            return false;
                        }

                        var data = {
                            id  : id,
                            title  : title,
                            content: content,
                            start  : start,
                            end    : end,
                            allDay : allDay,
                            is_end : is_end,
                            type   : type,
                            status : status,
                        };
                        console.log(data);

                        $.ajax({
                            url     : edit_url,
                            data    : data,
                            type    : 'POST',
                            dataType: 'json',
                            success : function (res) {
                                if(res.code == 1){
                                    var data = res.data
                                    $("#calendar").fullCalendar("renderEvent", data, true);
                                    $("#calendar").fullCalendar("removeEvents", function (event) {
                                        if (event.title == eventtitle) {
                                            return true;
                                        } else {
                                            return false;
                                        }
                                    });
                                }else{
                                    tips('编辑失败','danger')
                                }
                            },
                            error   : function () {
                                alert("Failed");
                            }

                        });

                    },
                    button     : [
                        {
                            value   : "删除",
                            callback: function () {
                                $.ajax({
                                    url     : del_url,
                                    data    : {id:event.id,table:'calendars'},
                                    type    : 'POST',
                                    dataType: 'json',
                                    success : function (res) {
                                        if(res.code == 1){
                                            $("#calendar").fullCalendar("removeEvents", function (event) {
                                                if (event.id == eventid) {
                                                    return true;
                                                } else {
                                                    return false;
                                                }
                                            });
                                        }else{
                                            tips('删除失败','danger')
                                        }
                                    },
                                    error   : function () {
                                        alert("Failed");
                                    }

                                });

                            }
                        }
                    ],
                    cancelValue: "取消",
                    cancel     : function () {
                    }
                }).showModal();
            },
            events    : list_url
        });
    };

    return {
        init: function () {
            // FullCalendar, for more examples you can check out http://fullcalendar.io/
            initEvents();
            initCalendar();
        }
    };
}();

// Initialize when page loads
jQuery(function () {
    BaseCompCalendar.init();
});