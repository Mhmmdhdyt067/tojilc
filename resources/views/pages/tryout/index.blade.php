@extends('layouts.app')

@section('content')
<div class="container my-3">
    <div class="row">
        <div class="col-xl-12">
            <!--begin::List Widget 5-->
            <div class="card card-custom bg-light-primary card-stretch gutter-b">
                <!--begin::header-->
                <div class="card-header border-0">
                    <h1 class="card-title font-weight-bolder text-primary">Try Out <span class="svg-icon svg-icon-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M8,3 L8,3.5 C8,4.32842712 8.67157288,5 9.5,5 L14.5,5 C15.3284271,5 16,4.32842712 16,3.5 L16,3 L18,3 C19.1045695,3 20,3.8954305 20,5 L20,21 C20,22.1045695 19.1045695,23 18,23 L6,23 C4.8954305,23 4,22.1045695 4,21 L4,5 C4,3.8954305 4.8954305,3 6,3 L8,3 Z" fill="#000000" opacity="0.3" />
                                    <path d="M11,2 C11,1.44771525 11.4477153,1 12,1 C12.5522847,1 13,1.44771525 13,2 L14.5,2 C14.7761424,2 15,2.22385763 15,2.5 L15,3.5 C15,3.77614237 14.7761424,4 14.5,4 L9.5,4 C9.22385763,4 9,3.77614237 9,3.5 L9,2.5 C9,2.22385763 9.22385763,2 9.5,2 L11,2 Z" fill="#000000" />
                                    <rect fill="#000000" opacity="0.3" x="10" y="9" width="7" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3" x="7" y="9" width="2" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3" x="7" y="13" width="2" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3" x="10" y="13" width="7" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3" x="7" y="17" width="2" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.3" x="10" y="17" width="7" height="2" rx="1" />
                                </g>
                            </svg>
                        </span></h1>


                    @can('isAdmin')
                    <div class="card-toolbar">
                        <a href="/tryout/create" class="btn btn-primary">
                            <i class="flaticon2-plus"></i> Buat TryOut
                        </a>
                    </div>
                    @endcan
                </div>
                <!--end::header-->
                <!--begin::Body-->
                <div class="card-body pt-0">
                    <!--begin::Item-->
                    @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true"><i class="ki ki-close"></i></span>
                        </button>
                    </div>
                    @endif

                    @if (auth()->user()->role == 'siswa')
                    @if($siswatryouts->isEmpty())
                    <div class="alert alert-custom alert-light-danger fade show mb-5" role="alert">
                        <div class="alert-icon"><i class="flaticon-warning"></i></div>
                        <div class="alert-text">Tidak ada Tryout saat ini, Silahkan cek kembali nanti!</div>
                        <div class="alert-close">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true"><i class="ki ki-close"></i></span>
                            </button>
                        </div>
                    </div>
                    @else
                    @foreach ($siswatryouts as $siswatryout)
                    <a href="/tryout/{{$siswatryout->id}}">
                        <div class="d-flex align-items-center mb-6">

                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1 py-2">
                                <h5 class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">{{ $siswatryout->title }}</h5>
                                <p class="text-muted font-weight-bold">{{ $siswatryout->waktu }} menit</p>
                            </div>
                            <!--end::Text-->
                        </div>
                    </a>
                    @endforeach
                    @endif
                    @else
                    @foreach ($admintryouts as $admintryout)
                    <a href="/tryout/{{$admintryout->id}}">
                        <div class="d-flex align-items-center mb-6">

                            <!--begin::Text-->
                            <div class="d-flex flex-column flex-grow-1 py-2">
                                <h5 class="text-dark-75 font-weight-bold text-hover-primary font-size-lg mb-1">{{ $admintryout->title }}</h5>
                                <p class="text-muted font-weight-bold">{{ $admintryout->waktu }} menit</p>
                            </div>
                            <!--end::Text-->
                        </div>
                    </a>
                    @endforeach
                    @endif


                    <!--end::Item-->

                </div>
                <!--end::Body-->
            </div>
            <!--end::List Widget 5-->
        </div>

    </div>
</div>
@endsection