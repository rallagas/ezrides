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

$(document).on('click', '.btn-approve-decline', function (e) {
    e.preventDefault();

    const $button = $(this); // Reference to the clicked button
    const walletId = $button.data('wallet-id'); // Get the wallet ID from data attribute
    const actionId = $button.data('action-id');

    if (walletId) {
        $.ajax({
            url: '_approve_decline_wallet.php', // Update endpoint to process approval
            type: 'POST',
            data: { txn_id: walletId, action_id: actionId }, // Send encrypted wallet ID
            success: function (response) {
                if (response.success) {
                    const $parentRow = $button.closest('tr'); // Find the parent <tr> elemen
                    // Optionally append a success icon somewhere if needed
                    $button.html(checkIcon);
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


$(document).on('click', '.btnApproveRental', function (e) {
    e.preventDefault();

    const $button = $(this); // Reference to the clicked button
    const apptxnId = $button.data('apptxnid'); // Get the wallet ID from data attribute
    const actionId = $button.data('action-id');
    const amounttopay = $button.data('amounttopay');
    const userId = $button.data('userid');
    const detailId = $button.data('detailid');
    if (apptxnId) {
        $.ajax({
            url: '_approve_decline_rental.php', // Update endpoint to process approval
            type: 'POST',
            data: { app_txn_id: apptxnId
                  , action_id: actionId
                  , amount_to_pay: amounttopay 
                  , user_id: userId
                  , detail_id: detailId
                }, 
            success: function (response) {
                if (response.success) {
                    const $parentRow = $button.closest('tr'); // Find the parent <tr> elemen
                    // Optionally append a success icon somewhere if needed
                    $button.html(checkIcon);
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
        alert('Invalid Txn ID.');
    }
});


$(document).on('submit', '#FormNewItem', function (e) {
    e.preventDefault();

    const form = $(this)[0];
    const formData = new FormData(form);

    $.ajax({
        url: 'save_new_item.php', // Backend script to process the form
        type: 'POST',
        data: formData,
        processData: false, // Prevent jQuery from automatically processing the data
        contentType: false, // Let the server set the content type
        dataType: 'json', // Expect JSON response
        success: function (response) {
            if (response.success) {
                $(".txn_status").html('New Item Saved successfully!').removeClass("alert-danger").addClass("alert-success");
                form.reset(); // Reset the form after success
                $('#merchantSuggestions').empty(); // Clear suggestions
            } else {
                alert();
                $(".txn_status").html('Failed to save item: ' + (response.error || 'Unknown error.')).removeClass("alert-success").addClass("alert-danger");
            }
        },
        error: function (xhr, status, error) {
            console.error('AJAX error:', status, error);
            $(".txn_status").html('Failed to save item: ' + (error || 'Unknown error.')).removeClass("alert-success").addClass("alert-danger");
        }
    });
});

$(document).on("click",".img-preview",function(){
    const imageUrl = $(this).data("imgsrc");
    previewImage(imageUrl);
});

function previewImage(imageUrl) {
    // Set the image source
    $('#previewImage').attr('src', imageUrl);

    // Show the modal
    $('#imagePreviewModal').modal('show');
}