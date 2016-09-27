<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 27/09/2016
 * Time: 12:56 AM
 */
class LeaderBoardModel extends Model
{
    public function __construct()
    {
        $collection = 'leaderboard';
        parent::__construct($collection);
    }

    public function ScorePost($data)
    {
        // Verify Integer fields
        $positiveIntegerInputFields = ['LeaderboardId', 'UserId'];
        foreach ($positiveIntegerInputFields as $integerInputField) {
            if(!Validators::validateInteger($integerInputField, $data, true)) {
                Application::app()->respond()->sendError('Bad ' . $integerInputField);
            }
        }

        if(!Validators::validateInteger('Score', $data)) {
            Application::app()->respond()->sendError('Bad Score');
        }

        $current = $this->collection->findOne([
            'UserId' => new MongoInt32($data['UserId']),
            'LeaderboardId' => new MongoInt32($data['LeaderboardId'])
        ], ['Score'], ['sort' => -1]);

        if(!$current) {
            $r = $this->collection->insert([
                'UserId' => new MongoInt32($data['UserId']),
                'LeaderboardId' => new MongoInt32($data['LeaderboardId']),
                'Score' => new MongoInt32($data['Score'])
            ]);
        } elseif($current['Score'] > (int)$data['Score']) {
            $r = $this->collection->insert([
                'UserId' => new MongoInt32($data['UserId']),
                'LeaderboardId' => new MongoInt32($data['LeaderboardId']),
                'Score' => new MongoInt32($data['Score'])
            ]);
        } else {
            $r = ['ok' => 1];
        }

        return $r;
    }
}