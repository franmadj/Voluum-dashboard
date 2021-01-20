<x-app-layout>


    <div class="py-12">



        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Replace with your content -->
            <div class="border-4 border-dashed border-gray-200 rounded-lg frame-sh">
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
                                    
var accounts = '{{$accounts}}'.split(',');
console.log(accounts);

                                    
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
                                    <tbody class="bg-white divide-y divide-gray-200 ws-content">






                                        <!-- More rows... -->
                                    </tbody>
                                </table>




                                </div>

                                <!-- /End replace -->
                                </div>





                                </div>
                                </x-app-layout>
