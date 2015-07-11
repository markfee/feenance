use feenance;

DROP VIEW IF EXISTS v_non_transfers;

CREATE VIEW v_non_transfers AS
  SELECT  transaction.*
  FROM    v_transactions transaction
    LEFT JOIN v_transfers source
      ON source.source_id        = transaction.id
    LEFT JOIN v_transfers destination
      ON destination.destination_id   = transaction.id
  WHERE (source.id IS NULL AND destination.id IS NULL)
;

SELECT * FROM v_non_transfers