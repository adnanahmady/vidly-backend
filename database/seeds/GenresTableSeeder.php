<?php

use Illuminate\Database\Seeder;

class GenresTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    DB::table('genres')->insert(
      [[
        "slug" => "action",
        "name" => "Action",
      ], [
        "slug" => "thriller",
        "name" => "Thriller"
      ], [
        "slug" => "comedy",
        "name" => "Comedy",
      ]]
    );
  }
}
