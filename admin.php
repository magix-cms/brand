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
 * @name plugins_brand_admin
 */
class plugins_brand_admin extends plugins_brand_db {
	/**
	 * @var backend_model_template $template
	 * @var backend_model_data $data
	 * @var component_core_message $message
	 * @var backend_controller_plugins $plugins
	 * @var backend_model_language $modelLanguage
	 * @var component_collections_language $collectionLanguage
	 * @var http_header $header
	 * @var backend_controller_tableform $tableform
	 * @var backend_controller_module $module
	 */
	protected backend_model_template $template;
	protected backend_model_data $data;
	protected component_core_message $message;
	protected backend_controller_plugins $plugins;
	protected backend_model_language $modelLanguage;
	protected component_collections_language $collectionLanguage;
	protected http_header $header;
	protected backend_controller_tableform $tableform;
	protected backend_controller_module $module;

	protected
		$order,
		$upload,
		$config,
		$imagesComponent,
		$modelPlugins,
		$routingUrl,
		$makeFiles,
		$finder,
		$xml,
		$sitemap;

	/**
	 * @var array $mods
	 */
	protected array $mods;

	/**
	 *
	 */
    public
		$pages,
		$img,
		$del_img,
		$ajax,
		$tableaction,
		$name_img,
		$menu_bd,
		$type;

	/**
	 * @var int
	 */
	public int
		$offset,
		$id,
		$id_bd,
		$parent_id,
		$edit;

	/**
	 * @var string
	 */
	public string
		$iso,
		$action,
		$tabs,
		$plugin,
		$controller;

	/**
	 * @var array
	 */
	public array
		$content,
		$search,
		$tableconfig = [
		'all' => [
			'id_bd',
			'name_bd' => ['title' => 'name'],
			'parent_bd' => ['col' => 'name_bd', 'title' => 'name'],
			'img_bd' => ['type' => 'bin', 'input' => null, 'class' => ''],
			'resume_bd' => ['type' => 'bin', 'input' => null],
			'content_bd' => ['type' => 'bin', 'input' => null],
			'seo_title_bd' => ['title' => 'seo_title', 'class' => '', 'type' => 'bin', 'input' => null],
			'seo_desc_bd' => ['title' => 'seo_desc', 'class' => '', 'type' => 'bin', 'input' => null],
			'menu_bd',
			'date_register'
		],
		'parent' => [
			'id_bd',
			'name_bd' => ['title' => 'name'],
			'img_bd' => ['type' => 'bin', 'input' => null, 'class' => ''],
			'resume_bd' => ['class' => 'fixed-td-lg', 'type' => 'bin', 'input' => null],
			'content_bd' => ['class' => 'fixed-td-lg', 'type' => 'bin', 'input' => null],
			'seo_title_bd' => ['title' => 'seo_title', 'class' => 'fixed-td-lg', 'type' => 'bin', 'input' => null],
			'seo_desc_bd' => ['title' => 'seo_desc', 'class' => 'fixed-td-lg', 'type' => 'bin', 'input' => null],
			'menu_bd',
			'date_register'
		]
	];

    /**
	 * plugins_brand_admin constructor
     */
    public function __construct(){
        $this->template = new backend_model_template();
		$this->data = new backend_model_data($this);
		$this->plugins = new backend_controller_plugins();
		$this->message = new component_core_message($this->template);
		$this->modelLanguage = new backend_model_language($this->template);
		$this->collectionLanguage = new component_collections_language();
        $this->upload = new component_files_upload();
        $this->imagesComponent = new component_files_images($this->template);
        $this->modelPlugins = new backend_model_plugins();
        $this->routingUrl = new component_routing_url();
        $this->makeFiles = new filesystem_makefile();
        $this->finder = new file_finder();
        $this->xml = new xml_sitemap();
        $this->sitemap = new backend_model_sitemap($this->template);

        // --- GET
		// --- GET
		if (http_request::isGet('controller')) $this->controller = form_inputEscape::simpleClean($_GET['controller']);
		if (http_request::isGet('edit')) $this->edit = form_inputEscape::numeric($_GET['edit']);
		if (http_request::isRequest('action')) $this->action = form_inputEscape::simpleClean($_REQUEST['action']);
		if (http_request::isGet('tabs')) $this->tabs = form_inputEscape::simpleClean($_GET['tabs']);
		if (http_request::isGet('plugin')) $this->plugin = form_inputEscape::simpleClean($_GET['plugin']);
        if (http_request::isGet('tableaction')) {
            $this->tableaction = form_inputEscape::simpleClean($_GET['tableaction']);
            $this->tableform = new backend_controller_tableform($this,$this->template);
        }
    }

