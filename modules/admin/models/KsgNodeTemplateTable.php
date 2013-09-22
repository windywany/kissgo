<?php
/**
 * node templates
 * @author Leo
 *
 */
class KsgNodeTemplateTable extends DbTable {
    var $table = 'system_nodetemplate';
    public function schema() {
        $schema = new DbSchema ( 'the templates of different theme ' );
        $schema->addPrimarykey ( array ('type', 'theme' ) );
        $schema ['theme'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 32, Idao::NN );
        $schema ['type'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 16, Idao::NN, Idao::CMMT => 'node type' );
        $schema ['template'] = array ('type' => 'varchar', 'extra' => 'normal', Idao::LENGTH => 512, Idao::NN );
        return $schema;
    }
    /**
     * retrieve the templates 
     * @param string $theme    
     * @return ResultCursor
     */
    public function getTemplates($theme) {
        $nt = new KsgNodeTypeTable ();
        $tpls = $nt->query ( 'NT.*', 'NT' );
        $rst = $this->query ( 'template', 'NTPL' )->where ( array ('NTPL.type' => imtf ( 'NT.type' ), 'NTPL.theme' => $theme ) );
        $tpls->field ( $rst, 'tpl' );
        return $tpls;
    }
    /**
     * get template
     * @param string $theme
     * @param string $type
     * @return string | NULL
     */
    public static function getTemplate($theme, $type) {
        $nt = new KsgNodeTypeTable ();
        $ntt = new KsgNodeTemplateTable ();
        $tpls = $nt->query ( 'NT.template', 'NT' )->where ( array ('NT.type' => $type ) );
        $rst = $ntt->query ( 'template', 'NTPL' )->where ( array ('NTPL.type' => imtf ( 'NT.type' ), 'NTPL.theme' => $theme/*, 'NTPL.type' => $type*/ ) );
        $tpls->field ( $rst, 'tpl' );
        $tpl = $tpls [0];
        if ($tpl) {
            if ($tpl ['tpl']) {
                return $tpl ['tpl'];
            } else {
                return $tpl ['template'];
            }
        }
        return null;
    }
}