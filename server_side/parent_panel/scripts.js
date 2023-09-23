function logout() {
    window.location.href = "logout.php";
}

function loadHomePage() {
    // Fetch the real name from a endpoint
    fetch('fetch_irl_name.php')
    .then(response => response.json())
    .then(data => {
        var contentMessage = `
            <h2>Welcome back ${data.irl_name}!</h2>
            <h3>This website is on a Beta version</h3>
            <p>This is the home page.</p>
            <p>This Website is made so you ${data.irl_name} can go in and see how much your kids watch.</p>
            <p>And because it's fun to program I love Programing. This website is made by Constantin</p>
            <p>If there is problems or a bug with the website then please Report it. And if theres something That you want to add just tell me in real life or via the messages</p>
            <p>Enjoy the website</p>
            <div id="topLeftLogoutButton">
                <button onclick="logout()">Logout</button> 
            </div>     
        `;
        document.getElementById("content").innerHTML = contentMessage;
    })
    .catch(error => {
        console.error('Error fetching IRL name:', error);
    });
}

//Chat system

function initializeChat() {
    fetch('getUsername.php').then(response => response.json())
    .then(data => {
        if (data.username) {
            window.currentUsername = data.username;
            console.log("Logged in as:", window.currentUsername);
        } else {
            console.log("Error fetching username:", data.error);
            alert(data.error);
            return;
        }
    });

    const contentDiv = document.getElementById('content');
    contentDiv.innerHTML = '';  // Clear current content

    // Add title over the chat room
    const chatTitle = document.createElement("h2");
    chatTitle.innerText = "The global chat room";
    contentDiv.appendChild(chatTitle);

    // Chat Box
    const chatBox = document.createElement("div");
    chatBox.id = "chatBox";
    chatBox.style.height = "700px";
    chatBox.style.overflowY = "scroll";
    contentDiv.appendChild(chatBox);

    // Message Textarea
    const messageTextarea = document.createElement("textarea");
    messageTextarea.id = "message";
    messageTextarea.placeholder = "Enter your message";
    contentDiv.appendChild(messageTextarea);

    // Send Button
    const sendButton = document.createElement("button");
    sendButton.innerText = "Send";
    sendButton.addEventListener('click', sendMessage);
    contentDiv.appendChild(sendButton);

    // Load messages initially
    loadMessages();

    // Start the interval outside of the initialization
    setInterval(loadMessages, 500);
}

function loadMessages() {
    fetch('getMessages.php').then(response => response.json())
    .then(data => {
        console.log("Fetched messages:", data);
        let chatHTML = '';
        data.forEach(msg => {
            console.log("Processing message from:", msg.user);
            // Extract only the time from the timestamp
            const timeOnly = msg.timestamp.split(" ")[1];

            // Check if the message user is the current user
            if (msg.user === window.currentUsername) {
                chatHTML += `<p class="myMessage"> ${msg.message} <strong> ${timeOnly} ${msg.user}</strong></p>`;
            } else {
                chatHTML += `<p><strong>${msg.user}</strong> ${timeOnly}: ${msg.message}</p>`;
            }
        });
        document.getElementById("chatBox").innerHTML = chatHTML;
    });
}

function sendMessage() {
    let message = document.getElementById("message").value;
    console.log("Sending message:", message);
    fetch('sendMessage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `username=${currentUsername}&message=${message}`
    }).then(response => response.json())
    .then(data => {
        if(data.success) {
            loadMessages();
            document.getElementById("message").value = '';
        } else {
            alert("Error sending message.");
        }
    });
}

//feedback

function Feedback() {
    let contentDiv = document.getElementById("content");

    // Clear previous content
    contentDiv.innerHTML = "";

    // Create and add the feedback form
    let formHTML = `
        <h2>Send Your Feedback to the developers</h2>
        <form id="feedbackForm">
            <div class="form-group">
                <label for="feedback">Feedback:</label>
                <textarea id="feedback" name="feedback" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Submit</button>
            </div>
        </form>
    `;

    contentDiv.innerHTML = formHTML;

    // Add an event listener to handle form submission
    document.getElementById("feedbackForm").addEventListener("submit", async function(event) {
        event.preventDefault(); // prevent the form from doing a regular submit
        submitFeedback();
    });
}

async function submitFeedback() {
    // Fetch the irl_name from the session
    let response = await fetch('fetch_irl_name.php');
    let data = await response.json();
    
    if (data.error) {
        alert('Error: ' + data.error);
        return;
    }

    let irl_name = data.irl_name;
    let feedbackMessage = document.getElementById('feedback').value; // Fixed the ID from 'feedbackMessage' to 'feedback'

    // Now, you can use the irl_name directly to submit feedback.
    let feedbackData = {
        'name': irl_name,
        'feedback': feedbackMessage
    };

    // Assuming you have a PHP script to handle feedback submission
    let submitResponse = await fetch('submit_feedback.php', {
        method: 'POST',
        body: JSON.stringify(feedbackData),
        headers: {
            'Content-Type': 'application/json'
        }
    });
    
    let submitData = await submitResponse.json();
    if (submitData.success) {
        alert('Feedback submitted successfully!');
    } else {
        alert('Error: ' + submitData.error);
    }
}

//View time a person has played
let currentPlaytimes = {};

