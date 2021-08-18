CREATE TRIGGER `company_contacts_create`
AFTER INSERT ON `company_contacts` FOR EACH ROW
INSERT INTO `company_contacts_history`
SET `action` = 'INSERT',
    `company_contact_id` = NEW.id,
    `name` = NEW.name,
    `email` = NEW.email,
    `mobile_number` = NEW.mobile_number,
    `phone_number` = NEW.phone_number,
    `is_active` = NEW.is_active,
    `transaction_at` = NOW(),
    `transaction_by` = NEW.created_by;

DELIMITER $$ 
CREATE TRIGGER `company_contacts_update`
AFTER UPDATE ON `company_contacts` FOR EACH ROW
BEGIN
    IF  NEW.name != OLD.name OR
        NEW.email != OLD.email OR
        NEW.mobile_number != OLD.mobile_number OR
        NEW.phone_number != OLD.phone_number OR
        NEW.is_active != OLD.is_active 
    THEN
        INSERT INTO `company_contacts_history`
            SET `action` = 'UPDATE',
                `company_contact_id` = NEW.id,
                `name` = NEW.name,
                `email` = NEW.email,
                `mobile_number` = NEW.mobile_number,
                `phone_number` = NEW.phone_number,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    ELSEIF NEW.deleted_at IS NOT NULL THEN
        INSERT INTO `company_contacts_history`
            SET `action` = 'DELETE',
                `company_contact_id` = NEW.id,
                `name` = NEW.name,
                `email` = NEW.email,
                `mobile_number` = NEW.mobile_number,
                `phone_number` = NEW.phone_number,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    END IF;
END$$
DELIMITER ;
