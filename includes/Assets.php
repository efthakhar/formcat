<?php


namespace FormCat;

use FormCat\Traits\Singleton;

class Assets{
    
     use Singleton;

    function __construct()
    {
        add_action( 'admin_enqueue_scripts', [$this,'load_assets'] );
        add_filter( 'script_loader_tag', [$this,'filter_script'], 10, 3 );
    }

    function load_assets($hook)
    {
	
        if( $hook != 'toplevel_page_formcat' ) 
        {
            return;
        }
        
        wp_enqueue_style( 'formcat_main_css',  FORMCAT_DIR.'vuejs/dist/index.css' );
        wp_enqueue_script('formcat_main_js', FORMCAT_DIR.'vuejs/dist/index.js',[],time() ); 
        
        wp_localize_script('formcat_main_js','formcat',
            [
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' )
            ]
        );
    }

    
    function filter_script( $tag, $handle, $source ) 
    {

        if ( 'formcat_main_js' === $handle ) {
            $tag = '<script type="module" crossorigin src="' . $source . '" ></script>';
        }
         
        return $tag;
    }

    
}

    


?>