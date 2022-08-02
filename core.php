<?php
/**
 * Class plugins_test_core
 * Fichier pour les plugins core
 */
class plugins_brand_core extends plugins_brand_admin {
    /**
     * @var
     */
    protected $modelPlugins;

    /**
     * @var array $brand
     */
    public array $brand;

    /**
     * @var int $mod_edit
     */
    public int $mod_edit;

    /**
     * @var
     */
    public $mod_actionn;

    /**
     * plugins_banner_core constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->modelPlugins = new backend_model_plugins();
        $this->plugins = new backend_controller_plugins();
        $formClean = new form_inputEscape();

        if(http_request::isGet('controller')) $this->controller = $formClean->simpleClean($_GET['controller']);
        if (http_request::isGet('plugin')) $this->plugin = $formClean->simpleClean($_GET['plugin']);
        if (http_request::isRequest('mod_action')) $this->mod_action = $formClean->simpleClean($_REQUEST['mod_action']);
        if (http_request::isGet('mod_edit')) $this->mod_edit = $formClean->numeric($_GET['mod_edit']);
        if (http_request::isPost('brand')) $this->brand = $formClean->arrayClean($_POST['brand']);
    }

    /**
     * @param $data
     * @return array
     */
    private function setItemData($id){
        $data = $this->getItems('brand',$id,'one',false);
        $this->template->assign('brand',$data);
    }

    /**
     * Execution du plugin dans un ou plusieurs modules core
     */
    public function run() {
        if(isset($this->controller)) {
            switch ($this->controller) {
                case 'product':
                    if(isset($this->brand)) {
                        $rel = $this->getItems('brand',$this->edit,'one',false);
                        $params = [
                            'type' => 'brand_rel',
                            'data' => [
                                'id' => $this->brand['id'],
                                'id_product' => $this->edit
                            ]
                        ];
                        if(!empty($rel)) {
                            $this->upd($params);
                        }
                        else {
                            $this->add($params);
                        }
                        $this->message->json_post_response(true, 'update', null);
                    }
                    else {
                        $this->modelLanguage->getLanguage();
                        $defaultLanguage = $this->collectionLanguage->fetchData(array('context'=>'one','type'=>'default'));
                        //$this->getItems('pagesSelect',['default_lang'=>$defaultLanguage['id_lang']],'all','brands');
                        $this->getItems('pages',['default_lang'=>$defaultLanguage['id_lang']],'all','brands');
                        $this->setItemData($this->edit);
                        $this->modelPlugins->display('mod/form/edit.tpl');
                    }
                    break;
            }
        }
    }
}