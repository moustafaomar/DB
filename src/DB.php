<?php

namespace MOOM;
use PDO;
class DB
{
    //Database host
    public $host = "localhost";
    //Database username
    public $username = "root";
    //Database password
    public $password = "";
    //Database name
    public $db = 'myblog';
    // Hold the class instance.
    private static $instance;

    /**
     * DB constructor.
     */
    protected function __construct()
    {
        try {
            self::$instance = new PDO("mysql:host=$this->host;dbname=$this->db", $this->username, $this->password);
            // set the PDO error mode to exception
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch
        (PDOException $e) {
            return 'fail';
        }
    }

    /**
     * Create new Instance of DB Class
     * @return PDO
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            new DB();
        }

        return self::$instance;
    }

    /**
     * @param $tbl_name
     * @param $params
     * @return \PDOStatement
     */

    private static function select($tbl_name, $params)
    {
        $string = "SELECT * FROM $tbl_name";
        if (isset($params['where'])) {
            $string .= " WHERE {$params['where']['column']} {$params['where']['operator']}" . ":" . "{$params['where']['column']}";
        }
        if (isset($params['order'])) {
            $string .= " order by {$params['order']['key']} {$params['order']['type']}";
        }
        if (isset($params['limit'])) {
            $string .= " LIMIT {$params['limit']}";
        }
        $instance = self::$instance;
        $stmt = $instance->prepare($string);
        if (isset($params['where']) && !empty($params['where'])) {
            $key = ":" . $params['where']['column'];
            $value = $params['where']['value'];
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_OBJ);
        return $stmt;
    }

    /**
     * @param $tbl_name
     * @param $params
     * @return mixed
     */
    public static function fetch($tbl_name, $params)
    {
        self::getInstance();
        try {
            $stmt = self::select($tbl_name, $params);
            $final = $stmt->fetch();
            return $final;
        } catch (PDOException $e) {
        }
    }

    /**
     * @param $tbl_name
     * @param $params
     * @return mixed
     */
    public static function fetchAll($tbl_name, $params)
    {
        self::getInstance();
        try {
            $stmt = self::select($tbl_name, $params);
            $final = $stmt->fetchAll();
            return $final;
        } catch (PDOException $e) {
            echo 'Failed to fetch data.';
        }

    }

    /**
     * @param $tbl_name
     * @param $params
     * @return mixed
     */
    public static function getAll($tbl_name, $params)
    {
        self::getInstance();
        $all = self::fetchAll($tbl_name, $params);
        return $all;
    }

    /**
     * @param $tbl_name
     * @param $id
     * @return mixed
     */
    public static function getByID($tbl_name, $id)
    {
        self::getInstance();
        $params = [
            'where' => [
                'column' => 'id',
                'operator' => '=',
                'value' => $id
            ]
        ];
        $all = self::fetch($tbl_name, $params);
        return $all;
    }

    /**
     * @param $tbl_name
     * @param $params
     * @return mixed
     */
    public static function first($tbl_name, $params)
    {
        self::getInstance();
        $stats = self::fetchAll($tbl_name, $params);
        return $stats[0];
    }

    /**
     * @param array $array
     * @return string
     */
    private static function get_bind($array = array())
    {
        $keys = array_keys($array);
        $values = null;
        foreach (array_slice($keys, 0, count($array) - 1) as $key => $value) {
            $values .= ':' . $value . ', ';
        }
        $all = $values . ':' . end($keys);
        return $all;
    }

    /**
     * @param array $array
     * @return string
     */
    private static function get_bind_exe($array = array())
    {
        $keys = array_keys($array);
        $values = null;
        foreach (array_slice($keys, 0, count($array) - 1) as $key => $value) {
            $values [] = ':' . $value;
        }
        $values[] = ':' . end($keys);
        return $values;
    }

    /**
     * @param array $array
     * @return string
     */
    private static function get_values($array = array())
    {
        $keys = array_values($array);
        return $keys;
    }

    /**
     * @param array $array
     * @return string
     */
    private static function get_keys($array = array())
    {
        $keys = array_keys($array);
        $values = null;
        foreach (array_slice($keys, 0, count($array) - 1) as $key => $value) {
            $values .= $value . ', ';
        }
        $all = $values . end($keys);
        return $all;
    }


    /**
     * @param $tbl_name
     * @param array $params
     * @return boolean|null
     */
    public static function Create($tbl_name, $params = array())
    {
        self::getInstance();
        $keys = self::get_keys($params);
        $bind = self::get_bind($params);
        $string = "INSERT INTO $tbl_name (" . $keys . ") VALUES (" . $bind . ")";
        $values = self::get_values($params);
        $bind_2 = self::get_bind_exe($params);
        $bindp = array_combine($bind_2, $values);
        try {
            $stmt = self::$instance->prepare($string);
            self::bindParam($stmt, $bindp);
            $stmt->execute();
        } catch
        (PDOException $e) {
            echo 'Failed to insert Data.';
            echo $e->getMessage();
        }
    }

    /**
     * @param $tbl_name
     * @param $condition
     */
    public static function Delete($tbl_name, $condition)
    {
        self::getInstance();
        $sql = "DELETE FROM $tbl_name WHERE {$condition['column']} {$condition['operator']} {$condition['value']}";
        $key = ":" . $condition['column'];
        $value = $condition['value'];
        try {
            $sql = self::$instance->prepare($sql);
            $sql->bindParam($key, $value);
            $sql->execute();
        } catch
        (PDOException $e) {
            echo 'Failed to Delete Data.';
        }
    }

    /**
     * @param $array
     * @return string
     */
    public static function update_array($array)
    {
        $statement = NULL;
        foreach (array_slice($array, 0, count($array) - 1, true) as $key => $value) {
            $statement .= $key . '=' . ":" . $key . ",";
        }
        end($array);
        $all = $statement . key($array) . ' = :' . key($array);
        return $all;
    }

    /**
     * @param $tbl_name
     * @param $array
     * @param $where
     * @return boolean|null
     */
    public static function Update($tbl_name, $array, $where)
    {
        self::getInstance();
        $sql = "UPDATE $tbl_name SET " . self::update_array($array) . ' ' . "WHERE {$where['column']} {$where['operator']} " . ':' . "{$where['column']}";
        $sql = self::$instance->prepare($sql);
        $key = ":" . $where['column'];
        $value = $where['value'];
        $sql->bindParam($key, $value);
        $values = self::get_values($array);
        $bind_2 = self::get_bind_exe($array);
        $bindp = array_combine($bind_2, $values);
        self::bindParam($sql, $bindp);

        try {
            if ($sql->execute()) {
                return true;
            } else {
                return false;
            }
        } catch
        (PDOException $e) {
            echo 'Failed to Update Data.';
        }
    }

    /**
     * @param \PDOStatement $stmt
     * @param $bind
     */
    private static function bindParam($stmt, $bind)
    {
        foreach ($bind as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    }

    /**
     * @param $statment
     * @return bool|mixed
     */
    public static function Query($statment)
    {
        self::getInstance();
        $string = $statment;
        $stmt = self::$instance->prepare($string);
        $stmt->execute();
        if (strtolower(substr($statment, 0, 6)) == 'select') {
            $stmt->setFetchMode(PDO::FETCH_OBJ);
            $result = $stmt->fetch();
            return $result;
        } else {
            if ($stmt) {
                return true;
            } else {
                return false;
            }
        }
    }

    protected function __clone()
    {
    }

    protected function __wakeup()
    {
    }
}
