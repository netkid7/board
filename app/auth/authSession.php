<?php
class AuthSession
{
    public static function getAuthBy($tableName)
    {
        if (!is_array($_SESSION['auth'][$tableName])) {
            $auth = loadClass('AuthControl', 'auth');
            $data = $auth->getAuthBy($tableName);

            if (empty($data)) {
                exit("Unable to search table $tableName");
            }

            $_SESSION['auth'][$tableName] = $data;
        }

        // return $_SESSION['auth'][$tableName];
    }

    private static function hasAble($func, $level)
    {
        return (($func == 'y') && self::hasAuth($level));
    }

    private static function hasAuth($level)
    {
        return (isset($_SESSION['_level']) && ($_SESSION['_level']) >= $level);
    }

    public static function isAdmin()
    {
        return self::hasAuth(9);
    }
}