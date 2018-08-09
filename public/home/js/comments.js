var pid = '';
$('.show_tmp').click(function () {
    var pid = $(this).attr('id');
    var tmp = $('.tmp_' + pid).html();
    $('.child_' + pid).html(tmp);
})

$('.comment__reply').click(function () {
    if (!user) {
        //询问框
        layer.open({
            content: '您还未登录，请登录后再来~'
            ,btn: ['登录', '取消']
            ,yes: function(index){
                location.href = '/user/login.html?ref='+location.href;
            }
        });
        return false;
    }
    pid      = $(this).attr('pid');
    nickname = $(this).attr('nickname');
    $('#content').attr('placeholder', "@" + nickname);
})

$('.post_comment').click(function () {
    if (!user) {
        //询问框
        layer.open({
            content: '您还未登录，请登录后再来~'
            ,btn: ['登录', '取消']
            ,yes: function(index){
                location.href = '/user/login.html?ref='+location.href;
            }
        });
        return false;
    }
    var content = $('#content').val();
    var data    = {
        'id'     : vid,
        'pid'    : pid,
        'content': content,
    }

    if (user) {
        data.user_id = user.id;
        data.nickname = user.nickname
    }

    $.post(api_host + '/api/postComment', data, function (res) {
        if (res.code) {
            $('#content').val('');
            //询问框
            layer.open({
                content: res.msg,
                btn    : ['确定', '取消']
            });

        } else {
            //询问框
            layer.open({
                content: res.msg
                , btn  : ['确定', '取消'],
            });
        }
    }, 'json')
})


var self = '';
$('.good-inc').click(function () {
    var id   = $(this).data('id');
    var data = {id: id};
    var self = $(this);
    $.post(api_host + '/api/cmtGoodInc', data, function (res) {
        if (res.code) {
            var num = $('#good-num' + id).text() ? $('#good-num' + id).text() : 0;
            $('#good-num' + id).text(parseInt(num) + 1);
            self.addClass('active');
            //询问框
            layer.open({
                content: res.msg
                , btn  : ['确定', '取消']
            });

        } else {
            //询问框
            layer.open({
                content: res.msg
                , btn  : ['确定', '取消']
            });
        }
    }, 'json')
})