<?php
class CommentControl extends CoreControl
{
    private $_row;

    public function __construct()
    {
        parent::__construct('comment');

        $this->_row = 20;
        $this->_model->setRow($this->_row);
    }

    public function index($master, $masterIdx, $page = 1)
    {
        $data = $this->_model->selectAll($master, $masterIdx, $page);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);
        $data['get_page'] = $page;
        $data['master'] = $master;
        $data['master_idx'] = $masterIdx;

        return $this->_view->index($data);
    }

    public function getComment($code)
    {
        return $this->_model->select($code);
    }

    public function view($code)
    {
        if (empty($code)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        }

        $data = $this->getComment($code);
        $data['c_content'] = htmlspecialchars_decode($data['c_content']);
        $data['c_content'] = nl2br($data['c_content']);

        $this->_view->view($data);
    }

    private function getBlank()
    {
        $tv = $this->_model->showColumns();

        $blank = array();
        foreach ($tv as $val) {
            $blank[$val['Field']] = '';
        }

        return $blank;
    }

    public function write()
    {
        if (empty($_POST)) {
            // var_dump($this->getBlank());
            $data = array_merge(array('hdnAction'=>'add', 'hdnParent'=>'', 'hdnIdx'=>''), 
                $this->getBlank());
            $data['c_parent'] = '';

            $this->_view->write($data);
        } else {
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);

            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['name'] = strip_tags($_POST['name']);
            $_POST['email'] = strip_tags($_POST['email']);

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $idx = $this->_model->insert();


            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function rewrite($code = '')
    {
        if (empty($_POST)) {
            $parent = $this->getComment($code);
            $data = array_merge(array('hdnAction'=>'rep', 'hdnParent'=>$code, 'hdnIdx'=>''), 
                $this->getBlank());

            $data['b_title'] = 'Re:'.$parent['b_title'];
            $data['b_parent'] = htmlspecialchars_decode($parent['b_content']);

            $this->_view->write($data);
        } else {
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];
            $step = $_POST['step'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);
            unset($_POST['step']);


            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['name'] = strip_tags($_POST['name']);
            $_POST['email'] = strip_tags($_POST['email']);

            // 답글앞에 원본글 내용 추가
            $parent = $this->getComment($_POST['parent']);
            $parentContent = '<ul class="reply"><li>'.htmlspecialchars_decode($parent['b_content']).'</li></ul>';
            $_POST['content'] = $parentContent.$_POST['content'];

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $idx = $this->_model->insert();

            $data = $this->getComment($idx);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function modify($code = '')
    {
        if (empty($_POST)) {
            // 접근 권한 확인후 적용
            if (!isAdmin()) {
                if ($this->_model->isParent($code)) {
                    popupMsg('답변이 있는 글은 수정할 수 없습니다.');
                    exit;
                }
            }

            $data = array_merge(array('hdnAction'=>'mod', 'hdnParent'=>'', 'hdnIdx'=>$code), 
                $this->getComment($code));
            $data['b_parent'] = '';
            $data['b_content'] = htmlspecialchars_decode($data['b_content']);

            $this->_view->modify($data);
        } else {
            // 접근 권한 확인후 적용
            if (!isAdmin()) {
                if ($this->_model->isParent($_POST['idx'])) {
                    popupMsg('답변이 있는 글은 수정할 수 없습니다.');
                    exit;
                }
            }

            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];
            $step = $_POST['step'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['parent']);
            unset($_POST['url']);
            unset($_POST['step']);

            $_POST['title'] = strip_tags($_POST['title']);
            $_POST['name'] = strip_tags($_POST['name']);
            $_POST['email'] = strip_tags($_POST['email']);

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $this->_model->update();

            $data = $this->getComment($_POST['idx']);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function remove()
    {
        if (empty($_POST)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        } else {
            // 접근 권한 확인후 적용
            if (!isAdmin()) {
                if ($this->_model->isParent($_POST['idx'])) {
                    popupMsg('답변이 있는 글은 삭제할 수 없습니다.');
                    exit;
                }
            }
            
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            $this->_model->delete();

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }
}