var form = document.getElementById("form-reportrange");
if (form)
    $(function () {

        var action = $(form).data('action');
        _dd(action);

        var start = moment();
        var end = moment();
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));



        $('#reportrange').daterangepicker({
            //                            timePicker: true,
            //                            timePicker24Hour: true,
            startDate: start,
            endDate: end,
            alwaysShowCalendars: true,
            showCustomRangeLabel: true,
            timePickerIncrement: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            var date_from = picker.startDate.format('YYYY-MM-DD');
            var date_to = picker.endDate.format('YYYY-MM-DD');
            _dd(date_from);
            _dd(date_to);
            var display_date = picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY');
            //$('#reportrange span').html(display_date);
            $('#date_from').val(date_from);
            $('#date_to').val(date_to);
            $('#daterange_format').val(display_date);
            $('#reportrange span').html(display_date);
            update_data();
            //$('#form-reportrange').submit();
        });
        $('.refresh-dashboard').click(function (e) {
            e.preventDefault();
            //do_refresh()
            update_data();

        });




        for (const key in accounts) {

            $.get('/get-data/' + accounts[key] + '/' + action, function (resp) {
                var rows = '';
                for (const ws in resp.data.ws) {
                    if (!resp.data.ws[ws]) {
                        continue;
                    }
                    if (action == 'dashboard')
                        rows += create_row(resp.data.ws[ws], true);
                    else if (action == 'network')
                        rows += create_row_net(resp.data.ws[ws], true);
                    else if (action == 'traffic-source')
                        rows += create_row_traffic(resp.data.ws[ws], true);

                }

                $('.ws-content').append(rows);
                rows_done++;
                _dd(rows_done);
                if (rows_done >= accounts.length) {
                    var totals = add_totals(true);
                    $('.ws-content').append(totals);
                    $('.frame-sh').css('height', $('.frame-sh').height());

                }


            });
        }
        setTimeout(function () {
            update_data();
        }, 60000);

        function update_data() {
            $('.refresh-dashboard').animateRotate(360, 5000, 'linear', function () {});
            visits = clicks = conversions = revenue = cost = profit = month_profit = rows_done = 0;


            data = {}
            if ($('#date_from').val().length && $('#date_to').val().length) {
                data = {"date_from": $('#date_from').val(), "date_to": $('#date_to').val()}

            }
            for (const key in accounts)
                $.get('/get-data/' + accounts[key] + '/' + action, data, function (resp) {
                    var rows = '';
                    for (const ws in resp.data.ws) {
                        if (!resp.data.ws[ws]) {
                            continue;
                        }
                        if (action == 'dashboard')
                            rows += create_row(resp.data.ws[ws], false);
                        else if (action == 'network')
                            rows += create_row_net(resp.data.ws[ws], false);
                        else if (action == 'traffic-source')
                            rows += create_row_traffic(resp.data.ws[ws], false);

                    }


                    rows_done++;
                    _dd(rows_done);
                    if (rows_done == accounts.length) {
                        add_totals(false);
                        //$('.ws-content').append(totals);
                        $('.frame-sh').css('height', $('.frame-sh').height());

                    }


                });

            clearTimeout(time_out);

            time_out = setTimeout(function () {

                update_data();
                _dd('update data');
            }, 60000);
        }





    });
var time_out;
var visits = clicks = conversions = revenue = cost = profit = month_profit = rows_done = 0;
const format_options = {style: 'currency', currency: 'USD'};
const  number_format = new Intl.NumberFormat('en-US', format_options);

