$(document).ready(function() {
    // Function to load the side panel content

    // Load PHP function
    function loadContent(param1, param2) {
        console.log('Loading content from:', param1, 'into:', param2); // Added logging for debugging
        $.ajax({
            url: param1,
            method: 'GET',
            success: function(response) {
                 console.log('Response received:', response); // Log the response
                $('#' + param2).html(response); // Load response into the div
            },
            error: function(xhr, status, error) {
                console.error('Error loading content: ' + xhr.statusText); // Handle error
                $('#' + param2).html('Error loading content.');
            }
        });
    }

    // Call the function to load the side panel on page load
 //   loadSidePanel();

    // Add event listener to buttons with the class 'loadPage'
    $('.loadPage').on('click', function() {
        var pageToLoad = $(this).attr('load-page');
        console.log('Page to load:', pageToLoad); // Added logging for debugging
        var loadIntoElement = $(this).attr('load-into');
        console.log('Element to load into:', loadIntoElement); // Added logging for debugging

        // Ensure both attributes are defined
        if (pageToLoad && loadIntoElement) {
            loadContent(pageToLoad, loadIntoElement);
        } else {
            console.error('Missing load-page or load-into attributes'); // Handle missing attributes
        }
    });
});
