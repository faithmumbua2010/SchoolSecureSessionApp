

// Validate the signup form
document.getElementById('signup-form').addEventListener('submit', function(event) {
    var username = document.getElementById('username').value;
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    if (username === '' || email === '' || password === '') {
        alert('All fields are required!');
        event.preventDefault();
    }
});

// Validate the login form
document.getElementById('login-form').addEventListener('submit', function(event) {
    var email = document.getElementById('email').value;
    var password = document.getElementById('password').value;

    if (email === '' || password === '') {
        alert('Email and password are required!');
        event.preventDefault();
    }

});
