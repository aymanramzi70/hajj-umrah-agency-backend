@props(['title', 'amount', 'bg'])

<div class="col-md-4">
    <div class="card text-white bg-{{ $bg }} h-100">
        <div class="card-header">{{ $title }}</div>
        <div class="card-body">
            <h5 class="card-title">{{ $title }}</h5>
            <p class="card-text fs-3">{{ number_format($amount, 2) }} SR</p>
        </div>
    </div>
</div>
