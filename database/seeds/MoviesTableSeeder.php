<?php

use Illuminate\Database\Seeder;

class MoviesTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('movies')->insert(
      [[
        "title" => "Terminator",
        "genre_id" => "1",
        "number_in_stock" => 6,
        "like" => false,
        "daily_rental_rate" => 2.5,
      ], [
        "title" => "Die Hard",
        "genre_id" => "1",
        "number_in_stock" => 5,
        "like" => false,
        "daily_rental_rate" => 2.5
      ], [
        "title" => "Get Out",
        "genre_id" => "2",
        "number_in_stock" => 8,
        "like" => false,
        "daily_rental_rate" => 3.5
      ], [
        "title" => "Trip to Italy",
        "genre_id" => "3",
        "number_in_stock" => 7,
        "like" => false,
        "daily_rental_rate" => 3.5
      ], [
        "title" => "Airplane",
        "genre_id" => "3",
        "number_in_stock" => 7,
        "like" => false,
        "daily_rental_rate" => 3.5
      ], [
        "title" => "Wedding Crashers",
        "genre_id" => "3",
        "number_in_stock" => 7,
        "like" => false,
        "daily_rental_rate" => 3.5
      ], [
        "title" => "Gone Girl",
        "genre_id" => "2",
        "number_in_stock" => 7,
        "like" => false,
        "daily_rental_rate" => 4.5
      ], [
        "title" => "The Sixth Sense",
        "genre_id" => "2",
        "number_in_stock" => 4,
        "like" => false,
        "daily_rental_rate" => 3.5
      ], [
        "title" => "The Avengers",
        "genre_id" => "1",
        "number_in_stock" => 7,
        "like" => false,
        "daily_rentalMoviesTableSeeder_rate" => 3.5
      ]]
    );

    DB::table('movies')
      ->where(['id' => '1'])
      ->update([
        "publish_date" => "2018-01-03T19=>04=>28.809Z"
      ]);
  }
}