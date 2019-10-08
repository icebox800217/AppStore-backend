<?php

use Illuminate\Database\Seeder;
use App\Members;

class MembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Members::class,10)->create();
    }
}
