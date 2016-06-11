<?php

use Illuminate\Database\Seeder;
use App\Meta;

class MetaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Meta::create(['name' => 'token_expire_time','value' => 2]);
        $meta = new Meta;
        $meta->name = 'token_expire_time';
     	$meta->value = 100;
     	$meta->save();
           
    }
}
