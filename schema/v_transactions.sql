use feenance;

DROP VIEW IF EXISTS v_transactions;

CREATE VIEW v_transactions AS
  SELECT
    transaction.id,
    date_format(transaction.date,'%Y-%m-%d') AS date,
    transaction.batch_id,
    transaction.account_id,
    transaction.payee_id,
    COALESCE(transaction.category_id, account.category_id) category_id,
    if((transaction.amount <= 0),NULL,(+0.01 * transaction.amount)) AS `credit`,
    if((transaction.amount >= 0),NULL,(-0.01 * transaction.amount)) AS `debit`,
    0.01 * transaction.amount movement,
    0.01 * balance.balance balance,
    account.is_loan,
    account.is_asset
  FROM
    transactions transaction
    LEFT JOIN balances balance
      ON transaction.id = balance.transaction_id
    LEFT JOIN v_accounts account
      ON transaction.account_id = account.id
--    LEFT JOIN account_types account_type
--      ON account.account_type_id = account_type.id
  ORDER BY transaction.date DESC;
