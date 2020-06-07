<?php

namespace App\Http\Controllers;

use App\ScoreType;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    protected const SUCCESS = 'success';
    protected const ERROR = 'error';

    protected function validateParams(Request $request, $params)
    {
        foreach ($params as $param) {
            if (!$request->input($param)) {
                $this->sendResponse($this::ERROR,"{$param} is required.");
            }
        }
    }

    protected function validateType(Request $request)
    {
        $type = ScoreType::firstWhere('name', $request->input('type'));
        if (!$type) {
            $this->sendResponse($this::ERROR, 'Wrong score type.');
        }
        return $type;
    }

    protected function sendResponse($type, $message, $data = null)
    {
        $body = [
            'status' => $type,
            'message' => $message
        ];
        if ($data) {
            $body['data'] = $data;
        }
        return response()->json($body)->throwResponse();
    }

    protected function getTranslation($word, $lang) {
        $translations = [
            'wpm' => [
                'en' => 'wpm',
                'ru' => 'wpm',
                'uk' => 'wpm'
            ],
            'sec' => [
                'en' => 'sec',
                'ru' => 'сек',
                'uk' => 'сек'
            ],
            'classic' => [
                'en' => 'Classic',
                'ru' => 'Классический',
                'uk' => 'Классический'
            ],
            'arcade' => [
                'en' => 'Arcade',
                'ru' => 'Аркадный',
                'uk' => 'Аркадний'
            ],
            'timer' => [
                'en' => 'Timer',
                'ru' => 'На время',
                'uk' => 'На час'
            ],
            'best_classic' => [
                'en' => 'Best Classic',
                'ru' => 'Топ классический',
                'uk' => 'Топ класичний'
            ],
            'best_arcade' => [
                'en' => 'Best Arcade',
                'ru' => 'Топ аркадный',
                'uk' => 'Топ аркадний'
            ],
            'best_timer' => [
                'en' => 'Best Timer',
                'ru' => 'Топ на время',
                'uk' => 'Топ на час'
            ],
            'average_all' => [
                'en' => 'Average of all time',
                'ru' => 'Среднее за все время',
                'uk' => 'Середнє за весь час'
            ],
            'average_last' => [
                'en' => 'Average of last 10 games',
                'ru' => 'Среднее последних 10 игр',
                'uk' => 'Середнє останніх 10 ігор'
            ],
            'first' => [
                'en' => '1st',
                'ru' => '1-ый',
                'uk' => '1-й'
            ],
            'second' => [
                'en' => '2nd',
                'ru' => '2-ой',
                'uk' => '2-й'
            ],
            'third' => [
                'en' => '3rd',
                'ru' => '3-ий',
                'uk' => '3-й'
            ]
        ];

        return $translations[$word][$lang];
    }
}
