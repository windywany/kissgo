<?php
/*
 * name: 默认安装
 */
/**
 * 
 * 默认安装配置
 * @author Leo Ning
 *
 */
class DefaultInstallProfile implements InstallProfile {
    public function onCheckDirectory(&$dirs) {
        // add some directories to check permission like below:
    // $dirs['name'] = fullpath of the directory
    }
    
    public function onCheckServerEnv(&$envs) {
        // do a enviorenment check like below:
    /*$env ['name'] = 'json';
        $env ['requirement'] = '有';
        if (function_exists ( 'json_encode' )) {
            $env ['current'] = '<span class="label label-success mr10">有</span>';
            $env ['cls'] = 'success';
        } else {
            $env ['current'] = '<span class="label label-important">无</span>';
            $env ['cls'] = 'error';
        }
        $envs [] = $env;*/
    }
    /**
     * (non-PHPdoc)
     * @see InstallProfile::onInitConfigForm()
     * @param BaseForm $form
     */
    public function onInitConfigForm(&$form) {    
     //$widget = array (FWT_LABEL => '测试', FWT_TIP => '测试也是可以的', FWT_INITIAL => 'LEO', FWT_VALIDATOR => array ('required' => '你真的需要填写这项' ) );
     //$form->addWidgets ( array ('test' => $widget ) );
    }
    
    public function onInstallModules() {
        //return array('your module1 id','your module2 id',......);    
    }
}