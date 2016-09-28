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

        $l_id = new MongoInt32($data['LeaderboardId']);
        $u_id = new MongoInt32($data['UserId']);

        $currentScore = $this->getUserScore($u_id, $l_id);

        $r = ['ok' => 1];

        $scoreUpdated = false;

        if(!$currentScore) {
            $r = $this->collection->insert([
                'UserId' => $u_id,
                'LeaderboardId' => $l_id,
                'Score' => new MongoInt32($data['Score'])
            ]);
            $scoreUpdated = true;
            $currentScore = $data['Score'];

            // Get Rank
            $this->_getRank($l_id, $data);
            // TODO: Update

        } elseif($currentScore < (int)$data['Score']) {
            $r = $this->collection->insert([
                'UserId' => $u_id,
                'LeaderboardId' => $l_id,
                'Score' => new MongoInt32($data['Score'])
            ]);
            $scoreUpdated = true;
            $currentScore = $data['Score'];

            // Get Rank
            $this->_getRank($l_id, $data);
            // TODO: Update
        }

        if(!$r['ok']) {
            return $r;
        }

        // Current Score
        $current =  $this->collection->find( [
            'UserId' => $u_id,
            'LeaderboardId' => $l_id
        ]);
        $current->sort( ['Score' => -1] );
        $current->limit(1);
        $current->next();
        $current = $current->current();

        return [
            'ok' => 1,
            'result' => [
                'UserId' => (int)$current['UserId'],
                'LeaderboardId' => (int)$current['LeaderboardId'],
                'Score' => $current['Score'],
                'Rank' => $current['Rank']
            ]
        ];
    }

    public function Leaderboard($data)
    {
        // Verify Integer fields
        $positiveIntegerInputFields = ['LeaderboardId', 'UserId', 'Offset', 'Limit'];
        foreach ($positiveIntegerInputFields as $integerInputField) {
            if(!Validators::validateInteger($integerInputField, $data, true)) {
                Application::app()->respond()->sendError('Bad ' . $integerInputField);
            }
        }

        $l_id = new MongoInt32($data['LeaderboardId']);
        $u_id = new MongoInt32($data['UserId']);

        $entries = $this->collection->find([
            'UserId' => $u_id,
            'LeaderboardId' => $l_id
        ])->skip($data['Offset'])->limit($data['Limit']);

        return $entries;
    }

    public function getUserScore($userId, $leaderboardId)
    {
        $currentScore =  $this->collection->find( [
            'UserId' => $userId,
            'LeaderboardId' => $leaderboardId
        ]);
        $currentScore->sort( ['Score' => -1] );
        $currentScore->limit(1);
        $currentScore->next();
        $currentScore = $currentScore->current();

        if(!$currentScore) {
            return false;
        }

        return $currentScore['Score'];
    }

    static function scoreSort($a, $b)
    {
        if ($a['Score'] == $b['Score']) {
            return 0;
        }
        return ($a['Score'] < $b['Score']) ? -1 : 1;
    }

    private function _getRank($l_id, $data)
    {
        $currentRank = 1;
        $users = $this->collection->distinct('UserId', ['LeaderboardId' => $l_id]);
        $scores = [];
        foreach ($users as $user) {
            $scores[] = ['UserId' => $user, 'Score' => $this->getUserScore($user, $l_id)];
        }
        usort($scores, ['self', 'scoreSort']);
        foreach ($scores as $k=>$score) {
            if($score['UserId'] == $data['UserId']) {
                $currentRank = $k+1;
                break;
            }
        }
        return $currentRank;
    }
}