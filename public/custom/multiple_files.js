const dropContainer = document.querySelector('.drop-container');
const fileInput = document.getElementById('file-input');
const previewContainer = document.getElementById('preview-container');

fileInput.addEventListener('change', handleFiles);

dropContainer.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropContainer.style.border = '2px dashed #333';
});

dropContainer.addEventListener('dragleave', (e) => {
    e.preventDefault();
    dropContainer.style.border = '2px dashed #ccc';
});

dropContainer.addEventListener('drop', (e) => {
    e.preventDefault();
    dropContainer.style.border = '2px dashed #ccc';
    fileInput.files = e.dataTransfer.files;
    handleFiles(e);
});

function handleFiles(e) {
    const files = e.target.files || e.dataTransfer.files;
    for (let i = 0; i < files.length; i++) {
        if (files[i].type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const imageContainer = document.createElement('div');
                imageContainer.classList.add('preview-image');

                const image = document.createElement('img');
                image.src = e.target.result;
                imageContainer.appendChild(image);

                const deleteButton = document.createElement('span');
                deleteButton.classList.add('delete-button');
                deleteButton.innerHTML = 'DELETE';
                deleteButton.addEventListener('click', () => {
                    previewContainer.removeChild(imageContainer);
                });

                imageContainer.appendChild(deleteButton);
                previewContainer.appendChild(imageContainer);
            };
            reader.readAsDataURL(files[i]);
        }
    }
}