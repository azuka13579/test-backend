<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. BUAT USER (Creator & Viewer)
        // Buat 1 Akun Creator Utama untuk Testing
        $creator = User::create([
            'username' => 'ahza',
            'email' => 'ahza@test.com',
            'password' => Hash::make('12345'),
            'role' => 'creator',
        ]);

        // Buat 5 User Viewer tambahan secara random
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'username' => 'viewer_' . $i,
                'email' => "viewer$i@test.com",
                'password' => Hash::make('password'),
                'role' => 'viewer',
            ]);
        }

        $allUsers = User::all();
        $faker = \Faker\Factory::create();

        // 2. BUAT 30 POST
        for ($i = 1; $i <= 30; $i++) {
            $post = Post::create([
                'user_id' => $creator->id, // Semua post dibuat oleh budi_creator
                'title' => $faker->sentence(6),
                'content' => $faker->paragraphs(3, true),
                'image_url' => 'https://picsum.photos/seed/' . Str::random(5) . '/800/600',
            ]);

            // 3. BUAT KOMENTAR UNTUK SETIAP POST
            // Setiap post akan dapet 2 - 4 komen dari user random
            $jumlahKomen = rand(2, 4);
            for ($j = 1; $j <= $jumlahKomen; $j++) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $allUsers->random()->id, // Komen dari user acak (bisa creator/viewer)
                    'content' => $faker->sentence(),
                ]);
            }
        }

        $this->command->info('Seeding selesai! 1 Creator, 5 Viewer, 30 Post, dan ratusan komen berhasil dibuat.');
    }
}