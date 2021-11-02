<?php

namespace app\lib;

class Db
{
    /**
     * @var \PDO db instance
     */
    protected $db;

    /**
     * @var string name of command - SELECT, UPDATE, INSERT, DELETE
     */
    private string $command;

    /**
     * @var array<string> list of fields
     */
    private $fields = [];

    /**
     * @var array<string> conditions for WHERE
     */
    private $conditions = [];

    /**
     * @var string table name from
     */
    protected $table;

    public function __construct()
    {
        // get config from file
        $config = require APP . '/config/db.php';

        extract($config); // extract vars from config file

        // get string to PDO connect
        $dns = $config['driver'] .
            ':host=' . $config['host'] .
            ((!empty($config['port'])) ? (';port=' . $config['port']) : '') .
            ';dbname=' . $config['dbname'];

        // try to connect
        try {
            $this->db = new \PDO($dns, $config['user'], $config['password']);
        } catch (\PDOException $e) {
            die('Cannot connect to db: ' . $e->getMessage());
        }
    }

    /**
     * set fields to be selected
     *
     * @param string ...$select fields to be selected
     * @return $this
     */
    public function select(string ...$select): self
    {
        $this->command = 'SELECT';
        $this->fields = $select;
        return $this;
    }

    /**
     * set fields ans values to be updated
     *
     * @param array $fields array with array[field, value] to be updated
     * @return $this
     */
    public function update(array $fields): self
    {
        $this->command = 'UPDATE';

        $this->fields = $fields;

        return $this;
    }

    /**
     * set fields to be inserted
     *
     * @param array $fields array with array[field, value] to be inserted
     * @return $this
     */
    public function insert(array $fields): self
    {
        $this->command = 'INSERT';

        $this->fields = $fields;

        return $this;
    }

    /**
     * set DELETE command for sql query
     *
     * @return $this
     */
    public function delete(): self
    {
        $this->command = 'DELETE';

        $this->fields = null;

        return $this;
    }

    /**
     * set list of where conditions
     *
     * @param string ...$where where conditions
     * @return $this
     */
    public function where(string ...$where): self
    {
        foreach ($where as $arg) {
            $this->conditions[] = $arg;
        }
        return $this;
    }

    /**
     * set table for execute query
     *
     * @param string $table table name
     * @return $this
     */
    public function setTable(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @return string SQL query
     */
    public function getSql(): string
    {
        // generate WHERE string
        $where = $this->conditions === [] ? '' : ' WHERE ' . implode(' AND ', $this->conditions);

        switch ($this->command){
            case 'SELECT':
                return 'SELECT ' . implode(', ', $this->fields)
                    . ' FROM ' . $this->table
                    . $where;

            case 'UPDATE':
                $updateFields = '';

                foreach ($this->fields as $field) {
                    $updateFields .= $field[0] . ' = '. "'$field[1]'" . ",\n";
                }

                $updateFields = trim($updateFields, ",\n");

                return 'UPDATE ' . $this->table
                    . ' SET ' . $updateFields
                    . $where;

            case 'INSERT':
                return 'ins';

            case 'DELETE':
                return 'del';
        }
    }

    /**
     * execute sql query
     *
     * @param array|null $params list of parameters to bind
     */
    public function execute(?array $params = null){
        $sql = $this->getSql();

        echo $sql;

        try {
            return $this->query($sql, $params);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * execute sql string
     *
     * @param $sql string sql string to execute
     */
    public function query(string $sql, $params = null)
    {
        if (!$this->db) {
//            if(IS_DEV) throw new \Exception('Db connection is failed!');
            return false;
        }

        $stmt = $this->db->prepare($sql);

//        if (!empty($params)) {
//            foreach ($params as $key => $val) {
//                $stmt->bindValue(':' . $key, $val);
//            }
//        }

        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
