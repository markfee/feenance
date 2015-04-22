use feenance;
DROP VIEW IF EXISTS v_12_month_rolling_account_by_month;
CREATE VIEW v_12_month_rolling_account_by_month AS
  SELECT account.*
    , SUM(rolling_year.movement) tot_movement_12
    , AVG(rolling_year.movement) avg_movement_12
  FROM v_account_by_month account
    LEFT JOIN v_account_by_month as rolling_year
      ON  rolling_year.account_id = account.account_id
          AND rolling_year.month_num > account.month_num - 12
          AND rolling_year.month_num <= account.month_num
  GROUP BY account.account_id, account.year, account.month
  ORDER BY account.year DESC, account.month DESC, account.account_id
;

SELECT * FROM v_12_month_rolling_account_by_month where account_id = 2;

use feenance;
SELECT
  year,
  month,
  SUM(bought_forward) bought_forward,
  SUM(movement) movement,
  SUM(carried_forward) carried_forward,
  SUM(tot_movement_12) tot_movement_12,
  SUM(tot_movement_12) / 12 avg_movement_12
FROM v_12_month_rolling_account_by_month
GROUP BY year, month
ORDER BY year DESC, month DESC
;
