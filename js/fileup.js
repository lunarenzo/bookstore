
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('book_cover').onchange = function() {
        var fileName = this.files[0] ? this.files[0].name : 'No file chosen';
        this.parentNode.querySelector('.file-upload-placeholder span').textContent = fileName;
    };

    document.getElementById('edit_book_cover').onchange = function() {
        var fileName = this.files[0] ? this.files[0].name : 'Drag and drop or click to upload new cover';
        this.parentNode.querySelector('.file-upload-placeholder span').textContent = fileName;
    };
});
