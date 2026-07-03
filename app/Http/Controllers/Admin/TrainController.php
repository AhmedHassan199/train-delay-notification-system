<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TrainRequest;
use App\Models\Train;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TrainController extends Controller
{
    public function index(): View
    {
        $trains = Train::withCount('trips')->orderBy('code')->paginate(15);

        return view('admin.trains.index', compact('trains'));
    }

    public function create(): View
    {
        return view('admin.trains.create', ['train' => new Train()]);
    }

    public function store(TrainRequest $request): RedirectResponse
    {
        Train::create($request->validated());

        return redirect()->route('admin.trains.index')->with('status', 'Train created.');
    }

    public function edit(Train $train): View
    {
        return view('admin.trains.edit', compact('train'));
    }

    public function update(TrainRequest $request, Train $train): RedirectResponse
    {
        $train->update($request->validated());

        return redirect()->route('admin.trains.index')->with('status', 'Train updated.');
    }

    public function destroy(Train $train): RedirectResponse
    {
        $train->delete();

        return redirect()->route('admin.trains.index')->with('status', 'Train deleted.');
    }
}
