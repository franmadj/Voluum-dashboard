var myEle = document.getElementById("form-reportrange");
if (myEle)
    $(function () {

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
            console.log(date_from);
            console.log(date_to);
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

            $.get('/get-data/' + accounts[key], function (resp) {
                var rows = '';
                for (const ws in resp.data.ws) {
                    if (!resp.data.ws[ws]) {
                        continue;
                    }

                    rows += create_row(resp.data.ws[ws], true);
                }
                ;
                $('.ws-content').append(rows);
                rows_done++;
                console.log(rows_done);
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
            visits = clicks = conversions = revenue = cost = profit = month_profit = rows_done = 0;


            data = {}
            if ($('#date_from').val().length && $('#date_to').val().length) {
                data = {"date_from": $('#date_from').val(), "date_to": $('#date_to').val()}

            }
            for (const key in accounts)
                $.get('/get-data/' + accounts[key], data, function (resp) {
                    var rows = '';
                    for (const ws in resp.data.ws) {
                        if (!resp.data.ws[ws]) {
                            continue;
                        }
                        create_row(resp.data.ws[ws], false);
                    }
                    ;

                    rows_done++;
                    console.log(rows_done);
                    if (rows_done == accounts.length) {
                        add_totals(false);
                        //$('.ws-content').append(totals);
                        $('.frame-sh').css('height', $('.frame-sh').height());

                    }


                });

            clearTimeout(time_out);

            time_out = setTimeout(function () {

                update_data();
                console.log('update data');
            }, 60000);
        }



    });
var time_out;
var visits = clicks = conversions = revenue = cost = profit = month_profit = rows_done = 0;
const format_options = {style: 'currency', currency: 'USD'};
const  number_format = new Intl.NumberFormat('en-US', format_options);

function create_row(ws, html) {
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