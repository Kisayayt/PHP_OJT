<?php

namespace App\Http\Controllers;

use App\Models\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{

    public function index()
    {
        $reasons = Reason::paginate(5);
        return view('reasons.index', compact('reasons'));
    }


    public function create()
    {
        return view('reasons.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        Reason::create($request->all());
        return redirect()->route('reasons.index')->with('success', 'Lý do đã được tạo!');
    }


    public function edit(Reason $reason)
    {
        return view('reasons.edit', compact('reason'));
    }


    public function update(Request $request, Reason $reason)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $reason->update($request->all());
        return redirect()->route('reasons.index')->with('success', 'Lý do đã được cập nhật!');
    }


    public function destroy(Reason $reason)
    {
        $reason->delete();
        return redirect()->route('reasons.index')->with('success', 'Lý do đã được xóa!');
    }
}
