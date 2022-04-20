<?php namespace App\WebSocket;

use App\Games\Kernel\Extended\ExtendedGame;
use App\Games\Kernel\Game;

class FinishWhisper extends WebSocketWhisper {

    public function event(): string {
        return 'Finish';
    }

    public function process($data): array {
        $game = \App\Game::where('_id', $data->id)->first();
        if($game == null) return \App\APIResponse::reject(1, 'Invalid game id');
        if($game->status !== 'in-progress') return \App\APIResponse::reject(2, 'Game is finished');

        $api_game = Game::find($game->game);
        if(!($api_game instanceof ExtendedGame)) return \App\APIResponse::reject(3, 'Unsupported game operation');

        $api_game->finish($game);
        return \App\APIResponse::success([
            'game' => $game->toArray()
        ]);
    }

}
