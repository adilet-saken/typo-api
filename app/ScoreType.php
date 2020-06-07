<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScoreType extends Model {
    public function toArray()
    {
        return [
            'name' => $this->name
        ];
    }
}
