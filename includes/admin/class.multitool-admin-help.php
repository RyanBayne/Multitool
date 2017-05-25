<?php
/**
 * Add the default content to the help tab.
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0.0
 */
          
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_Admin_Help', false ) ) :

/**
 * Multitool_Admin_Help Class.
 */
class Multitool_Admin_Help {
    public $mailchimp_id = 'a270c16691';

    /**
     * Hook in tabs.
     */
    public function __construct() {
        add_action( 'current_screen', array( $this, 'add_tabs' ), 50 );
    }

    /**
     * Add Contextual help tabs.
     */
    public function add_tabs() {
        $screen = get_current_screen();
                  
        if ( ! $screen || ! in_array( $screen->id, multitool_get_screen_ids() ) ) {
            return;
        }
        
        $page = empty( $_GET['page'] ) ? '' : sanitize_title( $_GET['page'] );
        $tab  = empty( $_GET['tab'] ) ? '' : sanitize_title( $_GET['tab'] );

        $screen->add_help_tab( array(
            'id'        => 'multitool_support_tab',
            'title'     => __( 'Help &amp; Support', 'multitool' ),
            'content'   => '<h2>' . __( 'Help &amp; Support', 'multitool' ) . '</h2>' . 
            '<p><a href="' . MULTITOOL_SKYPE . '" class="button button-primary">' . __( 'Skype', 'multitool' ) . 
            '</a> <a href="' . MULTITOOL_AUTHOR_SLACK .'" class="button button-primary">' . __( 'Slack', 'multitool' ) . 
            '</a> <a href="' . MULTITOOL_TRELLO . '" class="button button-primary">' . __( 'Trello', 'multitool' ) . 
            '</a> <a href="' . MULTITOOL_GITHUB . '/issues" class="button button-primary">' . __( 'Bugs', 'multitool' ) . '</a> </p>',
        ) );

        if( defined( 'MULTITOOL_GITHUB' ) ) { 
            $screen->add_help_tab( array(
                'id'        => 'multitool_bugs_tab',
                'title'     => __( 'Found a bug?', 'multitool' ),
                'content'   =>
                    '<h2>' . __( 'Please Report Bugs!', 'multitool' ) . '</h2>' .
                    '<p>You could save a lot of people a lot of time by reporting issues. Tell the developers and community what has gone wrong by creating a ticket. Please explain what you were doing, what you expected from your actions and what actually happened. Screenshots and short videos are often a big help as the evidence saves us time, we will give you cookies in return.</p>' .  
                    '<p><a href="' . MULTITOOL_GITHUB . '/issues?state=open' . '" class="button button-primary">' . __( 'Report a bug', 'multitool' ) . '</a></p>',
            ) );
        }
        
        /**
        * This is the right side sidebar, usually displaying a list of links. 
        * 
        * @var {WP_Screen|WP_Screen}
        */
        $screen->set_help_sidebar(
            '<p><strong>' . __( 'For more information:', 'multitool' ) . '</strong></p>' .
            '<p><a href="' . MULTITOOL_GITHUB . '/wiki" target="_blank">' . __( 'About Multitool', 'multitool' ) . '</a></p>' .
            '<p><a href="' . MULTITOOL_GITHUB . '" target="_blank">' . __( 'Github project', 'multitool' ) . '</a></p>' .
            '<p><a href="' . MULTITOOL_GITHUB . '/blob/master/CHANGELOG.txt" target="_blank">' . __( 'Change Log', 'multitool' ) . '</a></p>' .
            '<p><a href="' . MULTITOOL_HOME . '" target="_blank">' . __( 'Blog', 'multitool' ) . '</a></p>'
        );
        
        $screen->add_help_tab( array(
            'id'        => 'multitool_wizard_tab',
            'title'     => __( 'Setup wizard', 'multitool' ),
            'content'   =>
                '<h2>' . __( 'Setup wizard', 'multitool' ) . '</h2>' .
                '<p>' . __( 'If you need to access the setup wizard again, please click on the button below.', 'multitool' ) . '</p>' .
                '<p><a href="' . admin_url( 'index.php?page=multitool-setup' ) . '" class="button button-primary">' . __( 'Setup wizard', 'multitool' ) . '</a></p>',
        ) );   
             
        $screen->add_help_tab( array(
            'id'        => 'multitool_tutorial_tab',
            'title'     => __( 'Tutorial', 'multitool' ),
            'content'   =>
                '<h2>' . __( 'Pointers Tutorial', 'multitool' ) . '</h2>' .
                '<p>' . __( 'The plugin will explain some features using WordPress pointers.', 'multitool' ) . '</p>' .
                '<p><a href="' . admin_url( 'admin.php?page=multitool-quick&amp;multitooltutorial=normal' ) . '" class="button button-primary">' . __( 'Start Tutorial', 'multitool' ) . '</a></p>',
        ) );
  
        $screen->add_help_tab( array(
            'id'        => 'multitool_contribute_tab',
            'title'     => __( 'Contribute', 'multitool' ),
            'content'   => '<h2>' . __( 'Everyone Can Contribute', 'multitool' ) . '</h2>' .
            '<p>' . __( 'You can contribute in many ways and by doing so you will help the project thrive.' ) . '</p>' .
            '<p><a href="' . MULTITOOL_DONATE . '" class="button button-primary">' . __( 'Donate', 'multitool' ) . '</a> <a href="' . MULTITOOL_GITHUB . '/wiki" class="button button-primary">' . __( 'Update Wiki', 'multitool' ) . '</a> <a href="' . MULTITOOL_GITHUB . '/issues" class="button button-primary">' . __( 'Fix Bugs', 'multitool' ) . '</a></p>',
        ) );

        $screen->add_help_tab( array(
            'id'        => 'multitool_newsletter_tab',
            'title'     => __( 'Newsletter', 'multitool' ),
            'content'   => '<h2>' . __( 'Annual Newsletter', 'multitool' ) . '</h2>' .
            '<p>' . __( 'Mailchip is used to manage the projects newsletter subscribers list.' ) . '</p>' .
            '<p>' . '<!-- Begin MailChimp Signup Form -->
                <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
                <style type="text/css">         
                    #mc_embed_signup{background:#f6fbfd; clear:left; font:14px Helvetica,Arial,sans-serif; }
                    /* Add your own MailChimp form style overrides in your site stylesheet or in this style block.
                       We recommend moving this block and the preceding CSS link to the HEAD of your HTML file. */
                </style>
                <div id="mc_embed_signup">
                <form action="//webtechglobal.us9.list-manage.com/subscribe/post?u=99272fe1772de14ff2be02fe6&amp;id=' . $this->mailchimp_id . '" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                    <div id="mc_embed_signup_scroll">
                    <h2>Multitool Annual Newsletter</h2>
                <div class="indicates-required"><span class="asterisk">*</span> indicates required</div>
                <div class="mc-field-group">
                    <label for="mce-EMAIL">Email Address  <span class="asterisk">*</span>
                </label>
                    <input type="email" value="" name="EMAIL" class="required email" id="mce-EMAIL">
                </div>
                <div class="mc-field-group">
                    <label for="mce-FNAME">First Name </label>
                    <input type="text" value="" name="FNAME" class="" id="mce-FNAME">
                </div>
                <div class="mc-field-group">
                    <label for="mce-LNAME">Last Name </label>
                    <input type="text" value="" name="LNAME" class="" id="mce-LNAME">
                </div>
                <p>Powered by <a href="http://eepurl.com/2W_2n" title="MailChimp - email marketing made easy and fun">MailChimp</a></p>
                    <div id="mce-responses" class="clear">
                        <div class="response" id="mce-error-response" style="display:none"></div>
                        <div class="response" id="mce-success-response" style="display:none"></div>
                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_99272fe1772de14ff2be02fe6_' . $this->mailchimp_id . '" tabindex="-1" value=""></div>
                    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                    </div>
                </form>
                </div>
                <script type=\'text/javascript\' src=\'//s3.amazonaws.com/downloads.mailchimp.com/js/mc-validate.js\'></script><script type=\'text/javascript\'>(function($) {window.fnames = new Array(); window.ftypes = new Array();fnames[0]=\'EMAIL\';ftypes[0]=\'email\';fnames[1]=\'FNAME\';ftypes[1]=\'text\';fnames[2]=\'LNAME\';ftypes[2]=\'text\';}(jQuery));var $mcj = jQuery.noConflict(true);</script>
                <!--End mc_embed_signup-->' . '</p>',
        ) );
        
        $screen->add_help_tab( array(
            'id'        => 'multitool_credits_tab',
            'title'     => __( 'Credits', 'multitool' ),
            'content'   => '<h2>' . __( 'Credits', 'multitool' ) . '</h2>' .
            '<p>Please do not remove credits from the plugin. You may edit them or give credit somewhere else in your project.</p>' . 
            '<h4>' . __( 'Automattic - they created the best way to create plugins so we can all get more from WP.' ) . '</h4>' .
            '<h4>' . __( 'Brian at WPMUDEV - our discussion led to this project and entirely new approach in my development.' ) . '</h4>' . 
            '<h4>' . __( 'Ignacio Cruz at WPMUDEV - has giving us a good approach to handling shortcodes.' ) . '</h4>' .
            '<h4>' . __( 'Ashley Rich (A5shleyRich) - author of a crucial piece of the puzzle, related to asynchronous background tasks.' ) . '</h4>' .
            '<h4>' . __( 'Igor Vaynberg - thank you for an elegant solution to searching within a menu.' ) . '</h4>'
        ) );
                    
        $screen->add_help_tab( array(
            'id'        => 'multitool_faq_tab',
            'title'     => __( 'FAQ', 'multitool' ),
            'content'   => '',
            'callback'  => array( $this, 'faq' ),
        ) );
                        
    }
    
