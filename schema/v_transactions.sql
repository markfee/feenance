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
    0.01 * balance.balance balance
  FROM
    transactions transaction
    LEFT JOIN balances balance
      ON transaction.id = balance.transaction_id
    LEFT JOIN accounts account
      ON transaction.account_id = account.id
  ORDER BY transaction.date DESC;
