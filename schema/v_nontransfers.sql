use feenance;

DROP VIEW IF EXISTS v_non_transfers;
DROP VIEW IF EXISTS v_non_transfers_view;
DROP VIEW IF EXISTS v_non_transfers_union;

CREATE VIEW v_non_transfers AS
  SELECT  transaction.*
  FROM    v_transactions transaction
    LEFT JOIN v_transfers source
      ON source.source_id        = transaction.id AND source.to_loan = 0
    LEFT JOIN v_transfers destination
      ON destination.destination_id   = transaction.id AND destination.from_loan = 0
  WHERE source.id IS NULL AND destination.id IS NULL;