<?php include"header.php";?>
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
            display: flex;
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
            <div class="search-bar">
                <input type="text" placeholder="Search">
            </div>
            <div class="contacts">
                <div class="contact">
                    <img src="https://via.placeholder.com/40" alt="Profile">
                    <div class="details">
                        <div class="name">Chole Adams</div>
                        <div class="message">Hey! Did you just...</div>
                    </div>
                </div>
                <!-- Add more contacts as needed -->
            </div>
        </div>
        <div class="chat-section">
            <div class="chat-header">
                <img src="https://via.placeholder.com/40" alt="Profile">
                <div>
                    <div>Chole Adams</div>
                </div>
            </div>
            <div class="chat-messages">
                <div class="message received">
                    <div class="content">
                        Dude this thing we are trying will be the best failure in design
                    </div>
                </div>
                <div class="message sent">
                    <div class="content">
                        But if we leave like we don’t care we can’t design stuffs girl don’t care about criticism
                        <br><br>
                        I already published it 😜
                    </div>
                </div>
            </div>
            <div class="chat-input">
                <input type="text" placeholder="Type your message and press enter...">
                <button>Send</button>
            </div>
        </div>
    </div>

    <script>
    // Fungsi untuk mengirim pesan
    function sendMessage() {
        var input = document.querySelector('.chat-input input');
        var message = input.value.trim();
        
        if (message !== '') {
            var chatMessages = document.querySelector('.chat-messages');
            var newMessage = document.createElement('div');
            newMessage.classList.add('message', 'sent');
            newMessage.innerHTML = '<div class="content">' + message + '</div>';
            chatMessages.appendChild(newMessage);
            input.value = ''; // Bersihkan input setelah pesan terkirim

            // Scroll ke bawah untuk menampilkan pesan terbaru
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Fungsi untuk menangani pengiriman pesan ketika tombol "Send" ditekan
    document.querySelector('.chat-input button').addEventListener('click', function() {
        sendMessage();
    });

    // Fungsi untuk menangani pengiriman pesan ketika tombol "Enter" ditekan
    document.querySelector('.chat-input input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Data kontak
    var contacts = [
        {
            id: 1,
            name: 'Chole Adams',
            messages: [
                { sender: 'Chole Adams', content: 'Hey! Did you just...' }
                // Tambahkan pesan lainnya jika ada
            ]
        },
        // Tambahkan kontak lainnya jika ada
    ];

    // Fungsi untuk menampilkan pesan berdasarkan kontak yang dipilih
    function displayMessages(contactId) {
        var chatMessages = document.querySelector('.chat-messages');
        chatMessages.innerHTML = ''; // Bersihkan pesan sebelum menampilkan pesan baru

        var contact = contacts.find(function(contact) {
            return contact.id === contactId;
        });

        if (contact) {
            contact.messages.forEach(function(message) {
                var newMessage = document.createElement('div');
                newMessage.classList.add('message');
                newMessage.innerHTML = '<div class="content">' + message.content + '</div>';
                chatMessages.appendChild(newMessage);
            });
        }
    }

    // Fungsi untuk menampilkan kontak
    function displayContacts() {
        var contactsList = document.querySelector('.contacts');
        contactsList.innerHTML = ''; // Bersihkan daftar kontak sebelum menampilkan kontak baru

        contacts.forEach(function(contact) {
            var newContact = document.createElement('div');
            newContact.classList.add('contact');
            newContact.innerHTML = '<img src="https://via.placeholder.com/40" alt="Profile">' +
                                   '<div class="details">' +
                                   '<div class="name">' + contact.name + '</div>' +
                                   '</div>';
            newContact.addEventListener('click', function() {
                displayMessages(contact.id);
            });
            contactsList.appendChild(newContact);
        });
    }

    // Fungsi untuk menerima pesan dari lawan bicara
    function receiveMessage(message, contactId) {
        var contact = contacts.find(function(contact) {
            return contact.id === contactId;
        });

        if (contact) {
            var chatMessages = document.querySelector('.chat-messages');
            var newMessage = document.createElement('div');
            newMessage.classList.add('message', 'received');
            newMessage.innerHTML = '<div class="content">' + message + '</div>';
            chatMessages.appendChild(newMessage);

            // Scroll ke bawah untuk menampilkan pesan terbaru
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }
    }

    // Fungsi untuk mengirim pesan dari lawan bicara
    function sendMessageFromOpponent(message, contactId) {
        var contact = contacts.find(function(contact) {
            return contact.id === contactId;
        });

        if (contact) {
            contact.messages.push({ sender: contact.name, content: message });
            displayMessages(contactId);
        }
    }
    // Fungsi untuk menampilkan pesan berdasarkan kontak yang dipilih
    function displayMessages(contactId) {
        var chatMessages = document.querySelector('.chat-messages');
        chatMessages.innerHTML = ''; // Bersihkan pesan sebelum menampilkan pesan baru

        var contact = contacts.find(function(contact) {
            return contact.id === contactId;
        });

        if (contact) {
            contact.messages.forEach(function(message) {
                var newMessage = document.createElement('div');
                newMessage.classList.add('message');
                newMessage.innerHTML = '<div class="content">' + message.content + '</div>';
                chatMessages.appendChild(newMessage);
            });
        } else {
            // Jika kontak tidak ditemukan, tetapkan pesan "Pilih kontak untuk memulai percakapan"
            var emptyMessage = document.createElement('div');
            emptyMessage.classList.add('message');
            emptyMessage.innerHTML = '<div class="content">Pilih kontak untuk memulai percakapan</div>';
            chatMessages.appendChild(emptyMessage);
        }
    }

    // Fungsi untuk menampilkan kontak
    function displayContacts() {
        var contactsList = document.querySelector('.contacts');
        contactsList.innerHTML = ''; // Bersihkan daftar kontak sebelum menampilkan kontak baru

        contacts.forEach(function(contact) {
            var newContact = document.createElement('div');
            newContact.classList.add('contact');
            newContact.innerHTML = '<img src="https://via.placeholder.com/40" alt="Profile">' +
                                   '<div class="details">' +
                                   '<div class="name">' + contact.name + '</div>' +
                                   '</div>';
            newContact.addEventListener('click', function() {
                displayMessages(contact.id);
            });
            contactsList.appendChild(newContact);
        });
    }

    // Fungsi untuk mendapatkan data kontak dari penyimpanan lokal
    function getContactsFromLocalStorage() {
        var contactsData = localStorage.getItem('contacts');
        return contactsData ? JSON.parse(contactsData) : [];
    }

    // Fungsi untuk menyimpan data kontak ke penyimpanan lokal
    function saveContactsToLocalStorage(contacts) {
        localStorage.setItem('contacts', JSON.stringify(contacts));
    }

    // Fungsi untuk menampilkan pesan berdasarkan kontak yang dipilih
    function displayMessages(contactId) {
        var chatMessages = document.querySelector('.chat-messages');
        chatMessages.innerHTML = ''; // Bersihkan pesan sebelum menampilkan pesan baru

        var contacts = getContactsFromLocalStorage();

        var contact = contacts.find(function(contact) {
            return contact.id === contactId;
        });

        if (contact) {
            contact.messages.forEach(function(message) {
                var newMessage = document.createElement('div');
                newMessage.classList.add('message');
                newMessage.innerHTML = '<div class="content">' + message.content + '</div>';
                chatMessages.appendChild(newMessage);
            });
        } else {
            // Jika kontak tidak ditemukan, tetapkan pesan "Pilih kontak untuk memulai percakapan"
            var emptyMessage = document.createElement('div');
            emptyMessage.classList.add('message');
            emptyMessage.innerHTML = '<div class="content">Pilih kontak untuk memulai percakapan</div>';
            chatMessages.appendChild(emptyMessage);
        }
    }

    // Fungsi untuk menampilkan kontak
    function displayContacts() {
        var contactsList = document.querySelector('.contacts');
        contactsList.innerHTML = ''; // Bersihkan daftar kontak sebelum menampilkan kontak baru

        var contacts = getContactsFromLocalStorage();

        contacts.forEach(function(contact) {
            var newContact = document.createElement('div');
            newContact.classList.add('contact');
            newContact.innerHTML = '<img src="https://via.placeholder.com/40" alt="Profile">' +
                                   '<div class="details">' +
                                   '<div class="name">' + contact.name + '</div>' +
                                   '</div>';
            newContact.addEventListener('click', function() {
                displayMessages(contact.id);
            });
            contactsList.appendChild(newContact);
        });
    }

    // Memanggil fungsi untuk menampilkan kontak pertama kali
    displayContacts();
</script>

<?php include"footer.php";?>
