<?php
class CoreModel // extends PDO
{
    private static $databases;
    public $connection;

    public function __construct()
    {
        $connDetails = "localhost|brighten|brighten|qmfkdlxms";
        if (!is_object(self::$databases[$connDetails])) {
            list($host, $dbname, $user, $pass) = explode('|', $connDetails);

            $dsn = "mysql:host=$host;dbname=$dbname";
            self::$databases[$connDetails] = new PDO($dsn, $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            self::$databases[$connDetails]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        $this->connection = self::$databases[$connDetails];
    }

    public function selectAuth($tableName)
    {
        $sql = "
            SELECT *
            FROM cc_auth
            WHERE a_table = :table";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':table', $tableName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
