CREATE TABLE `address` (
  `id_address` int(11) PRIMARY KEY AUTO_INCREMENT,
  `street` varchar(255) NOT NULL,
  `number` varchar(20) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` char(2) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `country` varchar(50) NOT NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT into `address` (`street`, `number`, `city`, `state`, `zip_code`, `country`) VALUES
('Rua A', '123', 'Cidade X', 'ST', '12345-678', 'País Y');

-- ------------------------------------------------

CREATE TABLE `account` (
  `id_account` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `admin` char(1) not null DEFAULT 'N',
  `active` char(1) not null DEFAULT 'Y',
  `seller` char(1) not null DEFAULT 'N',
  `id_address` int(11) not null,

  CONSTRAINT `fk_account_address` FOREIGN KEY (`id_address`) REFERENCES `address`(`id_address`),
  CONSTRAINT `unique_email` unique (`email`),
  CONSTRAINT `unique_username` unique (`username`),
  CONSTRAINT `check_phone_length` CHECK (LENGTH(`phone`) >= 8),
  CONSTRAINT `check_admin_account` CHECK (`admin` IN ('Y', 'N')),
  CONSTRAINT `check_active_account` CHECK (`active` IN ('Y', 'N')),
  CONSTRAINT `check_seller_account` CHECK (`seller` IN ('Y', 'N'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Inserindo dados da tabela `account`
--

INSERT INTO `account` (`name`, `email`, `phone`, `username`, `password`, `admin`, `active`, `seller`, `id_address`) VALUES
('Administrador', 'adm@exemplo.com', '123456789', 'Administrador', PASSWORD('adm'), 'N', 'Y', 'N',  1);

-- --------------------------------------------------------

CREATE TABLE `category` (
  `id_category` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,

  CONSTRAINT `unique_category_name` UNIQUE (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `category` (`name`) VALUES
('Escolar');

--
-- Estrutura da tabela `prod`
--

CREATE TABLE `product` (
  `id_product` int(11) PRIMARY KEY AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `sell_price` decimal(9,2) NOT NULL,
  `buy_price` decimal(8,2) null DEFAULT NULL,
  `stock` int(11) not null DEFAULT 0,
  `image` varchar(255) null DEFAULT NULL,
  `description` text null DEFAULT NULL,
  `active` char(1) not null DEFAULT 'Y',
  `id_account` int(11) not null,
  `id_category` int(11) not null,

  CONSTRAINT `fk_product_account` FOREIGN KEY (`id_account`) REFERENCES `account`(`id_account`),
  CONSTRAINT `check_sell_price_positive` CHECK (`sell_price` > 0),
  CONSTRAINT `check_buy_price_positive` CHECK (`buy_price` IS NULL OR `buy_price` > 0),
  CONSTRAINT `check_stock_non_negative` CHECK (`stock` >= 0),
  CONSTRAINT `check_active_product` CHECK (`active` IN ('Y', 'N')),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`id_category`) REFERENCES `category`(`id_category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Inserindo dados da tabela `prod`
--

INSERT INTO `product` (`name`, `sell_price`, `buy_price`, `stock`, `image`, `description`,`active`, `id_account`, `id_category`) VALUES
('Mochila', 199.00, 150.00, 10, 'mochila.jpg', 'Mochila de alta qualidade', 'Y', 1, 1);

-- ---------------------------------------------------

CREATE TABLE `sale` (
  `id_sale` int(11) PRIMARY KEY AUTO_INCREMENT,
  `sale_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_value` decimal(10,2) NOT NULL,
  `id_account` int(11) not null,

  CONSTRAINT `fk_sale_account` FOREIGN KEY (`id_account`) REFERENCES `account`(`id_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `sale_product` (
  `id_sale` int(11) not null,
  `id_product` int(11) not null,
  `quantity` int(11) not null,

  CONSTRAINT `pk_sale_product` PRIMARY KEY (`id_sale`, `id_product`),
  CONSTRAINT `fk_sale_product_sale` FOREIGN KEY (`id_sale`) REFERENCES `sale`(`id_sale`),
  CONSTRAINT `fk_sale_product_product` FOREIGN KEY (`id_product`) REFERENCES `product`(`id_product`)
);