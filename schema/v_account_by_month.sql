DROP VIEW IF EXISTS v_account_by_month;
CREATE VIEW v_account_by_month AS
SELECT
	account.account_id,
	MONTH(transaction.date) AS month,
	YEAR(transaction.date) AS year,
	account.carried_forward - SUM(movement) bought_forward,
	SUM(transaction.credit) credit,
	SUM(transaction.debit) debit,
	SUM(transaction.movement) movement,
	account.carried_forward
FROM 	v_minmax_transaction_by_month account
JOIN 	v_transactions transaction
	ON account.account_id = transaction.account_id
	AND account.year 	= YEAR(transaction.date)
	AND account.month 	= MONTH(transaction.date)
GROUP BY account_id, year, month
ORDER BY account_id, year, month;

SELECT * FROM v_account_by_month;