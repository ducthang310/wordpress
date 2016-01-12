<?php

function dtdf_install() {
    global $wpdb, $dtdf_table;

    if($wpdb->get_var("SHOW TABLES LIKE '$dtdf_table'") != $dtdf_table) {
        $sql = "CREATE TABLE IF NOT EXISTS $dtdf_table (
		`id` mediumint(9) NOT NULL AUTO_INCREMENT,
		`test_field_1` text NULL,
		`test_field_2` text NULL,
		`test_field_3` text NULL,
		`test_field_4` text NULL,
		`created_date` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
		UNIQUE KEY id (id)
		);";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

dtdf_install();