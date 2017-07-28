<?php
/**
 * Multitool Admin Settings Class
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  Multitool/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Multitool_Admin_Settings' ) ) :

/**
 * Multitool_Admin_Settings Class.
 */
class Multitool_Admin_Settings {

    /**
     * Setting pages.
     *
     * @var array
     */
    private static $settings = array();

    /**
     * Error messages.
     *
     * @var array
     */
    private static $errors   = array();

    /**
     * Update messages.
     *
     * @var array
     */
    private static $messages = array();
    
    /**
    * This is more about configuration reminding.
    * 
    * @var mixed
    */
    private static $defaulttab = 'securitytools';

    /**
     * Include the settings page classes.
     */
    public static function get_settings_pages() {
        if ( empty( self::$settings ) ) {
            $settings = array();

            include_once( 'settings/class.multitool-settings-page.php' );

            $settings[] = include( 'settings/class.multitool-security-tools.php' );
            //$settings[] = include( 'settings/class.multitool-plugin-tools.php' );
            $settings[] = include( 'settings/class.multitool-developer-tools.php' );
            $settings[] = include( 'settings/class.multitool-plugin-settings.php' ); 
            
            
            self::$settings = apply_filters( 'multitool_get_settings_pages', $settings );
        }

        return self::$settings;
    }

