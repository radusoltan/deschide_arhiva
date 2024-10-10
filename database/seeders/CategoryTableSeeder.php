<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach(config('translatable.locales') as $locale) {

            $url = "http://arhiva.deschide.md/api/sections?page=1&items_per_page=100&language=$locale";
            $response = Http::get($url);

            app()->setLocale($locale);

            $allSections = collect($response->object()->items);

            $idsRO = [1,2,3,4,5,6, 7, 8, 10, 20, 22, 23, 25, 26];
            $idsEN = [1,2,3,4,5,6,7,8];
            $idsRU = [1,2,3,4,5,6,7,8,23,25,26];

            $filteredSections = new Collection();

            if ($locale === 'ro'){
                $filteredSections = $allSections->filter(function ($section) use ($idsRO){
                    return in_array($section->number, $idsRO);
                });
            } elseif ($locale === 'en'){
                $filteredSections = $allSections->filter(function ($section) use ($idsEN){
                    return in_array($section->number, $idsEN);
                });
            } elseif ($locale === 'ru'){
                $filteredSections = $allSections->filter(function ($section) use ($idsRU){
                    return in_array($section->number, $idsRU);
                });
            }

            foreach ($filteredSections->all() as $item){
                $category = Category::where("old_number", $item->number)->first();

                if (!$category) {
                    Category::create([
                        'old_number' => $item->number,
                        'title' => ucfirst($item->title),
                        'slug' => Str::slug($item->title),
                        'in_menu' => true,
                    ]);
                } else {
                    $category->update([
                        'old_number' => $item->number,
                        'title' => ucfirst($item->title),
                        'slug' => Str::slug($item->title),
                        'in_menu' => true,
                    ]);
                }

            }

        }
    }
}
