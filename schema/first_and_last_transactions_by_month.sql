use feenance;

DROP VIEW IF EXISTS v_minmax_transaction_date_by_month;
CREATE VIEW v_minmax_transaction_date_by_month AS 
SELECT DISTINCT account_id,
	YEAR(date) year, 
	MONTH(date) month, 
	MIN(DATE(date)) first_date, 
	MAX(DATE(date)) last_date
FROM transactions
GROUP BY account_id, year, month
;

DROP VIEW IF EXISTS v_minmax_transaction_by_day;
CREATE VIEW v_minmax_transaction_by_day AS 
	SELECT DISTINCT
	  MAX(id) last_transaction_id
	, MIN(id) first_transaction_id
	, account_id
	, DATE(date) date
FROM transactions
GROUP BY account_id, DATE(date);

DROP VIEW IF EXISTS v_minmax_transaction_by_month;
CREATE VIEW v_minmax_transaction_by_month AS 

SELECT DISTINCT
	v_minmax_transaction_date_by_month.account_id,
	v_minmax_transaction_date_by_month.year,
	v_minmax_transaction_date_by_month.month,
	v_minmax_transaction_date_by_month.first_date,
	first_transaction.first_transaction_id,
	v_minmax_transaction_date_by_month.last_date,
	last_transaction.last_transaction_id,
	0.01 * carried_forward.balance carried_forward
FROM v_minmax_transaction_date_by_month 
JOIN v_minmax_transaction_by_day first_transaction
	ON 	v_minmax_transaction_date_by_month.account_id = first_transaction.account_id
	AND v_minmax_transaction_date_by_month.first_date  = first_transaction.date
JOIN v_minmax_transaction_by_day last_transaction
	ON 	v_minmax_transaction_date_by_month.account_id = last_transaction.account_id
	AND v_minmax_transaction_date_by_month.last_date  = last_transaction.date
LEFT JOIN balances carried_forward
	ON carried_forward.transaction_id = last_transaction.last_transaction_id

ORDER BY account_id, year, month
;
	
SELECT * FROM v_minmax_transaction_by_month