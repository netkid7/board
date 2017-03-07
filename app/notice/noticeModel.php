<?php
class NoticeModel extends CoreModel
{
    private $_table;
    private $_row_page;

    public function __construct()
    {
        parent::__construct();

        $this->_table = 'coo_notice';
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

    public function selectAll($page)
    {
        $arr_search = array('1'=>'n_title', '2'=>'n_content', '3'=>'n_name');
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
                ORDER BY n_notice DESC, n_ref DESC, n_order ASC
                LIMIT $offset_page, $this->_row_page";
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT *
                FROM $this->_table
                ORDER BY n_notice DESC, n_ref DESC, n_order ASC
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
                SELECT COUNT(*) AS n_count
                FROM $this->_table
                WHERE ".implode(' AND ', $search);
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT COUNT(*) AS n_count 
                FROM $this->_table";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['n_count'];
    }

    public function select($code)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE n_idx = :code";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }

    public function insert()
    {
        // $ref = $this->getRef();
        $_POST['secret'] = (empty($_POST['secret']))? 0: $_POST['secret'];
        $_POST['notice'] = (empty($_POST['notice']))? 0: $_POST['notice'];
        
        $bCount = 0;
        $sql = "
            INSERT INTO $this->_table (
                n_title, n_name, n_id, n_password, n_content,
                n_count, n_email, n_secret, n_notice, 
                n_reg_IP, n_reg_date)
            VALUES (". implode(', ', array_fill(0, 10, '?')) .", NOW())";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $_POST['title']);
        $stmt->bindParam(2, $_POST['name']);
        $stmt->bindParam(3, $_SESSION['_id']);  // 작성자 ID
        $stmt->bindParam(4, $_SESSION['_id']);  // 작성자 비밀번호
        $stmt->bindParam(5, $_POST['content']);
        $stmt->bindParam(6, $bCount);
        $stmt->bindParam(7, $_POST['email']);
        $stmt->bindParam(8, $_POST['secret']);
        $stmt->bindParam(9, $_POST['notice']);
        $stmt->bindParam(10, $_SERVER['REMOTE_ADDR']);

        $stmt->execute();
        // $stmt->closeCursor();

        $n_idx = $this->connection->lastInsertId();

        $this->updateRef($n_idx, $_POST['parent']);

        return $n_idx;
    }

    private function updateRef($targetIdx, $parentIdx = '')
    {
        if ($parentIdx) {
            $parent = $this->select($parentIdx);

            // 들어가야할 순서위치
            $sql = "
                SELECT IFNULL(MAX(n_order), 0) AS r_order
                FROM $this->_table
                WHERE n_ref = :n_ref
                    AND n_parent = :n_parent";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':n_ref', $parent['n_ref']);
            $stmt->bindParam(':n_parent', $parent['n_idx']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $order = $row['r_order'];
            if ($order == '0') {
                $order = $parent['n_order'];
            }
            $order += 1;

            // 들어가야할 위치보다 큰 순서 재정렬
            $sql = "
                UPDATE $this->_table
                SET n_order = n_order + 1
                WHERE n_ref = :n_ref
                    AND n_order >= :n_order";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':n_ref' => $parent['n_ref'], ':n_order' => $order));


            $depth = $parent['n_depth'] + 1;
            $sql = "
                UPDATE $this->_table
                SET n_ref = :n_ref,
                    n_depth = :n_depth,
                    n_parent = :n_parent,
                    n_order = :n_order
                WHERE n_idx = :n_idx";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':n_ref', $parent['n_ref']);
            $stmt->bindParam(':n_depth', $depth);   // bindParam()는 반드시 변수 사용!! 상수나 연산(결과가 상수)을 넣으면 안된다.
            $stmt->bindParam(':n_parent', $parent['n_idx']);
            $stmt->bindParam(':n_order', $order);
            $stmt->bindParam(':n_idx', $targetIdx);
            $stmt->execute();
        } else {
            // 새글
            $sql = "
                UPDATE $this->_table
                SET n_ref = :n_ref,
                    n_depth = 0,
                    n_parent = 0,
                    n_order = 0
                WHERE n_idx = :n_idx";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':n_ref' => $targetIdx, ':n_idx' => $targetIdx));
        }
    }

    public function isParent($targetIdx)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE n_parent = :idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':idx', $targetIdx, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function nameHolder($i)
    {
        return ':n_'.$i;
    }

    public function updateCount($targetIdx)
    {
        $sql = "
            UPDATE $this->_table
            SET n_count = n_count + 1
            WHERE n_idx = :n_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':n_idx' => $targetIdx));
    }

    public function update()
    {
        $_POST['secret'] = (empty($_POST['secret']))? 0: $_POST['secret'];
        $_POST['notice'] = (empty($_POST['notice']))? 0: $_POST['notice'];
        
        // $keys = array_map(array($this, 'nameHolder'), array_keys($_POST));

        $sql = "
            UPDATE $this->_table
            SET n_title = :n_title,
                n_name = :n_name,
                n_content = :n_content,
                n_count = 0,
                n_email = :n_email,
                n_secret = :n_secret,
                n_notice = :n_notice,
                n_reg_date = NOW(),
                n_reg_IP = '{$_SERVER['REMOTE_ADDR']}'
            WHERE n_idx = :n_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':n_title', $_POST['title'], PDO::PARAM_STR);
        $stmt->bindParam(':n_name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':n_content', $_POST['content'], PDO::PARAM_STR);
        $stmt->bindParam(':n_email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':n_secret', $_POST['secret'], PDO::PARAM_STR);
        $stmt->bindParam(':n_notice', $_POST['notice'], PDO::PARAM_STR);
        $stmt->bindParam(':n_idx', $_POST['idx'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete()
    {
        // 관리자에 의한 대상글의 답변까지 모두 삭제 필요
        // $sql = "
        //     DELETE FROM $this->_table WHERE n_idx = :n_idx OR n_parent = :n_idx";
        $sql = "
            DELETE FROM $this->_table WHERE n_idx = :n_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':n_idx' => $_POST['idx']));
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