use feenance;
DROP VIEW IF EXISTS v_transactions_by_month;
CREATE VIEW v_transactions_by_month AS
  SELECT
    YEAR(transaction.date) AS year,
    MONTH(transaction.date) AS month,
    12 * YEAR(transaction.date) + MONTH(transaction.date) month_num,
    SUM(transaction.credit) credit,
    SUM(transaction.debit) debit,
    SUM(transaction.movement) movement
  FROM
    v_non_transfers transaction
  GROUP BY year , month
  ORDER BY year , month;

SELECT
    *
FROM
    v_transactions_by_month
WHERE year > 2014
ORDER BY year , month;