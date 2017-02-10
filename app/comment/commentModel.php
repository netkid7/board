<?php
class CommentModel extends CoreModel
{
    private $_table;
    private $_row_page;

    public function __construct()
    {
        parent::__construct();

        $this->_table = 'brn_comment';
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

    public function selectAll($parent, $parantIdx, $page = 1)
    {
        $search = array("c_parent = :c_parent", "c_parent_idx = :c_parent_idx");
        $param  = array(':c_parent' => $parent, ':c_parent_idx' => $parantIdx);

        $result = array();
        $result['total_count'] = $this->getRowCount($search, $param);

        $offset_page = ($page - 1) * $this->_row_page;
        $result['row_no'] = $offset_page;
        if (!empty($search)) {
            $sql = "
                SELECT *
                FROM $this->_table
                WHERE ".implode(' AND ', $search)."
                ORDER BY c_order ASC
                LIMIT $offset_page, $this->_row_page";
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT *
                FROM $this->_table
                ORDER BY c_order ASC
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
                SELECT COUNT(*) AS c_count
                FROM $this->_table
                WHERE ".implode(' AND ', $search);
            $stmt = $this->connection->prepare($sql);
            foreach ($param as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
        } else {
            $sql = "
                SELECT COUNT(*) AS c_count 
                FROM $this->_table";
            $stmt = $this->connection->prepare($sql);
        }
        $stmt->execute();
        $row = $stmt->fetch();

        return $row['c_count'];
    }

    public function select($code)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE c_idx = :code";
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
                c_content, c_name, c_id, c_password, 
                c_email, c_reg_IP, c_reg_date)
            VALUES (". implode(', ', array_fill(0, 6, '?')) .", NOW())";
        
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(1, $_POST['content']);
        $stmt->bindParam(2, $_POST['name']);
        $stmt->bindParam(3, $_SESSION['_id']);  // 작성자 ID
        $stmt->bindParam(4, $_SESSION['_id']);  // 작성자 비밀번호
        $stmt->bindParam(5, $_POST['email']);
        $stmt->bindParam(6, $_SERVER['REMOTE_ADDR']);

        $stmt->execute();
        // $stmt->closeCursor();

        $c_idx = $this->connection->lastInsertId();

        $this->updateRef($c_idx, $_POST['parent']);

        return $c_idx;
    }

    private function updateRef($targetIdx, $parentIdx = '')
    {
        if ($parentIdx) {
            $parent = $this->select($parentIdx);

            // 들어가야할 순서위치
            $sql = "
                SELECT IFNULL(MAX(c_order), 0) AS r_order
                FROM $this->_table
                WHERE c_ref = :c_ref
                    AND c_parent = :c_parent";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':c_ref', $parent['c_ref']);
            $stmt->bindParam(':c_parent', $parent['c_idx']);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $order = $row['r_order'];
            if ($order == '0') {
                $order = $parent['c_order'];
            }
            $order += 1;

            // 들어가야할 위치보다 큰 순서 재정렬
            $sql = "
                UPDATE $this->_table
                SET c_order = c_order + 1
                WHERE c_ref = :c_ref
                    AND c_order >= :c_order";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':c_ref' => $parent['c_ref'], ':c_order' => $order));


            $depth = $parent['c_depth'] + 1;
            $sql = "
                UPDATE $this->_table
                SET c_ref = :c_ref,
                    c_depth = :c_depth,
                    c_parent = :c_parent,
                    c_order = :c_order
                WHERE c_idx = :c_idx";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindParam(':c_ref', $parent['c_ref']);
            $stmt->bindParam(':c_depth', $depth);   // bindParam()는 반드시 변수 사용!! 상수나 연산(결과가 상수)을 넣으면 안된다.
            $stmt->bindParam(':c_parent', $parent['c_idx']);
            $stmt->bindParam(':c_order', $order);
            $stmt->bindParam(':c_idx', $targetIdx);
            $stmt->execute();
        } else {
            // 새글
            $sql = "
                UPDATE $this->_table
                SET c_ref = :c_ref,
                    c_depth = 0,
                    c_parent = 0,
                    c_order = 0
                WHERE c_idx = :c_idx";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute(array(':c_ref' => $targetIdx, ':c_idx' => $targetIdx));
        }
    }

    public function isParent($targetIdx)
    {
        $sql = "
            SELECT *
            FROM $this->_table
            WHERE c_parent = :idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':idx', $targetIdx, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function nameHolder($i)
    {
        return ':c_'.$i;
    }

    public function update()
    {
        // $keys = array_map(array($this, 'nameHolder'), array_keys($_POST));

        $sql = "
            UPDATE $this->_table
            SET c_content = :c_content,
                c_name = :c_name,
                c_email = :c_email,
                c_reg_date = NOW(),
                c_reg_IP = '{$_SERVER['REMOTE_ADDR']}'
            WHERE c_idx = :c_idx";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':c_content', $_POST['content'], PDO::PARAM_STR);
        $stmt->bindParam(':c_name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':c_email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':c_idx', $_POST['idx'], PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete()
    {
        // 관리자에 의한 대상글의 답변까지 모두 삭제 필요
        // $sql = "
        //     DELETE FROM $this->_table WHERE c_idx = :c_idx OR c_parent = :c_idx";
        $sql = "
            DELETE FROM $this->_table WHERE c_idx = :c_idx";
        $stmt = $this->connection->prepare($sql);
        return $stmt->execute(array(':c_idx' => $_POST['idx']));
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