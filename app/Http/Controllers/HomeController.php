<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Develop;

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

    public function dev_home()
    {
        $items = Develop::get();
        return view('dev-home', compact('items'));
    }

    public function dev_show_create(){
        return view('dev-create');
    }

    public function dev_show_edit(Request $request){
        $id = $request->input('forEdit');
        $item = Develop::find($id);

        return view('dev-edit', compact('item'));
    }

    public function dev_save(Request $request)
    {
        $request->validate([
            'id' => 'nullable|integer|exists:develops,id',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
            'text3' => 'nullable|string',
            'int1' => 'nullable|integer',
            'int2' => 'nullable|integer',
            'int3' => 'nullable|integer',
            'int4' => 'nullable|integer',
            'int5' => 'nullable|integer',
            'date' => 'nullable|date',
        ]);
    
        $id = $request->input('id') ?? null;
        $item = $id ? Develop::find($id) : new Develop;
    
        $item->text1 = $request->input('text1');
        $item->text2 = $request->input('text2');
        $item->text3 = $request->input('text3');
        $item->int1 = $request->input('int1');
        $item->int2 = $request->input('int2');
        $item->int3 = $request->input('int3');
        $item->int4 = $request->input('int4');
        $item->int5 = $request->input('int5');
        $item->date = $request->input('date');
    
        $item->save();
    
        return redirect()->route('dev.home')->with('success', 'レコードが正常に更新されました');
    }

    public function dev_delete(Request $request){
        $id = $request->input('id');
        Develop::where('id', $id)->delete();

        // リダイレクト処理
        return redirect()->route('dev.home')->with('success', 'Item deleted successfully');
    }
}
