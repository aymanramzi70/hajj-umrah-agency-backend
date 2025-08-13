@props(['route', 'title', 'count', 'bg' => 'primary'])

<div class="col-md-4">
    <a href="{{ route($route) }}" class="card text-white bg-{{ $bg }} h-100 text-decoration-none d-block">
        <div class="card-header">{{ $title }}</div>
        <div class="card-body">
            <h5 class="card-title">عدد {{ $title }}:</h5>
            <p class="card-text fs-3">{{ $count }}</p>
        </div>
    </a>
</div>
