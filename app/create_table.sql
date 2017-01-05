CREATE TABLE `brn_attach` (
    `a_idx` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `a_parent` VARCHAR(50) NOT NULL,
    `a_parent_idx` INT(11) UNSIGNED NOT NULL,
    `a_file_name` VARCHAR(50) NOT NULL,
    `a_save_name` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`a_idx`),
    INDEX `a_parent` (`a_parent`),
    INDEX `a_parent_idx` (`a_parent_idx`)
)
COMMENT='첨부파일\r\n'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `brn_auth` (
    `a_idx` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `a_table` VARCHAR(50) NOT NULL COMMENT '권한적용하는 테이블',
    `a_list` TINYINT(3) NOT NULL DEFAULT '0',
    `a_view` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '== a_list',
    `a_write` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_list',
    `a_download` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_list',
    `a_modify` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_view',
    `a_remove` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_view',
    `a_reply` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_view 작성권한',
    `a_comment` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_view 작성권한',
    `a_notice` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_create',
    `a_secret` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_create',
    `a_attach` TINYINT(3) NOT NULL DEFAULT '0' COMMENT '>= a_create',
    `f_notice` VARCHAR(5) NOT NULL DEFAULT 'y' COMMENT '공지사항 사용여부',
    `f_secret` VARCHAR(5) NOT NULL DEFAULT 'n' COMMENT '비밀글 사용여부',
    `f_attach_count` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'max_count(첨부파일)',
    `f_attach_type` VARCHAR(200) NOT NULL COMMENT '첨부파일 확장자',
    PRIMARY KEY (`a_idx`),
    INDEX `a_table` (`a_table`)
)
COMMENT='각 권한별 최소 접근 레벨\r\nCRUD 는 기본 기능이므로 기능 사용여부 없음\r\n그외는 권한+기능 조합'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `brn_board` (
    `b_idx` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `b_ref` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '글 묶음',
    `b_depth` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '글 묶음 들여쓰기',
    `b_parent` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '자신의 부모글',
    `b_order` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '글 묶음 정렬순서',
    `b_title` VARCHAR(200) NOT NULL,
    `b_name` VARCHAR(100) NOT NULL,
    `b_id` VARCHAR(50) NOT NULL,
    `b_password` VARCHAR(50) NOT NULL,
    `b_content` TEXT NULL,
    `b_count` INT(10) UNSIGNED NULL DEFAULT '0',
    `b_email` VARCHAR(100) NULL DEFAULT NULL,
    `b_secret` TINYINT(3) UNSIGNED NULL DEFAULT '0',
    `b_notice` TINYINT(3) UNSIGNED NULL DEFAULT '0',
    `b_reg_IP` VARCHAR(50) NULL DEFAULT NULL,
    `b_reg_date` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`b_idx`),
    INDEX `b_ref` (`b_ref`)
)
COMMENT='게시판\r\n'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

CREATE TABLE `brn_member` (
    `m_idx` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `m_id` VARCHAR(50) NOT NULL,
    `m_password` VARCHAR(200) NOT NULL,
    `m_name` VARCHAR(100) NULL DEFAULT NULL,
    `m_level` VARCHAR(50) NULL DEFAULT NULL,
    `m_state` VARCHAR(50) NULL DEFAULT NULL,
    `m_email` VARCHAR(100) NULL DEFAULT NULL,
    `m_dept` VARCHAR(200) NULL DEFAULT NULL,
    `m_phone` VARCHAR(50) NULL DEFAULT NULL,
    `m_memo` VARCHAR(200) NULL DEFAULT NULL,
    `m_last_in` DATETIME NULL DEFAULT NULL,
    `m_reg_date` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`m_idx`),
    INDEX `m_id` (`m_id`),
    INDEX `m_password` (`m_password`)
)
COMMENT='회원정보\r\n'
COLLATE='utf8_general_ci'
ENGINE=MyISAM
;

