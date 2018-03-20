/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  it02
 * Created: May 29, 2017
 */

SELECT selltocustname, (SELECT sum(pc1.totalcuft)
FROM [dbo].[POfromCustomer] pc1
WHERE pc1.selltocustname = pc.selltocustname AND pc1.doctype=4 AND pc1.reportgroup = 'JUN-2017' AND pc1.Finished = 0) as sumcuft
FROM [dbo].[POfromCustomer] pc WHERE doctype=4 AND reportgroup = 'JUN-2017' GROUP BY selltocustname;



--check quantity between cust po and blanket
SELECT
	cph.CustomerName,
	cpl.[Blanket PO#],
	cpl.ItemNo,
	SUM (cpl.Quantity),
	(
		SELECT
			SUM (sl.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
		LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom1 ON (
			sl.No_ = iuom1.[Item No_]
			AND iuom1.Code = 'CTNS'
		)
		WHERE
			sl.[Document No_] = cpl.[Blanket PO#]
		AND sl.No_ = cpl.ItemNo
	)
FROM
	[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo = cpl.PONo)
LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom ON (
	cpl.ItemNo = iuom.[Item No_]
	AND iuom.Code = 'CTNS'
)
WHERE
	cph.CommitReqShipDateFrom >= '2017-06-01'
AND cph.[Order Type] IN (0, 1)
GROUP BY
	cph.CustomerName,
	cpl.[Blanket PO#],
	cpl.ItemNo
HAVING
	SUM (cpl.Quantity) - (
		SELECT
			SUM (sl.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
		LEFT JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Item Unit of Measure] iuom1 ON (
			sl.No_ = iuom1.[Item No_]
			AND iuom1.Code = 'CTNS'
		)
		WHERE
			sl.[Document No_] = cpl.[Blanket PO#]
		AND sl.No_ = cpl.ItemNo
	) <> 0



SELECT
	sl.[Document No_],
	sl.No_,
	sl.Quantity,
	(
		SELECT
			SUM (cpl.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo = cpl.PONo)
		WHERE
			cpl.[Blanket PO#] = sl.[Document No_]
		AND cpl.ItemNo = sl.No_ AND cph.[Order Type] IN (0,1)
	)
FROM
	[dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl
WHERE (
		SELECT
			SUM (cpl.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo = cpl.PONo)
		WHERE
			cpl.[Blanket PO#] = sl.[Document No_]
		AND cpl.ItemNo = sl.No_ AND cph.[Order Type] IN (0,1)
	) IS NOT NULL AND (sl.Quantity - (
		SELECT
			SUM (cpl.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph ON (cph.PONo = cpl.PONo)
		WHERE
			cpl.[Blanket PO#] = sl.[Document No_]
		AND cpl.ItemNo = sl.No_ AND cph.[Order Type] IN (0,1)
	)) <> 0;





----------
SELECT
	cph.POCustomer po_no,
	cph.PONo technical_po_no,
	cph.CommitReqShipDateFrom confirm_ship_date,
	cph.[Order Type] po_type,
	CASE cph.[Order Type]
WHEN 1 THEN
	LEFT (
		cpl.ItemNo,
		CHARINDEX('-', cpl.ItemNo) - 1
	) + 'D' + RIGHT (
		cpl.ItemNo,
		LEN(cpl.ItemNo) - CHARINDEX('-', cpl.ItemNo) + 1
	)
ELSE
	cpl.ItemNo
END item_no,
 SUM (cpl.Quantity) ordered_quantity,
 shipping.[SO No_] so,
 shipping.[Shipment Calc_ Date] real_calc_ship_date,
 CAST (
	(
		SELECT
			SUM (csn.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
		JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
			sh.No_ = csn.[SO No_]
			AND sh.Ship = 1
		)
		WHERE
			csn.[CustPO No_] = cph.PONo
		AND csn.[Item No_] = cpl.ItemNo
	) AS INT
) total_shipped
FROM
	[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo) OUTER APPLY (
	SELECT
		TOP 1 csn.[Item No_],
		csn.[SO No_],
		sh.[Shipment Calc_ Date]
	FROM
		[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
	JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON (
		sl.[Document No_] = csn.[SO No_]
		AND sl.No_ = csn.[Item No_]
	)
	JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sl.[Document No_] = sh.No_
	WHERE
		csn.[CustPO No_] = cpl.PONo
	AND csn.[Item No_] = cpl.ItemNo
	AND sh.Ship = 1
	ORDER BY
		sh.[Shipment Calc_ Date] DESC
) shipping
WHERE
	cph.CustomerNo = 'C54000'
AND cph.CommitReqShipDateFrom BETWEEN '2017-07-01'
AND '2017-07-31'
AND cph.[Order Type] IN (0, 2)
GROUP BY
	cph.POCustomer,
	cph.PONo,
	cph.CommitReqShipDateFrom,
	cpl.ItemNo,
	cph.[Order Type],
	shipping.[Shipment Calc_ Date],
	shipping.[SO No_]
ORDER BY
	cph.POCustomer,
	cpl.ItemNo
---------
lazboy score card
SELECT
	cph.POCustomer po_no,
	cph.PONo technical_po_no,
	cph.CommitReqShipDateFrom confirm_ship_date,
	cph.[Order Type] po_type,
cpl.ItemNo item_no_technical,
	CASE cph.[Order Type]
WHEN 1 THEN
	LEFT (
		cpl.ItemNo,
		CHARINDEX('-', cpl.ItemNo) - 1
	) + 'D' + RIGHT (
		cpl.ItemNo,
		LEN(cpl.ItemNo) - CHARINDEX('-', cpl.ItemNo) + 1
	)
ELSE
	cpl.ItemNo
END item_no,
 SUM (cpl.Quantity) ordered_quantity,
 shipping.[SO No_] so,
 shipping.[Shipment Calc_ Date] real_calc_ship_date,
 CAST (
	(
		SELECT
			SUM (csn.Quantity)
		FROM
			[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
		JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
			sh.No_ = csn.[SO No_]
			AND sh.Ship = 1
		)
		WHERE
			csn.[CustPO No_] = cph.PONo
		AND csn.[Item No_] = cpl.ItemNo
	) AS INT
) total_shipped
FROM
	[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo) OUTER APPLY (
	SELECT
		TOP 1 csn.[Item No_],
		csn.[SO No_],
		sh.[Shipment Calc_ Date]
	FROM
		[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
	JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON (
		sl.[Document No_] = csn.[SO No_]
		AND sl.No_ = csn.[Item No_]
	)
	JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sl.[Document No_] = sh.No_
	WHERE
		csn.[CustPO No_] = cpl.PONo
	AND csn.[Item No_] = cpl.ItemNo
	AND sh.Ship = 1
	ORDER BY
		sh.[Shipment Calc_ Date] DESC
) shipping
WHERE
	cph.CustomerNo = 'C54000'
AND cph.CommitReqShipDateFrom BETWEEN '2017-07-01'
AND '2017-07-31'
AND cph.[Order Type] IN (0, 2)
GROUP BY
	cph.POCustomer,
	cph.PONo,
	cph.CommitReqShipDateFrom,
	cpl.ItemNo,
	cph.[Order Type],
	shipping.[Shipment Calc_ Date],
	shipping.[SO No_]
UNION
	SELECT
		cph.POCustomer po_no,
		cph.PONo technical_po_no,
		cph.CommitReqShipDateFrom confirm_ship_date,
		cph.[Order Type] po_type,
cpl.ItemNo item_no_technical,
		CASE cph.[Order Type]
	WHEN 1 THEN
		LEFT (
			cpl.ItemNo,
			CHARINDEX('-', cpl.ItemNo) - 1
		) + 'D' + RIGHT (
			cpl.ItemNo,
			LEN(cpl.ItemNo) - CHARINDEX('-', cpl.ItemNo) + 1
		)
	ELSE
		cpl.ItemNo
	END item_no,
	SUM (cpl.Quantity) ordered_quantity,
	sh.No_ so,
	sh.[Shipment Calc_ Date] real_calc_ship_date,
	CAST (
		(
			SELECT
				SUM (csn.Quantity)
			FROM
				[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPO Shipment NEW] csn
			JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON (
				sh.No_ = csn.[SO No_]
				AND sh.Ship = 1
			)
			WHERE
				csn.[CustPO No_] = cph.PONo
			AND csn.[Item No_] = cpl.ItemNo
		) AS INT
	) total_shipped
FROM
	[dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOHeader] cph
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$CustPOLine] cpl ON (cph.PONo = cpl.PONo)
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Line] sl ON sl.No_ = cpl.ItemNo AND sl.[Document No_] = cpl.[Blanket PO#]
JOIN [dbo].[SAN LIM FURNITURE VIETNAM LTD$Sales Header] sh ON sl.[Document No_] = sh.No_
AND sh.[Document Type] = 4
WHERE
	cph.CustomerNo = 'C54000'
AND cph.CommitReqShipDateFrom BETWEEN '2017-07-01'
AND '2017-07-31'
AND cph.[Order Type] IN (1)
GROUP BY
	cph.POCustomer,
	cph.PONo,
	cph.CommitReqShipDateFrom,
	cpl.ItemNo,
	cph.[Order Type],
	sh.No_,
	sh.[Shipment Calc_ Date]
ORDER BY
	po_no,
technical_po_no,
item_no