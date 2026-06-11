

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";



--
--


--
--

DROP TABLE IF EXISTS `areabooking_tbl`;
CREATE TABLE IF NOT EXISTS `areabooking_tbl` (
  `areabooking_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `seatingarea_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `booking_status_` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`areabooking_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
--

DROP TABLE IF EXISTS `catbooking_tbl`;
CREATE TABLE IF NOT EXISTS `catbooking_tbl` (
  `booking_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `cat_id` int NOT NULL,
  `areabooking_id` int NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `booking_status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`booking_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
--

INSERT INTO `catbooking_tbl` (`booking_id`, `customer_id`, `cat_id`, `areabooking_id`, `booking_date`, `booking_time`, `booking_status`) VALUES
(1, 1, 1, 1, '2026-06-08', '14:30:54', 'Pending'),
(2, 1, 1, 2, '2026-06-08', '14:35:37', 'Pending'),
(3, 1, 1, 3, '2026-06-08', '14:41:42', 'Pending'),
(4, 1, 1, 3, '2026-06-08', '14:41:42', 'Pending'),
(5, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(6, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(7, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(8, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(9, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(10, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(11, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(12, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(13, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending'),
(14, 1, 1, 4, '2026-06-08', '14:42:32', 'Pending');


--
--

DROP TABLE IF EXISTS `cat_tbl`;
CREATE TABLE IF NOT EXISTS `cat_tbl` (
  `cat_id` int NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `breed` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `age` int NOT NULL,
  `gender` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
--

INSERT INTO `cat_tbl` (`cat_id`, `cat_name`, `breed`, `age`, `gender`, `img`, `description`) VALUES
(1, 'Luna', 'Ginger Cat', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\Luna.jpg', 'A friendly ginger cat who loves meeting cafe visitors.'),
(2, 'appleranger', 'puspin', 3, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\appleranger.jpg', 'A sweet and playful puspin who enjoys exploring every corner.'),
(3, 'biscuit', 'Puspin', 1, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\biscuit.jpg', 'A curious kitten with a gentle and affectionate personality.'),
(4, 'blacky', 'puspin', 3, 'female', 'C:\\wamp64\\www\\cafe\\Cat\\blacky.jpg', 'A calm black cat who enjoys relaxing in cozy spaces.'),
(5, 'brownie', 'puspin', 1, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\brownie.jpg', 'A playful young cat with endless energy and charm.'),
(6, 'chicken', 'sphynx', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\chicken.jpg', 'A unique sphynx cat who loves warm spots and attention.'),
(7, 'coco', 'puspin', 4, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\r\n\\coco.jpg', 'A relaxed and friendly cat who enjoys quiet afternoons.'),
(8, 'dahyun', 'puspin', 3, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\dahyun.jpg', 'A lovable companion who greets guests with curiosity.'),
(9, 'el gato', 'puspin', 5, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\el gato.jpg', 'A confident cat with a big personality and a playful spirit.'),
(10, 'gatito', 'puspin', 3, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\gatito.jpg', 'A gentle female cat who loves cuddles and treats.'),
(11, 'holy', 'puspin', 5, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\holy.jpg', 'A graceful cat known for her calm and loving nature.'),
(12, 'kit', 'puspin', 1, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\kit.jpg', 'An energetic young cat who enjoys chasing toys.'),
(13, 'loki', 'puspin', 6, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\loki.jpg', 'An adventurous cat who is always ready to explore.'),
(14, 'mango', 'puspin', 2, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\mango.jpg', 'A sweet and friendly cat with a cheerful personality.'),
(15, 'marshmallow', 'puspin', 2, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\marshmallow.jpg', 'A fluffy cat who enjoys naps and cozy blankets.'),
(16, 'meowgry', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\meowgry.jpg', 'A curious foodie who is always looking for snacks.'),
(17, 'milo', 'puspin', 4, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\milo.jpg', 'A gentle and affectionate cat who loves human company.'),
(18, 'mingming', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\mingming', 'A playful cat who brightens everyones day.'),
(19, 'mochi', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\mochi.jpg', 'A charming cat with a soft coat and friendly attitude.'),
(20, 'neko', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\neko.jpg', 'A calm and observant cat who enjoys peaceful surroundings.'),
(21, 'nugget', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\nugget.jpg', 'A small but brave cat full of energy and curiosity.'),
(22, 'oreo', 'Puspin', 1, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\oreo.jpg', 'A playful black and white cat who loves attention.'),
(23, 'peanut', 'puspin', 1, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\peanut.jpg', 'A tiny cat with a big personality and endless charm.'),
(24, 'pumpkin', 'puspin', 3, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\pumpkin.jpg', 'A cheerful orange cat who enjoys meeting new people.'),
(25, 'sailormoon', 'puspin', 1, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\sailormoon.jpg', 'A playful young cat inspired by adventure and fun.'),
(26, 'sushi', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\sushi.jpg', 'A relaxed cat who enjoys long naps and quiet company.'),
(27, 'tiger', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\tiger.jpg', 'A striped cat with a confident and friendly nature.'),
(28, 'tik', 'puspin', 5, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\tik.jpg', 'A wise and calm cat who enjoys watching the world go by.'),
(29, 'toungi', 'puspin', 2, 'Female', 'C:\\wamp64\\www\\cafe\\Cat\\toungi.jpg', 'A sweet female cat who loves affection and treats.'),
(30, 'whitey', 'puspin', 2, 'Male', 'C:\\wamp64\\www\\cafe\\Cat\\whitey.jpg', 'A handsome white cat with a gentle personality.');


--
--

DROP TABLE IF EXISTS `customer_tbl`;
CREATE TABLE IF NOT EXISTS `customer_tbl` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `Fname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Lname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact` int NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci DEFAULT 'user',
  PRIMARY KEY (`customer_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
--

INSERT INTO `customer_tbl` (`customer_id`, `Fname`, `Mname`, `Lname`, `email`, `contact`, `password`, `role`) VALUES
(1, 'aya', 'Bobadilla', 'Obena', 'abc@gmail.com', 912345678, '$2y$10$7oGRg/vi5nrG/rtdnR5c..pM7xjaZBp99Aj0PdqKzVXaCsdN28TY.', 'user'),
(2, 'Jhon ', '', 'Mackay', 'mackay@gmail.com', 993736322, '$2y$10$AOqPcIdrB4Zw9n6Tfi6WW.4p98PmBAr0vGE5GysMHSAm/TiZ8O2UC', 'user'),
(3, 'Jhon', 'Drei', 'Mackay', 'mackayjhondrei632@gmail.com', 2147483647, '$2y$10$NQKlaE.hM/PQzZCHF72Dk.z8Q7a7enajm/9RtMPItHmj3XUjlIJRS', 'user');


--
--

DROP TABLE IF EXISTS `delivery_tbl`;
CREATE TABLE IF NOT EXISTS `delivery_tbl` (
  `delivery_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `delivery_address` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipient_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` int NOT NULL,
  `delivery_status` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`delivery_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
--

DROP TABLE IF EXISTS `menuitem_tbl`;
CREATE TABLE IF NOT EXISTS `menuitem_tbl` (
  `item_id` int NOT NULL AUTO_INCREMENT,
  `item_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
--

INSERT INTO `menuitem_tbl` (`item_id`, `item_name`, `category`, `price`, `image`, `description`) VALUES
(1, 'Lotus Biscoff Cookie', 'Cookie', 125.00, 'Cookies/Lotus.jpg', 'Soft-baked brown butter cookie loaded with crushed...'),
(2, 'Pink Birthday Cake Cookie', 'Cookie', 110.00, 'Cookies/birthday.jpg', 'Funfetti-speckled sweet cream cookie topped with s...'),
(3, 'White Chocolate Macadamia Cookie', 'Cookie', 130.00, 'Cookies/White.jpg', 'Classic rich recipe featuring buttery macadamia nu...'),
(4, 'Cookies & Cream Cookie', 'Cookie', 120.00, 'Cookies/Cookies & Cream.jpg', 'A dark cocoa dough base folded with crushed Oreo p...'),
(5, 'Classic Chocolate Chip Cookie', 'Cookie', 95.00, 'Cookies/classic.jpg', 'Our timeless recipe, chewy on the inside with a cr...'),
(6, 'Frosted Animal Sugar Cookie', 'Cookie', 110.00, 'Cookies/frosted.jpg', 'Nostalgic melt-in-your-mouth sugar cookie topped w...'),
(7, 'Rocky Road Cookie', 'Cookie', 130.00, 'Cookies/rocky road.jpg', 'Double chocolate dough packed with mini marshmallo...'),
(8, 'Matcha White Chocolate Cookie', 'Cookie', 125.00, 'Cookies/matcha.jpg', 'Earthy premium Japanese Uji matcha base beautifull...'),
(9, 'Strawberry Cream Cookie', 'Cookie', 120.00, 'Cookies/strawberry.jpg', 'Delicate strawberry infused cookie dough layered w...'),
(10, 'Red Velvet Cookie', 'Cookie', 120.00, 'Cookies/red velvet.jpg', 'Striking crimson cocoa cookie with a soft, velvet ...'),
(11, 'Nutella Swirl Brownie', 'Brownie', 125.00, 'Brownies/Nutella.jpg', 'Rich fudge brownie swirled heavily with smooth haz...'),
(12, 'Ube White Chocolate Brownie', 'Brownie', 130.00, 'Brownies/Ube.jpg', 'Vibrant purple yam brownie providing a unique swee...'),
(13, 'Matcha Walnut Brownie', 'Brownie', 125.00, 'Brownies/Matcha.jpg', 'Fudgy premium green tea matcha blondie loaded up w...'),
(14, 'Cheesecake Brownie', 'Brownie', 130.00, 'Brownies/Cheesecake.jpg', 'Decadent dark chocolate brownie beautifully marble...'),
(15, 'Classic Fudge Brownie', 'Brownie', 105.00, 'Brownies/Classic.jpg', 'Our ultra-dense signature dark chocolate fudge bro...'),
(16, 'Salted Caramel Brownie', 'Brownie', 125.00, 'Brownies/Salted Caramel.jpg', 'Rich chocolate brownie layer smothered in an artis...'),
(17, 'Cookies & Cream Brownie', 'Brownie', 125.00, 'Brownies/Cookies & Cream.jpg', 'Deep dark cocoa brownie embedded with whole crushe...'),
(18, 'Smores Brownie', 'Brownie', 130.00, 'Brownies/Smore.jpg', 'Fudgy brownie base topped with a layer of honey gr...'),
(19, 'Red Velvet Brownie', 'Brownie', 125.00, 'Brownies/Red velvet.jpg', 'Stunning crimson fudgy cocoa brownie layered with...'),
(20, 'Walnut Fudge Brownie', 'Brownie', 115.00, 'Brownies/Walnut.jpg', 'A classic dark chocolate fudge brownie packed with...'),
(21, 'Carrot Cream Cake', 'Cake', 185.00, 'cake/Carrot.jpg', 'Spiced, moist cake layer packed with grated carrot...'),
(22, 'Chocolate Strawberry Cake', 'Cake', 210.00, 'cake/Chocolate Strawberry.jpg', 'Rich dark chocolate cake sponge separated by fresh...'),
(23, 'Red Velvet Cake', 'Cake', 185.00, 'cake/Red velvet.jpg', 'Classic light cocoa layer cake tinted a gorgeous r...'),
(24, 'Strawberry Shortcake', 'Cake', 195.00, 'cake/Strawberry.jpg', 'Light, airy vanilla sponge layered cleanly with fr...'),
(25, 'Chocolate Layer Cake', 'Cake', 210.00, 'cake/Chocolate.jpg', 'Decadent, sky-high triple chocolate sponge layered...'),
(26, 'Ube Cheesecake', 'Cake', 220.00, 'cake/Ube cheese cake.jpg', 'Creamy New York style cheesecake infused with auth...'),
(27, 'Classic Tiramisu', 'Cake', 195.00, 'cake/Tiramisu.jpg', 'Espresso-soaked ladyfingers layered elegantly with...'),
(28, 'Blueberry Cream Cake', 'Cake', 185.00, 'cake/Blueberry Cream.jpg', 'Sweet vanilla bean sponge cake layered with wild b...'),
(29, 'Blueberry Cheesecake', 'Cake', 220.00, 'cake/Blueberry cheesecake.jpg', 'Dense, velvety classic baked cream cheese cake cro...'),
(30, 'Matcha Layer Cake', 'Cake', 195.00, 'cake/Matcha.jpg', 'Delicate, earthy green tea sponge layers frosted l...'),
(31, 'Matcha Latte', 'Coffee', 175.00, 'Coffee/Matcha.jpg', 'Whisked premium ceremonial Japanese matcha green t...'),
(32, 'Cafe Latte', 'Coffee', 155.00, 'Coffee/Cafe Latte.jpg', 'Our rich signature espresso blend mellowed with si...'),
(33, 'Espresso', 'Coffee', 110.00, 'Coffee/Espresso.jpg', 'A concentrated, full-bodied double shot of our pre...'),
(34, 'Cafee Mocha', 'Coffee', 170.00, 'Coffee/Mocha.jpg', 'A perfect balance of robust espresso and decadent...'),
(35, 'Flat White', 'Coffee', 160.00, 'Coffee/Flat White.jpg', 'Expertly prepared double espresso shot combined wi...'),
(36, 'Iced Latte', 'Coffee', 165.00, 'Coffee/Iced .jpg', 'Chilled signature espresso served over ice, topped...'),
(37, 'Cappuccino', 'Coffee', 155.00, 'Coffee/Cappuccino.jpg', 'A balanced combination of bold espresso, silky ste...'),
(38, 'Caramel Macchiato', 'Coffee', 180.00, 'Coffee/Caramel.jpg', 'Creamy milk and espresso topped with sweet caramel...'),
(39, 'Dalgona Coffee', 'Coffee', 175.00, 'Coffee/Dalgona.jpg', 'Creamy milk and espresso topped with sweet caramel...'),
(40, 'Americano', 'Coffee', 150.00, 'Coffee/Americano.jpg', 'Smooth espresso blended with hot water for a bold,...');


--
--

DROP TABLE IF EXISTS `orderdetails_tbl`;
CREATE TABLE IF NOT EXISTS `orderdetails_tbl` (
  `orderDeatails_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `sub_total` int NOT NULL,
  PRIMARY KEY (`orderDeatails_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
--

INSERT INTO `orderdetails_tbl` (`orderDeatails_id`, `order_id`, `item_id`, `quantity`, `sub_total`) VALUES
(1, 1, 11, 5, 625),
(2, 2, 1, 1, 125),
(3, 3, 11, 1, 125),
(4, 4, 11, 1, 125),
(5, 5, 11, 3, 375);


--
--

DROP TABLE IF EXISTS `order_tbl`;
CREATE TABLE IF NOT EXISTS `order_tbl` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `order_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `total_amount` decimal(10,4) NOT NULL,
  `order_status` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
--

INSERT INTO `order_tbl` (`order_id`, `customer_id`, `order_type`, `order_date`, `total_amount`, `order_status`) VALUES
(1, 1, 'Pre-order', '2026-06-09', 625.0000, 'Pending'),
(2, 1, 'Pre-order', '2026-06-10', 125.0000, 'Pending'),
(3, 1, 'Pre-order', '2026-06-10', 125.0000, 'Pending'),
(4, 1, 'Pre-order', '2026-06-24', 125.0000, 'Pending'),
(5, 1, 'Pre-order', '2026-06-09', 375.0000, '0');

-- --------------------------------------------------------

--
--

DROP TABLE IF EXISTS `payment_tbl`;
CREATE TABLE IF NOT EXISTS `payment_tbl` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash',
  `amount` decimal(10,2) NOT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
--

INSERT INTO `payment_tbl` (`payment_id`, `order_id`, `customer_id`, `payment_method`, `amount`, `payment_status`, `payment_date`) VALUES
(1, 1, 1, 'Cash', 625.00, 'Pending', '2026-06-08 06:30:57'),
(2, 2, 1, 'Cash', 125.00, 'Pending', '2026-06-08 06:35:39'),
(3, 3, 1, 'Cash', 125.00, 'Pending', '2026-06-08 06:41:47'),
(4, 4, 1, 'Cash', 125.00, 'Pending', '2026-06-08 06:42:33'),
(5, 5, 1, 'Cash', 375.00, 'Pending', '2026-06-08 07:36:22');


--
--

DROP TABLE IF EXISTS `seatingarea_tbl`;
CREATE TABLE IF NOT EXISTS `seatingarea_tbl` (
  `seatingarea_id` int NOT NULL AUTO_INCREMENT,
  `seat_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `floor` int NOT NULL COMMENT '1 = Ground, 2 = Mezzanine, 3 = Third Floor',
  `capacity` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Available','Occupied') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`seatingarea_id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--

INSERT INTO `seatingarea_tbl` (`seatingarea_id`, `seat_name`, `floor`, `capacity`, `description`, `status`) VALUES
(1, 'Forest Table 01', 1, '2 Guests', 'Round wooden base near scratch trees', 'Occupied'),
(2, 'Forest Table 02', 1, '2 Guests', 'Standard table alongside climbing paths', 'Available'),
(3, 'Forest Main Sofa 03', 1, '4 Guests', 'Plush fabric layout for small groups', 'Available'),
(4, 'Forest Pod 04', 1, '2 Guests', 'Secluded window viewing hub', 'Available'),
(5, 'Hearth Ring 05', 1, '3 Guests', 'Circular layout surrounding main play tree', 'Available'),
(6, 'Hearth Ring 06', 1, '3 Guests', 'Circular layout near warm sleep beds', 'Available'),
(7, 'Hearth Ring 07', 1, '2 Guests', 'Cozy low-profile floor setup', 'Available'),
(8, 'Espresso Bar 08', 1, '1 Guest', 'High stool bar overlooking barista bay', 'Available'),
(9, 'Espresso Bar 09', 1, '1 Guest', 'High stool bar facing path corridor', 'Available'),
(10, 'Grand Box 10', 1, '5 Guests', 'Premium wide family velvet sofa arrangement', 'Available'),
(11, 'Sky Overlook 11', 2, '2 Guests', 'Hanging platform rail view', 'Available'),
(12, 'Sky Overlook 12', 2, '2 Guests', 'Bridge platform dynamic rail view', 'Available'),
(13, 'Sky Overlook 13', 2, '2 Guests', 'Suspended path viewing arrangement', 'Available'),
(14, 'Sky Overlook 14', 2, '2 Guests', 'End runway overlook next to high wall ramps', 'Available'),
(15, 'Library Velvet Pod 15', 2, '3 Guests', 'Deep-set velvet relaxation pod', 'Available'),
(16, 'Library Nook 16', 2, '2 Guests', 'Quiet corner table surrounded by bookshelves', 'Available'),
(17, 'Library Nook 17', 2, '2 Guests', 'Reading layout with structural cat cubbies', 'Available'),
(18, 'Sun Panoramic Deck 18', 2, '4 Guests', 'Sofa assembly looking through secondary glass', 'Available'),
(19, 'Sun Oasis 19', 2, '2 Guests', 'Bright window sunspot table arrangement', 'Available'),
(20, 'Sun Oasis 20', 2, '2 Guests', 'Bright window corner layout facing plant shelves', 'Available'),
(21, 'Greenhouse Table 21', 3, '2 Guests', 'Tempered glass roof perimeter setup', 'Available'),
(22, 'Greenhouse Table 22', 3, '2 Guests', 'Mid-aisle layout alongside hanging vines', 'Available'),
(23, 'Greenhouse Table 23', 3, '2 Guests', 'Rear solarium corner spot layout', 'Available'),
(24, 'Solarium Glazed Sofa 24', 3, '4 Guests', 'Premium cushioned lounge layout under sky lights', 'Available'),
(25, 'Botanical Nook 25', 3, '2 Guests', 'Flora surrounded conversation spot', 'Available'),
(26, 'Botanical Nook 26', 3, '2 Guests', 'Flora surrounded corner setup', 'Available'),
(27, 'Cloud Counter Top 27', 3, '1 Guest', 'High platform observation window desk', 'Available'),
(28, 'Cloud Counter Top 28', 3, '1 Guest', 'High platform skylight view desk', 'Available'),
(29, 'Zen Tatami Mat 29', 3, '4 Guests', 'Traditional low wood layout with floor cushions', 'Available'),
(30, 'Zen Pebble Table 30', 3, '2 Guests', 'Polished stone top matching surrounding water feature', 'Available');
COMMIT;

