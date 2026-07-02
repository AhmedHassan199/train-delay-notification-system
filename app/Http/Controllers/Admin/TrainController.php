<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Train;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrainController extends Controller
{
    public function index(): View
    {
        $trains = Train::withCount('trips')->orderBy('code')->get();

        return view('admin.trains.index', compact('trains'));
    }

    public function create(): View
    {
        return view('admin.trains.create', ['train' => new Train()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Train::create($this->validated($request));

        return redirect()->route('admin.trains.index')->with('status', 'Train created.');
    }

    public function edit(Train $train): View
    {
        return view('admin.trains.edit', compact('train'));
    }

    public function update(Request $request, Train $train): RedirectResponse
    {
        $train->update($this->validated($request, $train));

        return redirect()->route('admin.trains.index')->with('status', 'Train updated.');
    }

    public function destroy(Train $train): RedirectResponse
    {
        $train->delete();

        return redirect()->route('admin.trains.index')->with('status', 'Train deleted.');
    }

    private function validated(Request $request, ?Train $train = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:trains,code'.($train ? ','.$train->id : '')],
            'total_seats' => ['required', 'integer', 'min:1', 'max:2000'],
        ]);
    }
}
