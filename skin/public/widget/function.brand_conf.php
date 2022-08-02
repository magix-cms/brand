<?php
function smarty_function_brand_conf($params, $template){
	$modelTemplate = new frontend_model_template();
	$modelTemplate->addConfigFile([component_core_system::basePath().'/plugins/brand/i18n/'], ['public_local_'],false);
	$modelTemplate->configLoad();
}