    /**
     * Save the settings.
     */
    public static function save() {
        global $current_tab;

        if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'multitool-settings' ) ) {
            die( __( 'Action failed. Please refresh the page and retry.', 'multitool' ) );
        }

        // Trigger actions
        do_action( 'multitool_settings_save_' . $current_tab );
        do_action( 'multitool_update_options_' . $current_tab );
        do_action( 'multitool_update_options' );

        self::add_message( __( 'Your settings have been saved.', 'multitool' ) );
        self::check_download_folder_protection();
             
        // Clear any unwanted data and flush rules
        flush_rewrite_rules();

        do_action( 'multitool_settings_saved' );
    }

    /**
     * Add a message.
     * @param string $text
     */
    public static function add_message( $text ) {
        self::$messages[] = $text;
    }

    /**
     * Add an error.
     * @param string $text
     */
    public static function add_error( $text ) {
        self::$errors[] = $text;
    }

    /**
     * Output messages + errors.
     * @return string
     */
    public static function show_messages() {
        if ( sizeof( self::$errors ) > 0 ) {
            foreach ( self::$errors as $error ) {
                echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
            }
        } elseif ( sizeof( self::$messages ) > 0 ) {
            foreach ( self::$messages as $message ) {
                echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
            }
        }
    }

    /**
     * Settings page.
     *
     * Handles the display of the main multitool settings page in admin.
     */
    public static function output() {
        global $current_section, $current_tab;

        $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        do_action( 'multitool_settings_start' );

        // Include settings pages
        self::get_settings_pages();

        // Get current tab/section
        $current_tab     = empty( $_GET['tab'] ) ? self::$defaulttab : sanitize_title( $_GET['tab'] );
        $current_section = empty( $_REQUEST['section'] ) ? '' : sanitize_title( $_REQUEST['section'] );

        // Save settings if data has been posted
        if ( ! empty( $_POST ) ) {
            self::save();
        }

        // Add any posted messages
        if ( ! empty( $_GET['multitool_error'] ) ) {
            self::add_error( stripslashes( $_GET['multitool_error'] ) );
        }

        if ( ! empty( $_GET['multitool_message'] ) ) {
            self::add_message( stripslashes( $_GET['multitool_message'] ) );
        }

        // Get tabs for the settings page
        $tabs = apply_filters( 'multitool_settings_tabs_array', array() );

        include 'views/html-admin-settings.php';
    }

    /**
     * Get a setting from the settings API.
     *
     * @param mixed $option_name
     * @return string
     */
    public static function get_option( $option_name, $default = '' ) {
        // Array value
        if ( strstr( $option_name, '[' ) ) {

            parse_str( $option_name, $option_array );

            // Option name is first key
            $option_name = current( array_keys( $option_array ) );

            // Get value
            $option_values = get_option( $option_name, '' );

            $key = key( $option_array[ $option_name ] );

            if ( isset( $option_values[ $key ] ) ) {
                $option_value = $option_values[ $key ];
            } else {
                $option_value = null;
            }

        // Single value
        } else {
            $option_value = get_option( $option_name, null );
        }

        if ( is_array( $option_value ) ) {
            $option_value = array_map( 'stripslashes', $option_value );
        } elseif ( ! is_null( $option_value ) ) {
            $option_value = stripslashes( $option_value );
        }

        return $option_value === null ? $default : $option_value;
    }

    /**
     * Output admin fields.
     *
     * Loops though the multitool options array and outputs each field.
     *
     * @param array $options Opens array to output
     */
    public static function output_fields( $options ) {
        foreach ( $options as $value ) {
            if ( ! isset( $value['type'] ) ) {
                continue;
            }
            if ( ! isset( $value['id'] ) ) {
                $value['id'] = '';
            }
            if ( ! isset( $value['title'] ) ) {
                $value['title'] = isset( $value['name'] ) ? $value['name'] : '';
            }
            if ( ! isset( $value['class'] ) ) {
                $value['class'] = '';
            }
            if ( ! isset( $value['css'] ) ) {
                $value['css'] = '';
            }
            if ( ! isset( $value['default'] ) ) {
                $value['default'] = '';
            }
            if ( ! isset( $value['desc'] ) ) {
                $value['desc'] = '';
            }
            if ( ! isset( $value['desc_tip'] ) ) {
                $value['desc_tip'] = false;
            }
            if ( ! isset( $value['placeholder'] ) ) {
                $value['placeholder'] = '';
            }

            // Custom attribute handling
            $custom_attributes = array();

            if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
                foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
                    $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
                }
            }

            // Description handling
            $field_description = self::get_field_description( $value );
            extract( $field_description );

            // Switch based on type
            switch ( $value['type'] ) {

                // Section Titles
                case 'title':
                    if ( ! empty( $value['title'] ) ) {
                        echo '<h2>' . esc_html( $value['title'] ) . '</h2>';
                    }
                    if ( ! empty( $value['desc'] ) ) {
                        echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
                    }
                    echo '<table class="form-table">'. "\n\n";
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'multitool_settings_' . sanitize_title( $value['id'] ) );
                    }
                    break;

                // Section Ends
                case 'sectionend':
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'multitool_settings_' . sanitize_title( $value['id'] ) . '_end' );
                    }
                    echo '</table>';
                    if ( ! empty( $value['id'] ) ) {
                        do_action( 'multitool_settings_' . sanitize_title( $value['id'] ) . '_after' );
                    }
                    break;

                // Standard text inputs and subtypes like 'number'
                case 'text':
                case 'email':
                case 'number':
                case 'color' :
                case 'password' :

                    $type         = $value['type'];
                    $option_value = self::get_option( $value['id'], $value['default'] );

                    if ( $value['type'] == 'color' ) {
                        $type = 'text';
                        $value['class'] .= 'colorpick';
                        $description .= '<div id="colorPickerDiv_' . esc_attr( $value['id'] ) . '" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>';
                    }

                    ?><tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php echo $tooltip_html; ?>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
                            <?php
                            if ( 'color' == $value['type'] ) {
                                echo '<span class="colorpickpreview" style="background: ' . esc_attr( $option_value ) . ';"></span>';
                            }
                            ?>
                            <input
                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                type="<?php echo esc_attr( $type ); ?>"
                                style="<?php echo esc_attr( $value['css'] ); ?>"
                                value="<?php echo esc_attr( $option_value ); ?>"
                                class="<?php echo esc_attr( $value['class'] ); ?>"
                                placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
                                <?php echo implode( ' ', $custom_attributes ); ?>
                                /> <?php echo $description; ?>
                        </td>
                    </tr><?php
                    break;

                // Textarea
                case 'textarea':

                    $option_value = self::get_option( $value['id'], $value['default'] );

                    ?><tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php echo $tooltip_html; ?>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
                            <?php echo $description; ?>

                            <textarea
                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                style="<?php echo esc_attr( $value['css'] ); ?>"
                                class="<?php echo esc_attr( $value['class'] ); ?>"
                                placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
                                <?php echo implode( ' ', $custom_attributes ); ?>
                                ><?php echo esc_textarea( $option_value );  ?></textarea>
                        </td>
                    </tr><?php
                    break;

                // Select boxes
                case 'select' :
                case 'multiselect' :

                    $option_value = self::get_option( $value['id'], $value['default'] );

                    ?><tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php echo $tooltip_html; ?>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
                            <select
                                name="<?php echo esc_attr( $value['id'] ); ?><?php if ( $value['type'] == 'multiselect' ) echo '[]'; ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                style="<?php echo esc_attr( $value['css'] ); ?>"
                                class="<?php echo esc_attr( $value['class'] ); ?>"
                                <?php echo implode( ' ', $custom_attributes ); ?>
                                <?php echo ( 'multiselect' == $value['type'] ) ? 'multiple="multiple"' : ''; ?>
                                >
                                <?php
                                    foreach ( $value['options'] as $key => $val ) {
                                        ?>
                                        <option value="<?php echo esc_attr( $key ); ?>" <?php

                                            if ( is_array( $option_value ) ) {
                                                selected( in_array( $key, $option_value ), true );
                                            } else {
                                                selected( $option_value, $key );
                                            }

                                        ?>><?php echo $val ?></option>
                                        <?php
                                    }
                                ?>
                            </select> <?php echo $description; ?>
                        </td>
                    </tr><?php
                    break;

                // Radio inputs
                case 'radio' :

                    $option_value = self::get_option( $value['id'], $value['default'] );

                    ?><tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php echo $tooltip_html; ?>
                        </th>
                        <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
                            <fieldset>
                                <?php echo $description; ?>
                                <ul>
                                <?php
                                    foreach ( $value['options'] as $key => $val ) {
                                        ?>
                                        <li>
                                            <label><input
                                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                                value="<?php echo $key; ?>"
                                                type="radio"
                                                style="<?php echo esc_attr( $value['css'] ); ?>"
                                                class="<?php echo esc_attr( $value['class'] ); ?>"
                                                <?php echo implode( ' ', $custom_attributes ); ?>
                                                <?php checked( $key, $option_value ); ?>
                                                /> <?php echo $val ?></label>
                                        </li>
                                        <?php
                                    }
                                ?>
                                </ul>
                            </fieldset>
                        </td>
                    </tr><?php
                    break;

                // Checkbox input
                case 'checkbox' :

                    $option_value    = self::get_option( $value['id'], $value['default'] );
                    $visbility_class = array();

                    if ( ! isset( $value['hide_if_checked'] ) ) {
                        $value['hide_if_checked'] = false;
                    }
                    if ( ! isset( $value['show_if_checked'] ) ) {
                        $value['show_if_checked'] = false;
                    }
                    if ( 'yes' == $value['hide_if_checked'] || 'yes' == $value['show_if_checked'] ) {
                        $visbility_class[] = 'hidden_option';
                    }
                    if ( 'option' == $value['hide_if_checked'] ) {
                        $visbility_class[] = 'hide_options_if_checked';
                    }
                    if ( 'option' == $value['show_if_checked'] ) {
                        $visbility_class[] = 'show_options_if_checked';
                    }

                    if ( ! isset( $value['checkboxgroup'] ) || 'start' == $value['checkboxgroup'] ) {
                        ?>
                            <tr valign="top" class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
                                <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
                                <td class="forminp forminp-checkbox">
                                    <fieldset>
                        <?php
                    } else {
                        ?>
                            <fieldset class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
                        <?php
                    }

                    if ( ! empty( $value['title'] ) ) {
                        ?>
                            <legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ) ?></span></legend>
                        <?php
                    }

                    ?>
                        <label for="<?php echo $value['id'] ?>">
                            <input
                                name="<?php echo esc_attr( $value['id'] ); ?>"
                                id="<?php echo esc_attr( $value['id'] ); ?>"
                                type="checkbox"
                                class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
                                value="1"
                                <?php checked( $option_value, 'yes'); ?>
                                <?php echo implode( ' ', $custom_attributes ); ?>
                            /> <?php echo $description ?>
                        </label> <?php echo $tooltip_html; ?>
                    <?php

                    if ( ! isset( $value['checkboxgroup'] ) || 'end' == $value['checkboxgroup'] ) {
                                    ?>
                                    </fieldset>
                                </td>
                            </tr>
                        <?php
                    } else {
                        ?>
                            </fieldset>
                        <?php
                    }
                    break;

                // Default: run an action
                default:
                    do_action( 'multitool_admin_field_' . $value['type'], $value );
                    break;
            }
        }
    }

    /**
     * Helper function to get the formated description and tip HTML for a
     * given form field. Plugins can call this when implementing their own custom
     * settings types.
     *
     * @param  array $value The form field value array
     * @return array The description and tip as a 2 element array
     */
    public static function get_field_description( $value ) {
        $description  = '';
        $tooltip_html = '';

        if ( true === $value['desc_tip'] ) {
            $tooltip_html = $value['desc'];
        } elseif ( ! empty( $value['desc_tip'] ) ) {
            $description  = $value['desc'];
            $tooltip_html = $value['desc_tip'];
        } elseif ( ! empty( $value['desc'] ) ) {
            $description  = $value['desc'];
        }

        if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ) ) ) {
            $description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
        } elseif ( $description && in_array( $value['type'], array( 'checkbox' ) ) ) {
            $description = wp_kses_post( $description );
        } elseif ( $description ) {
            $description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
        }

        if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ) ) ) {
            $tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
        } elseif ( $tooltip_html ) {
            $tooltip_html = multitool_help_tip( $tooltip_html );
        }

        return array(
            'description'  => $description,
            'tooltip_html' => $tooltip_html
        );
    }

    /**
     * Save admin fields.
     *
     * Loops though the multitool options array and outputs each field.
     *
     * @param array $options Options array to output
     * @return bool
     */
    public static function save_fields( $options ) {
        if ( empty( $_POST ) ) {
            return false;
        }

        // Options to update will be stored here and saved later.
        $update_options = array();

        // Loop options and get values to save.
        foreach ( $options as $option ) {
            if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) ) {
                continue;
            }

            // Get posted value.
            if ( strstr( $option['id'], '[' ) ) {
                parse_str( $option['id'], $option_name_array );
                $option_name  = current( array_keys( $option_name_array ) );
                $setting_name = key( $option_name_array[ $option_name ] );
                $raw_value    = isset( $_POST[ $option_name ][ $setting_name ] ) ? wp_unslash( $_POST[ $option_name ][ $setting_name ] ) : null;
            } else {
                $option_name  = $option['id'];
                $setting_name = '';
                $raw_value    = isset( $_POST[ $option['id'] ] ) ? wp_unslash( $_POST[ $option['id'] ] ) : null;
            }

            // Format the value based on option type.
            switch ( $option['type'] ) {
                case 'checkbox' :
                    $value = is_null( $raw_value ) ? 'no' : 'yes';
                    break;
                case 'textarea' :
                    $value = wp_kses_post( trim( $raw_value ) );
                    break;
                case 'multiselect' :
                case 'multi_select_countries' :
                    $value = array_filter( array_map( 'multitool_clean', (array) $raw_value ) );
                    break;
                case 'image_width' :
                    $value = array();
                    if ( isset( $raw_value['width'] ) ) {
                        $value['width']  = multitool_clean( $raw_value['width'] );
                        $value['height'] = multitool_clean( $raw_value['height'] );
                        $value['crop']   = isset( $raw_value['crop'] ) ? 1 : 0;
                    } else {
                        $value['width']  = $option['default']['width'];
                        $value['height'] = $option['default']['height'];
                        $value['crop']   = $option['default']['crop'];
                    }
                    break;
                default :
                    $value = multitool_clean( $raw_value );
                    break;
            }

            /**
             * Fire an action when a certain 'type' of field is being saved.
             * @deprecated 2.4.0 - doesn't allow manipulation of values!
             */
            if ( has_action( 'multitool_update_option_' . sanitize_title( $option['type'] ) ) ) {
                _deprecated_function( 'The multitool_update_option_X action', '2.4.0', 'multitool_admin_settings_sanitize_option filter' );
                do_action( 'multitool_update_option_' . sanitize_title( $option['type'] ), $option );
                continue;
            }

            /**
             * Sanitize the value of an option.
             * @since 2.4.0
             */
            $value = apply_filters( 'multitool_admin_settings_sanitize_option', $value, $option, $raw_value );

            /**
             * Sanitize the value of an option by option name.
             * @since 2.4.0
             */
            $value = apply_filters( "multitool_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

            if ( is_null( $value ) ) {
                continue;
            }

            // Check if option is an array and handle that differently to single values.
            if ( $option_name && $setting_name ) {
                if ( ! isset( $update_options[ $option_name ] ) ) {
                    $update_options[ $option_name ] = get_option( $option_name, array() );
                }
                if ( ! is_array( $update_options[ $option_name ] ) ) {
                    $update_options[ $option_name ] = array();
                }
                $update_options[ $option_name ][ $setting_name ] = $value;
            } else {
                $update_options[ $option_name ] = $value;
            }

            /**
             * Fire an action before saved.
             * @deprecated 2.4.0 - doesn't allow manipulation of values!
             */
            do_action( 'multitool_update_option', $option );
        }

        // Save all options in our array.
        foreach ( $update_options as $name => $value ) {
            update_option( $name, $value );
        }

        return true;
    }

    /**
     * Checks which method we're using to serve downloads.
     *
     * If using force or x-sendfile, this ensures the .htaccess is in place.
     */
    public static function check_download_folder_protection() {
        $upload_dir      = wp_upload_dir();
        $downloads_url   = $upload_dir['basedir'] . '/multitool_uploads';
        $download_method = get_option( 'multitool_file_download_method' );

        if ( 'redirect' == $download_method ) {

            // Redirect method - don't protect
            if ( file_exists( $downloads_url . '/.htaccess' ) ) {
                unlink( $downloads_url . '/.htaccess' );
            }

        } else {

            // Force method - protect, add rules to the htaccess file
            if ( ! file_exists( $downloads_url . '/.htaccess' ) ) {
                if ( $file_handle = @fopen( $downloads_url . '/.htaccess', 'w' ) ) {
                    fwrite( $file_handle, 'deny from all' );
                    fclose( $file_handle );
                }
            }
        }
    }
}

endif;