function create_row(ws, html) {
    _dd('create_row');
    var id = ws['id'];



    var revenue_ = number_format.format(ws['revenue'].toFixed(2));
    var cost_ = number_format.format(ws['cost'].toFixed(2));
    var profit_ = number_format.format(ws['profit'].toFixed(2));
    var month_profit_ = number_format.format(ws['month_profit'].toFixed(2));

    visits += parseFloat(ws['visits']);
    clicks += parseFloat(ws['clicks']);
    conversions += parseFloat(ws['conversions']);
    revenue += parseFloat(ws['revenue']);
    cost += parseFloat(ws['cost']);
    profit += parseFloat(ws['profit']);
    month_profit += parseFloat(ws['month_profit']);

    if (html)
        return `<tr class='workspace'>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs name-${id}">
                                                ${ws['name']}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs visits-${id}">
                                                ${ws['visits']}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs clicks-${id}">
                                                ${ws['clicks']}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs conversions-${id}">
                                                ${ws['conversions']}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs revenue-${id}">
                                                ${revenue_} 
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs cost-${id}">
                                                ${cost_}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs profit-${id}">
                                                ${profit_}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs month_profit-${id}">
                                                ${month_profit_}
                                            </td>
                                        </tr>`;

    $('.name-' + id).text(ws['name']).addClass('new-data');

    $('.visits-' + id).text(ws['visits']).addClass('new-data');
    $('.clicks-' + id).text(ws['clicks']).addClass('new-data');
    $('.conversions-' + id).text(ws['conversions']).addClass('new-data');
    $('.revenue-' + id).text(revenue_).addClass('new-data');
    $('.cost-' + id).text(cost_).addClass('new-data');
    $('.profit-' + id).text(profit_).addClass('new-data');
    $('.month_profit-' + id).text(month_profit_).addClass('new-data');
}
function add_totals(html) {

    revenue = number_format.format(revenue.toFixed(2));
    cost = number_format.format(cost.toFixed(2));
    profit = number_format.format(profit.toFixed(2));
    month_profit = number_format.format(month_profit.toFixed(2));


    if (html)
        return `<tr class='totals bg-teal-200'>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                                Totals...
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-visits">
                                                ${visits}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-clicks">
                                                ${clicks}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-conversions">
                                                ${conversions}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-revenue">
                                                ${revenue} 
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-cost">
                                                ${cost}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-profit">
                                                ${profit}
                                            </td>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs tot-month_profit">
                                                ${month_profit}
                                            </td>
                                        </tr>`;


    $('.tot-visits').text(visits).addClass('new-data');
    $('.tot-clicks').text(clicks).addClass('new-data');
    $('.tot-conversions').text(conversions).addClass('new-data');
    $('.tot-revenue').text(revenue).addClass('new-data');
    $('.tot-cost').text(cost).addClass('new-data');
    $('.tot-profit').text(profit).addClass('new-data');
    $('.tot-month_profit').text(month_profit).addClass('new-data');

    setTimeout(function () {
        $('.new-data').removeClass('new-data');
    }, 3000);




}


function create_row_net(ws, html) {
    _dd('create_row_net');
    var id = ws['id'];
    let row = `<tr class='workspace bg-gray-200'>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs name-${id}">
                                                ${ws['name']}
                                            </td><td colspan="7"></td></tr>`;

    for (const net in ws.networks) {
        network = ws.networks[net];

        _dd(network);

        var revenue_ = number_format.format(network['revenue'].toFixed(2));
        var cost_ = number_format.format(network['cost'].toFixed(2));
        var profit_ = number_format.format(network['profit'].toFixed(2));
        var month_profit_ = number_format.format(network['month_profit'].toFixed(2));

        _dd('*****************conversions******************');

        _dd(parseFloat(network['conversions']));

        visits += parseFloat(network['visits']);
        clicks += parseFloat(network['clicks']);
        conversions += parseFloat(network['conversions']);
        revenue += parseFloat(network['revenue']);
        cost += parseFloat(network['cost']);
        profit += parseFloat(network['profit']);
        month_profit += parseFloat(network['month_profit']);

        var net_id = network['affiliateNetworkId'];

        if (html) {
            row += `<tr class='workspace'>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs net-name-${net_id}">
                                                ${network['affiliateNetworkName']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs visits-${net_id}">
                                                ${network['visits']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs clicks-${net_id}">
                                                ${network['clicks']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs conversions-${net_id}">
                                                ${network['conversions']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs revenue-${net_id}">
                                                ${revenue_} 
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs cost-${net_id}">
                                                ${cost_}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs profit-${net_id}">
                                                ${profit_}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs month_profit-${net_id}">
                                                ${month_profit_}
                                            </td>
                                        </tr>`;
        } else {

            $('.name-' + net_id).text(ws['name']).addClass('new-data');
            $('.net-name-' + net_id).text(network['affiliateNetworkName']).addClass('new-data');

            $('.visits-' + net_id).text(network['visits']).addClass('new-data');
            $('.clicks-' + net_id).text(network['clicks']).addClass('new-data');
            $('.conversions-' + net_id).text(network['conversions']).addClass('new-data');
            $('.revenue-' + net_id).text(revenue_).addClass('new-data');
            $('.cost-' + net_id).text(cost_).addClass('new-data');
            $('.profit-' + net_id).text(profit_).addClass('new-data');
            $('.month_profit-' + net_id).text(month_profit_).addClass('new-data');
        }

    }
    if (html)
        return row;




}

