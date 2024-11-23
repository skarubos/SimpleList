@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center pt-5">
    <div class="relative flex w-9/12 p-3 flex-col rounded-xl bg-slate-800 bg-clip-border text-gray-200 shadow-md">
        <div class="p-3 text-center">買い物リストに追加</div>
        <form method='POST' action="/store">
            @csrf
            <div class="items-center w-11/12 mx-auto p-3">
                <input name='item' type="text" class="flex w-full px-5 text-lg text-gray-800" id="item" placeholder="入力">
            </div>
            <div class="px-10 py-2 flex justify-end">
                <button type='submit' class="w-4/12 bg-sky-500 hover:bg-sky-700 px-5 py-3 text-sm leading-5 rounded-full font-semibold text-white">
                    追加
                </button>
            </div>
        </form>
    </div>
</div>
@endsection