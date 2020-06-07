<?php

namespace App\Http\Controllers;

use App\ScoreType;

class ScoreTypeController extends Controller {
    public function showAll()
    {
        $types = ScoreType::all();
        return $this->sendResponse($this::SUCCESS, 'Score types are sent.', $types);
    }
}
