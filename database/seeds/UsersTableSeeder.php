<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Book;

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
        
        foreach ($users->users as $user) {
            $newUser = User::create([
                'facebook_id' => $user->facebook_id,
                'name' => $user->name,
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

                $newUser->books()->attach($book->id);
            }
        }
    }
}
