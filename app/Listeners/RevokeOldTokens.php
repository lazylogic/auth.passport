<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

# event:generate 로 생성 될 때 Event Class 경로가 잘못 지정된다. 수정하자.
# use App\Events\Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\Events\AccessTokenCreated;

# Add : Passport 의 Token Model 추가
use Laravel\Passport\Token;

class RevokeOldTokens
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Access Token 이 새로 발급되면, 해당 유저의 다른 Personal Access Token 을 삭제 한다.
     *
     * @param  AccessTokenCreated  $event
     * @return void
     */
    public function handle( AccessTokenCreated $event )
    {
        Token::where( 'id', '<>', $event->tokenId )
            ->where( 'user_id', $event->userId )
            ->where( 'client_id', $event->clientId )
            ->delete();
    }
}