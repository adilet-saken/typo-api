<?php

namespace App\Http\Controllers;

use App\BestScore;

use Illuminate\Http\Request;

class ScoreController extends Controller
{
    public function leaderboard(Request $request)
    {
        $lang = $request->header('Accept-Language', 'en');
        $user = $request->user;
        $this->validateParams($request, ['type']);
        $type = $this->validateType($request);
        $page = $request->input('page', 1);
        $perPage = 10;
        $bestScores = BestScore::where('score_type_id', $type->id)->with(['score', 'user'])->get()->sortByDesc('score.wpm')->filter(function ($bestScore) {
            return $bestScore->user->username !== null;
        });
        $bestScores = $bestScores->forPage($page, $perPage)->values();
        $count = $perPage * ($page - 1);
        $bestScores = $bestScores->transform(function ($bestScore, $key) use (&$count, $lang, $user) {
            $count += 1;
            $unit = $bestScore->scoreType->name === 'timer' ? $this->getTranslation('sec', $lang) : $this->getTranslation('wpm', $lang);
            $result = [
                'username' => $bestScore->user->username,
                'score' => $bestScore->score->wpm,
                'unit' => $unit,
                'place' => ''
            ];
            if ($bestScore->user->id === $user->id) {
                $result['place'] = strval($count);
            }
            if ($count === 1) {
                $result['place'] = $this->getTranslation('first', $lang);
            } elseif ($count === 2) {
                $result['place'] = $this->getTranslation('second', $lang);
            } elseif ($count === 3) {
                $result['place'] = $this->getTranslation('third', $lang);
            }
            return $result;
        });
        return $this->sendResponse($this::SUCCESS, 'Leaderboard is sent.', $bestScores);
    }
}
