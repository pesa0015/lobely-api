<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Bookshelf;
use App\Book;
use Faker\Factory;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = json_decode(file_get_contents(database_path() . '/seeds/Data/Users.json'));
        $faker = Factory::create();
        
        foreach ($users->users as $user) {
            $newUser = User::create([
                'facebook_id' => $user->facebook_id,
                'name' => $user->name,
                'slug' => User::generateSlug($user->name),
                'gender' => $user->gender,
                'interested_in_gender' => $user->interested_in_gender,
                'email' => $user->email,
                'profile_img' => $user->profile_img,
                'birth_date' => $user->birth_date,
                'password' => bcrypt($user->password)
            ]);
            foreach ($user->books as $book) {
                if (!isset($book->title)) {
                    continue;
                }
                $book = Book::where('title', $book->title)->first();
                $newUser->books()->attach($book->id, [
                    'comment' => $faker->sentence(15)
                ]);
            }
        }

        factory('App\User', 20)->create()->each(function ($user) use ($faker) {
            if (rand(0, 1)) {
                $books = Book::whereNotIn('id', $user->books()->get()->pluck('id')->toArray())->inRandomOrder()->take(rand(3, 15))->get();
                foreach ($books as $book) {
                    Bookshelf::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'comment' => ucfirst($faker->words(rand(3, 15), true)),
                    ]);
                }
            }
        });
    }
}
