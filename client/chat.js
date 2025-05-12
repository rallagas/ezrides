let chatIntervalId = null;

// Fetch and display messages
async function loadMessages(senderId, receiverId) {
    try {
        const messages = await fetchChatMessages(senderId, receiverId);
        const conversationDiv = $('#conversation');
        conversationDiv.empty();

        if (messages.length > 0) {
            messages.forEach(msg => {
                const isSender = msg.sender_id == senderId;

                const alignmentClass = isSender ? 'justify-content-end' : 'justify-content-start';
                const bubbleClass = isSender ? 'bg-primary text-white' : 'bg-secondary text-light';

                const messageHtml = `
                    <div class="d-flex ${alignmentClass} mb-1">
                        <div class="p-2 rounded-3 ${bubbleClass} shadow-sm" style="max-width: 75%;">
                            <div>${msg.message}</div>
                            <i class="text-end small mt-1 text-muted">${msg.date_received}</i>
                        </div>
                    </div>
                `;
                conversationDiv.append(messageHtml);
            });

            // Optional: scroll to bottom after loading messages
            conversationDiv.scrollTop(conversationDiv[0].scrollHeight);
        } else {
            conversationDiv.html('<small class="text-center text-body-tertiary">No messages yet for today. Start the conversation!</small>');
        }
    } catch (error) {
        console.log('No Message Recieved.');
    }
}


// Start chat polling
function startChatPolling(senderId, receiverId) {
    loadMessages(senderId, receiverId); // Initial load
    chatIntervalId = setInterval(() => loadMessages(senderId, receiverId), 5000); // Every 5 seconds
}

// Stop chat polling
function stopChatPolling() {
    if (chatIntervalId) {
        clearInterval(chatIntervalId);
        chatIntervalId = null;
    }
}

// When modal is shown
$('#chatModal').on('show.bs.modal', function (event) {
    const button = $(event.relatedTarget);
    const riderUsername = button.data('rider-username');
    const riderUserId = button.data('rider-userid');
    const senderId = $('#senderuserid').val();

    // Update UI
    $('#riderName').text(riderUsername);
    $('#riderNameModal').text(riderUsername);
    $('#rideruserid').val(riderUserId);

    startChatPolling(senderId, riderUserId);
});

// When modal is hidden
$('#chatModal').on('hide.bs.modal', function () {
    stopChatPolling();
});

// Form submit handler
$('#formChatRider').on('submit', async function (e) {
    e.preventDefault();
    const formData = $(this).serialize();
    try {
        const response = await $.ajax({
            url: '_ajax_send_message.php',
            type: 'POST',
            data: formData
        });

        const result = JSON.parse(response);
        if (result.status === 'success') {
            $('#messagecontent').val('');
            const senderId = $('input[name="sender_id"]').val();
            const receiverId = $('input[name="receiver_id"]').val();
            loadMessages(senderId, receiverId);
        } else {
            console.log('Message failed to send.');
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
});

// Fetch messages function remains unchanged
async function fetchChatMessages(senderId, receiverId) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: '_ajax_fetch_messages.php',
            method: 'POST',
            data: { sender_id: senderId, receiver_id: receiverId },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    resolve(response.messages);
                } else {
                    reject(response.error || 'Failed to fetch messages.');
                }
            },
            error: function (xhr, status, error) {
                reject(error || 'An error occurred while fetching messages.');
            }
        });
    });
}




function fetchUnreadCount() {
    $.ajax({
        url: '_ajax_unreadmsg.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const count = response.unread_count;
                const $counter = $('.msgCounter');
                if (count > 0) {
                    $counter.text(count).addClass('bg-danger').show();
                } else {
                    $counter.text('').removeClass('bg-danger').hide();
                }
            }
        },
        error: function(xhr, status, error) {
            console.error("Failed to fetch unread count:", error);
        }
    });
}

$(document).ready(function(){
// Initial fetch
fetchUnreadCount();

// Poll every 10 seconds
setInterval(fetchUnreadCount, 10000);    
});
