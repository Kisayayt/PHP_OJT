<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;

class WorkTimeController extends Controller
{
    public function showWorkTime()
    {
        $workStart = Configuration::where('name', 'work_start')->value('time');
        $workEnd = Configuration::where('name', 'work_end')->value('time');

        return view('workTime.index', compact('workStart', 'workEnd'));
    }

    public function updateWorkTime(Request $request)
    {
        $workStart = strlen($request->work_start) === 5 ? $request->work_start . ':00' : $request->work_start;
        $workEnd = strlen($request->work_end) === 5 ? $request->work_end . ':00' : $request->work_end;

        $request->merge([
            'work_start' => $workStart,
            'work_end' => $workEnd,
        ]);

        $request->validate([
            'work_start' => 'required|date_format:H:i:s',
            'work_end' => 'required|date_format:H:i:s',
        ]);

        Configuration::where('name', 'work_start')->update(['time' => $workStart]);
        Configuration::where('name', 'work_end')->update(['time' => $workEnd]);

        return redirect()->route('admin.workTime')->with('success', 'Thời gian làm việc đã được cập nhật!');
    }
}
