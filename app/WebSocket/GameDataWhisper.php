<?php namespace App\WebSocket;

use App\Games\Kernel\Game;

class GameDataWhisper extends WebSocketWhisper {

    public function event(): string {
        return "GameData";
    }

    public function process($data): array {
        $game = Game::find($data->api_id);
        if($game == null) return \App\APIResponse::reject(-3, 'Unknown API game id');
        return \App\APIResponse::success($game->data());
    }

}
