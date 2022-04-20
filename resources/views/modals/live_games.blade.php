@php
    if(!isset($data)) die('Invalid type');
    $show = isset($_COOKIE['show']) ? intval($_COOKIE['show']) : 10;
@endphp

@if($data === 'mine')
    <table class="live-table">
        <thead>
            <tr>
                <th>
                    {{ __('general.bets.game') }}
                </th>
                <th>
                    {{ __('general.bets.player') }}
                </th>
                <th class="d-none d-md-table-cell">
                    {{ __('general.bets.time') }}
                </th>
                <th class="d-none d-md-table-cell">
                    {{ __('general.bets.bet') }}
                </th>
                <th class="d-none d-md-table-cell">
                    Multiplier
                </th>
                <th>
                    Result
                </th>
            </tr>
        </thead>
        <tbody class="live_games live_games_selector"></tbody>
        <script type="text/javascript">
            $(document).ready(function() {
                @foreach(\App\Game::latest()->where('demo', '!=', true)->where('user', auth()->user()->_id)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($show)->get()->reverse() as $game)
                $.insertLiveGame({!! json_encode([
                        'game' => $game->toArray(),
                        'user' => \App\User::where('_id', $game->user)->first()->toArray(),
                        'metadata' => \App\Games\Kernel\Game::find($game->game)->metadata()->toArray()
                    ]) !!});
                $.putNextInLiveQueue(true);
                @endforeach
            });
        </script>
    </table>
@endif

@if($data === 'all')
    <table class="live-table">
        <thead>
            <tr>
                <th>
                    {{ __('general.bets.game') }}
                </th>
                <th>
                    {{ __('general.bets.player') }}
                </th>
                <th class="d-none d-md-table-cell">
                    {{ __('general.bets.time') }}
                </th>
                <th class="d-none d-md-table-cell">
                    {{ __('general.bets.bet') }}
                </th>
                <th class="d-none d-md-table-cell">
                    Multiplier
                </th>
                <th>
                    Result
                </th>
            </tr>
        </thead>
        <tbody class="live_games live_games_selector"></tbody>
        <script type="text/javascript">
            $(document).ready(function() {
                @foreach(\App\Game::latest()->where('demo', '!=', true)->where('user', '!=', null)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($show)->get()->reverse() as $game)
                $.insertLiveGame({!! json_encode([
                        'game' => $game->toArray(),
                        'user' => \App\User::where('_id', $game->user)->first()->toArray(),
                        'metadata' => \App\Games\Kernel\Game::find($game->game)->metadata()->toArray()
                    ]) !!});
                $.putNextInLiveQueue(true);
                @endforeach
            });
        </script>
    </table>
@endif

@if($data === 'lucky_wins')
    <table class="live-table">
        <thead>
        <tr>
            <th>
                {{ __('general.bets.game') }}
            </th>
            <th>
                {{ __('general.bets.player') }}
            </th>
            <th class="d-none d-md-table-cell">
                {{ __('general.bets.time') }}
            </th>
            <th class="d-none d-md-table-cell">
                {{ __('general.bets.bet') }}
            </th>
            <th class="d-none d-md-table-cell">
                    Multiplier
                </th>
                <th>
                    Result
            </th>
        </tr>
        </thead>
        <tbody class="live_games live_games_selector"></tbody>
        <script type="text/javascript">
            $(document).ready(function() {
                @foreach(\App\Game::latest()->where('multiplier', '>=', 10)->where('demo', '!=', true)->where('user', '!=', null)->where('status', 'win')->take($show)->get()->reverse() as $game)
                $.insertLiveGame({!! json_encode([
                        'game' => $game->toArray(),
                        'user' => \App\User::where('_id', $game->user)->first()->toArray(),
                        'metadata' => \App\Games\Kernel\Game::find($game->game)->metadata()->toArray()
                    ]) !!});
                $.putNextInLiveQueue(true);
                @endforeach
            });
        </script>
    </table>
@endif

@if($data === 'high_rollers')
    <table class="live-table">
        <thead>
        <tr>
            <th>
                {{ __('general.bets.game') }}
            </th>
            <th>
                {{ __('general.bets.player') }}
            </th>
            <th class="d-none d-md-table-cell">
                {{ __('general.bets.time') }}
            </th>
            <th class="d-none d-md-table-cell">
                {{ __('general.bets.bet') }}
            </th>
            <th class="d-none d-md-table-cell">
                    Multiplier
                </th>
                <th>
                    Result
            </th>
        </tr>
        </thead>
        <tbody class="live_games live_games_selector"></tbody>
        <script type="text/javascript">
            $(document).ready(function() {
                @foreach(\App\Game::latest()->where('demo', '!=', true)->where('user', '!=', null)->where('status', '!=', 'in-progress')->where('status', '!=', 'cancelled')->take($show)->get()->reverse() as $game)
                    @if($game->wager < floatval(\App\Currency\Currency::find($game->currency)->option('high_roller_requirement'))) @continue @endif
                    $.insertLiveGame({!! json_encode([
                            'game' => $game->toArray(),
                            'user' => \App\User::where('_id', $game->user)->first()->toArray(),
                            'metadata' => \App\Games\Kernel\Game::find($game->game)->metadata()->toArray()
                        ]) !!});
                    $.putNextInLiveQueue(true);
                @endforeach
            });
        </script>
    </table>
@endif
