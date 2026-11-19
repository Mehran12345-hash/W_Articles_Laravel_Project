@if ($paginator->hasPages())
<nav class="pagination" role="navigation" aria-label="Pagination"><div class="pagination-info">Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</div><div class="pagination-controls">
    @if($paginator->onFirstPage())<span class="page-button disabled">← Previous</span>@else<a class="page-button" href="{{ $paginator->previousPageUrl() }}">← Previous</a>@endif
    <span class="page-current">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>
    @if($paginator->hasMorePages())<a class="page-button" href="{{ $paginator->nextPageUrl() }}">Next →</a>@else<span class="page-button disabled">Next →</span>@endif
</div></nav>
@endif
