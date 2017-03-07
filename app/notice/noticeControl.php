<?php
class NoticeControl extends CoreControl
{
    private $_row;
    private $_table;    // attach와 연결에 쓸 자신이 테이블 이름
    private $_auth;
    private $_authMap;

    private $_attach;
    private $_comment;

    public function __construct()
    {
        parent::__construct('notice');

        $this->_row = 20;
        $this->_model->setRow($this->_row);

        $this->_table = $this->_model->getTable();

        $this->_auth = loadClass('AuthControl', 'auth');
        $this->_authMap = $this->_auth->getAuthBy($this->_table);

        $this->_attach = loadClass('AttachControl', 'attach');
        $this->_attach->setUploadExtension();
        // $this->_comment = loadClass('CommentControl', 'comment');
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
        checkAuth($this->_authMap['auth_list']);

        $query = $this->urlQuery();

        $data = $this->_model->selectAll($query['get_page']);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);
        $data['auth'] = $this->_authMap;

        $data = array_merge($data, $query);

        $this->_view->index($data);
    }

    public function getNotice($code)
    {
        return $this->_model->select($code);
    }

    public function view($code)
    {
        checkAuth($this->_authMap['auth_view']);

        if (empty($code)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        }

        $this->_model->updateCount($code);

        $data = $this->getNotice($code);
        $data['n_content'] = htmlspecialchars_decode($data['n_content']);
        $data['n_content'] = nl2br($data['n_content']);

        $data['auth'] = $this->_authMap;
        $data['auth']['auth_admin'] = $this->_auth->isAdmin();
        $data['n_attach'] = $this->_attach->view($this->_table, $code);

        // $data['n_comment'] = $this->_comment->index($this->_table, $code);

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
        checkAuth($this->_authMap['auth_write']);

        if (empty($_POST)) {
            // var_dump($this->getBlank());
            $data = array_merge(array('hdnAction'=>'add', 'hdnParent'=>'', 'hdnIdx'=>''), 
                $this->getBlank());
            $data['n_parent'] = '';

            $data['n_attach'] = $this->_attach->write($this->_table, '', $this->_authMap['auth_attach_count']);

            $data['auth'] = $this->_authMap;

            $this->_view->write($data);
        } else {
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);

            $_POST['title'] = htmlspecialchars(strip_tags($_POST['title']));
            $_POST['name'] = htmlspecialchars(strip_tags($_POST['name']));
            $_POST['email'] = htmlspecialchars(strip_tags($_POST['email']));

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $idx = $this->_model->insert();

            $this->_attach->write($this->_table, $idx);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function rewrite($code = '')
    {
        checkAuth($this->_authMap['auth_reply']);

        if (empty($_POST)) {
            $parent = $this->getNotice($code);
            $data = array_merge(array('hdnAction'=>'rep', 'hdnParent'=>$code, 'hdnIdx'=>''), 
                $this->getBlank());

            $data['n_title'] = 'Re:'.$parent['n_title'];
            $data['n_parent'] = htmlspecialchars_decode($parent['n_content']);

            $data['n_attach'] = $this->_attach->write($this->_table, '', $this->_authMap['auth_attach_count']);

            $data['auth'] = $this->_authMap;

            $this->_view->write($data);
        } else {
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];
            $step = $_POST['step'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);
            unset($_POST['step']);


            $_POST['title'] = htmlspecialchars(strip_tags($_POST['title']));
            $_POST['name'] = htmlspecialchars(strip_tags($_POST['name']));
            $_POST['email'] = htmlspecialchars(strip_tags($_POST['email']));

            // 답글앞에 원본글 내용 추가
            $parent = $this->getNotice($_POST['parent']);
            $parentContent = '<ul class="reply"><li>'.htmlspecialchars_decode($parent['n_content']).'</li></ul>';
            $_POST['content'] = $parentContent.$_POST['content'];

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $idx = $this->_model->insert();

            $data = $this->getNotice($idx);
            $this->_attach->write($this->_table, $idx);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function modify($code = '')
    {
        checkAuth($this->_authMap['auth_modify']);

        if (empty($_POST)) {
            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
                if ($this->_model->isParent($code)) {
                    popupMsg('답변이 있는 글은 수정할 수 없습니다.');
                    exit;
                }
            }

            $data = array_merge(array('hdnAction'=>'mod', 'hdnParent'=>'', 'hdnIdx'=>$code), 
                $this->getNotice($code));
            $data['n_parent'] = '';
            $data['n_content'] = htmlspecialchars_decode($data['n_content']);

            $data['n_attach'] = $this->_attach->modify($this->_table, $code, $this->_authMap['auth_attach_count']);

            $data['auth'] = $this->_authMap;

            $this->_view->modify($data);
        } else {
            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
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

            $_POST['title'] = htmlspecialchars(strip_tags($_POST['title']));
            $_POST['name'] = htmlspecialchars(strip_tags($_POST['name']));
            $_POST['email'] = htmlspecialchars(strip_tags($_POST['email']));

            $_POST['content'] = htmlspecialchars($_POST['content']);

            $this->_model->update();

            $data = $this->getNotice($_POST['idx']);
            $this->_attach->modify($this->_table, $_POST['idx'], $this->_authMap['auth_attach_count']);

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function remove()
    {
        checkAuth($this->_authMap['auth_remove']);

        if (empty($_POST)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        } else {
            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
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