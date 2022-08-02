<?php
include_once ('db.php');
/*
 # -- BEGIN LICENSE BLOCK ----------------------------------
 #
 # This file is part of MAGIX CMS.
 # MAGIX CMS, The content management system optimized for users
 # Copyright (C) 2008 - 2022 magix-cms.com <support@magix-cms.com>
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
/**
 * MAGIX CMS
 * @category plugins
 * @package brand
 * @copyright  MAGIX CMS Copyright (c) 2008 - 2022 Gerits Aurelien,
 * http://www.magix-cms.com,  http://www.magix-cjquery.com
 * @license Dual licensed under the MIT or GPL Version 3 licenses.
 * @version 2.0
 * @author: Salvatore Di Salvo
 * @name plugins_brand_public
 */
class plugins_brand_public extends plugins_brand_db {
	/**
	 * @var frontend_model_template $template
	 * @var frontend_model_data $template
	 * @var frontend_model_seo $seo
	 * @var frontend_model_logo $logo
	 * @var component_files_images $imagesComponent
	 * @var frontend_model_module $module
	 * @var frontend_model_catalog $modelCatalog
	 */
	protected frontend_model_template $template;
	protected frontend_model_data $data;
	protected frontend_model_seo $seo;
	protected frontend_model_logo $logo;
	protected component_files_images $imagesComponent;
	protected frontend_model_module $module;
	protected frontend_model_catalog $modelCatalog;

	/**
	 * @var array $mods
	 */
    protected array $mods;

	/**
	 * @var string $controller
	 * @var string $getlang
	 */
    public string
		$controller,
		$lang;

	/**
	 * @var int $id
	 */
    public int
		$id;

	/**
	 * @var array $imagePlaceHolder
	 * @var array $fetchConfig
	 * @var array $imgPrefix
	 */
    public array
		$imagePlaceHolder,
		$fetchConfig,
		$imgPrefix;

	/**
	 * plugins_brand_public constructor.
	 * @param frontend_model_template|null $t
	 */
    public function __construct(frontend_model_template $t = null) {
        $this->template = $t instanceof frontend_model_template ? $t : new frontend_model_template();
		$this->data = new frontend_model_data($this,$this->template);
		$this->seo = new frontend_model_seo('brand', '', '',$this->template);
        $this->lang = $this->template->currentLanguage();

        if(http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);
        if(http_request::isGet('id')) $this->id = form_inputEscape::numeric($_GET['id']);
    }

	// --- Modules methods
    /**
     *
     */
    private function loadModules() {
        $this->module = $this->module ?? new frontend_model_module($this->template);
		if(empty($this->mods)) $this->mods = $this->module->load_module('brand');
    }
	// --------------------

	// --- Database methods
	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param array|int|null $id
	 * @param string|null $context
	 * @param bool|string $assign
	 * @return mixed
	 */
	public function getItems(string $type, $id = null, string $context = null, $assign = true) {
		return $this->data->getItems($type, $id, $context, $assign);
	}
	// --------------------

	// --- Slider methods
	/**
	 * @param bool $random
	 * @return array
	 */
	public function getSlides(bool $random = false): array {
		$brands = $this->getBrandList();
		if($random && !empty($brands)) shuffle($brands);
		return $brands;
	}
	// --------------------

	/**
	 * Get Brand Product list
	 * @return array
	 */
	private function getProductList(): array {
		$productsData = $this->getItems('product',['iso' => $this->lang, 'id_bd' => $this->id],'all',false);
		$products = [];
		if(!empty($productsData)) {
			$this->modelCatalog = new frontend_model_catalog($this->template);
			foreach ($productsData as $product) {
				$products[] = $this->modelCatalog->setItemData($product,null);
			}
		}
		return $products;
	}

	/**
	 *
	 */
	private function initImageComponents() {
		$this->logo = new frontend_model_logo();
		$this->imagesComponent = new component_files_images($this->template);
		$this->imagePlaceHolder = $this->logo->getImagePlaceholder();
		$this->imgPrefix = $this->imagesComponent->prefix();
		$this->fetchConfig = $this->imagesComponent->getConfigItems([
			'module_img' => 'plugins',
			'attribute_img' => 'brand'
		]);
	}

