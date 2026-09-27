@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-lg bg-secondary/10 p-4 text-sm font-medium text-secondary text-center line-height-[1.5]']) }}>
        {{ $status }}
    </div>
@endif
