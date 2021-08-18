CREATE TRIGGER `vendor_create`
AFTER INSERT ON `vendors` FOR EACH ROW
INSERT INTO `vendors_history`
SET `action` = 'INSERT',
    `vendor_id` = NEW.id,
    `name` = NEW.name,
    `trade_name` = NEW.trade_name,
    `sole_proprietor_owner` = NEW.sole_proprietor_owner,
    `address` = NEW.address,
    `tin_number` = NEW.tin_number,
    `taxpayer_classification` = NEW.taxpayer_classification,
    `withholding_tax_id` = NEW.withholding_tax_id,
    `withholding_tax_rate` = NEW.withholding_tax_rate,
    `terms` = NEW.terms,
    `bank_account` = NEW.bank_account,
    `bank_number` = NEW.bank_number,
    `email` = NEW.email,
    `contact_person` = NEW.contact_person,
    `contact_number` = NEW.contact_number,
    `is_active` = NEW.is_active,
    `transaction_at` = NOW(),
    `transaction_by` = NEW.created_by;

DELIMITER $$ 
CREATE TRIGGER `vendor_update`
AFTER UPDATE ON `vendors` FOR EACH ROW
BEGIN
    IF  NEW.name != OLD.name OR
        NEW.email != OLD.email OR
        NEW.address != OLD.address OR
        NEW.contact_person != OLD.contact_person OR
        NEW.contact_number != OLD.contact_number OR
        NEW.is_active != OLD.is_active 
    THEN
        INSERT INTO `vendors_history`
            SET `action` = 'UPDATE',
                `vendor_id` = NEW.id,
                `name` = NEW.name,
                `trade_name` = NEW.trade_name,
                `sole_proprietor_owner` = NEW.sole_proprietor_owner,
                `address` = NEW.address,
                `tin_number` = NEW.tin_number,
                `taxpayer_classification` = NEW.taxpayer_classification,
                `withholding_tax_id` = NEW.withholding_tax_id,
                `withholding_tax_rate` = NEW.withholding_tax_rate,
                `terms` = NEW.terms,
                `bank_account` = NEW.bank_account,
                `bank_number` = NEW.bank_number,
                `email` = NEW.email,
                `contact_person` = NEW.contact_person,
                `contact_number` = NEW.contact_number,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    ELSEIF NEW.deleted_at IS NOT NULL THEN
        INSERT INTO `vendors_history`
            SET `action` = 'DELETE',
                `vendor_id` = NEW.id,
                `name` = NEW.name,
                `trade_name` = NEW.trade_name,
                `sole_proprietor_owner` = NEW.sole_proprietor_owner,
                `address` = NEW.address,
                `tin_number` = NEW.tin_number,
                `taxpayer_classification` = NEW.taxpayer_classification,
                `withholding_tax_id` = NEW.withholding_tax_id,
                `withholding_tax_rate` = NEW.withholding_tax_rate,
                `terms` = NEW.terms,
                `bank_account` = NEW.bank_account,
                `bank_number` = NEW.bank_number,
                `email` = NEW.email,
                `contact_person` = NEW.contact_person,
                `contact_number` = NEW.contact_number,
                `is_active` = NEW.is_active,
                `transaction_at` = NOW(),
                `transaction_by` = NEW.updated_by;
    END IF;
END$$
DELIMITER ;
