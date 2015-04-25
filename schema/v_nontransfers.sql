use feenance;

DROP VIEW IF EXISTS v_non_transfers;

CREATE VIEW v_non_transfers AS
  SELECT v_transactions.*
  FROM v_transactions
    LEFT JOIN transfers
      ON (    transfers.source        = v_transactions.id
              OR  transfers.destination   = v_transactions.id
      )
  WHERE transfers.id IS NULL