<?php
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2022 magix-cms.com support[at]magix-cms[point]com
 #
 # OFFICIAL TEAM :
 #
 #   * Gerits Aurelien (Author - Developer) <aurelien@magix-cms.com> <contact@aurelien-gerits.be>
 #
 # Redistributions of files must retain the above copyright notice.
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU General Public License as published by
 # the Free Software Foundation, either version 3 of the License, or
 # (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU General Public License for more details.
 #
 # You should have received a copy of the GNU General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 # -- END LICENSE BLOCK -----------------------------------
 #
 # DISCLAIMER
 #
 # Do not edit or add to this file if you wish to upgrade MAGIX CMS to newer
 # versions in the future. If you wish to customize MAGIX CMS for your
 # needs please refer to http://www.magix-cms.com for more information.
 */
class plugins_brand_db {
	/**
	 * @param array $config
	 * @param null|array $params
	 * @return mixed|null
	 * @throws Exception
	 */
	public function fetchData(array $config, array $params = []) {
		$query = '';
		$dateFormat = new component_format_date();

		if ($config['context'] === 'all') {
			switch ($config['type']) {
				case 'pages':
					$limit = '';
					if($config['offset']) {
						$limit = ' LIMIT 0, '.$config['offset'];
						if(isset($config['page']) && $config['page'] > 1) {
							$limit = ' LIMIT '.(($config['page'] - 1) * $config['offset']).', '.$config['offset'];
						}
					}

					$query = "SELECT p.id_bd, c.name_bd, p.img_bd, c.content_bd, c.seo_title_bd, c.seo_desc_bd, p.menu_bd, p.date_register
						FROM mc_brand AS p
							JOIN mc_brand_content AS c USING ( id_bd )
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.id_lang = :default_lang 
							GROUP BY p.id_bd 
						ORDER BY p.order_bd".$limit;

					if(isset($config['search'])) {
						$cond = '';
						if(is_array($config['search']) && !empty($config['search'])) {
							$nbc = 1;
							foreach ($config['search'] as $key => $q) {
								if($q !== '') {
									$cond .= 'AND ';
									$p = 'p'.$nbc;
									switch ($key) {
										case 'id_bd':
										case 'menu_bd':
											$cond .= 'p.'.$key.' = :'.$p.' ';
											break;
										case 'published_bd':
											$cond .= 'c.'.$key.' = :'.$p.' ';
											break;
										case 'name_bd':
											$cond .= "c.".$key." LIKE CONCAT('%', :".$p.", '%') ";
											break;
										case 'date_register':
											$q = $dateFormat->date_to_db_format($q);
											$cond .= "p.".$key." LIKE CONCAT('%', :".$p.", '%') ";
											break;
									}
									$params[$p] = $q;
									$nbc++;
								}
							}

							$query = "SELECT p.id_bd, c.name_bd, p.img_bd, c.content_bd, c.seo_title_bd, c.seo_desc_bd, p.menu_bd, p.date_register
								FROM mc_brand AS p
									JOIN mc_brand_content AS c USING ( id_bd )
									JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
									WHERE c.id_lang = :default_lang $cond
									GROUP BY p.id_bd 
								ORDER BY p.order_bd".$limit;
						}
					}
					break;
				case 'page':
					$query = 'SELECT p.*,c.*,lang.*
							FROM mc_brand AS p
							JOIN mc_brand_content AS c USING(id_bd)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_bd = :edit';
					break;
				case 'img':
					$query = 'SELECT p.id_bd, p.img_bd FROM mc_brand AS p WHERE p.img_bd IS NOT NULL';
					break;
				case 'sitemap':
					$query = 'SELECT p.id_bd, p.img_bd, c.name_bd, c.url_bd, lang.iso_lang, c.id_lang, c.last_update
							FROM mc_brand AS p
							JOIN mc_brand_content AS c USING ( id_bd )
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.published_bd = 1 AND c.id_lang = :id_lang
							ORDER BY p.id_bd ASC';
					break;
				case 'lastPages':
					$query = "SELECT p.id_bd, c.name_bd, p.date_register
							FROM mc_brand AS p
							JOIN mc_brand_content AS c USING ( id_bd )
							JOIN mc_lang AS lang ON ( c.id_lang = lang.id_lang )
							WHERE c.id_lang = :default_lang
							GROUP BY p.id_bd 
							ORDER BY p.id_bd DESC
							LIMIT 5";
					break;
				case 'root':
					$query = 'SELECT d.name_info, d.value_info 
                                FROM mc_brand_data AS d
                                JOIN mc_lang AS lang ON(d.id_lang = lang.id_lang)
                                WHERE lang.iso_lang = :iso';
					break;
				case 'rootContent':
					$query = 'SELECT a.*
							FROM mc_brand_data AS a
							JOIN mc_lang AS lang ON(a.id_lang = lang.id_lang)';
					break;
				case 'brandLangs':
					$query = 'SELECT * 
							FROM mc_brand mb
    							LEFT JOIN mc_brand_content mbc on mb.id_bd = mbc.id_bd
    							LEFT JOIN mc_lang ml on mbc.id_lang = ml.id_lang
							WHERE mb.id_bd = :id
								AND mbc.published_bd = 1';
					break;
				case 'brands':
					$query = "SELECT
								mb.*,
								mbc.name_bd,
								mbc.title_bd,
								mbc.url_bd,
								mbc.resume_bd,
								mbc.content_bd,
								mbc.published_bd,
								mbc.last_update,
								COALESCE(mbc.alt_img, mbc.name_bd) as alt_img,
								COALESCE(mbc.title_img, mbc.alt_img, mbc.name_bd) as title_img,
								COALESCE(mbc.caption_img, mbc.title_img, mbc.alt_img, mbc.name_bd) as caption_img,
								COALESCE(mbc.seo_title_bd, mbc.name_bd) as seo_title_bd,
								COALESCE(mbc.seo_desc_bd, mbc.resume_bd) as seo_desc_bd,
								ml.id_lang,
								ml.iso_lang,
								ml.default_lang
							FROM mc_brand mb
							JOIN mc_brand_content mbc ON(mb.id_bd = mbc.id_bd) 
							JOIN mc_lang ml ON(mbc.id_lang = ml.id_lang) 
							WHERE ml.iso_lang = :iso 
							  AND mbc.published_bd = 1 
							ORDER BY mb.order_bd";
					break;
				case 'brandProducts':
					$query = 'SELECT 
							mb.*,
							mbc.*,
							mbp.*,
							mccc.name_cat, 
							mccc.url_cat, 
							mc.id_cat, 
							mcp.*, 
							mcpc.name_p,  
							mcpc.resume_p, 
							mcpc.content_p, 
							mcpc.url_p, 
							mcpc.id_lang,
							ml.iso_lang, 
							mcpc.last_update, 
							mcpi.name_img,
							COALESCE(mcpic.alt_img, mcpc.longname_p, mcpc.name_p) as alt_img,
							COALESCE(mcpic.title_img, mcpic.alt_img, mcpc.longname_p, mcpc.name_p) as title_img,
							COALESCE(mcpic.caption_img, mcpic.title_img, mcpic.alt_img, mcpc.longname_p, mcpc.name_p) as caption_img,
							mcpc.seo_title_p,
							mcpc.seo_desc_p
						FROM mc_catalog mc
						JOIN mc_catalog_cat mcc ON ( mc.id_cat = mcc.id_cat )
						JOIN mc_catalog_cat_content mccc ON ( mcc.id_cat = mccc.id_cat )
						JOIN mc_catalog_product mcp ON ( mc.id_product = mcp.id_product )
						JOIN mc_catalog_product_content mcpc ON ( mcp.id_product = mcpc.id_product )
						LEFT JOIN mc_catalog_product_img mcpi ON (mcp.id_product = mcpi.id_product)
						LEFT JOIN mc_catalog_product_img_content mcpic ON (mcpic.id_img = mcpi.id_img and mcpc.id_lang = mcpic.id_lang)
						JOIN mc_lang ml ON ( mcpc.id_lang = ml.id_lang ) AND (mccc.id_lang = ml.id_lang)
						LEFT JOIN mc_brand_product mbp on mbp.id_product = mcp.id_product
						LEFT JOIN mc_brand mb on mb.id_bd = mbp.id_bd
						LEFT JOIN mc_brand_content mbc on mb.id_bd = mbc.id_bd AND (mbc.id_lang = ml.id_lang)
						WHERE mcp.id_product NOT IN(:product_excluded) 
						  AND mbp.id_bd = :id_bd 
						  AND ml.iso_lang = :iso 
						  AND mc.default_c = 1 
						  AND (mcpi.default_img = 1 OR mcpi.default_img IS NULL)';
					break;
				case 'product':
					$query = "SELECT 
								catalog.* ,
								cat.name_cat, 
								cat.url_cat, 
								p.*, 
								pc.name_p, 
								pc.longname_p, 
								pc.resume_p, 
								pc.content_p, 
								pc.url_p, 
								pc.id_lang,
								lang.iso_lang, 
								pc.last_update, 
								img.name_img,
								COALESCE(imgc.alt_img, pc.longname_p, pc.name_p) as alt_img,
								COALESCE(imgc.title_img, imgc.alt_img, pc.longname_p, pc.name_p) as title_img,
								COALESCE(imgc.caption_img, imgc.title_img, imgc.alt_img, pc.longname_p, pc.name_p) as caption_img,
								pc.seo_title_p,
								pc.seo_desc_p
						FROM mc_catalog AS catalog
						JOIN mc_catalog_cat AS c ON ( catalog.id_cat = c.id_cat )
						JOIN mc_catalog_cat_content AS cat ON ( c.id_cat = cat.id_cat )
						JOIN mc_catalog_product AS p ON ( catalog.id_product = p.id_product )
						JOIN mc_catalog_product_content AS pc ON ( p.id_product = pc.id_product )
						LEFT JOIN mc_catalog_product_img AS img ON (p.id_product = img.id_product)
						LEFT JOIN mc_catalog_product_img_content AS imgc ON (imgc.id_img = img.id_img and pc.id_lang = imgc.id_lang)
						JOIN mc_lang AS lang ON ( pc.id_lang = lang.id_lang ) AND (cat.id_lang = lang.id_lang)
						JOIN mc_brand_product mbp ON (p.id_product = mbp.id_product AND id_bd = :id_bd)
                        WHERE lang.iso_lang = :iso 
						AND pc.published_p = 1 
						AND (img.default_img = 1 OR img.default_img IS NULL) 
						AND catalog.default_c = 1
						ORDER BY catalog.order_p";
					break;
			}

			return $query ? component_routing_db::layer()->fetchAll($query, $params) : null;
		}
		elseif ($config['context'] === 'one') {
			switch ($config['type']) {
				case 'root':
					$query = 'SELECT * FROM mc_brand ORDER BY id_bd DESC LIMIT 0,1';
					break;
				case 'content':
					$query = 'SELECT * FROM `mc_brand_content` WHERE `id_bd` = :id_bd AND `id_lang` = :id_lang';
					break;
				case 'page':
					$query = 'SELECT * FROM mc_brand WHERE `id_bd` = :id_bd';
					break;
				case 'pageLang':
					$query = 'SELECT p.*,c.*,lang.*
							FROM mc_brand AS p
							JOIN mc_brand_content AS c USING(id_bd)
							JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang)
							WHERE p.id_bd = :id
							AND lang.iso_lang = :iso';
					break;
				case 'rootContent':
					$query = 'SELECT * FROM `mc_brand_data` WHERE `id_lang` = :id_lang';
					break;
				case 'brand':
					$query = "SELECT * FROM mc_brand_product WHERE id_product = :id ORDER BY id_bd_p DESC LIMIT 0,1";
					break;
				case 'brandContent':
					$query = 'SELECT p.*,
							c.name_bd,
							c.title_bd,
							c.url_bd,
							c.resume_bd,
							c.content_bd,
							c.published_bd,
							c.last_update,
							COALESCE(c.alt_img, c.name_bd) as alt_img,
							COALESCE(c.title_img, c.alt_img, c.name_bd) as title_img,
							COALESCE(c.caption_img, c.title_img, c.alt_img, c.name_bd) as caption_img,
							c.seo_title_bd,
							c.seo_desc_bd,
							lang.id_lang,
							lang.iso_lang,
							lang.default_lang
						FROM mc_brand AS p
						JOIN mc_brand_content AS c ON(p.id_bd = c.id_bd) 
						JOIN mc_lang AS lang ON(c.id_lang = lang.id_lang) 
						WHERE p.id_bd = :id AND lang.iso_lang = :iso AND c.published_bd = 1';
					break;
				case 'productBrand':
					$query = 'SELECT * FROM mc_brand mb
								LEFT JOIN mc_brand_content mbc on mb.id_bd = mbc.id_bd
								LEFT JOIN mc_brand_product mbp on mb.id_bd = mbp.id_bd
         						LEFT JOIN mc_lang ml on mbc.id_lang = ml.id_lang
								WHERE id_product = :id_product
								AND ml.iso_lang = :iso';
					break;
			}

			return $query ? component_routing_db::layer()->fetch($query, $params) : null;
		}
	}

	/**
	 * @param array $config
	 * @param array $params
	 * @return bool|string
	 */
	public function insert(array $config, array $params = []) {
		$query = '';
		
		switch ($config['type']) {
			case 'page':
				//$query = "INSERT INTO `mc_brand`(menu_bd, order_bd, date_register) SELECT :menu_bd, COUNT(id_bd), NOW() FROM mc_brand";

				$queries = [
					['request' => 'INSERT INTO `mc_brand`(menu_bd, order_bd, date_register) SELECT :menu_bd, COUNT(id_bd), NOW() FROM mc_brand', 'params' => $params],
					['request' => 'SELECT id_bd FROM `mc_brand` ORDER BY `id_bd` DESC LIMIT 0,1', 'params' => [], 'fetch' => true]
				];
				try {
					$results = component_routing_db::layer()->transaction($queries);
					return $results[1];
				}
				catch (Exception $e) {
					return 'Exception reçue : '.$e->getMessage();
				}

				break;
			case 'content':
				$query = 'INSERT INTO `mc_brand_content`(id_bd,id_lang,name_bd,title_bd,url_bd,resume_bd,content_bd,seo_title_bd,seo_desc_bd,published_bd) 
				  		VALUES (:id_bd,:id_lang,:name_bd,:title_bd,:url_bd,:resume_bd,:content_bd,:seo_title_bd,:seo_desc_bd,:published_bd)';
				break;
			case 'brand_rel':
				$query = 'INSERT INTO `mc_brand_product`(id_bd,id_product) VALUES (:id,:id_product)';
				break;
			case 'root':
				$queries = [
					[
						'request' => "SET @lang = :id_lang",
						'params' => ['id_lang' => $params['id_lang']]
					],
					[
						'request' => "INSERT INTO `mc_brand_data` (`id_lang`,`name_info`,`value_info`) VALUES
							(@lang,'name',:nm),(@lang,'content',:content),(@lang,'seo_desc',:seo_desc),(@lang,'seo_title',:seo_title)",
						'params' => [
							'nm'        => $params['name'],
							'content'   => $params['content'],
							'seo_desc'  => $params['seo_desc'],
							'seo_title' => $params['seo_title']
						]
					]
				];

				try {
					component_routing_db::layer()->transaction($queries);
					return true;
				}
				catch (Exception $e) {
					return 'Exception reçue : '.$e->getMessage();
				}
		}

		if($query === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->insert($query,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}

	/**
	 * @param array $config
	 * @param array $params
	 * @return bool|string
	 */
	public function update(array $config, array $params = []) {
		$query = '';

		switch ($config['type']) {
			case 'page':
				$query = 'UPDATE mc_brand SET menu_bd = :menu_bd WHERE id_bd = :id_bd';
				break;
			case 'content':
				$query = 'UPDATE mc_brand_content 
						SET 
							name_bd = :name_bd,
							title_bd = :title_bd,
							url_bd = :url_bd,
							resume_bd = :resume_bd,
							content_bd = :content_bd,
							seo_title_bd = :seo_title_bd,
							seo_desc_bd = :seo_desc_bd, 
							published_bd = :published_bd
                		WHERE id_bd = :id_bd 
                		AND id_lang = :id_lang';
				break;
			case 'brand_rel':
				$query = 'UPDATE mc_brand_product 
						SET 
							id_bd = :id
                		WHERE id_product = :id_product';
				break;
			case 'root':
				$query = "UPDATE `mc_brand_data`
                        SET `value_info` = CASE `name_info`
                            WHEN 'name' THEN :nm
                            WHEN 'content' THEN :content
                            WHEN 'seo_desc' THEN :seo_desc
						    WHEN 'seo_title' THEN :seo_title
                        END
                        WHERE `name_info` IN ('name','content','seo_desc','seo_title') AND id_lang = :id_lang";
				break;
			case 'img':
				$query = 'UPDATE mc_brand 
						SET img_bd = :img_bd
                		WHERE id_bd = :id_bd';
				break;
			case 'imgContent':
				$query = 'UPDATE mc_brand_content 
						SET 
							alt_img = :alt_img,
							title_img = :title_img,
							caption_img = :caption_img
                		WHERE id_bd = :id_bd 
                		AND id_lang = :id_lang';
				break;
			case 'pageActiveMenu':
				$query = 'UPDATE mc_brand 
						SET menu_bd = :menu_bd 
						WHERE id_bd IN ('.$params['id_bd'].')';
				$params = array('menu_bd' => $params['menu_bd']);
				break;
			case 'order':
				$query = 'UPDATE mc_brand 
						SET order_bd = :order_bd
                		WHERE id_bd = :id_bd';
				break;
		}

		if($query === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->update($query,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}

	/**
	 * @param array $config
	 * @param array $params
	 * @return bool|string
	 */
	public function delete(array $config, array $params = []) {
		$query = '';

		switch ($config['type']) {
			case 'delPages':
				$query = 'DELETE FROM mc_brand WHERE id_bd IN ('.$params['id'].')';
				$params = array();
				break;
		}

		if($query === '') return 'Unknown request asked';

		try {
			component_routing_db::layer()->delete($query,$params);
			return true;
		}
		catch (Exception $e) {
			return 'Exception reçue : '.$e->getMessage();
		}
	}
}