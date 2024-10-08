<?php

namespace Database\Seeders;

use App\Models\Author;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AuthorTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $typesUrl = 'http://arhiva.deschide.md/api/authors/types.json';

        $response = Http::get($typesUrl);

        foreach ($response->object()->items as $type) {

            $url = 'http://arhiva.deschide.md/api/authors/types/' . $type->id . '.json';

            $authorsResult = Http::get($url);


            foreach($authorsResult->object()->authors as $author) {

                $number = $this->extractNumberFromUrl($author->link);

                $url = 'http://arhiva.deschide.md/api/authors/'.$number.'.json';

                $authorsResult = Http::get($url);

                $author = Author::where('old_number', $authorsResult->object()->id)->first();

                if (!$author){
                    Author::create([
                        'old_number' => $authorsResult->object()->id,
                        'first_name' => $authorsResult->object()->firstName,
                        'last_name' => $authorsResult->object()->lastName,
                        'full_name' => $authorsResult->object()->firstName.' '.$authorsResult->object()->lastName,
                        'slug' => Str::slug($authorsResult->object()->firstName . ' ' . $authorsResult->object()->lastName)
                    ]);
                }

            }
        }
    }

    public function extractNumberFromUrl($url)
    {
        $pattern = '/\d+$/'; // caută un șir de cifre la sfârșitul URL-ului
        if (preg_match($pattern, $url, $matches)) {
            return $matches[0]; // returnează numărul găsit
        }

        return null; // dacă nu a fost găsit niciun număr
    }

    private function getAuthor($id){
        $url = 'http://arhiva.deschide.md/api/authors/'.$id.'.json';
        $response = Http::get($url);

    }
}
