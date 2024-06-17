
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
if ($selected_contact_id) {
    $messages_query = "SELECT sender_id, content, timestamp FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) ORDER BY timestamp ASC";
    $stmt = $conn->prepare($messages_query);
    $stmt->bind_param("iiii", $user_id, $selected_contact_id, $selected_contact_id, $user_id);
    $stmt->execute();
    $messages_result = $stmt->get_result();

    while ($row = $messages_result->fetch_assoc()) {
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
    background-color: #f1f1f1;
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
}

.message.sent {
    justify-content: flex-end;
}

.message .content {
    max-width: 60%;
    padding: 10px;
    border-radius: 10px;
}

.message.sent .content {
    background-color: #007bff; /* Blue background for sent messages */
    color: white; /* White text for sent messages */
}

.message.received .content {
    background-color: #f1f0f0;
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

</style>
</head>
<body>
    
<div class="container">
    <div class="sidebar">
        <div class="contacts">
            <?php foreach ($contacts as $contact): ?>
                <div class="contact" data-id="<?= $contact['id'] ?>">
                    <img src="<?= $contact['profile_image'] ?>" alt="Profile">
                    <div class="details">
                        <div class="name"><?= $contact['name'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="chat-section">
        <div class="chat-header">
            <img id="contact-profile-image" alt="Profile">
             <div id="contact-name"></div>
         </div>

        
        <div class="chat-messages" id="chat-messages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?= $message['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                    <div class="content"><?= htmlspecialchars($message['content']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="chat-input">
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
    function fetchMessages() {
        var contactId = document.getElementById('contact-id').value;
        fetch('fetch_messages.php?contact_id=' + contactId)
            .then(response => response.json())
            .then(data => {
                var chatMessages = document.getElementById('chat-messages');
                var shouldScrollToBottom = chatMessages.scrollTop + chatMessages.clientHeight === chatMessages.scrollHeight;

                chatMessages.innerHTML = ''; // Clear existing messages

                data.messages.forEach(message => {
                    var messageElement = document.createElement('div');
                    messageElement.classList.add('message');
                    messageElement.classList.add(message.sender_id == <?= $user_id ?> ? 'sent' : 'received');

                    var contentElement = document.createElement('div');
                    contentElement.classList.add('content');
                    contentElement.textContent = message.content;

                    messageElement.appendChild(contentElement);
                    chatMessages.appendChild(messageElement);
                });

                if (shouldScrollToBottom) {
                    chatMessages.scrollTop = chatMessages.scrollHeight; // Scroll to bottom if already at bottom
                }
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    setInterval(fetchMessages, 3000); // Refresh messages every 3 seconds

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

            window.location.href = '?mod=chat&contact_id=' + contactId;
        });
    });

    function sendMessage() {
        var form = document.getElementById('message-form');
        var message = form.querySelector('#message-input').value.trim();

        if (message !== '') {
            form.submit();
        }
    }
});
</script>


<?php include "footer.php"; ?>

