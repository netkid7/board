-- --------------------------------------------------------
-- 호스트:                          127.0.0.1
-- 서버 버전:                        10.1.13-MariaDB - mariadb.org binary distribution
-- 서버 OS:                        Win32
-- HeidiSQL 버전:                  9.4.0.5130
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- 테이블 brighten.brn_attach 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_attach` (
  `a_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_parent` varchar(50) NOT NULL,
  `a_parent_idx` int(11) unsigned NOT NULL,
  `a_file_name` varchar(50) NOT NULL,
  `a_save_name` varchar(50) NOT NULL,
  PRIMARY KEY (`a_idx`),
  KEY `a_parent` (`a_parent`),
  KEY `a_parent_idx` (`a_parent_idx`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='첨부파일\r\n';

-- 테이블 데이터 brighten.brn_attach:1 rows 내보내기
/*!40000 ALTER TABLE `brn_attach` DISABLE KEYS */;
INSERT INTO `brn_attach` (`a_idx`, `a_parent`, `a_parent_idx`, `a_file_name`, `a_save_name`) VALUES
	(1, 'brn_board', 1, 'Eloquent_JavaScript.pdf', '1483690752.pdf');
/*!40000 ALTER TABLE `brn_attach` ENABLE KEYS */;

-- 테이블 brighten.brn_auth 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_auth` (
  `a_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `a_table` varchar(50) NOT NULL COMMENT '권한적용하는 테이블',
  `a_list` tinyint(3) NOT NULL DEFAULT '0',
  `a_view` tinyint(3) NOT NULL DEFAULT '0' COMMENT '== a_list',
  `a_write` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_list',
  `a_download` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_list',
  `a_modify` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_view',
  `a_remove` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_view',
  `a_reply` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_view 작성권한',
  `a_comment` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_view 작성권한',
  `a_notice` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_create',
  `a_secret` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_create',
  `a_attach` tinyint(3) NOT NULL DEFAULT '0' COMMENT '>= a_create',
  `f_reply` varchar(5) NOT NULL DEFAULT 'y' COMMENT '답글 사용여부',
  `f_comment` varchar(5) NOT NULL DEFAULT 'y' COMMENT '덧글 사용여부',
  `f_notice` varchar(5) NOT NULL DEFAULT 'y' COMMENT '공지사항 사용여부',
  `f_secret` varchar(5) NOT NULL DEFAULT 'n' COMMENT '비밀글 사용여부',
  `f_attach_count` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'max_count(첨부파일)',
  `f_attach_type` varchar(200) NOT NULL COMMENT '첨부파일 확장자',
  PRIMARY KEY (`a_idx`),
  KEY `a_table` (`a_table`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='각 권한별 최소 접근 레벨\r\nCRUD 는 기본 기능이므로 기능 사용여부 없음\r\n그외는 권한+기능 조합';

-- 테이블 데이터 brighten.brn_auth:8 rows 내보내기
/*!40000 ALTER TABLE `brn_auth` DISABLE KEYS */;
INSERT INTO `brn_auth` (`a_idx`, `a_table`, `a_list`, `a_view`, `a_write`, `a_download`, `a_modify`, `a_remove`, `a_reply`, `a_comment`, `a_notice`, `a_secret`, `a_attach`, `f_reply`, `f_comment`, `f_notice`, `f_secret`, `f_attach_count`, `f_attach_type`) VALUES
	(1, 'brn_member', 9, 9, 9, 9, 9, 9, 0, 0, 0, 0, 0, 'y', 'y', 'n', 'n', 0, ''),
	(2, 'brn_notice', 1, 1, 9, 1, 9, 9, 0, 0, 0, 0, 9, 'y', 'y', 'y', 'n', 3, ''),
	(3, 'brn_board', 1, 1, 1, 1, 1, 1, 1, 1, 9, 1, 1, 'n', 'n', 'y', 'y', 2, ''),
	(4, 'brn_gallery', 1, 1, 1, 1, 1, 1, 1, 1, 9, 1, 1, 'y', 'y', 'y', 'y', 2, 'jpg,png,bmp,gif'),
	(5, 'brn_pds', 1, 1, 9, 1, 9, 9, 0, 0, 9, 0, 9, 'y', 'y', 'y', 'n', 2, '*'),
	(6, 'brn_qan', 1, 1, 1, 0, 9, 9, 1, 1, 9, 0, 0, 'y', 'y', 'y', 'n', 2, 'jpg,png,bmp,gif'),
	(7, 'brn_walk', 9, 9, 1, 9, 9, 9, 0, 0, 9, 0, 0, 'n', 'n', 'n', 'n', 0, ''),
	(8, 'brn_comment', 1, 1, 1, 0, 1, 1, 0, 9, 0, 0, 0, 'n', 'n', 'n', 'n', 0, '');
/*!40000 ALTER TABLE `brn_auth` ENABLE KEYS */;

-- 테이블 brighten.brn_board 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_board` (
  `b_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음',
  `b_depth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
  `b_parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자신의 부모글',
  `b_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
  `b_title` varchar(200) NOT NULL,
  `b_name` varchar(100) NOT NULL,
  `b_id` varchar(50) NOT NULL,
  `b_password` varchar(50) NOT NULL,
  `b_content` text,
  `b_count` int(10) unsigned DEFAULT '0',
  `b_email` varchar(100) DEFAULT NULL,
  `b_secret` tinyint(3) unsigned DEFAULT '0',
  `b_notice` tinyint(3) unsigned DEFAULT '0',
  `b_reg_IP` varchar(50) DEFAULT NULL,
  `b_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`b_idx`),
  KEY `b_ref` (`b_ref`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='게시판 기본 테이블\r\n';

-- 테이블 데이터 brighten.brn_board:5 rows 내보내기
/*!40000 ALTER TABLE `brn_board` DISABLE KEYS */;
INSERT INTO `brn_board` (`b_idx`, `b_ref`, `b_depth`, `b_parent`, `b_order`, `b_title`, `b_name`, `b_id`, `b_password`, `b_content`, `b_count`, `b_email`, `b_secret`, `b_notice`, `b_reg_IP`, `b_reg_date`) VALUES
	(1, 1, 0, 0, 0, '제목입니다. 가나다라마바사아자차카타파하 아야어여오요우유으이abcdefghijklm', '관리자', 'adminx', 'adminx', '&lt;p&gt;내용이에요&lt;/p&gt;', 9, 'abc@test.com', 1, 1, '127.0.0.1', '2017-02-07 10:44:22'),
	(2, 2, 0, 0, 0, 'test', '관리자', 'adminx', 'adminx', '&lt;p&gt;test&lt;br&gt;&lt;/p&gt;', 3, '', 0, 0, '127.0.0.1', '2017-02-10 13:57:10'),
	(3, 2, 1, 2, 1, 'Re:test', '관리자', 'adminx', 'adminx', '&lt;ul class=&quot;reply&quot;&gt;&lt;li&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;', 1, '', 0, 0, '127.0.0.1', '2017-02-10 14:07:44'),
	(4, 2, 2, 3, 3, 'Re:Re:test', '관리자', 'adminx', 'adminx', '&lt;ul class=&quot;reply&quot;&gt;&lt;li&gt;&lt;ul class=&quot;reply&quot;&gt;&lt;li&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;', 0, '', 0, 0, '127.0.0.1', '2017-02-10 14:08:04'),
	(5, 2, 1, 2, 2, 'Re:test', '관리자', 'adminx', 'adminx', '&lt;ul class=&quot;reply&quot;&gt;&lt;li&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;&lt;/li&gt;&lt;/ul&gt;&lt;p&gt;test&lt;br&gt;&lt;/p&gt;', 15, '', 0, 0, '127.0.0.1', '2017-02-10 14:08:12');
/*!40000 ALTER TABLE `brn_board` ENABLE KEYS */;

-- 테이블 brighten.brn_comment 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_comment` (
  `c_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `c_parent` varchar(50) NOT NULL COMMENT '대상 테이블',
  `c_parent_idx` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음의 원본글인 대상 테이블의 글 번호',
  `c_depth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
  `c_upper` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자신의 부모글(윗글)',
  `c_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
  `c_content` text,
  `c_name` varchar(100) NOT NULL,
  `c_id` varchar(50) NOT NULL,
  `c_password` varchar(50) NOT NULL,
  `c_email` varchar(100) DEFAULT NULL,
  `c_reg_IP` varchar(50) DEFAULT NULL,
  `c_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`c_idx`),
  KEY `c_parent` (`c_parent`),
  KEY `c_parent_idx` (`c_parent_idx`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='댓글\r\n';

-- 테이블 데이터 brighten.brn_comment:0 rows 내보내기
/*!40000 ALTER TABLE `brn_comment` DISABLE KEYS */;
/*!40000 ALTER TABLE `brn_comment` ENABLE KEYS */;

-- 테이블 brighten.brn_gallery 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_gallery` (
  `b_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음',
  `b_depth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
  `b_parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자신의 부모글',
  `b_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
  `b_title` varchar(200) NOT NULL,
  `b_name` varchar(100) NOT NULL,
  `b_id` varchar(50) NOT NULL,
  `b_password` varchar(50) NOT NULL,
  `b_content` text,
  `b_count` int(10) unsigned DEFAULT '0',
  `b_email` varchar(100) DEFAULT NULL,
  `b_secret` tinyint(3) unsigned DEFAULT '0',
  `b_notice` tinyint(3) unsigned DEFAULT '0',
  `b_reg_IP` varchar(50) DEFAULT NULL,
  `b_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`b_idx`),
  KEY `b_ref` (`b_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Q&A\r\n';

-- 테이블 데이터 brighten.brn_gallery:0 rows 내보내기
/*!40000 ALTER TABLE `brn_gallery` DISABLE KEYS */;
/*!40000 ALTER TABLE `brn_gallery` ENABLE KEYS */;

-- 테이블 brighten.brn_member 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_member` (
  `m_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `m_id` varchar(50) NOT NULL,
  `m_password` varchar(200) NOT NULL,
  `m_name` varchar(100) DEFAULT NULL,
  `m_level` varchar(50) DEFAULT NULL,
  `m_state` varchar(50) DEFAULT NULL,
  `m_email` varchar(100) DEFAULT NULL,
  `m_dept` varchar(200) DEFAULT NULL,
  `m_phone` varchar(50) DEFAULT NULL,
  `m_memo` varchar(200) DEFAULT NULL,
  `m_last_in` datetime DEFAULT NULL,
  `m_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`m_idx`),
  KEY `m_id` (`m_id`),
  KEY `m_password` (`m_password`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='회원정보\r\n';

-- 테이블 데이터 brighten.brn_member:3 rows 내보내기
/*!40000 ALTER TABLE `brn_member` DISABLE KEYS */;
INSERT INTO `brn_member` (`m_idx`, `m_id`, `m_password`, `m_name`, `m_level`, `m_state`, `m_email`, `m_dept`, `m_phone`, `m_memo`, `m_last_in`, `m_reg_date`) VALUES
	(1, 'adminx', '33275a8aa48ea918bd53a9181aa975f15ab0d0645398f5918a006d08675c1cb27d5c645dbd084eee56e675e25ba4019f2ecea37ca9e2995b49fcb12c096a032e', '관리자', '99', 'y', 'email', NULL, '연락처', NULL, '2017-02-10 13:55:56', '2017-01-06 10:32:28'),
	(2, 'tester1', '33275a8aa48ea918bd53a9181aa975f15ab0d0645398f5918a006d08675c1cb27d5c645dbd084eee56e675e25ba4019f2ecea37ca9e2995b49fcb12c096a032e', 'tester1', '1', 'y', 'e1', NULL, 't1', NULL, '2017-01-06 17:22:16', '2017-01-06 11:26:40'),
	(3, 'tester2', '33275a8aa48ea918bd53a9181aa975f15ab0d0645398f5918a006d08675c1cb27d5c645dbd084eee56e675e25ba4019f2ecea37ca9e2995b49fcb12c096a032e', '새이름', '1', 'y', 'dummy@test.com', NULL, '010-2222-5678', '가나다라마바아자차카타파하', '2017-01-06 13:56:34', '2017-01-06 11:38:46');
/*!40000 ALTER TABLE `brn_member` ENABLE KEYS */;

-- 테이블 brighten.brn_notice 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_notice` (
  `b_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음',
  `b_depth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
  `b_parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자신의 부모글',
  `b_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
  `b_title` varchar(200) NOT NULL,
  `b_name` varchar(100) NOT NULL,
  `b_id` varchar(50) NOT NULL,
  `b_password` varchar(50) NOT NULL,
  `b_content` text,
  `b_count` int(10) unsigned DEFAULT '0',
  `b_email` varchar(100) DEFAULT NULL,
  `b_secret` tinyint(3) unsigned DEFAULT '0',
  `b_notice` tinyint(3) unsigned DEFAULT '0',
  `b_reg_IP` varchar(50) DEFAULT NULL,
  `b_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`b_idx`),
  KEY `b_ref` (`b_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='공지사항';

-- 테이블 데이터 brighten.brn_notice:0 rows 내보내기
/*!40000 ALTER TABLE `brn_notice` DISABLE KEYS */;
/*!40000 ALTER TABLE `brn_notice` ENABLE KEYS */;

-- 테이블 brighten.brn_pds 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_pds` (
  `b_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음',
  `b_depth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
  `b_parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자신의 부모글',
  `b_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
  `b_title` varchar(200) NOT NULL,
  `b_name` varchar(100) NOT NULL,
  `b_id` varchar(50) NOT NULL,
  `b_password` varchar(50) NOT NULL,
  `b_content` text,
  `b_count` int(10) unsigned DEFAULT '0',
  `b_email` varchar(100) DEFAULT NULL,
  `b_secret` tinyint(3) unsigned DEFAULT '0',
  `b_notice` tinyint(3) unsigned DEFAULT '0',
  `b_reg_IP` varchar(50) DEFAULT NULL,
  `b_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`b_idx`),
  KEY `b_ref` (`b_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='자료실\r\n';

-- 테이블 데이터 brighten.brn_pds:0 rows 내보내기
/*!40000 ALTER TABLE `brn_pds` DISABLE KEYS */;
/*!40000 ALTER TABLE `brn_pds` ENABLE KEYS */;

-- 테이블 brighten.brn_qna 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_qna` (
  `b_idx` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `b_ref` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음',
  `b_depth` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
  `b_parent` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '자신의 부모글',
  `b_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
  `b_title` varchar(200) NOT NULL,
  `b_name` varchar(100) NOT NULL,
  `b_id` varchar(50) NOT NULL,
  `b_password` varchar(50) NOT NULL,
  `b_content` text,
  `b_count` int(10) unsigned DEFAULT '0',
  `b_email` varchar(100) DEFAULT NULL,
  `b_secret` tinyint(3) unsigned DEFAULT '0',
  `b_notice` tinyint(3) unsigned DEFAULT '0',
  `b_reg_IP` varchar(50) DEFAULT NULL,
  `b_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`b_idx`),
  KEY `b_ref` (`b_ref`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='갤러리\r\n';

-- 테이블 데이터 brighten.brn_qna:0 rows 내보내기
/*!40000 ALTER TABLE `brn_qna` DISABLE KEYS */;
/*!40000 ALTER TABLE `brn_qna` ENABLE KEYS */;

-- 테이블 brighten.brn_walk 구조 내보내기
CREATE TABLE IF NOT EXISTS `brn_walk` (
  `w_idx` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `w_name` varchar(100) NOT NULL COMMENT '이름',
  `w_sex` tinyint(3) unsigned NOT NULL COMMENT '성별',
  `w_age` tinyint(3) unsigned NOT NULL COMMENT '나이',
  `w_tel` varchar(50) NOT NULL COMMENT '연락처',
  `w_app` varchar(100) NOT NULL COMMENT '사용앱',
  `w_reg_IP` varchar(50) DEFAULT NULL,
  `w_reg_date` datetime DEFAULT NULL,
  PRIMARY KEY (`w_idx`),
  KEY `w_name_w_tel` (`w_name`,`w_tel`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='걷기왕 신청내역';

-- 테이블 데이터 brighten.brn_walk:~2 rows (대략적) 내보내기
/*!40000 ALTER TABLE `brn_walk` DISABLE KEYS */;
INSERT INTO `brn_walk` (`w_idx`, `w_name`, `w_sex`, `w_age`, `w_tel`, `w_app`, `w_reg_IP`, `w_reg_date`) VALUES
	(2, '나이름1', 1, 35, '018-1478-5236', '기타', '127.0.0.1', '2017-02-09 13:20:37'),
	(3, '김아름', 2, 28, '010-0123-2232', 'stepz', '127.0.0.1', '2017-02-09 14:18:36');
/*!40000 ALTER TABLE `brn_walk` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
