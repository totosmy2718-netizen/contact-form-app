<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag;
use Faker\Factory as Faker;

class ContactSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ja_JP');

        $categoryIds = Category::pluck('id');
        $tagIds = Tag::pluck('id');

        for ($i = 0; $i < 20; $i++) {
            $contact = Contact::create([
                'category_id' => $categoryIds->random(),
                'first_name' => $faker->firstName(),
                'last_name' => $faker->lastName(),
                'gender' => $faker->numberBetween(1, 3),
                'email' => $faker->unique()->safeEmail(),
                'tel' => $faker->numerify('###########'),
                'address' => $faker->address(),
                'building' => $faker->optional()->secondaryAddress(),
                'detail' => $faker->realText(100),
            ]);

            $contact->tags()->attach(
                $tagIds->random(rand(1, 3))->all()
            );
        }
    }
}