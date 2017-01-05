<?php
class BoardModel extends CoreModel
{
    private $_table;
    private $_row_page;

    public function __construct()
    {
        parent::__construct();

        $this->_table = 'brn_board';
        $this->_row_page = 30;
    }

    public function getAuth()
    {
        return $this->selectAuth($this->_table);
    }

    public function setRow($row)
    {
        $this->_row_page = $row;
    }

    public function selectAll($page)
    {
        $arr_search = array('1'=>'b_title', '2'=>'b_content', '3'=>'b_name');
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
                SELECT b.*, IFNULL(t_step, 0) AS t_step
                FROM $this->_table AS b LEFT JOIN cc_task AS t
                ON b_ref = t_parent_ref AND t_parent = '{$this->_table}'
                WHERE ".implode(' AND ', $search)."
                ORDER BY b_notice DESC, t_step ASC, b_ref DESC, b_order ASC
                LIMIT $offset_page, $this->_row_page";
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT b.*, IFNULL(t_step, 0) AS t_step
                FROM $this->_table AS b LEFT JOIN cc_task AS t
                ON b_ref = t_parent_ref AND t_parent = '{$this->_table}'
                ORDER BY b_notice DESC, t_step ASC, b_ref DESC, b_order ASC
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
                SELECT COUNT(*) AS b_count
                FROM $this->_table
                WHERE ".implode(' AND ', $search);
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT COUNT(*) AS b_count 
                FROM $this->_table";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['b_count'];
    }

    public function select($code)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE b_idx = :code";
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
                b_title, b_name, b_id, b_password, b_content,
                b_count, b_email, b_secret, b_notice, 
                b_reg_IP, b_reg_date)
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

        $b_idx = $this->connection->lastInsertId();

        $this->updateRef($b_idx, $_POST['parent']);

        return $b_idx;
    }

    private function updateRef($targetIdx, $parentIdx = '')
    {
        if ($parentIdx) {
            $parent = $this->select($parentIdx);

            // 들어가야할 순서위치
            $sql = "
                SELECT IFNULL(MAX(b_order), 0) AS r_order
                FROM $this->_table
                WHERE b_ref = :b_ref
                    AND b_parent = :b_parent";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':b_ref', $parent['b_ref']);
            $stmt->bindParam(':b_parent', $parent['b_idx']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $order = $row['r_order'];
            if ($order == '0') {
                $order = $parent['b_order'];
            }
            $order += 1;

            // 들어가야할 위치보다 큰 순서 재정렬
            $sql = "
                UPDATE $this->_table
                SET b_order = b_order + 1
                WHERE b_ref = :b_ref
                    AND b_order >= :b_order";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':b_ref' => $parent['b_ref'], ':b_order' => $order));


            $depth = $parent['b_depth'] + 1;
            $sql = "
                UPDATE $this->_table
                SET b_ref = :b_ref,
                    b_depth = :b_depth,
                    b_parent = :b_parent,
                    b_order = :b_order
                WHERE b_idx = :b_idx";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':b_ref', $parent['b_ref']);
            $stmt->bindParam(':b_depth', $depth);   // bindParam()는 반드시 변수 사용!! 상수나 연산(결과가 상수)을 넣으면 안된다.
            $stmt->bindParam(':b_parent', $parent['b_idx']);
            $stmt->bindParam(':b_order', $order);
            $stmt->bindParam(':b_idx', $targetIdx);
            $stmt->execute();
        } else {
            // 새글
            $sql = "
                UPDATE $this->_table
                SET b_ref = :b_ref,
                    b_depth = 0,
                    b_parent = 0,
                    b_order = 0
                WHERE b_idx = :b_idx";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':b_ref' => $targetIdx, ':b_idx' => $targetIdx));
        }
    }

    public function isParent($targetIdx)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE b_parent = :idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':idx', $targetIdx, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function nameHolder($i)
    {
        return ':b_'.$i;
    }

    public function updateCount($targetIdx)
    {
        $sql = "
            UPDATE $this->_table
            SET b_count = b_count + 1
            WHERE b_idx = :b_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute(array(':b_idx' => $targetIdx));
    }

    public function update()
    {
        $_POST['secret'] = (empty($_POST['secret']))? 0: $_POST['secret'];
        $_POST['notice'] = (empty($_POST['notice']))? 0: $_POST['notice'];
        
        // $keys = array_map(array($this, 'nameHolder'), array_keys($_POST));

        $sql = "
            UPDATE $this->_table
            SET b_title = :b_title,
                b_name = :b_name,
                b_content = :b_content,
                b_count = 0,
                b_email = :b_email,
                b_secret = :b_secret,
                b_notice = :b_notice,
                b_reg_date = NOW(),
                b_reg_IP = '{$_SERVER['REMOTE_ADDR']}'
            WHERE b_idx = :b_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':b_title', $_POST['title'], PDO::PARAM_STR);
        $stmt->bindParam(':b_name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':b_content', $_POST['content'], PDO::PARAM_STR);
        $stmt->bindParam(':b_email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':b_secret', $_POST['secret'], PDO::PARAM_STR);
        $stmt->bindParam(':b_notice', $_POST['notice'], PDO::PARAM_STR);
        $stmt->bindParam(':b_idx', $_POST['idx'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete()
    {
        // 관리자에 의한 대상글의 답변까지 모두 삭제 필요
        // $sql = "
        //     DELETE FROM $this->_table WHERE b_idx = :b_idx OR b_parent = :b_idx";
        $sql = "
            DELETE FROM $this->_table WHERE b_idx = :b_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':b_idx' => $_POST['idx']));
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