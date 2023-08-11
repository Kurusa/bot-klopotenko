<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use App\Traits\ButtonsTrait;
use DOMDocument;
use DOMXPath;
use ErrorException;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ParseRecipesCommand extends Command
{
    const EXCLUDED_URLS = [
        'https://klopotenko.com/salat-z-buryakom-i-suhariyamy-z-zhytniogo-hliba/',
        'https://klopotenko.com/grechka-u-zelenomu-chayi-z-tomatami-ta-kovbaskami/',
        'https://klopotenko.com/garbuzove-pyure-z-anisom/',
        'https://klopotenko.com/midii-v-vershkovomu-sousi/',
        'https://klopotenko.com/tykva-s-seldju-idealnaja-zakuska/',
    ];
    protected $signature = 'parse-recipes';
    use ButtonsTrait;

    public function handle()
    {
        $recipes = Recipe::all();
        foreach ($recipes as $recipe) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'follow_location' => 1,
                    ]
                ]);
                $html = file_get_contents($recipe->source_url, false, $context);
            } catch (ErrorException) {
                return [];
            }

            $dom = new DOMDocument();
            libxml_use_internal_errors(true);
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");

            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom); // $dom - ваш об'єкт DOMDocument

            $ingredients = $this->extractIngredients($xpath);
            Ingredient::insertOrIgnore($ingredients);
            $ingredientIds = Ingredient::whereIn('title', collect($ingredients)->pluck('title')->toArray())->get(['id']);
            $recipe->ingredients()->attach($ingredientIds);
        }

        exit();
        $categories = Category::whereNotNull('source_key')->get();
        $sourceUrls = Recipe::all()->pluck('source_url');
        $client = new Client();
        foreach ($categories as $category) {
            $page = 0;
            while (true) {
                $response = $client->post('https://klopotenko.com/wp-admin/admin-ajax.php', [
                    'form_params' => [
                        'page' => $page,
                        'query' => '{"page":0,"ranna_recipe_category":"' . $category->source_key . '","error":"","m":"","p":0,"post_parent":"","subpost":"","subpost_id":"","attachment":"","attachment_id":0,"name":"","pagename":"","page_id":0,"second":"","minute":"","hour":"","day":0,"monthnum":0,"year":0,"w":0,"category_name":"","tag":"","cat":"","tag_id":"","author":"","author_name":"","feed":"","tb":"","paged":0,"meta_key":"","meta_value":"","preview":"","s":"","sentence":"","title":"","fields":"","menu_order":"","embed":"","category__in":[],"category__not_in":[],"category__and":[],"post__in":[],"post__not_in":[],"post_name__in":[],"tag__in":[],"tag__not_in":[],"tag__and":[],"tag_slug__in":[],"tag_slug__and":[],"post_parent__in":[],"post_parent__not_in":[],"author__in":[],"author__not_in":[],"search_columns":[],"ignore_sticky_posts":false,"suppress_filters":false,"cache_results":true,"update_post_term_cache":true,"update_menu_item_cache":false,"lazy_load_term_meta":true,"update_post_meta_cache":true,"post_type":"","posts_per_page":8,"nopaging":false,"comments_per_page":"50","no_found_rows":false,"taxonomy":"ranna_recipe_category","term":"' . $category->source_key . '","order":"DESC"}',
                        'action' => 'loadmore',
                    ]
                ])->getBody()->getContents();
                if (empty($response)) {
                    break;
                }
                $xpath = $this->loadHtml($response);
                $recipeUrlNodes = $xpath->query(".//h3[@class='item-title']//a");
                foreach ($recipeUrlNodes as $recipeUrlNode) {
                    $recipeUrl = $recipeUrlNode->getAttribute('href');
                    if (in_array($this->trimString($recipeUrl), self::EXCLUDED_URLS)) {
                        continue;
                    }
                    if ($sourceUrls->contains($recipeUrl)) {
                        continue;
                    }
                    $recipeData = $this->fetchRecipeData($recipeUrl, $category->id);
                    $this->saveRecipeToDatabase($recipeData);
                }

                $page++;
            }
        }
    }

    private function fetchRecipeData(string $url, int $categoryId): array
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'follow_location' => 1,
                ]
            ]);
            $html = file_get_contents($url, false, $context);
        } catch (ErrorException) {
            return [];
        }

        $xpath = $this->loadHtml($html);
        if (!$xpath) {
            dd($url);
        }

        $sourceUrl = $url;
        $title = $this->extractTitle($xpath);
        $portions = $this->extractPortions($xpath);
        $time = $this->extractTime($xpath);
        $complexity = $this->extractComplexity($xpath);
        $steps = $this->extractSteps($xpath);
        $ingredients = $this->extractIngredients($xpath);

        return compact('categoryId', 'sourceUrl', 'title', 'portions', 'time', 'steps', 'complexity', 'ingredients');
    }

    private function saveRecipeToDatabase(array $recipeData): void
    {
        try {
            $recipe = Recipe::create([
                'category_id' => $recipeData['categoryId'],
                'title' => $recipeData['title'],
                'portions' => $recipeData['portions'],
                'time' => $recipeData['time'],
                'complexity' => $recipeData['complexity'],
                'source_url' => $recipeData['sourceUrl'],
            ]);

            Ingredient::insertOrIgnore($recipeData['ingredients']);
            $ingredientIds = Ingredient::whereIn('title', collect($recipeData['ingredients'])->pluck('title')->toArray())->get(['id']);
            $recipe->ingredients()->attach($ingredientIds);

            $result = [];
            foreach ($recipeData['steps'] as $step) {
                $result[] = new Step([
                    'description' => $step,
                    'recipe_id' => $recipe->id,
                ]);
            }
            $recipe->steps()->saveMany($result);
        } catch (QueryException $e) {
            Log::info($e->getMessage());
        }
    }

    private function extractPortions(DOMXPath $xpath): int
    {
        $node = $xpath->query(".//div[@class='feature-sub-title']")->item(1);
        return (int)$node?->textContent;
    }

    private function extractTime(DOMXPath $xpath): string
    {
        $node = $xpath->query(".//div[@class='feature-sub-title total_time']")->item(0);
        if ($node) {
            return $this->trimString(str_replace('total_time_text ', '', $node->textContent));
        }

        return '';
    }

    private function extractComplexity(DOMXPath $xpath): string
    {
        $node = $xpath->query(".//div[@class='feature-sub-title']")->item(2);
        if ($node) {
            return config('constants')['complexity_map'][mb_strtolower($node->textContent)];
        }

        return '';
    }

    private function extractSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $node = $xpath->query("//div[contains(@class, 'row direction-st')]//p/text()");
        foreach ($node as $item) {
            $steps[] = $this->trimString($item->textContent);
        }

        return $steps;
    }

    private function extractIngredients(DOMXPath $xpath): array
    {
        $ingredients = [];
        $node = $xpath->query("//label[starts-with(@for, 'checkbox_')]");
        foreach ($node as $item) {
            $ingredients[] = [
                'title' => $this->trimString($item->textContent),
            ];
        }

        return $ingredients;
    }

    private function extractTitle(DOMXPath $xpath): string
    {
        $titleNode = $xpath->query(".//h1[@class='item-title']")->item(0);
        return $titleNode->textContent ?? '';
    }

    private function loadHtml($html): DOMXPath|string
    {
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', "UTF-8");
        $dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    private function trimString(string $string): string
    {
        $string = preg_replace('/^\p{Z}+|\p{Z}+$/u', '', trim($string));
        return preg_replace('~\x{00a0}~siu', '', $string);
    }
}
