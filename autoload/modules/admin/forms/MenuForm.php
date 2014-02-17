<?php

/**
 * menu form
 *
 * @author Leo
 *
 */
class MenuForm extends AbstractForm {
    private $id = array ('type' => 'int' );
    private $name = array ('rules' => array ('required' => '请填写菜单名' ) );
    private $alias = array ('rules' => array ('required' => '请填写引用名', 'callback(@chkalias,id)' => '引用名已经被占用.' ) );

    public function chkalias($value, $data) {
        $where = array ('alias' => $value );
        if ($data ['id']) {
            $where ['id <>'] = $data ['id'];
        }
        $rst = dbselect ( 'id' )->from ( '{menus}' )->where ( $where );
        if (count ( $rst ) > 0) {
            return false;
        }
        return true;
    }

    public function save($id) {
        $menu = $this->toArray ();
        unset ( $menu ['id'] );
        if ($id) {
            $rst = dbupdate ( '{menus}' )->set ( $menu )->where ( array ('id' => $id ) );
        } else {
            $rst = dbinsert ( $menu )->inito ( '{menus}' );
        }
        if (count ( $rst ) !== false) {
            if (! $id) {
                $id = $rst [0];
            }
            return $id;
        } else {
            return false;
        }
    }
}