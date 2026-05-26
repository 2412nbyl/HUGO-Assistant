{{-- Usage: @include('partials.page-header', ['backUrl' => route('...'), 'backLabel' => '...', 'title' => '...', 'subtitle' => optional]) --}}
<div class="page-header-block">
    @if(!empty($backUrl))
    <a href="{{ $backUrl }}" class="back-link">
        <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6" /></svg>
        {{ $backLabel ?? 'Kembali' }}
    </a>
    @endif
    <h2 class="page-title">{{ $title }}</h2>
    @if(!empty($subtitle))
    <p class="page-subtitle">{{ $subtitle }}</p>
    @endif
</div>
