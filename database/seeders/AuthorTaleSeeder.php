<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class AuthorTaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = "https://deschide.md/api/authors/types.json";
        $resp = Http::get($url);

        foreach($resp->object()->items as $item) {

            $typeUrl = Http::get("https://deschide.md/api/authors/types/{$item->id}.json")->object();

            dump($typeUrl);



        }

    }
}
