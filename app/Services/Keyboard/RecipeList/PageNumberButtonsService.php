<?php

namespace App\Services\Keyboard\RecipeList;

use App\Enums\CallbackAction\CallbackAction;

class PageNumberButtonsService {
    public static function createPageNumbers(
        int $categoryId,
        int $offset,
        int $recipeCount,
        int $limit,
    ): array
    {
        $numberButtons = [];
        $currentPage = (int)floor($offset / $limit);
        $totalPages = (int)ceil($recipeCount / $limit);

        if ($totalPages <= 8) {
            for ($page = 0; $page < $totalPages; $page++) {
                $text = (string)($page + 1);
                if ($page === $currentPage) {
                    $text = '•' . $text . '•';
                }

                $numberButtons[] = [
                    'text' => $text,
                    'callback_data' => json_encode([
                        'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                        'offset' => $page * $limit,
                        'cat_id' => $categoryId,
                    ]),
                ];
            }
            return $numberButtons;
        }

        $visiblePages = 8;
        $edgePages = 3;
        $pages = [];

        for ($i = 0; $i < min($edgePages, $totalPages); $i++) {
            $pages[] = $i;
        }

        $startMiddle = max($edgePages, $currentPage - floor(($visiblePages - $edgePages * 2) / 2));
        $endMiddle = min($totalPages - $edgePages, $currentPage + ceil(($visiblePages - $edgePages * 2) / 2));

        for ($i = $startMiddle; $i < $endMiddle; $i++) {
            $pages[] = $i;
        }

        for ($i = max($totalPages - $edgePages, $endMiddle); $i < $totalPages; $i++) {
            $pages[] = $i;
        }

        $pages = array_values(array_unique($pages));
        $previousPage = -1;

        foreach ($pages as $page) {
            if ($previousPage !== -1 && $page > $previousPage + 1) {
                $numberButtons[] = [
                    'text' => '...',
                    'callback_data' => json_encode([
                        'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                        'offset' => ($previousPage + 1) * $limit,
                        'cat_id' => $categoryId,
                    ]),
                ];
            }

            $text = (string)($page + 1);
            if ($page === $currentPage) {
                $text = '•' . $text . '•';
            }

            $numberButtons[] = [
                'text' => $text,
                'callback_data' => json_encode([
                    'a' => CallbackAction::RECIPE_LIST_NAVIGATION->value,
                    'offset' => $page * $limit,
                    'cat_id' => $categoryId,
                ]),
            ];

            $previousPage = $page;
        }

        return $numberButtons;
    }
}
