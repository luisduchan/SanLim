-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `auth_assignment`;
CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('inventory_report',	'2',	NULL),
('material_ouput_report',	'1',	NULL);

DROP TABLE IF EXISTS `auth_item`;
CREATE TABLE `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('branch_create',	2,	'Create Branch',	NULL,	NULL,	1481595555,	1481595555),
('branch_delete',	2,	'Delete Branch',	NULL,	NULL,	1481595555,	1481595555),
('branch_index',	2,	'Index',	NULL,	NULL,	1481595555,	1481595555),
('branch_owner',	1,	NULL,	NULL,	NULL,	1481596019,	1481596019),
('branch_update',	2,	'Update Branch',	NULL,	NULL,	1481595555,	1481595555),
('branch_view',	2,	'View Branch',	NULL,	NULL,	1481595555,	1481595555),
('inventory_report',	1,	NULL,	NULL,	NULL,	1481597081,	1481597081),
('material_ouput_report',	2,	'Material Output Report',	NULL,	NULL,	1481596953,	1481596953);

DROP TABLE IF EXISTS `auth_item_child`;
CREATE TABLE `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('inventory_report',	'material_ouput_report');

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `branches`;
CREATE TABLE `branches` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) DEFAULT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `branches` (`id`, `branch_name`, `branch_address`) VALUES
(1,	'hoaht',	'trang bom');

