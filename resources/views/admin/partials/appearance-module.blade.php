{{-- ═══════════════════════════════════════════════════
     Appearance Module — Skin Picker Grid
     Include via: @include('admin.partials.appearance-module')
     ═══════════════════════════════════════════════════ --}}

@php
    $skins      = config('skins.available', []);
    $activeSkin = auth()->check() && auth()->user()->skin
                    ? auth()->user()->skin
                    : config('skins.default', 'cyber');
@endphp

<div class="card">
    <div class="card-body pt-4 pb-3">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="card-title mb-0 pb-0" style="border:none;">
                <i class="bi bi-palette me-1"></i> Appearance
            </h5>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="togglePreviewBtn" title="Toggle skeleton preview">
                <i class="bi bi-bar-chart-fill"></i>
            </button>
        </div>
        <p class="text-muted mb-3" style="font-size:.84rem;">
            Choose a skin to customize the look of your dashboard. Changes apply instantly.
        </p>

        {{-- ── Skin Grid ── --}}
        <div class="skins-grid">
            @foreach($skins as $skin)
                <div class="skin-card {{ $skin['id'] === $activeSkin ? 'selected' : '' }}"
                     data-skin-id="{{ $skin['id'] }}"
                     title="{{ $skin['name'] }}: {{ $skin['desc'] }}">

                    {{-- Checkmark for selected --}}
                    <div class="selected-check" style="{{ $skin['id'] === $activeSkin ? '' : 'display:none' }}">
                        <i class="bi bi-check"></i>
                    </div>

                    {{-- Preview area: bars (default) + skeleton (toggle) --}}
                    <div class="skin-preview sk-{{ $skin['id'] }}">
                        <div class="preview-bars" style="display:flex">
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                            <div class="bar"></div>
                        </div>
                        <div class="preview-skeleton" style="display:none">
                            <div class="skel-line"></div>
                            <div class="skel-line"></div>
                            <div class="skel-line"></div>
                            <div class="skel-dot"></div>
                        </div>
                    </div>

                    {{-- Label --}}
                    <div class="skin-info">
                        <div class="skin-name">{{ $skin['name'] }}</div>
                        <p class="skin-desc">{{ $skin['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Actions ── --}}
        <div class="appearance-actions">
            <button type="button" class="btn-apply" id="skinApplyBtn">
                <i class="bi bi-palette saved-icon"></i> Apply Skin
            </button>
            <button type="button" class="btn-ghost" id="skinResetBtn">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>

        {{-- ── Mini live preview ── --}}
        <div class="chat-preview">
            <div class="chat-preview-label">Live Preview</div>
            <div class="chat-bubble chat-bubble-ai">
                Welcome to Meezan School! This is how cards and text will look.
            </div>
            <div class="chat-bubble chat-bubble-user">
                Looks great — I love this skin!
            </div>
        </div>
    </div>
</div>
