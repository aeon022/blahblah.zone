<?php return array (
  'app' => 
  array (
    'name' => 'BLAHBLAHPM',
    'env' => 'local',
    'debug' => false,
    'url' => 'https://blahblah.zone/calapp/public',
    'asset_url' => NULL,
    'front_url' => 'https://blahblah.zone/calapp',
    'timezone' => 'Europe/Vienna',
    'locale' => 'austria',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => 'base64:a0CbAes2wiwSKyLURQYD+FhNXfI2p7UvJxv2+HkhjiY=',
    'cipher' => 'AES-256-CBC',
    'providers' => 
    array (
      0 => 'Illuminate\\Auth\\AuthServiceProvider',
      1 => 'Illuminate\\Broadcasting\\BroadcastServiceProvider',
      2 => 'Illuminate\\Bus\\BusServiceProvider',
      3 => 'Illuminate\\Cache\\CacheServiceProvider',
      4 => 'Illuminate\\Foundation\\Providers\\ConsoleSupportServiceProvider',
      5 => 'Illuminate\\Cookie\\CookieServiceProvider',
      6 => 'Illuminate\\Database\\DatabaseServiceProvider',
      7 => 'Illuminate\\Encryption\\EncryptionServiceProvider',
      8 => 'Illuminate\\Filesystem\\FilesystemServiceProvider',
      9 => 'Illuminate\\Foundation\\Providers\\FoundationServiceProvider',
      10 => 'Illuminate\\Hashing\\HashServiceProvider',
      11 => 'Illuminate\\Mail\\MailServiceProvider',
      12 => 'Illuminate\\Notifications\\NotificationServiceProvider',
      13 => 'Illuminate\\Pagination\\PaginationServiceProvider',
      14 => 'Illuminate\\Pipeline\\PipelineServiceProvider',
      15 => 'Illuminate\\Queue\\QueueServiceProvider',
      16 => 'Illuminate\\Redis\\RedisServiceProvider',
      17 => 'Illuminate\\Auth\\Passwords\\PasswordResetServiceProvider',
      18 => 'Illuminate\\Session\\SessionServiceProvider',
      19 => 'Illuminate\\Translation\\TranslationServiceProvider',
      20 => 'Illuminate\\Validation\\ValidationServiceProvider',
      21 => 'Illuminate\\View\\ViewServiceProvider',
      22 => 'App\\Providers\\AppServiceProvider',
      23 => 'App\\Providers\\AuthServiceProvider',
      24 => 'App\\Providers\\EventServiceProvider',
      25 => 'App\\Providers\\RouteServiceProvider',
      26 => 'Modules\\Installer\\Providers\\LaravelInstallerServiceProvider',
    ),
    'aliases' => 
    array (
      'App' => 'Illuminate\\Support\\Facades\\App',
      'Artisan' => 'Illuminate\\Support\\Facades\\Artisan',
      'Auth' => 'Illuminate\\Support\\Facades\\Auth',
      'Blade' => 'Illuminate\\Support\\Facades\\Blade',
      'Broadcast' => 'Illuminate\\Support\\Facades\\Broadcast',
      'Bus' => 'Illuminate\\Support\\Facades\\Bus',
      'Cache' => 'Illuminate\\Support\\Facades\\Cache',
      'Config' => 'Illuminate\\Support\\Facades\\Config',
      'Cookie' => 'Illuminate\\Support\\Facades\\Cookie',
      'Crypt' => 'Illuminate\\Support\\Facades\\Crypt',
      'DB' => 'Illuminate\\Support\\Facades\\DB',
      'Eloquent' => 'Illuminate\\Database\\Eloquent\\Model',
      'Event' => 'Illuminate\\Support\\Facades\\Event',
      'File' => 'Illuminate\\Support\\Facades\\File',
      'Gate' => 'Illuminate\\Support\\Facades\\Gate',
      'Hash' => 'Illuminate\\Support\\Facades\\Hash',
      'Lang' => 'Illuminate\\Support\\Facades\\Lang',
      'Log' => 'Illuminate\\Support\\Facades\\Log',
      'Mail' => 'Illuminate\\Support\\Facades\\Mail',
      'Notification' => 'Illuminate\\Support\\Facades\\Notification',
      'Password' => 'Illuminate\\Support\\Facades\\Password',
      'Queue' => 'Illuminate\\Support\\Facades\\Queue',
      'Redirect' => 'Illuminate\\Support\\Facades\\Redirect',
      'Redis' => 'Illuminate\\Support\\Facades\\Redis',
      'Request' => 'Illuminate\\Support\\Facades\\Request',
      'Response' => 'Illuminate\\Support\\Facades\\Response',
      'Route' => 'Illuminate\\Support\\Facades\\Route',
      'Schema' => 'Illuminate\\Support\\Facades\\Schema',
      'Session' => 'Illuminate\\Support\\Facades\\Session',
      'Storage' => 'Illuminate\\Support\\Facades\\Storage',
      'URL' => 'Illuminate\\Support\\Facades\\URL',
      'Validator' => 'Illuminate\\Support\\Facades\\Validator',
      'View' => 'Illuminate\\Support\\Facades\\View',
    ),
  ),
  'auth' => 
  array (
    'defaults' => 
    array (
      'guard' => 'api',
      'passwords' => 'users',
    ),
    'guards' => 
    array (
      'web' => 
      array (
        'driver' => 'session',
        'provider' => 'users',
      ),
      'api' => 
      array (
        'driver' => 'jwt',
        'provider' => 'users',
      ),
    ),
    'providers' => 
    array (
      'users' => 
      array (
        'driver' => 'eloquent',
        'model' => 'Modules\\User\\Models\\User\\User',
      ),
    ),
    'passwords' => 
    array (
      'users' => 
      array (
        'provider' => 'users',
        'table' => 'password_resets',
        'expire' => 60,
      ),
    ),
  ),
  'broadcasting' => 
  array (
    'default' => 'log',
    'connections' => 
    array (
      'pusher' => 
      array (
        'driver' => 'pusher',
        'key' => '',
        'secret' => '',
        'app_id' => '',
        'options' => 
        array (
          'cluster' => 'mt1',
          'encrypted' => true,
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
      ),
      'log' => 
      array (
        'driver' => 'log',
      ),
      'null' => 
      array (
        'driver' => 'null',
      ),
    ),
  ),
  'cache' => 
  array (
    'default' => 'file',
    'stores' => 
    array (
      'apc' => 
      array (
        'driver' => 'apc',
      ),
      'array' => 
      array (
        'driver' => 'array',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'cache',
        'connection' => NULL,
      ),
      'file' => 
      array (
        'driver' => 'file',
        'path' => '/home/j0555/public_html/calapp/storage/framework/cache/data',
      ),
      'memcached' => 
      array (
        'driver' => 'memcached',
        'persistent_id' => NULL,
        'sasl' => 
        array (
          0 => NULL,
          1 => NULL,
        ),
        'options' => 
        array (
        ),
        'servers' => 
        array (
          0 => 
          array (
            'host' => '127.0.0.1',
            'port' => 11211,
            'weight' => 100,
          ),
        ),
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'cache',
      ),
    ),
    'prefix' => 'blahblahpm_cache',
  ),
  'core' => 
  array (
    'table_prefix' => 'capl',
    'acl' => 
    array (
      'users_table' => 'capl_users',
      'password_resets_table' => 'capl_password_resets',
      'roles_table' => 'capl_roles',
      'departments_table' => 'capl_departments',
      'departments_roles_table' => 'capl_departments_roles',
      'user_role_department' => 'capl_user_role_department',
      'users_settings_table' => 'capl_user_settings',
      'database_backups_table' => 'capl_database_backups',
      'user_todos_table' => 'capl_user_todos',
      'todo_user_pivot' => 'capl_todo_user_pivot',
      'announcements_table' => 'capl_announcements',
      'holiday_table' => 'capl_holidays',
      'meetings' => 'capl_meetings',
      'meeting_members' => 'capl_meeting_members',
      'teams' => 'capl_teams',
      'teams_members' => 'capl_teams_members',
      'email_group_table' => 'capl_email_template_groups',
      'email_template_table' => 'capl_email_template',
      'user_activities_table' => 'capl_user_activities',
      'email_inbox_table' => 'capl_email_inbox',
      'inbox_attachment_table' => 'capl_email_inbox_attachment',
      'folder_table' => 'capl_folders',
      'files_table' => 'capl_files',
      'menu_table' => 'capl_menus',
      'department_role_menu_table' => 'capl_department_role_menu',
      'countries_table' => 'capl_countries',
      'languages_table' => 'capl_languages',
      'locales_table' => 'capl_locales',
      'currency' => 'capl_currency',
      'clients_table' => 'capl_clients',
      'form_table' => 'capl_form',
      'custom_fields' => 'capl_custom_fields',
      'projects_table' => 'capl_projects',
      'project_user_table' => 'capl_project_user',
      'project_comments_table' => 'capl_project_comments',
      'project_attachments_table' => 'capl_project_attachments',
      'task_table' => 'capl_tasks',
      'task_user_table' => 'capl_task_user',
      'task_comments_table' => 'capl_task_comments',
      'task_attachments_table' => 'capl_task_attachments',
      'defects_table' => 'capl_defects',
      'defects_user_table' => 'capl_defect_user',
      'defects_history_table' => 'capl_defects_history',
      'defect_comments_table' => 'capl_defect_comments',
      'defect_attachments_table' => 'capl_defect_attachments',
      'incidents_table' => 'capl_incidents',
      'incident_user_table' => 'capl_incident_user',
      'incident_history_table' => 'capl_incident_history',
      'incident_attachments_table' => 'capl_incident_attachments',
      'incident_comments_table' => 'capl_incident_comments',
      'project_sprints_table' => 'capl_project_sprints',
      'project_sprint_members_table' => 'capl_project_sprint_members',
      'project_sprint_tasks_table' => 'capl_project_sprint_tasks',
      'project_sprint_task_members_table' => 'capl_project_sprint_task_members',
      'knowledge_base_category_table' => 'capl_knowledge_base_category',
      'knowledge_base_article_table' => 'capl_knowledge_base_article',
      'user' => 'Modules\\User\\Models\\User\\User',
      'role' => 'Modules\\User\\Models\\Role\\Role',
      'department' => 'Modules\\User\\Models\\Department\\Department',
      'user_todo' => 'Modules\\ToDo\\Models\\ToDo',
      'meeting_model' => 'Modules\\Meeting\\Models\\Meeting',
      'user_setting_model' => 'Modules\\Setting\\Models\\Setting',
      'announcement' => 'Modules\\Announcement\\Models\\Announcement',
      'user_activity' => 'Modules\\UserActivity\\Models\\UserActivity',
      'mailbox' => 'Modules\\Mailbox\\Models\\Mailbox',
      'mailbox_attachment' => 'Modules\\Mailbox\\Models\\MailboxAttachment',
      'file' => 'Modules\\FileBrowser\\Models\\File',
      'file_browser' => 'Modules\\FileBrowser\\Models\\FileBrowser',
      'email_template' => 'Modules\\EmailTemplate\\Models\\EmailTemplate',
      'clients_model' => 'Modules\\Client\\Models\\Client',
      'team_model' => 'Modules\\Team\\Models\\Team',
      'form' => 'Modules\\CustomField\\Models\\Form',
      'custom_field' => 'Modules\\CustomField\\Models\\CustomField',
      'project_model' => 'Modules\\Projects\\Models\\Project',
      'project_comment_model' => 'Modules\\ProjectComment\\Models\\ProjectComment',
      'project_attachment_model' => 'Modules\\ProjectAttachment\\Models\\ProjectAttachment',
      'task_model' => 'Modules\\Task\\Models\\Task',
      'task_comment_model' => 'Modules\\TaskComment\\Models\\TaskComment',
      'task_attachment_model' => 'Modules\\TaskAttachment\\Models\\TaskAttachment',
      'defects_model' => 'Modules\\Defect\\Models\\Defect',
      'defects_history_model' => 'Modules\\Defect\\Models\\DefectHistory',
      'defect_comment_model' => 'Modules\\DefectComment\\Models\\DefectComment',
      'defect_attachment_model' => 'Modules\\DefectAttachment\\Models\\DefectAttachment',
      'project_planner_sprint_model' => 'Modules\\ProjectPlannerSprint\\Models\\ProjectPlannerSprint',
      'project_planner_sprint_task_model' => 'Modules\\ProjectSprintTask\\Models\\ProjectSprintTask',
      'incidents_model' => 'Modules\\Incident\\Models\\Incident',
      'incident_history_model' => 'Modules\\Incident\\Models\\IncidentHistory',
      'incident_comment_model' => 'Modules\\IncidentComment\\Models\\IncidentComment',
      'incident_attachment_model' => 'Modules\\IncidentAttachment\\Models\\IncidentAttachment',
      'knowledge_base_category_model' => 'Modules\\KnowledgeBaseCategory\\Models\\KnowledgeBaseCategory',
      'knowledge_base_article_model' => 'Modules\\KnowledgeBaseArticle\\Models\\KnowledgeBaseArticle',
    ),
    'EMAIL_ACTIVATION_EXPIRE' => 172800,
    'FORGOT_PASSWORD_EXPIRE' => 86400,
    'COMPANY_NAME' => 'BLAHBLAHPM',
    'support_mail_id' => 'post@abteilung83.net',
    'max_length' => 255,
  ),
  'database' => 
  array (
    'default' => 'mysql',
    'connections' => 
    array (
      'sqlite' => 
      array (
        'driver' => 'sqlite',
        'database' => 'j0555_calapp',
        'prefix' => '',
        'foreign_key_constraints' => true,
      ),
      'mysql' => 
      array (
        'driver' => 'mysql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'j0555_calapp',
        'username' => 'j0555_ucalapp',
        'password' => 'bwikFLc2AU17',
        'unix_socket' => '',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'prefix_indexes' => true,
        'strict' => true,
        'engine' => NULL,
      ),
      'pgsql' => 
      array (
        'driver' => 'pgsql',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'j0555_calapp',
        'username' => 'j0555_ucalapp',
        'password' => 'bwikFLc2AU17',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
        'schema' => 'public',
        'sslmode' => 'prefer',
      ),
      'sqlsrv' => 
      array (
        'driver' => 'sqlsrv',
        'host' => '127.0.0.1',
        'port' => '3306',
        'database' => 'j0555_calapp',
        'username' => 'j0555_ucalapp',
        'password' => 'bwikFLc2AU17',
        'charset' => 'utf8',
        'prefix' => '',
        'prefix_indexes' => true,
      ),
    ),
    'migrations' => 'migrations',
    'redis' => 
    array (
      'client' => 'predis',
      'default' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 0,
      ),
      'cache' => 
      array (
        'host' => '127.0.0.1',
        'password' => NULL,
        'port' => '6379',
        'database' => 1,
      ),
    ),
  ),
  'filesystems' => 
  array (
    'default' => 'local',
    'cloud' => 's3',
    'disks' => 
    array (
      'local' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/storage/app',
      ),
      'public' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/storage/app/public',
        'visibility' => 'public',
      ),
      's3' => 
      array (
        'driver' => 's3',
        'key' => NULL,
        'secret' => NULL,
        'region' => NULL,
        'bucket' => NULL,
        'url' => NULL,
      ),
      'user_avtar' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/profile',
      ),
      'login_bg' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/login_bg',
      ),
      'company_logo' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/company_logo',
      ),
      'company_sidebar_logo' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/company_sidebar_logo',
      ),
      'sidebar_background_images' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/sidebar_background_images',
      ),
      'project_uploads' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/project',
      ),
      'project_attachment' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/project_attachment',
      ),
      'task_attachment' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/task_attachment',
      ),
      'defect' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/defect',
      ),
      'defect_attachment' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/defect_attachment',
      ),
      'incident_attachment' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/incident_attachment',
      ),
      'category_attachment' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/category_attachment',
      ),
      'article_attachment' => 
      array (
        'driver' => 'local',
        'root' => '/home/j0555/public_html/calapp/public/uploads/article_attachment',
      ),
    ),
  ),
  'hashing' => 
  array (
    'driver' => 'bcrypt',
    'bcrypt' => 
    array (
      'rounds' => 10,
    ),
    'argon' => 
    array (
      'memory' => 1024,
      'threads' => 2,
      'time' => 2,
    ),
  ),
  'jwt' => 
  array (
    'secret' => 'yuzzhRrcg5NvjhPwXtCBjhVilwUczN0MBkQJyTU3IgbwaXMnDH24erackLZmUQUL',
    'keys' => 
    array (
      'public' => NULL,
      'private' => NULL,
      'passphrase' => NULL,
    ),
    'ttl' => 120,
    'refresh_ttl' => 20160,
    'algo' => 'HS256',
    'required_claims' => 
    array (
      0 => 'iss',
      1 => 'iat',
      2 => 'exp',
      3 => 'nbf',
      4 => 'sub',
      5 => 'jti',
    ),
    'persistent_claims' => 
    array (
    ),
    'lock_subject' => true,
    'leeway' => 0,
    'blacklist_enabled' => true,
    'blacklist_grace_period' => 0,
    'decrypt_cookies' => false,
    'providers' => 
    array (
      'jwt' => 'Tymon\\JWTAuth\\Providers\\JWT\\Lcobucci',
      'auth' => 'Tymon\\JWTAuth\\Providers\\Auth\\Illuminate',
      'storage' => 'Tymon\\JWTAuth\\Providers\\Storage\\Illuminate',
    ),
  ),
  'laraupdater' => 
  array (
    'tmp_path' => 'C:/tmp',
    'update_baseurl' => 'http://chetsapp.de:8886',
    'middleware' => 
    array (
      0 => 'web',
    ),
    'allow_users_id' => 
    array (
      0 => 1,
    ),
    'update_url' => 'http://adminpanel.sw/api/verify/purchase_code',
  ),
  'logging' => 
  array (
    'default' => 'stack',
    'channels' => 
    array (
      'stack' => 
      array (
        'driver' => 'stack',
        'channels' => 
        array (
          0 => 'daily',
        ),
      ),
      'single' => 
      array (
        'driver' => 'single',
        'path' => '/home/j0555/public_html/calapp/storage/logs/laravel.log',
        'level' => 'debug',
      ),
      'daily' => 
      array (
        'driver' => 'daily',
        'path' => '/home/j0555/public_html/calapp/storage/logs/laravel.log',
        'level' => 'debug',
        'days' => 14,
      ),
      'slack' => 
      array (
        'driver' => 'slack',
        'url' => NULL,
        'username' => 'Laravel Log',
        'emoji' => ':boom:',
        'level' => 'critical',
      ),
      'papertrail' => 
      array (
        'driver' => 'monolog',
        'level' => 'debug',
        'handler' => 'Monolog\\Handler\\SyslogUdpHandler',
        'handler_with' => 
        array (
          'host' => NULL,
          'port' => NULL,
        ),
      ),
      'stderr' => 
      array (
        'driver' => 'monolog',
        'handler' => 'Monolog\\Handler\\StreamHandler',
        'formatter' => NULL,
        'with' => 
        array (
          'stream' => 'php://stderr',
        ),
      ),
      'syslog' => 
      array (
        'driver' => 'syslog',
        'level' => 'debug',
      ),
      'errorlog' => 
      array (
        'driver' => 'errorlog',
        'level' => 'debug',
      ),
    ),
  ),
  'mail' => 
  array (
    'driver' => 'smtp',
    'host' => 'sternenbasis11.abteilung83.net',
    'port' => '465',
    'from' => 
    array (
      'address' => 'booking@blahblah.zone',
      'name' => 'BLAHBLAHPM',
    ),
    'encryption' => 'ssl',
    'username' => 'booking@blahblah.zone',
    'password' => 'ZFVoMs4hlth2',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'markdown' => 
    array (
      'theme' => 'default',
      'paths' => 
      array (
        0 => '/home/j0555/public_html/calapp/resources/views/vendor/mail',
      ),
    ),
    'log_channel' => NULL,
  ),
  'modules' => 
  array (
    'namespace' => 'Modules',
    'stubs' => 
    array (
      'enabled' => false,
      'path' => '/home/j0555/public_html/calapp/vendor/nwidart/laravel-modules/src/Commands/stubs',
      'files' => 
      array (
        'routes/web' => 'Routes/web.php',
        'routes/api' => 'Routes/api.php',
        'scaffold/config' => 'Config/config.php',
        'composer' => 'composer.json',
      ),
      'replacements' => 
      array (
        'routes/web' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'STUDLY_NAME',
        ),
        'routes/api' => 
        array (
          0 => 'LOWER_NAME',
        ),
        'json' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'STUDLY_NAME',
          2 => 'MODULE_NAMESPACE',
        ),
        'scaffold/config' => 
        array (
          0 => 'STUDLY_NAME',
        ),
        'composer' => 
        array (
          0 => 'LOWER_NAME',
          1 => 'STUDLY_NAME',
          2 => 'VENDOR',
          3 => 'AUTHOR_NAME',
          4 => 'AUTHOR_EMAIL',
          5 => 'MODULE_NAMESPACE',
        ),
      ),
      'gitkeep' => true,
    ),
    'paths' => 
    array (
      'modules' => '/home/j0555/public_html/calapp/Modules',
      'assets' => '/home/j0555/public_html/calapp/public/modules',
      'migration' => '/home/j0555/public_html/calapp/database/migrations',
      'generator' => 
      array (
        'config' => 
        array (
          'path' => 'Config',
          'generate' => false,
        ),
        'command' => 
        array (
          'path' => 'Console',
          'generate' => false,
        ),
        'migration' => 
        array (
          'path' => 'Database/Migrations',
          'generate' => true,
        ),
        'seeder' => 
        array (
          'path' => 'Database/Seeders',
          'generate' => true,
        ),
        'factory' => 
        array (
          'path' => 'Database/factories',
          'generate' => true,
        ),
        'model' => 
        array (
          'path' => 'Models',
          'generate' => true,
        ),
        'controller' => 
        array (
          'path' => 'Http/Controllers',
          'generate' => true,
        ),
        'filter' => 
        array (
          'path' => 'Http/Middleware',
          'generate' => true,
        ),
        'request' => 
        array (
          'path' => 'Http/Requests',
          'generate' => true,
        ),
        'provider' => 
        array (
          'path' => 'Providers',
          'generate' => true,
        ),
        'assets' => 
        array (
          'path' => 'Resources/assets',
          'generate' => false,
        ),
        'lang' => 
        array (
          'path' => 'Resources/lang',
          'generate' => false,
        ),
        'views' => 
        array (
          'path' => 'Resources/views',
          'generate' => false,
        ),
        'test' => 
        array (
          'path' => 'Tests',
          'generate' => true,
        ),
        'repository' => 
        array (
          'path' => 'Repositories',
          'generate' => true,
        ),
        'event' => 
        array (
          'path' => 'Events',
          'generate' => false,
        ),
        'listener' => 
        array (
          'path' => 'Listeners',
          'generate' => false,
        ),
        'policies' => 
        array (
          'path' => 'Policies',
          'generate' => false,
        ),
        'rules' => 
        array (
          'path' => 'Rules',
          'generate' => true,
        ),
        'jobs' => 
        array (
          'path' => 'Jobs',
          'generate' => false,
        ),
        'emails' => 
        array (
          'path' => 'Emails',
          'generate' => false,
        ),
        'notifications' => 
        array (
          'path' => 'Notifications',
          'generate' => false,
        ),
        'resource' => 
        array (
          'path' => 'Transformers',
          'generate' => false,
        ),
      ),
    ),
    'scan' => 
    array (
      'enabled' => false,
      'paths' => 
      array (
        0 => '/home/j0555/public_html/calapp/vendor/*/*',
      ),
    ),
    'composer' => 
    array (
      'vendor' => 'vipspatel',
      'author' => 
      array (
        'name' => 'Vips Patel',
        'email' => 'info@chetsapp.com',
      ),
    ),
    'cache' => 
    array (
      'enabled' => false,
      'key' => 'laravel-modules',
      'lifetime' => 60,
    ),
    'register' => 
    array (
      'translations' => true,
      'files' => 'register',
    ),
  ),
  'queue' => 
  array (
    'default' => 'database',
    'connections' => 
    array (
      'sync' => 
      array (
        'driver' => 'sync',
      ),
      'database' => 
      array (
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'beanstalkd' => 
      array (
        'driver' => 'beanstalkd',
        'host' => 'localhost',
        'queue' => 'default',
        'retry_after' => 90,
      ),
      'sqs' => 
      array (
        'driver' => 'sqs',
        'key' => 'your-public-key',
        'secret' => 'your-secret-key',
        'prefix' => 'https://sqs.us-east-1.amazonaws.com/your-account-id',
        'queue' => 'your-queue-name',
        'region' => 'us-east-1',
      ),
      'redis' => 
      array (
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
        'block_for' => NULL,
      ),
    ),
    'failed' => 
    array (
      'database' => 'mysql',
      'table' => 'failed_jobs',
    ),
  ),
  'responsecache' => 
  array (
    'enabled' => false,
    'cache_profile' => 'Spatie\\ResponseCache\\CacheProfiles\\CacheAllSuccessfulGetRequests',
    'cache_lifetime_in_minutes' => 10080,
    'add_cache_time_header' => false,
    'cache_store' => 'file',
    'cache_tag' => '',
  ),
  'services' => 
  array (
    'mailgun' => 
    array (
      'domain' => '',
      'secret' => '',
      'endpoint' => 'api.mailgun.net',
    ),
    'ses' => 
    array (
      'key' => NULL,
      'secret' => NULL,
      'region' => 'us-east-1',
    ),
    'sparkpost' => 
    array (
      'secret' => '',
    ),
    'mandrill' => 
    array (
      'secret' => '',
    ),
    'stripe' => 
    array (
      'model' => 'App\\User',
      'key' => NULL,
      'secret' => NULL,
      'webhook' => 
      array (
        'secret' => NULL,
        'tolerance' => 300,
      ),
    ),
  ),
  'session' => 
  array (
    'driver' => 'file',
    'lifetime' => '120',
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => '/home/j0555/public_html/calapp/storage/framework/sessions',
    'connection' => NULL,
    'table' => 'sessions',
    'store' => NULL,
    'lottery' => 
    array (
      0 => 2,
      1 => 100,
    ),
    'cookie' => 'blahblahpm_session',
    'path' => '/',
    'domain' => NULL,
    'secure' => false,
    'http_only' => true,
    'same_site' => NULL,
  ),
  'view' => 
  array (
    'paths' => 
    array (
      0 => '/home/j0555/public_html/calapp/resources/views',
    ),
    'compiled' => '/home/j0555/public_html/calapp/storage/framework/views',
  ),
  'debug-server' => 
  array (
    'host' => 'tcp://127.0.0.1:9912',
  ),
  'trustedproxy' => 
  array (
    'proxies' => NULL,
    'headers' => 30,
  ),
  'announcement' => 
  array (
    'name' => 'Announcement',
  ),
  'mailbox' => 
  array (
    'name' => 'Mailbox',
  ),
  'user' => 
  array (
    'name' => 'User',
  ),
  'todo' => 
  array (
    'name' => 'ToDo',
  ),
  'team' => 
  array (
    'name' => 'Team',
  ),
  'taskcomment' => 
  array (
    'name' => 'TaskComment',
  ),
  'taskattachment' => 
  array (
    'name' => 'TaskAttachment',
  ),
  'task' => 
  array (
    'name' => 'Task',
  ),
  'setting' => 
  array (
    'name' => 'Setting',
  ),
  'projects' => 
  array (
    'name' => 'Projects',
  ),
  'projectsprinttask' => 
  array (
    'name' => 'ProjectSprintTask',
  ),
  'projectplannersprint' => 
  array (
    'name' => 'ProjectPlannerSprint',
  ),
  'projectcomment' => 
  array (
    'name' => 'ProjectComment',
  ),
  'projectattachment' => 
  array (
    'name' => 'ProjectAttachment',
  ),
  'menu' => 
  array (
    'name' => 'Menu',
  ),
  'meeting' => 
  array (
    'name' => 'Meeting',
  ),
  'knowledgebasecategory' => 
  array (
    'name' => 'KnowledgeBaseCategory',
  ),
  'client' => 
  array (
    'name' => 'Client',
  ),
  'emailtemplate' => 
  array (
    'name' => 'EmailTemplate',
  ),
  'customfield' => 
  array (
    'name' => 'CustomField',
  ),
  'databasebackup' => 
  array (
    'name' => 'DatabaseBackup',
  ),
  'defect' => 
  array (
    'name' => 'Defect',
  ),
  'defectattachment' => 
  array (
    'name' => 'DefectAttachment',
  ),
  'defectcomment' => 
  array (
    'name' => 'DefectComment',
  ),
  'emailgroup' => 
  array (
    'name' => 'EmailGroup',
  ),
  'filebrowser' => 
  array (
    'name' => 'FileBrowser',
  ),
  'knowledgebasearticle' => 
  array (
    'name' => 'KnowledgeBaseArticle',
  ),
  'helper' => 
  array (
    'name' => 'Helper',
  ),
  'holiday' => 
  array (
    'name' => 'Holiday',
  ),
  'incident' => 
  array (
    'name' => 'Incident',
  ),
  'incidentattachment' => 
  array (
    'name' => 'IncidentAttachment',
  ),
  'incidentcomment' => 
  array (
    'name' => 'IncidentComment',
  ),
  'installer' => 
  array (
    'name' => 'Installer',
  ),
  'useractivity' => 
  array (
    'name' => 'UserActivity',
  ),
  'tinker' => 
  array (
    'commands' => 
    array (
    ),
    'dont_alias' => 
    array (
      0 => 'App\\Nova',
    ),
  ),
);
