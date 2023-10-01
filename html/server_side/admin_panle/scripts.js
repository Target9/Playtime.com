function logout() {
    window.location.href = "logout.php";
}

function loadHomePage() {
    const contentDiv = document.getElementById('content');
    contentDiv.innerHTML = '';  // Clear current content

    // Check if 'contentContainer' exists, if yes, clear it
    const contentContainerDiv = document.getElementById('contentContainer');
    if (contentContainerDiv) {
        contentContainerDiv.innerHTML = '';
    }

    // Fetch the real name from a new endpoint
    fetch('fetch_irl_name.php')
    .then(response => response.json())
    .then(data => {
        var contentMessage = `
            <h2>Welcome back ${data.irl_name}!</h2>
            <p>This is the home page, for Admins</p>
            <p>Here Admins can control the server</p>
            <p>And theres Admin tools. Have fun</p>
            <div>
                <button onclick="redirectToParentPanel()">Parent Panel</button>
                <button onclick="redirectToKidPanel()">Kid Panel</button>
            </div>
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

function redirectToParentPanel() {
    // Redirect to the Parent Panel page
    window.location.href = '../Parent_panel/parent_panel.php';
}

function redirectToKidPanel() {
    // Redirect to the Kid Panel page
    window.location.href = '../Kid_panel/kid_panel.php';
}

//Control panel

let personToIpMap = {};
let currentSelectedPerson = null;

function controlpanel() {
    fetch('fetch_accounts.php')
    .then(response => response.json())
    .then(data => {
        if (!data.success || !Array.isArray(data.data)) {
            throw new Error("Received data is not valid or in the expected format");
        }

        personToIpMap = data.data.reduce((acc, curr) => {
            acc[curr.person] = curr.ip_address;
            return acc;
        }, {});

        let htmlContent = '<h2>Select a Person that you want to View or Manage</h2>';
        htmlContent += '<select id="personDropdown">';
        htmlContent += '<option value="">Select a person</option>';
        data.data.forEach(entry => {
            htmlContent += `<option value="${entry.person}">${entry.person}</option>`;
        });
        htmlContent += '</select>';

        // Add the confirmation button
        htmlContent += '<button id="confirmButton" onclick="handleConfirmation()">Confirm</button>';

        document.getElementById('content').innerHTML = htmlContent;
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        document.getElementById('content').innerHTML = `<p style="color: red;">Error fetching data. Please try again later.</p>`;
    });
}

function handleConfirmation() {
    const selectedPerson = document.getElementById('personDropdown').value;
    if (selectedPerson) {
        currentSelectedPerson = selectedPerson;  // update the global variable here
        loadPersonData(selectedPerson);
    } else {
        alert('Please select a person from the dropdown before confirming.');
    }
}

function loadPersonData(personName) {
    // Start with your title
    let htmlContent = `<h2>You are managing ${personName} right now</h2>`;

    // Wrapper for columns to ensure they sit next to each other
    htmlContent += `<div style="display: flex;">`;

    // Add Actions/Tools section
    htmlContent += `
        <div style="flex: 1; flex-direction: column; margin-right: 50px;">
            <h3>Actions/Tools:</h3>
            <button id="killGameButton" onclick="killGame('${personName}')">Kill Game</button>
            <button id="checkRobloxButton" onclick="checkRobloxStatus('${personName}')">Is Roblox Running?</button>
            <button id="DataAnalyst" onclick="DataAnalyst('${personName}')">Data Analyst</button>
        </div>`;

    // Add Admin Tools section
    htmlContent += `
    <div id="AdminToolsContainer" style="flex: 1; flex-direction: column; margin-right: 50px;">
        <h3>Admin Tools:</h3>
        <div style="display: flex;"> <!-- Flex container for the two buttons -->
            <button id="create_button" onclick="beginCreateButton()" style="flex-grow: 0; flex-shrink: 0; margin-right: 10px;">Create a New Button</button>
            <button id="delete_button" onclick="delete_button()" style="flex-grow: 0; flex-shrink: 0;">Delete a Button</button>
        </div>
    </div>`;

    // Add Dev Tools section
    htmlContent += `
        <div style="flex: 1; flex-direction: column;">
            <h3>Dev Tools:</h3>
            <button id="updateScriptButton" onclick="updatePythonScript()">Update Script</button>
        </div>`;

    // Close the wrapper
    htmlContent += `</div>`;

    // Inject our structured HTML into the 'content' div
    document.getElementById('content').innerHTML = htmlContent;
    fetchButtons();
}

function killGame(personName) {
    const ipAddress = personToIpMap[personName];
    if (!ipAddress) {
        alert('Error: Could not determine IP address for ' + personName);
        return;
    }
    fetch(`http://${ipAddress}:8000/kill`, {
        method: 'GET'
    })
    .then(response => {
        const robloxStatus = response.headers.get('Roblox-Status');
        return response.text().then(text => {
            if (!response.ok) {
                throw new Error(text);
            }
            return text;
        });
    })
    .then(message => {
        alert(message);
    })
    .catch(error => {
        console.error('There was an error!', error);
        alert(error.message);
    });
}

