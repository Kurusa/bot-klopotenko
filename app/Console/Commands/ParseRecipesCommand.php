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
        'https://klopotenko.com/dva-v-odnomu-marynovani-ogirky-z-shampinjonamy/',
        'https://klopotenko.com/grog-z-chaem-i-konjakom/',
        'https://klopotenko.com/tort-pavlova-z-bananom-i-zhuravlynou/',
        'https://klopotenko.com/yak-robutu-nydnuj-rus-nenydnum/',
        'https://klopotenko.com/grushevyj-tort-z-solenou-karamellu/',
        'https://klopotenko.com/mlunci-yagidni-pechinkovi-z-brokoli/',
    ];

    protected $signature = 'parse-recipes';

    private $url;
    use ButtonsTrait;

    public function handle()
    {
        //$categories = Category::whereNotNull('source_key')->get();
        $categories = Category::where('source_key', 'yevropejska')->get();
        $sourceUrls = Recipe::all()->pluck('source_url');
        $client = new Client();
        foreach ($categories as $category) {
            $page = 0;
            while (true) {
                if ($category->source_key === 'ukrayinska') {
                    $query = '{"ranna_recipe_cuisine":"ukrayinska","error":"","m":"","p":0,"post_parent":"","subpost":"","subpost_id":"","attachment":"","attachment_id":0,"name":"","pagename":"","page_id":0,"second":"","minute":"","hour":"","day":0,"monthnum":0,"year":0,"w":0,"category_name":"","tag":"","cat":"","tag_id":"","author":"","author_name":"","feed":"","tb":"","paged":0,"meta_key":"","meta_value":"","preview":"","s":"","sentence":"","title":"","fields":"","menu_order":"","embed":"","category__in":[],"category__not_in":[],"category__and":[],"post__in":[],"post__not_in":[],"post_name__in":[],"tag__in":[],"tag__not_in":[],"tag__and":[],"tag_slug__in":[],"tag_slug__and":[],"post_parent__in":[],"post_parent__not_in":[],"author__in":[],"author__not_in":[],"search_columns":[],"ignore_sticky_posts":false,"suppress_filters":false,"cache_results":true,"update_post_term_cache":true,"update_menu_item_cache":false,"lazy_load_term_meta":true,"update_post_meta_cache":true,"post_type":"","posts_per_page":8,"nopaging":false,"comments_per_page":"50","no_found_rows":false,"taxonomy":"ranna_recipe_cuisine","term":"ukrayinska","order":"DESC"}';
                } elseif ($category->source_key === 'yevropejska') {
                    $query = '{"ranna_recipe_cuisine":"yevropejska","error":"","m":"","p":0,"post_parent":"","subpost":"","subpost_id":"","attachment":"","attachment_id":0,"name":"","pagename":"","page_id":0,"second":"","minute":"","hour":"","day":0,"monthnum":0,"year":0,"w":0,"category_name":"","tag":"","cat":"","tag_id":"","author":"","author_name":"","feed":"","tb":"","paged":0,"meta_key":"","meta_value":"","preview":"","s":"","sentence":"","title":"","fields":"","menu_order":"","embed":"","category__in":[],"category__not_in":[],"category__and":[],"post__in":[],"post__not_in":[],"post_name__in":[],"tag__in":[],"tag__not_in":[],"tag__and":[],"tag_slug__in":[],"tag_slug__and":[],"post_parent__in":[],"post_parent__not_in":[],"author__in":[],"author__not_in":[],"search_columns":[],"ignore_sticky_posts":false,"suppress_filters":false,"cache_results":true,"update_post_term_cache":true,"update_menu_item_cache":false,"lazy_load_term_meta":true,"update_post_meta_cache":true,"post_type":"","posts_per_page":8,"nopaging":false,"comments_per_page":"50","no_found_rows":false,"taxonomy":"ranna_recipe_cuisine","term":"yevropejska","order":"DESC"}';
                } else {
                    $query = '{"page":0,"ranna_recipe_category":"' . $category->source_key . '","error":"","m":"","p":0,"post_parent":"","subpost":"","subpost_id":"","attachment":"","attachment_id":0,"name":"","pagename":"","page_id":0,"second":"","minute":"","hour":"","day":0,"monthnum":0,"year":0,"w":0,"category_name":"","tag":"","cat":"","tag_id":"","author":"","author_name":"","feed":"","tb":"","paged":0,"meta_key":"","meta_value":"","preview":"","s":"","sentence":"","title":"","fields":"","menu_order":"","embed":"","category__in":[],"category__not_in":[],"category__and":[],"post__in":[],"post__not_in":[],"post_name__in":[],"tag__in":[],"tag__not_in":[],"tag__and":[],"tag_slug__in":[],"tag_slug__and":[],"post_parent__in":[],"post_parent__not_in":[],"author__in":[],"author__not_in":[],"search_columns":[],"ignore_sticky_posts":false,"suppress_filters":false,"cache_results":true,"update_post_term_cache":true,"update_menu_item_cache":false,"lazy_load_term_meta":true,"update_post_meta_cache":true,"post_type":"","posts_per_page":8,"nopaging":false,"comments_per_page":"50","no_found_rows":false, "taxonomy":"ranna_recipe_category","term":"' . $category->source_key . '","order":"DESC"}';
                }
                $response = $client->post('https://klopotenko.com/wp-admin/admin-ajax.php', [
                    'form_params' => [
                        'page' => $page,
                        'query' => $query,
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
                    if (in_array($this->trimString($recipeUrl), self::EXCLUDED_URLS) || $sourceUrls->contains($recipeUrl)) {
                        continue;
                    }

                    $this->url = $recipeUrl;

                    $recipeData = $this->fetchRecipeData($recipeUrl, $category->id);
                    if ($recipeData) {
                        $this->saveRecipeToDatabase($recipeData);
                    }
                }

                $page++;
            }
        }
    }

    private function fetchRecipeData(string $url, int $categoryId): array
    {
        try {
            $html = file_get_contents(
                $url,
                false,
                stream_context_create(['http' => ['follow_location' => 1]])
            );
        } catch (ErrorException) {
            return [];
        }

        $xpath = $this->loadHtml($html);
        if (!$xpath) {
            dd($url);
        }
        $breadcrumb = $xpath->query(".//span[@class=' 2 breadcrumb-first']//a")->item(0);
        try {
            if (in_array(trim($breadcrumb->textContent), ['Здорове харчування', 'Поради', 'Новини'])) {
                return [];
            }
        } catch (ErrorException) {
        }

        $sourceUrl = $url;
        $title = $this->extractTitle($xpath);
        $advice = $this->extractAdvice($xpath);
        $recipeImage = $this->extractRecipeImage($xpath);
        $portions = $this->extractPortions($xpath);
        $time = $this->extractTime($xpath);
        $complexity = $this->extractComplexity($xpath);
        $steps = $this->extractSteps($xpath);
        $ingredients = $this->extractIngredients($xpath);

        return compact('advice', 'categoryId', 'sourceUrl', 'title', 'portions', 'time', 'steps', 'complexity', 'ingredients', 'recipeImage');
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
                'image_url' => $recipeData['recipeImage'],
                'advice' => $recipeData['advice'],
            ]);

            foreach ($recipeData['ingredients'] as $ingredient) {
                if (!Ingredient::where('title', $ingredient['title'])->exists()) {
                    Ingredient::insert($ingredient);
                }
            }

            $ingredientIds = Ingredient::whereIn('title', collect($recipeData['ingredients'])->pluck('title')->toArray())->get(['id']);
            $recipe->ingredients()->attach($ingredientIds);

            $result = [];
            foreach ($recipeData['steps'] as $step) {
                $result[] = new Step([
                    'description' => $step['description'],
                    'recipe_id' => $recipe->id,
                    'image_url' => $step['image_url'],
                ]);
            }
            $recipe->steps()->saveMany($result);
        } catch (QueryException $e) {
            Log::info($e->getMessage());
        }
    }

    public function extractAdvice(DOMXPath $xpath): string
    {
        $paragraphs = $xpath->query('//div[@class="item-description"]/p');

        $textContent = '';
        foreach ($paragraphs as $paragraph) {
            $textContent .= $paragraph->textContent;
        }

        return $textContent;
    }

    public function extractRecipeImage(DOMXPath $xpath): string
    {
        try {
            return $xpath
                ->query(".//img[@class='attachment-ranna-size10 size-ranna-size10 wp-post-image']")
                ->item(0)
                ->getAttribute('src');
        } catch (\Error) {
            dd($this->url);
        }
    }

    private function extractPortions(DOMXPath $xpath): int
    {
        return (int)$xpath
            ->query(".//div[@class='feature-sub-title']")
            ->item(1)?->textContent;
    }

    private function extractTime(DOMXPath $xpath): string
    {
        $node = $xpath->query(".//div[@class='feature-sub-title total_time']")->item(0);
        return $node ? $this->trimString(str_replace('total_time_text ', '', $node->textContent)) : '';
    }

    private function extractComplexity(DOMXPath $xpath): string
    {
        $node = $xpath->query(".//div[@class='feature-sub-title']")->item(2);
        return $node ? config('constants')['complexity_map'][mb_strtolower($node->textContent)] : '';

    }

    private function extractSteps(DOMXPath $xpath): array
    {
        $steps = [];
        $node = $xpath->query("//div[contains(@class, 'row direction-st')]//p/text()");

        $query = "//div[contains(@class, 'item-figure mobile_hide')]";
        foreach ($node as $key => $item) {
            try {
                if ($xpath->query($query)->item($key)->childNodes->item(1)) {
                    $imageUrl = $xpath->query($query)->item($key)->childNodes->item(1)->getAttribute('data-src');
                }
            } catch (ErrorException) {
            }

            $steps[] = [
                'description' => $this->trimString($item->textContent),
                'image_url' => $imageUrl ?? '',
            ];
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
        return $xpath
            ->query(".//h1[@class='item-title']")
            ->item(0)
            ->textContent ?? '';
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
