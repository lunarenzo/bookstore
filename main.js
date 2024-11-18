document.addEventListener('DOMContentLoaded', () => {
    const themeToggle = document.querySelector('.theme-toggle');
    const html = document.documentElement;
    const themeIcon = themeToggle.querySelector('i');
    const themeText = themeToggle.querySelector('.theme-toggle-text');

    const savedTheme = localStorage.getItem('theme') || 'dark';
    html.setAttribute('data-theme', savedTheme);
    updateThemeUI(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeUI(newTheme);
    });

    function updateThemeUI(theme) {
        // Update icon
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
        
        // Update text
        themeText.textContent = theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const openModalBtn = document.getElementById('openModalBtn');  // Button to open the modal
    const addBookModal = document.getElementById('addBookModal');
    const closeModalBtn = document.getElementById('closeModalBtn'); // Button to close the modal

    // Open modal when 'Add New' button is clicked
    openModalBtn.addEventListener('click', () => {
        addBookModal.style.display = 'block';
    });

    // Close modal when 'x' button is clicked
    closeModalBtn.addEventListener('click', () => {
        addBookModal.style.display = 'none';
    });

    // Close modal if clicked outside of modal content
    window.addEventListener('click', (event) => {
        if (event.target === addBookModal) {
            addBookModal.style.display = 'none';
        }
    });
});

window.onload = function() {
    var successMessage = document.querySelector('.alert-success');
    var errorMessage = document.querySelector('.alert-error');
    var deletedMessage = document.querySelector('.alert-deleted');
    
    if (successMessage) {
        setTimeout(function() {
            successMessage.classList.remove('show');
        }, 3000); // Hide after 3 seconds
    }
    
    if (errorMessage) {
        setTimeout(function() {
            errorMessage.classList.remove('show');
        }, 3000); // Hide after 3 seconds
    }

    if (deletedMessage) {
        setTimeout(function() {
            deletedMessage.classList.remove('show');
        }, 3000); // Hide after 3 seconds
    }
};

  // Toggle password visibility for login
  const togglePassword = document.getElementById('togglePassword');
  const password = document.getElementById('password');
  
  togglePassword.addEventListener('click', function() {
    // Toggle the type of the password field
    const type = password.type === 'password' ? 'text' : 'password';
    password.type = type;
    
    // Toggle the eye icon
    this.classList.toggle('fa-eye-slash'); // Change the icon to show password (closed eye)
  });

  // Toggle confirm password visibility for register
  const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
  const confirmPassword = document.getElementById('confirmPassword');
  
  toggleConfirmPassword.addEventListener('click', function() {
    // Toggle the type of the confirm password field
    const type = confirmPassword.type === 'password' ? 'text' : 'password';
    confirmPassword.type = type;
    
    // Toggle the eye icon
    this.classList.toggle('fa-eye-slash'); // Change the icon to show password (closed eye)
  });
