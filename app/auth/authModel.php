<?php
class AuthModel extends CoreModel
{
    private $_table;
    private $_row_page;

    public function __construct()
    {
        parent::__construct();

        $this->_table = 'brn_auth';
        $this->_row_page = 30;
    }

    public function setRow($row)
    {
        $this->_row_page = $row;
    }

    public function selectAuth($tableName)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE a_table = :table";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':table', $tableName, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectAll($parent, $parantIdx, $page = 1)
    {
        $arr_search = array('1'=>'a_table', '2'=>'a_name');
        $search = array();
        $param  = array();

        if (!empty($_GET['k']) && array_key_exists($_GET["s"], $arr_search)) {
            $column = $arr_search[$_GET["s"]];
            $search[] = "$column LIKE :{$column}";
            $param[":{$column}"] = "%".noInject($_GET['k'])."%";
        }

        $result = array();
        $result['total_count'] = $this->getRowCount($search, $param);

        $offset_page = ($page - 1) * $this->_row_page;
        $result['row_no'] = $offset_page;
        if (!empty($search)) {
            $sql = "
                SELECT *
                FROM $this->_table
                WHERE ".implode(' AND ', $search)."
                ORDER BY a_idx ASC
                LIMIT $offset_page, $this->_row_page";
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT *
                FROM $this->_table
                ORDER BY a_idx ASC
                LIMIT $offset_page, $this->_row_page";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        $result['rows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    private function getRowCount($search = array(), $param = array())
    {
        if (!empty($search)) {
            $sql = "
                SELECT COUNT(*) AS a_count
                FROM $this->_table
                WHERE ".implode(' AND ', $search);
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT COUNT(*) AS a_count 
                FROM $this->_table";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['a_count'];
    }

    public function select($code)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE a_idx = :code";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function insert()
    {
        $sql = "
            INSERT INTO $this->_table (
                a_table, a_list, a_view, a_write, a_download, a_modify, a_remove, 
                a_reply, a_comment, a_comment_reply, a_notice, a_secret, a_attach, 
                f_reply, f_comment, f_comment_reply, f_notice, f_secret, f_attach_count, f_attach_type)
            VALUES (". implode(', ', array_fill(0, 6, '?')) .", NOW())";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(, $_POST['table']);
        $stmt->bindParam(, $_POST['list']);
        $stmt->bindParam(, $_POST['view']);
        $stmt->bindParam(, $_POST['write']);
        $stmt->bindParam(, $_POST['download']);
        $stmt->bindParam(, $_POST['modify']);
        $stmt->bindParam(, $_POST['remove']);
        $stmt->bindParam(, $_POST['reply']);
        $stmt->bindParam(, $_POST['comment']);
        $stmt->bindParam(, $_POST['commentReply']);
        $stmt->bindParam(, $_POST['notice']);
        $stmt->bindParam(, $_POST['secret']);
        $stmt->bindParam(, $_POST['attach']);
        $stmt->bindParam(, $_POST['fReply']);
        $stmt->bindParam(, $_POST['fComment']);
        $stmt->bindParam(, $_POST['fCommentReply']);
        $stmt->bindParam(, $_POST['fNotice']);
        $stmt->bindParam(, $_POST['fSecret']);
        $stmt->bindParam(, $_POST['fAttachCount']);
        $stmt->bindParam(, $_POST['fAttachType']);

        $stmt->execute();

        $c_idx = $this->connection->lastInsertId();

        $this->updateRef($c_idx, $_POST['parent']);

        return $c_idx;
    }

    public function update()
    {
        $sql = "
            UPDATE $this->_table
            SET a_table = :a_table,
                a_list = :a_list,
                a_view = :a_view,
                a_write = :a_write,
                a_download = :a_download,
                a_modify = :a_modify,
                a_remove = :a_remove,
                a_reply = :a_reply,
                a_comment = :a_comment,
                a_comment_reply = :a_comment_reply,
                a_notice = :a_notice,
                a_secret = :a_secret,
                a_attach = :a_attach,
                f_reply = :f_reply,
                f_comment = :f_comment,
                f_comment_reply = :f_comment_reply,
                f_notice = :f_notice,
                f_secret = :f_secret,
                f_attach_count = :f_attach_count,
                f_attach_type = :f_attach_type
            WHERE a_idx = :a_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':a_table', $_POST['table'], PDO::PARAM_STR);
        $stmt->bindParam(':a_list', $_POST['list'], PDO::PARAM_INT);
        $stmt->bindParam(':a_view', $_POST['view'], PDO::PARAM_INT);
        $stmt->bindParam(':a_write', $_POST['write'], PDO::PARAM_INT);
        $stmt->bindParam(':a_download', $_POST['download'], PDO::PARAM_INT);
        $stmt->bindParam(':a_modify', $_POST['modify'], PDO::PARAM_INT);
        $stmt->bindParam(':a_remove', $_POST['remove'], PDO::PARAM_INT);
        $stmt->bindParam(':a_reply', $_POST['reply'], PDO::PARAM_INT);
        $stmt->bindParam(':a_comment', $_POST['comment'], PDO::PARAM_INT);
        $stmt->bindParam(':a_comment_reply', $_POST['commentReply'], PDO::PARAM_INT);
        $stmt->bindParam(':a_notice', $_POST['notice'], PDO::PARAM_INT);
        $stmt->bindParam(':a_secret', $_POST['secret'], PDO::PARAM_INT);
        $stmt->bindParam(':a_attach', $_POST['attach'], PDO::PARAM_INT);
        $stmt->bindParam(':f_reply', $_POST['fReply'], PDO::PARAM_STR);
        $stmt->bindParam(':f_comment', $_POST['fComment'], PDO::PARAM_STR);
        $stmt->bindParam(':f_comment_reply', $_POST['fCommentReply'], PDO::PARAM_STR);
        $stmt->bindParam(':f_notice', $_POST['fNotice'], PDO::PARAM_STR);
        $stmt->bindParam(':f_secret', $_POST['fSecret'], PDO::PARAM_STR);
        $stmt->bindParam(':f_attach_count', $_POST['fAttachCount'], PDO::PARAM_INT);
        $stmt->bindParam(':f_attach_type', $_POST['fAttachType'], PDO::PARAM_STR);
        $stmt->bindParam(':a_idx', $_POST['idx'], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete()
    {
        $sql = "
            DELETE FROM $this->_table WHERE a_idx = :a_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':a_idx' => $_POST['idx']));
    }

    public function showColumns()
    {
        $sql = "
            SHOW COLUMNS FROM $this->_table";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}