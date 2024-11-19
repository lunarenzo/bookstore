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

    // Open modal when 'Add New' button is clicked
    openModalBtn.addEventListener('click', () => {
        addBookModal.style.display = 'block';
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


  function openEditModal(bookId) {
    // Fetch book data and populate the form
    fetch('get_book_details.php?id=' + bookId)
        .then(response => response.json())
        .then(data => {
            document.getElementById('book_id').value = data.id;
            document.getElementById('edit_title').value = data.title;
            document.getElementById('edit_isbn').value = data.isbn;
            document.getElementById('edit_author').value = data.author;
            document.getElementById('edit_price').value = data.price;
            document.getElementById('edit_genre').value = data.genre;
            document.getElementById('edit_date_published').value = data.date_published;
            document.getElementById('editBookModal').style.display = 'block';
        });
}


// Function to close the modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = "none"; // Hide the modal
}   
