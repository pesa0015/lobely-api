<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Bookshelf;
use App\Book;
use App\Heart;
use App\Constants\HeartStatus;
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

        $heartStatuses = [HeartStatus::PENDING, HeartStatus::APPROVED, HeartStatus::DENIED];
        
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

        factory('App\User', 20)->create()->each(function ($user) use ($faker, $heartStatuses) {
            if (rand(0, 1)) {
                $books = Book::whereNotIn('id', $user->books()->get()->pluck('id')->toArray())->inRandomOrder()->take(rand(3, 15))->get();
                foreach ($books as $book) {
                    Bookshelf::create([
                        'user_id' => $user->id,
                        'book_id' => $book->id,
                        'comment' => ucfirst($faker->words(rand(3, 15), true)),
                    ]);
                }

                $users = Bookshelf::whereNotIn('user_id', array_merge(
                    $user->heartsToMe()->get()->pluck('user_id')->toArray(),
                    $user->heartsToPartner()->get()->pluck('heart_user_id')->toArray()
                ))->inRandomOrder()->take(1, 3)->get();

                if (!$users->isEmpty()) {
                    foreach ($users as $heartToUser) {
                        if ($user->id === $heartToUser->user_id
                            || Heart::whereColumn('user_id', 'heart_user_id')->exists()) {
                            continue;
                        }

                        $status = $heartStatuses[rand(0, 2)];

                        $heart = factory('App\Heart')->create([
                            'user_id' => $user->id,
                            'heart_user_id' => $heartToUser->user_id,
                            'book_id' => $heartToUser->book_id,
                            'status'  => $status,
                        ]);

                        $user->heartsToPartner()->save($heart);

                        if ($status === HeartStatus::APPROVED) {
                            for ($i = 0; $i <= rand(5, 9); $i++) {
                                $heart->messages()->save(factory('App\Message')->create([
                                    'user_id' => rand(0, 1) ? $user->id : $heartToUser->user_id,
                                ]));
                            }
                        }
                    }
                }
            }
        });
    }
}
