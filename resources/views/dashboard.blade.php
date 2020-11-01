<x-app-layout>


    <div class="py-12">



        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Replace with your content -->
            <div class="border-4 border-dashed border-gray-200 rounded-lg">
                <form action="/dashboard" method="POST" id="form-reportrange">
                    @csrf 
                    <input type="hidden" id="date_from" name="date_from">
                    <input type="hidden" id="date_to" name="date_to">
                    <input type="hidden" id="daterange_format" name="daterange_format">
                </form>

                <div id="reportrange">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span></span> <i class="fa fa-caret-down"></i>
                </div>

                <script type="text/javascript">
                    $(function () {

                    var start = moment().subtract(29, 'days');
                    var end = moment();
                    function cb(start, end) {
                        @if ($daterange)
                            $('#reportrange span').html('{{$daterange}}');
                                @else
                                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                        @endif
                    }

                    $('#reportrange').daterangepicker({
//                            timePicker: true,
//                            timePicker24Hour: true,
                    startDate: start,
                            endDate: end,
                            alwaysShowCalendars: true,
                            showCustomRangeLabel:true,
                            timePickerIncrement:true,
                            ranges: {
                            'Today': [moment(), moment()],
                                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                            }
                    }, cb);
                    cb(start, end);
                    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
                    console.log(picker.startDate.utc().format());
                    console.log(picker.endDate.utc().format());
                    $('#reportrange span').html(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                    $('#date_from').val(picker.startDate.utc().format());
                    $('#date_to').val(picker.endDate.utc().format());
                    $('#daterange_format').val(picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY'));
                    $('#form-reportrange').submit();
                    });
                    });
                </script>





                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Account
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Visits
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Clicks
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Conversions
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Revenue
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Cost
                            </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                Profit
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $visits=$clicks=$conversions=$revenue=$cost=$profit=0; @endphp

                        @foreach($data as $d)
                        @foreach($d['data'] as $key=>$value)
                        
                        @if('acc'==$key)
                        
                        @php 
                        $visits+=$value->visits;
                        $clicks+=$value->clicks;
                        $conversions+=$value->conversions;
                        $revenue+=$value->revenue;
                        $cost+=$value->cost;
                        $profit+=$value->profit;
                        @endphp
                        
                        <tr class='account bg-gray-300'>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$d['name']}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$value->visits}}
                                
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$value->clicks}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$value->conversions}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ {{number_format($value->revenue,2)}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ {{number_format($value->cost,2)}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ {{number_format($value->profit,2)}}
                            </td>
                        </tr>

                        @else

                        @foreach($value as $key=>$workspace)
                        <tr class='workspace'>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$key}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$workspace->visits}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$workspace->clicks}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                {{$workspace->conversions}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ {{number_format($workspace->revenue,2)}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ {{number_format($workspace->cost,2)}}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ {{number_format($workspace->profit,2)}}
                            </td>
                        </tr>

                        @endforeach

                        @endif

                        @endforeach
                        @endforeach
                        
                        <tr class='totals bg-teal-200'>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                Totals...
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                @php echo $visits; @endphp
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                              @php echo $clicks; @endphp
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                              @php echo $conversions; @endphp
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ @php echo number_format($revenue,2); @endphp
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ @php echo number_format($cost,2); @endphp
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                $ @php echo number_format($profit,2); @endphp
                            </td>
                        </tr>
                        
                        
                        


                        <!-- More rows... -->
                    </tbody>
                </table>




            </div>

            <!-- /End replace -->
        </div>





    </div>
</x-app-layout>
