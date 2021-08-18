CREATE TRIGGER `company_create`
AFTER INSERT ON `companies` FOR EACH ROW
INSERT INTO `companies_history`
SET `action` = 'INSERT',
    `company_id` = NEW.id,
    `logo` = NEW.logo,
    `code` = NEW.code,
    `name` = NEW.name,
    `is_active` = NEW.is_active,
    `transaction_at` = NOW(),
    `transaction_by` = NEW.created_by;

DELIMITER $$ 
CREATE TRIGGER `company_update`
AFTER UPDATE ON `companies` FOR EACH ROW
BEGIN
    IF  NEW.code != OLD.code OR  NEW.name != OLD.name OR  NEW.is_active != OLD.is_active OR NEW.logo != OLD.logo THEN
        INSERT INTO `companies_history`
            SET `action` = 'UPDATE',
                `company_id` = NEW.id,
                `code` = NEW.code,
                `name` = NEW.name,
                `logo` = NEW.logo,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    ELSEIF NEW.deleted_at IS NOT NULL THEN
        INSERT INTO `companies_history`
            SET `action` = 'DELETE',
                `company_id` = NEW.id,
                `code` = NEW.code,
                `name` = NEW.name,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    END IF;
END$$
DELIMITER ;
