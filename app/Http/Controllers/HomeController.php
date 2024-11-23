<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class HomeController extends Controller
{
    public function home()
    {
        // リスト一覧を取得
        $items = Item::orderBy('updated_at', 'DESC')->get();
        // dd($items);
        return view('home', compact('items'));
    }

    public function create()
    {
        return view('create');
    }    

    public function store(Request $request)
    {
        // バリデーション
        $validatedData = $request->validate([
            'item' => 'required|string|max:10',
        ]);

        // 新しいアイテムを作成
        $item = new Item;
        $item->name = $validatedData['item'];
        $item->save();

        // リダイレクトまたはレスポンス
        return redirect()->route('home')->with('success', 'Item added successfully');
    }

    public function delete($id){
        Item::where('id', $id)->delete();

        // リダイレクト処理
        return redirect()->route('home')->with('success', 'Item deleted successfully');
    }
}
