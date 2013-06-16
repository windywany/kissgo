<?php
/**
 * 
 * @author Leo
 *
 */
class ThumbPreferenceForm extends BootstrapForm {
    
    protected function getDefaultWidgetOptions() {
        return array (FWT_OPTIONS => array ('class' => 'span5' ), FWT_TIP_SHOW => FWT_TIP_SHOW_S );
    }
}