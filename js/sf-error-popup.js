/* Swift form error popup */
jQuery(document).ready(function($) {
    jQuery("#SwiftFormErModal").fadeIn();
    jQuery(".sf-err-modal-close").on('click',function(){
        jQuery("#SwiftFormErModal").fadeOut();
    });
});