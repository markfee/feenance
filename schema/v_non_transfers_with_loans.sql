use feenance;

DROP VIEW IF EXISTS v_non_transfers_with_loans;

CREATE VIEW v_non_transfers_with_loans AS

SELECT  transaction.*
    FROM    
        v_transactions transaction
    JOIN v_accounts account
        ON account.id = transaction.account_id
    LEFT JOIN v_transfers source
      ON source.source_id        = transaction.id
    LEFT JOIN v_transfers destination
      ON destination.destination_id   = transaction.id
  WHERE 
    (
        (source.id IS NULL AND destination.id IS NULL)  -- Filter Out Transfers 
        OR source.to_loan = 1                           -- EXCEPT payments to loans as these are valid expenses
    ) 
    AND NOT (account.is_loan = 1 AND debit IS NOT NULL) -- Filter out interest and charges on loans as they are a part of the payment to loans
ORDER BY source.to_loan DESC
;

SELECT * FROM v_non_transfers_with_loans;