function create_row_traffic(ws, html) {
    _dd('create_row_traffic');
    var id = ws['id'];
    let row = `<tr class='workspace bg-gray-200'>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs name-${id}">
                                                ${ws['name']}
                                            </td><td colspan="7"></td></tr>`;

    for (const key in ws.traffic) {
        var traffic = ws.traffic[key];

        _dd(traffic);

        var revenue_ = number_format.format(traffic['revenue'].toFixed(2));
        var cost_ = number_format.format(traffic['cost'].toFixed(2));
        var profit_ = number_format.format(traffic['profit'].toFixed(2));
        var month_profit_ = number_format.format(traffic['month_profit'].toFixed(2));

        _dd('*****************conversions******************');
        _dd(parseFloat(traffic['conversions']));

        visits += parseFloat(traffic['visits']);
        clicks += parseFloat(traffic['clicks']);
        conversions += parseFloat(traffic['conversions']);
        revenue += parseFloat(traffic['revenue']);
        cost += parseFloat(traffic['cost']);
        profit += parseFloat(traffic['profit']);
        month_profit += parseFloat(traffic['month_profit']);

        var net_id = traffic['trafficSourceId'];
        
        _dd('html***');
        _dd(html);

        if (html) {
            row += `<tr class='workspace'>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs net-name-${net_id}">
                                                ${traffic['trafficSourceName']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs visits-${net_id}">
                                                ${traffic['visits']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs clicks-${net_id}">
                                                ${traffic['clicks']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs conversions-${net_id}">
                                                ${traffic['conversions']}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs revenue-${net_id}">
                                                ${revenue_} 
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs cost-${net_id}">
                                                ${cost_}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs profit-${net_id}">
                                                ${profit_}
                                            </td>
                                            <td class="px-6 py-1 whitespace-no-wrap text-left text-xs month_profit-${net_id}">
                                                ${month_profit_}
                                            </td>
                                        </tr>`;
        } else {
            
            console.log('else******','.visits-' + net_id,traffic['visits']);

            $('.name-' + id).text(ws['name']).addClass('new-data');
            $('.net-name-' + net_id).text(traffic['trafficSourceName']).addClass('new-data');

            $('.visits-' + net_id).text(traffic['visits']).addClass('new-data');
            $('.clicks-' + net_id).text(traffic['clicks']).addClass('new-data');
            $('.conversions-' + net_id).text(traffic['conversions']).addClass('new-data');
            $('.revenue-' + net_id).text(revenue_).addClass('new-data');
            $('.cost-' + net_id).text(cost_).addClass('new-data');
            $('.profit-' + net_id).text(profit_).addClass('new-data');
            $('.month_profit-' + net_id).text(month_profit_).addClass('new-data');
        }

    }
    if (html)
        return row;




}

function _dd(val) {
    return;
    console.log(val);

}

$.fn.animateRotate = function (angle, duration, easing, complete) {
    var args = $.speed(duration, easing, complete);
    var step = args.step;
    return this.each(function (i, e) {
        args.complete = $.proxy(args.complete, e);
        args.step = function (now) {
            $.style(e, 'transform', 'rotate(' + now + 'deg)');
            if (step)
                return step.apply(e, arguments);
        };

        $({deg: 0}).animate({deg: angle}, args);
    });
};
