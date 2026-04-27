@props([
    'prevUrl' => '#',
    'nextUrl' => '#',
    'page' => 1,
])

<nav class="flex items-center justify-between mt-6" aria-label="Pagination">
    <x-ui.button :href="$prevUrl" variant="outline" size="sm">Previous</x-ui.button>
    <span class="text-sm text-[#6B7280]">Page {{ $page }}</span>
    <x-ui.button :href="$nextUrl" variant="outline" size="sm">Next</x-ui.button>
</nav>
