<div class="md:col-span-1">
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
            <tr>
                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">
                    Name
                </th>

                <th class="px-6 py-3 bg-gray-50 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">

                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">

            @foreach ($accounts as $acc)


            <tr>
                <td class="px-6 py-4 whitespace-no-wrap capitalize">
                    {{$acc->name}}
                </td>
                <td class="px-6 py-4 whitespace-no-wrap flex">
                    
                    <a href="{{ route('edit.accounts', $acc->id) }}" 
                       class="text-gray-600 hover:text-indigo-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a> 
                    <a href="#" 
                       class="text-gray-600 hover:text-indigo-900" 
                       onclick="event.preventDefault();document.getElementById('delete-acc-{{ $acc->id }}').submit();">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </a>
                    <form id="delete-acc-{{ $acc->id }}" action="{{ route('delete.accounts', $acc->id) }}" method="POST" style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </td>


            </tr>
            @endforeach

            <!-- More rows... -->
        </tbody>
    </table>
</div>