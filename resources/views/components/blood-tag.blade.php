@props(['group'])

{{-- Looks like a label on a blood bag: punched hole, perforation, group. --}}
<span {{ $attributes->merge(['class' => 'inline-flex items-stretch rounded-sm border border-brand-600 bg-white text-xs font-semibold leading-none text-brand-700 tabular-nums']) }}>
    <span class="flex items-center border-r border-dashed border-brand-600 px-1.5">
        <span class="h-1.5 w-1.5 rounded-full border border-brand-600"></span>
    </span>
    <span class="px-2 py-1.5">{{ $group }}</span>
</span>