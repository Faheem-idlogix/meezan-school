@extends('admin.layout.master')
@section('title', 'Search Results')
@section('content')
<main id="main" class="main">
    <div class="pagetitle">
        <h1><i class="bi bi-search me-2"></i>Search Results</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Search</li>
            </ol>
        </nav>
    </div>

    <section class="section">
        {{-- Search bar --}}
        <div class="card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('global.search') }}" class="d-flex gap-2">
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="Search students, vouchers, invoices, teachers, classes..." autofocus>
                    <button class="btn btn-primary"><i class="bi bi-search me-1"></i> Search</button>
                </form>
            </div>
        </div>

        @if(strlen($q) < 2)
            <div class="alert alert-info"><i class="bi bi-info-circle me-1"></i> Please enter at least 2 characters to search.</div>
        @elseif(empty($results))
            <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i> No results found for "<strong>{{ e($q) }}</strong>".</div>
        @else
            @foreach($results as $category => $items)
                <div class="card mb-3">
                    <div class="card-header py-2">
                        <h5 class="card-title mb-0">{{ $category }} <span class="badge bg-primary ms-1">{{ $items->count() }}</span></h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td style="width:40px;" class="text-center">
                                                <i class="{{ $item['icon'] }}" style="font-size:1.2rem;"></i>
                                            </td>
                                            <td>
                                                <a href="{{ $item['url'] }}" class="text-decoration-none fw-semibold" style="color:#012970;">
                                                    {{ $item['title'] }}
                                                </a>
                                                @if($item['sub'])
                                                    <br><small class="text-muted">{{ $item['sub'] }}</small>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ $item['url'] }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-arrow-right"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </section>
</main>
@endsection
