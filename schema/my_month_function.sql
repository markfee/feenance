USE feenance;

DROP FUNCTION IF EXISTS fn_my_month;

CREATE FUNCTION fn_my_month(p_date DATE)  RETURNS INT
  BEGIN
    SET @cuttoff = 23;
    IF (DAY(p_date) >= @cuttoff) THEN
      RETURN  MONTH(p_date);
    ELSE IF (MONTH(p_date) = 1 ) THEN
      RETURN 12;
    ELSE
      RETURN MONTH(p_date) - 1;
    END IF;
    END IF;
END;

DROP FUNCTION IF EXISTS fn_my_year;

CREATE FUNCTION fn_my_year(p_date DATE)  RETURNS INT
  BEGIN
    SET @cuttoff = 23;
    IF MONTH(p_date) = 1 AND (DAY(p_date) < @cuttoff) THEN
      RETURN YEAR(p_date) - 1;
    ELSE
      RETURN  YEAR(p_date);
    END IF;
  END;