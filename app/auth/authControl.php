<?php
class AuthControl extends CoreControl
{
    private $_row;
    private $_table;

    private $_authMap;

    public function __construct()
    {
        parent::__construct('auth');

        $this->_row = 20;
        $this->_model->setRow($this->_row);

        $this->_table = $this->_model->getTable();
        
        $this->_authMap = $this->getAuthBy($this->_table);
        if ($this->_authMap == FALSE) {
            exit('No authorized table: Auth.');
        }
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

        if ($data) {
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
        } else {
            $result = false;
        }

        return $result;
    }

    public function isAdmin()
    {
        return $this->hasAuth(9);
    }

    public function index($parent, $parentIdx, $page = 1)
    {
        checkAuth($this->_authMap['auth_list']);

        $data = $this->_model->selectAll($parent, $parentIdx, $page);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);
        
        $data['auth'] = $this->_authMap;

        $this->_view->index($data);
    }

    public function getAuth($code)
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

        $data = $this->getAuth($code);
        // $data 값 특수문자 되돌리기
        // single line - 특수문자 그래도 표현
        // multiline
        // $data['...'] = htmlspecialchars_decode($data['...'])
        // $data['...'] = nl2br($data['...'])

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
            $data = array_merge(array('hdnAction'=>'add', 'hdnParent'=>'', 'hdnIdx'=>''), 
                $this->getBlank());

            $data['auth'] = $this->_authMap;

            $this->_view->write($data);
        } else {
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);

            // $_POST 값 특수문자 변경 
            // input text : single line
            // $_POST['...'] = htmlspecialchars(strip_tags($_POST['...']))
            // textarea
            // $_POST['...'] = htmlspecialchars($_POST['...'])

            $idx = $this->_model->insert();

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function modify($code = '')
    {
        checkAuth($this->_authMap['auth_modify']);

        if (empty($_POST)) {
            $data = array_merge(array('hdnAction'=>'mod', 'hdnParent'=>'', 'hdnIdx'=>$code), 
                $this->getAuth($code));
            
            $data['auth'] = $this->_authMap;

            $this->_view->modify($data);
        } else {
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];
            $step = $_POST['step'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['parent']);
            unset($_POST['url']);
            unset($_POST['step']);

            // $_POST 값 특수문자 변경 
            // input text : single line
            // $_POST['...'] = htmlspecialchars(strip_tags($_POST['...']))
            // textarea
            // $_POST['...'] = htmlspecialchars($_POST['...'])

            $this->_model->update();

            $data = $this->getAuth($_POST['idx']);

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
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            $this->_model->delete();

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }
}