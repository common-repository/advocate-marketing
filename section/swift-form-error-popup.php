<?php
/**
 *      SwiftForm error popup.
 *      -   Showing SwiftForm error messages.
 */
if (isset($_GET['swift_err']) && isset($_GET['swift_err_msg']) && !empty($_GET['swift_err']) && !empty($_GET['swift_err_msg']) && $_GET['swift_err'] == 1) {
    add_action('wp_enqueue_scripts', 'swift_form_error_popup_script');
}
if (!function_exists('swift_form_error_popup_script')) {

    function swift_form_error_popup_script() {
        wp_enqueue_script('sf-error-popup', plugins_url('../js/sf-error-popup.js', __FILE__), array('jquery'), '', true);
    }

}

//add_action("wp_footer", "swift_form_error_popup_cb");

if (!function_exists('swift_form_error_popup_cb')) {

    function swift_form_error_popup_cb() {
        ?>
        <div class="sf-err-modal" id="SwiftFormErModal" style="display: none;">
            <div class="sf-err-modal-container">
                <div class="sf-err-modal-content">
                    <div class="sf-err-modal-alert">
                        <div class="sf-err-modal-close">&times;</div>
                        <span><img src="<?php echo plugins_url('../images/sf-alert.png', __FILE__); ?>" alt="alert" /></span>
                        <p><?php echo (isset($_GET['swift_err_msg']) && !empty($_GET['swift_err_msg'])) ? $_GET['swift_err_msg'] : ""; ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}