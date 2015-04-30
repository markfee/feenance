USE feenance;

DROP FUNCTION IF EXISTS fn_my_month;

CREATE FUNCTION fn_my_month(p_date DATE, p_cuttoff TINYINT)  RETURNS INT
  BEGIN

    DECLARE v_month INT;

    IF (DAY(p_date) < p_cuttoff)
    THEN
      SET v_month = MONTH(p_date) - 1;
    ELSE
      SET v_month = MONTH(p_date);
    END IF;

    RETURN v_month;

  END