function checkRobloxStatus(personName) {
    const ipAddress = personToIpMap[personName];
    if (!ipAddress) {
        alert('Error: Could not determine IP address for ' + personName);
        return;
    }

    // Timeout promise that rejects after 3 seconds
    const timeout = new Promise((_, reject) =>
        setTimeout(() => reject(new Error('Request took too long, computer might be sleeping.')), 3000)
    );

    Promise.race([
        fetch(`http://${ipAddress}:8000/isRunning`),
        timeout
    ])
    .then(response => {
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }

        const robloxStatus = response.headers.get('Roblox-Status');
        if (robloxStatus) {
            alert(`Roblox is ${robloxStatus}`);
        } else {
            throw new Error('Roblox status header not found.');
        }
    })
    .catch(error => {
        console.error('There was an error!', error);
        alert(error.message);
    });
}

function updatePythonScript() {
    if (!currentSelectedPerson) {
        alert('No person has been selected.');
        return;
    }
    const ipAddress = personToIpMap[currentSelectedPerson];
    if (!ipAddress) {
        alert('Error: Could not determine IP address for the selected person.');
        return;
    }

    fetch(`http://${ipAddress}:5000/update_script`)
    .then(response => {
        if (!response.ok) {
            throw new Error(response.statusText);  // If it's not a 200 response, throw the status text
        }
        return response.text();
    })
    .then(data => {
        alert(data);
    })
    .catch(error => {
        console.error('Error updating script:', error);
        alert('Failed to update script: ' + error.message);
    });
}

