CREATE TRIGGER `company_address_create`
AFTER INSERT ON `company_addresses` FOR EACH ROW
INSERT INTO `company_addresses_history`
SET `action` = 'INSERT',
    `company_address_id` = NEW.id,
    `code` = NEW.code,
    `name` = NEW.name,
    `is_active` = NEW.is_active,
    `transaction_at` = NOW(),
    `transaction_by` = NEW.created_by;

DELIMITER $$ 
CREATE TRIGGER `company_address_update`
AFTER UPDATE ON `company_addresses` FOR EACH ROW
BEGIN
    IF  NEW.code != OLD.code OR  NEW.name != OLD.name OR  NEW.is_active != OLD.is_active THEN
        INSERT INTO `company_addresses_history`
            SET `action` = 'UPDATE',
                `company_address_id` = NEW.id,
                `code` = NEW.code,
                `name` = NEW.name,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    ELSEIF NEW.deleted_at IS NOT NULL THEN
        INSERT INTO `company_addresses_history`
            SET `action` = 'DELETE',
                `company_address_id` = NEW.id,
                `code` = NEW.code,
                `name` = NEW.name,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    END IF;
END$$
DELIMITER ;
