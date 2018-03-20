SELECT     h.No_ AS docno, h.[Document Type] AS doctype, CASE WHEN h.[Order Date] < '1753-01-02' THEN NULL ELSE h.[Order Date] END AS orderdate, 
                      CASE WHEN h.[Customer PO Date] < '1753-01-02' THEN NULL ELSE h.[Customer PO Date] END AS orderdateend, h.[Sell-to Customer No_] AS selltocustno, 
                      RTRIM(LTRIM(h.[Sell-to Customer Name 2])) AS selltocustname, h.[Bill-to Customer No_] AS billtocustno, RTRIM(LTRIM(h.[Bill-to Name])) AS billtocustname, 
                      h.IK AS ikno, h.[Package Tracking No_] AS sik, h.POD AS productionline, RTRIM(LTRIM(h.[Customer PO 2])) AS productgroup, h.[Your Reference] AS reportgroup, 
                      h.[Conts Adjmt] AS contmovement, h.[Related Order#] AS relatedpo, h.[Cutting No_] AS cuttingno, h.Remark, h.Finished, h.[CONT Total] AS cuftpercont, 
                      CASE WHEN h.[Requested Delivery Date] < '1753-01-02' THEN NULL ELSE h.[Requested Delivery Date] END AS curreqdate, 
                      CASE WHEN h.[Requested Ship Date End] < '1753-01-02' THEN NULL ELSE h.[Requested Ship Date End] END AS curreqdateend, 
                      CASE WHEN h.[Factory Cof_ Ship Date Start] < '1753-01-02' THEN NULL ELSE h.[Factory Cof_ Ship Date Start] END AS orgshipdate, 
                      CASE WHEN h.[Factory Cof_ Ship Date End] < '1753-01-02' THEN NULL ELSE h.[Factory Cof_ Ship Date End] END AS orgshipdateend, 
                      CASE WHEN h.[1st Cof_ Ship Date Start] < '1753-01-02' THEN NULL ELSE h.[1st Cof_ Ship Date Start] END AS firstcofdate, 
                      CASE WHEN h.[1st Cof_ Ship Date End] < '1753-01-02' THEN NULL ELSE h.[1st Cof_ Ship Date End] END AS firstcofdateend, 
                      CASE WHEN h.[2nd Cof_ Ship Date Start] < '1753-01-02' THEN NULL ELSE h.[2nd Cof_ Ship Date Start] END AS secondcofdate, 
                      CASE WHEN h.[2nd Cof_ Ship Date End] < '1753-01-02' THEN NULL ELSE h.[2nd Cof_ Ship Date End] END AS secondcofdateend, 
                      CASE WHEN h.[3rd Cof_ Ship Date Start] < '1753-01-02' THEN NULL ELSE h.[3rd Cof_ Ship Date Start] END AS thirdcofdate, 
                      CASE WHEN h.[3rd Cof_ Ship Date End] < '1753-01-02' THEN NULL ELSE h.[3rd Cof_ Ship Date End] END AS thirdcofdateend, 
                      CASE WHEN h.[Last Cof_ Ship Date Start] < '1753-01-02' THEN NULL ELSE h.[Last Cof_ Ship Date Start] END AS lastcofdate, 
                      CASE WHEN h.[Last Cof_ Ship Date End] < '1753-01-02' THEN NULL ELSE h.[Last Cof_ Ship Date End] END AS lastcofdateend, 
                      CASE WHEN h.[Scheduled Ass_ Date Start] < '1753-01-02' THEN NULL ELSE h.[Scheduled Ass_ Date Start] END AS assemplydate, 
                      CASE WHEN h.[Scheduled Ass_ Date End] < '1753-01-02' THEN NULL ELSE h.[Scheduled Ass_ Date End] END AS assemplydateend, 
                      CASE WHEN h.[Cargo Ready Date] < '1753-01-02' THEN NULL ELSE h.[Cargo Ready Date] END AS cargoreadydate, 
                      CASE WHEN h.[Estimated ETD] < '1753-01-02' THEN NULL ELSE h.[Estimated ETD] END AS etd, CASE WHEN h.[Org_ Req_ Ship Date Start] < '1753-01-02' THEN NULL 
                      ELSE h.[Org_ Req_ Ship Date Start] END AS orgreqdate, CASE WHEN h.[Org_ Req_ Ship Date End] < '1753-01-02' THEN NULL 
                      ELSE h.[Org_ Req_ Ship Date End] END AS orgreqdateend, ISNULL(l.No_, N'') AS itemno, h.[Location Code] AS location, ISNULL(l.Description, N'') AS description, 
                      ISNULL(l.Quantity, 0) AS quantity, ISNULL(l.[Quantity Shipped], 0) AS shpQty, ISNULL(l.[Outstanding Quantity], 0) AS otsQty, ISNULL(i.CUFT, 0) AS cuft, 
                      ISNULL(l.Quantity, 0) * ISNULL(i.CUFT, 0) AS totalcuft, h.[Currency Code] AS currcode, h.[Currency Factor] AS currfactor, ISNULL(l.[Unit Price], 0) AS unitprice, 
                      ISNULL(l.[Line Amount], 0) AS lineamount, 
                      CASE WHEN h.[Org_ Req_ Ship Date End] < '1753-01-02' THEN CASE WHEN h.[Org_ Req_ Ship Date Start] < '1753-01-02' THEN NULL 
                      ELSE h.[Org_ Req_ Ship Date Start] END ELSE h.[Org_ Req_ Ship Date End] END AS orgreqlatecalc, 
                      CASE WHEN h.[Requested Ship Date End] < '1753-01-02' THEN CASE WHEN h.[Requested Delivery Date] < '1753-01-02' THEN NULL 
                      ELSE h.[Requested Delivery Date] END ELSE h.[Requested Ship Date End] END AS curreqlatecalc, 
                      CASE WHEN h.[Factory Cof_ Ship Date End] < '1753-01-02' THEN CASE WHEN h.[Factory Cof_ Ship Date Start] < '1753-01-02' THEN NULL 
                      ELSE h.[Factory Cof_ Ship Date Start] END ELSE h.[Factory Cof_ Ship Date End] END AS orgshiplatecalc, CASE WHEN r.[Begin Date] < '1753-01-02' THEN NULL 
                      ELSE r.[Begin Date] END AS reportbegindate, CASE WHEN r.[End Date] < '1753-01-02' THEN NULL ELSE r.[End Date] END AS reportenddate
FROM         dbo.[SAN LIM FURNITURE VIETNAM LTD$Sales Header] AS h LEFT OUTER JOIN
                      dbo.[SAN LIM FURNITURE VIETNAM LTD$Sales Line] AS l ON h.No_ = l.[Document No_] AND h.[Document Type] = l.[Document Type] AND l.[Document Type] = 4 AND 
                      l.Type = 2 LEFT OUTER JOIN
                      dbo.[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] AS i ON l.No_ = i.[Item No_] AND i.Code = 'CTNS' LEFT OUTER JOIN
                      dbo.[SAN LIM FURNITURE VIETNAM LTD$Report Group Setup] AS r ON h.[Your Reference] = r.Code
WHERE     (h.[Document Type] = 4) AND (h.NotRealOrder = 0)