/**
 * Admin JavaScript for Web Ex Machina AI Toolbox
 */
jQuery.noConflict();
jQuery( document ).ready(function( $ ) {

    const baseUrl = "/contao/webex-ai/seo/query";
    const baseUrlSave = "/contao/webex-ai/seo/save";

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
            url: baseUrlSave,
            type: 'POST',
            data: {
                champ: 'description',
                post_id: postId,
                value: document.getElementById("description-" + postId).value,
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
            url: baseUrlSave,
            type: 'POST',
            data: {
                champ: 'title',
                post_id: postId,
                value: document.getElementById("title-" + postId).value,
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
        const baseClasses = 'flex items-center p-4 mb-4 rounded-lg shadow-md border-l-4 max-w-md';
        const typeClasses = type === 'success' 
            ? 'bg-green-50 border-green-500 text-green-800' 
            : 'bg-red-50 border-red-500 text-red-800';
        
        // Create icon based on type
        const icon = type === 'success'
            ? '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>'
            : '<svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';
        
        // Create notification element with Tailwind classes
        const notification = $('<div class="' + baseClasses + ' ' + typeClasses + '">' + 
            icon + 
            '<div class="ml-3 text-sm font-medium">' + message + '</div>' +
            '</div>');
        
        // Add notification to the top of the page
        $('#notification').prepend(notification);
        
        // Add click event to close button
        notification.find('button').on('click', function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        });

        // Auto-dismiss after 5 seconds
        setTimeout(function() {
            notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }

});