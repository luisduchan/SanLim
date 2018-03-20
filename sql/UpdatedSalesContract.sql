SELECT     nxat05 AS itemno, nxat53 AS pono, nxat35 AS linenumber, SUM(nxat21) AS qty
FROM         dbo.nxat
GROUP BY nxat05, nxat53, nxat35