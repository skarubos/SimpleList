<x-app-layout>
    <div class="">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 py-6 px-6 shadow-sm sm:rounded-lg">

                <!--  -->
                <form action="/dev/scraping" method="POST" enctype="multipart/form-data" class="mb-5">
                    @csrf
                    <label for="url_a" class="text-sm font-medium">URLのページを取得</label>
                    <div class="">
                    <select name="url_a" id="url_a" class="min-w-56 dark:bg-gray-900 mt-1 mr-5 py-2 px-4 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md">
                        @foreach($items as $item)
                            <option value="{{ $item->text1 }}" class="">
                            {{ $item->text2 . '：' . $item->text1 }}
                            </option>
                        @endforeach
                    </select>
                    <select name="url_b" id="url_b" class="min-w-56 dark:bg-gray-900 mt-1 mr-5 py-2 px-4 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md">
                        @foreach($items as $item)
                            <option value="{{ $item->text1 }}" class="">
                            {{ $item->text2 . '：' . $item->text1 }}
                            </option>
                        @endforeach
                    </select>
                    <select name="url_c" id="url_c" class="min-w-56 dark:bg-gray-900 mt-1 mr-5 py-2 px-4 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md">
                        @foreach($items as $item)
                            <option value="{{ $item->text1 }}" class="">
                            {{ $item->text2 . '：' . $item->text1 }}
                            </option>
                        @endforeach
                    </select>
                    <x-primary-button class="px-5 my-2 items-center justify-center">
                        excute
                    </x-primary-button>
                    </div>
                </form>

                <!-- 編集用 -->
                <form action="/dev/edit/show" method="POST" enctype="multipart/form-data" class="mb-5">
                    @csrf
                    <select name="forEdit" id="url_c" class="min-w-56 dark:bg-gray-900 mt-1 mr-5 py-2 px-4 shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 rounded-md">
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" class="">
                            {{ $item->text2 . '：' . $item->text1 }}
                            </option>
                        @endforeach
                    </select>
                    <x-primary-button class="px-8 my-2 items-center justify-center">
                        edit
                    </x-primary-button>
                    </div>
                </form>
                <a href="/dev/create">
                    <x-primary-button class="px-8 my-2">
                        new
                    </x-primary-button>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
