<?php

namespace App\Http\Controllers;

use App\Score;
use App\ScoreType;
use App\BestScore;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user;
        $this->sendResponse($this::SUCCESS, 'User data is sent.', $user);
    }

    public function save(Request $request)
    {
        $this->validateParams($request, ['username']);
        $user = $request->user;
        $username = $request->input('username');
        $user->username = $username;
        $user->save();
        return $this->sendResponse($this::SUCCESS,'Username is saved.');
    }

    public function score(Request $request)
    {
        $this->validateParams($request, ['wpm', 'type']);
        // Check if type is allowed
        $type = $this->validateType($request);
        // Save score
        $wpm = $request->input('wpm');
        $user = $request->user;
        $score = new Score;
        $score->user_id = $user->id;
        $score->wpm = $wpm;
        $score->score_type_id = $type->id;
        $score->save();
        // Check if score beats previous best score
        $bestScore = $user->bestScores->where('score_type_id', $type->id)->first();
        if ($bestScore && $bestScore->score->wpm < $wpm || !$bestScore) {
            if ($bestScore) {
                $bestScore->delete();
            }
            $bestScore = new BestScore;
            $bestScore->user_id = $user->id;
            $bestScore->score_type_id = $type->id;
            $bestScore->score_id = $score->id;
            $bestScore->save();
        }
        // Calculate how many scores of users new score beats
        $scores = Score::where('score_type_id', $type->id)->get();
        $betterThan = $scores->where('wpm', '<=', $wpm);
        $percentage = $betterThan->count() / $scores->count() * 100;
        $percentageRounded = round($percentage, 2);
        $this->sendResponse($this::SUCCESS, 'Score is saved.', ['percentage' => $percentageRounded]);
    }

    public function stats(Request $request)
    {
        $lang = $request->header('Accept-Language', 'en');
        $user = $request->user;
        $stats = [];
        $types = ScoreType::all();
        $types->each(function ($type) use ($user, &$stats, $lang) {
            $scores = $user->scores->where('score_type_id', $type->id);
            if ($scores->count() > 0) {
                $average = $scores->avg('wpm');
                $averageRounded = round($average, 2);
                $unit = $type->name === 'timer' ? $this->getTranslation('sec', $lang) : $this->getTranslation('wpm', $lang);
                $stats[] = [
                    'score'  => $averageRounded,
                    'unit' => $unit,
                    'title' => $this->getTranslation($type->name, $lang)
                ];
                $bestScore = $user->bestScores->where('score_type_id', $type->id)->first();
                $stats[] = [
                    'score'  => $bestScore->score->wpm,
                    'unit' => $unit,
                    'title' => $this->getTranslation("best_{$type->name}", $lang)
                ];
            }
        });
        // Calculate average of all and last 10 games
        $scores = $user->scores;
        $lastScores = $user->scores->sortByDesc('created_at')->take(10);
        $averageAll = round($scores->avg('wpm'), 2);
        $stats[] = [
            'score' => $averageAll,
            'unit' => $this->getTranslation('wpm', $lang),
            'title' => $this->getTranslation('average_all', $lang)
        ];
        $averageLastScores = round($lastScores->avg('wpm'), 2);
        $stats[] = [
            'score' => $averageLastScores,
            'unit' => $this->getTranslation('wpm', $lang),
            'title' => $this->getTranslation('average_last', $lang)
        ];
        $this->sendResponse($this::SUCCESS, 'User statistic is sent.', $stats);
    }

    public function leaderboard(Request $request)
    {
        $user = $request->user;
        // Calculate user's position in leaderboards for each score type
        $positions = [];
        $types = ScoreType::all();
        foreach ($types as $type) {
            $bestScores = BestScore::where('score_type_id', $type->id)->get()->sortByDesc('score.wpm')->filter(function ($bestScore) {
                return $bestScore->user->username !== null;
            })->values();
            $position = $bestScores->search(function ($score) use ($user) {
                return $score->user_id === $user->id;
            });
            if ($position !== false) {
                $positions[] = [
                    'type'     => $type->toArray(),
                    'position' => $position + 1,
                    'wpm'      => $bestScores->get($position)->score->wpm
                ];
            }
        }
        return $this->sendResponse($this::SUCCESS,'User leaderboard positions are sent.', $positions);
    }
}
