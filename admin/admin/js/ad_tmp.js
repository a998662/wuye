var ad = [{}];
var html = "";
for (var sKeys in ad) {
    html += "<a href='" + ad[sKeys].redirect_url + "'>";
    html += "<img  class='' src='" + ad[sKeys].pic_url + "'/>";
    html += "</a>";
}
$("#ad_div").append(html);
