document.addEventListener('DOMContentLoaded', () => {
    const openModalBtn = document.getElementById('openModalBtn');
    const addBookModal = document.getElementById('addBookModal');

    // Open modal when 'Add New' button is clicked
    openModalBtn.addEventListener('click', () => {
        addBookModal.style.display = 'flex';
    });
});



window.onload = function () {
    var successMessage = document.querySelector('.alert-success');
    var errorMessage = document.querySelector('.alert-error');
    var deletedMessage = document.querySelector('.alert-deleted');

    if (successMessage) {
        setTimeout(function () {
            successMessage.classList.remove('show');
        }, 3000); // Hide after 3 seconds
    }

    if (errorMessage) {
        setTimeout(function () {
            errorMessage.classList.remove('show');
        }, 3000);
    }

    if (deletedMessage) {
        setTimeout(function () {
            deletedMessage.classList.remove('show');
        }, 3000); 
    }
};

// Toggle password visibility for login
const togglePassword = document.getElementById('togglePassword');
const password = document.getElementById('password');

togglePassword.addEventListener('click', function () {
    const type = password.type === 'password' ? 'text' : 'password';
    password.type = type;

    this.classList.toggle('fa-eye-slash');
});

// Toggle confirm password visibility for register
const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
const confirmPassword = document.getElementById('confirmPassword');

toggleConfirmPassword.addEventListener('click', function () {
    const type = confirmPassword.type === 'password' ? 'text' : 'password';
    confirmPassword.type = type;

    this.classList.toggle('fa-eye-slash'); 
});


function openEditModal(bookId) {
    // Fetch book data
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
            document.getElementById('editBookModal').style.display = 'flex';
        });
}


// Function to close the modal
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.style.display = "none";
}   