DROP TABLE IF EXISTS `component`;
CREATE TABLE `component` (
  `item_no` char(255) NOT NULL,
  PRIMARY KEY (`item_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `component` (`item_no`) VALUES
('WOODABZN.014.1100'),
('WOODABZN.014.1250'),
('WOODRBBF.1000.500'),
('WOODRBBF.340.570'),
('WOODRBBF.380.500'),
('WOODRBBF.380.570'),
('WOODRBBF.420.570'),
('WOODRBBF.450.570'),
('WOODRBBF.500.500'),
('WOODRBBF.550.500'),
('WOODRBBF.550.570'),
('WOODRBBF.700.500'),
('WOODRBBF.700.570'),
('WOODRBBF.750.500'),
('WOODRBBF.800.570');

DROP TABLE IF EXISTS `customer`;
CREATE TABLE `customer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(1) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `customer` (`id`, `code`, `name`) VALUES
(1,	NULL,	'KLAUSSNER'),
(2,	NULL,	'PB'),
(3,	NULL,	'RIVER SIDE'),
(4,	NULL,	'BOMBAY'),
(5,	NULL,	'BOWRING'),
(6,	NULL,	'AMERICAN DREW'),
(7,	NULL,	'CITYFURNITURE'),
(8,	NULL,	'DMI'),
(9,	NULL,	'DAVIS DIRECT'),
(10,	NULL,	'RTG'),
(11,	NULL,	'ASHLEY CHINA'),
(12,	NULL,	'ASHLEY'),
(13,	NULL,	'RAY MOUR'),
(14,	NULL,	'HAVERTY'),
(15,	NULL,	'RAYMOUR');

DROP TABLE IF EXISTS `destination`;
CREATE TABLE `destination` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `item`;
CREATE TABLE `item` (
  `timestamp` longblob NOT NULL,
  `No_` varchar(20) NOT NULL,
  `No_ 2` varchar(20) NOT NULL,
  `Description` varchar(250) NOT NULL,
  `Search Description` varchar(250) NOT NULL,
  `Description 2` varchar(50) NOT NULL,
  `Base Unit of Measure` varchar(10) NOT NULL,
  `Price Unit Conversion` int(11) NOT NULL,
  `Type` int(11) NOT NULL,
  `Inventory Posting Group` varchar(10) NOT NULL,
  `Shelf No_` varchar(10) NOT NULL,
  `Item Disc_ Group` varchar(20) NOT NULL,
  `Allow Invoice Disc_` tinyint(4) NOT NULL,
  `Statistics Group` int(11) NOT NULL,
  `Commission Group` int(11) NOT NULL,
  `Unit Price` decimal(38,20) NOT NULL,
  `Price_Profit Calculation` int(11) NOT NULL,
  `Profit _` decimal(38,20) NOT NULL,
  `Costing Method` int(11) NOT NULL,
  `Unit Cost` decimal(38,20) NOT NULL,
  `Standard Cost` decimal(38,20) NOT NULL,
  `Last Direct Cost` decimal(38,20) NOT NULL,
  `Indirect Cost _` decimal(38,20) NOT NULL,
  `Cost is Adjusted` tinyint(4) NOT NULL,
  `Allow Online Adjustment` tinyint(4) NOT NULL,
  `Vendor No_` varchar(20) NOT NULL,
  `Vendor Item No_` varchar(20) NOT NULL,
  `Lead Time Calculation` varchar(32) NOT NULL,
  `Reorder Point` decimal(38,20) NOT NULL,
  `Maximum Inventory` decimal(38,20) NOT NULL,
  `Reorder Quantity` decimal(38,20) NOT NULL,
  `Alternative Item No_` varchar(20) NOT NULL,
  `Unit List Price` decimal(38,20) NOT NULL,
  `Duty Due _` decimal(38,20) NOT NULL,
  `Duty Code` varchar(10) NOT NULL,
  `Gross Weight` decimal(38,20) NOT NULL,
  `Net Weight` decimal(38,20) NOT NULL,
  `Units per Parcel` decimal(38,20) NOT NULL,
  `Durability` varchar(10) NOT NULL,
  `Freight Type` varchar(10) NOT NULL,
  `Tariff No_` varchar(20) NOT NULL,
  `Duty Unit Conversion` decimal(38,20) NOT NULL,
  `Country_Region Purchased Code` varchar(10) NOT NULL,
  `Budget Quantity` decimal(38,20) NOT NULL,
  `Budgeted Amount` decimal(38,20) NOT NULL,
  `Budget Profit` decimal(38,20) NOT NULL,
  `Blocked` tinyint(4) NOT NULL,
  `Last Date Modified` datetime NOT NULL,
  `Price Includes VAT` tinyint(4) NOT NULL,
  `VAT Bus_ Posting Gr_ (Price)` varchar(10) NOT NULL,
  `Gen_ Prod_ Posting Group` varchar(10) NOT NULL,
  `Picture` longblob,
  `Country_Region of Origin Code` varchar(10) NOT NULL,
  `Automatic Ext_ Texts` tinyint(4) NOT NULL,
  `No_ Series` varchar(10) NOT NULL,
  `Tax Group Code` varchar(10) NOT NULL,
  `VAT Prod_ Posting Group` varchar(10) NOT NULL,
  `Reserve` int(11) NOT NULL,
  `Global Dimension 1 Code` varchar(20) NOT NULL,
  `Global Dimension 2 Code` varchar(20) NOT NULL,
  `Stockout Warning` int(11) NOT NULL,
  `Prevent Negative Inventory` int(11) NOT NULL,
  `Application Wksh_ User ID` varchar(128) NOT NULL,
  `Assembly Policy` int(11) NOT NULL,
  `GTIN` varchar(14) NOT NULL,
  `Default Deferral Template Code` varchar(10) NOT NULL,
  `Low-Level Code` int(11) NOT NULL,
  `Lot Size` decimal(38,20) NOT NULL,
  `Serial Nos_` varchar(10) NOT NULL,
  `Last Unit Cost Calc_ Date` datetime NOT NULL,
  `Rolled-up Material Cost` decimal(38,20) NOT NULL,
  `Rolled-up Capacity Cost` decimal(38,20) NOT NULL,
  `Scrap _` decimal(38,20) NOT NULL,
  `Inventory Value Zero` tinyint(4) NOT NULL,
  `Discrete Order Quantity` int(11) NOT NULL,
  `Minimum Order Quantity` decimal(38,20) NOT NULL,
  `Maximum Order Quantity` decimal(38,20) NOT NULL,
  `Safety Stock Quantity` decimal(38,20) NOT NULL,
  `Order Multiple` decimal(38,20) NOT NULL,
  `Safety Lead Time` varchar(32) NOT NULL,
  `Flushing Method` int(11) NOT NULL,
  `Replenishment System` int(11) NOT NULL,
  `Rounding Precision` decimal(38,20) NOT NULL,
  `Sales Unit of Measure` varchar(10) NOT NULL,
  `Purch_ Unit of Measure` varchar(10) NOT NULL,
  `Time Bucket` varchar(32) NOT NULL,
  `Reordering Policy` int(11) NOT NULL,
  `Include Inventory` tinyint(4) NOT NULL,
  `Manufacturing Policy` int(11) NOT NULL,
  `Rescheduling Period` varchar(32) NOT NULL,
  `Lot Accumulation Period` varchar(32) NOT NULL,
  `Dampener Period` varchar(32) NOT NULL,
  `Dampener Quantity` decimal(38,20) NOT NULL,
  `Overflow Level` decimal(38,20) NOT NULL,
  `Manufacturer Code` varchar(10) NOT NULL,
  `Item Category Code` varchar(10) NOT NULL,
  `Created From Nonstock Item` tinyint(4) NOT NULL,
  `Product Group Code` varchar(10) NOT NULL,
  `Service Item Group` varchar(10) NOT NULL,
  `Item Tracking Code` varchar(10) NOT NULL,
  `Lot Nos_` varchar(10) NOT NULL,
  `Expiration Calculation` varchar(32) NOT NULL,
  `Special Equipment Code` varchar(10) NOT NULL,
  `Put-away Template Code` varchar(10) NOT NULL,
  `Put-away Unit of Measure Code` varchar(10) NOT NULL,
  `Phys Invt Counting Period Code` varchar(10) NOT NULL,
  `Last Counting Period Update` datetime NOT NULL,
  `Use Cross-Docking` tinyint(4) NOT NULL,
  `Next Counting Start Date` datetime NOT NULL,
  `Next Counting End Date` datetime NOT NULL,
  `NPL Group Code` varchar(20) NOT NULL,
  `Discontinous` tinyint(4) NOT NULL,
  `ScrapSample` decimal(38,20) NOT NULL,
  `Special Item Group` int(11) NOT NULL,
  `Metre23` decimal(38,20) NOT NULL,
  `Specification 2` varchar(60) NOT NULL,
  `CostingPrice` decimal(38,20) NOT NULL,
  `Additional` decimal(38,20) NOT NULL,
  `OutWorkCost` decimal(38,20) NOT NULL,
  `IsOutWork` tinyint(4) NOT NULL,
  `GroupSumInComponent` tinyint(4) NOT NULL,
  `ItemType` int(11) NOT NULL,
  `AverageUsed` decimal(38,20) NOT NULL,
  `DspMsg` tinyint(4) NOT NULL,
  `Thick (MS)` decimal(38,20) NOT NULL,
  `Vietnamese Description` varchar(100) NOT NULL,
  `Copied To Copied Company` tinyint(4) NOT NULL,
  `Is Copied` tinyint(4) NOT NULL,
  `PicturePath` varchar(80) NOT NULL,
  `CAD` varchar(80) NOT NULL,
  `PackingMethod` varchar(60) NOT NULL,
  `PackingCAD` varchar(80) NOT NULL,
  `Specification` varchar(60) NOT NULL,
  `Art No` varchar(20) NOT NULL,
  `Item Available` int(11) NOT NULL,
  `Cutomer code2` varchar(20) NOT NULL,
  `Temp Code` varchar(20) NOT NULL,
  `No of Comp_` int(11) NOT NULL,
  `Last User Modified` varchar(50) NOT NULL,
  `Variant` varchar(60) NOT NULL,
  `Purchaser Code` varchar(10) NOT NULL,
  `Routing No_` varchar(20) NOT NULL,
  `Production BOM No_` varchar(20) NOT NULL,
  `Single-Level Material Cost` decimal(38,20) NOT NULL,
  `Single-Level Capacity Cost` decimal(38,20) NOT NULL,
  `Single-Level Subcontrd_ Cost` decimal(38,20) NOT NULL,
  `Single-Level Cap_ Ovhd Cost` decimal(38,20) NOT NULL,
  `Single-Level Mfg_ Ovhd Cost` decimal(38,20) NOT NULL,
  `Overhead Rate` decimal(38,20) NOT NULL,
  `Rolled-up Subcontracted Cost` decimal(38,20) NOT NULL,
  `Rolled-up Mfg_ Ovhd Cost` decimal(38,20) NOT NULL,
  `Rolled-up Cap_ Overhead Cost` decimal(38,20) NOT NULL,
  `Order Tracking Policy` int(11) NOT NULL,
  `Critical` tinyint(4) NOT NULL,
  `Common Item No_` varchar(20) NOT NULL,
  `Item Marketing Code` varchar(20) NOT NULL,
  `Ext_ Safety Stock Quantity` decimal(38,20) NOT NULL,
  `OP Scrap` decimal(38,20) NOT NULL,
  `Cutomer No_` varchar(20) NOT NULL,
  PRIMARY KEY (`No_`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `item_category`;
CREATE TABLE `item_category` (
  `timestamp` longblob NOT NULL,
  `Code` varchar(10) NOT NULL,
  `Description` varchar(50) NOT NULL,
  `Def_ Gen_ Prod_ Posting Group` varchar(10) NOT NULL,
  `Def_ Inventory Posting Group` varchar(10) NOT NULL,
  `Def_ Tax Group Code` varchar(10) NOT NULL,
  `Def_ Costing Method` int(11) NOT NULL,
  `Def_ VAT Prod_ Posting Group` varchar(10) NOT NULL,
  `Purchase Scrap` decimal(38,20) NOT NULL,
  `Calc_ Unit of Measure` varchar(10) NOT NULL,
  `Unit of Measure Description` varchar(50) NOT NULL,
  `Calc_ Cubic Metre` tinyint(4) NOT NULL,
  `Is Calc_ Display` tinyint(4) NOT NULL,
  PRIMARY KEY (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `item_category` (`timestamp`, `Code`, `Description`, `Def_ Gen_ Prod_ Posting Group`, `Def_ Inventory Posting Group`, `Def_ Tax Group Code`, `Def_ Costing Method`, `Def_ VAT Prod_ Posting Group`, `Purchase Scrap`, `Calc_ Unit of Measure`, `Unit of Measure Description`, `Calc_ Cubic Metre`, `Is Calc_ Display`) VALUES
('\0\0\0\0	3�',	'ALCOHOL.90',	'alcohol 90 degrees',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0��]',	'APR.DOC',	'Approval Price List',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0Èn',	'ASSEMBLE',	'Assemble instruction',	'PACK',	'PACKING',	'',	3,	'VATHH10',	0.00000000000000000000,	'SET',	'SET',	1,	1),
('\0\0\0\0���',	'B5934',	'B5934',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0~�',	'BACK KNIFE',	'BACK KNIFE',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'BENTWOOD',	'BENT WOOD',	'BENTWOOD',	'BENTWOOD',	'',	3,	'VATHH10',	0.00000000000000000000,	'M3',	'M3',	1,	1),
('\0\0\0\0Èo',	'CARTON',	'Carton',	'PACK',	'PACKING',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	1),
('\0\0\0\0Èp',	'CHEM',	'Chemical items',	'CHEMICAL',	'CHEM',	'',	3,	'VATHH10',	0.00000000000000000000,	'KG',	'KG',	1,	1),
('\0\0\0\0Èq',	'COMM',	'Comon items',	'NON-STOCK',	'COMM',	'',	3,	'NOVAT',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0È�',	'CORKBOARD',	'CORKBOARD',	'',	'',	'',	3,	'',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0Èr',	'CUTTER',	'Cutter',	'TOOL',	'SPAR-TOOL',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0�\Zd',	'CUTTER-OLD',	'Cutter ',	'COMM',	'COMM',	'',	3,	'NOVAT',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0d�',	'CUTTER-R',	'Cutter',	'COMM',	'COMM',	'',	3,	'NOVAT',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0~�',	'DRILL',	'DRILL',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'FA',	'Fix Assets',	'FA',	'MCHI',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0È�',	'FAB--1108U',	'',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'FAB.300',	'',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0Ès',	'FG',	'Finished goods',	'FG',	'FG',	'',	3,	'VATHH0',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0~�',	'FINISH',	'FINISH',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0Èu',	'FOAM',	'FOAM',	'PACK',	'PACKING',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	1),
('\0\0\0\0Èv',	'HARDWARE',	'Hardware items',	'HARDWARE',	'HARDWARE',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	1),
('\0\0\0\0È�',	'HARDWARE1',	'Hardware items',	'HARDWARE',	'HARDWARE',	'',	3,	'VATHH10',	0.00000000000000000000,	'M',	'M',	1,	1),
('\0\0\0\0Èw',	'LABEL',	'Label-tem',	'PACK',	'PACKING',	'',	3,	'NOVAT',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0È�',	'LEATHER',	'',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'LICENSE',	'License & Others',	'SUB',	'SUB',	'',	3,	'NOVAT',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0È�',	'MCHI',	'MACHINE',	'MAC',	'MCHI',	'',	3,	'',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0È�',	'MDF',	'MDF - VAN',	'MDF',	'MDF',	'',	3,	'VATHH0',	0.00000000000000000000,	'M3',	'M3',	1,	1),
('\0\0\0\0È�',	'NAILER',	'',	'',	'',	'',	3,	'',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0È�',	'OS',	'Outsourcing',	'FG',	'FG',	'',	3,	'VATHH0',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0��',	'OTHER',	'OTHER',	'',	'',	'',	0,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0;\0C',	'PACKING',	'PACKING',	'PACK',	'PACKING',	'',	3,	'NOVAT',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0Èx',	'PACKOTHER',	'PACKING-OTHERS',	'',	'PACKING',	'',	3,	'NOVAT',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0È�',	'PB',	'PARTICAL BOARD/PLY WOOD',	'PB',	'PB',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	1),
('\0\0\0\0È�',	'PRESSB654',	'1301K',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0!�',	'REPAIR',	'CUTTER REPAIR',	'COMM',	'COMM',	'',	3,	'NOVAT',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0Èz',	'SAMPLE',	'Sample items',	'SAMPLE',	'SAMPLE',	'',	3,	'VATHH0',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0È{',	'SANDING',	'Sanding',	'SANDING',	'SANDING',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0È�',	'SLEIGHVN',	'1111Z',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0���',	'SPAR',	'Spare parts/tool',	'TOOL',	'SPAR-TOOL',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0���',	'SPAR OTHER',	'Spare parts',	'TOOL',	'SPAR-TOOL',	'',	3,	'VATHH10',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0�f',	'SPAR-OLD',	'TOOL',	'COMM',	'COMM',	'',	3,	'NOVAT',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'SUB',	'Sub Assy',	'SUB',	'SUB',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	1,	0),
('\0\0\0\0È�',	'TNCB',	'thanh noÃ¡i chaÃ¢n baÃ¸n',	'HARDWARE',	'HARDWARE',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0��z',	'TOOL',	'Tools',	'TOOL',	'SPAR-TOOL',	'',	3,	'VATHH10',	0.00000000000000000000,	'PCS',	'PCS',	0,	0),
('\0\0\0\0È~',	'VENEER',	'',	'TOOL',	'VENEER',	'',	3,	'VATHH0',	0.00000000000000000000,	'SF',	'SF',	1,	1),
('\0\0\0\0È�',	'VN B572',	'1111L',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'VN SLEI',	'',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'VN--B672',	'111L-1',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È�',	'WDPL',	'PLYWOOD',	'',	'',	'',	3,	'',	0.00000000000000000000,	'',	'',	0,	0),
('\0\0\0\0È',	'WOOD',	'Wood material',	'WOOD',	'WOOD',	'',	3,	'VATHH0',	0.00000000000000000000,	'M3',	'M3',	1,	1),
('\0\0\0\0Èt',	'WOOD#',	'WOOD OTHERS',	'WOOD#',	'WOOD#',	'',	3,	'VATHH10',	0.00000000000000000000,	'M3',	'M3',	1,	1);

DROP TABLE IF EXISTS `item_groups`;
CREATE TABLE `item_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `item_groups` (`id`, `name`) VALUES
(1,	'1807'),
(2,	'488'),
(3,	'B5920'),
(4,	'134'),
(5,	'3000'),
(6,	'919'),
(7,	'920'),
(8,	'B5907'),
(9,	'D5907'),
(10,	'W5907'),
(11,	'6231'),
(12,	'D5932'),
(13,	'451'),
(14,	'5146'),
(15,	'BC9005'),
(16,	'B690'),
(17,	'B670'),
(18,	'B672'),
(19,	'872'),
(20,	'B712'),
(21,	'B714'),
(22,	'654-E'),
(23,	'654-G'),
(24,	'654-E&G'),
(25,	'CATALINA-SW'),
(26,	'CATALINA-CH'),
(27,	'OOHLALA-SNW'),
(28,	'5311B'),
(29,	'5311D'),
(30,	'1840'),
(31,	'1851'),
(32,	'1005'),
(33,	'1015'),
(34,	'T5907'),
(35,	'D5921'),
(36,	'B673'),
(37,	'4146'),
(38,	'513'),
(39,	'5000'),
(40,	'B693'),
(41,	'B647'),
(42,	'B5934'),
(43,	'B583'),
(44,	'B614'),
(45,	'8844'),
(46,	'CATALINA-CC'),
(47,	'JULIETTE'),
(48,	'LILAC-VSW'),
(49,	'LILAC-AG'),
(50,	'CATALINA-BF'),
(51,	'H5907'),
(52,	'9951'),
(53,	'1909'),
(54,	'400'),
(55,	'402'),
(56,	'403'),
(57,	'710'),
(58,	'LILAC-E'),
(59,	'JULIETTE-SW'),
(60,	'ULTIMATE-SW'),
(61,	'ULTIMATE-DE'),
(62,	'ULTIMATE-BG'),
(63,	'AUBURN-SW'),
(64,	'102'),
(65,	'1010'),
(66,	'SOMERSET'),
(67,	'1012'),
(68,	'1963(1)'),
(69,	'1018'),
(70,	'AUBURN-WB'),
(71,	'CATALINA-CCWB'),
(72,	'CATALINA-CHWB'),
(73,	'JEWELRY'),
(74,	'1963'),
(75,	'1897'),
(76,	'1000'),
(77,	'1003'),
(78,	'99090'),
(79,	'405'),
(80,	'ELLIPSE'),
(81,	'COLETTE'),
(82,	'MINNIE-SNW'),
(83,	'354'),
(84,	'554'),
(85,	'KL3903'),
(86,	'B643'),
(87,	'5757'),
(88,	'ROSANNA'),
(89,	'9016MB'),
(90,	'T2081'),
(91,	'MINNIE'),
(92,	'SWIVEL'),
(93,	'RETRO'),
(94,	'BRAZILIAN'),
(95,	'ROSANNA-E'),
(96,	'B715'),
(97,	'KL3901'),
(98,	'2811'),
(99,	'1216'),
(100,	'MINNIE-PL'),
(101,	'RETRO-WN'),
(102,	'ROSANNA-G'),
(103,	'CAMPBELL-SC'),
(104,	'KL3931'),
(105,	'KL3895'),
(106,	'CARVED'),
(107,	'ENTRY');

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `timestamp` longblob NOT NULL,
  `Code` varchar(10) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Default Bin Code` varchar(20) NOT NULL,
  `Name 2` varchar(50) NOT NULL,
  `Address` varchar(50) NOT NULL,
  `Address 2` varchar(50) NOT NULL,
  `City` varchar(30) NOT NULL,
  `Phone No_` varchar(30) NOT NULL,
  `Phone No_ 2` varchar(30) NOT NULL,
  `Telex No_` varchar(30) NOT NULL,
  `Fax No_` varchar(30) NOT NULL,
  `Contact` varchar(50) NOT NULL,
  `Post Code` varchar(20) NOT NULL,
  `County` varchar(30) NOT NULL,
  `E-Mail` varchar(80) NOT NULL,
  `Home Page` varchar(90) NOT NULL,
  `Country_Region Code` varchar(10) NOT NULL,
  `Use As In-Transit` tinyint(4) NOT NULL,
  `Require Put-away` tinyint(4) NOT NULL,
  `Require Pick` tinyint(4) NOT NULL,
  `Cross-Dock Due Date Calc_` varchar(32) NOT NULL,
  `Use Cross-Docking` tinyint(4) NOT NULL,
  `Require Receive` tinyint(4) NOT NULL,
  `Require Shipment` tinyint(4) NOT NULL,
  `Bin Mandatory` tinyint(4) NOT NULL,
  `Directed Put-away and Pick` tinyint(4) NOT NULL,
  `Default Bin Selection` int(11) NOT NULL,
  `Outbound Whse_ Handling Time` varchar(32) NOT NULL,
  `Inbound Whse_ Handling Time` varchar(32) NOT NULL,
  `Put-away Template Code` varchar(10) NOT NULL,
  `Use Put-away Worksheet` tinyint(4) NOT NULL,
  `Pick According to FEFO` tinyint(4) NOT NULL,
  `Allow Breakbulk` tinyint(4) NOT NULL,
  `Bin Capacity Policy` int(11) NOT NULL,
  `Open Shop Floor Bin Code` varchar(20) NOT NULL,
  `To-Production Bin Code` varchar(20) NOT NULL,
  `From-Production Bin Code` varchar(20) NOT NULL,
  `Adjustment Bin Code` varchar(20) NOT NULL,
  `Always Create Put-away Line` tinyint(4) NOT NULL,
  `Always Create Pick Line` tinyint(4) NOT NULL,
  `Special Equipment` int(11) NOT NULL,
  `Receipt Bin Code` varchar(20) NOT NULL,
  `Shipment Bin Code` varchar(20) NOT NULL,
  `Cross-Dock Bin Code` varchar(20) NOT NULL,
  `To-Assembly Bin Code` varchar(20) NOT NULL,
  `From-Assembly Bin Code` varchar(20) NOT NULL,
  `Asm_-to-Order Shpt_ Bin Code` varchar(20) NOT NULL,
  `Base Calendar Code` varchar(10) NOT NULL,
  `Use ADCS` tinyint(4) NOT NULL,
  `Validating for Copy Data` tinyint(4) NOT NULL,
  `DF Return Reason Code` varchar(10) NOT NULL,
  `DF Bin Code` varchar(10) NOT NULL,
  PRIMARY KEY (`Code`),
  UNIQUE KEY `$1` (`Name`,`Code`),
  UNIQUE KEY `$2` (`Use As In-Transit`,`Bin Mandatory`,`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `location` (`timestamp`, `Code`, `Name`, `Default Bin Code`, `Name 2`, `Address`, `Address 2`, `City`, `Phone No_`, `Phone No_ 2`, `Telex No_`, `Fax No_`, `Contact`, `Post Code`, `County`, `E-Mail`, `Home Page`, `Country_Region Code`, `Use As In-Transit`, `Require Put-away`, `Require Pick`, `Cross-Dock Due Date Calc_`, `Use Cross-Docking`, `Require Receive`, `Require Shipment`, `Bin Mandatory`, `Directed Put-away and Pick`, `Default Bin Selection`, `Outbound Whse_ Handling Time`, `Inbound Whse_ Handling Time`, `Put-away Template Code`, `Use Put-away Worksheet`, `Pick According to FEFO`, `Allow Breakbulk`, `Bin Capacity Policy`, `Open Shop Floor Bin Code`, `To-Production Bin Code`, `From-Production Bin Code`, `Adjustment Bin Code`, `Always Create Put-away Line`, `Always Create Pick Line`, `Special Equipment`, `Receipt Bin Code`, `Shipment Bin Code`, `Cross-Dock Bin Code`, `To-Assembly Bin Code`, `From-Assembly Bin Code`, `Asm_-to-Order Shpt_ Bin Code`, `Base Calendar Code`, `Use ADCS`, `Validating for Copy Data`, `DF Return Reason Code`, `DF Bin Code`) VALUES
('\0\0\0\0\\�',	'GCSL 1',	'Outsourcing SanLim 1',	'',	'',	'SANLIM 1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	0,	0,	0,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	''),
('\0\0\0\0]',	'GCSL 2',	'Outsourcing SanLim 2',	'',	'',	'SANLIM 2',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	0,	0,	0,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	''),
('\0\0\0\0]�',	'IN-GC',	'In-Transit Outsourcing',	'',	'',	'SANLIM',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	1,	0,	0,	'',	0,	0,	0,	0,	0,	0,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	''),
('\0\0\0\0};�',	'INTRAN',	'In-Transit',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	1,	0,	0,	'',	0,	0,	0,	0,	0,	0,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	''),
('\0\0\0\04��',	'NOOK SL1',	'NOOK SL1',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	1,	0,	1,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'NOOK',	'NO01'),
('\0\0\0\04�6',	'NOOK SL2',	'NOOK SL2',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	1,	0,	1,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'NOOK',	'NO02'),
('\0\0\0\0�',	'SANLIM 1',	'SanLim 1 warehouse',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	1,	0,	1,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	''),
('\0\0\0\0��',	'SANLIM 2',	'SanLim 2 warehouse',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	1,	0,	1,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	''),
('\0\0\0\0�',	'TF',	'TRANSFER MATERIAL',	'',	'',	'SL2',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	'',	0,	0,	0,	'',	0,	0,	0,	0,	0,	0,	'',	'',	'',	0,	0,	0,	0,	'',	'',	'',	'',	0,	0,	0,	'',	'',	'',	'',	'',	'',	'',	0,	0,	'',	'');

DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base',	1481255102),
('m130524_201442_init',	1481255186),
('m140506_102106_rbac_init',	1481531192);

DROP TABLE IF EXISTS `number_container`;
CREATE TABLE `number_container` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number_container` float DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `month_year` varchar(255) DEFAULT NULL,
  `location_code` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `number_container` (`id`, `number_container`, `date`, `month_year`, `location_code`) VALUES
(1,	359,	'2016-06-30 09:10:05',	'6/2016',	'SANLIM 1'),
(2,	126.18,	'2016-06-30 09:10:11',	'6/2016',	'SANLIM 2'),
(3,	381,	'2016-07-31 09:10:45',	'7/2016',	'SANLIM 1'),
(4,	157.05,	'2016-07-31 09:05:04',	'7/2016',	'SANLIM 2'),
(5,	392,	'2016-08-30 09:05:34',	'8/2016',	'SANLIM 1'),
(6,	171.2,	'2016-08-31 09:06:27',	'8/2016',	'SANLIM 2'),
(7,	375,	'2016-09-30 09:06:46',	'9/2016',	'SANLIM 1'),
(8,	157,	'2016-09-30 09:07:24',	'9/2016',	'SANLIM 2'),
(9,	401,	'2016-10-31 09:07:46',	'10/2016',	'SANLIM 1'),
(10,	160,	'2016-10-31 09:08:01',	'10/2016',	'SANLIM 2'),
(11,	391,	'2016-11-30 09:08:39',	'11/2016',	'SANLIM 1'),
(12,	161,	'2016-11-30 09:08:50',	'11/2016',	'SANLIM 2');

DROP TABLE IF EXISTS `purchaser`;
CREATE TABLE `purchaser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `purchase_code` varchar(255) DEFAULT NULL,
  `disable` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `purchaser` (`id`, `user_name`, `name`, `purchase_code`, `disable`) VALUES
(1,	'LINHDHT',	'Äáº­u HÃ  TÃ¹ng Linh',	'PUR 5',	0),
(2,	'TRANCM',	'ChÃ­ Má»¹ TrÃ¢n',	'PUR2',	NULL),
(3,	'LOANNTK',	'Nguyá»…n Thá»‹ Kiá»u Loan',	'PUR2',	NULL),
(4,	'TOAINTM',	'Nguyá»…n Thá»‹ Minh Toáº¡i',	'PUR5',	NULL),
(5,	'TRANGHT',	'Huá»³nh Thá»‹ Trang',	'PUR4',	NULL),
(6,	'ANHTMT',	'Há»“ Thá»‹ Má»™ng ThiÃªn Ã‚n',	'PUR4',	NULL),
(7,	'PHUONGDTL',	'ÄÃµ Thá»‹ Lan PhÆ°Æ¡ng',	NULL,	NULL);

DROP TABLE IF EXISTS `purchase_order`;
CREATE TABLE `purchase_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_name` varchar(255) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `required_ship_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `factory_confirm_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `total_qty` float DEFAULT NULL,
  `container_size` varchar(255) DEFAULT NULL,
  `total_cuft` float DEFAULT NULL,
  `desination_id` int(255) DEFAULT NULL,
  `sik` varchar(255) DEFAULT NULL,
  `ik_item` varchar(255) DEFAULT NULL,
  `item_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ik_item` varchar(255) NOT NULL,
  `ik` varchar(255) DEFAULT NULL,
  `item_group_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `requested_date` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `assembly_date_from` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `assembly_date_to` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `warehousing_date_from` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `warehousing_date_to` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `cutting` varchar(255) DEFAULT NULL,
  `line` varchar(255) DEFAULT NULL,
  `factory` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_group_id` (`item_group_id`),
  KEY `customer_id` (`customer_id`),
  CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`item_group_id`) REFERENCES `item_groups` (`id`),
  CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`item_group_id`) REFERENCES `item_groups` (`id`),
  CONSTRAINT `schedule_ibfk_3` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `schedule` (`id`, `ik_item`, `ik`, `item_group_id`, `customer_id`, `requested_date`, `assembly_date_from`, `assembly_date_to`, `warehousing_date_from`, `warehousing_date_to`, `cutting`, `line`, `factory`) VALUES
(40,	'1701_ENTRY',	'1701S-1',	107,	2,	'2017-01-08 00:00:00',	NULL,	NULL,	'2017-01-07 00:00:00',	NULL,	'1TH(cộng mẫu)',	NULL,	NULL),
(41,	'1612_1807',	'1612U',	1,	8,	'2017-01-04 00:00:00',	NULL,	NULL,	'2017-01-08 00:00:00',	'2017-01-10 00:00:00',	'41TH',	NULL,	NULL),
(42,	'1701_488',	'1701W',	2,	6,	'2017-01-08 00:00:00',	NULL,	NULL,	'2017-01-11 00:00:00',	NULL,	'13TH PO0095179',	NULL,	NULL),
(43,	'1701_B5920',	'1701A-2',	3,	11,	'2017-01-24 00:00:00',	NULL,	NULL,	'2017-01-11 00:00:00',	NULL,	'17TH',	NULL,	NULL),
(44,	'1612_134',	'1612K',	4,	3,	'2107-01-15 00:00:00',	NULL,	NULL,	'2017-01-12 00:00:00',	NULL,	'1ST',	NULL,	NULL),
(45,	'1612_3000',	'1612W-1',	5,	7,	'2017-01-09 00:00:00',	NULL,	NULL,	'2017-01-12 00:00:00',	'2017-01-13 00:00:00',	'33th',	NULL,	NULL),
(46,	'1701_919',	'1701K',	6,	6,	'2017-01-15 00:00:00',	NULL,	NULL,	'2017-01-14 00:00:00',	NULL,	'5TH PO0095180',	NULL,	NULL),
(47,	'1701_920',	'1701K-1',	7,	6,	'2017-01-15 00:00:00',	NULL,	NULL,	'2017-01-14 00:00:00',	NULL,	'5TH PO0095180',	NULL,	NULL),
(48,	'1701_B5907',	'1701O-1',	8,	11,	'2017-01-14 00:00:00',	NULL,	NULL,	'2017-01-14 00:00:00',	NULL,	'36TH',	NULL,	NULL),
(49,	'1701_D5907',	'1701O-2',	9,	11,	'2017-01-14 00:00:00',	NULL,	NULL,	'2017-01-15 00:00:00',	NULL,	'36TH',	NULL,	NULL),
(50,	'1701_W5907',	'1701O-4',	10,	11,	'2017-01-14 00:00:00',	NULL,	NULL,	'2017-01-15 00:00:00',	NULL,	'36TH',	NULL,	NULL);

DROP TABLE IF EXISTS `status`;
CREATE TABLE `status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `status` (`id`, `name`) VALUES
(1,	'active'),
(2,	'cancel');

DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1,	'hoaht',	'm9gfRg1QTNdycDuL4ngcqU5ICMMCQjw8',	'$2y$13$r1MTKUys4XPRGozTaWM7DuXizAtnOXlmjCB2acpKTNPya5PaZ9o3.',	NULL,	'hoaht@gmail.com',	10,	1481255266,	1481255266),
(2,	'hoaht1',	'EoplG57zkAUUN_kDMGLMtRnNOh-aFcC_',	'$2y$13$MPys0/RustxkNEFcNAfjV.ztCxVNId9vwo12XKH6e55b6GDbvrYjm',	NULL,	'hoaht1@gmail.com',	10,	1481596286,	1481596286);

-- 2017-02-16 02:31:10
