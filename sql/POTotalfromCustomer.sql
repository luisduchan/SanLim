SELECT     doctype, reportgroup, productgroup, selltocustno, selltocustname, orgreqdate, orgshipdate, reportbegindate, reportenddate, 
                      ROUND(contmovement + SUM(totalcuft / (CASE WHEN cuftpercont > 10 THEN cuftpercont ELSE 2350 END)), 2) AS totalconts
FROM         dbo.POfromCustomer AS a
WHERE     (doctype = 4)
GROUP BY doctype, reportgroup, productgroup, selltocustno, selltocustname, contmovement, orgreqdate, orgshipdate, reportbegindate, reportenddate