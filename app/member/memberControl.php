<?php
class MemberControl extends CoreControl
{
    private $_row;
    private $_table;

    private $_level;
    private $_idPattern;

    private $_auth;
    private $_authMap;

    private $_attach;

    public function __construct()
    {
        parent::__construct('member');

        $this->_row = 20;
        $this->_model->setRow($this->_row);

        $this->_table = $this->_model->getTable();

        $this->_level = array(
            99 => '최고관리자',
            9 => '관리자',
            2 => '직원',
            1 => '일반회원'
        );
        $this->_idPattern = '/^[a-zA-Z][0-9a-zA-Z_]{5,19}$/';

        $this->_auth = loadClass('AuthControl', 'auth');
        $this->_authMap = $this->_auth->getAuthBy($this->_table);
        if ($this->_authMap == FALSE) {
            exit('No authorized table: Member.');
        }

        $this->_attach = loadClass('AttachControl', 'attach');
        $this->_attach->setUploadExtension();
    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function hasID()
    {
        if ($this->_model->existId($_GET['id'])) {
            $result = array('val'=> '0', 'msg'=>'이미 있는 아이디입니다.', 'id'=>$_GET['id']);
        } else {
            $result = array('val'=> '1', 'msg'=>'쓸 수 있는 아이디입니다.', 'id'=>$_GET['id']);
        }

        responseOK($result);
    }

    public function logIn()
    {
        if (isset($_SESSION['_idx'])) {
            $this->memberView();
            return;
        }

        if (empty($_POST)) {
            $data = array('hdnAction'=>'login');
            $data['id_pattern'] = $this->_idPattern;

            $this->_view->login($data);
        } else {
            
            $result = $this->_model->active();

            if ($result) {
                $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

                $this->_model->updateLast($result['m_idx']);

                $_SESSION["_idx"]    = $result['m_idx'];
                $_SESSION["_id"]     = $result['m_id'];
                $_SESSION["_name"]   = $result['m_name'];
                $_SESSION["_level"]  = $result['m_level'];

                // if ($_POST["saveid"] == "y" ) {
                //     setcookie("saveid",  $_POST["m_id"], 0);
                // }

                $url = '/member/index.php'; //.$url;
                header("Location: $url");
            } else {
                popupMsg('아이디 또는 비밀번호가 일치하지 않습니다.');
                exit;
            }
        }
    }

    public function logOut()
    {
        session_unset();

        $url = '/member/';
        header("Location: $url");
    }

    public function memberView()
    {
        if (!empty($_SESSION['_idx'])) {
            $data = $this->getMember($_SESSION["_idx"] );
            $data['m_level'] = $this->_level[$data['m_level']];
            $data['m_last_in'] = ($data['m_last_in'])? date('Y-m-d H:m', strtotime($data['m_last_in'])): '';
            $data['m_reg_date'] = ($data['m_reg_date'])? date('Y-m-d H:m', strtotime($data['m_reg_date'])): '';

            $this->_view->memberView($data);
        } else {
            $this->logIn();
        }
    }

    public function memberWrite()
    {
        if (empty($_POST)) {
            // $this->getBlank();
            $data = array_merge(array('hdnAction'=>'reg', 'hdnDupl'=>'0'), 
                $this->getBlank());
            // $data['level'] = array_slice($this->_level, -2, null, TRUE);
            $data['id_pattern'] = $this->_idPattern;

            $this->_view->memberWrite($data);
        } else {

            if ($_POST['id_duple'] !== '1') {
                popupMsg('아이디 중복 여부를 확인해주세요.');
                exit;
            }

            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['url']);
            unset($_POST['id_duple']);

            $result = $this->_model->memberInsert();
            
            if ($result === FALSE) {
                popupMsg('이미 존재하는 아이디 입니다.');
                exit;
            }

            $result = $this->_model->active();

            $_SESSION["_idx"]    = $result['m_idx'];
            $_SESSION["_id"]     = $result['m_id'];
            $_SESSION["_name"]   = $result['m_name'];
            $_SESSION["_level"]  = $result['m_level'];

            $this->memberView();
        }
    }