    /**
     * Method to override the name of the plugin in the admin menu
     * @return string
     */
    public function getExtensionName(): string {
        return $this->template->getConfigVars('brand_plugin');
    }

	// --- Modules methods
	/**
	 *
	 */
	private function loadModules() {
		$this->module = $this->module ?? new backend_controller_module();
		if(empty($this->mods)) $this->mods = $this->module->load_module('brand');
	}

	/**
	 * @return void
	 */
	private function getModuleTabs() {
		$newsItems = [];
		foreach ($this->mods as $name => $mod) {
			$item['name'] = $name;
			if (method_exists($mod, 'getExtensionName')) {
				$this->template->addConfigFile(
					array(component_core_system::basePath() . 'plugins' . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR),
					array($name . '_admin_')
				);
				$item['title'] = $mod->getExtensionName();
			}
			else {
				$item['title'] = $name;
			}
			$newsItems[] = $item;
		}
		$this->template->assign('setTabsPlugins', $newsItems);
	}
	// --------------------

	// --- TableForm methods
    /**
     * @param bool $ajax
     * @return mixed
     */
    public function tableSearch(bool $ajax = false) {
        $this->modelLanguage->getLanguage();
        $defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
        $params = [];

        if($this->edit)
            $results = $this->getItems('pagesChild',$this->edit,'all',false);
        else
            $results = $this->getItems('pages',array('default_lang'=>$defaultLanguage['id_lang']),'all',false, true);

        $assign = $this->tableconfig[(($ajax || $this->edit) ? 'parent' : 'all')];

        if($ajax) {
            $params['section'] = 'pages';
            $params['idcolumn'] = 'id_bd';
            $params['activation'] = true;
            $params['sortable'] = true;
            $params['checkbox'] = true;
            $params['edit'] = true;
            $params['dlt'] = true;
            $params['readonly'] = [];
            $params['cClass'] = 'plugins_brand_admin';
        }

        $this->data->getScheme(['mc_brand','mc_brand_content'],['id_bd','name_bd','img_bd','resume_bd','content_bd','seo_title_bd','seo_desc_bd','menu_bd','date_register'],$assign);

        return [
			'data' => $results,
			'var' => 'pages',
			'tpl' => 'index.tpl',
			'params' => $params
		];
    }

	/**
	 * Active / Unactive page(s)
	 * @param array $params
	 */
	public function tableActive(array $params) {
		$this->upd([
			'type' => 'pageActiveMenu',
			'data' => [
				'menu_bd' => $params['active'],
				'id_bd' => $params['ids']
			]
		]);
		$this->message->getNotify('update',['method'=>'fetch','assignFetch'=>'message']);
	}
	// --------------------

	// --- Database methods
	/**
	 * Assign data to the defined variable or return the data
	 * @param string $type
	 * @param array|int|null $id
	 * @param string|null $context
	 * @param bool|string $assign
	 * @param bool $pagination
	 * @return mixed
	 */
	public function getItems(string $type, $id = null, string $context = null, $assign = true, bool $pagination = false) {
		return $this->data->getItems($type, $id, $context, $assign, $pagination);
	}

	/**
	 * Insert data
	 * @param array $data
	 * @return bool|string|void
	 */
    public function add(array $data) {
        switch ($data['type']) {
            case 'page':
				return parent::insert(
					['context' => $data['context'], 'type' => $data['type']],
					$data['data']
				);
				break;
            case 'content':
            case 'brand_rel':
            case 'root':
				parent::insert(
				['context' => $data['context'], 'type' => $data['type']],
				$data['data']
			);
                break;
        }
    }

    /**
     * Update data
     * @param array $data
     */
    public function upd(array $data) {
        switch ($data['type']) {
            case 'order':
                $p = $this->order;
                for ($i = 0; $i < count($p); $i++) {
                    parent::update(
                        ['type'=>$data['type']],
						[
							'id_bd' => $p[$i],
							'order_bd' => $i + (isset($this->offset) ? ($this->offset + 1) : 0)
						]
                    );
                }
                break;
            case 'page':
            case 'content':
            case 'brand_rel':
            case 'img':
            case 'imgContent':
            case 'pageActiveMenu':
            case 'root':
                parent::update(
                    ['context' => $data['context'], 'type' => $data['type']],
                    $data['data']
                );
                break;
        }
    }

