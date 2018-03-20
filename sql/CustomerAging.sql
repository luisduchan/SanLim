SELECT  a.[Entry No_] AS EntryNo, a.[Customer No_] AS CustomerNo, a.[Posting Date] AS PostingDate, a.[Due Date] AS DueDate, 
                      a.[Document No_] AS DocumentNo, a.[Currency Code] AS CurrencyCode, SUM(b.Amount) AS RemainingAmount, SUM(b.[Amount (LCY)]) 
                      AS RemainingAmountLCY
FROM         dbo.[SAN LIM FURNITURE VIETNAM LTD$Cust_ Ledger Entry] a INNER JOIN
                      dbo.[SAN LIM FURNITURE VIETNAM LTD$Detailed Cust_ Ledg_ Entry] b ON a.[Entry No_] = b.[Cust_ Ledger Entry No_]
WHERE     (a.[Document Type] = 2)
GROUP BY a.[Customer No_], a.[Due Date], a.[Currency Code], a.[Entry No_], a.[Posting Date], a.[Document No_]
--ORDER BY a.[Customer No_], a.[Due Date], a.[Currency Code]