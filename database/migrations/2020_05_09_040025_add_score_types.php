<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\ScoreType;

class AddScoreTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $scoreTypes = ['arcade', 'time', 'classic'];
        foreach ($scoreTypes as $scoreType) {
            $type = new ScoreType;
            $type->name = $scoreType;
            $type->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('score_types')->truncate();
    }
}
