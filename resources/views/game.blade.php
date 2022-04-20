@php
    $game = \App\Games\Kernel\Game::find($data);
    if($game == null || $game->isDisabled()) {
        header('Location: /');
        die();
    }
@endphp

<div class="container-fluid">
    <div class="game-container">
        <div class="row">
            <div class="col {{-- d-none d-md-block --}}">
                <div class="game-sidebar"><div class="game-sidebar-sticky"></div></div>
            </div>
            <div class="col">
                <div class="game-content"></div>
            </div>
        </div>
    </div>
    <div class="game-footer"></div>
</div>

@if(!auth()->guest())
    @php $latest_game = \App\Game::latest()->where('game', $data)->where('user', auth()->user()->_id)->where('status', 'in-progress')->first(); @endphp
    @if(!is_null($latest_game))
        <script type="text/javascript">
            window.restoreGame = {
                'game': {!! json_encode($latest_game->makeHidden('server_seed')->makeHidden('nonce')->makeHidden('data')->toArray()) !!},
                'history': {!! json_encode($latest_game->data['history']) !!},
                'user_data': {!! json_encode($latest_game->data['user_data']) !!}
            };
        </script>
    @else
        <script type="text/javascript">
            window.restoreGame = undefined;
        </script>
    @endif
@endif
