@if ($paginator->hasPages())
    <div class="pagination-shell">
        <p>
            Showing {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }} records
        </p>

        <div class="pagination-actions">
            @if ($paginator->onFirstPage())
                <span class="page-button is-disabled">Previous</span>
            @else
                <a class="page-button" href="{{ $paginator->previousPageUrl() }}">Previous</a>
            @endif

            <span class="page-current">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

            @if ($paginator->hasMorePages())
                <a class="page-button" href="{{ $paginator->nextPageUrl() }}">Next</a>
            @else
                <span class="page-button is-disabled">Next</span>
            @endif
        </div>
    </div>
@endif
