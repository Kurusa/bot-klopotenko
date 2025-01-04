<?php

namespace App\Services\Handlers\Updates;

use App\Http\Controllers\InlineQuery;
use App\Models\Message;
use App\Utils\Update as CustomUpdate;

class InlineQueryHandler implements UpdateHandlerInterface
{
    public function supports(CustomUpdate $update): bool
    {
        return $update->getInlineQuery() !== null;
    }

    public function handle(CustomUpdate $update): void
    {
        request()->get('user')->messages()->save(new Message([
            'text' => 'inline query: ' . $update->getInlineQuery()->getQuery()
        ]));

        app()->make(InlineQuery::class, ['update' => $update])->handle();
    }
}
