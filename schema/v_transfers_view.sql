use feenance;

DROP VIEW IF EXISTS v_transfers;

CREATE VIEW v_transfers AS
  SELECT
    transfers.id,
    destination.date		date,
    transfers.source 		source_id,
    transfers.destination 	destination_id,
    destination.amount		amount,
    source.account_id		source_account,
    destination.account_id	destination_account
  FROM transfers
  JOIN transactions source ON transfers.source = source.id
  JOIN transactions destination ON transfers.destination = destination.id;

