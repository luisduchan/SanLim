SELECT     a.No_ AS itemno, a.[Document No_] AS pono, a.[Line No_] AS linenumber, a.Quantity - ISNULL(t.qty, 0) AS xqty
FROM         dbo.[SAN LIM FURNITURE VIETNAM LTD$Purchase Line] AS a LEFT OUTER JOIN
                          (SELECT     nxat05 AS itemno, nxat53 AS pono, nxat35 AS linenumber, SUM(nxat21) AS qty
                            FROM          dbo.nxat
                            GROUP BY nxat05, nxat53, nxat35) AS t ON t.itemno = a.No_ AND t.pono = a.[Document No_] AND t.linenumber = a.[Line No_]
WHERE     (a.No_ <> '') AND (a.Type = 2)