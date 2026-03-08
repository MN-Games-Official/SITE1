-- =============================================================================
-- EduWrite AI - Seed Data
-- =============================================================================
-- Realistic sample data for development and testing.
-- Run AFTER schema.sql has been applied.
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- -----------------------------------------------------------------------------
-- Schools (2)
-- -----------------------------------------------------------------------------
INSERT INTO `schools` (`id`, `name`, `slug`, `domain`, `logo_url`, `subscription_tier`, `settings`, `timezone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Lincoln Heights Academy', 'lincoln-heights-academy', 'lincolnheights.edu', '/uploads/logos/lincoln-heights.png', 'premium', JSON_OBJECT(
    'max_students', 500,
    'ai_enabled', true,
    'allow_student_sharing', true,
    'require_email_verification', true,
    'default_ai_strictness', 'moderate'
), 'America/New_York', 1, '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(2, 'Riverside Community School', 'riverside-community', 'riverside-cs.edu', '/uploads/logos/riverside.png', 'standard', JSON_OBJECT(
    'max_students', 200,
    'ai_enabled', true,
    'allow_student_sharing', false,
    'require_email_verification', false,
    'default_ai_strictness', 'strict'
), 'America/Chicago', 1, '2024-09-01 10:00:00', '2024-09-01 10:00:00');

-- -----------------------------------------------------------------------------
-- Users (10) — passwords are bcrypt hashes of "Password123!"
-- -----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `school_id`, `email`, `username`, `password_hash`, `first_name`, `last_name`, `role`, `avatar_url`, `is_active`, `email_verified_at`, `last_login_at`, `login_count`, `preferences`, `created_at`, `updated_at`) VALUES
-- Super admin
(1,  1, 'admin@eduwriteai.com',         'superadmin',       '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'System',    'Admin',      'super_admin', NULL, 1, '2024-08-01 09:00:00', '2025-01-15 08:30:00', 142, JSON_OBJECT('theme', 'dark', 'notifications_email', true), '2024-08-01 09:00:00', '2025-01-15 08:30:00'),

-- Lincoln Heights: admin, teacher, 4 students
(2,  1, 'p.martinez@lincolnheights.edu', 'pmartinez',        '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Patricia',  'Martinez',   'admin',       NULL, 1, '2024-08-02 10:00:00', '2025-01-14 15:00:00',  87, JSON_OBJECT('theme', 'light', 'notifications_email', true), '2024-08-02 10:00:00', '2025-01-14 15:00:00'),
(3,  1, 'j.oconnor@lincolnheights.edu',  'joconnor',         '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'James',     'O''Connor',  'teacher',     '/uploads/avatars/joconnor.jpg', 1, '2024-08-05 11:00:00', '2025-01-15 07:45:00', 203, JSON_OBJECT('theme', 'light', 'default_rubric', 'standard'), '2024-08-05 11:00:00', '2025-01-15 07:45:00'),
(4,  1, 'e.chen@lincolnheights.edu',     'echen',            '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Emily',     'Chen',       'student',     NULL, 1, '2024-09-01 08:00:00', '2025-01-15 09:10:00',  64, JSON_OBJECT('theme', 'auto', 'font_size', 14), '2024-09-01 08:00:00', '2025-01-15 09:10:00'),
(5,  1, 'm.johnson@lincolnheights.edu',  'mjohnson',         '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Marcus',    'Johnson',    'student',     NULL, 1, '2024-09-01 08:00:00', '2025-01-14 14:30:00',  45, JSON_OBJECT('theme', 'dark', 'font_size', 16), '2024-09-01 08:00:00', '2025-01-14 14:30:00'),
(6,  1, 's.patel@lincolnheights.edu',    'spatel',           '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Sophia',    'Patel',      'student',     NULL, 1, '2024-09-02 08:00:00', '2025-01-15 10:00:00',  52, JSON_OBJECT('theme', 'light', 'font_size', 14), '2024-09-02 08:00:00', '2025-01-15 10:00:00'),
(7,  1, 'a.williams@lincolnheights.edu', 'awilliams',        '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Aiden',     'Williams',   'student',     NULL, 1, '2024-09-02 08:00:00', '2025-01-13 11:20:00',  38, JSON_OBJECT('theme', 'auto', 'font_size', 14), '2024-09-02 08:00:00', '2025-01-13 11:20:00'),

-- Riverside: teacher, student
(8,  2, 'l.garcia@riverside-cs.edu',     'lgarcia',          '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Laura',     'Garcia',     'teacher',     '/uploads/avatars/lgarcia.jpg', 1, '2024-09-05 09:00:00', '2025-01-15 08:00:00', 115, JSON_OBJECT('theme', 'light', 'default_rubric', 'detailed'), '2024-09-05 09:00:00', '2025-01-15 08:00:00'),
(9,  2, 'k.nguyen@riverside-cs.edu',     'knguyen',          '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Kevin',     'Nguyen',     'student',     NULL, 1, '2024-09-10 08:00:00', '2025-01-15 09:30:00',  30, JSON_OBJECT('theme', 'dark', 'font_size', 15), '2024-09-10 08:00:00', '2025-01-15 09:30:00'),
(10, 2, 'r.thompson@riverside-cs.edu',   'rthompson',        '$2y$12$LJ3m4ys3Gz8y/YQFH0sZOeFGYpfGoBmN1V6Bnv/cKZwR0qF2Xab6y', 'Rachel',    'Thompson',   'student',     NULL, 1, '2024-09-10 08:00:00', '2025-01-14 16:00:00',  27, JSON_OBJECT('theme', 'light', 'font_size', 14), '2024-09-10 08:00:00', '2025-01-14 16:00:00');

-- -----------------------------------------------------------------------------
-- Classes (3)
-- -----------------------------------------------------------------------------
INSERT INTO `classes` (`id`, `school_id`, `teacher_id`, `name`, `description`, `code`, `subject`, `grade_level`, `academic_year`, `semester`, `is_active`, `is_archived`, `settings`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'AP English Language & Composition', 'Advanced Placement English focusing on rhetorical analysis, argument, and synthesis.', 'ENG-AP-24F', 'English', '11', '2024-2025', 'Fall', 1, 0, JSON_OBJECT('allow_peer_review', true, 'ai_brainstorm_enabled', true, 'word_count_visible', true), '2024-08-20 09:00:00', '2024-08-20 09:00:00'),
(2, 1, 3, 'English 10 – Creative Writing', 'Exploration of fiction, poetry, and creative nonfiction through guided writing workshops.', 'ENG-CW-24F', 'English', '10', '2024-2025', 'Fall', 1, 0, JSON_OBJECT('allow_peer_review', true, 'ai_brainstorm_enabled', true, 'word_count_visible', false), '2024-08-20 09:30:00', '2024-08-20 09:30:00'),
(3, 2, 8, 'World History – Research & Writing', 'Writing-intensive world history course with an emphasis on primary-source analysis.', 'HIST-RW-24F', 'History', '9', '2024-2025', 'Fall', 1, 0, JSON_OBJECT('allow_peer_review', false, 'ai_brainstorm_enabled', true, 'word_count_visible', true), '2024-09-01 10:00:00', '2024-09-01 10:00:00');

-- -----------------------------------------------------------------------------
-- Enrollments
-- -----------------------------------------------------------------------------
INSERT INTO `enrollments` (`id`, `class_id`, `student_id`, `status`, `enrolled_at`, `created_at`) VALUES
(1,  1, 4, 'active', '2024-09-03 08:00:00', '2024-09-03 08:00:00'),
(2,  1, 5, 'active', '2024-09-03 08:05:00', '2024-09-03 08:05:00'),
(3,  1, 6, 'active', '2024-09-03 08:10:00', '2024-09-03 08:10:00'),
(4,  2, 6, 'active', '2024-09-03 08:15:00', '2024-09-03 08:15:00'),
(5,  2, 7, 'active', '2024-09-03 08:20:00', '2024-09-03 08:20:00'),
(6,  2, 4, 'active', '2024-09-03 08:25:00', '2024-09-03 08:25:00'),
(7,  3, 9, 'active', '2024-09-12 08:00:00', '2024-09-12 08:00:00'),
(8,  3, 10,'active', '2024-09-12 08:05:00', '2024-09-12 08:05:00');

-- -----------------------------------------------------------------------------
-- AI Policy Rules
-- -----------------------------------------------------------------------------
INSERT INTO `ai_policy_rules` (`id`, `school_id`, `class_id`, `assignment_id`, `rule_name`, `rule_type`, `category`, `strictness_level`, `conditions`, `message`, `is_active`, `priority`, `created_at`, `updated_at`) VALUES
-- School-wide defaults for Lincoln Heights
(1, 1, NULL, NULL, 'Allow Grammar Assistance',      'allow', 'grammar',            'lenient',   NULL, 'Grammar and spelling checks are always available.', 1, 10, '2024-08-15 09:00:00', '2024-08-15 09:00:00'),
(2, 1, NULL, NULL, 'Allow Brainstorming',            'allow', 'brainstorm',         'moderate',  NULL, 'AI brainstorming is permitted for idea generation.', 1, 10, '2024-08-15 09:00:00', '2024-08-15 09:00:00'),
(3, 1, NULL, NULL, 'Deny Direct Content Generation', 'deny',  'content_generation', 'strict',    NULL, 'AI cannot write essay content directly. Use brainstorm or outline modes instead.', 1, 100, '2024-08-15 09:00:00', '2024-08-15 09:00:00'),
(4, 1, NULL, NULL, 'Limit Rewriting',                'limit', 'rewrite',            'moderate',  JSON_OBJECT('max_requests_per_document', 3, 'min_original_ratio', 0.7), 'You may use limited rewrite suggestions, but at least 70% of your text must remain original.', 1, 50, '2024-08-15 09:00:00', '2024-08-15 09:00:00'),

-- Riverside school-wide
(5, 2, NULL, NULL, 'Strict Content Generation Ban',  'deny',  'content_generation', 'strict',    NULL, 'AI-generated content is not permitted at Riverside Community School.', 1, 100, '2024-09-05 10:00:00', '2024-09-05 10:00:00'),
(6, 2, NULL, NULL, 'Allow Outlining',                'allow', 'outline',            'moderate',  NULL, 'Students may use AI to help structure outlines.', 1, 10, '2024-09-05 10:00:00', '2024-09-05 10:00:00'),

-- Class-level override
(7, NULL, 1, NULL, 'AP Exam Mode – Analysis Only',   'allow', 'analysis',           'exam',      JSON_OBJECT('applies_during', 'exam_window'), 'During exam preparation, only text-analysis mode is available.', 1, 200, '2024-10-01 09:00:00', '2024-10-01 09:00:00');

-- -----------------------------------------------------------------------------
-- Assignments
-- -----------------------------------------------------------------------------
INSERT INTO `assignments` (`id`, `class_id`, `teacher_id`, `title`, `description`, `instructions`, `assignment_type`, `due_date`, `available_from`, `max_words`, `min_words`, `rubric`, `ai_policy_id`, `is_published`, `is_archived`, `allow_late`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Rhetorical Analysis: "Letter from Birmingham Jail"',
    'Analyze Martin Luther King Jr.''s rhetorical strategies in his 1963 letter.',
    'Write a 1000-1500 word essay analyzing the rhetorical appeals (ethos, pathos, logos) used by Dr. King. Support your claims with direct quotations from the text. You may use the AI brainstorm tool for idea generation, but all writing must be your own.',
    'analysis', '2025-02-14 23:59:59', '2025-01-20 00:00:00', 1500, 1000,
    JSON_OBJECT(
        'criteria', JSON_ARRAY(
            JSON_OBJECT('name', 'Thesis & Argument', 'weight', 25, 'description', 'Clear, arguable thesis with logical structure'),
            JSON_OBJECT('name', 'Evidence & Analysis', 'weight', 30, 'description', 'Effective use of textual evidence with insightful analysis'),
            JSON_OBJECT('name', 'Organization', 'weight', 20, 'description', 'Logical paragraph structure with smooth transitions'),
            JSON_OBJECT('name', 'Style & Mechanics', 'weight', 15, 'description', 'Formal tone, varied sentence structure, minimal errors'),
            JSON_OBJECT('name', 'MLA Formatting', 'weight', 10, 'description', 'Proper MLA citations and formatting')
        )
    ),
    NULL, 1, 0, 1, '2025-01-10 14:00:00', '2025-01-10 14:00:00'),

(2, 2, 3, 'Short Story: The Unexpected Journey',
    'Write an original short story centered on the theme of an unexpected journey.',
    'Craft a short story (800-1200 words) with a clear narrative arc, developed characters, and vivid sensory details. You may use the AI brainstorm tool to generate initial ideas and the outline tool for planning, but the creative writing must be entirely your own.',
    'creative', '2025-02-21 23:59:59', '2025-01-27 00:00:00', 1200, 800,
    JSON_OBJECT(
        'criteria', JSON_ARRAY(
            JSON_OBJECT('name', 'Narrative Arc', 'weight', 25, 'description', 'Clear beginning, rising action, climax, and resolution'),
            JSON_OBJECT('name', 'Character Development', 'weight', 25, 'description', 'Well-drawn characters with believable motivations'),
            JSON_OBJECT('name', 'Setting & Imagery', 'weight', 20, 'description', 'Vivid sensory details that bring the world to life'),
            JSON_OBJECT('name', 'Voice & Style', 'weight', 20, 'description', 'Distinctive narrative voice and engaging prose'),
            JSON_OBJECT('name', 'Mechanics', 'weight', 10, 'description', 'Correct grammar, punctuation, and spelling')
        )
    ),
    NULL, 1, 0, 0, '2025-01-15 10:00:00', '2025-01-15 10:00:00'),

(3, 3, 8, 'Primary Source Analysis: The Silk Road',
    'Analyze a primary source document related to trade along the Silk Road.',
    'Select one of the provided primary sources. Write a 600-900 word analysis addressing: who created the source, the intended audience, the historical context, and what it reveals about cultural exchange along the Silk Road. AI outline assistance is permitted.',
    'research', '2025-02-07 23:59:59', '2025-01-20 00:00:00', 900, 600,
    JSON_OBJECT(
        'criteria', JSON_ARRAY(
            JSON_OBJECT('name', 'Source Identification', 'weight', 20, 'description', 'Accurately identifies author, date, and purpose'),
            JSON_OBJECT('name', 'Historical Context', 'weight', 25, 'description', 'Places the source within its broader historical setting'),
            JSON_OBJECT('name', 'Analysis & Argument', 'weight', 30, 'description', 'Draws insightful conclusions supported by evidence'),
            JSON_OBJECT('name', 'Writing Quality', 'weight', 25, 'description', 'Clear, organized, and well-written prose')
        )
    ),
    6, 1, 0, 1, '2025-01-12 11:00:00', '2025-01-12 11:00:00'),

(4, 1, 3, 'Reflection Journal: Week 3',
    'Reflect on your writing process this week.',
    'Write a 300-500 word reflection on what you learned about your own writing process this week. Consider: What strategies worked? What was challenging? How will you approach next week differently?',
    'reflection', '2025-01-31 23:59:59', '2025-01-27 00:00:00', 500, 300,
    NULL,
    NULL, 1, 0, 0, '2025-01-20 09:00:00', '2025-01-20 09:00:00');

-- -----------------------------------------------------------------------------
-- Documents
-- -----------------------------------------------------------------------------
INSERT INTO `documents` (`id`, `user_id`, `assignment_id`, `title`, `content`, `word_count`, `character_count`, `status`, `is_shared`, `share_token`, `last_autosave_at`, `submitted_at`, `created_at`, `updated_at`) VALUES
(1, 4, 1, 'Rhetorical Analysis – Emily Chen',
    'In his "Letter from Birmingham Jail," Dr. Martin Luther King Jr. masterfully employs rhetorical strategies to defend nonviolent resistance and challenge the complacency of white moderates. Through a carefully woven tapestry of ethos, pathos, and logos, King transforms a personal letter into a universal call for justice that transcends the immediate context of the 1963 Birmingham campaign.\n\nKing establishes his credibility early in the letter by invoking his position as president of the Southern Christian Leadership Conference...',
    287, 1614, 'in_progress', 0, NULL, '2025-01-28 15:30:00', NULL, '2025-01-22 10:00:00', '2025-01-28 15:30:00'),

(2, 5, 1, 'MLK Rhetorical Analysis Draft',
    'Martin Luther King Jr. wrote his famous letter while sitting in a jail cell in Birmingham, Alabama. The letter is addressed to fellow clergymen who criticized his nonviolent protest campaign. In this essay, I will analyze the rhetorical strategies King uses to persuade his audience...',
    46, 285, 'draft', 0, NULL, '2025-01-25 11:00:00', NULL, '2025-01-24 09:00:00', '2025-01-25 11:00:00'),

(3, 6, 2, 'The Map That Led Nowhere',
    'The attic smelled of cedar and forgotten Christmases. Priya pushed aside a box of tangled ornaments and reached for the leather tube wedged behind the water heater. Inside, she found a hand-drawn map on paper so thin she could see her fingertips through it.\n\n"Gran, what is this?" she called downstairs, but only silence answered. She had the house to herself for the first time since the funeral...',
    68, 396, 'in_progress', 0, NULL, '2025-01-30 16:00:00', NULL, '2025-01-28 14:00:00', '2025-01-30 16:00:00'),

(4, 9, 3, 'Silk Road Trade: A Merchant''s Account',
    'The selected primary source is a merchant''s diary fragment dated approximately 1150 CE, discovered in the Dunhuang caves. The anonymous author describes a journey westward from Chang''an carrying silk, porcelain, and paper...',
    34, 219, 'submitted', 0, NULL, '2025-02-03 20:00:00', '2025-02-03 20:15:00', '2025-01-25 09:00:00', '2025-02-03 20:15:00');

-- -----------------------------------------------------------------------------
-- Document Versions
-- -----------------------------------------------------------------------------
INSERT INTO `document_versions` (`id`, `document_id`, `user_id`, `version_number`, `content`, `word_count`, `change_summary`, `snapshot_type`, `created_at`) VALUES
(1, 1, 4, 1, 'In his "Letter from Birmingham Jail," Dr. Martin Luther King Jr. masterfully employs rhetorical strategies...', 120, 'Initial draft with introduction', 'manual', '2025-01-22 10:00:00'),
(2, 1, 4, 2, 'In his "Letter from Birmingham Jail," Dr. Martin Luther King Jr. masterfully employs rhetorical strategies to defend nonviolent resistance and challenge the complacency of white moderates...', 220, 'Expanded thesis and added ethos paragraph', 'autosave', '2025-01-26 14:00:00'),
(3, 4, 9, 1, 'The selected primary source is a merchant''s diary fragment...', 20, 'Initial draft', 'manual', '2025-01-25 09:00:00'),
(4, 4, 9, 2, 'The selected primary source is a merchant''s diary fragment dated approximately 1150 CE, discovered in the Dunhuang caves. The anonymous author describes a journey westward from Chang''an carrying silk, porcelain, and paper...', 34, 'Final submission', 'submission', '2025-02-03 20:15:00');

-- -----------------------------------------------------------------------------
-- Document Comments
-- -----------------------------------------------------------------------------
INSERT INTO `document_comments` (`id`, `document_id`, `user_id`, `content`, `selection_start`, `selection_end`, `parent_id`, `is_resolved`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Strong opening! Your thesis is clear and sets up the analysis well. Consider briefly previewing the specific rhetorical appeals you will examine.', 0, 45, NULL, 0, '2025-01-27 09:00:00', '2025-01-27 09:00:00'),
(2, 1, 4, 'Thank you, Mr. O''Connor! I''ll add a roadmap sentence before the next draft.', NULL, NULL, 1, 0, '2025-01-27 14:30:00', '2025-01-27 14:30:00'),
(3, 2, 3, 'Good start, Marcus. Remember to go beyond summary — the assignment asks for analysis. What effect do these strategies have on the audience?', 0, 50, NULL, 0, '2025-01-26 10:00:00', '2025-01-26 10:00:00');

-- -----------------------------------------------------------------------------
-- AI Provider Configurations
-- -----------------------------------------------------------------------------
INSERT INTO `ai_provider_config` (`id`, `school_id`, `provider_name`, `api_endpoint`, `model_name`, `is_default`, `is_enabled`, `max_tokens`, `temperature`, `rate_limit_per_minute`, `rate_limit_per_hour`, `settings`, `created_at`, `updated_at`) VALUES
(1, NULL, 'openai',    'https://api.openai.com/v1/chat/completions', 'gpt-4o-mini', 1, 1, 2048, 0.70, 30, 500,
    JSON_OBJECT('system_prompt_prefix', 'You are an educational writing coach. Never write content for the student.', 'safety_filter', true),
    '2024-08-01 09:00:00', '2024-08-01 09:00:00'),

(2, NULL, 'anthropic', 'https://api.anthropic.com/v1/messages', 'claude-3-5-haiku-20241022', 0, 1, 2048, 0.60, 20, 300,
    JSON_OBJECT('system_prompt_prefix', 'You are an educational writing coach. Guide students through the writing process without doing the work for them.', 'safety_filter', true),
    '2024-08-01 09:00:00', '2024-08-01 09:00:00'),

(3, 1, 'openai', 'https://api.openai.com/v1/chat/completions', 'gpt-4o', 1, 1, 4096, 0.65, 20, 400,
    JSON_OBJECT('system_prompt_prefix', 'You are a Socratic writing tutor at Lincoln Heights Academy. Ask guiding questions rather than providing answers.', 'safety_filter', true),
    '2024-08-15 09:00:00', '2024-08-15 09:00:00'),

(4, 2, 'openai', 'https://api.openai.com/v1/chat/completions', 'gpt-4o-mini', 1, 1, 1024, 0.50, 15, 200,
    JSON_OBJECT('system_prompt_prefix', 'You are a strict educational assistant at Riverside Community School. Only provide feedback and questions. Never generate content.', 'safety_filter', true),
    '2024-09-05 10:00:00', '2024-09-05 10:00:00');

-- -----------------------------------------------------------------------------
-- AI Sessions (sample)
-- -----------------------------------------------------------------------------
INSERT INTO `ai_sessions` (`id`, `user_id`, `document_id`, `assignment_id`, `session_token`, `mode`, `started_at`, `ended_at`, `request_count`, `created_at`) VALUES
(1, 4, 1, 1, 'ais_ec_20250122_a1b2c3d4e5f6a7b8c9d0e1f2a3b4c5d6', 'brainstorm', '2025-01-22 09:30:00', '2025-01-22 09:55:00', 3, '2025-01-22 09:30:00'),
(2, 4, 1, 1, 'ais_ec_20250126_f6e5d4c3b2a1f0e9d8c7b6a5f4e3d2c1', 'revision',   '2025-01-26 13:00:00', '2025-01-26 13:45:00', 2, '2025-01-26 13:00:00'),
(3, 9, 4, 3, 'ais_kn_20250130_1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d', 'outline',    '2025-01-30 10:00:00', '2025-01-30 10:30:00', 2, '2025-01-30 10:00:00');

-- -----------------------------------------------------------------------------
-- AI Requests (sample)
-- -----------------------------------------------------------------------------
INSERT INTO `ai_requests` (`id`, `session_id`, `user_id`, `document_id`, `request_type`, `prompt`, `selected_text`, `context_summary`, `model_used`, `provider`, `status`, `policy_check_result`, `risk_score`, `tokens_used`, `response_time_ms`, `created_at`) VALUES
(1, 1, 4, 1, 'brainstorm',
    'I need to analyze the rhetorical strategies in Letter from Birmingham Jail. What are some angles I could explore?',
    NULL, 'Student is beginning a rhetorical analysis essay on MLK''s Letter from Birmingham Jail.',
    'gpt-4o', 'openai', 'completed', 'allowed', 0.05, 482, 1230, '2025-01-22 09:32:00'),

(2, 1, 4, 1, 'brainstorm',
    'Can you help me think about how King uses pathos specifically?',
    NULL, 'Student exploring pathos in rhetorical analysis.',
    'gpt-4o', 'openai', 'completed', 'allowed', 0.08, 356, 980, '2025-01-22 09:40:00'),

(3, 2, 4, 1, 'revision',
    'Can you check if my introduction paragraph has a clear thesis?',
    'In his "Letter from Birmingham Jail," Dr. Martin Luther King Jr. masterfully employs rhetorical strategies to defend nonviolent resistance.',
    'Student reviewing thesis clarity in introduction.',
    'gpt-4o', 'openai', 'completed', 'allowed', 0.10, 290, 870, '2025-01-26 13:05:00'),

(4, 3, 9, 4, 'outline',
    'Help me structure my analysis of a Silk Road merchant diary.',
    NULL, 'Student planning a primary source analysis for World History.',
    'gpt-4o-mini', 'openai', 'completed', 'allowed', 0.03, 410, 1100, '2025-01-30 10:05:00');

-- -----------------------------------------------------------------------------
-- AI Responses (sample)
-- -----------------------------------------------------------------------------
INSERT INTO `ai_responses` (`id`, `request_id`, `content`, `response_type`, `metadata`, `created_at`) VALUES
(1, 1, 'Great question! Here are some angles to consider for your rhetorical analysis:\n\n1. **Ethos (Credibility):** How does King establish his authority? Consider his references to historical figures, religious leaders, and his own role in the movement.\n\n2. **Pathos (Emotion):** Look at the vivid descriptions of injustice — the "stinging darts of segregation" passage is particularly powerful.\n\n3. **Logos (Logic):** Examine his step-by-step argument for why the protests were necessary and timely.\n\n4. **Kairos (Timeliness):** Why was this the right moment for action?\n\nWhich of these angles interests you most? What connections do you already see in the text?',
    'suggestion', JSON_OBJECT('confidence', 0.92, 'educational_value', 'high'), '2025-01-22 09:32:01'),

(2, 2, 'To explore King''s use of pathos, consider these guiding questions:\n\n- What emotions does King evoke when he describes explaining segregation to his young daughter?\n- How does the contrast between the "just" and "unjust" laws create an emotional response?\n- Where does King shift from rational argument to moral urgency?\n\nTry highlighting 2-3 specific passages that moved you as a reader, then analyze *why* they are effective. What word choices and imagery create that emotional impact?',
    'feedback', JSON_OBJECT('confidence', 0.89, 'educational_value', 'high'), '2025-01-22 09:40:01'),

(3, 3, 'Your thesis is a good start! It identifies the text, the author, and the general strategy (rhetorical strategies). To strengthen it, consider:\n\n- **Be more specific:** Which rhetorical strategies will you focus on? Naming them gives your reader a roadmap.\n- **Add the "so what":** Why does King''s use of these strategies matter? What effect do they achieve?\n\nA stronger version might follow this pattern: "In [text], [author] uses [specific strategy 1], [strategy 2], and [strategy 3] to [achieve what purpose/effect]."\n\nWhat specific strategies are you planning to discuss?',
    'feedback', JSON_OBJECT('confidence', 0.94, 'educational_value', 'high'), '2025-01-26 13:05:01'),

(4, 4, 'Here is a suggested outline structure for your primary source analysis:\n\n**I. Introduction**\n   - Identify the source (type, date, author if known)\n   - Brief thesis about what the source reveals\n\n**II. Historical Context**\n   - The Silk Road in the 12th century\n   - Dunhuang as a cultural crossroads\n\n**III. Source Analysis**\n   - A. Author''s perspective and purpose\n   - B. Key details about trade goods and routes\n   - C. Evidence of cultural exchange\n\n**IV. Significance**\n   - What does this source tell us that other sources might not?\n   - Limitations of the source\n\n**V. Conclusion**\n\nWhich section would you like to develop first?',
    'outline', JSON_OBJECT('confidence', 0.91, 'educational_value', 'high'), '2025-01-30 10:05:01');

-- -----------------------------------------------------------------------------
-- Student Support Profiles
-- -----------------------------------------------------------------------------
INSERT INTO `student_support_profiles` (`id`, `user_id`, `writing_level`, `learning_style`, `accommodations`, `ai_interaction_preferences`, `support_notes`, `last_assessment_at`, `created_at`, `updated_at`) VALUES
(1, 4, 'proficient', 'visual', JSON_OBJECT('extended_time', false, 'text_to_speech', false), JSON_OBJECT('preferred_mode', 'brainstorm', 'feedback_detail', 'detailed'), 'Strong analytical thinker. Benefits from brainstorming support for idea generation.', '2024-10-15 10:00:00', '2024-09-15 09:00:00', '2024-10-15 10:00:00'),
(2, 5, 'developing', 'kinesthetic', JSON_OBJECT('extended_time', true, 'text_to_speech', true), JSON_OBJECT('preferred_mode', 'draft_coach', 'feedback_detail', 'step_by_step'), 'Benefits from structured step-by-step guidance. Needs additional time on timed assignments. Strong ideas but struggles with organization.', '2024-10-15 10:30:00', '2024-09-15 09:00:00', '2024-10-15 10:30:00'),
(3, 7, 'beginner', 'auditory', JSON_OBJECT('extended_time', true, 'text_to_speech', true, 'simplified_prompts', true), JSON_OBJECT('preferred_mode', 'grammar', 'feedback_detail', 'simple'), 'ELL student. Native Mandarin speaker. Prioritize grammar support and simplified explanations.', '2024-10-20 11:00:00', '2024-09-15 09:00:00', '2024-10-20 11:00:00'),
(4, 9, 'developing', 'reading_writing', JSON_OBJECT('extended_time', false, 'text_to_speech', false), JSON_OBJECT('preferred_mode', 'outline', 'feedback_detail', 'detailed'), 'Good with research but needs support structuring arguments. Responds well to outlining tools.', '2024-11-01 09:00:00', '2024-10-01 09:00:00', '2024-11-01 09:00:00');

-- -----------------------------------------------------------------------------
-- Teacher Comments
-- -----------------------------------------------------------------------------
INSERT INTO `teacher_comments` (`id`, `assignment_id`, `student_id`, `document_id`, `teacher_id`, `comment`, `comment_type`, `grade`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 3, 'Emily, your analysis is off to a strong start. Your thesis clearly identifies King''s rhetorical purpose. As you continue drafting, make sure each body paragraph connects back to your thesis. I look forward to reading the complete essay.', 'feedback', NULL, '2025-01-27 09:15:00', '2025-01-27 09:15:00'),
(2, 1, 5, 2, 3, 'Marcus, I can see you understand the text well, but your draft reads more like a summary than an analysis. Remember: the assignment asks you to analyze HOW King persuades, not just WHAT he says. Try the brainstorm tool to generate analytical angles.', 'suggestion', NULL, '2025-01-26 10:15:00', '2025-01-26 10:15:00'),
(3, 3, 9, 4, 8, 'Excellent primary source analysis, Kevin. You placed the document in its historical context effectively and drew thoughtful conclusions about cultural exchange. Minor note: double-check your date references in paragraph 3.', 'grade', 'A-', '2025-02-05 14:00:00', '2025-02-05 14:00:00');

-- -----------------------------------------------------------------------------
-- Notifications
-- -----------------------------------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `type`, `title`, `message`, `link`, `is_read`, `read_at`, `metadata`, `created_at`) VALUES
(1, 4, 'assignment', 'New Assignment Posted', 'Rhetorical Analysis: "Letter from Birmingham Jail" has been posted in AP English.', '/classes/1/assignments/1', 1, '2025-01-10 15:00:00', NULL, '2025-01-10 14:00:00'),
(2, 4, 'comment',    'New Comment on Your Document', 'Mr. O''Connor left feedback on your rhetorical analysis draft.', '/documents/1#comment-1', 1, '2025-01-27 10:00:00', NULL, '2025-01-27 09:00:00'),
(3, 5, 'assignment', 'New Assignment Posted', 'Rhetorical Analysis: "Letter from Birmingham Jail" has been posted in AP English.', '/classes/1/assignments/1', 1, '2025-01-10 16:00:00', NULL, '2025-01-10 14:00:00'),
(4, 5, 'comment',    'New Comment on Your Document', 'Mr. O''Connor left feedback on your rhetorical analysis draft.', '/documents/2#comment-3', 0, NULL, NULL, '2025-01-26 10:00:00'),
(5, 9, 'grade',      'Assignment Graded', 'Your Silk Road analysis has been graded.', '/classes/3/assignments/3', 1, '2025-02-05 15:00:00', JSON_OBJECT('grade', 'A-'), '2025-02-05 14:00:00'),
(6, 3, 'ai_alert',   'AI Usage Alert', 'Student Emily Chen used 5 AI brainstorm requests on assignment 1.', '/admin/ai-usage?user=4&assignment=1', 0, NULL, JSON_OBJECT('request_count', 5, 'user_id', 4), '2025-01-28 16:00:00');

-- -----------------------------------------------------------------------------
-- Activity Logs (sample)
-- -----------------------------------------------------------------------------
INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1,  3, 'create', 'assignment', 1, JSON_OBJECT('title', 'Rhetorical Analysis'), '10.0.1.45', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2025-01-10 14:00:00'),
(2,  4, 'create', 'document',   1, JSON_OBJECT('assignment_id', 1), '10.0.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-01-22 10:00:00'),
(3,  4, 'ai_request', 'ai_session', 1, JSON_OBJECT('mode', 'brainstorm', 'request_count', 3), '10.0.1.102', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', '2025-01-22 09:55:00'),
(4,  9, 'submit', 'document',  4, JSON_OBJECT('assignment_id', 3, 'word_count', 34), '192.168.1.50', 'Mozilla/5.0 (iPad; CPU OS 17_0)', '2025-02-03 20:15:00'),
(5,  3, 'grade', 'assignment', 3, JSON_OBJECT('student_id', 9, 'grade', 'A-'), '10.0.1.45', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', '2025-02-05 14:00:00');

-- -----------------------------------------------------------------------------
-- Analytics Events (sample)
-- -----------------------------------------------------------------------------
INSERT INTO `analytics_events` (`id`, `user_id`, `event_type`, `event_category`, `event_data`, `session_id`, `page_url`, `created_at`) VALUES
(1, 4, 'document_open', 'engagement', JSON_OBJECT('document_id', 1, 'time_spent_seconds', 1800), 'sess_a1b2c3d4', '/documents/1/edit', '2025-01-22 10:00:00'),
(2, 4, 'ai_brainstorm_start', 'ai_usage', JSON_OBJECT('assignment_id', 1, 'mode', 'brainstorm'), 'sess_a1b2c3d4', '/documents/1/edit', '2025-01-22 09:30:00'),
(3, 9, 'document_submit', 'engagement', JSON_OBJECT('document_id', 4, 'word_count', 34), 'sess_e5f6g7h8', '/documents/4/edit', '2025-02-03 20:15:00');

-- -----------------------------------------------------------------------------
-- Default Settings
-- -----------------------------------------------------------------------------
INSERT INTO `settings` (`id`, `scope`, `scope_id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
-- Global defaults
(1,  'global', 0, 'platform_name',                'EduWrite AI',                       '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(2,  'global', 0, 'default_ai_model',             'gpt-4o-mini',                       '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(3,  'global', 0, 'max_document_size_kb',         '5120',                              '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(4,  'global', 0, 'autosave_interval_seconds',    '30',                                '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(5,  'global', 0, 'session_lifetime_minutes',     '480',                               '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(6,  'global', 0, 'max_ai_requests_per_hour',     '30',                                '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(7,  'global', 0, 'maintenance_mode',             'false',                             '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(8,  'global', 0, 'allow_student_registration',   'false',                             '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(9,  'global', 0, 'default_timezone',             'America/New_York',                  '2024-08-01 09:00:00', '2024-08-01 09:00:00'),
(10, 'global', 0, 'support_email',                'support@eduwriteai.com',            '2024-08-01 09:00:00', '2024-08-01 09:00:00'),

-- School-level overrides for Lincoln Heights (school_id = 1)
(11, 'school', 1, 'ai_enabled',                   'true',                              '2024-08-15 09:00:00', '2024-08-15 09:00:00'),
(12, 'school', 1, 'max_ai_requests_per_hour',     '25',                                '2024-08-15 09:00:00', '2024-08-15 09:00:00'),
(13, 'school', 1, 'allow_peer_sharing',           'true',                              '2024-08-15 09:00:00', '2024-08-15 09:00:00'),
(14, 'school', 1, 'default_strictness',           'moderate',                          '2024-08-15 09:00:00', '2024-08-15 09:00:00'),

-- School-level overrides for Riverside (school_id = 2)
(15, 'school', 2, 'ai_enabled',                   'true',                              '2024-09-05 10:00:00', '2024-09-05 10:00:00'),
(16, 'school', 2, 'max_ai_requests_per_hour',     '15',                                '2024-09-05 10:00:00', '2024-09-05 10:00:00'),
(17, 'school', 2, 'allow_peer_sharing',           'false',                             '2024-09-05 10:00:00', '2024-09-05 10:00:00'),
(18, 'school', 2, 'default_strictness',           'strict',                            '2024-09-05 10:00:00', '2024-09-05 10:00:00'),

-- Class-level setting
(19, 'class', 1, 'ai_exam_mode',                  'false',                             '2024-10-01 09:00:00', '2024-10-01 09:00:00'),

-- User-level preference
(20, 'user', 4, 'editor_font_family',             'Georgia',                           '2024-09-15 10:00:00', '2024-09-15 10:00:00');

SET FOREIGN_KEY_CHECKS = 1;
