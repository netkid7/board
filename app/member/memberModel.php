<?php
class MemberModel extends CoreModel
{
    private $_table;
    private $_row_page;

    public function __construct()
    {
        parent::__construct();

        $this->_table = 'brn_member';
        $this->_row_page = 30;
    }

    public function getTable()
    {
        return $this->_table;
    }

    public function setRow($row)
    {
        $this->_row_page = $row;
    }

    /*
     * 암호화(해쉬)한 결과를 가져온다.
     * 비밀번호 암호화에 사용한다.
     * @param string 암호화 하려는 문자열
     * return string 암호화된 문자열
     */
    private function getEncrypt($str)
    {
        if ($str) {
            return hash('sha512', $str);
        } else {
            return false;
        }
    }

    public function selectAll($page)
    {
        $arr_search = array('1'=>'m_name', '2'=>'m_id');
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
                WHERE m_level <= {$_SESSION['_level']} AND ".implode(' AND ', $search)."
                ORDER BY m_idx ASC
                LIMIT $offset_page, $this->_row_page";
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT *
                FROM $this->_table
                WHERE m_level <= {$_SESSION['_level']}
                ORDER BY m_idx ASC
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
                SELECT COUNT(*) AS m_count
                FROM $this->_table
                WHERE m_level <= {$_SESSION['_level']} AND ".implode(' AND ', $search);
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT COUNT(*) AS m_count
                FROM $this->_table
                WHERE m_level <= {$_SESSION['_level']}";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['m_count'];
    }

