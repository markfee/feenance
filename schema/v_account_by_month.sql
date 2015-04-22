DROP VIEW IF EXISTS v_account_by_month;
CREATE VIEW v_account_by_month AS
  SELECT
    account.account_id,
    YEAR(transaction.date) AS year,
    MONTH(transaction.date) AS month,
    12 * YEAR(transaction.date) + MONTH(transaction.date) month_num,
    account.carried_forward - SUM(movement) bought_forward,
    SUM(transaction.credit) credit,
    SUM(transaction.debit) debit,
    SUM(transaction.movement) movement,
    account.carried_forward
  FROM
    v_minmax_transaction_by_month account
    JOIN
    v_transactions transaction ON account.account_id = transaction.account_id
                                  AND account.year = YEAR(transaction.date)
                                  AND account.month = MONTH(transaction.date)
  GROUP BY account_id , year , month
  ORDER BY account_id , year , month;

SELECT
    *
FROM
    v_account_by_month
WHERE year > 2014
ORDER BY year , month, account_id;