    public function faq() {
        $questions = array(
            0 => __( '-- Select a question --', 'multitool' ),
            1 => __( "Can I add my own tools to Multitool?", 'multitool' ),
            2 => __( "What are Quick Tools?", 'multitool' ),
            3 => __( "What are Configuration Tools?", 'appointments' ),
            4 => __( "What are Advanced Tools?", 'appointments' ),
            5 => __( "What are Multitool extensions?", 'appointments' ),
            6 => __( "Can I hire Ryan to add new tools?", 'appointments' )
        );  
        
        ?>

        <style>
            .faq-answers li {
                background:white;
                padding:10px 20px;
                border:1px solid #cacaca;
            }
        </style>

        <p>
            <ul id="faq-index">
                <?php foreach ( $questions as $question_index => $question ): ?>
                    <li data-answer="<?php echo $question_index; ?>"><a href="#q<?php echo $question_index; ?>"><?php echo $question; ?></a></li>
                <?php endforeach; ?>
            </ul>
        </p>
        
        <ul class="faq-answers">
            <li class="faq-answer" id='q1'>
                <p> <?php _e('Yes, you can make a request for new tools to be added or contribute to the project. You can also create an extesion which is basically a WP plugin without an interface for it to work alone.', 'multitool');?> </p>
            </li>
            <li class="faq-answer" id='q2'>
                <p> <?php _e('Quick Tools are also known as one-click tools or one-click actions. They are always added to the Quick Tools table.', 'multitool');?> </p>
            </li>

            <li class="faq-answer" id='q3'>
                <p> <?php _e('Configuration Tools have options that are saved. As a result of requiring form fields they are not added to the Quick Tools table due to how complex a single page would become. Instead I choose to use the same approach as a settings page and make use of the WordPress options API. This makes the development of Configuration Tools easy. Some configuration tools can filter or perform an action when triggered. Thus requiring proper storage of options.', 'multitool');?> </p>
            </li>

            <li class="faq-answer" id='q4'>
                <p><?php _e('Advanced Tools take users through a step by step procedure. Each step will offer a form of options. Your choices will determine what the next step displays. This approach is commonly used for export or import plugins. Advanced Tools are basically like plugins condensed into a specific approach. This way the user is familiar with the steps, how the system works and the core plugin can be relied on.', 'multitool');?></p>
            </li>
            <li class="faq-answer" id='q5'>
                <p> <?php _e('Multitool extensions are essentially WordPress plugins that add more tools to the plugin. They do not have any administration menu or interface for use on their own. They do nothing on their own at all and if created in the way I recommend. They will not activate until the core Multitool plugin is activate. That does not mean a normal plugin cannot integrate and act like an extension. The purpose of extensions is adding your own tools without releasing them to the public. We also have an opportunity to create libraries of premium tools and sell them on CodeCanyon.', 'multitool');?> </p>
            </li>        
        </ul>
             
        <script>
            jQuery( document).ready( function( $ ) {
                var selectedQuestion = '';

                function selectQuestion() {
                    var q = $( '#' + $(this).val() );
                    if ( selectedQuestion.length ) {
                        selectedQuestion.hide();
                    }
                    q.show();
                    selectedQuestion = q;
                }

                var faqAnswers = $('.faq-answer');
                var faqIndex = $('#faq-index');
                faqAnswers.hide();
                faqIndex.hide();

                var indexSelector = $('<select/>')
                    .attr( 'id', 'question-selector' )
                    .addClass( 'widefat' );
                var questions = faqIndex.find( 'li' );
                var advancedGroup = false;
                questions.each( function () {
                    var self = $(this);
                    var answer = self.data('answer');
                    var text = self.text();
                    var option;

                    if ( answer === 99 ) {
                        advancedGroup = $( '<optgroup />' )
                            .attr( 'label', "<?php _e( 'Advanced: This part of FAQ requires some knowledge about HTML, PHP and/or WordPress coding.', 'appointments' ); ?>" );

                        indexSelector.append( advancedGroup );
                    }

                    if ( answer !== '' && text !== '' ) {
                        option = $( '<option/>' )
                            .val( 'q' + answer )
                            .text( text );
                        if ( advancedGroup ) {
                            advancedGroup.append( option );
                        }
                        else {
                            indexSelector.append( option );
                        }

                    }

                });

                faqIndex.after( indexSelector );
                indexSelector.before(
                    $('<label />')
                        .attr( 'for', 'question-selector' )
                        .text( "<?php _e( 'Select a question', 'appointments' ); ?>" )
                        .addClass( 'screen-reader-text' )
                );

                indexSelector.change( selectQuestion );
            });
        </script>        

        <?php 
    }
}

endif;

return new Multitool_Admin_Help();