	/**
	 * @param array $rawData
	 * @return array
	 */
	public function setBrandData(array $rawData): array {
		$brand = [];

		if (!empty($rawData)) {
			$extwebp = 'webp';
			$string_format = new component_format_string();
			$this->initImageComponents();

			if (isset($rawData['name_bd'])) {
				$brand = [
					'id' => $rawData['id_bd'],
					'menu' => $rawData['menu_bd'],
					'name' => $rawData['name_bd'],
					'title' => $rawData['title_bd'],
					'iso' => $rawData['iso_lang'],
					'url' => '/'.$rawData['iso_lang'].'/brand/'.$rawData['id_bd'].'-'.$rawData['url_bd'].'/',
					'content' => $rawData['content_bd'],
					'resume' => $rawData['resume_bd'] ?: ($rawData['content_bd'] ? $string_format->truncate(strip_tags($rawData['content_bd'])) : ''),
					'date' => [
						'update' => $rawData['last_update'],
						'register' => $rawData['date_register']
					],
					'img' => [
						'default' => [
							'src' => $this->imagePlaceHolder['pages'] ?? '/skin/'.$this->template->theme.'/img/pages/default.png'
						],
						'alt' => $rawData['alt_img'],
						'title' => $rawData['title_img'],
						'caption' => $rawData['caption_img'],
					]
				];

				if (isset($rawData['img_bd'])) {
					$pathinfo = pathinfo($rawData['img_bd']);
					$filename = $pathinfo['filename'];
					$brand['img']['name'] = $rawData['img_bd'];

					foreach ($this->fetchConfig as $value) {
						$type = $value['type_img'];
						$imginfo = $this->imagesComponent->getImageInfos(component_core_system::basePath().'/upload/brand/'.$rawData['id_bd'].'/'.$this->imgPrefix[$value['type_img']].$rawData['img_bd']);
						$brand['img'][$type] = [
							'src' => '/upload/brand/'.$rawData['id_bd'].'/'.$this->imgPrefix[$type].$rawData['img_bd'],
							'src_webp' => '/upload/brand/'.$rawData['id_bd'].'/'.$this->imgPrefix[$type].$filename.'.'.$extwebp,
							'w' => $value['resize_img'] === 'basic' ? $imginfo['width'] : $value['width_img'],
							'h' => $value['resize_img'] === 'basic' ? $imginfo['height'] : $value['height_img'],
							'crop' => $value['resize_img'],
							'ext' => mime_content_type(component_core_system::basePath().'/upload/brand/'.$rawData['id_bd'].'/'.$this->imgPrefix[$value['type_img']].$rawData['img_bd'])
						];
					}
				}

				$this->seo->level = 'record';
				$seoTitle = $this->seo->replace_var_rewrite('',$brand['name'],'title') ?: $brand['name'];
				$seoDesc = $this->seo->replace_var_rewrite('',$brand['name'],'description') ?: ($brand['resume'] ?: $brand['seo']['title']);

				$brand['seo'] = [
					'title' => $rawData['seo_title_bd'] ?: $seoTitle,
					'description' => $rawData['seo_desc_bd'] ?: $seoDesc,
				];
			}
		}

		return $brand;
	}

	/**
	 *
	 */
	private function setHreflang() {
		$brandLangs = $this->getItems('brandLangs',['id' => $this->id],'all',false);
		$hreflang = array();
		foreach ($brandLangs as $langData) {
			$hreflang[$langData['id_lang']] = http_url::getUrl().'/'.$langData['iso_lang'].'/brand/'.$this->id.'-'.$langData['name_bd'].'/';
		}
		$this->template->assign('hreflang',$hreflang,true);
	}

	/**
	 * @return array
	 */
	private function getBrandList(): array {
		$brandsData = $this->getItems('brands',['iso' => $this->lang],'all',false);
		$brands = [];
		if(!empty($brandsData)) {
			foreach ($brandsData as $item) {
				$brands[] = $this->setBrandData($item);
			}
		}
		return $brands;
	}

