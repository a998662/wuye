var iosDown = function() {
    var userAgent = navigator.userAgent || navigator.vendor || window.opera;
    var isIOS_Safari = false;
    if (/iPad|iPhone|iPod/i.test(userAgent)) {
        if (/Safari/i.test(userAgent)) {
            isIOS_Safari = true;
        }
    }
    if (!isIOS_Safari) {
        alert("抱歉，请从苹果自带浏览器Safari中打开。谢谢");
    } else {
        window.location.href = "https://www.langren70.com";
    }
}