    /**
     * Insertion de données
     * @param array $data
     */
    private function del(array $data) {
        switch($data['type']){
            case 'delPages':
                parent::delete(
                    ['type' => $data['type']],
                    $data['data']
                );
                break;
        }
    }
	// --------------------

    /**
     * @return array
     * @throws Exception
     */
    private function setRootData() {
        $data = $this->getItems('rootContent', null, 'all', false);
        $newArr = array();
        foreach ($data as $item) {
            $newArr[$item['id_lang']][$item['name_info']] = $item['value_info'];
        }
        return $newArr;
    }

    /**
     * @param $data
     * @return array
     * @throws Exception
     */
    private function setItemData($data) {
        //return $this->getItems('page',$this->edit, 'all',false);
        $imgPath = $this->upload->imgBasePath('upload/brand');
        $arr = array();
        $conf = array();
        $fetchConfig = $this->imagesComponent->getConfigItems(array('module_img'=>'plugins','attribute_img'=>'brand'));
        $imgPrefix = $this->imagesComponent->prefix();

        foreach ($data as $page) {

            /*$publicUrl = !empty($page['url_bd']) ? $this->routingUrl->getBuildUrl(array(
                    'type'      =>  'pages',
                    'iso'       =>  $page['iso_lang'],
                    'id'        =>  $page['id_bd'],
                    'url'       =>  $page['url_bd']
                )
            ) : '';*/
            $publicUrl = '/'.$page['iso_lang'].'/'.$this->controller.'/'.$page['id_bd'].'-'.$page['url_bd'].'/';

            if (!array_key_exists($page['id_bd'], $arr)) {
                $arr[$page['id_bd']] = array();
                $arr[$page['id_bd']]['id_bd'] = $page['id_bd'];
                $arr[$page['id_bd']]['id_parent'] = $page['id_parent'];
                $img_bd = pathinfo($page['img_bd']);
                $arr[$page['id_bd']]['img_bd'] = $img_bd['filename'];
                if($page['img_bd'] != null) {
                    if(file_exists($imgPath.DIRECTORY_SEPARATOR.$page['id_bd'].DIRECTORY_SEPARATOR.$page['img_bd'])){
                        $originalSize = getimagesize($imgPath.DIRECTORY_SEPARATOR.$page['id_bd'].DIRECTORY_SEPARATOR.$page['img_bd']);
                        $arr[$page['id_bd']]['imgSrc']['original']['img'] = $page['img_bd'];
                        $arr[$page['id_bd']]['imgSrc']['original']['width'] = $originalSize[0];
                        $arr[$page['id_bd']]['imgSrc']['original']['height'] = $originalSize[1];
                    }
                    foreach ($fetchConfig as $key => $value) {
                        $size = getimagesize($imgPath.DIRECTORY_SEPARATOR.$page['id_bd'].DIRECTORY_SEPARATOR.$imgPrefix[$value['type_img']] . $page['img_bd']);
                        $arr[$page['id_bd']]['imgSrc'][$value['type_img']]['img'] = $imgPrefix[$value['type_img']] . $page['img_bd'];
                        $arr[$page['id_bd']]['imgSrc'][$value['type_img']]['width'] = $size[0];
                        $arr[$page['id_bd']]['imgSrc'][$value['type_img']]['height'] = $size[1];
                    }
                }
                $arr[$page['id_bd']]['menu_bd'] = $page['menu_bd'];
                $arr[$page['id_bd']]['date_register'] = $page['date_register'];
            }
            $arr[$page['id_bd']]['content'][$page['id_lang']] = array(
                'id_lang'           => $page['id_lang'],
                'iso_lang'          => $page['iso_lang'],
                'name_bd'        => $page['name_bd'],
                'title_bd'  => $page['title_bd'],
                'url_bd'         => $page['url_bd'],
                'resume_bd'      => $page['resume_bd'],
                'content_bd'     => $page['content_bd'],
                'alt_img'     		=> $page['alt_img'],
                'title_img'     	=> $page['title_img'],
                'caption_img'       => $page['caption_img'],
                'seo_title_bd'   => $page['seo_title_bd'],
                'seo_desc_bd'    => $page['seo_desc_bd'],
                'published_bd'   => $page['published_bd'],
                'public_url'        => $publicUrl
            );
        }
        return $arr;
    }

