let chatInterval = null;

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

async function loadMessages(senderId, receiverId) {
    try {
        const messages = await fetchChatMessages(senderId, receiverId);
        const conversationDiv = $('#conversation');
        conversationDiv.empty();

        if (messages.length > 0) {
            messages.forEach(msg => {
                const isSender = msg.sender_id === senderId;

                const alignmentClass = isSender ? 'justify-content-end' : 'justify-content-start';
                const bubbleClass = isSender ? 'bg-primary text-white' : 'bg-light text-dark';

                const messageHtml = `
                    <div class="d-flex ${alignmentClass} mb-1">
                        <div class="p-2 rounded-3 ${bubbleClass}" style="max-width: 75%;">
                            ${msg.message}
                            <div class="text-end small mt-1 text-muted">${msg.date_received}</div>
                        </div>
                    </div>`;
                conversationDiv.append(messageHtml);
            });

            // Auto-scroll to bottom
            conversationDiv.scrollTop(conversationDiv[0].scrollHeight);
        } else {
            conversationDiv.html('<small class="text-center text-body-tertiary">No messages yet for today. Start the conversation!</small>');
        }
    } catch (error) {
        console.error('Error fetching messages:', error);
    }
}

function startChatPolling(senderId, receiverId) {
    // Clear previous interval if any
    if (chatInterval) clearInterval(chatInterval);

    // Load immediately
    loadMessages(senderId, receiverId);

    // Then poll every 3 seconds
    chatInterval = setInterval(() => {
        loadMessages(senderId, receiverId);
    }, 3000);
}

// Handle opening the modal
$(document).on('click', '.open-chat-modal', function () {
    const senderId = $(".rider-id").data('rider-userid');
    const receiverId = $(".customer-id").data('customer-userid');

    startChatPolling(senderId, receiverId);
});

// Handle form submission
$(document).on('submit', '#formChatCustomer', async function (e) {
    e.preventDefault();

    const formData = $(this).serialize();
    const senderId = $(".rider-id").data('rider-userid');
    const receiverId = $(".customer-id").data('customer-userid');

    try {
        const response = await $.ajax({
            url: '_ajax_send_message.php',
            type: 'POST',
            data: formData
        });

        const result = JSON.parse(response);

        if (result.status === 'success') {
            $('#formChatCustomer #messagecontent').val('');
            await loadMessages(senderId, receiverId); // Refresh immediately after sending
        } else {
            console.log('Failed to send the message. Please try again.');
        }
    } catch (error) {
        console.error('An error occurred while sending the message.', error);
    }
});
