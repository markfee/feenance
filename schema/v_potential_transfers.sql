use feenance;

DROP VIEW IF EXISTS v_potential_transfers;

CREATE VIEW v_potential_transfers AS
  SELECT
          source.date,
          source.id               source_id,
          destination.id          destination_id,
          source.account_id 		  source_account_id,
          destination.account_id  destination_account_id,
          source.amount			      source_amount,
          destination.amount		  destination_amount
        FROM transactions source
          JOIN transactions destination
            ON	DATEDIFF(source.date, destination.date) = 0
            AND source.amount + destination.amount = 0
            AND source.account_id <> destination.account_id
          LEFT JOIN transfers
            ON transfers.source = source.id
            OR transfers.source = destination.id
        WHERE
            source.amount < 0;