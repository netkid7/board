<?php
class AttachControl extends CoreControl
{
    private $_row;

    public function __construct()
    {
        parent::__construct('attach');

        $this->_row = 1;
    }

    public function setUploadFunction($name = 'uploadImageByDate')
    {
        $this->_model->setUploadFunc($name);
    }

    public function setUploadExtension($arr = array("txt", "csv", "hwp", "doc", "docx", "ppt", "pptx", "xls", "xlsx", "pdf", "zip", "jpg", "png", "bmp", "gif"))
    {
        $this->_model->setUploadExt($arr);
    }

    public function setRow($row)
    {
        $this->_row = $row;
    }

    public function getDownload($idx)
    {
        return $this->_model->select($idx);
    }

    public function getAttach($parent, $parentIdx)
    {
        return $this->_model->selectAttach($parent, $parentIdx);
    }

    public function view($parent, $parentIdx)
    {
        if (empty($parent) || empty($parentIdx)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        }

        $data = $this->getAttach($parent, $parentIdx);
        return $this->_view->view($data);
    }

    private function getBlank()
    {
        $attach = $this->_model->showColumns();

        $blank = array();
        foreach ($attach as $val) {
            $blank[$val['Field']] = '';
        }

        return $blank;
    }

    public function write($parent, $parentIdx, $maxCount = 0)
    {
        if (empty($_POST)) {
            if ($maxCount <= 0) {
                $data = array(
                    'rows' => array()
                );
            } else {
                $data = array(
                    'rows' => array_fill(0, $maxCount, $this->getBlank())
                );
            }

            return $this->_view->write($data);
        } else {
            $insertCount = $this->_model->insert($parent, $parentIdx);
        }
    }

    public function modify($parent = '', $parentIdx = '', $maxCount = 0)
    {
        if (empty($_POST)) {
            $data = $this->getAttach($parent, $parentIdx);
            // 첨부가능한 빈항목 추가
            $blank = $maxCount - count($data['rows']);
            for ($i = 0; $i < $blank; $i++) {
                array_push($data['rows'], $this->getBlank());
            }

            return $this->_view->modify($data);
        } else {
            $this->_model->delete();
            $idx = $this->_model->insert($parent, $parentIdx);

            // 기존 첨부파일을 삭제하지 않고 추가만 하여 첨부갯수를 넘기면
            // 넘긴 수량만큼 오래된 파일을 삭제한다.
            $this->_model->deleteOld($parent, $parentIdx, $maxCount);
            // $this->_model->update();
        }
    }

    // 사용안함
    public function remove($parent = '', $parentIdx = '')
    {
        if (empty($_POST)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        } else {
            $this->_model->delete($parent, $parentIdx);
        }
    }

    public function removeAll($parent = '', $parentIdx = '')
    {
        $this->_model->deleteAll($parent, $parentIdx);
    }
}
