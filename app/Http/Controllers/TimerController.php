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
        return Timer::mine()->orderBy('started_at', 'desc')->get()->toArray();
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

    public function save(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|max:30',
            'memo' => 'nullable|max:140',
            'category_id' => 'nullable',
            'category_name' => 'nullable|max:20',
            'category_color' => 'nullable',
            'started_at' => 'required',
            'stopped_at' => 'required'
        ]);

        $started_at = new Carbon($data['started_at']);
        $started_at->addHour(9);
        $stopped_at = new Carbon($data['stopped_at']);
        $stopped_at->addHour(9);

        $timer = Timer::mine()->create([
            'name' => $data['name'],
            'memo' => $data['memo'],
            'category_id' => $data['category_id'],
            'category_name' => $data['category_name'],
            'category_color' => $data['category_color'],
            'user_id' => Auth::user()->id,
            'started_at' => $started_at,
            'stopped_at' => $stopped_at,
        ]);

        return $timer;
    }

    public function update(Request $request, int $id)
    {
        $data = $request->validate([
            'name' => 'required|max:30',
            'memo' => 'nullable|max:140',
            'category_id' => 'nullable',
            'category_name' => 'nullable|max:20',
            'category_color' => 'nullable',
            'started_at' => 'required',
            'stopped_at' => 'required'
        ]);

        $started_at = new Carbon($data['started_at']);
        $started_at->addHour(9);
        $stopped_at = new Carbon($data['stopped_at']);
        $stopped_at->addHour(9);

        $timer = Timer::mine()->where('id', $id)->first();
        $timer->name = $data['name'];
        $timer->memo = $data['memo'];
        $timer->category_id = $data['category_id'];
        $timer->category_name = $data['category_name'];
        $timer->category_color = $data['category_color'];
        $timer->started_at = $started_at;
        $timer->stopped_at = $stopped_at;
        $timer->save();

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
