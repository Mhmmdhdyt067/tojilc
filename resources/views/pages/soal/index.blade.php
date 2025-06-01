@extends('layouts.app')

@section('content')

<form action="{{ route('question.update', $tryout->id) }}" method="POST">
    @csrf
    @method('PATCH')
    <div class="container text-center">
        <div class="row my-4">
            <div class="container-fluid text-center">

                <h2>{{ $tryout->title }}</h2>
            </div>
        </div>

        <!-- Navigasi Soal -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card card-custom card-shadowless card-stretch gutter-b card-spacer">
                    <div class="card-toolbar">
                        <h5 id="timer" class="text-danger font-weight-bold"></h5>

                    </div>
                    <div class="card-body text-center">
                        @foreach($questions as $index => $question)
                        <button type="button"
                            class="btn btn-outline-primary m-1 question-button"
                            data-question="{{ $question->id }}"
                            id="btn-{{ $question->id }}">
                            <span class="btn-text">{{ $index + 1 }}</span>
                        </button>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>

        <!-- Tampilan Soal (Satu per waktu) -->
        <div class="row">
            <div class="col-xl-12">
                @foreach ($questions as $question)
                <div class="card card-custom bg-light card-stretch gutter-b question-card"
                    id="question-{{ $question->id }}" style="{{ $loop->first ? '' : 'display: none;' }}">

                    <!-- Header -->
                    <div class="card-header border-0">
                        <h3 class="card-title font-weight-bold text-dark">Soal {{ $loop->iteration }}</h3>
                    </div>

                    <!-- Soal (gambar + teks jika ada) -->
                    <div class="card-body pt-2">
                        @if ($question->image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $question->image) }}" width="200" alt="Soal Gambar">
                        </div>
                        @endif

                        @if ($question->soal)
                        <h5>{{ $question->soal }}</h5>
                        @endif

                        <!-- Input untuk jawaban -->
                        <input type="hidden" name="answers[{{ $question->id }}]" value="" />

                        <div class="form-group">
                            <div class="radio-inline">

                                <div class="pilihan-wrapper">
                                    @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                    <label class="radio pilihan-item">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" {{ $loop->first ? 'required' : '' }} />
                                        <span></span>
                                        @php
                                        $isi = $question->{'pilihan_' . strtolower($option)};
                                        @endphp
                                        @if(filter_var($isi, FILTER_VALIDATE_URL) || strpos($isi, 'pilihan/') === 0)
                                        <img src="{{ asset('storage/' . $isi) }}" alt="Pilihan {{ strtoupper($option) }}" style="max-width: 40%; height: auto;">
                                        @else
                                        {{ $isi }}
                                        @endif
                                    </label>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>

        <button type="submit" class="btn btn-success mt-3">Selesai dan Kirim</button>
    </div>
</form>

@push('scripts')
@php
$waktu = $tryout->waktu * 60;
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const totalTime = @json($waktu); // waktu dalam detik
        let timeLeft = totalTime;
        const timerDisplay = document.getElementById('timer');
        const form = document.querySelector('form');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerDisplay.textContent = `Sisa waktu: ${minutes}m ${seconds < 10 ? '0' : ''}${seconds}s`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('Waktu habis! Jawaban Anda akan otomatis dikirim.');
                form.submit();
            } else {
                timeLeft--;
            }
        }

        updateTimer();
        const timerInterval = setInterval(updateTimer, 1000);

        // Navigasi soal
        const buttons = document.querySelectorAll('.question-button');
        const cards = document.querySelectorAll('.question-card');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-question');
                cards.forEach(card => card.style.display = 'none');

                buttons.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });

                document.getElementById('question-' + targetId).style.display = 'block';

                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
            });
        });

        // Ceklis soal yang sudah dijawab
        document.querySelectorAll('input[type=radio]').forEach(input => {
            input.addEventListener('change', function() {
                const qId = this.name.match(/\d+/)[0];
                const btn = document.getElementById('btn-' + qId);
                const textSpan = btn.querySelector('.btn-text');

                if (textSpan.innerText !== '✔️') {
                    textSpan.innerHTML = '✔️';
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-success', 'animate__animated', 'animate__bounceIn');
                }
            });
        });
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            alert('Waktu habis! Jawaban Anda akan otomatis dikirim.');
            form.submit(); // Ini akan jalan kalau input hidden sudah ditambahkan
        }
    });
</script>

<!-- CDN Animate.css -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
@endpush

@push('style')
<style>
    .pilihan-wrapper {
        display: flex;
        flex-direction: column;
    }

    .pilihan-item {
        display: flex;
        align-items: center;
        gap: 10px;
        /* Jarak antara radio dan teks/gambar */
        margin-bottom: 12px;
        /* Jarak antar opsi */
    }
</style>
@endpush

@endsection