<?php
class AuthSession
{
    public static function getAuthBy($tableName)
    {
        if (!is_array($_SESSION['auth'][$tableName])) {
            $model = loadClass('AuthModel', 'auth');
            $data = $model->selectAuth($tableName);

            if (empty($data)) {
                exit("Unable to search table $tableName");
            }

            $_SESSION['auth'][$tableName] = array(
                'auth_list' => self::hasAuth($data['a_list']),
                'auth_view' => self::hasAuth($data['a_view']),
                'auth_write' => self::hasAuth($data['a_write']),
                'auth_download' => self::hasAuth($data['a_download']),
                'auth_modify' => self::hasAuth($data['a_modify']),
                'auth_remove' => self::hasAuth($data['a_remove']),
                'auth_reply' => self::hasAble($data['f_reply'], $data['a_reply']),
                'auth_comment' => self::hasAble($data['f_comment'], $data['a_comment']),
                'auth_comment_reply' => self::hasAble($data['f_comment_reply'], $data['a_comment_reply']),
                'auth_notice' => self::hasAble($data['f_notice'], $data['a_notice']),
                'auth_secret' => self::hasAble($data['f_secret'], $data['a_secret']),
                'auth_attach' => (self::hasAuth($data['a_attach']) && ($data['f_attach_count'] > 0)),
                'auth_attach_count' => $data['f_attach_count'],
                'auth_attach_type' => $data['f_attach_type']
                );
        }

        return $_SESSION['auth'][$tableName];
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