use feenance;

DROP VIEW IF EXISTS v_transfers;

CREATE VIEW v_transfers AS
  SELECT
    transfers.id,
    destination.date		    date,
    transfers.source 		    source_id,
    transfers.destination 	destination_id,
    destination.amount		  amount,
    source.account_id		    source_account,
    destination.account_id	destination_account,
    source_account.is_asset from_asset,
    source_account.is_loan  from_loan,
    destination_account.is_asset to_asset,
    destination_account.is_loan  to_loan
  FROM transfers
  JOIN transactions source        ON transfers.source = source.id
  JOIN transactions destination   ON transfers.destination = destination.id
  JOIN v_accounts source_account      ON source_account.id = source.account_id
  JOIN v_accounts destination_account ON destination_account.id = destination.account_id
  ;

