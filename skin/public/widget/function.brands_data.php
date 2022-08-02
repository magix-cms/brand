<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */
function smarty_function_brands_data($params, $template){
	$brands = new plugins_brands_public();
	$assign = isset($params['assign']) ?? 'brands';
	$template->assign($assign,$brands->getSlides($params));
}