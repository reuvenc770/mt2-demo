@if ($breadcrumbs)
    @foreach ($breadcrumbs as $breadcrumb)
        @if ($breadcrumb->url && !$breadcrumb->last)
            <md-button target="_self" ng-href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</md-button>

            <span><md-icon md-svg-src="img/icons/ic_chevron_right_white_36px.svg"></md-icon></span>
        @else
            <md-button class="md-warn">{{ $breadcrumb->title }}</md-button>
        @endif
    @endforeach
@endif
