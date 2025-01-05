<x-app-layout>
<div class="flex justify-center items-center pt-5">
    <div class="relative flex w-96 flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md">
        <div class="p-6">
        <div class="mb-4 flex justify-between">
            <p class="w-4/5 font-sans text-center text-lg font-bold leading-snug tracking-normal text-gray-500 antialiased">
                Shopping List
            </p>
            <a href="/create" class="w-1/5 font-sans text-base font-bold leading-normal text-blue-500 antialiased text-right">
                Add
            </a>
        </div>
        <div class="divide-y divide-gray-200">
        @foreach($items AS $item)
            <div class="flex items-center justify-between pb-3 pt-3">
                <div class="flex items-center gap-x-3">
                    <div class="block pl-2 font-sans text-2xl leading-relaxed tracking-normal antialiased">
                        {{ $item['name'] }}
                    </div>
                </div>
                <form method='POST' action="/delete/{{ $item['id'] }}">
                    @csrf
                    <button class="block px-2" style='border:none;'><i class="fas fa-trash"></i></button>
                </form>
            </div>
        @endforeach
        </div>
        </div>
    </div>
</div>
</x-app-layout>
