@props(['estado'])

<span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full whitespace-nowrap"
      style="background-color: {{ $estado->color }}18; color: {{ $estado->color }};">
    <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $estado->color }}"></span>
    {{ $estado->nombre }}
</span>
