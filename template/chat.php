<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ?mod=login');
    exit();
}

include "db_connect.php"; // File untuk koneksi ke database

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
?>

<?php include "header.php"; ?>
<style>
body {
    font-family: 'Roboto', sans-serif;
    margin: 0;
    display: flex;
    flex-direction: column;
    height: 100vh;
    background-color: #f0f2f5;
}
.container {
    display: flex;
    flex-grow: 1;
    height: calc(100vh - 100px); 
}
.sidebar {
    width: 30%;
    background-color: white;
    border-right: 1px solid #ddd;
    display: flex;
    flex-direction: column;
}
.search-bar {
    padding: 1rem;
    border-bottom: 1px solid #ddd;
}
.search-bar input {
    width: 100%;
    padding: 0.5rem;
    border-radius: 20px;
    border: 1px solid #ddd;
}
.contacts {
    flex-grow: 1;
    overflow-y: auto;
}
.contact {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #ddd;
    cursor: pointer;
}
.contact:hover {
    background-color: #f9f9f9;
}
.contact img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 1rem;
}
.contact .details {
    display: flex;
    flex-direction: column;
}
.contact .details .name {
    font-weight: 500;
}
.contact .details .message {
    color: #888;
}
.chat-section {
    width: 70%;
    display: flex;
    flex-direction: column;
    background-color: white;
}
.chat-header {
    display: flex;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid #ddd;
}
.chat-header img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 1rem;
}
.chat-messages {
    flex-grow: 1;
    padding: 1rem;
    overflow-y: auto;
}
.message {
    margin-bottom: 1rem;
    display: flex;
}
.message.sent {
    justify-content: flex-end;
}
.message .content {
    max-width: 60%;
    padding: 0.75rem 1rem;
    border-radius: 20px;
    position: relative;
}
.message.received .content {
    background-color: #f0f0f0;
}
.message.sent .content {
    background-color: #0066cc;
    color: white;
}
.message .content::before {
    content: "";
    position: absolute;
    width: 0;
    height: 0;
}
.message.received .content::before {
    left: -10px;
    top: 10px;
    border: 10px solid transparent;
    border-right: 10px solid #f0f0f0;
}
.message.sent .content::before {
    right: -10px;
    top: 10px;
    border: 10px solid transparent;
    border-left: 10px solid #0066cc;
}
.chat-input {
    display: none; /* Hide by default, show when a contact is selected */
    padding: 1rem;
    border-top: 1px solid #ddd;
}
.chat-input input {
    flex-grow: 1;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 20px;
    margin-right: 1rem;
}
.chat-input button {
    background-color: #0066cc;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 20px;
    color: white;
    cursor: pointer;
}
</style>
</head>
<body>
    
<div class="container">
    <div class="sidebar">
        <!-- Kontak-kontak yang ditampilkan di sini -->
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
        <!-- Header Chat -->
        <div class="chat-header">
            <img id="contact-profile-image" style="display:none;" alt="Profile">
            <div id="contact-name"></div>
        </div>

        
        <!-- Menampilkan pesan-pesan dari database -->
        <div class="chat-messages">
            <?php foreach ($messages as $message): ?>
                <div class="message <?= $message['sender_id'] == $user_id ? 'sent' : 'received' ?>">
                    <div class="content"><?= htmlspecialchars($message['content']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Form untuk mengirim pesan -->
        <div class="chat-input">
            <form id="message-form" action="page.php?mod=send_message" method="post">
                <input type="hidden" id="contact-id" name="receiver_id" value="<?php echo isset($selected_contact_id) ? $selected_contact_id : ''; ?>">
                <input type="text" id="message-input" name="message" placeholder="Type your message and press enter...">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to load contact information from the server
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

    // Event listener for each contact
    document.querySelectorAll('.contact').forEach(function(contact) {
        contact.addEventListener('click', function() {
            var contactId = this.getAttribute('data-id');
            var contactName = this.querySelector('.name').textContent;
            var contactImage = this.querySelector('img').src;

            // Update header chat with selected contact information
            document.getElementById('contact-name').textContent = contactName;
            var profileImage = document.getElementById('contact-profile-image');
            profileImage.src = contactImage;
            profileImage.style.display = 'block';

            // Update hidden input value with selected contact id
            document.getElementById('contact-id').value = contactId;

            // Show form to send messages
            document.querySelector('.chat-input').style.display = 'flex';

            // Store contact information on the server
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

    // Event listener for message sending form
    document.getElementById('message-form').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    // Function to send a message
    function sendMessage() {
        var form = document.getElementById('message-form');
        var message = form.querySelector('#message-input').value.trim();

        if (message !== '') {
            // Submit form
            form.submit();
        }
    }
});



</script>

<?php include "footer.php"; ?>
