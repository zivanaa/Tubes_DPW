<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ?mod=login');
    exit();
}

include "db_connect.php";

$user_id = $_SESSION['user_id'];

// Mengambil data kontak
$contacts_query = "SELECT id, name, profile_image FROM users WHERE id != ?";
$stmt = $conn->prepare($contacts_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$contacts_result = $stmt->get_result();

$contacts = [];
while ($row = $contacts_result->fetch_assoc()) {
    $contacts[] = $row;
}

// Mengambil pesan antara pengguna yang sedang login dan kontak yang dipilih
$selected_contact_id = isset($_GET['contact_id']) ? (int)$_GET['contact_id'] : null;
$messages = [];
$selected_contact = null;
if ($selected_contact_id) {
    // Mengambil informasi kontak yang dipilih
    $contact_query = "SELECT name, profile_image FROM users WHERE id = ?";
    $stmt = $conn->prepare($contact_query);
    $stmt->bind_param("i", $selected_contact_id);
    $stmt->execute();
    $contact_result = $stmt->get_result();
    $selected_contact = $contact_result->fetch_assoc();

    // Mengambil pesan antara pengguna yang sedang login dan kontak yang dipilih
    $messages_query = "SELECT sender_id, content, timestamp FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
    $stmt = $conn->prepare($messages_query);
    $stmt->bind_param("iiii", $user_id, $selected_contact_id, $selected_contact_id, $user_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();
    
    while ($row = $messages_result->fetch_assoc()) {
        $row['timestamp'] = date("Y-m-d H:i:s", strtotime($row['timestamp']));
        $messages[] = $row;
    }
}

// Handle sending a message
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_id']) && isset($_POST['message'])) {
    $sender_id = $user_id;
    $receiver_id = $_POST['receiver_id'];
    $content = $_POST['message'];

    $query = "INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iis", $sender_id, $receiver_id, $content);
    if ($stmt->execute()) {
        header('Location: ?mod=chat&contact_id=' . $receiver_id);
        exit();
    } else {
        echo "Failed to send message.";
    }
}
?>
<?php include "header.php"; ?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<style>
/* CSS styles for chat section */
.container {
    display: flex;
}

.sidebar {
    width: 25%;
    border-right: 1px solid #ccc;
}

.contacts {
    display: flex;
    flex-direction: column;
}

.contact {
    display: flex;
    align-items: center;
    padding: 10px;
    cursor: pointer;
}

.contact img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
}

.chat-section {
    width: 75%;
    display: flex;
    flex-direction: column;
}

.chat-header {
    display: flex;
    align-items: center;
    padding: 10px;
    background-color: #11174F; /* Blue background for chat header */
    color: white; /* White text for chat header */
    border-bottom: 1px solid #ccc;
}

.chat-header img {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
}

.chat-messages {
    flex-grow: 1;
    padding: 10px;
    overflow-y: auto; /* Enable vertical scrolling */
    max-height: 60vh; /* Maximum height for chat messages */
}

.message {
    margin-bottom: 10px;
    display: flex;
    flex-direction: column;
}

.message.sent {
    align-items: flex-end;
}

.message.received {
    align-items: flex-start;
}

.message .content {
    max-width: 60%;
    padding: 10px;
    border-radius: 10px;
    position: relative;
}

.message.sent .content {
    background-color: #007bff; /* Blue background for sent messages */
    color: white; /* White text for sent messages */
    order: 1;
}

.message.sent .timestamp {
    order: 2;
    text-align: right; /* Align text to the right */
}

.message.received .content {
    background-color: #f1f0f0;
    order: 1;
}

.message.received .timestamp {
    order: 2;
    text-align: left; /* Align text to the left */
}

.message .timestamp {
    font-size: 0.8em;
    color: gray;
    margin-top: 5px;
}

.chat-input {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ccc;
}

.chat-input input[type="text"] {
    flex-grow: 1;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 20px;
    margin-right: 10px;
}

.chat-input button {
    padding: 10px 20px;
    border: none;
    background-color: #007bff;
    color: white;
    border-radius: 20px;
    cursor: pointer;
}

.chat-input button:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>
<div class="container">
    <div class="sidebar">
    <input type="text" id="contact-search" placeholder="Search contacts...">
    <div class="contacts">
        <?php foreach ($contacts as $contact): ?>
            <div class="contact" data-id="<?= $contact['id'] ?>">
                <img src="<?= !empty($contact['profile_image']) ? htmlspecialchars($contact['profile_image']) : 'assets/profile/none.png' ?>" alt="User Image" alt="Profile">
                <div class="details">
                    <div class="name"><?= $contact['name'] ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
