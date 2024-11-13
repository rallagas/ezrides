
// Function to load transaction history
let currentPage = 1; // Start on page 1
const pageSize = 5; // Number of transactions per page
let transactions = []; // Store the full list of transactions

// Function to load transaction history
function loadTransactionHistory() {
    $.ajax({
        url: 'ajax_fetch_wallet_transactions.php', // Endpoint to fetch transaction history
        type: 'GET',
        dataType: 'json', // Expecting JSON response
        success: function(response) {
            transactions = response; // Store the transactions
            renderTransactions(); // Render the current page of transactions
            renderPagination(); // Render the pagination controls
        },
        error: function(xhr, status, error) {
            console.error('Failed to load transaction history:', error);
        }
    });
}

// Function to render transactions for the current page
function renderTransactions() {
    const tbody = $('#transactionHistoryTable tbody');
    tbody.empty(); // Clear any existing rows

    // Get the transactions for the current page
    const pageTransactions = transactions.slice((currentPage - 1) * pageSize, currentPage * pageSize);

    // Loop through each transaction in the current page and create rows
    pageTransactions.forEach(transaction => {
        const row = `
            <tr>
                <td>$${transaction.amount}</td>
                <td>${transaction.type}</td>
                <td>${transaction.status}</td>
                <td>${transaction.date}</td>
            </tr>
        `;
        tbody.append(row);
    });
}


// Function to render pagination controls
function renderPagination() {
    const totalPages = Math.ceil(transactions.length / pageSize); // Total number of pages
    const paginationContainer = $('#pagination');

    paginationContainer.empty(); // Clear any existing pagination buttons

    // Add "Previous" button
    if (currentPage > 1) {
        paginationContainer.append(`<button class="btn btn-secondary" onclick="changePage(${currentPage - 1})">Previous</button>`);
    }

    // Add page number buttons
    for (let i = 1; i <= totalPages; i++) {
        const activeClass = (i === currentPage) ? 'active' : '';
        paginationContainer.append(`<button class="btn btn-secondary ${activeClass}" onclick="changePage(${i})">${i}</button>`);
    }

    // Add "Next" button
    if (currentPage < totalPages) {
        paginationContainer.append(`<button class="btn btn-secondary" onclick="changePage(${currentPage + 1})">Next</button>`);
    }
}

    
    // Function to change the page and re-render the data
function changePage(page) {
    if (page >= 1 && page <= Math.ceil(transactions.length / pageSize)) {
        currentPage = page;
        renderTransactions();
        renderPagination();
    }
}


$(document).ready(function(){

    
    
    
$('form#formRegistration').submit(function(e){

				$.ajax({
				type: "POST",
				url: "_action_register_user.php",
				data: $("form#formRegistration").serialize(),
				success: function(data){	
			//alert(data);//return false;
                            if(data){
                                $("button.reset-button").click();
                              $("div.status").addClass("alert alert-success").html(data);
                            }
						
				        }
				});		
		e.preventDefault();
});
    
    
$('#formCarRental').submit(function(e){

				$.ajax({
				type: "POST",
				url: "ajax_process_car_rental.php",
				data: $("#formCarRental").serialize(),
				success: function(data){	
			//alert(data);//return false;
                                
                                $("div#RentalAlert").addClass("alert-success mt-3").html("<div class='spinner'></div> Processing...");
                                $("div.spinner").addClass("spinner-border");
                                
                                setTimeout(function(){
                                     $("div#RentalAlert").html(data);;
                                },1000);
						
				        }
				});		
		e.preventDefault();
});
    
    
$('#formFindAngkas').submit(function(e){

				$.ajax({
				type: "POST"
				, url: "ajax_process_find_angkas.php"
				, data: $("#formFindAngkas").serialize()
				, success: function(data){	
                            if(data == '0'){
                                $("#infoAlert").removeClass("alert-warning").addClass("alert alert-success").html("Looking for a rider.").append("<div class='spinner-grow spinner-grow-sm'><span class='visually-hidden'>Thank you for your patience.</span></div>");
                            }else{
                                $("#infoAlert").removeClass("alert-success").addClass("alert alert-warning").html(data);
                            }
                          // $("#findMeARiderBTN").append("<span class='alert alert-info'>"+data+"</span>");    
                                
						}
				});		
		e.preventDefault();
});
    
    
        // Check if the #transactionHistoryTable element exists on the page
if ($('#transactionHistoryTable').length) {
    // Call loadTransactionHistory() to fetch and display transaction history
    loadTransactionHistory();
}
    

    
$('#topUpForm').on('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    // Gather form data
    const formData = {
        amount: $('#topUpAmount').val(),
    };

    // Send the data via AJAX
    $.ajax({
        url: 'ajax_top_up_wallet.php', // Target URL for top-up
        type: 'POST',
        data: formData,
        dataType: 'json', // Expecting JSON response
        success: function(response) {
            if (response.success) {
                $('#topUpModal').modal('hide'); // Close the modal
                loadTransactionHistory(); // Refresh the transaction history
            } else {
                alert(response.error || 'Top-up failed. Please try again.');
            }
        },
        error: function(xhr, status, error) {
            alert('An error occurred. Please try again later.');
        }
    });
});

    

    


});

