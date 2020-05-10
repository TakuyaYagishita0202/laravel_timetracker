<?php

namespace App\Http\Controllers;

use App\Timer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TimerController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return Timer::mine()->orderBy('id', 'desc')->get()->toArray();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:30',
            'memo' => 'nullable|max:140',
            'category_id' => 'nullable',
            'category_name' => 'nullable|max:20',
            'category_color' => 'nullable'
        ]);
        $timer = Timer::mine()->create([
            'name' => $data['name'],
            'memo' => $data['memo'],
            'category_id' => $data['category_id'],
            'category_name' => $data['category_name'],
            'category_color' => $data['category_color'],
            'user_id' => Auth::user()->id,
            'started_at' => new Carbon,
            'stopped_at' => null,
        ]);

        return $timer;
    }

    public function delete(int $id)
    {
        $timer = Timer::mine()->find($id);
        $timer->delete();
        return $timer;
    }

    public function running()
    {
        return Timer::mine()->running()->first() ?? [];
    }

    public function stopRunning()
    {
        if ($timer = Timer::mine()->running()->first()) {
            $timer->update(['stopped_at' => new Carbon]);
        }

        return $timer;
    }
}
