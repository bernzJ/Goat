<?php
namespace Goat\backend;

use Goat\goat;

class Model
{

    private $connection;

    public function __construct()
    {
        $this->connection = new \PDO('mysql:host=' . goat::$app->config['db']['host'] . ';dbname=' . goat::$app->config['db']['dbname'], goat::$app->config['db']['username'], goat::$app->config['db']['password']); //mysql_pconnect(goat::$config['db_host'], goat::$app->config['db_username'], goat::$app->config['db_password']) or die('MySQL Error: '. mysql_error());
        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
    }
    public function query($qry, $params = [])
    {
        try {

            if (!empty($params['format'])) {
                foreach ($params['format'] as $key => &$value) {
                    $qry = str_replace($key, $value, $qry);
                }
            }

            $statement = $this->connection->prepare($qry);
            if (!empty($params['binded'])) {
                foreach ($params['binded'] as $key => &$value) {
                    $statement->bindParam($key, $value);
                }
            }

            $statement->execute();

            if (isset($params['fetch'])) {
                if ($params['fetch'] == 'all') {
                    return $statement->fetchAll();
                } else {
                    return $statement->fetch();
                }
            } else {
                return 'success';
            }

        } catch (\PDOException $Exception) {
            die(var_dump($Exception->getMessage(), $Exception->getCode()));
        }

    }
}