    public function memberModify()
    {
        if (empty($_POST)) {
            $data = array_merge(array('hdnAction'=>'correct', 'hdnDupl'=>'1'), 
                $this->getMember($_SESSION['_idx']));
            // $data['level'] = array_slice($this->_level, -2, null, TRUE);
            $data['id_pattern'] = $this->_idPattern;

            $this->_view->memberModify($data);
        } else {
            // 접근 권한 확인후 적용
            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['url']);
            unset($_POST['id_duple']);

            $result = $this->_model->memberUpdate();

            if ($result) {
                $_SESSION["_name"]   = $_POST['name'];
            }

            // 패스워드가 입력되어 있으면 수정한다.
            if ($_POST['password']) {
                $this->_model->updatePassword($_SESSION['_idx']);
            }

            $this->memberView();
        }
    }

    public function memberLeave()
    {
        if (empty($_SESSION['_idx'])) {
            popupMsg('로그인 되어 있어야 합니다.');
            exit;
        } else {
            unset($_POST);

            $this->_model->memberDelete();

            $this->logOut();
        }
    }

    public function dispatcher()
    {
        if ($this->_auth->isAdmin()) {
            $this->index();
        } else {
            $this->memberView();
        }
    }

    public function index()
    {
        if (!$this->_authMap['auth_list']) {
            noAuthMsg();
        }

        $query = $this->urlQuery();

        $data = $this->_model->selectAll($query['get_page']);
        $data['total_page'] = (int)ceil($data['total_count'] / $this->_row);
        $data['level'] = $this->_level;
        $data['auth'] = $this->_authMap;

        $data = array_merge($data, $query);

        $this->_view->index($data);
    }

    public function getMember($code)
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

        $data = $this->getMember($code);
        $data['m_level'] = $this->_level[$data['m_level']];
        $data['m_state'] = ($data['m_state'] == 'y')? '사용': '미사용';
        $data['m_last_in'] = ($data['m_last_in'])? date('Y-m-d H:m', strtotime($data['m_last_in'])): '';
        $data['m_reg_date'] = ($data['m_reg_date'])? date('Y-m-d H:m', strtotime($data['m_reg_date'])): '';
        
        $data['auth'] = $this->_authMap;

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

    private function getBelowLevel()
    {
        $belowLevel = array();
        foreach ($this->_level as $key => $val) {
            if ($key <= $_SESSION['_level']) {
                $belowLevel[$key] = $val;
            }
        }

        return $belowLevel;
    }

    public function write()
    {
        if (!$this->_authMap['auth_write']) {
            noAuthMsg();
        }

        if (empty($_POST)) {
            $data = array_merge(array('hdnAction'=>'add', 'hdnIdx'=>'', 'hdnDupl'=>'0'), 
                $this->getBlank());
            $data['level'] = $this->getBelowLevel();
            $data['id_pattern'] = $this->_idPattern;

            $data['auth'] = $this->_authMap;

            $this->_view->write($data);
        } else {
            if ($_POST['id_duple'] !== '1') {
                popupMsg('아이디 중복 여부를 확인해주세요.');
                exit;
            }

            // ID pattern 체크
            // 이름, 이메일, 연락처, 메모 특수문자 확인

            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['idx']);
            unset($_POST['url']);
            unset($_POST['id_duple']);

            $result = $this->_model->insert();
            if ($result === FALSE) {
                popupMsg('이미 존재하는 아이디 입니다.');
                exit;
            }

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }

    public function modify($code)
    {
        if (!$this->_authMap['auth_modify']) {
            noAuthMsg();
        }

        if (empty($_POST)) {
            $data = array_merge(array('hdnAction'=>'mod', 'hdnIdx'=>$code, 'hdnDupl'=>'1'), 
                $this->getMember($code));
            $data['level'] = $this->getBelowLevel();
            $data['id_pattern'] = $this->_idPattern;

            $this->_view->modify($data);
        } else {
            // 필수 항목 체크
            
            $url = (empty($_POST['url']))? '?': '?'.$_POST['url'].'&';

            // model에서 쿼리문 구성을 위해 unset()
            unset($_POST['action']);
            unset($_POST['url']);
            unset($_POST['search']);
            unset($_POST['id_duple']);

            $this->_model->update();

            // 패스워드가 입력되어 있으면 수정한다.
            if ($_POST['password']) {
                $this->_model->updatePassword($_POST['idx']);
            }

            $url = 'index.php'. $url .'enter=v&idx='. $_POST['idx'];
            header("Location: $url");
        }
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
            // 접근 권한 확인후 적용
            if (!$this->_auth->isAdmin()) {
                noAuthMsg();
            }

            $url = (empty($_POST['url']))? '': '?'.$_POST['url'];

            $this->_model->delete();

            $url = 'index.php'.$url;
            header("Location: $url");
        }
    }
}