document.getElementById('login-form').addEventListener('submit', async (e) => {
  e.preventDefault();

  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value.trim();

  try {
    const response = await axios.post('http://127.0.0.1:8000/api/login', {
      username,
      password,
    }, {
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      }
    });

    if (response.status === 200) {
      console.log('Login successful:', response.data);
      window.location.href = '../chat/index.html'; // Redirect to chat page
    } else {
      document.getElementById('error-message').style.display = 'block';
    }
  } catch (error) {
    console.error('Login failed:', error.response?.data || error.message);
    document.getElementById('error-message').style.display = 'block';
  }
});