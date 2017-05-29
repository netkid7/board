<?php
class BoardControl extends CoreControl
{
    private $_row;
    private $_table;    // attach와 연결에 쓸 자신이 테이블 이름

    private $_auth;
    private $_authMap;

    private $_attach;
    private $_comment;

    public function __construct()
    {
        parent::__construct('board');

        $this->_row = 20;
        $this->_model->setRow($this->_row);

        $this->_table = $this->_model->getTable();

        $this->_auth = loadClass('AuthControl', 'auth');
        $this->_authMap = $this->_auth->getAuthBy($this->_table);

        $this->_attach = loadClass('AttachControl', 'attach');
        $this->_attach->setUploadExtension();
        $this->_comment = loadClass('CommentControl', 'comment');
    }

    public function index()
    {
        if (!$this->_authMap['auth_list']) {
            noAuthMsg();
        }

        $query = $this->urlQuery();

        $data = $this->_model->selectAll($query['get_page']);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);
        $data['auth'] = $this->_authMap;

        $data = array_merge($data, $query);

        $this->_view->index($data);
    }

    public function getBoard($code)
    {
        return $this->_model->select($code);
    }

    public function view($code)
    {
        if (!$this->_authMap['auth_view']) {
            noAuthMsg();
        }

        if (empty($code)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        }

        $this->_model->updateCount($code);

        $data = $this->getBoard($code);
        $data['b_content'] = htmlspecialchars_decode($data['b_content']);
        $data['b_content'] = nl2br($data['b_content']);

        $data['auth'] = $this->_authMap;

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
        if (!$this->_authMap['auth_write']) {
            noAuthMsg();
        }


        if (empty($_POST)) {
            $data = array_merge(array('hdnAction'=>'add', 'hdnParent'=>'', 'hdnIdx'=>''), 
                $this->getBlank());
            $data['b_parent'] = '';

            $data['auth'] = $this->_authMap;

            if ($this->_authMap['auth_attach']) {
                $data['b_attach'] = $this->_attach->write($this->_table, '', $this->_authMap['auth_attach_count']);
            } else {
                $data['b_attach'] = '';
            }

            $this->_view->write($data);
        } else {
            // 필수 항목 체크
            
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);

            $_POST['title'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
            $_POST['name'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
            $_POST['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

            $_POST['content'] = htmlspecialchars($_POST['content'], ENT_QUOTES);

            $idx = $this->_model->insert();

            if ($this->_authMap['auth_attach']) {
                $this->_attach->write($this->_table, $idx);
            }

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function reply($code = '')
    {
        if (!$this->_authMap['auth_reply']) {
            noAuthMsg();
        }


        if (empty($_POST)) {
            $parent = $this->getBoard($code);
            $data = array_merge(array('hdnAction'=>'rep', 'hdnParent'=>$code, 'hdnIdx'=>''), 
                $this->getBlank());

            $data['b_title'] = 'Re:'.$parent['b_title'];
            $data['b_parent'] = htmlspecialchars_decode(nl2br($parent['b_content']), ENT_QUOTES);

            $data['auth'] = $this->_authMap;

            if ($this->_authMap['auth_attach']) {
                $data['b_attach'] = $this->_attach->write($this->_table, '', $this->_authMap['auth_attach_count']);
            } else {
                $data['b_attach'] = '';
            }

            $this->_view->write($data);
        } else {
            // 필수 항목 체크

            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];
            $step = $_POST['step'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);
            unset($_POST['step']);


            $_POST['title'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
            $_POST['name'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
            $_POST['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

            // 답글앞에 원본글 내용 추가
            $parent = $this->getBoard($_POST['parent']);
            $parentContent = '<ul class="reply"><li>'.htmlspecialchars_decode($parent['b_content']).'</li></ul>';
            $_POST['content'] = $parentContent.$_POST['content'];

            $_POST['content'] = htmlspecialchars($_POST['content'], ENT_QUOTES);

            $idx = $this->_model->insert();

            if ($this->_authMap['auth_attach']) {
                $this->_attach->write($this->_table, $idx);
            }

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function modify($code = '')
    {
        if (!$this->_authMap['auth_modify']) {
            noAuthMsg();
        }

        if (empty($_POST)) {
            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
                if ($this->_model->isParent($code)) {
                    popupMsg('답변이 있는 글은 수정할 수 없습니다.');
                    exit;
                }
            }

            $data = array_merge(array('hdnAction'=>'mod', 'hdnParent'=>'', 'hdnIdx'=>$code), 
                $this->getBoard($code));
            $data['b_parent'] = '';
            $data['b_content'] = htmlspecialchars_decode($data['b_content']);

            if ($this->_authMap['auth_attach']) {
                $data['b_attach'] = $this->_attach->modify($this->_table, $code, $this->_authMap['auth_attach_count']);
            }

            $data['auth'] = $this->_authMap;

            $this->_view->modify($data);
        } else {
            // 필수 항목 체크

            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
                if ($this->_model->isParent($_POST['idx'])) {
                    popupMsg('답변이 있는 글은 수정할 수 없습니다.');
                    exit;
                }
                
                if (!$this->checkPassword()) {
                    popupMsg('비밀번호가 일치하지 않습니다.');
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

            $_POST['title'] = htmlspecialchars($_POST['title'], ENT_QUOTES);
            $_POST['name'] = htmlspecialchars($_POST['name'], ENT_QUOTES);
            $_POST['email'] = htmlspecialchars($_POST['email'], ENT_QUOTES);

            $_POST['content'] = htmlspecialchars($_POST['content'], ENT_QUOTES);

            $this->_model->update();

            if ($this->_authMap['auth_attach']) {
                $this->_attach->modify($this->_table, $_POST['idx'], $this->_authMap['auth_attach_count']);
            }

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    private function checkPassword()
    {
        $data = $this->getBoard($_POST['idx']);
        $password = (empty($_POST['password']))? $_SESSION["_id"]: $_POST['password'];

        return ($password == $data['b_password']);
    }

    public function remove()
    {
        if (!$this->_authMap['auth_remove']) {
            noAuthMsg();
        }

        if (empty($_POST)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        } else {
            // 필수 항목 체크

            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
                if ($this->_model->isParent($_POST['idx'])) {
                    popupMsg('답변이 있는 글은 삭제할 수 없습니다.');
                    exit;
                }

                if (!$this->checkPassword()) {
                    popupMsg('비밀번호가 일치하지 않습니다.');
                    exit;
                }
            }
            
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            $this->_model->delete();

            if ($this->_authMap['auth_attach']) {
                $this->_attach->removeAll($this->_table, $_POST['idx']);
            }

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }
}