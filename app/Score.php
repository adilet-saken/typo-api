<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scoreType()
    {
        return $this->belongsTo('App\ScoreType');
    }

    public function toArray()
    {
        return [
            'wpm' => $this->wpm,
            'type' => $this->scoreType->toArray()
        ];
    }
}
