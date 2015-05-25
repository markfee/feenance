use feenance;

DROP VIEW IF EXISTS v_non_transfers;

CREATE VIEW v_non_transfers AS
  SELECT  transaction.*
  FROM    v_transactions transaction
    LEFT JOIN v_transfers transfer
      ON
        (
          (transfer.source_id        = transaction.id AND from_loan = 0)
          OR  (transfer.destination_id   = transaction.id AND to_loan = 0)
        )
  WHERE transfer.id IS NULL;