<?php
class AttachModel extends CoreModel
{
    private $_table;
    private $_upload_func;
    private $_upload_ext;

    public function __construct()
    {
        parent::__construct();

        $this->_table = 'brn_attach';
        $this->_upload_func = 'uploadByDate';
        $this->_upload_ext = array("jpg", "png", "bmp", "gif");
    }

    public function setUploadFunc($funcName)
    {
        $this->_upload_func = $funcName;
    }

    public function setUploadExt($arrExt)
    {
        $this->_upload_ext = $arrExt;
    }


    public function selectAttach($parent, $parentIdx)
    {
        $sql = "
                SELECT * 
                FROM $this->_table
                WHERE a_parent = :a_parent AND a_parent_idx = :a_parent_idx
                ORDER BY a_idx ASC";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':a_parent', $parent, PDO::PARAM_STR);
        $stmt->bindParam(':a_parent_idx', $parentIdx, PDO::PARAM_INT);
        $stmt->execute();
        $result['rows'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result;
    }

    public function select($code)
    {
        $sql = "
            SELECT * FROM $this->_table
            WHERE a_idx = :code";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':code', $code, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($parent, $parentIdx)
    {
        $insertRows = array();
        // summmernote(js)에서 추가한 image FormData 는 첨부파일에 포함하지 않는다.
        unset($_FILES['files']);

        $uploadNames = uploadRearrange();
        foreach ($uploadNames as $val) {
            // $result = $this->_upload_func($val);
            $result = call_user_func($this->_upload_func, $val, $this->_upload_ext);
            if ($result['save_name'] == FALSE) {
                continue;
            } else {
                $insertRows[] = array(
                    $parent,
                    $parentIdx,
                    $result['upload_name'],
                    $result['save_name']
                    );
            }
        }

        if (count($insertRows) == 0) {
            return FALSE;
        } else {
            $sql = "
                INSERT INTO $this->_table (
                    a_parent, a_parent_idx, a_file_name, a_save_name)
                VALUES (". implode(', ', array_fill(0, count($insertRows[0]), '?')) .")";
            
            $stmt = $this->connection->prepare($sql);

            foreach ($insertRows as $row) {
                $stmt->execute($row);
            }

            return count($insertRows);
        }
    }

    private function nameHolder($i)
    {
        return ':a_'.$i;
    }

    // 첨부파일 정보 수정은 사용안함.
    public function update()
    {
        $keys = array_map(array($this, 'nameHolder'), array_keys($_POST));

        $sql = "
            UPDATE $this->_table
            SET a_parent = :parent,
                a_parent_idx = :parent_idx,
                a_file_name = :file_name,
                a_save_name = :save_name
            WHERE a_idx = :a_idx";
        $stmt = $this->connection->prepare($sql);
        foreach ($keys as $key) {
            $stmt->bindParam($key, $_POST[substr($key, 3)]);
        }

        return $stmt->execute();
    }

    public function deleteOld($parent, $parentIdx, $maxCount)
    {
        $data = $this->selectAttach($parent, $parentIdx);

        $deleteCount = count($data['rows']) - $maxCount;
        if ($deleteCount > 0) {
            $keys = array();
            for ($i = 0; $i < $deleteCount; $i++) {
                $keys[] = $data['rows'][$i]['a_idx'];
            }
            
            $this->deleteFileRow(implode(', ', $keys));
        }
    }

    public function deleteAll($parent, $parentIdx)
    {
        $data = $this->selectAttach($parent, $parentIdx);
        $keys = array();
        foreach ($data['rows'] as $val) {
            $keys = $val['a_idx'];
        }

        $this->deleteFileRow(implode(', ', $keys));
    }

    private function deleteFileRow($keys)
    {
        if ($keys) {
            $sql = "
                SELECT a_save_name 
                FROM $this->_table
                WHERE a_idx IN ($keys)";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $val) {
                deleteFile($val['a_save_name']);
            }

            $sql = "
                DELETE FROM $this->_table WHERE a_idx IN ($keys)";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute();
        } else {
            return false;
        }
    }

    public function delete()
    {
        // 복수 파일 삭제 요청이 있을 수 있다.
        // <input name="a_idx[]" />
        if (is_array($_POST['a_idx'])) {
            $temp = array();
            foreach ($_POST['a_idx'] as $val) {
                if ($val) {
                    $temp[] = $val;
                }
            }
            $keys = implode(', ', $temp);
        } else {
            $keys = $_POST['a_idx'];
        }

        $this->deleteFileRow($keys);

        // if ($keys) {
        //     $sql = "
        //         SELECT a_save_name 
        //         FROM $this->_table
        //         WHERE a_idx IN ($keys)";
        //     $stmt = $this->connection->prepare($sql);
        //     $stmt->execute();
        //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //     foreach ($result as $val) {
        //         deleteFile($val['a_save_name']);
        //     }

        //     $sql = "
        //         DELETE FROM $this->_table WHERE a_idx IN ($keys)";
        //     $stmt = $this->connection->prepare($sql);
        //     return $stmt->execute();
        // } else {
        //     return false;
        // }
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