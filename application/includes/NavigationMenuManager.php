<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Leo
 * Date: 12-11-17
 * Time: 下午1:44
 * To change this template use File | Settings | File Templates.
 */
/**
 * 菜单管理器
 */
class NavigationMenuManager {
    private $menus = array();

    /**
     * @param NavigationMenu $menu
     * @return bool
     */
    public function addMenu($menu) {
        if ($menu instanceof NavigationMenu) {
            $this->menus[$menu->getId()] = $menu;
            return true;
        }
        return false;
    }

    /**
     * @param $menuId
     * @param $itemId
     * @param $name
     * @param $url
     * @param string $cls
     * @param string $target
     * @return bool
     */
    public function addMenuItem($menuId, $itemId, $name, $url, $cls = '', $target = '') {
        if (isset($this->menus[$menuId])) {
            $item = new NavigationMenuItem($itemId, $name, $url, $cls, $target);
            return $this->menus[$menuId]->addMenuItem($item);
        }
        return false;
    }

    public function render() {
        $html = '';
        foreach ($this->menus as $menu) {
            $html .= $menu->render();
        }
        return $html;
    }
}

/**
 * 菜单
 */
class NavigationMenu {
    private $id;
    private $name;
    private $cls;
    private $url;
    private $menuItems = array();

    public function __construct($id, $name, $url = '#', $cls = '') {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->cls = $cls;
    }

    public function addMenuItem($menuItem) {
        if ($menuItem instanceof NavigationMenuItem) {
            $this->menuItems[$menuItem->getId()] = $menuItem;
            return true;
        }
        return false;
    }

    public function render() {
        $single = empty($this->menuItems);
        $html = '<li class="dropdown" id="' . $this->id . '">';
        $html .= '<a href="#" data-toggle="dropdown" class="dropdown-toggle">';
        if ($this->cls) {
            $html .= '<i class="' . $this->cls . '"></i>';
        }
        $html .= $this->name;
        if (!empty($this->menuItems)) {
            $html .= '<span class="caret"></span></a><ul class="dropdown-menu">';
            foreach ($this->menuItems as $item) {
                $html .= $item->render();
            }
            $html .= '</ul>';
        } else {
            $html .= '</a>';
        }
        return $html . '</li>';
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }
}

/**
 * 菜单项目
 */
class NavigationMenuItem {
    private $id;
    private $name;
    private $url;
    private $cls;
    private $target;

    public function __construct($id, $name, $url = '', $cls = '', $target = '') {
        $this->id = $id;
        $this->name = $name;
        $this->url = $url;
        $this->cls = $cls;
        $this->target = $target;
    }

    public function getId() {
        return $this->id;
    }

    public function render() {
        $target = empty($this->target) ? '' : ' target="' . $this->target . '" ';
        $icon = empty($this->cls) ? '' : '<i class="' . $this->cls . '"></i>';
        return '<li><a href="' . $this->url . '" id="' . $this->id . '"' . $target . '>' . $icon . $this->name . '</a></li>';
    }
}

/**
 * 菜单分隔符
 */
class NavigationMenuItemDivider extends NavigationMenuItem {
    public function __construct() {
        parent::__construct('_divider_' . rand(10000, 9999999), '', '');
    }

    public function render() {
        return '<li class="divider"><span></span></li>';
    }
}