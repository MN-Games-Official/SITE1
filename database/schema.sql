-- =============================================================================
-- EduWrite AI - Educational SaaS Platform Database Schema
-- =============================================================================
-- A school-governed AI writing assistant platform.
-- MySQL 8.0+ compatible. All tables use InnoDB, utf8mb4_unicode_ci.
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- 1. schools
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `schools`;
CREATE TABLE `schools` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `name`              VARCHAR(255)    NOT NULL,
    `slug`              VARCHAR(100)    NOT NULL,
    `domain`            VARCHAR(255)    DEFAULT NULL,
    `logo_url`          VARCHAR(500)    DEFAULT NULL,
    `subscription_tier` VARCHAR(50)     NOT NULL DEFAULT 'free',
    `settings`          JSON            DEFAULT NULL,
    `timezone`          VARCHAR(64)     NOT NULL DEFAULT 'America/New_York',
    `is_active`         TINYINT(1)      NOT NULL DEFAULT 1,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_schools_slug` (`slug`),
    UNIQUE KEY `uq_schools_domain` (`domain`),
    INDEX `idx_schools_is_active` (`is_active`),
    INDEX `idx_schools_subscription_tier` (`subscription_tier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. users
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `school_id`         INT UNSIGNED    NOT NULL,
    `email`             VARCHAR(255)    NOT NULL,
    `username`          VARCHAR(100)    NOT NULL,
    `password_hash`     VARCHAR(255)    NOT NULL,
    `first_name`        VARCHAR(100)    NOT NULL,
    `last_name`         VARCHAR(100)    NOT NULL,
    `role`              ENUM('student','teacher','admin','super_admin') NOT NULL DEFAULT 'student',
    `avatar_url`        VARCHAR(500)    DEFAULT NULL,
    `is_active`         TINYINT(1)      NOT NULL DEFAULT 1,
    `email_verified_at` TIMESTAMP       NULL DEFAULT NULL,
    `last_login_at`     TIMESTAMP       NULL DEFAULT NULL,
    `login_count`       INT UNSIGNED    NOT NULL DEFAULT 0,
    `preferences`       JSON            DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    UNIQUE KEY `uq_users_username` (`username`),
    INDEX `idx_users_school_id` (`school_id`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_is_active` (`is_active`),
    INDEX `idx_users_created_at` (`created_at`),
    CONSTRAINT `fk_users_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. sessions
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    NOT NULL,
    `token`         VARCHAR(128)    NOT NULL,
    `ip_address`    VARCHAR(45)     DEFAULT NULL,
    `user_agent`    TEXT            DEFAULT NULL,
    `csrf_token`    VARCHAR(128)    NOT NULL,
    `expires_at`    TIMESTAMP       NOT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_sessions_token` (`token`),
    INDEX `idx_sessions_user_id` (`user_id`),
    INDEX `idx_sessions_expires_at` (`expires_at`),
    CONSTRAINT `fk_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. password_resets
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    NOT NULL,
    `token`         VARCHAR(128)    NOT NULL,
    `expires_at`    TIMESTAMP       NOT NULL,
    `used_at`       TIMESTAMP       NULL DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_password_resets_token` (`token`),
    INDEX `idx_password_resets_user_id` (`user_id`),
    INDEX `idx_password_resets_expires_at` (`expires_at`),
    CONSTRAINT `fk_password_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. classes
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `school_id`     INT UNSIGNED    NOT NULL,
    `teacher_id`    INT UNSIGNED    NOT NULL,
    `name`          VARCHAR(255)    NOT NULL,
    `description`   TEXT            DEFAULT NULL,
    `code`          VARCHAR(20)     NOT NULL,
    `subject`       VARCHAR(100)    DEFAULT NULL,
    `grade_level`   VARCHAR(20)     DEFAULT NULL,
    `academic_year` VARCHAR(20)     DEFAULT NULL,
    `semester`      VARCHAR(20)     DEFAULT NULL,
    `is_active`     TINYINT(1)      NOT NULL DEFAULT 1,
    `is_archived`   TINYINT(1)      NOT NULL DEFAULT 0,
    `settings`      JSON            DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_classes_code` (`code`),
    INDEX `idx_classes_school_id` (`school_id`),
    INDEX `idx_classes_teacher_id` (`teacher_id`),
    INDEX `idx_classes_is_active` (`is_active`),
    INDEX `idx_classes_academic_year` (`academic_year`),
    CONSTRAINT `fk_classes_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_classes_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. enrollments
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `class_id`      INT UNSIGNED    NOT NULL,
    `student_id`    INT UNSIGNED    NOT NULL,
    `status`        ENUM('active','inactive','dropped') NOT NULL DEFAULT 'active',
    `enrolled_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `dropped_at`    TIMESTAMP       NULL DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_enrollments_class_student` (`class_id`, `student_id`),
    INDEX `idx_enrollments_student_id` (`student_id`),
    INDEX `idx_enrollments_status` (`status`),
    CONSTRAINT `fk_enrollments_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_enrollments_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. ai_policy_rules  (created before assignments due to FK reference)
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ai_policy_rules`;
CREATE TABLE `ai_policy_rules` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `school_id`         INT UNSIGNED    DEFAULT NULL,
    `class_id`          INT UNSIGNED    DEFAULT NULL,
    `assignment_id`     INT UNSIGNED    DEFAULT NULL,
    `rule_name`         VARCHAR(255)    NOT NULL,
    `rule_type`         ENUM('allow','deny','limit','redirect') NOT NULL,
    `category`          ENUM('content_generation','grammar','brainstorm','outline','analysis','reflection','rewrite','translation','code_help','other') NOT NULL,
    `strictness_level`  ENUM('lenient','moderate','strict','exam') NOT NULL DEFAULT 'moderate',
    `conditions`        JSON            DEFAULT NULL,
    `message`           TEXT            DEFAULT NULL,
    `is_active`         TINYINT(1)      NOT NULL DEFAULT 1,
    `priority`          INT             NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ai_policy_rules_school_id` (`school_id`),
    INDEX `idx_ai_policy_rules_class_id` (`class_id`),
    INDEX `idx_ai_policy_rules_assignment_id` (`assignment_id`),
    INDEX `idx_ai_policy_rules_category` (`category`),
    INDEX `idx_ai_policy_rules_is_active` (`is_active`),
    INDEX `idx_ai_policy_rules_priority` (`priority`),
    CONSTRAINT `fk_ai_policy_rules_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_policy_rules_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_policy_rules_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. assignments
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `assignments`;
CREATE TABLE `assignments` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `class_id`          INT UNSIGNED    NOT NULL,
    `teacher_id`        INT UNSIGNED    NOT NULL,
    `title`             VARCHAR(255)    NOT NULL,
    `description`       TEXT            DEFAULT NULL,
    `instructions`      TEXT            DEFAULT NULL,
    `assignment_type`   ENUM('essay','research','creative','reflection','analysis','other') NOT NULL DEFAULT 'essay',
    `due_date`          DATETIME        DEFAULT NULL,
    `available_from`    DATETIME        DEFAULT NULL,
    `max_words`         INT UNSIGNED    DEFAULT NULL,
    `min_words`         INT UNSIGNED    DEFAULT NULL,
    `rubric`            JSON            DEFAULT NULL,
    `ai_policy_id`      INT UNSIGNED    DEFAULT NULL,
    `is_published`      TINYINT(1)      NOT NULL DEFAULT 0,
    `is_archived`       TINYINT(1)      NOT NULL DEFAULT 0,
    `allow_late`        TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_assignments_class_id` (`class_id`),
    INDEX `idx_assignments_teacher_id` (`teacher_id`),
    INDEX `idx_assignments_due_date` (`due_date`),
    INDEX `idx_assignments_is_published` (`is_published`),
    INDEX `idx_assignments_assignment_type` (`assignment_type`),
    INDEX `idx_assignments_ai_policy_id` (`ai_policy_id`),
    CONSTRAINT `fk_assignments_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_assignments_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_assignments_ai_policy` FOREIGN KEY (`ai_policy_id`) REFERENCES `ai_policy_rules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 9. documents
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `documents`;
CREATE TABLE `documents` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED    NOT NULL,
    `assignment_id`     INT UNSIGNED    DEFAULT NULL,
    `title`             VARCHAR(255)    NOT NULL,
    `content`           LONGTEXT        DEFAULT NULL,
    `word_count`        INT UNSIGNED    NOT NULL DEFAULT 0,
    `character_count`   INT UNSIGNED    NOT NULL DEFAULT 0,
    `status`            ENUM('draft','in_progress','submitted','returned','graded') NOT NULL DEFAULT 'draft',
    `is_shared`         TINYINT(1)      NOT NULL DEFAULT 0,
    `share_token`       VARCHAR(64)     DEFAULT NULL,
    `last_autosave_at`  TIMESTAMP       NULL DEFAULT NULL,
    `submitted_at`      TIMESTAMP       NULL DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_documents_share_token` (`share_token`),
    INDEX `idx_documents_user_id` (`user_id`),
    INDEX `idx_documents_assignment_id` (`assignment_id`),
    INDEX `idx_documents_status` (`status`),
    INDEX `idx_documents_created_at` (`created_at`),
    CONSTRAINT `fk_documents_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_documents_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 10. document_versions
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `document_versions`;
CREATE TABLE `document_versions` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `document_id`       INT UNSIGNED    NOT NULL,
    `user_id`           INT UNSIGNED    NOT NULL,
    `version_number`    INT UNSIGNED    NOT NULL,
    `content`           LONGTEXT        DEFAULT NULL,
    `word_count`        INT UNSIGNED    NOT NULL DEFAULT 0,
    `change_summary`    VARCHAR(500)    DEFAULT NULL,
    `snapshot_type`     ENUM('autosave','manual','submission','revert') NOT NULL DEFAULT 'autosave',
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_doc_versions_doc_version` (`document_id`, `version_number`),
    INDEX `idx_document_versions_user_id` (`user_id`),
    INDEX `idx_document_versions_snapshot_type` (`snapshot_type`),
    CONSTRAINT `fk_document_versions_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_document_versions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 11. document_comments
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `document_comments`;
CREATE TABLE `document_comments` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `document_id`       INT UNSIGNED    NOT NULL,
    `user_id`           INT UNSIGNED    NOT NULL,
    `content`           TEXT            NOT NULL,
    `selection_start`   INT             DEFAULT NULL,
    `selection_end`     INT             DEFAULT NULL,
    `parent_id`         INT UNSIGNED    DEFAULT NULL,
    `is_resolved`       TINYINT(1)      NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_document_comments_document_id` (`document_id`),
    INDEX `idx_document_comments_user_id` (`user_id`),
    INDEX `idx_document_comments_parent_id` (`parent_id`),
    INDEX `idx_document_comments_is_resolved` (`is_resolved`),
    CONSTRAINT `fk_document_comments_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_document_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_document_comments_parent` FOREIGN KEY (`parent_id`) REFERENCES `document_comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 12. document_shares
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `document_shares`;
CREATE TABLE `document_shares` (
    `id`                    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `document_id`           INT UNSIGNED    NOT NULL,
    `shared_with_user_id`   INT UNSIGNED    NOT NULL,
    `permission`            ENUM('view','comment','edit') NOT NULL DEFAULT 'view',
    `shared_by_user_id`     INT UNSIGNED    NOT NULL,
    `created_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_document_shares_doc_user` (`document_id`, `shared_with_user_id`),
    INDEX `idx_document_shares_shared_with` (`shared_with_user_id`),
    INDEX `idx_document_shares_shared_by` (`shared_by_user_id`),
    CONSTRAINT `fk_document_shares_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_document_shares_shared_with` FOREIGN KEY (`shared_with_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_document_shares_shared_by` FOREIGN KEY (`shared_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 13. ai_sessions
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ai_sessions`;
CREATE TABLE `ai_sessions` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED    NOT NULL,
    `document_id`       INT UNSIGNED    DEFAULT NULL,
    `assignment_id`     INT UNSIGNED    DEFAULT NULL,
    `session_token`     VARCHAR(128)    NOT NULL,
    `mode`              ENUM('brainstorm','outline','draft_coach','revision','grammar','reflection','analysis','planning','interpret','redirect') NOT NULL,
    `started_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `ended_at`          TIMESTAMP       NULL DEFAULT NULL,
    `request_count`     INT UNSIGNED    NOT NULL DEFAULT 0,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_ai_sessions_token` (`session_token`),
    INDEX `idx_ai_sessions_user_id` (`user_id`),
    INDEX `idx_ai_sessions_document_id` (`document_id`),
    INDEX `idx_ai_sessions_assignment_id` (`assignment_id`),
    INDEX `idx_ai_sessions_mode` (`mode`),
    INDEX `idx_ai_sessions_created_at` (`created_at`),
    CONSTRAINT `fk_ai_sessions_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_sessions_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_ai_sessions_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 14. ai_requests
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ai_requests`;
CREATE TABLE `ai_requests` (
    `id`                    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `session_id`            INT UNSIGNED    NOT NULL,
    `user_id`               INT UNSIGNED    NOT NULL,
    `document_id`           INT UNSIGNED    DEFAULT NULL,
    `request_type`          ENUM('brainstorm','outline','draft_coach','revision','grammar','reflection','analysis','planning','interpret','redirect','other') NOT NULL,
    `prompt`                TEXT            NOT NULL,
    `selected_text`         TEXT            DEFAULT NULL,
    `context_summary`       TEXT            DEFAULT NULL,
    `model_used`            VARCHAR(100)    DEFAULT NULL,
    `provider`              VARCHAR(50)     DEFAULT NULL,
    `status`                ENUM('pending','processing','completed','failed','refused','redirected') NOT NULL DEFAULT 'pending',
    `policy_check_result`   ENUM('allowed','denied','redirected','flagged') DEFAULT NULL,
    `risk_score`            DECIMAL(3,2)    DEFAULT NULL,
    `tokens_used`           INT UNSIGNED    DEFAULT NULL,
    `response_time_ms`      INT UNSIGNED    DEFAULT NULL,
    `created_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ai_requests_session_id` (`session_id`),
    INDEX `idx_ai_requests_user_id` (`user_id`),
    INDEX `idx_ai_requests_document_id` (`document_id`),
    INDEX `idx_ai_requests_status` (`status`),
    INDEX `idx_ai_requests_request_type` (`request_type`),
    INDEX `idx_ai_requests_created_at` (`created_at`),
    INDEX `idx_ai_requests_policy_check` (`policy_check_result`),
    CONSTRAINT `fk_ai_requests_session` FOREIGN KEY (`session_id`) REFERENCES `ai_sessions` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_requests_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ai_requests_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 15. ai_responses
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ai_responses`;
CREATE TABLE `ai_responses` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `request_id`        INT UNSIGNED    NOT NULL,
    `content`           LONGTEXT        NOT NULL,
    `response_type`     ENUM('suggestion','feedback','outline','analysis','reflection','redirect','refusal','error') NOT NULL,
    `metadata`          JSON            DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ai_responses_request_id` (`request_id`),
    INDEX `idx_ai_responses_response_type` (`response_type`),
    CONSTRAINT `fk_ai_responses_request` FOREIGN KEY (`request_id`) REFERENCES `ai_requests` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 16. ai_provider_config
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `ai_provider_config`;
CREATE TABLE `ai_provider_config` (
    `id`                    INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `school_id`             INT UNSIGNED    DEFAULT NULL,
    `provider_name`         VARCHAR(100)    NOT NULL,
    `api_endpoint`          VARCHAR(500)    NOT NULL,
    `model_name`            VARCHAR(100)    NOT NULL,
    `is_default`            TINYINT(1)      NOT NULL DEFAULT 0,
    `is_enabled`            TINYINT(1)      NOT NULL DEFAULT 1,
    `max_tokens`            INT UNSIGNED    NOT NULL DEFAULT 2048,
    `temperature`           DECIMAL(3,2)    NOT NULL DEFAULT 0.70,
    `rate_limit_per_minute` INT UNSIGNED    NOT NULL DEFAULT 30,
    `rate_limit_per_hour`   INT UNSIGNED    NOT NULL DEFAULT 500,
    `settings`              JSON            DEFAULT NULL,
    `created_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`            TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_ai_provider_config_school_id` (`school_id`),
    INDEX `idx_ai_provider_config_provider` (`provider_name`),
    INDEX `idx_ai_provider_config_is_default` (`is_default`),
    CONSTRAINT `fk_ai_provider_config_school` FOREIGN KEY (`school_id`) REFERENCES `schools` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 17. policy_violations
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `policy_violations`;
CREATE TABLE `policy_violations` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED    NOT NULL,
    `document_id`       INT UNSIGNED    DEFAULT NULL,
    `assignment_id`     INT UNSIGNED    DEFAULT NULL,
    `ai_request_id`     INT UNSIGNED    DEFAULT NULL,
    `violation_type`    ENUM('direct_answer','plagiarism_attempt','rewrite_detection','exam_violation','laundering','excessive_use','other') NOT NULL,
    `severity`          ENUM('low','medium','high','critical') NOT NULL DEFAULT 'low',
    `description`       TEXT            DEFAULT NULL,
    `evidence`          TEXT            DEFAULT NULL,
    `status`            ENUM('pending','reviewed','resolved','dismissed') NOT NULL DEFAULT 'pending',
    `reviewed_by`       INT UNSIGNED    DEFAULT NULL,
    `reviewed_at`       TIMESTAMP       NULL DEFAULT NULL,
    `resolution_notes`  TEXT            DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_policy_violations_user_id` (`user_id`),
    INDEX `idx_policy_violations_document_id` (`document_id`),
    INDEX `idx_policy_violations_assignment_id` (`assignment_id`),
    INDEX `idx_policy_violations_ai_request_id` (`ai_request_id`),
    INDEX `idx_policy_violations_status` (`status`),
    INDEX `idx_policy_violations_severity` (`severity`),
    INDEX `idx_policy_violations_created_at` (`created_at`),
    CONSTRAINT `fk_policy_violations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_policy_violations_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_policy_violations_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_policy_violations_ai_request` FOREIGN KEY (`ai_request_id`) REFERENCES `ai_requests` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_policy_violations_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 18. integrity_flags
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `integrity_flags`;
CREATE TABLE `integrity_flags` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `document_id`   INT UNSIGNED    NOT NULL,
    `user_id`       INT UNSIGNED    NOT NULL,
    `flag_type`     ENUM('suspicious_pattern','rapid_content','external_paste','style_mismatch','ai_overuse','policy_breach') NOT NULL,
    `severity`      ENUM('info','warning','serious','critical') NOT NULL DEFAULT 'info',
    `details`       JSON            DEFAULT NULL,
    `is_reviewed`   TINYINT(1)      NOT NULL DEFAULT 0,
    `reviewed_by`   INT UNSIGNED    DEFAULT NULL,
    `reviewed_at`   TIMESTAMP       NULL DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_integrity_flags_document_id` (`document_id`),
    INDEX `idx_integrity_flags_user_id` (`user_id`),
    INDEX `idx_integrity_flags_flag_type` (`flag_type`),
    INDEX `idx_integrity_flags_severity` (`severity`),
    INDEX `idx_integrity_flags_is_reviewed` (`is_reviewed`),
    INDEX `idx_integrity_flags_created_at` (`created_at`),
    CONSTRAINT `fk_integrity_flags_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_integrity_flags_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_integrity_flags_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 19. notifications
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    NOT NULL,
    `type`          ENUM('assignment','comment','flag','grade','system','policy','ai_alert','class','reminder') NOT NULL,
    `title`         VARCHAR(255)    NOT NULL,
    `message`       TEXT            DEFAULT NULL,
    `link`          VARCHAR(500)    DEFAULT NULL,
    `is_read`       TINYINT(1)      NOT NULL DEFAULT 0,
    `read_at`       TIMESTAMP       NULL DEFAULT NULL,
    `metadata`      JSON            DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_notifications_user_id` (`user_id`),
    INDEX `idx_notifications_type` (`type`),
    INDEX `idx_notifications_is_read` (`is_read`),
    INDEX `idx_notifications_created_at` (`created_at`),
    INDEX `idx_notifications_user_read` (`user_id`, `is_read`),
    CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 20. activity_logs
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    NOT NULL,
    `action`        VARCHAR(100)    NOT NULL,
    `entity_type`   VARCHAR(50)     DEFAULT NULL,
    `entity_id`     INT UNSIGNED    DEFAULT NULL,
    `details`       JSON            DEFAULT NULL,
    `ip_address`    VARCHAR(45)     DEFAULT NULL,
    `user_agent`    TEXT            DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_activity_logs_user_id` (`user_id`),
    INDEX `idx_activity_logs_action` (`action`),
    INDEX `idx_activity_logs_entity` (`entity_type`, `entity_id`),
    INDEX `idx_activity_logs_created_at` (`created_at`),
    CONSTRAINT `fk_activity_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 21. student_support_profiles
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `student_support_profiles`;
CREATE TABLE `student_support_profiles` (
    `id`                            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`                       INT UNSIGNED    NOT NULL,
    `writing_level`                 ENUM('beginner','developing','proficient','advanced') NOT NULL DEFAULT 'developing',
    `learning_style`                VARCHAR(100)    DEFAULT NULL,
    `accommodations`                JSON            DEFAULT NULL,
    `ai_interaction_preferences`    JSON            DEFAULT NULL,
    `support_notes`                 TEXT            DEFAULT NULL,
    `last_assessment_at`            TIMESTAMP       NULL DEFAULT NULL,
    `created_at`                    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`                    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_student_support_profiles_user` (`user_id`),
    CONSTRAINT `fk_student_support_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 22. teacher_comments
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `teacher_comments`;
CREATE TABLE `teacher_comments` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `assignment_id` INT UNSIGNED    NOT NULL,
    `student_id`    INT UNSIGNED    NOT NULL,
    `document_id`   INT UNSIGNED    DEFAULT NULL,
    `teacher_id`    INT UNSIGNED    NOT NULL,
    `comment`       TEXT            NOT NULL,
    `comment_type`  ENUM('feedback','grade','encouragement','concern','suggestion') NOT NULL DEFAULT 'feedback',
    `grade`         VARCHAR(10)     DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_teacher_comments_assignment_id` (`assignment_id`),
    INDEX `idx_teacher_comments_student_id` (`student_id`),
    INDEX `idx_teacher_comments_document_id` (`document_id`),
    INDEX `idx_teacher_comments_teacher_id` (`teacher_id`),
    INDEX `idx_teacher_comments_comment_type` (`comment_type`),
    CONSTRAINT `fk_teacher_comments_assignment` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_teacher_comments_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_teacher_comments_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_teacher_comments_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 23. analytics_events
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `analytics_events`;
CREATE TABLE `analytics_events` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED    NOT NULL,
    `event_type`        VARCHAR(100)    NOT NULL,
    `event_category`    VARCHAR(50)     DEFAULT NULL,
    `event_data`        JSON            DEFAULT NULL,
    `session_id`        VARCHAR(128)    DEFAULT NULL,
    `page_url`          VARCHAR(500)    DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_analytics_events_user_id` (`user_id`),
    INDEX `idx_analytics_events_event_type` (`event_type`),
    INDEX `idx_analytics_events_event_category` (`event_category`),
    INDEX `idx_analytics_events_session_id` (`session_id`),
    INDEX `idx_analytics_events_created_at` (`created_at`),
    CONSTRAINT `fk_analytics_events_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 24. export_jobs
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `export_jobs`;
CREATE TABLE `export_jobs` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`       INT UNSIGNED    NOT NULL,
    `export_type`   ENUM('document','class_report','student_report','analytics','grades','audit_log') NOT NULL,
    `status`        ENUM('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
    `file_path`     VARCHAR(500)    DEFAULT NULL,
    `file_size`     INT UNSIGNED    DEFAULT NULL,
    `parameters`    JSON            DEFAULT NULL,
    `error_message` TEXT            DEFAULT NULL,
    `started_at`    TIMESTAMP       NULL DEFAULT NULL,
    `completed_at`  TIMESTAMP       NULL DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_export_jobs_user_id` (`user_id`),
    INDEX `idx_export_jobs_status` (`status`),
    INDEX `idx_export_jobs_created_at` (`created_at`),
    CONSTRAINT `fk_export_jobs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 25. import_jobs
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `import_jobs`;
CREATE TABLE `import_jobs` (
    `id`                INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `user_id`           INT UNSIGNED    NOT NULL,
    `import_type`       ENUM('students','assignments','classes','documents') NOT NULL,
    `status`            ENUM('pending','processing','completed','failed','partial') NOT NULL DEFAULT 'pending',
    `file_path`         VARCHAR(500)    DEFAULT NULL,
    `total_rows`        INT UNSIGNED    DEFAULT NULL,
    `processed_rows`    INT UNSIGNED    DEFAULT NULL,
    `error_rows`        INT UNSIGNED    DEFAULT NULL,
    `errors`            JSON            DEFAULT NULL,
    `started_at`        TIMESTAMP       NULL DEFAULT NULL,
    `completed_at`      TIMESTAMP       NULL DEFAULT NULL,
    `created_at`        TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_import_jobs_user_id` (`user_id`),
    INDEX `idx_import_jobs_status` (`status`),
    INDEX `idx_import_jobs_created_at` (`created_at`),
    CONSTRAINT `fk_import_jobs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 26. settings
-- -----------------------------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id`            INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    `scope`         ENUM('global','school','class','user') NOT NULL DEFAULT 'global',
    `scope_id`      INT UNSIGNED    NOT NULL DEFAULT 0,
    `setting_key`   VARCHAR(100)    NOT NULL,
    `setting_value` TEXT            DEFAULT NULL,
    `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_settings_scope_key` (`scope`, `scope_id`, `setting_key`),
    INDEX `idx_settings_scope` (`scope`, `scope_id`),
    INDEX `idx_settings_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