    /**
     * @param $id
     * @return void
     * @throws Exception
     */
    private function saveContent($id) {
        $extendData = [];

        foreach ($this->content as $lang => $content) {
            $content['id_lang'] = $lang;
            $content['id_bd'] = $id;
            $content['published_bd'] = (!isset($content['published_bd']) ? 0 : 1);
            $content['title_bd'] = (!empty($content['title_bd']) ? $content['title_bd'] : NULL);
            $content['resume_bd'] = (!empty($content['resume_bd']) ? $content['resume_bd'] : NULL);
            $content['content_bd'] = (!empty($content['content_bd']) ? $content['content_bd'] : NULL);
            $content['seo_title_bd'] = (!empty($content['seo_title_bd']) ? $content['seo_title_bd'] : NULL);
            $content['seo_desc_bd'] = (!empty($content['seo_desc_bd']) ? $content['seo_desc_bd'] : NULL);

            if (empty($content['url_bd'])) {
                $content['url_bd'] = http_url::clean($content['name_bd'],[
					'dot' => false,
					'ampersand' => 'strict',
					'cspec' => '',
					'rspec' => ''
				]);
            }

            $contentPage = $this->getItems('content', ['id_bd' => $id, 'id_lang' => $lang], 'one', false);

            if (!empty($contentPage)) {
                $this->upd([
					'type' => 'page',
					'data' => [
						'id_bd' => $id,
						'menu_bd' => isset($this->menu_bd) ? 1 : 0
					]
				]);
                $this->upd([
					'type' => 'content',
					'data' => $content
				]);
            }
			else {
                $this->add([
					'type' => 'content',
					'data' => $content
				]);
            }

            if (isset($this->id_bd)) {
                $setEditData = $this->getItems('page', ['edit' => $this->edit], 'all', false);
                $setEditData = $this->setItemData($setEditData);
                $extendData[$lang] = $setEditData[$this->id_bd]['content'][$lang]['public_url'];
            }
        }

        //if (!empty($extendData)) return $extendData;
        if (!empty($extendData)){
            $this->message->json_post_response(true, 'update', ['result'=>$this->id_bd,'extend'=>$extendData]);
        }
    }

    /**
     * save data
     */
    private function saveRoot() {
        if (isset($this->content)) {
            foreach ($this->content as $lang => $content) {
                $rootContent = $this->getItems('rootContent', array('id_lang' => $lang), 'one', false);

                if ($rootContent != null) {
                    $this->upd(
                        array(
                            'type' => 'root',
                            'data' => array(
                                'nm' => !empty($content['brand_name']) ? $content['brand_name'] : NULL,
                                'content' => !empty($content['brand_content']) ? $content['brand_content'] : NULL,
                                'seo_title' => !empty($content['seo_title']) ? $content['seo_title'] : NULL,
                                'seo_desc'  => !empty($content['seo_desc']) ? $content['seo_desc'] : NULL,
                                'id_lang' => $lang
                            )
                        )
                    );

                } else {
                    $this->add(
                        array(
                            'type' => 'root',
                            'data' => array(
                                'name' => !empty($content['brand_name']) ? $content['brand_name'] : NULL,
                                'content' => !empty($content['brand_content']) ? $content['brand_content'] : NULL,
                                'seo_title' => !empty($content['seo_title']) ? $content['seo_title'] : NULL,
                                'seo_desc'  => !empty($content['seo_desc']) ? $content['seo_desc'] : NULL,
                                'id_lang' => $lang
                            )
                        )
                    );
                }
            }
            $this->message->json_post_response(true, 'update', $this->content);
        }
    }

