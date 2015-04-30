use feenance;
DROP VIEW IF EXISTS v_transactions_by_month;
CREATE VIEW v_transactions_by_month AS
  SELECT
    FN_MY_YEAR(transaction.date) AS year,
    FN_MY_MONTH(transaction.date) AS month,
    12 * FN_MY_YEAR(transaction.date) + FN_MY_MONTH(transaction.date) month_num,
    SUM(transaction.credit) credit,
    SUM(transaction.debit) debit,
    SUM(transaction.movement) movement
  FROM
    v_non_transfers transaction
  GROUP BY year , month
  ORDER BY year DESC, month DESC;

SELECT
    *
FROM
    v_transactions_by_month
WHERE year > 2014
ORDER BY year , month;