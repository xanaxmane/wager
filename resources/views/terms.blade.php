<div class="container-fluid">
    @if($data === 'gaming_policy')
        {!! __('policy.gaming_policy') !!}
    @elseif($data === 'privacy_notice')
        {!! __('policy.privacy_notice') !!}
    @elseif($data === 'privacy_policy')
        {!! __('policy.privacy_policy') !!}
    @elseif($data === 'terms_and_conditions')
        {!! __('policy.terms_and_conditions') !!}
    @else
        404
    @endif
</div>
