<?php
/**
 * 
 * 安装配置文件
 * @author Leo Ning
 *
 */
interface InstallProfile {
    /**
     * 
     * 需要默认安装的模块
     */
    public function onInstallModules();
    /**
     * 
     * 修改配置页面
     * @param BasicForm $form
     */
    public function onInitConfigForm(&$form);
    /**
     * 
     * 需要检查的目录
     * @param array $dirs
     */
    public function onCheckDirectory(&$dirs);
    /**
     * 
     * 环境检测
     * @param array $envs
     */
    public function onCheckServerEnv(&$envs);
}