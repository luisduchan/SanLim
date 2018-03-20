SELECT
	a.orderno,
	a.orderline,
	a.itemno,
	CASE
WHEN a.qty > ISNULL(c.xpbp15, 0) THEN
	a.qty - ISNULL(c.xpbp15, 0)
ELSE
	0
END AS qty,
 a.lastestcomedate
FROM
	(
		SELECT
			xloe01 AS orderno,
			xloe02 AS orderline,
			xloe04 AS itemno,
			SUM (xloe06) AS qty,
			MAX (xloe20) AS lastestcomedate
		FROM
			dbo.[SAN LIM FURNITURE VIETNAM LTD$ReceivingDetail]
		GROUP BY
			xloe01,
			xloe02,
			xloe04
	) AS a
LEFT OUTER JOIN (
	SELECT
		xpap00,
		xpbp02,
		xpbp05,
		SUM (xpbp15) AS xpbp15
	FROM
		dbo.xpbp AS b
	WHERE
		(xpbp04 = 'ITEM')
	GROUP BY
		xpap00,
		xpbp02,
		xpbp05
) AS c ON a.orderno = c.xpap00
AND a.orderline = c.xpbp02
AND a.itemno = c.xpbp05