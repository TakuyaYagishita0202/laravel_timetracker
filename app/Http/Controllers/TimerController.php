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

    // public function index()
    // {
    //     return Timer::mine()->orderBy('started_at', 'desc')->get()->toArray();
    // }

    public function index()
    {
        return Timer::mine()->orderBy('started_at', 'desc')->paginate(20)->toArray();
    }

    public function indexMonth()
    {
        $this_year = Carbon::today('Asia/Tokyo')->year;
        $this_month = Carbon::today('Asia/Tokyo')->month;
        return Timer::mine()
            ->whereYear('started_at', $this_year)
            // 以下修正
            ->whereMonth('started_at', $this_month - 2)
            ->get();
    }

    public function indexTotal()
    {
        $timers = Timer::mine()->get()->toArray();
        $total_seconds = 0;
        for($i = 0; $i < count($timers) ;$i++){
            $started_at = new Carbon($timers[$i]['started_at']);
            $stopped_at = new Carbon($timers[$i]['stopped_at']);
            $diff = $started_at->diffInSeconds($stopped_at);
            $total_seconds += $diff;
        }

        // 小数点第一位を丸める
        return round(($total_seconds / 3600), 1);
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
