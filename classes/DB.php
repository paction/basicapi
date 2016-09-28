<?php

/**
 * Created by PhpStorm.
 * User: paction
 * Date: 26/09/2016
 * Time: 8:04 PM
 */
class DB
{
    public $connection;
    public $db;
    public $collections;

    public function __construct()
    {
        try {
            
            $this->connection = new \MongoDB\Driver\Manager();
            //$this->db = new MongoDB($this->connection, 'test');
            //$dbs = $this->connection->adminCommand(['listDatabases' => 1]);
            $this->db = $this->connection->selectDatabase('test');
var_dump($this->db); exit;
            /*$dbs = $dbs['databases'];
            $exists = false;
            foreach ($dbs as $db) {
                if($db['name'] == 'test') {
                    $exists = true;
                    break;
                }
            }*/

            if(!$exists) {
                $this->db = new MongoDB($this->connection, 'test');
            }

            $this->db = $this->connection->selectDatabase('test');

            $this->collections = $this->db->getCollectionNames();

            if(!in_array('transactions', $this->collections)) {
                $this->db->createCollection("transactions");
            }

            if(!in_array('leaderboard', $this->collections)) {
                $this->db->createCollection("leaderboard");
            }

        } catch (MongoConnectionException $e) {
            Application::app()->respond()->sendError('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            Application::app()->respond()->sendError('Error: ' . $e->getMessage());
        }
    }
}
