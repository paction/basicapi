<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 27/09/2016
 * Time: 12:06 AM
 */
class TransactionModel extends Model
{

    public function __construct()
    {
        $collection = 'transactions';
        parent::__construct($collection);
    }

    public function CreateTransaction($data)
    {
        // Verify Integer fields
        $positiveIntegerInputFields = ['TransactionId', 'UserId'];

        foreach ($positiveIntegerInputFields as $integerInputField) {
            if(!Validators::validateInteger($integerInputField, $data, true)) {
                Application::app()->respond()->sendError('Bad ' . $integerInputField);
            }
        }

        if(!Validators::validateInteger('CurrencyAmount', $data)) {
            Application::app()->respond()->sendError('Bad CurrencyAmount');
        }

        $verifier = sha1(Application::app()->config('secretKey') .
            $data['TransactionId'] .
            $data['UserId'] .
            $data['CurrencyAmount']);

        if(empty($data['Verifier']) || $verifier != $data['Verifier']) {
            //Application::app()->respond()->sendError('Bad Verifier');
        }

        // Check transaction
        $existing = $this->collection->findOne(['TransactionId' => new MongoInt32($data['TransactionId'])]);
        if($existing) {
            Application::app()->respond()->sendError('Transaction exists');
        }

        // Save transaction
        $transaction = [
            'TransactionId' => new MongoInt32($data['TransactionId']),
            'UserId' =>  new MongoInt32($data['UserId']),
            'CurrencyAmount' => new MongoInt32($data['CurrencyAmount'])
        ];

        $r = $this->collection->insert($transaction);

        if(!$r['ok']) {
            return $r;
        }

        return [
            'ok' => 1,
            'result' => ['Success' => true]
        ];
    }
    
    public function TransactionStats($data)
    {
        if(!Validators::validateInteger('UserId', $data, true)) {
            Application::app()->respond()->sendError('Bad UserId');
        }

        $r = $this->collection->aggregate([
            ['$match' => ['UserId' => new MongoInt32($data['UserId'])]],
            ['$group' => [
                '_id' => null,
                'sum' => ['$sum' => '$CurrencyAmount'],
                'count' => ['$sum' => 1]
            ]]
        ]);

        if(!$r['ok']) {
            return $r;
        }

        return [
            'ok' => 1,
            'result' => [
                'UserId' => (int)$data['UserId'],
                'TransactionCount' => $r['result'][0]['count'],
                'CurrencySum' => $r['result'][0]['sum']
            ]
        ];
    }
}