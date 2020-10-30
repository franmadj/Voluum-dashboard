<x-app-layout>
   

    <div class="py-12">



        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Replace with your content -->
          
                <div class="border-4 border-dashed border-gray-200 rounded-lg h-96">


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
                           
                            @foreach($data as $d)
                             <tr>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    {{$d['name']}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    0
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    0
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    {{$d['data']->conversions}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    {{$d['data']->revenue}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    {{$d['data']->cost}}
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap text-left text-xs">
                                    {{$d['data']->profit}}
                                </td>
                            </tr>
                            @endforeach
                             

                            <!-- More rows... -->
                        </tbody>
                    </table>




                </div>
           
            <!-- /End replace -->
        </div>





    </div>
</x-app-layout>
