// --- Configuration ---
// Replace this with the actual IP address or domain of your backend server.
const API_URL = 'http://<YOUR_VPS_IP>:8000';

// --- DOM Elements ---
const searchInput = document.getElementById('search-input');
const searchButton = document.getElementById('search-button');
const resultsContainer = document.getElementById('results-container');
const statusMessage = document.getElementById('status-message');

// --- Functions ---

/**
 * Displays a message to the user (e.g., "Searching...", "No results found.").
 * @param {string} message - The message to display.
 */
const showStatusMessage = (message) => {
    statusMessage.textContent = message;
    statusMessage.style.display = 'block';
};

/**
 * Hides the status message.
 */
const hideStatusMessage = () => {
    statusMessage.style.display = 'none';
};

/**
 * Creates and appends a single result item to the results container.
 * @param {object} result - The search result object.
 */
const createResultItem = (result) => {
    const item = document.createElement('div');
    item.className = 'result-item';

    const source = document.createElement('span');
    source.className = 'source';
    source.textContent = `Source: ${result.source} - ${result.source_channel}`;

    const content = document.createElement('p');
    content.className = 'content';
    content.textContent = result.content;

    const link = document.createElement('a');
    link.className = 'link';
    link.href = result.link;
    link.textContent = 'View Original Message';
    link.target = '_blank'; // Open in a new tab
    link.rel = 'noopener noreferrer';

    item.appendChild(source);
    item.appendChild(content);
    item.appendChild(link);

    resultsContainer.appendChild(item);
};

/**
 * Fetches search results from the API and displays them.
 */
const performSearch = async () => {
    const query = searchInput.value.trim();
    if (!query) {
        showStatusMessage('Please enter a search term.');
        return;
    }

    // Clear previous results and show searching message
    resultsContainer.innerHTML = '';
    showStatusMessage('Searching...');

    try {
        const response = await fetch(`${API_URL}/search?q=${encodeURIComponent(query)}`);

        if (!response.ok) {
            throw new Error(`Network response was not ok (status: ${response.status})`);
        }

        const data = await response.json();
        hideStatusMessage();

        if (data.length === 0) {
            showStatusMessage('No results found.');
        } else {
            data.forEach(createResultItem);
        }
    } catch (error) {
        console.error('Fetch error:', error);
        showStatusMessage('An error occurred while fetching results. Please check the console.');
    }
};

// --- Event Listeners ---

// Listen for clicks on the search button
searchButton.addEventListener('click', performSearch);

// Allow pressing "Enter" in the input field to trigger a search
searchInput.addEventListener('keypress', (event) => {
    if (event.key === 'Enter') {
        performSearch();
    }
});
