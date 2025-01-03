jQuery(document).ready(function($) {
    $('.floating-contacts-color-field').wpColorPicker({
        defaultColor: '#0073aa',
    });

    var customLinkIndex = FC_Admin.totalCustomLinks;
    var $customLinksContainer = $('#floating-contacts-custom-links');

    $('#add-custom-link').on('click', function() {
        var template = wp.template('floating-contacts-custom-link');
        $customLinksContainer.append(template({ index: customLinkIndex }));
        customLinkIndex++;
    });

    $(document).on('click', '.remove-custom-link', function() {
        $(this).closest('.fc-custom-link-item').remove();
    });

    // Icon preview functionality
    $customLinksContainer.on('input', '.fc-icon-input', function() {
        var $input = $(this);
        var $preview = $input.next('.fc-icon-preview');
        
        if (!$preview.length) {
            $preview = $('<span class="fc-icon-preview"></span>').insertAfter($input);
        }

        var iconName = $input.val().trim();
        if (iconName) {
            $preview.html('<i class="' + iconName + '"></i>');
        } else {
            $preview.empty();
        }
    });

    // Trigger icon preview on page load for existing inputs
    $('.fc-icon-input').trigger('input');
});
