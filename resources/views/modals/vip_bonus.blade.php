@if(isset($data))
    @if(auth()->guest() || auth()->user()->vipLevel() == 0) 404 @endif

    @if(auth()->user()->weekly_bonus_obtained)
        <div class="unavailable">
            <div class="slanting">
                <div class="unavailableContent">
                    {!! __('vip.bonus.timeout') !!}
                </div>
            </div>
        </div>
    @endif

    <div class="font-weight-bold mb-2">{{ __('vip.bonus.progress_title') }}</div>
    <div class="bonus-image">
        <div class="progress">
            @php $percent = number_format(auth()->user()->weekly_bonus ?? 0, 2, '.', ''); @endphp
            <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%;">{{ $percent }}%</div>
        </div>
        <div class="btn btn-primary mt-2 @if((auth()->user()->weekly_bonus ?? 0) < 0.1) disabled @endif">{!! __('general.take', ['value' => number_format(((auth()->user()->weekly_bonus ?? 0) / 100) * auth()->user()->vipBonus(), 8, '.', ''), 'icon' => auth()->user()->clientCurrency()->icon()]) !!}</div>
    </div>

    @switch(auth()->user()->vipLevel())
        @case(0) @php $vip = "none"; @endphp @break
        @case(1) @php $vip = "bronze"; @endphp @break
        @case(2) @php $vip = "silver"; @endphp @break
        @case(3) @php $vip = "gold"; @endphp @break
        @case(4) @php $vip = "platinum"; @endphp @break
        @case(5) @php $vip = "diamond"; @endphp @break
    @endswitch

    <div class="font-weight-bold mt-2" style="font-size: 1.05em">{{ __('vip.bonus.title') }}</div>
    <div class="vipDesc">{!! __('vip.bonus.description', [
            'vip' => "<svg style='width: 14px; height: 14px;'><use href='#vip-$vip'></use></svg>"
        ]) !!}</div>
@else
    <div class="vip_bonus modal">
        <div class="content">
            <i class="fas fa-close-symbol"></i>
            <div class="vip_bonus_content"></div>
        </div>
    </div>
@endif
