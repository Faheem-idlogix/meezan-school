@extends('admin.layout.master')
@section('title', 'Appearance — ' . setting('school_name', 'School'))
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-palette me-2"></i>Appearance</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Appearance</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            <div class="col-lg-8">
                @include('admin.partials.appearance-module')
            </div>
            <div class="col-lg-4">
                {{-- Tips card --}}
                <div class="card">
                    <div class="card-body pt-4 pb-3">
                        <h5 class="card-title mb-0 pb-0" style="border:none;">
                            <i class="bi bi-lightbulb me-1"></i> Tips
                        </h5>
                        <ul class="mt-3" style="font-size:.85rem; padding-left:1.2rem;">
                            <li class="mb-2">Click any skin card to <strong>instantly preview</strong> it.</li>
                            <li class="mb-2">Press <strong>"Apply Skin"</strong> to save your choice permanently.</li>
                            <li class="mb-2"><strong>"Reset"</strong> returns to the default Cyber Dark skin.</li>
                            <li class="mb-2">Your preference is remembered even if you log out.</li>
                            <li class="mb-2">Toggle the <i class="bi bi-bar-chart-fill"></i> button to switch between bar and skeleton previews.</li>
                        </ul>

                        <hr>
                        <h6 class="mb-2" style="font-size:.82rem; font-weight:600;">Current Skin</h6>
                        <div class="d-flex align-items-center gap-2">
                            @php
                                $currentSkin = auth()->user()->skin ?? config('skins.default', 'cyber');
                                $skinMeta = collect(config('skins.available'))->firstWhere('id', $currentSkin);
                            @endphp
                            <div class="sk-{{ $currentSkin }}" style="width:36px;height:36px;border-radius:8px;flex-shrink:0;"></div>
                            <div>
                                <div style="font-size:.88rem;font-weight:600;">{{ $skinMeta['name'] ?? ucfirst($currentSkin) }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $skinMeta['desc'] ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
