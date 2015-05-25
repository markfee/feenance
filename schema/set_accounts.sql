use feenance;
SELECT * FROM account_types;
UPDATE accounts SET account_type_id = 3 WHERE id IN (21, 23);
UPDATE accounts SET account_type_id = 4 WHERE id IN (11, 12, 17);
UPDATE accounts SET account_type_id = 6 WHERE id IN (10, 22);
UPDATE accounts SET account_type_id = 2 WHERE id IN (3, 4, 5, 6, 7, 8, 9, 18, 19, 20);
SELECT * FROM v_transactions where is_asset = 1 limit 10;