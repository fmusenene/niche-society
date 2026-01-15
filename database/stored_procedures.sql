-- ========================================
-- STORED PROCEDURES FOR NICHE SOCIETY
-- ========================================
-- This file contains stored procedures that can be executed directly in phpMyAdmin
-- Execute each procedure separately or all at once
-- ========================================

USE niche_society;

-- ========================================
-- Procedure: Log User Activity
-- ========================================
-- Drops the procedure if it exists to avoid conflicts
DROP PROCEDURE IF EXISTS sp_log_activity;

-- Creates the procedure without DEFINER clause
CREATE PROCEDURE sp_log_activity(
    IN p_user_id INT,
    IN p_action VARCHAR(100),
    IN p_entity_type VARCHAR(50),
    IN p_entity_id INT,
    IN p_description TEXT,
    IN p_ip_address VARCHAR(45)
)
BEGIN
    INSERT INTO activity_log (user_id, action, entity_type, entity_id, description, ip_address)
    VALUES (p_user_id, p_action, p_entity_type, p_entity_id, p_description, p_ip_address);
END;

-- ========================================
-- Procedure: Get Service Statistics
-- ========================================
-- Drops the procedure if it exists to avoid conflicts
DROP PROCEDURE IF EXISTS sp_get_service_stats;

-- Creates the procedure without DEFINER clause
CREATE PROCEDURE sp_get_service_stats()
BEGIN
    SELECT 
        category,
        COUNT(*) as total,
        SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured_count,
        AVG(display_order) as avg_order
    FROM services
    WHERE status = 'active'
    GROUP BY category;
END;

-- ========================================
-- USAGE INSTRUCTIONS
-- ========================================
-- 1. Open phpMyAdmin
-- 2. Select the 'niche_society' database
-- 3. Go to the SQL tab
-- 4. Copy and paste the entire contents of this file
-- 5. Click "Go" to execute
-- 
-- OR execute each procedure separately:
-- - Copy only the DROP and CREATE statements for sp_log_activity
-- - Execute, then copy and execute sp_get_service_stats
-- ========================================
