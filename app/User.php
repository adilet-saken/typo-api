<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function scores()
    {
        return $this->hasMany('App\Score');
    }

    public function bestScores()
    {
        return $this->hasMany('App\BestScore');
    }

    public function toArray()
    {
        return [
            'username' => $this->username
        ];
    }
}
