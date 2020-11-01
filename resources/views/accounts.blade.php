<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden sm:rounded-lg">


                <div class="mt-10 sm:mt-0">
                    <div class="md:grid md:grid-cols-3 md:gap-6">


                        @include('accounts-list')


                        <div class="mt-5 md:mt-0 md:col-span-2">

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            <form action="/accounts" method="POST" autocomplete="off">
                                @csrf
                                
                                <div class="shadow overflow-hidden sm:rounded-md">
                                    <div class="px-4 py-5 bg-white sm:p-6">
                                        <div class="grid grid-cols-6 gap-6">

                                            <div class="col-span-12">
                                                <label for="name" class="block text-sm font-medium leading-5 text-gray-700">name</label>
                                                <input required="" name="name"  value="{{ old('name') }}" autocomplete="off"
                                                       class="@error('name') is-invalid @enderror mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                                @error('name')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>






                                            <div class="col-span-12">
                                                <label for="access_key_id" class="block text-sm font-medium leading-5 text-gray-700">Access key ID</label>
                                                <input type="password" required="" name="access_key_id" value="{{ old('access_key_id') }}" autocomplete="new-password"
                                                       class="@error('access_key_id') is-invalid @enderror mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                                @error('access_key_id')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-span-12">
                                                <label for="access_key" value="{{ old('access_key') }}" 
                                                       class="block text-sm font-medium leading-5 text-gray-700">Access key</label>
                                                <input type="password" required="" name="access_key" autocomplete="off"
                                                       class="@error('access_key') is-invalid @enderror mt-1 form-input block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5">
                                                @error('access_key')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>



                                            <div class="col-span-12">
                                                <label for="workspaces" class="block text-sm font-medium leading-5 text-gray-700">Workspaces</label>
                                                <div class="rounded-md shadow-sm">
                                                    <textarea name="workspaces" rows="3" 
                                                              class="@error('workspaces') is-invalid @enderror form-textarea mt-1 block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5">{{ old('workspaces') }}</textarea>
                                                </div>
                                                @error('workspaces')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                                <p class="mt-2 text-sm text-gray-400 italic">
                                                    One per line.
                                                </p>
                                            </div>


                                        </div>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                                        <button type="submit" class="py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 shadow-sm hover:bg-indigo-500 focus:outline-none focus:shadow-outline-blue active:bg-indigo-600 transition duration-150 ease-in-out">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
