<x-app-layout>
<div class="flex justify-center items-center pt-5">
    <div class="relative flex w-9/12 p-3 flex-col rounded-xl bg-slate-800 bg-clip-border text-gray-200 shadow-md">
        <div class="p-3 text-center">開発用テーブルの項目を編集</div>
        <form method="POST" action="/dev/edit/save">
            @csrf
            <input name="id" type="hidden" value="{{ $item->id }}">

            @foreach (['text1', 'text2', 'text3'] as $textField)
                <div class="items-center w-11/12 mx-auto p-3">
                    <input name="{{ $textField }}" type="text" value="{{ $item->$textField }}" class="flex w-full px-5 text-lg text-gray-800">
                </div>
            @endforeach

            <div class="flex items-center w-11/12 mx-auto p-3">
                @foreach (range(1, 5) as $i)
                    <input name="int{{ $i }}" type="number" value="{{ $item->{'int'.$i} }}" class="flex w-1/6 px-5 mr-3 text-lg text-gray-800">
                @endforeach
            </div>

            <div class="items-center w-11/12 mx-auto p-3">
                <input name="date" type="date" value="{{ $item->date }}" class="flex px-5 text-lg text-gray-800">
            </div>

            <div class="px-10 py-2 flex justify-end">
                <button type="submit" class="w-4/12 bg-sky-500 hover:bg-sky-700 px-5 py-3 text-sm leading-5 rounded-full font-semibold text-white">
                    変更
                </button>
            </div>
        </form>
        <form method="POST" action="/dev/edit/delete">
            <div class="px-10 py-2 flex justify-end">
                @csrf
                <input name="id" type="hidden" value="{{ $item->id }}">
                <button type="submit" class="w-4/12 bg-orange-500 hover:bg-orange-700 px-5 py-3 text-sm leading-5 rounded-full font-semibold text-white">
                    削除
                </button>
        </div>
        </form>
    </div>
</div>
</x-app-layout>