@if ($paginator->hasPages())
    <div class="pagination-bar">
        <p>显示 {{ $paginator->firstItem() }} - {{ $paginator->lastItem() }}，共 {{ $paginator->total() }} 条</p>

        <div class="pagination-actions">
            @if ($paginator->onFirstPage())
                <span class="secondary-button is-disabled">上一页</span>
            @else
                <a class="secondary-button" href="{{ $paginator->previousPageUrl() }}">上一页</a>
            @endif

            <span class="pagination-current">第 {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }} 页</span>

            @if ($paginator->hasMorePages())
                <a class="secondary-button" href="{{ $paginator->nextPageUrl() }}">下一页</a>
            @else
                <span class="secondary-button is-disabled">下一页</span>
            @endif
        </div>
    </div>
@endif
