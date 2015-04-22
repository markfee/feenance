use feenance;
DROP VIEW IF EXISTS v_6_month_rolling_account_by_month;
CREATE VIEW v_6_month_rolling_account_by_month AS
  SELECT account.*
    , SUM(rolling_year.movement) tot_movement_6
    , AVG(rolling_year.movement) avg_movement_6
  FROM v_account_by_month account
    LEFT JOIN v_account_by_month as rolling_year
      ON  rolling_year.account_id = account.account_id
          AND rolling_year.month_num > account.month_num - 6
          AND rolling_year.month_num <= account.month_num
  GROUP BY account.account_id, account.year, account.month
  ORDER BY account.year DESC, account.month DESC, account.account_id
;

SELECT * FROM v_6_month_rolling_account_by_month where account_id = 2;

use feenance;
SELECT
  year,
  month,
  SUM(bought_forward) bought_forward,
  SUM(movement) movement,
  SUM(carried_forward) carried_forward,
  SUM(tot_movement_6) tot_movement_6,
  SUM(tot_movement_6) / 6 avg_movement_6
FROM v_6_month_rolling_account_by_month
GROUP BY year, month
ORDER BY year DESC, month DESC
;
