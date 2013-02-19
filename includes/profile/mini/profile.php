<?php
/**
 * 
 * 最小化安装配置
 * @author Leo Ning
 *
 */
class MiniInstallProfile implements InstallProfile {
    public function getDescription() {
        return "只安装最小系统。";
    }
    
    public function getProfileName() {
        return "最小安装";
    }
    public function onCheckDirectory(&$dirs) {
        // TODO Auto-generated method stub
    

    }
    
    public function onCheckServerEnv(&$envs) {
        // TODO Auto-generated method stub
    

    }
    
    public function onInitConfigForm(&$form) {
        // TODO Auto-generated method stub
    

    }
    
    public function onInstallModules() {
        // TODO Auto-generated method stub
    

    }

}