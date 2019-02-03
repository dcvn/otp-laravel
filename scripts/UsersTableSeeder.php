<?php
// Example seeder
use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $user = new User();
        $user->forceFill([
            'name'              => 'Admin',
            'email'             => 'admin@example.com',
            'email_verified_at' => now(),
            'password'          => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
            'remember_token'    => str_random(10),
        ]);
        $user->save();

        // By default, all factory user passwords are "secret".
        factory(User::class, 10)->create();
    }
}
