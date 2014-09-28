use feenance;
DROP VIEW IF EXISTS v_category_by_month;
CREATE VIEW v_category_by_month as
SELECT DATE_FORMAT(transaction.date, "%Y-%m") month, 
transaction.category_id, 
category.parent_id, 
IFNULL(parentCategory.name, category.name) parentCategory,
category.name category,
IF(transaction.amount <= 0, null, 		transaction.amount) credit,
IF(transaction.amount >= 0, null, -1 * 	transaction.amount) debit
FROM transactions transaction
LEFT JOIN categories category	
	ON category.id = transaction.category_id
LEFT JOIN categories parentCategory	
	ON parentCategory.id = category.parent_id
ORDER BY month DESC;

SELECT * FROM v_category_by_month ORDER BY parentCategory, category;

SELECT month, parentCategory, category, SUM(credit) credit, SUM(debit) debit 
FROM v_category_by_month
GROUP BY month, category_id, parent_id
ORDER BY month DESC, parentCategory, category
;