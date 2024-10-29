<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            "name" => "Admin",
            "email" => "admin@admin.com",
            "password" => Hash::make("sr324395"),
            "email_verified_at" => now(),
            'remember_token' => Str::random(10)
        ]);
        User::create([
            "name" => 'Igor Libec',
            'email' => "igor.liubec@deschide.md",
            "password" => Hash::make("Uituc1970!"),
            "email_verified_at" => now(),
            'remember_token' => Str::random(10)
        ]);
        User::create([
            "name" => 'Tudor Ionita',
            'email' => "tudor.ionita@deschide.md",
            "password" => Hash::make("Uituc1970!"),
            "email_verified_at" => now(),
            'remember_token' => Str::random(10)
        ]);
        User::create([
            "name" => 'Eliza Mihalache',
            'email' => "eliza.mihalache@deschide.md",
            "password" => Hash::make("Uituc1970!"),
            "email_verified_at" => now(),
            'remember_token' => Str::random(10)
        ]);
        User::create([
            "name" => 'Cristina Vlah',
            'email' => "cristina.vlah@deschide.md",
            "password" => Hash::make("Uituc1970!"),
            "email_verified_at" => now(),
            'remember_token' => Str::random(10)
        ]);
        User::create([
            "name" => 'Monica Scutaru',
            'email' => "monica.scutaru@deschide.md",
            "password" => Hash::make("Uituc1970!"),
            "email_verified_at" => now(),
            'remember_token' => Str::random(10)
        ]);
    }
}