function fetchButtons() {
    console.log("Fetching buttons...");
    fetch('../button_API.php?fetch=buttons')
    .then(response => {
        if (!response.ok) {
            throw new Error(`Network response was not ok: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log("Received buttons data:", data, "Buttons array:", data.buttons);
        
        const container = document.getElementById('AdminToolsContainer');
        const existingButtons = container.querySelectorAll('button:not(#create_button):not(#delete_button)');  // select all buttons except create and delete
        existingButtons.forEach(btn => btn.remove());  // remove existing buttons
        
        if (data.buttons && data.buttons.length > 0) {
            data.buttons.forEach(button => {
                const btn = document.createElement('button');
                btn.innerText = button.text;
                btn.onclick = () => handleButtonAction(button.action);
                container.appendChild(btn);
            });
        } else {
            const noAdminToolsText = document.createElement('p');
            noAdminToolsText.innerText = 'No admin tools available at the moment.';
            container.appendChild(noAdminToolsText);
        }
        
    })
    .catch(error => {
        console.error('Error fetching buttons:', error);
    });
}

function handleButtonAction(action) {
    switch(action) {
        case 'startProcess':
            // Call your start process API
            fetch('../button_API.php?action=startProcess')
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Error starting process:', error);
            });
            break;
        case 'killProcess':
            // Call your kill process API
            fetch('../button_API.php?action=killProcess')
            .then(response => response.json())
            .then(data => {
                alert(data.message);
            })
            .catch(error => {
                console.error('Error killing process:', error);
            });
            break;
        // ... other actions
        default:
            alert('Unknown action.');
    }
}

function beginCreateButton() {

    document.getElementById('content').innerHTML = '';
    // Get the content div
    const content = document.getElementById('content');
    
    // Create a div for the form
    const formDiv = document.createElement('div');
    formDiv.setAttribute("id", "createForm");

    // Define the form HTML
    const formHTML = `
        <h2>Create a New Button</h2>
        <div>
            <label>What should the button do?</label>
            <select id="buttonAction">
                <option value="startProcess">Start a Process</option>
                <option value="killProcess">Kill a Process</option>
            </select>
        </div>
        <div>
            <label for="processName">Process Name:</label>
            <input type="text" id="processName">
        </div>
        <div>
            <label for="buttonDisplayName">Button Display Name:</label>
            <input type="text" id="buttonDisplayName">
        </div>
        <div>
            <button onclick="submitNewButton()">Create Button</button>
        </div>
    `;

    // Set the form HTML to the formDiv
    formDiv.innerHTML = formHTML;

    // Append the formDiv to the content
    content.appendChild(formDiv);
}

function submitNewButton() {
    const action = document.getElementById('buttonAction').value;
    const processName = document.getElementById('processName').value;
    const displayName = document.getElementById('buttonDisplayName').value;

    if (!action || !processName || !displayName) {
        alert("Please fill out all fields before submitting.");
        return;
    }

    fetch('/server_side/button_API.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            text: displayName,
            action: action + "|" + processName  // Storing action and processName together separated by "|"
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadPersonData(currentSelectedPerson); // Reload to the previous state
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error creating button:', error);
    });
}

function delete_button() {
    fetch('../button_API.php?fetch=buttons')
    .then(response => response.json())
    .then(data => {
        if (data.success && Array.isArray(data.buttons)) {
            let htmlContent = `<h2>Select a Button to Delete</h2>`;
            htmlContent += `<select id="buttonDeleteDropdown">`;
            htmlContent += `<option value="">Choose a button</option>`;
            data.buttons.forEach(button => {
                htmlContent += `<option value="${button.id}">${button.text}</option>`;
            });
            htmlContent += `</select>`;
            htmlContent += `<button onclick="handleDeleteButton()">Delete Button</button>`;

            document.getElementById('content').innerHTML = htmlContent;
        } else {
            alert('Error fetching buttons or no buttons available to delete.');
        }
    })
    .catch(error => {
        console.error('Error fetching buttons:', error);
    });
}

function handleDeleteButton() {
    const selectedButtonId = document.getElementById('buttonDeleteDropdown').value;
    if (selectedButtonId) {
        fetch(`../button_API.php?id=${selectedButtonId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                loadPersonData(currentSelectedPerson);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error deleting button:', error);
        });
    } else {
        alert('Please select a button to delete.');
    }
}

function DataAnalyst() {
    notification
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

    const contentContainerDiv = document.getElementById('contentContainer');
    if (contentContainerDiv) {
        contentContainerDiv.innerHTML = '';
    }


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

//Show Feedback

function showFeedbacks() {
    const contentDiv = document.getElementById('content');
    contentDiv.innerHTML = ""; // Clear the content div

    const feedbackContainer = document.createElement('div'); // Create feedbackContainer div
    feedbackContainer.id = 'feedbackContainer';
    contentDiv.appendChild(feedbackContainer); // Append it to contentDiv

    fetch('fetchFeedbacks.php')
    .then(response => response.json())
    .then(data => {
        console.log("Raw data from server:", data);  // Log the raw data

        if(data.success) {
            if(data.feedbacks.length === 0) {  // Check if feedbacks array is empty
                feedbackContainer.innerHTML = "<p><strong>There's no feedback.</strong></p>";
                return;  // Exit the function
            }

            data.feedbacks.forEach(feedback => {
                console.log("Individual feedback:", feedback); // Log each feedback item

                const name = feedback.user || "Unknown"; // Use the 'user' property
                const feedbackMessage = feedback.message || "No message provided"; // Use the 'message' property
                const timestamp = feedback.timestamp || "Timestamp not available"; // Extract the timestamp

                const feedbackBox = document.createElement('div');
                feedbackBox.classList.add('feedback-box');
                feedbackBox.innerHTML = `
                    <p>Name: ${name}. Feedback:</p>
                    <p><strong>${feedbackMessage}</strong></p>
                    <p>Timestamp: ${timestamp}</p>
                `;

                const deleteButton = document.createElement('button');
                deleteButton.innerText = "Delete";
                deleteButton.onclick = function() {
                    deleteFeedback(feedback.id); // Use the ID here
                };

                feedbackBox.appendChild(deleteButton);
                feedbackContainer.appendChild(feedbackBox);
            });
        } else {
            console.error("Error fetching feedbacks:", data.message);
        }
    })
    .catch(error => {
        console.error('Error in fetching feedback:', error);
    });
}

function deleteFeedback(id) {
    fetch('deleteFeedback.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert("Feedback deleted successfully");
            showFeedbacks();  // Refresh the feedback list
        } else {
            alert("Error deleting feedback:", data.message);
        }
    })
    .catch(error => {
        console.error('Error in deleting feedback:', error);
    });
}

//See the contents of the databases and display them

function loadDBInfo() {
    let dbSelectionHTML = `
        <h2>Database Info</h2>
        <p>Select a database from the list below to view its contents:</p>
        <button onclick="displaySearchBarAndTableForDB('Admin')">Admin</button>
        <button onclick="displaySearchBarAndTableForDB('Parent')">Parent</button>
        <button onclick="displaySearchBarAndTableForDB('Kid')">Kid</button>
    `;

    document.getElementById("content").innerHTML = dbSelectionHTML;
}

function viewDBContents(dbName) {
    const searchTerm = document.getElementById('searchTerm').value;

    fetch('fetch_data.php', {
        method: 'POST',
        body: JSON.stringify({ database: dbName, searchTerm: searchTerm }),
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then((data) => {
        displayData(data, dbName);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        console.error('Error message:', error.message);
        console.error('Error name:', error.name);
    });
}

function displaySearchBarAndTableForDB(dbName) {
    let searchBarAndTableHTML = `
        <h2>Search in ${dbName} Database</h2>
        <div style="margin-bottom: 20px; display: flex; align-items: center;">
            <input type="text" id="searchTerm" placeholder="Search by username...">
            <button onclick="viewDBContents('${dbName}')">Search</button>
        </div>
        <div id="dbResults"></div>
    `;

    document.getElementById("content").innerHTML = searchBarAndTableHTML;
    viewDBContents(dbName);  // Load the table for the selected DB when this page loads
}

function displayData(data, dbName) {
    let contentHTML = `<h2>Database Contents from ${dbName}</h2>`;
    contentHTML += `<button class="goBack" onclick="loadDBInfo()">Go Back</button>`;
    contentHTML += '<table>';
    contentHTML += '<tr><th>ID</th><th>Username</th><th>Password</th><th>IRL Name</th></tr>';

    // Check if the data received is an array
    if (Array.isArray(data)) {
        data.forEach((item) => {
            contentHTML += `<tr>
                <td>${item.id}</td>
                <td>${item.username}</td>
                <td>${item.password}</td>
                <td>${item.irl_name}</td>
            </tr>`;
        });
    } else if (data.error) { // If data has an error key
        contentHTML += `<tr><td colspan="4">${data.error}</td></tr>`; // Display the error message
    } else {
        contentHTML += `<tr><td colspan="4">Unknown error occurred.</td></tr>`;
    }

    contentHTML += '</table>';
    document.getElementById('dbResults').innerHTML = contentHTML;
}

//This is Database info now it's Create Account

function createAccount() {
    const content = document.getElementById("content");
    const html = `
        <h2>Select Database for New Account</h2>
        <p>Select a database from the list below to Create an Account:</p>
        <button onclick="showAccountForm('Admin')">Admin</button>
        <button onclick="showAccountForm('Parent')">Parent</button>
        <button onclick="showAccountForm('Kid')">Kid</button>
    `;
    content.innerHTML = html;
}

function showAccountForm(db) {
    const content = document.getElementById("content");
    const html = `
        <h2>Create Account for ${db} Database</h2>
        <form id="createAccountForm">
            <label for="username">Username:</label>
            <input type="text" id="newUsername" required>
            <br>

            <label for="password">Password:</label>
            <input type="password" id="newPassword" required>
            <br>

            <label for="irl_name">IRL Name:</label>
            <input type="text" id="irlName" required>
            <br>

            <button type="button" onclick="submitAccount('${db}')">Create</button>
        </form>
    `;
    content.innerHTML = html;
}

function submitAccount(db) {
    const username = document.getElementById('newUsername').value;
    const password = document.getElementById('newPassword').value;
    const irl_name = document.getElementById('irlName').value;

    fetch('create_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ db: db, username: username, password: password, irl_name: irl_name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Account created successfully!');
        } else {
            alert('Error creating account: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

//Now it's Delete an Account

function deleteAccount() {
    let selectionHTML = `
        <h2>Delete Account</h2>
        <p>From Which Database?</p>
        <button onclick="loadDeleteForm('Admin')">Admin</button>
        <button onclick="loadDeleteForm('Parent')">Parent</button>
        <button onclick="loadDeleteForm('Kid')">Kid</button>
    `;

    document.getElementById("content").innerHTML = selectionHTML;
}

function loadDeleteForm(dbName) {
    // Fetch the accounts
    fetch('fetch_data.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ database: dbName })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        displayAccountsForDeletion(data, dbName);

    })
    .catch(error => {
        console.error('There was a problem with the fetch operation:', error);
    });
}

function displayAccountsForDeletion(data, dbName) {
    let contentHTML = `
        <h2>Delete Account from ${dbName} Table</h2>

        <button id="deleteWithUsernameButton" onclick="handleDeleteWithUsername('${dbName}')">Or Delete with Username or IRL Name</button>
        <br><br>  <!-- Add breaks to give some space between button and table -->


        <table>
            <tr><th>ID</th><th>Username</th><th>IRL Name</th></tr>
    `;

    if (!Array.isArray(data)) {
        console.error('Data is not an array:', data);
        contentHTML += `<tr><td colspan="3">No data available</td></tr>`;
    } else {
        data.forEach((item) => {
            contentHTML += `<tr>
                <td>${item.id}</td>
                <td onclick="confirmDelete('${item.username}', ${item.id}, '${dbName}')" style="cursor:pointer; color: #007BFF;">${item.username}</td>
                <td onclick="confirmDelete('${item.irl_name}', ${item.id}, '${dbName}')" style="cursor:pointer; color: #007BFF;">${item.irl_name}</td>
            </tr>`;
        });
    }

    contentHTML += `</table>`;
    document.getElementById("content").innerHTML = contentHTML;
}

function handleDeleteWithUsername(dbName) {
    let formHTML = `
    <h2>Delete Account from ${dbName}</h2>
    <form id="deleteAccountForm" onsubmit="submitDeleteForm(event, '${dbName}')">
        <label for="deleteUsername">Username or Irl Name:</label>
        <input type="text" id="deleteUsername" name="username" required>
        <button type="submit">Delete</button>
    </form>
`;

document.getElementById("content").innerHTML = formHTML;
}

function confirmDelete(accountName, id, dbName) {
    const confirmation = confirm(`Do you really want to delete ${accountName}?`);

    if (confirmation) {
        deleteAccountById(id, dbName);
    }
}

function submitDeleteForm(event, dbName) {
    event.preventDefault(); // Prevent the form from refreshing the page

    const usernameToDelete = document.getElementById('deleteUsername').value;

    fetch('delete_account.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username: usernameToDelete, database: dbName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Account deleted successfully!');
        } else {
            alert('Error deleting account: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function deleteAccountById(id, dbName) {
    fetch('delete_account_by_id.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id, database: dbName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Account deleted successfully!');
            loadDeleteForm(dbName);  // Refresh the accounts table
        } else {
            alert('Error deleting account: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

//View time a person has played
let currentPlaytimes = {};

function filterPlaytimes() {
    const searchValue = document.getElementById("searchPerson").value.toLowerCase();

    const filteredPlaytimes = {};
    for (const person in currentPlaytimes) {
        if (person.toLowerCase().includes(searchValue)) {
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
    .then(response => {
        // Log raw response for debugging
        console.log('Raw Response:', response);

        // Try parsing the response
        return response.json();
    })
    .then(data => {
        console.log('Parsed Response Data:', data);

        if (data.error) {
            console.error('Server-side error:', data.error);
            // You can display an alert or a message on the page if needed
            alert(data.error);
            return;  // Exit the function early
        }

        // If no errors, proceed as usual
        currentPlaytimes = data;
        populateTable(currentPlaytimes);
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
    setInterval(fetchPlaytimes, 7000);
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

//Shows timestamps

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

// timestaps management

function TimeStampsManagement() {
    const contentDiv = document.getElementById("content");

    const html = `
        <h2>Timestamps Management Panel</h2>

        <!-- Delete Entry by Person's Name -->
        <div>
            <label>Delete Entry:</label>
            <input type="text" id="deleteName" placeholder="Enter person's name...">
            <button onclick="deleteEntry()">Delete</button>
        </div>

        <!-- Delete Entry by Timestamp -->
        <div>
            <label>Delete Entry by Timestamp:</label>
            <input type="text" id="deleteTimestamp" placeholder="Enter timestamp...">
            <button onclick="deleteEntryByTimestamp()">Delete by Timestamp</button>
        </div>

        <!-- Update Entry using Timestamp -->
        <div>
            <h3>Update Entry by Timestamp :</h3>
            <label>Timestamp (optional):</label>
            <input type="text" id="updateTimestamp" placeholder="Enter timestamp">
            <label>Person's Name:</label>
            <input type="text" id="updateName" placeholder="Enter person's name">
            <label>Status:</label>
            <input type="text" id="updateStatus" placeholder="Enter new status">
            <label>Game:</label>
            <input type="text" id="updateGame" placeholder="Enter new game">
            <button onclick="updateEntry()">Update</button>
        </div>

    `;

    contentDiv.innerHTML = html;
}

function deleteEntry() {
    const name = document.getElementById("deleteName").value;

    fetch('delete_entry.php', {
        method: 'POST',
        body: JSON.stringify({name: name}),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Entry deleted successfully!');
        }
    });
}

function deleteEntryByTimestamp() {
    const timestamp = document.getElementById("deleteTimestamp").value;

    fetch('delete_entry_by_timestamp.php', {
        method: 'POST',
        body: JSON.stringify({timestamp: timestamp}),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Entry deleted successfully by timestamp!');
        } else {
            alert(data.message); // Display an error message if the deletion was not successful
        }
    });
}

function updateEntry() {
    const name = document.getElementById("updateName").value;
    const status = document.getElementById("updateStatus").value;
    const game = document.getElementById("updateGame").value;
    const timestamp = document.getElementById("updateTimestamp").value;  // Assuming you added this input field

    fetch('update_entry.php', {
        method: 'POST',
        body: JSON.stringify({
            name: name,
            status: status,
            game: game,
            timestamp: timestamp
        }),
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Entry updated successfully!');
        }
    });
}

//Notifications

document.addEventListener('DOMContentLoaded', function () {

    let notificationTimeout; // Declare timeout variable at the top

    function createNotificationElement() {
        // Create a div for the notification
        const notification = document.createElement("div");
        notification.id = "siteNotification";
        notification.style.display = 'none';
        notification.style.position = 'fixed';
        notification.style.bottom = '0%';
        notification.style.left = '84%';
        notification.style.transform = 'translate(-50%, -50%)';
        notification.style.padding = '20px';
        notification.style.backgroundColor = '#87CEEB';
        notification.style.color = 'black';
        notification.style.borderRadius = '8px';
        notification.style.boxShadow = '0px 0px 10px rgba(0, 0, 0, 0.1)';
        notification.style.zIndex = '9999';

        // Create a strong element for the title
        const titleElement = document.createElement("p");
        titleElement.id = "notificationTitle";
        notification.appendChild(titleElement);

        // Create a paragraph element for the body
        const bodyElement = document.createElement("strong");
        bodyElement.id = "notificationBody";
        notification.appendChild(bodyElement);

        // Attach a click event to the notification to hide it on click
        notification.addEventListener('click', function() {
            fadeOut(notification);
        });

        // Append the notification div to the body of the document
        document.body.appendChild(notification);

        return {
            notification: notification,
            titleElement: titleElement,
            bodyElement: bodyElement
        };
    }

    function fadeOut(element) {
        let op = 1;  // initial opacity
        const timer = setInterval(function () {
            if (op <= 0.1) {
                clearInterval(timer);
                element.style.display = 'none';
            }
            element.style.opacity = op;
            element.style.filter = 'alpha(opacity=' + op * 100 + ")";
            op -= op * 0.1;
        }, 50);
    }

    const { notification, titleElement, bodyElement } = createNotificationElement();

    function showSiteNotification(title, body) {
        // Clear any existing timeout
        if (notificationTimeout) {
            clearTimeout(notificationTimeout);
        }

        titleElement.textContent = title;
        bodyElement.textContent = body;
        notification.style.opacity = 1; // Reset opacity
        notification.style.display = 'block';

        // Set the new timeout for the notification
        notificationTimeout = setTimeout(function(){
            fadeOut(notification);
        }, 15000);
    }

    // Overriding the alert function
    window.originalAlert = window.alert;
    window.alert = function(message) {
        showSiteNotification('Notification:', message);
    };
});