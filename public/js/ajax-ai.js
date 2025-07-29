/**
 * Admin JavaScript for Web Ex Machina AI Toolbox
 */
jQuery.noConflict();
jQuery( document ).ready(function( $ ) {

    const baseUrl = "/contao/webex-ai/seo/";

    // Wait for DOM to be ready
    $(document).ready(function() {
        // Initialize the UI
        initializeUI();
    });

    /**
     * Initialize the UI and event handlers
     */
    function initializeUI() {
        // Add event listener for "Optimize the title" button
        $('button[data-title]').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const postId = button.data('id');
            // Disable button and show loading state
            button.prop('disabled', true).text('Optimizing...');

            // Call the optimize title AJAX function
            optimizeTitle(postId, button, "title");
        });

        // Add event listener for "Optimize the description" button
        $('button[data-description]').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const postId = button.data('id');

            // Disable button and show loading state
            button.prop('disabled', true).text('Optimizing...');

            // Call the optimize description AJAX function
            optimizeDescription(postId, button, "description");
        });


        $('button[data-save-id-description]').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const postId = button.data('save-id-description');

            button.prop('disabled', true).text('Saving...');

            saveDescription(postId, button);
        });

        $('button[data-save-id-title]').on('click', function(e) {
            e.preventDefault();
            const button = $(this);
            const postId = button.data('save-id-title');

            button.prop('disabled', true).text('Saving...');

            saveTitle(postId, button);
        });

    }

    /**
     * Optimize the title of a post
     *
     * @param {number} postId - The ID of the post
     * @param {jQuery} button - The button element
     * @param {string} champ - The button element
     */
    function optimizeTitle(postId, button, champ) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                champ: champ,
                post_id: postId
            },
            success: function(response) {
                if (response.success) {
                    // Update the title in the UI
                    document.getElementById("title-" + postId).setAttribute('value', response.data.title);

                    // Show success message
                    showNotification('success', response.data.message);
                } else {
                    // Show error message
                    console.log(response);
                    showNotification('error', response.data.message);
                }
            },
            error: function(response) {
                // Show error message
                showNotification('error', 'An error occurred while optimizing the title.');
            },
            complete: function() {
                button.prop('disabled', false).text('Optimize the title');
                document.getElementById("button-title-" + postId).removeAttribute('disabled');
            }
        });
    }

    /**
     * Optimize the description of a post
     *
     * @param {number} postId - The ID of the post
     * @param {jQuery} button - The button element
     * @param {string} champ - The button element
     */
    function optimizeDescription(postId, button, champ) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                champ: champ,
                post_id: postId,
            },
            success: function(response) {
                if (response.success) {
                    document.getElementById("description-" + postId).setAttribute('value', response.data.description);
                    showNotification('success', response.data.message);
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function(response) {
                showNotification('error', 'An error occurred while optimizing the description.');
            },
            complete: function() {

                button.prop('disabled', false).text('Optimize the description');
                document.getElementById("button-description-" + postId).removeAttribute('disabled');
            }
        });
    }


    function saveDescription(postId, button) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                action: 'save_description',
                post_id: postId,
                content: document.getElementById("description-" + postId).value,
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.data.message);
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function(response) {
                showNotification('error', 'An error occurred while saving the description.');
            },
            complete: function() {
                button.prop('disabled', false).text('Save');
            }
        });
    }

    function saveTitle(postId, button) {
        $.ajax({
            url: baseUrl,
            type: 'POST',
            data: {
                action: 'save_title',
                post_id: postId,
                content: document.getElementById("title-" + postId).value,
            },
            success: function(response) {
                if (response.success) {
                    showNotification('success', response.data.message);
                } else {
                    showNotification('error', response.data.message);
                }
            },
            error: function(response) {
                showNotification('error', 'An error occurred while saving the title.');
            },
            complete: function() {
                button.prop('disabled', false).text('Save');
            }
        });
    }


    /**
     * Show a notification message
     *
     * @param {string} type - The type of notification ('success' or 'error')
     * @param {string} message - The message to display
     */
    function showNotification(type, message) {
        const notificationClass = type === 'success' ? 'notice-success' : 'notice-error';

        // Create notification element
        const notification = $('<div class="notice ' + notificationClass + ' is-dismissible"><p>' + message + '</p></div>');

        // Add notification to the top of the page
        $('#notification').prepend(notification);

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

});