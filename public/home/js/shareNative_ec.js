function configShare() {
    var addHTML_canvas = '<div id="shareCanvas" class="overlay">';
    addHTML_canvas += '    <div class="overlay-content">';
    addHTML_canvas += '    <img id="shareImg" src="https://img.alicdn.com/imgextra/i3/3491683832/TB2.p8de2DH8KJjy1XcXXcpdXXa_!!2-martrix_bbs.png" style="height:100%;max-width: 100%;" onclick="closeShare();">';
    addHTML_canvas += '    </div>';
    addHTML_canvas += '    </div>';
    var addHTML_fav    = '    <div class="overlay-content" onclick="closeFav();">';
    addHTML_fav += '    <img id="favImg" src="https://img.alicdn.com/imgextra/i3/4028991139/TB2CYI1HHSYBuNjSspiXXXNzpXa_!!2-martrix_bbs.png" style="height:100%;max-width: 100%;" >';
    addHTML_fav += '    <a target="_top" href="/help/collec.html"></a>';
    addHTML_fav += '    </div>';
    var favDiv         = document.createElement('div');
    favDiv.setAttribute("id", "favCanvas");
    favDiv.setAttribute("class", "overlay");
    favDiv.innerHTML = addHTML_fav;
    document.body.appendChild(favDiv);

    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    if (/iPad|iPhone|iPod/i.test(userAgent)) {
        var mobile_ios = true;
    }
    if (/android/i.test(userAgent)) {
        var mobile_and = true;
    }
    if (/micromessenger|wechat|weixin/i.test(userAgent)) {
        var wechat = true;
    }
    if (mobile_ios || mobile_and) {
        var ua = navigator.userAgent.toLowerCase()
        if (!ua.match(/lrapp/i)) {
            $('.btn-share').css('display', 'inline-block');
        }else{
            $('.app_hide').hide();
            $('.app_disable').attr('disabled',true);
        }

        var validShareBlock = document.getElementsByClassName('shareBlock');
        for (var i = 0; i < validShareBlock.length; i++) {

            validShareBlock[i].innerHTML = addHTML_canvas;

        }
        var Obj_shareImg = document.getElementById("shareImg");

        var Obj_favImg = document.getElementById("favImg");
        if (wechat) {
            Obj_shareImg.src = "/js/fenxiang/wechat.png";
            Obj_favImg.src   = "/js/home/fav/wechat.png";
        } else {
            if (mobile_ios) {
                if (/crios/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2Nds5eNrI8KJjy0FpXXb5hVXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i4/4028991139/TB2Ei30HHSYBuNjSspiXXXNzpXa_!!2-martrix_bbs.png";
                } else if (/fxios/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i3/3491683832/TB2Ba31eInI8KJjSspeXXcwIpXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB2ms63HMmTBuNjy1XbXXaMrVXa_!!2-martrix_bbs.png";
                } else if (/sogou/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2VGc9eNPI8KJjSspoXXX6MFXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB21oRkHY1YBuNjSszeXXablFXa_!!2-martrix_bbs.png";
                } else if (/ucbrowser/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i1/3491683832/TB27Clge26H8KJjSspmXXb2WXXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB2g3dtHYSYBuNjSspfXXcZCpXa_!!2-martrix_bbs.png";
                } else if (/qhbrowser/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2NPWibvjM8KJjSZFsXXXdZpXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i2/4028991139/TB2VeijaPbguuRkHFrdXXb.LFXa_!!2-martrix_bbs.png";
                } else if (/qqbrowser/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i3/3491683832/TB2oZJGbxk98KJjSZFoXXXS6pXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i2/4028991139/TB2BxuOmiMnBKNjSZFzXXc_qVXa_!!2-martrix_bbs.png";
                } else if (/baidu/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i1/3491683832/TB2DFw1eInI8KJjSspeXXcwIpXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i4/4028991139/TB2eZAFHHGYBuNjy0FoXXciBFXa_!!2-martrix_bbs.png";
                } else if (/2345/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i4/3491683832/TB2UXIOeN6I8KJjSszfXXaZVXXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i3/4028991139/TB2MZwwl8nTBKNjSZPfXXbf1XXa_!!2-martrix_bbs.png";
                } else if (/version/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i3/3491683832/TB2MfZUeL2H8KJjy0FcXXaDlFXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB2dYA2HNSYBuNjSspjXXX73VXa_!!2-martrix_bbs.png";
                } else {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i3/3491683832/TB2.p8de2DH8KJjy1XcXXcpdXXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i3/4028991139/TB2r09SmbArBKNjSZFLXXc_dVXa_!!2-martrix_bbs.png";
                }
            }
            if (mobile_and) {
                if (/baidu/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i3/3491683832/TB2gcI3eMLD8KJjSszeXXaGRpXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i3/4028991139/TB2W0_9zvuSBuNkHFqDXXXfhVXa_!!2-martrix_bbs.png";
                } else if (/firefox/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2nGQKeTTI8KJjSsphXXcFppXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i3/4028991139/TB2.aB0H7SWBuNjSszdXXbeSpXa_!!2-martrix_bbs.png";
                } else if (/sogou/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2uP32eRDH8KJjSspnXXbNAVXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i4/4028991139/TB2IuESHHuWBuNjSszgXXb8jVXa_!!2-martrix_bbs.png";
                    alert(3)
                } else if (/ucbrowser/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i1/3491683832/TB2W3tge26H8KJjSspmXXb2WXXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB2hC7AHL9TBuNjy1zbXXXpepXa_!!2-martrix_bbs.png";
                } else if (/qqbrowser/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2D079eL6H8KJjy0FjXXaXepXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i4/4028991139/TB2faznHNSYBuNjSsphXXbGvVXa_!!2-martrix_bbs.png";
                } else if (/2345/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2mq32eRTH8KJjy0FiXXcRsXXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i2/4028991139/TB2bwoqHL9TBuNjy0FcXXbeiFXa_!!2-martrix_bbs.png";
                } else if (/liebao/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2fE.WeJrJ8KJjSspaXXXuKpXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i4/4028991139/TB2JbaFzxuTBuNkHFNRXXc9qpXa_!!2-martrix_bbs.png";
                } else if (/samsung/i.test(userAgent)) {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2QYgIeJbJ8KJjy1zjXXaqapXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB2vA.AHL9TBuNjy1zbXXXpepXa_!!2-martrix_bbs.png";
                } else if (/chrome/i.test(userAgent)) {
                    if (/version/i.test(userAgent)) {
                        Obj_shareImg.src = "https://img.alicdn.com/imgextra/i3/3491683832/TB2VvBTaQfb_uJjSsD4XXaqiFXa_!!2-martrix_bbs.png";
                        Obj_favImg.src   = "https://img.alicdn.com/imgextra/i3/4028991139/TB2HXFzhwZC2uNjSZFnXXaxZpXa_!!2-martrix_bbs.png";
                    } else {
                        Obj_shareImg.src = "https://img.alicdn.com/imgextra/i1/3491683832/TB2U0hge26H8KJjSspmXXb2WXXa_!!2-martrix_bbs.png";
                        Obj_favImg.src   = "https://img.alicdn.com/imgextra/i2/4028991139/TB2q5DkzsyYBuNkSnfoXXcWgVXa_!!2-martrix_bbs.png";
                    }
                } else {
                    Obj_shareImg.src = "https://img.alicdn.com/imgextra/i2/3491683832/TB2D079eL6H8KJjy0FjXXaXepXa_!!2-martrix_bbs.png";
                    Obj_favImg.src   = "https://img.alicdn.com/imgextra/i1/4028991139/TB2UkDmzviSBuNkSnhJXXbDcpXa_!!2-martrix_bbs.png";
                }
            }
        }
    }
}

function openShare() {
    document.getElementById("shareCanvas").style.height = "100vh";
    //document.getElementById("download_dibu").style.visibility = "hidden"
}

function closeShare() {
    document.getElementById("shareCanvas").style.height = "0%";
}

function openFav() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    if (/android|iPad|iPhone|iPod/i.test(userAgent)) {
        document.getElementById("favCanvas").style.height = "100%";
        if (/liebao/i.test(userAgent)) {
            closeFav();
        }
    } else {
        var Obj_favImg                                    = document.getElementById("favImg");
        Obj_favImg.src                                    = "https://img.alicdn.com/imgextra/i3/4028991139/TB2CYI1HHSYBuNjSspiXXXNzpXa_!!2-martrix_bbs.png";
        document.getElementById("favCanvas").style["top"] = "70%";
        document.getElementById("favCanvas").style.height = "30%";
    }
}

function closeFav() {
    document.getElementById("favCanvas").style.height = "0%";
}
