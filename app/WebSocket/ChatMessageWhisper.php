<?php namespace App\WebSocket;

class ChatMessageWhisper extends WebSocketWhisper {

    public function event(): string {
        return 'ChatMessage';
    }

    public function process($data): array {
        if(strlen($data->message) < 1 || strlen($data->message) > 10000) return \App\APIResponse::reject(1, 'Message is too short or long');
        if($this->user->mute != null && !$this->user->mute->isPast()) return \App\APIResponse::reject(2, 'User is banned');

        $message = \App\Chat::create([
            'user' => $this->user->toArray(),
            'vipLevel' => $this->user->vipLevel(),
            'data' => mb_substr($data->message, 0, 400),
            'type' => 'message',
            'channel' => $this->user->chatChannel()
        ]);

        event(new \App\Events\ChatMessage($message));

        if(\App\Settings::where('name', 'quiz_active')->first()->value === 'true') {
            $sanitize = function ($input) {
                return mb_strtolower(preg_replace("/[^A-Za-zА-Яа-я0-9\-]/u", '', $input));
            };

            if($sanitize($data->message) === $sanitize(\App\Settings::where('name', 'quiz_answer')->first()->value)) {
                \App\Settings::where('name', 'quiz_active')->update(['value' => 'false']);
                $this->user->balance($this->user->clientCurrency())->add(floatval($this->user->clientCurrency()->option('quiz')), \App\Transaction::builder()->message('Quiz')->get());
                event(new \App\Events\QuizAnswered($this->user, \App\Settings::where('name', 'quiz_question')->first()->value, \App\Settings::where('name', 'quiz_answer')->first()->value));
            }
        }

        return \App\APIResponse::success();
    }

}
