<?php 
include_once "../../_db.php";
include_once "../../_sql_utility.php";
include_once "../_class_grocery.php";
include_once "func.php";

if (isset($_GET['approveCashout'])) {
    // Decode the wallet ID from Base64
    $walletid_encoded = $_GET['walletid'];
    $walletid = base64_decode($walletid_encoded);


    // Validate and sanitize decoded wallet ID
    if ($walletid && ctype_digit($walletid)) { // Ensure it's a numeric value
        $table = 'user_wallet';
        $set = ['wallet_txn_status' => 'C'];
        $where = ['user_wallet_id' => $walletid];

        // Update data in the database
        update_data($table, $set, $where);
        
        header("location: ?CashoutApproved");
        
    } else {
        // Handle invalid wallet ID (log or show error)
        echo "Invalid wallet ID.";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .bg-purple {
            background-color: mediumpurple;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <?php include_once "navbar.php";?>

            <div class="col-lg-12 col-sm-12" id="controlDashboard">
                <!--               load page functions here -->
                <?php
                            loadPage();
                        ?>

            </div>
        </div>


    </div>



</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="_loader.js"></script>
<script>
    $(document).ready(function() {
        // Trigger file selection
        $('.attach-file').on('click', function() {
            $(this).siblings('.form-file').click();
        });

        // Submit the form data
        $('form#newvehicle').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Collect form data
            let formData = new FormData(this);

            $.ajax({
                url: '_process_vehicle.php', // Server-side script
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.success) {
                        alert('Vehicle registered successfully.');
                        $('form')[0].reset();
                    } else {
                        alert('Error: ' + res.message);
                    }
                },
                error: function() {
                    alert('An error occurred while processing your request.');
                }
            });
        });
        $.post('_get_user_counts.php', function(data) {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const ctx = document.getElementById('userPieChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Customers', 'Riders'],
                    datasets: [{
                        data: [data.customers, data.riders],
                        backgroundColor: ['#0d6efd', '#ffc107'],
                        borderColor: '#fff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }, 'json');

        $.post('_get_rider_activity.php', function(data) {
            if (data.error) {
                alert(data.error);
                return;
            }

            const ctx = document.getElementById('newRiderTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'New Riders',
                        data: data.counts,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.3)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of New Riders'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date Joined'
                            }
                        }
                    }
                }
            });
        }, 'json');
        $.post('_get_new_customer_trend.php', function(data) {
            if (data.error) {
                alert(data.error);
                return;
            }

            const ctx = document.getElementById('newCustomerTrendChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'New Customers',
                        data: data.counts,
                        borderColor: '#17a2b8',
                        backgroundColor: 'rgba(23, 162, 184, 0.3)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Number of New Customers'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Date Joined'
                            }
                        }
                    }
                }
            });
        }, 'json');


        //---------------------------------
        //search

        let typingTimer;
        const delay = 2000; // 2 seconds

        function performSearch() {
            let keyword = $('#searchInput').val().trim();
            $('#loadingIndicator').show(); // Show "Searching..." message

            $.post('_search_user.php', {
                keyword: keyword
            }, function(data) {
                $('#loadingIndicator').hide(); // Hide when done

                $('#searchUser').html('<span class="small text-muted">Search Result</span><br>').append(data).append("<hr>");
            });
        }

        $('#searchInput').on('input', function() {
            clearTimeout(typingTimer);
            $('#loadingIndicator').show(); // Show immediately while typing
            typingTimer = setTimeout(performSearch, delay);
        });

        $('#searchInput').on('keydown', function() {
            clearTimeout(typingTimer);
        });


    });
       $(document).on('click', '#downloadPDF', function () {
    const element = document.getElementById('userActivity');

    // Expand all Bootstrap cols to full width
    $('#userActivity .row [class*="col-lg-"]').each(function () {
        $(this).removeClass(function (index, className) {
            return (className.match(/col-lg-\S+/g) || []).join(' ');
        }).addClass('col-12');
    });

    // Resize canvases to fit parent width
    $('#userActivity canvas').each(function () {
        const $canvas = $(this);
        const $parent = $canvas.parent();
        $canvas.css({
            width: 'auto',
            height: 'auto'
        });
    });

    // Expand all scrollable elements (overflow-y) to full height for capture
    $('#userActivity .overflow_y-scroll, #userActivity .overflow-y-scroll, #userActivity tbody').each(function () {
        $(this).css({
            'overflow': 'visible',
            'max-height': 'none'
        });
    });

    // Temporarily expand the container height
    $('#userActivity').css('height', 'auto');

    // Generate PDF
    html2pdf().set({
        margin: 0.5,
        filename: 'user-activity-summary.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
            scale: 2,
            useCORS: true,
            logging: true
        },
        jsPDF: {
            unit: 'in',
            format: 'letter',
            orientation: 'portrait'
        }
    }).from(element).save();
});



    $(document).on('click', '.user-log-trigger', function(e) {
        e.preventDefault(); // Prevent the default anchor behavior

        const userId = $(this).attr('id');
        $('#userActivity').html('<div class="text-center text-muted p-3"><div class="spinner-border spinner-border-sm"></div> Loading activity...</div>');

        $.post('_fetch_user_activity.php', {
            user_id: userId
        }, function(data) {
            $('#userActivity').html(data);
        });

    });
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</html>