    /**
     *
     */
    public function run() {
		$this->loadModules();
		$defaultLanguage = $this->collectionLanguage->fetchData(['context' => 'one', 'type' => 'default']);

		if (http_request::isGet('ajax')) $this->ajax = form_inputEscape::simpleClean($_GET['ajax']);
		if (http_request::isGet('offset')) $this->offset = intval(form_inputEscape::simpleClean($_GET['offset']));

		// --- Search
		if (http_request::isGet('search')) {
			$this->search = form_inputEscape::arrayClean($_GET['search']);
			$this->search = array_filter($this->search, function ($value) { return $value !== ''; });
		}

		if(isset($this->plugin)) {
			$this->modelLanguage->getLanguage();
			$this->getItems('brand', ['default_lang' => $defaultLanguage['id_lang']], 'all');
			$this->getModuleTabs();
			// Execute un plugin core
			$class = 'plugins_' . $this->plugin . '_core';
			if(file_exists(component_core_system::basePath().'plugins'.DIRECTORY_SEPARATOR.$this->plugin.DIRECTORY_SEPARATOR.'core.php') && class_exists($class) && method_exists($class, 'run')) {
				$executeClass =  new $class;
				if($executeClass instanceof $class){
					$executeClass->run();
				}
			}
		}
        elseif(isset($this->tableaction)) {
            $this->tableform->run();
        }
        elseif(isset($this->action)) {
			if(http_request::isMethod('GET')) {
				$this->modelLanguage->getLanguage();

				switch ($this->action) {
					case 'add':
						$this->getItems('pagesSelect',['default_lang'=>$defaultLanguage['id_lang']],'all');
						$this->template->display('add.tpl');
						break;
					case 'edit':
						// Initialise l'API menu des plugins core
						$setEditData = $this->getItems('page', array('edit'=>$this->edit),'all',false);
						$setEditData = $this->setItemData($setEditData);
						$this->template->assign('page',$setEditData[$this->edit]);

						$this->data->getScheme(['mc_brand','mc_brand_content'],['id_bd','name_bd','img_bd','resume_bd','content_bd','seo_title_bd','seo_desc_bd','menu_bd','date_register'],$this->tableconfig['parent']);
						$this->getItems('pagesChild',$this->edit,'all');
						$this->getItems('pagesSelect',['default_lang'=>$defaultLanguage['id_lang']],'all');

						$this->template->display('edit.tpl');
						break;
					case 'getLink':
						if(isset($this->id_bd) && isset($this->iso)) {
							$page = $this->getItems('pageLang',['id' => $this->id_bd,'iso' => $this->iso],'one',false);
							if($page) {
								$page['url'] = $this->routingUrl->getBuildUrl([
									'type' => 'pages',
									'iso' => $page['iso_lang'],
									'id' => $page['id_bd'],
									'url' => $page['url_bd']
								]);
								$link = '<a title="'.$page['url'].'" href="'.$page['name_bd'].'">'.$page['name_bd'].'</a>';
								$this->header = new http_header();
								$this->header->set_json_headers();
								print '{"name":'.json_encode($page['name_bd']).',"url":'.json_encode($page['url']).'}';
							}
							else {
								print false;
							}
						}
						break;
				}
			}
			elseif(http_request::isMethod('POST')) {
				// --- ADD or EDIT
				if (http_request::isPost('id')) $this->id = form_inputEscape::simpleClean($_POST['id']);
				if (http_request::isPost('parent_id')) $this->parent_id = form_inputEscape::simpleClean($_POST['parent_id']);
				if (http_request::isPost('type')) $this->type = form_inputEscape::simpleClean($_POST['type']);
				if (http_request::isPost('menu_bd')) $this->menu_bd = form_inputEscape::simpleClean($_POST['menu_bd']);
				if (http_request::isPost('content')) {
					$array = $_POST['content'];
					foreach($array as $key => $arr) {
						foreach($arr as $k => $v) {
							$array[$key][$k] = ($k == 'content_bd' OR $k == 'brand_content') ? form_inputEscape::cleanQuote($v) : form_inputEscape::simpleClean($v);
						}
					}
					$this->content = $array;
				}
				if (http_request::isPost('brand')) $this->order = form_inputEscape::arrayClean($_POST['brand']);
				if (http_request::isPost('del_img')) $this->del_img = form_inputEscape::simpleClean($_POST['del_img']);

				// --- Image Upload
				if (isset($_FILES['img']["name"])) $this->img = http_url::clean($_FILES['img']["name"]);
				if (http_request::isPost('name_img')) $this->name_img = http_url::clean($_POST['name_img']);

				switch ($this->action) {
					case 'add':
						$status = false;
						$notify = 'error';
						$result = null;
						if(isset($this->content)) {
							$return = $this->add([
								'type' => 'page',
								'data' => [
									'menu_bd' => isset($this->menu_bd) ? 1 : 0
								]
							]);

							//$page = $this->getItems('root',null,'one',false);

							if (!empty($return) && isset($return[0]['id_bd'])) {
								$this->saveContent($return[0]['id_bd']);
								//$this->message->json_post_response(true,'add_redirect');
								$status = true;
								$notify = 'add_redirect';
							}
						}
						$this->message->json_post_response($status, $notify, $result);
						break;
					case 'edit':
						if(isset($this->img) || isset($this->name_img)){
							$defaultLanguage = $this->collectionLanguage->fetchData(array('context' => 'one', 'type' => 'default'));
							$page = $this->getItems('pageLang', array('id' => $this->id_bd, 'iso' => $defaultLanguage['iso_lang']), 'one', false);
							$settings = array(
								'name' => $this->name_img !== '' ? $this->name_img : $page['url_bd'],
								'edit' => $page['img_bd'],
								'prefix' => array('s_', 'm_', 'l_'),
								'module_img' => 'plugins',
								'attribute_img' => 'brand',
								'original_remove' => false
							);
							$dirs = array(
								'upload_root_dir' => 'upload/brand', //string
								'upload_dir' => $this->id_bd //string ou array
							);
							$filename = '';
							$update = false;

							if(isset($this->img)) {
								$resultUpload = $this->upload->setImageUpload('img', $settings, $dirs, false);
								$filename = $resultUpload['file'];
								$update = true;
							}
							elseif(isset($this->name_img)) {
								$img_bd = pathinfo($page['img_bd']);
								$img_name = $img_bd['filename'];

								if($this->name_img !== $img_name && $this->name_img !== '') {
									$result = $this->upload->renameImages($settings,$dirs);
									$filename = $result;
									$update = true;
								}
							}

							if($filename !== '' && $update) {
								$this->upd(array(
									'type' => 'img',
									'data' => array(
										'id_bd' => $this->id_bd,
										'img_bd' => $filename
									)
								));
							}

							foreach ($this->content as $lang => $content) {
								$content['id_lang'] = $lang;
								$content['id_bd'] = $this->id_bd;
								$content['alt_img'] = (!empty($content['alt_img']) ? $content['alt_img'] : NULL);
								$content['title_img'] = (!empty($content['title_img']) ? $content['title_img'] : NULL);
								$content['caption_img'] = (!empty($content['caption_img']) ? $content['caption_img'] : NULL);
								$this->upd(array(
									'type' => 'imgContent',
									'data' => $content
								));
							}

							$setEditData = $this->getItems('page',array('edit'=>$this->id_bd),'all',false);
							$setEditData = $this->setItemData($setEditData);
							$this->template->assign('page',$setEditData[$this->id_bd]);
							$display = $this->template->fetch('brick/img.tpl');
							$this->message->json_post_response(true, 'update',$display);
						}
						elseif (isset($this->id_bd)) {
							$this->saveContent($this->id_bd);
						}
						elseif (isset($this->type)) {
							$this->saveRoot();
						}
						break;
					case 'order':
						if (isset($this->order)) {
							$this->upd(
								array(
									'type' => 'order'
								)
							);
						}
						break;
					case 'delete':
						$status = false;
						$notify = 'error';
						$result = null;
						if(isset($this->id_bd)) {
							$this->del(
								array(
									'type' => 'delPages',
									'data' => ['id' => $this->id_bd]
								)
							);
							$this->message->json_post_response(true,'delete',['id' => $this->id_bd]);
						}
						elseif(isset($this->del_img)) {
							$this->upd(array(
								'type' => 'img',
								'data' => [
									'id_bd' => $this->del_img,
									'img_bd' => NULL
								]
							));

							$setEditData = $this->getItems('page',array('edit'=>$this->del_img),'all',false);
							$setEditData = $this->setItemData($setEditData);

							$setImgDirectory = $this->upload->dirImgUpload(
								array_merge(
									array('upload_root_dir'=>'upload/brand/'.$this->del_img),
									array('imgBasePath'=>true)
								)
							);

							if(file_exists($setImgDirectory)){
								$setFiles = $this->finder->scanDir($setImgDirectory);
								$clean = '';
								if($setFiles != null){
									foreach($setFiles as $file){
										$clean .= $this->makeFiles->remove($setImgDirectory.$file);
									}
								}
							}
							$this->template->assign('page',$setEditData[$this->del_img]);
							$display = $this->template->fetch('brick/img.tpl');
							$this->message->json_post_response(true, 'update',$display);
						}
						$this->message->json_post_response($status, $notify, $result);
						break;
				}
			}
        }
        else {
            $this->modelLanguage->getLanguage();
            $this->template->assign('contentData',$this->setRootData());
            $defaultLanguage = $this->collectionLanguage->fetchData(['context'=>'one','type'=>'default']);
            $this->getItems('pages',['default_lang'=>$defaultLanguage['id_lang']],'all',true,true);
            $this->data->getScheme(['mc_brand','mc_brand_content'],['id_bd','name_bd','img_bd','resume_bd','content_bd','seo_title_bd','seo_desc_bd','menu_bd','date_register'],$this->tableconfig['parent']);
            $this->template->display('index.tpl');
        }
    }
}