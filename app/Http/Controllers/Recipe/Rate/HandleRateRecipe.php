<?php

namespace App\Http\Controllers\Recipe\Rate;

use App\Http\Controllers\BaseCommand;

class HandleRateRecipe extends BaseCommand
{
    public function handle(): void
    {
        $this->user
            ->finishedRecipes()
            ->where('recipe_id', $this->update->getCallbackQueryByKey('recipe_id'))
            ->update(['rating' => $this->update->getCallbackAction()->getValue()]);

        $this->getBot()->deleteMessageById($this->update->getCallbackQueryMessageId());

        $this->getBot()->sendText(__('texts.thanks_for_rating'));
    }
}
