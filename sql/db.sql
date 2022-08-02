CREATE TABLE IF NOT EXISTS `mc_brand` (
     `id_bd` int(7) UNSIGNED NOT NULL AUTO_INCREMENT,
     `img_bd` varchar(125) DEFAULT NULL,
     `menu_bd` smallint(1) UNSIGNED DEFAULT '1',
     `order_bd` smallint(5) UNSIGNED NOT NULL DEFAULT '0',
     `date_register` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     PRIMARY KEY (`id_bd`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `mc_brand_content` (
     `id_content` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
     `id_bd` int(7) UNSIGNED NOT NULL,
     `id_lang` smallint(3) UNSIGNED NOT NULL DEFAULT '1',
     `name_bd` varchar(150) DEFAULT NULL,
     `title_bd` varchar(150) DEFAULT NULL,
     `url_bd` varchar(150) DEFAULT NULL,
     `resume_bd` text,
     `content_bd` text,
     `alt_img` varchar(70) DEFAULT NULL,
     `title_img` varchar(70) DEFAULT NULL,
     `caption_img` varchar(125) DEFAULT NULL,
     `seo_title_bd` varchar(180) DEFAULT NULL,
     `seo_desc_bd` text,
     `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `published_bd` smallint(1) NOT NULL DEFAULT '0',
     PRIMARY KEY (`id_content`),
     KEY `id_bd` (`id_bd`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

ALTER TABLE `mc_brand_content`
    ADD CONSTRAINT `mc_brand_content_ibfk_1` FOREIGN KEY (`id_bd`) REFERENCES `mc_brand` (`id_bd`) ON DELETE CASCADE ON UPDATE CASCADE;

CREATE TABLE IF NOT EXISTS `mc_brand_data` (
      `id_data` smallint(2) UNSIGNED NOT NULL AUTO_INCREMENT,
      `id_lang` smallint(3) UNSIGNED NOT NULL,
      `name_info` varchar(30) DEFAULT NULL,
      `value_info` text,
      PRIMARY KEY (`id_data`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `mc_brand_product` (
    `id_bd_p` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_bd` int(11) UNSIGNED NOT NULL,
    `id_product` int(11) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_bd_p`),
    KEY `id_bd` (`id_bd`),
    KEY `id_product` (`id_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `mc_brand_product`
    ADD CONSTRAINT `mc_brand_product_ibfk_1` FOREIGN KEY (`id_bd`) REFERENCES `mc_brand` (`id_bd`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `mc_brand_product_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `mc_catalog_product` (`id_product`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT INTO `mc_config_img` (`id_config_img`, `module_img`, `attribute_img`, `width_img`, `height_img`, `type_img`, `resize_img`) VALUES
(NULL, 'plugins', 'brand', '256', '256', 'small', 'basic'),
(NULL, 'plugins', 'brand', '512', '512', 'medium', 'basic'),
(NULL, 'plugins', 'brand', '1200', '1200', 'large', 'basic');