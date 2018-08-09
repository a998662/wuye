/*
 *  Document   : base_pages_dashboard_v2.js
 *  Author     : pixelcave
 *  Description: Custom JS code used in Dashboard v2 Page
 */

var BasePagesDashboardv2 = function() {
    // Chart.js Chart, for more examples you can check out http://www.chartjs.org/docs
    var initDashv2ChartJS = function(){
        // Get Chart Container
        var $dashChartEarningsCon = jQuery('.js-dash-chartjs-earnings')[0].getContext('2d');

        // Earnings Chart Data
        var $dashChartEarningsData = {
            labels: ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六','星期日'],
            datasets: [
                {
                    label: '上周',
                    fillColor: 'rgba(120, 120, 120, .07)',
                    strokeColor: 'rgba(120, 120, 120, .25)',
                    pointColor: 'rgba(120, 120, 120, .25)',
                    pointStrokeColor: '#fff',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(120, 120, 120, 1)',
                    data: lastWeek
                },
                {
                    label: '本周',
                    fillColor: 'rgba(100, 100, 100, .25)',
                    strokeColor: 'rgba(100, 100, 100, .55)',
                    pointColor: 'rgba(100, 100, 100, .55)',
                    pointStrokeColor: '#fff',
                    pointHighlightFill: '#fff',
                    pointHighlightStroke: 'rgba(100, 180, 100, 1)',
                    data: thisWeek
                }
            ]
        };

        // Init Earnings Chart
        var $dashChartEarnings = new Chart($dashChartEarningsCon).Line($dashChartEarningsData, {
            scaleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
            scaleFontColor: '#999',
            scaleFontStyle: '600',
            tooltipTitleFontFamily: "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif",
            tooltipCornerRadius: 3,
            maintainAspectRatio: false,
            responsive: true
        });
    };

    return {
        init: function () {
            // Init ChartJS charts
            initDashv2ChartJS();
        }
    };
}();

// Initialize when page loads
jQuery(function(){ BasePagesDashboardv2.init(); });