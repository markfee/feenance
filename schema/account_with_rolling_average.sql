use feenance;
DROP VIEW IF EXISTS v_rolling_account_by_month;
CREATE VIEW v_rolling_account_by_month AS
  SELECT account.*
    , SUM(rolling_year.movement) tot_movement_12
    , AVG(rolling_year.movement) avg_movement_12
  FROM v_account_by_month account
    LEFT JOIN v_account_by_month as rolling_year
      ON  rolling_year.account_id = account.account_id
          AND ((12 * rolling_year.year + rolling_year.month) >  (12 * (account.year-1) + account.month))
          AND ((12 * rolling_year.year + rolling_year.month) <= (12 * (account.year  ) + account.month))
  GROUP BY account.account_id, account.year, account.month
  ORDER BY account.year DESC, account.month DESC, account.account_id
;

SELECT * FROM v_rolling_account_by_month;