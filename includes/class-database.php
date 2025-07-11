<?php
/**
 * Database Management Class
 * Handles all database operations for the plugin
 */

if (!defined('ABSPATH')) {
    exit;
}

class GSBA_Database {
    
    private $wpdb;
    private $business_profiles_table;
    private $templates_table;
    private $generated_pages_table;
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->business_profiles_table = $wpdb->prefix . 'gsba_business_profiles';
        $this->templates_table = $wpdb->prefix . 'gsba_templates';
        $this->generated_pages_table = $wpdb->prefix . 'gsba_generated_pages';
    }
    
    public function init() {
        // Check if database needs update
        $installed_version = get_option('gsba_db_version');
        if ($installed_version != GSBA_DB_VERSION) {
            $this->create_tables();
            update_option('gsba_db_version', GSBA_DB_VERSION);
        }
    }
    
    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();
        
        // Business Profiles Table
        $sql_profiles = "CREATE TABLE {$this->business_profiles_table} (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            business_name varchar(255) NOT NULL,
            business_type varchar(100) NOT NULL,
            description text NOT NULL,
            logo_url varchar(500) DEFAULT '',
            primary_color varchar(7) DEFAULT '#667eea',
            secondary_color varchar(7) DEFAULT '#764ba2',
            website_url varchar(500) DEFAULT '',
            phone varchar(50) DEFAULT '',
            email varchar(100) DEFAULT '',
            address text DEFAULT '',
            tagline varchar(255) DEFAULT '',
            social_media longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        
        // Templates Table
        $sql_templates = "CREATE TABLE {$this->templates_table} (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            description text DEFAULT '',
            preview_image varchar(500) DEFAULT '',
            block_structure longtext NOT NULL,
            seo_config longtext DEFAULT '',
            created_by bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY type (type),
            KEY status (status)
        ) $charset_collate;";
        
        // Generated Pages Table
        $sql_pages = "CREATE TABLE {$this->generated_pages_table} (
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            business_profile_id int(11) NOT NULL,
            template_id int(11) NOT NULL,
            page_id bigint(20) NOT NULL,
            generated_content longtext NOT NULL,
            ai_model_used varchar(50) DEFAULT '',
            seo_data longtext DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY page_id (page_id),
            KEY template_id (template_id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_profiles);
        dbDelta($sql_templates);
        dbDelta($sql_pages);
    }
    
    // Business Profile Methods
    public function save_business_profile($user_id, $data) {
        $existing = $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT id FROM {$this->business_profiles_table} WHERE user_id = %d AND business_name = %s",
            $user_id, $data['business_name']
        ));
        
        $profile_data = array(
            'user_id' => $user_id,
            'business_name' => $data['business_name'],
            'business_type' => $data['business_type'],
            'description' => $data['description'],
            'logo_url' => $data['logo_url'],
            'primary_color' => $data['primary_color'] ?: '#667eea',
            'secondary_color' => $data['secondary_color'] ?: '#764ba2',
            'website_url' => $data['website_url'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'address' => $data['address'],
            'tagline' => $data['tagline'],
            'social_media' => wp_json_encode($data['social_media'])
        );
        
        if ($existing) {
            $this->wpdb->update(
                $this->business_profiles_table,
                $profile_data,
                array('id' => $existing->id),
                array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
            return $existing->id;
        } else {
            $this->wpdb->insert(
                $this->business_profiles_table,
                $profile_data,
                array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
            );
            return $this->wpdb->insert_id;
        }
    }
    
    public function get_business_profile($profile_id) {
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->business_profiles_table} WHERE id = %d",
            $profile_id
        ));
    }
    
    public function get_user_business_profiles($user_id, $limit = 10) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT * FROM {$this->business_profiles_table} WHERE user_id = %d ORDER BY updated_at DESC LIMIT %d",
            $user_id, $limit
        ));
    }
    
    // Template Methods
    public function save_template($data) {
        $template_data = array(
            'name' => $data['name'],
            'type' => $data['type'],
            'description' => $data['description'],
            'preview_image' => $data['preview_image'],
            'block_structure' => $data['block_structure'],
            'seo_config' => $data['seo_config'],
            'created_by' => $data['created_by'],
            'status' => $data['status'] ?: 'active'
        );
        
        if (isset($data['id']) && $data['id']) {
            $this->wpdb->update(
                $this->templates_table,
                $template_data,
                array('id' => $data['id']),
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s'),
                array('%d')
            );
            return $data['id'];
        } else {
            $this->wpdb->insert(
                $this->templates_table,
                $template_data,
                array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s')
            );
            return $this->wpdb->insert_id;
        }
    }
    
    public function get_template($template_id) {
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT * FROM {$this->templates_table} WHERE id = %d AND status = 'active'",
            $template_id
        ));
    }
    
    public function get_all_templates($type = '') {
        $where = "WHERE status = 'active'";
        if ($type) {
            $where .= $this->wpdb->prepare(" AND type = %s", $type);
        }
        
        return $this->wpdb->get_results(
            "SELECT * FROM {$this->templates_table} {$where} ORDER BY created_at DESC"
        );
    }
    
    public function delete_template($template_id) {
        return $this->wpdb->update(
            $this->templates_table,
            array('status' => 'deleted'),
            array('id' => $template_id),
            array('%s'),
            array('%d')
        );
    }
    
    // Generated Pages Methods
    public function save_generated_page($user_id, $profile_id, $template_id, $page_id, $generated_content, $ai_model = '') {
        return $this->wpdb->insert(
            $this->generated_pages_table,
            array(
                'user_id' => $user_id,
                'business_profile_id' => $profile_id,
                'template_id' => $template_id,
                'page_id' => $page_id,
                'generated_content' => wp_json_encode($generated_content),
                'ai_model_used' => $ai_model
            ),
            array('%d', '%d', '%d', '%d', '%s', '%s')
        );
    }
    
    public function get_user_generated_pages($user_id, $limit = 20) {
        return $this->wpdb->get_results($this->wpdb->prepare(
            "SELECT gp.*, bp.business_name, t.name as template_name, t.type as template_type, p.post_title, p.post_status
             FROM {$this->generated_pages_table} gp
             LEFT JOIN {$this->business_profiles_table} bp ON gp.business_profile_id = bp.id
             LEFT JOIN {$this->templates_table} t ON gp.template_id = t.id
             LEFT JOIN {$this->wpdb->posts} p ON gp.page_id = p.ID
             WHERE gp.user_id = %d
             ORDER BY gp.created_at DESC
             LIMIT %d",
            $user_id, $limit
        ));
    }
    
    public function get_generated_page($page_id) {
        return $this->wpdb->get_row($this->wpdb->prepare(
            "SELECT gp.*, bp.business_name, t.name as template_name
             FROM {$this->generated_pages_table} gp
             LEFT JOIN {$this->business_profiles_table} bp ON gp.business_profile_id = bp.id
             LEFT JOIN {$this->templates_table} t ON gp.template_id = t.id
             WHERE gp.page_id = %d",
            $page_id
        ));
    }
    
    // Statistics Methods
    public function get_user_stats($user_id) {
        $profiles_count = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->business_profiles_table} WHERE user_id = %d",
            $user_id
        ));
        
        $pages_count = $this->wpdb->get_var($this->wpdb->prepare(
            "SELECT COUNT(*) FROM {$this->generated_pages_table} WHERE user_id = %d",
            $user_id
        ));
        
        $templates_count = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->templates_table} WHERE status = 'active'"
        );
        
        return array(
            'business_profiles' => $profiles_count,
            'generated_pages' => $pages_count,
            'available_templates' => $templates_count
        );
    }
    
    public function get_global_stats() {
        $total_profiles = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->business_profiles_table}"
        );
        
        $total_pages = $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->generated_pages_table}"
        );
        
        $active_users = $this->wpdb->get_var(
            "SELECT COUNT(DISTINCT user_id) FROM {$this->business_profiles_table}"
        );
        
        return array(
            'total_profiles' => $total_profiles,
            'total_pages' => $total_pages,
            'active_users' => $active_users
        );
    }
    
    // Cleanup Methods
    public function cleanup_orphaned_data() {
        // Remove generated page records for deleted WordPress posts
        $this->wpdb->query(
            "DELETE gp FROM {$this->generated_pages_table} gp
             LEFT JOIN {$this->wpdb->posts} p ON gp.page_id = p.ID
             WHERE p.ID IS NULL"
        );
        
        // Remove business profiles for deleted users
        $this->wpdb->query(
            "DELETE bp FROM {$this->business_profiles_table} bp
             LEFT JOIN {$this->wpdb->users} u ON bp.user_id = u.ID
             WHERE u.ID IS NULL"
        );
    }
}