<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Nilai;
use App\Models\Answer;
use App\Models\Tryout;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id) {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'soal' => 'nullable|string',
            'image' => 'nullable|image',
            'subject' => 'required',
            'jawaban_benar' => 'nullable',
            'tryout_id' => 'required',
            'pilihan_a_text' => 'nullable|string',
            'pilihan_b_text' => 'nullable|string',
            'pilihan_c_text' => 'nullable|string',
            'pilihan_d_text' => 'nullable|string',
            'pilihan_e_text' => 'nullable|string',
            'skor_a' => 'nullable|numeric', // Ubah menjadi numeric
            'skor_b' => 'nullable|numeric', // Ubah menjadi numeric
            'skor_c' => 'nullable|numeric', // Ubah menjadi numeric
            'skor_d' => 'nullable|numeric', // Ubah menjadi numeric
            'skor_e' => 'nullable|numeric', // Ubah menjadi numeric
        ]);

        // Ambil data dari request
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('soal', 'public');
        }

        // Simpan soal
        Question::create([
            'tryout_id' => $request->tryout_id,
            'subject_id' => $request->subject,
            'soal' => $request->soal,
            'image' => $imagePath,
            'pilihan_a' => $this->getPilihan('A', $request),
            'pilihan_b' => $this->getPilihan('B', $request),
            'pilihan_c' => $this->getPilihan('C', $request),
            'pilihan_d' => $this->getPilihan('D', $request),
            'pilihan_e' => $this->getPilihan('E', $request),
            'jawaban_benar' => $request->jawaban_benar ?? null,
            'skor_a' => $request->skor_A ?? null,
            'skor_b' => $request->skor_B ?? null,
            'skor_c' => $request->skor_C ?? null,
            'skor_d' => $request->skor_D ?? null,
            'skor_e' => $request->skor_E ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Soal berhasil ditambahkan.']);
    }

    // Pastikan fungsi getPilihan didefinisikan dengan benar
    private function getPilihan($option, $request)
    {
        // Cek apakah ada gambar untuk pilihan
        $pilihanGambar = $request->file("pilihan_{$option}_gambar");
        if ($pilihanGambar) {
            // Jika ada gambar, simpan dan kembalikan path gambar
            return $pilihanGambar->store('pilihan', 'public');
        }

        // Ambil teks pilihan
        $pilihanText = $request->input("pilihan_{$option}_text");

        // Jika tidak ada gambar dan teks, kembalikan null atau nilai default
        return !empty($pilihanText) ? $pilihanText : null;
    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $tryout = Tryout::find($id);
        $questions = Question::where('tryout_id', $id)->latest()->get();

        $now = now();

        if ($now->lt($tryout->start_time) || $now->gt($tryout->end_time)) {
            return redirect()->route('tryout.index')
                ->with('error', 'Try Out ini hanya tersedia dari ' . Carbon::parse($tryout->start_time)->format('H:i') . ' sampai ' .
                    Carbon::parse($tryout->end_time)->format('H:i'));
        }

        return view('pages.soal.index', [
            'tryout' => $tryout,
            'questions' => $questions,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $answers = $request->input('answers'); // ['question_id' => 'jawaban']

        foreach ($answers as $questionId => $userAnswer) {
            $question = Question::find($questionId);

            if (!$question) continue;

            // Lewati jika sudah ada jawaban sebelumnya
            $existingAnswer = Answer::where('user_id', auth()->id())
                ->where('question_id', $questionId)
                ->first();

            if ($existingAnswer) continue;

            $poin = 0;

            if ($question->subject_id == 3) {
                $skorField = 'skor_' . strtolower($userAnswer);
                $poin = $question->$skorField ?? 0;
            } else {
                // Jika kosong, dianggap salah (nilai 0)
                $poin = strtoupper($userAnswer) === strtoupper($question->jawaban_benar) ? 5 : 0;
            }

            Answer::create([
                'user_id'       => auth()->id(),
                'question_id'   => $questionId,
                'jawaban_user'  => strtoupper($userAnswer),
                'poin'          => $poin,
            ]);
        }

        // Setelah semua jawaban disimpan
        $userId = auth()->id();
        $tryoutId = $request->tryout_id ?? optional($question)->tryout_id;

        // Ambil semua jawaban user pada tryout tersebut beserta relasi soalnya
        $answersGrouped = Answer::with('question')
            ->where('user_id', $userId)
            ->whereHas('question', function ($query) use ($tryoutId) {
                $query->where('tryout_id', $tryoutId);
            })
            ->get()
            ->groupBy(function ($answer) {
                return $answer->question->subject_id;
            });

        // Hitung dan simpan ke tabel nilai/score
        foreach ($answersGrouped as $subjectId => $answers) {
            $totalPoin = $answers->sum('poin');

            // Atur ambang kelulusan berdasarkan subject
            $passingScore = match ((int)$subjectId) {
                1 => 65,
                2 => 80,
                3 => 165,
                default => 0,
            };

            $status = $totalPoin >= $passingScore ? 'Lulus' : 'Tidak Lulus';

            // Simpan ke tabel nilai
            Nilai::updateOrCreate(
                [
                    'user_id'    => $userId,
                    'tryout_id'  => $tryoutId,
                    'subject_id' => $subjectId,
                ],
                [
                    'total_poin' => $totalPoin,
                    'status'     => $status,
                ]
            );
        }
        return redirect()->route('nilai.show', $tryoutId);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        //


    }
}
