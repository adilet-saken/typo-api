<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BestScore extends Model
{
    protected $with = ['user', 'scoreType', 'score'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scoreType()
    {
        return $this->belongsTo('App\ScoreType');
    }

    public function score()
    {
        return $this->belongsTo('App\Score');
    }

    public function toArray()
    {
        return [
            'user' => $this->user->toArray(),
            'score' => $this->score->toArray()
        ];
    }
}
