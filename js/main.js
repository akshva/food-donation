// js/main.js
// Example: Add validation to registration form
document.addEventListener("DOMContentLoaded", function() {
    const regForm = document.querySelector('form[action="register.php"]');
    
    if (regForm) {
        regForm.addEventListener("submit", function(event) {
            const password = regForm.querySelector('input[name="password"]');
            if (password.value.length < 6) {
                alert("Password must be at least 6 characters long.");
                event.preventDefault(); // Stop form from submitting
            }
            
            // You can add more checks here (e.g., email format)
        });
    }
});