	/**
	 * @return array
	 */
	private function getRootPageData(): array {
		$rootData = [];
		$rootRawData = $this->getItems('root',['iso'=>$this->lang],'all',false);

		if(!empty($rootRawData)) {
			foreach ($rootRawData as $row) {
				$rootData[$row['name_info']] = $row['value_info'];
			}

			$this->seo->level = 'root';
			$rootData['seo']['title'] = empty($rootData['seo_title']) ? ($this->seo->replace_var_rewrite('','','title') ?: $rootData['name']) : $rootData['seo_title'];

			$string_format = new component_format_string();
			$rootData['seo']['description'] = empty($rootData['seo_desc']) ? ($this->seo->replace_var_rewrite('','','title') ?: $string_format->truncate(strip_tags($rootData['content']))) : $rootData['seo_desc'];
		}

		return $rootData;
	}

    /**
	 *
     */
    public function run() {
		$breadplugin = [];
		$breadplugin[] = ['name' => $this->template->getConfigVars('brand')];

		$this->template->assign('root',$this->getRootPageData(),true);

        if(isset($this->id)) {
			$brandData = $this->getItems('brandContent',['id' => $this->id, 'iso' => $this->lang],'one',false);
			if(!empty($brandData)) {
				$this->setHreflang();
				$this->template->assign('brand',$this->setBrandData($brandData),true);

				$breadplugin[0]['url'] = http_url::getUrl().'/'.$this->lang.'/brand/';
				$breadplugin[0]['title'] = $this->template->getConfigVars('brand');
				$breadplugin[] = ['name' => $brandData['name_bd']];
			}

            $this->template->assign('products',$this->getProductList(),true);

			$lists = [];
			$this->loadModules();
			if(!empty($this->mods)) {
				foreach ($this->mods as $name => $mod){
					if(method_exists($mod,'getBrandContent')) {
						$lists[$name] = $mod->getBrandContent($this->id);
					}
				}
			}
			$this->template->assign('lists',$lists,true);

			$tpl = 'brand';
        }
        else {
			$this->template->assign('brands',$this->getBrandList(),true);
            $tpl = 'index';
        }

		$this->template->assign('breadplugin', $breadplugin,true);
		$this->template->display("brand/$tpl.tpl");
    }

	// --- Override methods
	/**
	 * @return array
	 */
	public function getBuildProductItems(): array {
		$modelCatalog = new frontend_model_catalog($this->template);
		$db_catalog = new frontend_db_catalog();
		$collection = $db_catalog->fetchData(
			['context' => 'one', 'type' => 'product'],
			['iso' => $this->template->lang, 'id' => $this->id]
		);
		$brandData = $this->getItems('productBrand', ['id_product' => $this->id, 'iso' => $this->lang],'one',false);
		$collection['brand'] = $this->setBrandData($brandData);

		$imgCollection = $db_catalog->fetchData(
			['context' => 'all', 'type' => 'images'],
			['iso' => $this->template->lang, 'id' => $this->id]
		);
		if (!empty($imgCollection))	$collection['img'] = $imgCollection;

		$associatedCollection = $db_catalog->fetchData(
			['context' => 'all', 'type' => 'similar'],
			['iso' => $this->template->lang, 'id' => $this->id]
		);
		if(!empty($associatedCollection)) {
			foreach ($associatedCollection as &$row) {
				$row['brand'] = $this->getItems('productBrand', ['id_product' => $row['id_product'], 'iso' => $this->template->lang],'one',false);
			}

			$collection['associated'] = $associatedCollection;
		}

		$brandCollection = $this->getitems('brandProducts',['id_bd' => $collection['brand']['id_bd'], 'iso' => $this->lang, 'product_excluded' => $this->id],'all',false);
		if(!empty($brandCollection)) {
			foreach ($brandCollection as &$product) {
				$product = $modelCatalog->setItemData($product, null,['brand' => 'brand']);
			}
		}
		$collection['sameBrand'] = empty($brandCollection) ? [] : $brandCollection;

		return $modelCatalog->setItemData($collection, null,['brand' => 'brand','sameBrand' => 'sameBrand']);
	}
	// --------------------
}