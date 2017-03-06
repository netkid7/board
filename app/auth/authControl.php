<?php
class AuthControl extends CoreControl
{
    private $_row;

    public function __construct()
    {
        parent::__construct('auth');

        $this->_row = 20;
        $this->_model->setRow($this->_row);
    }

    /*
     * 특정 게시판의 부가기능 사용 여부를 확인한다.
     * 설정은 brn_auth 테이블에 있다.
     * 부가기능 on + 부가기능 level
     * @param string 사용하고자 하는 기능(oh/off)
     * @param int 기능을 사용할 수 있는 최소권한
     * 
     */
    private function hasAble($func, $level)
    {
        return (($func == 'y') && $this->hasAuth($level));
    }

    /*
     * 특정 권한/레벨을 사용할 수 있는지 학인
     * @param int 권한레벨
     * return bool 권한이 있으면(레벨 <= 세션) TRUE;
     */
    private function hasAuth($level)
    {
        return (isset($_SESSION['_level']) && ($_SESSION['_level']) >= $level);
    }

    public function getAuthBy($tableName)
    {
        $data = $this->_model->selectAuth($tableName);

        $result = array(
            'auth_list' => $this->hasAuth($data['a_list']),
            'auth_view' => $this->hasAuth($data['a_view']),
            'auth_write' => $this->hasAuth($data['a_write']),
            'auth_download' => $this->hasAuth($data['a_download']),
            'auth_modify' => $this->hasAuth($data['a_modify']),
            'auth_remove' => $this->hasAuth($data['a_remove']),
            'auth_reply' => $this->hasAble($data['f_reply'], $data['a_reply']),
            'auth_comment' => $this->hasAble($data['f_comment'], $data['a_comment']),
            'auth_comment_reply' => $this->hasAble($data['f_comment_reply'], $data['a_comment_reply']),
            'auth_notice' => $this->hasAble($data['f_notice'], $data['a_notice']),
            'auth_secret' => $this->hasAble($data['f_secret'], $data['a_secret']),
            'auth_attach' => ($this->hasAuth($data['a_attach']) && ($data['f_attach_count'] > 0)),
            'auth_attach_count' => $data['f_attach_count'],
            'auth_attach_type' => $data['f_attach_type']
            );

        return $result;
    }

    public function isAdmin()
    {
        return $this->hasAuth(9);
    }

    public function index($parent, $parentIdx, $page = 1)
    {
        $data = $this->_model->selectAll($parent, $parentIdx, $page);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);

        $this->_view->index($data);
    }

    public function getAuth($code)
    {
        return $this->_model->select($code);
    }

    public function view($code)
    {
        if (empty($code)) {
            popupMsg('요청이 잘못되었습니다.');
            exit;
        }

        $data = $this->getAuth($code);
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
                $this->getAuth($code));
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

            $data = $this->getAuth($_POST['idx']);

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