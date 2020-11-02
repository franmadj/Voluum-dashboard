<x-app-layout>


    <div class="py-12">



        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Replace with your content -->
            <div class="border-4 border-dashed border-gray-200 rounded-lg">
                <form action="/dashboard" method="POST" id="form-reportrange">
                    @csrf 
                    <input type="hidden" id="date_from" name="date_from" @if($date_from) value="{{$date_from}}" @endif>
                        <input type="hidden" id="date_to" name="date_to" @if($date_to) value="{{$date_to}}" @endif>
                            <input type="hidden" id="daterange_format" name="daterange_format" @if($daterange) value="{{$daterange}}" @endif>
                                </form>
                                <div class="list-header">

                                    <div id="reportrange">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>&nbsp;
                                        <span></span></i>
                                    </div>
                                    <a href="#" class="refresh-dashboard">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </a>
                                </div>
                                <script type="text/javascript">
                                    $(function () {

                                    var start = moment();
                                    var end = moment();
                                    function cb(start, end) {
                                        @if($daterange)
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
                                        var date_from=picker.startDate.format('YYYY-MM-DD');
                                        var date_to=picker.endDate.format('YYYY-MM-DD');
                                        
                                        console.log(date_from);
                                        console.log(date_to);


                                        var display_date=picker.startDate.format('MMMM D, YYYY') + ' - ' + picker.endDate.format('MMMM D, YYYY');
                                        //$('#reportrange span').html(display_date);
                                        $('#date_from').val(date_from);
                                        $('#date_to').val(date_to);
                                        $('#daterange_format').val(display_date);
                                        $('#form-reportrange').submit();
                                    });
                                    
                                    $('.refresh-dashboard').click(function(e){
                                        e.preventDefault();
                                        do_refresh()
                                        
                                    });
                                    
                                    
                                    setTimeout(function(){
                                        do_refresh()
                                    },60000);
                                    
                                    function do_refresh(){ 
                                        @if(!$daterange)
                                           
                                            window.location.reload();
                                        @else
                                           
                                            $('#form-reportrange').submit();
                                        @endif
                                        
                                    }
                                    
                                    
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
                                            
                                            <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                                Month Profit
                                            </th>
                                            
                                            
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $visits=$clicks=$conversions=$revenue=$cost=$profit=$month_profit=0; @endphp

                                        @foreach($data as $d)
                                        @foreach($d['data'] as $key=>$value)

                                        @if('accc'==$key)

                                        

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

                                        @elseif('ws'==$key)

                                        @foreach($value as $key=>$workspace)
                                        
                                        @php 
                                        $visits+=$workspace->visits;
                                        $clicks+=$workspace->clicks;
                                        $conversions+=$workspace->conversions;
                                        $revenue+=$workspace->revenue;
                                        $cost+=$workspace->cost;
                                        $profit+=$workspace->profit;
                                        $month_profit+=$workspace->month_profit;
                                        @endphp
                                        
                                        <tr class='workspace'>
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                                {{$workspace->name}}
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
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                                $ {{number_format($workspace->month_profit,2)}}
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
                                            <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                                $ @php echo number_format($month_profit,2); @endphp
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