function filterData() {
    const searchBarElement = document.getElementById('searchBar');
    const calendarInputElement = document.getElementById('calendarInput');

    if (!searchBarElement || !calendarInputElement) {
        console.error("Required elements not found in the DOM");
        return;
    }

    const query = searchBarElement.value.toLowerCase();
    const chosenDate = calendarInputElement.value;

    const filteredPlaytimes = {};
    for (const person in currentPlaytimes) {
        if (person.toLowerCase().includes(query)) {  // Use 'query' here instead of 'searchValue'
            filteredPlaytimes[person] = currentPlaytimes[person];
        }
    }

    populateTable(filteredPlaytimes);
}

function fetchPlaytimes() {
    let date = document.getElementById("date").value;

    // If no date is selected, default to today's date
    if (!date) {
        const today = new Date();
        date = today.toISOString().split('T')[0];
        document.getElementById("date").value = date;
    }

    document.getElementById("header").innerText = `Total Playtime for ${date}`;

    fetch(`getPlaytimes.php?date=${date}`)
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            console.error("Error from server:", data.error);
            return;
        }

        console.log("Received playtimes:", data);
        currentPlaytimes = data;
        filterPlaytimes();  // Apply the current filter on the newly fetched data
    })
    .catch(error => {
        console.error('Error fetching playtimes:', error);
    });
}

function initializePlaytimePage() {
    // Get the content div
    const contentDiv = document.getElementById("content");

    // Create the header, search bar, and table structure
    let html = `
        <h1 id="header">Total Playtime</h1>
        <form>
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date">
            <input type="button" value="Search" onclick="fetchPlaytimes()">
        </form>
        <div>
            <label for="searchPerson">Search by Person:</label>
            <input type="text" id="searchPerson" placeholder="Type to filter..." oninput="filterPlaytimes()">
        </div>
        <table>
            <thead>
                <tr>
                    <th>Person</th>
                    <th>Minutes Played</th>
                    <th>Hours Played</th>
                </tr>
            </thead>
            <tbody id="playtimeTable"></tbody>
        </table>
    `;

    contentDiv.innerHTML = html;
    fetchPlaytimes();  // Automatically fetch playtimes for the selected date
    setInterval(fetchPlaytimes, 2000);
}

function populateTable(playtimes) {
    const tbody = document.getElementById("playtimeTable");
    tbody.innerHTML = "";

    Object.entries(playtimes).forEach(([person, minutes]) => {
        const hours = Math.floor(minutes / 60); 
        const remainingMinutes = (minutes % 60).toFixed(0); 

        const row = `
            <tr>
                <td>${person}</td>
                <td>${remainingMinutes} minutes</td>
                <td>${hours} hours</td>
            </tr>
        `;

        tbody.insertAdjacentHTML('beforeend', row);
    });
}

//show timestamps

function createSearchBar() {
    // Create the search bar input element
    const searchBar = document.createElement('input');
    searchBar.type = 'text';
    searchBar.id = 'searchBar';
    searchBar.placeholder = "Search by person's name or timestamp...";
    
    searchBar.addEventListener('input', function() {
        filterData();
    });

    // Create a date input for the calendar and set it to today's date by default
    const calendarInput = document.createElement('input');
    calendarInput.type = 'date';
    calendarInput.id = 'calendarInput';
    const today = new Date().toISOString().split('T')[0];
    calendarInput.value = today;  // Set today's date

    calendarInput.addEventListener('change', function() {
        filterData();
    });

    // Get the content div
    const contentDiv = document.getElementById('content');
    
    // Append the search bar and calendar to content div
    contentDiv.appendChild(searchBar);
    contentDiv.appendChild(calendarInput);
}

function filterData() {
    const query = document.getElementById('searchBar').value.toLowerCase();
    const chosenDate = document.getElementById('calendarInput').value;  // This will already be set to today's date by default
    const filteredData = allData.filter(item => {
        const matchesQuery = item.person.toLowerCase().startsWith(query) || item.timestamp.toLowerCase().startsWith(query);
        const itemDate = item.timestamp.split(' ')[0];
        const matchesDate = itemDate === chosenDate;  // Only match the exact date
        return matchesQuery && matchesDate;
    });

    updateTableData(filteredData);
}

function updateTableData(data) {
    let html = '<table>';
    html += '<thead><tr><th>ID</th><th>Timestamp</th><th>Status</th><th>Person</th><th>Game</th><th>IP Address</th></tr></thead><tbody>';
    data.forEach(item => {
        html += '<tr>';
        html += `<td>${item.id}</td>`;
        html += `<td>${item.timestamp}</td>`;
        html += `<td>${item.status}</td>`;
        html += `<td>${item.person}</td>`;
        html += `<td>${item.game}</td>`;
        html += `<td>${item.ip_address}</td>`;
        html += '</tr>';
    });
    html += '</tbody></table>';

    const tableContainer = document.getElementById('tableContainer');
    if (tableContainer) {
        tableContainer.innerHTML = html;
    } else {
        const newTableContainer = document.createElement('div');
        newTableContainer.id = 'tableContainer';
        newTableContainer.innerHTML = html;
        document.getElementById('content').appendChild(newTableContainer);
    }
}

function fetchData() {
    console.log("Fetching data from server...");
    fetch('get_playtime.php')
        .then(response => response.json())
        .then(data => {
            allData = data;
            console.log("Fetched data from server.");
            filterData();  // Call filter after data fetch
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
}

function initializeSearchBarAndData() {
    // Clear the content div
    const contentDiv = document.getElementById('content');
    contentDiv.innerHTML = ''; 

    // Create search bar
    createSearchBar();

    // Start fetching data
    fetchData();

    // Fetch and load the data every 500 milliseconds
    setInterval(fetchData, 500);
}
