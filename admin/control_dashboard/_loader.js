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

const checkIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill text-success mx-2" viewBox="0 0 16 16">
  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
</svg>`;

$(document).on('click', '.btn-approve', function (e) {
    e.preventDefault();

    const $button = $(this); // Reference to the clicked button
    const walletId = $button.data('wallet-id'); // Get the wallet ID from data attribute

    if (walletId) {
        $.ajax({
            url: '_approve_wallet.php', // Update endpoint to process approval
            type: 'POST',
            data: { txn_id: walletId }, // Send encrypted wallet ID
            success: function (response) {
                if (response.success) {
                    const $parentRow = $button.closest('tr'); // Find the parent <tr> elemen
                    // Optionally append a success icon somewhere if needed
                    $button.after(checkIcon);
                    setTimeout(()=>{
                        $parentRow.fadeOut();//addClass('d-none');
                    },1000);
                } else {
                    alert('Failed to approve transaction: ' + (response.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('An error occurred while processing the transaction.');
            }
        });
    } else {
        alert('Invalid wallet ID.');
    }
});
