<?php

namespace App\Http\Controllers;

use App\Models\Tryout;
use App\Models\Subject;
use App\Http\Requests\StoreTryoutRequest;
use App\Http\Requests\UpdateTryoutRequest;
use Carbon\Carbon;

class TryoutController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $now = now()->toDateTimeString();

        $admintryouts = Tryout::get();
        $siswatryouts = TryOut::where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->get();
        return view('pages.tryout.index', compact('admintryouts', 'siswatryouts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::all();
        return view('pages.tryout.create', compact('subjects')); //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTryoutRequest $request)
    {
        $validated = $request->validated();

        $tryout = TryOut::create($validated);

        return redirect()->route('tryout.index')->with('success', 'Try Out berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tryout = Tryout::with('questions')->findOrFail($id);
        
        return view('pages.soal.create', [
            'tryout' => $tryout,
            'questions' => $tryout->questions,
            'id' => $tryout->id,
            'title' => $tryout->title,
            'waktu' => $tryout->waktu
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tryout $tryout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTryoutRequest $request, Tryout $tryout)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tryout $tryout)
    {
        //
    }
}
