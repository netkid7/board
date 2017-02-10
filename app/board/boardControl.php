<?php
class BoardControl extends CoreControl
{
    private $_row;
    private $_table;    // attach와 연결에 쓸 자신이 테이블 이름
    private $_auth;

    private $_attach;
    private $_comment;

    public function __construct()
    {
        parent::__construct('board');

        $this->_row = 20;
        $this->_model->setRow($this->_row);

        $this->_auth = $this->_model->getAuth();
        $this->_table = $this->_model->getTable();

        $this->_attach = loadClass('AttachControl', 'attach');
        $this->_attach->setUploadExtension();
        $this->_comment = loadClass('CommentControl', 'comment');
    }

    private function urlQuery()
    {
        $get_page = (empty($_GET["page"]))? 1: noInject($_GET["page"]);
        $get_s = (empty($_GET["s"]))? '': noInject($_GET["s"]);
        $get_k = (empty($_GET["k"]))? '': noInject($_GET["k"]);

        return compact('get_page', 'get_s', 'get_k');
    }

    public function index()
    {
        checkAuth($this->_auth['a_list']);

        $query = $this->urlQuery();

        $data = $this->_model->selectAll($query['get_page']);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);
        $data['auth'] = $this->_auth;

        $data = array_merge($data, $query);

        $this->_view->index($data);
    }

    public function getBoard($code)
    {
        return $this->_model->select($code);
    }

    public function view($code)
    {
        checkAuth($this->_auth['a_view']);

        if (empty($code)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        }

        $this->_model->updateCount($code);

        $data = $this->getBoard($code);
        $data['b_content'] = htmlspecialchars_decode($data['b_content']);
        $data['b_content'] = nl2br($data['b_content']);

        $data['auth'] = $this->_auth;
        $data['b_attach'] = $this->_attach->view($this->_table, $code);

        $data['b_comment'] = $this->_comment->index($this->_table, $code);

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
        checkAuth($this->_auth['a_write']);

        if (empty($_POST)) {
            // var_dump($this->getBlank());
            $data = array_merge(array('hdnAction'=>'add', 'hdnParent'=>'', 'hdnIdx'=>''), 
                $this->getBlank());
            $data['b_parent'] = '';

            $data['b_attach'] = $this->_attach->write($this->_table, '', $this->_auth['f_attach_count']);

            $data['auth'] = $this->_auth;

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

            $this->_attach->write($this->_table, $idx);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function rewrite($code = '')
    {
        checkAuth($this->_auth['a_reply']);

        if (empty($_POST)) {
            $parent = $this->getBoard($code);
            $data = array_merge(array('hdnAction'=>'rep', 'hdnParent'=>$code, 'hdnIdx'=>''), 
                $this->getBlank());

            $data['b_title'] = 'Re:'.$parent['b_title'];
            $data['b_parent'] = htmlspecialchars_decode($parent['b_content']);

            $data['b_attach'] = $this->_attach->write($this->_table, '', $this->_auth['f_attach_count']);

            $data['auth'] = $this->_auth;

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
            $parent = $this->getBoard($_POST['parent']);
            $parentContent = '<ul class="reply"><li>'.htmlspecialchars_decode($parent['b_content']).'</li></ul>';
            $_POST['content'] = $parentContent.$_POST['content'];

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $idx = $this->_model->insert();

            $data = $this->getBoard($idx);
            $this->_attach->write($this->_table, $idx);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function modify($code = '')
    {
        checkAuth($this->_auth['a_modify']);

        if (empty($_POST)) {
            // 접근 권한 확인후 적용
            if (!isAdmin()) {
                if ($this->_model->isParent($code)) {
                    popupMsg('답변이 있는 글은 수정할 수 없습니다.');
                    exit;
                }
            }

            $data = array_merge(array('hdnAction'=>'mod', 'hdnParent'=>'', 'hdnIdx'=>$code), 
                $this->getBoard($code));
            $data['b_parent'] = '';
            $data['b_content'] = htmlspecialchars_decode($data['b_content']);

            $data['b_attach'] = $this->_attach->modify($this->_table, $code, $this->_auth['f_attach_count']);

            $data['auth'] = $this->_auth;

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

            $data = $this->getBoard($_POST['idx']);
            $this->_attach->modify($this->_table, $_POST['idx'], $this->_auth['f_attach_count']);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function remove()
    {
        checkAuth($this->_auth['a_remove']);

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

            $this->_attach->removeAll($this->_table, $_POST['idx']);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }
}