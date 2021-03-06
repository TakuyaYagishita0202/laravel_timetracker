<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return Category::mine()->with('timers')->get()->toArray();
    }

    public function store(Request $request)
    {
        // バリデーションされたデータを返す
        $data = $request->validate([
            'name' => 'required|max:20',
            'color' => 'required'
            ]);

        // ユーザーIDとマージする
        $data = array_merge($data, ['user_id' => auth()->user()->id]);

        $category = Category::create($data);

        return $category->toArray();
    }
}
