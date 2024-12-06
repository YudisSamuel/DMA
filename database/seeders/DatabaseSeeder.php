<?php

   use App\Models\User;
   use Illuminate\Database\Seeder;
   use Illuminate\Support\Facades\Hash;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           User::create([
               'username' => 'samuel',
               'password' => Hash::make('password123'),
           ]);
       }
   }
