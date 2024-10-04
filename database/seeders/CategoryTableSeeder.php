<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
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

            $url = "https://deschide.md/api/sections?page=1&items_per_page=100&language=$locale";
            $response = Http::get($url);

            app()->setLocale($locale);

            foreach ($response->object()->items as $item){
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
