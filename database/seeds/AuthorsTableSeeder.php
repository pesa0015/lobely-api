<?php

use Illuminate\Database\Seeder;
use App\Author;

class AuthorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authors = json_decode(file_get_contents(database_path() . '/seeds/Data/Authors.json'));
        
        foreach ($authors->authors as $author) {
            Author::create((array)$author);
        }
    }
}