</div>

    <div class="chat-section">
        <div class="chat-header">
            <img id="contact-profile-image" src="<?= !empty($selected_contact['profile_image']) ? htmlspecialchars($selected_contact['profile_image']) : 'assets/profile/none.png' ?>" alt="User Image" style="width: 50px; height: 50px; 'block' : 'none'; ?>;">
            <div id="contact-name"><?= isset($selected_contact['name']) ? $selected_contact['name'] : ''; ?></div>
        </div>

        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?= $message['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                    <div class="content"><?= htmlspecialchars($message['content']) ?></div>
                    <div class="timestamp"><?= htmlspecialchars($message['timestamp']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-input" style="display: <?= isset($selected_contact_id) ? 'flex' : 'none'; ?>;">
            <form id="message-form" action="" method="post">
                <input type="hidden" id="contact-id" name="receiver_id" value="<?php echo isset($selected_contact_id) ? $selected_contact_id : ''; ?>">
                <input type="text" id ="message-input" name="message" placeholder="Type your message and press enter...">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    function loadContactFromServer() {
        fetch('load_contact.php')
            .then(response => response.json())
            .then(data => {
                if (data.contact_name && data.contact_image && data.contact_id) {
                    // Update header chat with stored contact information
                    document.getElementById('contact-name').textContent = data.contact_name;
                    var profileImage = document.getElementById('contact-profile-image');
                    profileImage.src = data.contact_image;
                    profileImage.style.display = 'block';

                    // Update hidden input value with stored contact id
                    document.getElementById('contact-id').value = data.contact_id;

                    // Show form to send messages
                    document.querySelector('.chat-input').style.display = 'flex';
                }
            })
            .catch(error => console.error('Error loading contact:', error));
    }

    // Load contact information when the page is loaded
    loadContactFromServer();

    function scrollToBottom() {
        var chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Call function to scroll to the bottom when the page loads
    scrollToBottom();

    var initialContactId = document.getElementById('contact-id').value;
    if (!initialContactId) {
        document.querySelector('.chat-messages').style.display = 'none';
        document.querySelector('.chat-input').style.display = 'none';
    }

    function fetchMessages(contactId) {
        fetch('fetch_messages.php?contact_id=' + contactId)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    var chatMessages = document.getElementById('chat-messages');
                    var shouldScrollToBottom = chatMessages.scrollTop + chatMessages.clientHeight === chatMessages.scrollHeight;

                    chatMessages.innerHTML = ''; // Clear existing messages

                    data.messages.forEach(message => {
                        var messageElement = document.createElement('div');
                        messageElement.classList.add('message');
                        messageElement.classList.add(message.sender_id == <?= $user_id ?> ? 'sent' : 'received');

                        var contentElement = document.createElement('div');
                        contentElement.classList.add('content');
                        contentElement.textContent = message.content
                        contentElement.textContent = message.content;

                        var timestampElement = document.createElement('div');
                        timestampElement.classList.add('timestamp');
                        timestampElement.textContent = message.timestamp;

                        messageElement.appendChild(contentElement);
                        messageElement.appendChild(timestampElement);
                        chatMessages.appendChild(messageElement);
                    });

                    if (shouldScrollToBottom) {
                        chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom if already at bottom
                    }
                } else {
                    console.error('Error fetching messages:', data.message);
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    setInterval(() => {
        var contactId = document.getElementById('contact-id').value;
        if (contactId) fetchMessages(contactId);
    }, 3000); // Refresh messages every 3 seconds

    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    document.querySelectorAll('.contact').forEach(function(contact) {
        contact.addEventListener('click', function() {
            var contactId = this.getAttribute('data-id');
            var contactName = this.querySelector('.name').textContent;
            var contactImage = this.querySelector('img').src;

            document.getElementById('contact-name').textContent = contactName;
            var profileImage = document.getElementById('contact-profile-image');
            profileImage.src = contactImage;
            profileImage.style.display = 'block';
            document.getElementById('contact-id').value = contactId;
            document.querySelector('.chat-input').style.display = 'flex';

            fetchMessages(contactId);

            fetch('save_contact.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'contact_id': contactId,
                    'contact_name': contactName,
                    'contact_image': contactImage
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('Contact saved successfully');
                } else {
                    console.error('Failed to save contact');
                }
            })
            .catch(error => console.error('Error saving contact:', error));

            // Redirect to chat page with contact_id
            window.location.href = '?mod=chat&contact_id=' + contactId;

        });
    });

    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    function sendMessage() {
        var form = document.getElementById('message-form');
        var message = form.querySelector('#message-input').value.trim();

        if (message !== '') {
            form.submit();
        }
    }
});
document.addEventListener('DOMContentLoaded', function() {
    var contactSearch = document.getElementById('contact-search');
    var contacts = document.querySelectorAll('.contact');

    contactSearch.addEventListener('input', function() {
        var filter = contactSearch.value.toLowerCase();

        contacts.forEach(function(contact) {
            var name = contact.querySelector('.name').textContent.toLowerCase();
            var displayStyle = name.includes(filter) ? 'flex' : 'none';
            contact.style.display = displayStyle;
        });
    });
});
</script>
<?php include "footer.php"; ?>