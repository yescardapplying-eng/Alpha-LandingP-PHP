<?php
/**
 * Update MySQL columns to robust string types (VARCHAR / TEXT)
 */
require_once dirname(__DIR__) . '/config/database.php';

try {
    $pdo = getDBConnection();
    echo "Connected to database...\n";

    $alters = [
        // leads table
        "ALTER TABLE `leads` MODIFY COLUMN `status` VARCHAR(50) NOT NULL DEFAULT 'partial'",
        "ALTER TABLE `leads` MODIFY COLUMN `current_step` VARCHAR(20) NOT NULL DEFAULT '1'",
        "ALTER TABLE `leads` MODIFY COLUMN `challenge` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `full_name` VARCHAR(255) NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `mobile_number` VARCHAR(100) NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `trading_segment` VARCHAR(255) NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `trading_capital` VARCHAR(255) NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `whatsapp_number` VARCHAR(100) NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `state` VARCHAR(255) NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `utm_source` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `utm_medium` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `utm_campaign` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `utm_content` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `utm_term` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `fbclid` TEXT NULL",
        "ALTER TABLE `leads` MODIFY COLUMN `ip_address` VARCHAR(100) NULL",

        // assessment_interactions table
        "ALTER TABLE `assessment_interactions` MODIFY COLUMN `step_number` VARCHAR(20) NOT NULL DEFAULT '1'",
        "ALTER TABLE `assessment_interactions` MODIFY COLUMN `question` TEXT NOT NULL",
        "ALTER TABLE `assessment_interactions` MODIFY COLUMN `selected_answer` TEXT NOT NULL",
        "ALTER TABLE `assessment_interactions` MODIFY COLUMN `field_name` VARCHAR(100) NOT NULL",
        "ALTER TABLE `assessment_interactions` MODIFY COLUMN `action_type` VARCHAR(100) NOT NULL",
        "ALTER TABLE `assessment_interactions` MODIFY COLUMN `ip_address` VARCHAR(100) NULL"
    ];

    foreach ($alters as $sql) {
        echo "Executing: {$sql}\n";
        $pdo->exec($sql);
    }

    echo "\nAll columns successfully updated to flexible String (VARCHAR/TEXT) types!\n";

} catch (Exception $e) {
    echo "Error updating columns: " . $e->getMessage() . "\n";
    exit(1);
}