    public function select($code)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE m_idx = :code";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectByID($id)
    {
        $sql = "
            SELECT m_id AS id, m_gcm AS gcm, 
                m_name AS name, m_type AS type, 
                m_email AS mail, m_image AS image,
                m_idx, m_state
            FROM $this->_table
            WHERE m_id = :id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existId($targetId)
    {
        $sql = "
            SELECT m_id AS id
            FROM $this->_table
            WHERE m_id = :m_id";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':m_id', $targetId, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($row) ? TRUE: FALSE;
    }

    public function active()
    {
        $encryptedPwd = $this->getEncrypt($_POST['password']);

        $sql = "
            SELECT *
            FROM $this->_table
            WHERE m_state = 'y' AND m_id = :id AND m_password = :password";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $encryptedPwd, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function memberInsert()
    {
        if ($this->existId($_POST['id'])) {
            return FALSE;
        }

        $gcm = 'browser-regist';
        $level = 1;
        $type = 'S';
        $encryptedPwd = $this->getEncrypt($_POST['password']);
        $profile = $this->appImage($_POST["id"]);
        $state = 'y';


        $sql = "
            INSERT INTO $this->_table (
                m_id, m_gcm, m_level, m_type,
                m_name, m_email, m_password, m_image, 
                m_state, m_reg_date)
            VALUES (". implode(', ', array_fill(0, 9, '?')) .", NOW())";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $_POST['id'], PDO::PARAM_STR);
        $stmt->bindParam(2, $gcm, PDO::PARAM_STR);
        $stmt->bindParam(3, $level, PDO::PARAM_INT);
        $stmt->bindParam(4, $type, PDO::PARAM_STR);
        $stmt->bindParam(5, $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(7, $encryptedPwd, PDO::PARAM_STR);
        $stmt->bindParam(8, $profile, PDO::PARAM_STR);
        $stmt->bindParam(9, $state, PDO::PARAM_STR);

        $stmt->execute();
        // $stmt->closeCursor();

        $m_idx = $this->connection->lastInsertId();

        return $m_idx;
    }

    public function memberUpdate()
    {
        $before = $this->selectByID($_SESSION["_id"]);
        $profile = $this->appImage($_SESSION["_id"]);
        if ($profile) {
            deleteFile(urlencode($before['image']));
        } else {
            $profile = $before['image'];
        }

        $sql = "
            UPDATE $this->_table
            SET m_name = :m_name,
                m_email = :m_email,
                m_image = :m_image
            WHERE m_idx = :m_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':m_name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':m_email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':m_image', $profile, PDO::PARAM_STR);
        
        $stmt->bindParam(':m_idx', $_SESSION["_idx"], PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function memberDelete()
    {
        $before = $this->selectByID($_SESSION["_id"]);
        deleteFile(urlencode($before['image']));

        $sql = "
            UPDATE $this->_table
            SET m_type = NULL,
                m_name = NULL,
                m_email = NULL,
                m_password = NULL,
                m_image = NULL,
                m_state = 'n'
            WHERE m_idx = :m_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':m_idx' => $_SESSION["_idx"]));
    }

    public function insert()
    {
        if ($this->existId($_POST['id'])) {
            return FALSE;
        }

        $encryptedPwd = $this->getEncrypt($_POST['password']);
        $profile = $this->appImage($_POST["id"]);

        $sql = "
            INSERT INTO $this->_table (
                m_id, m_gcm, m_level, m_type, 
                m_name, m_email, m_password, m_image,
                m_state, m_memo, m_reg_date)
            VALUES (". implode(', ', array_fill(0, 10, '?')) .", NOW())";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $_POST['id'], PDO::PARAM_STR);
        $stmt->bindParam(2, $_POST['gcm'], PDO::PARAM_STR);
        $stmt->bindParam(3, $_POST['level'], PDO::PARAM_INT);
        $stmt->bindParam(4, $_POST['type'], PDO::PARAM_STR);
        $stmt->bindParam(5, $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(6, $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(7, $encryptedPwd, PDO::PARAM_STR);
        $stmt->bindParam(8, $profile, PDO::PARAM_STR);
        $stmt->bindParam(9, $_POST['state'], PDO::PARAM_STR);
        $stmt->bindParam(10, $_POST['memo'], PDO::PARAM_STR);

        $stmt->execute();
        // $stmt->closeCursor();

        $m_idx = $this->connection->lastInsertId();

        return $m_idx;
    }

    private function nameHolder($i)
    {
        return ':m_'.$i;
    }

    public function update()
    {
        $before = $this->select($_POST['idx']);
        $profile = $this->appImage($before['m_id']);
        if ($profile) {
            deleteFile(urlencode($before['m_image']));
        } else {
            $profile = $before['m_image'];
        }
        // $keys = array_map(array($this, 'nameHolder'), array_keys($_POST));

        $sql = "
            UPDATE $this->_table
            SET m_gcm = :m_gcm,
                m_level = :m_level,
                m_type = :m_type,
                m_name = :m_name,
                m_email = :m_email,
                m_image = :m_image,
                m_state = :m_state,
                m_memo = :m_memo
            WHERE m_idx = :m_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':m_gcm', $_POST['gcm'], PDO::PARAM_STR);
        $stmt->bindParam(':m_level', $_POST['level'], PDO::PARAM_INT);
        $stmt->bindParam(':m_type', $_POST['type'], PDO::PARAM_STR);
        $stmt->bindParam(':m_name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':m_email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':m_image', $profile, PDO::PARAM_STR);
        $stmt->bindParam(':m_state', $_POST['state'], PDO::PARAM_STR);
        $stmt->bindParam(':m_memo', $_POST['memo'], PDO::PARAM_STR);
        $stmt->bindParam(':m_idx', $_POST['idx'], PDO::PARAM_INT);
        // foreach ($keys as $key) {
        //     $stmt->bindParam($key, $_POST[substr($key, 3)]);
        // }

        return $stmt->execute();
    }

    public function updatePassword($idx)
    {
        $encryptedPwd = $this->getEncrypt($_POST['password']);

        $sql = "
            UPDATE $this->_table
            SET m_password = :m_password
            WHERE m_idx = :m_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':m_password', $encryptedPwd, PDO::PARAM_STR);
        $stmt->bindParam(':m_idx', $idx, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateLast($idx)
    {
        $sql = "
            UPDATE $this->_table
            SET m_last_in = NOW()
            WHERE m_idx = :m_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':m_idx' => $idx));
    }

    public function delete()
    {
        $before = $this->select($_POST['idx']);
        deleteFile(urlencode($before['m_image']));

        $sql = "
            DELETE FROM $this->_table WHERE m_idx = :m_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':m_idx' => $_POST['idx']));
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