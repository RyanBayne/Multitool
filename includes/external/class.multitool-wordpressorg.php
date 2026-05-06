<?php             
/**
 * Multitool - WordPress.org API
 *
 * Interacts with WordPress.org and fetches plugins data. 
 *
 * @author   Ryan Bayne
 * @category External
 * @package  Multitool/WordPressAPI
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Multitool_Wordpressorgapi {  

    /**
    * Query plugin data on WordPress.org
    */
    public function query_plugins( $url = 'http://api.wordpress.org/plugins/info/1.0/', $args = array() ) {
        return wp_remote_post(
            $url,
            array(
                'body' => array(
                    'action' => 'query_plugins',
                    'request' => serialize((object)$args)
                )
            )
        );    
    }

    /**
    * Query plugin data on WordPress.org. 
    */
    public function query_themes( $url = 'http://api.wordpress.org/plugins/info/1.0/', $args = array()) {
        return wp_remote_post(
            $url,
            array(
                'body' => array(
                    'action' => 'query_themes',
                    'request' => serialize((object)$args)
                )
            )
        );    
    }
       
    /**
    * Plugin properties as stored on WordPress.org
    * 
    * @version 1.2
    */
    public function plugin_properties() {              
        return array(
            'slug'              => array( 'description' => __( 'The slug of the plug-in to return the data for.', 'multitool' ) ), 
            'author'            => array( 'description' => __( '(When the action is query_plugins). The author\'s WordPress username, to retrieve plugins by a particular author.', 'multitool' ) ),  
            'version'           => array( 'description' => __( 'Latest plugin version.', 'multitool' ) ),
            'author'            => array( 'description' => __( 'Author name and link to profile.', 'multitool' ) ), 
            'requires'          => array( 'description' => __( 'The minimum WordPress version required.', 'multitool' ) ), 
            'tested'            => array( 'description' => __( 'The latest WordPress version tested.', 'multitool' ) ), 
            'compatibility'     => array( 'description' => __( "An array which contains an array for each version of your plug-in. This array stores the number of votes, the number of 'works' votes and this number as a percentage.", 'multitool' ) ), 
            'downloaded'        => array( 'description' => __( 'The number of times the plugin has been downloaded.', 'multitool' ) ), 
            'rating'            => array( 'description' => __( 'The plugins rating as percentage.', 'multitool' ) ), 
            'num_ratings'       => array( 'description' => __( 'Number of times the plugin has been rated.', 'multitool' ) ),
            'sections'          => array( 'description' => __( "An array with the HTML for each section on the WordPress plug-in page as values, keys can include 'description', 'installation', 'screenshots', 'changelog' and 'faq'.", 'multitool' ) ),  
            'description'       => array( 'description' => __( 'Plugins full description, default false.', 'multitool' ) ),
            'short_description' => array( 'description' => __( 'Plugins short description, default false.', 'multitool' ) ), 
            'name'              => array( 'description' => __( 'Name of the plugin.', 'multitool' ) ),
            'author_profile'    => array( 'description' => __( 'Unsure, please update. Does it return URL to authors profile or an array of the authors details?', 'multitool' ) ), 
            'tags'              => array( 'description' => __( 'Unsure.', 'multitool' ) ),
            'homepage'          => array( 'description' => __( 'Unsure.', 'multitool' ) ), 
            'contributors'      => array( 'description' => __( 'Array of contributors.', 'multitool' ) ), 
            'added'             => array( 'description' => __( 'When the plugin was added to the repository.', 'multitool' ) ),
            'last_updated'      => array( 'description' => __( 'Unsure, please update. It may be the author stated update or the last time the repository for this plugin was updated.', 'multitool' ) ),
        );
    }

    /**
    * Theme properties as stored on WordPress.org
    * 
    * @version 1.2
    */
    public function theme_properties() {            
        return array(
            'slug'              => array( 'description' => __( 'The slug of the theme to return the data for.', 'multitool' ) ), 
            'browse'            => array( 'description' => __( 'Takes the values featured, new or updated.', 'multitool' ) ), 
            'author'            => array( 'description' => __( 'The author\'s username, to retrieve themes by a particular author.', 'multitool' ) ), 
            'tag'               => array( 'description' => __( 'An array of tags with which to retrieve themes for.', 'multitool' ) ),  
            'search'            => array( 'description' => __( 'A search term, with which to search the repository.', 'multitool' ) ), 
            'fields'            => array( 'description' => __( 'An array with a true or false value for each key (field). The fields that are included make up the properties of the returned object above.', 'multitool' ) ),  
            'version'           => array( 'description' => __( 'Themes latest version.', 'multitool' ) ), 
            'author'            => array( 'description' => __( 'Author of the theme.', 'multitool' ) ),
            'preview_url'       => array( 'description' => __( 'URL to wp-themes.com hosted preview.', 'multitool' ) ), 
            'screenshot_url'    => array( 'description' => __( 'URL to screenshot image.', 'multitool' ) ), 
            'screenshot_count'  => array( 'description' => __( 'Number of screenshots the theme has.', 'multitool' ) ), 
            'screenshots'       => array( 'description' => __( 'Array of screenshot URLs', 'multitool' ) ), 
            'rating'            => array( 'description' => __( 'Themes rating as a percentage.', 'multitool' ) ),
            'num_ratings'       => array( 'description' => __( 'Number of times the theme has been rated.', 'multitool' ) ), 
            'downloaded'        => array( 'description' => __( 'Number of times the theme has been downloaded.', 'multitool' ) ), 
            'sections'          => array( 'description' => __( 'Array of the data from each section on the plugins page.', 'multitool' ) ),
            'description'       => array( 'description' => __( 'Description of the theme.', 'multitool' ) ),
            'download_link'     => array( 'description' => __( 'Unsure, please update. Is it a HTML link or URL?', 'multitool' ) ),
            'name'              => array( 'description' => __( 'Name of the theme.', 'multitool' ) ),
            'slug'              => array( 'description' => __( 'The themes slug, may not match themes full name.', 'multitool' ) ),
            'tags'              => array( 'description' => __( 'Theme tags as found in readme.txt', 'multitool' ) ),
            'homepage'          => array( 'description' => __( 'Themes home page.', 'multitool' ) ),
            'contributors'      => array( 'description' => __( 'Array of contributors.', 'multitool' ) ),
            'last_updated'      => array( 'description' => __( 'Unsure, please update. Is it the authors stated last update month and year or is it a repository time.', 'multitool' ) ),
        );
    }
}