use feenance;

DROP VIEW IF EXISTS v_accounts;

CREATE VIEW v_accounts AS
  SELECT
    account.id,
    account.name,
    CONCAT( COALESCE(CONCAT(NULLIF(account.sort_code, ''), ': '), ''), COALESCE(account.acc_number, '') )  acc_number,
    account.open,
    account.opening_balance * 0.01 opening_balance,
    account_type.name account_type,
    account_type.is_asset,
    account_type.is_loan
  FROM accounts account
  JOIN account_types account_type
  ON account.account_type_id = account_type.id
;

DROP VIEW IF EXISTS v_current_accounts;

CREATE VIEW v_current_accounts as
  SELECT * FROM v_accounts WHERE open = 1
;