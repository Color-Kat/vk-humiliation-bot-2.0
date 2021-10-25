<?php

namespace app\lib;

class Db
{
    protected $db;

    public function __construct()
    {
        $config = require APP . '/config/db.php';

        extract($config);

        $dns = $config['driver'] .
            ':host=' . $config['host'] .
            ((!empty($config['port'])) ? (';port=' . $config['port']) : '') .
            ';dbname=' . $config['dbname'];

        try {
            $this->db = new \PDO($dns, $config['user'], $config['password']);
        } catch (\PDOException $e) {
            die('Cannot connect to db: ' . $e->getMessage());
        }
    }

    /**
     * execute sql string
     * 
     * @param $sql sql string to execute
     */
    public function query($sql, $params = null)
    {
        // TODO use builder pattern like in laravel

        if (!$this->db) throw new \Exception('Db connection is failed!');

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $key => $val) {
                $stmt->bindValue(':' . $key, $val);
            }
        }

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
