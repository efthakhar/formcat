<?php

namespace FormCat;

use FormCat\Traits\Singleton;

class Pages{

    use Singleton;

    function __construct()
    {
        add_action( 'admin_menu', [$this,'formcat_register_admin_pages'] );
    }


    function formcat_register_admin_pages() 
    {
        add_menu_page(
            __( 'formcat', 'formcat' ),
            __( 'Form Cat', 'formcat' ),
            'manage_options',
            'formcat',
            [$this,'formcat_admin_page_contents'],
            'dashicons-database-import',
            1
        );

        add_submenu_page(
            'formcat',
            __( 'home', 'formcat' ),
            __( 'Home', 'formcat' ),
            'manage_options',
            'admin.php?page=formcat#/',
            NULL
        );

        add_submenu_page(
            'formcat',
            __( 'forms', 'formcat' ),
            __( 'Forms', 'formcat' ),
            'manage_options',
            'admin.php?page=formcat#/forms',
            NULL
        );

        remove_submenu_page('formcat', 'formcat');
    }


    function formcat_admin_page_contents() 
    {
        
        ?>
            <div id="app"></div>
        <?php
    
    }




}

    


?>