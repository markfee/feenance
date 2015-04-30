use feenance;

DROP VIEW IF EXISTS v_accounts;

CREATE VIEW v_accounts AS
  SELECT
    id,
    name,
    CONCAT( COALESCE(CONCAT(NULLIF(sort_code, ''), ': '), ''), COALESCE(acc_number, '') )  acc_number,
    open,
    opening_balance * 0.01 opening_balance
  FROM accounts
;

DROP VIEW IF EXISTS v_current_accounts;

CREATE VIEW v_current_accounts as
  SELECT * FROM v_accounts WHERE open = 1
;