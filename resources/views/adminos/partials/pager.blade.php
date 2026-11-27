@if($page->total() > 0)
  <div class="pager">
    <span class="muted">{{ $page->firstItem() }}–{{ $page->lastItem() }} of {{ $page->total() }}</span>
    @if($page->previousPageUrl()) <a class="btn" href="{{ $page->previousPageUrl() }}">Previous</a> @endif
    @if($page->nextPageUrl()) <a class="btn" href="{{ $page->nextPageUrl() }}">Next</a> @endif
  </div>
@endif
