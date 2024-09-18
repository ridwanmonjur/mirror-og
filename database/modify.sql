-- ALTER TABLE old_table_name 
-- RENAME TO new_table_name;

ALTER TABLE payment_transactions 
RENAME TO payment_transactions;

-- ALTER TABLE child_table
-- ADD CONSTRAINT fk_parent
-- FOREIGN KEY (child_id) REFERENCES parent_table(id)
-- ON DELETE CASCADE;

-- ALTER TABLE participant_payments
-- ADD CONSTRAINT payment_transactions
-- FOREIGN KEY (payment_id) REFERENCES payment_transactions(id)
-- ON DELETE CASCADE;

ALTER TABLE table_name
DROP COLUMN payment_request_id;

ALTER TABLE payment_transactions 
RENAME COLUMN discount_id TO system_discount_id;

ALTER TABLE payment_transactions 
ADD COLUMN user_discount_id BIGINT UNSIGNED;

ALTER TABLE payment_transactions 
ADD CONSTRAINT fk_user_discount 
FOREIGN KEY (user_discount_id) REFERENCES user_discounts(id);