// Your app credentials
const sender_id = 80; // This should come from your auth logic
const receiver_id = 52; // ID of the other user in the chat
const authToken = 'Bearer 76|xLe2jEBT64FeFeFHS2OA6U4Osc4iFKmxNW5JcExtc89dd5a3';

// Echo + Pusher config
window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: 'pusher',
  key: "a15caebdd7d644a83788",
  cluster: "ap1",
  forceTLS: true,
  encrypted: true
});

// DOM references
const chatBox = document.getElementById('chat-box');
const form = document.getElementById('message-form');
const input = document.getElementById('message-input');

// Add new message to chat box
function addMessage(text, from = 'other') {
  const msg = document.createElement('div');
  msg.textContent = text;
  msg.className = from === 'self' ? 'message self' : 'message';
  chatBox.appendChild(msg);
  chatBox.scrollTop = chatBox.scrollHeight;
}

// Load chat history on page load
window.onload = async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/message/' + `${receiver_id}`, {
      headers: {
        'Authorization': authToken,
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      }
    });

    const messages = response.data;

    for (let msg of messages) {
      const from = msg.sender_id === sender_id ? 'self' : 'other';
      addMessage(msg.message, from);
    }

  } catch (err) {
    console.error("Failed to load messages:", err.response?.data || err.message);
  }
};

// Send message (to backend)
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const message = input.value.trim();
  if (!message) return;

  addMessage(message, 'self');
  input.value = '';

  axios.post('http://127.0.0.1:8000/api/message' + `/${receiver_id}`, {
    message: message,
  }, {
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': authToken,
    }
  })
  .then(response => {
    console.log('Message sent:', response.data);
  })
  .catch(error => {
    console.error('Failed to send message:', error.response?.data || error.message);
  });
});

// Listen for broadcasted messages
window.Echo.private(`chat.${sender_id}`) // Listen on current user's private channel
  .listen('MessageSent', (messageData) => {
    console.log('Received message:', messageData);
    // Ignore messages we sent ourselves (already rendered)
    addMessage(`${messageData.sender_id}: ${messageData.message}`, 'other');
  })
  .error((error) => {
    console.error('Error listening to messages:', error);
  });
