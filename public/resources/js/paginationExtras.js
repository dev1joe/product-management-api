import {asynchronousFormSubmission, resetForm} from "./helperFunctions.js";
import {injectProductIntoForm} from "./productsHelper.js";
import {resetPage} from "./pagination.js"

//______________DEFINITIONS
const productsApi = '/api/products';
const popupWindow = document.getElementById('popup-window');
const createProductForm = popupWindow.querySelector('form#create-product-form');
const imageContainer = popupWindow.querySelector('div.image-container');
const createProductButton = document.getElementById('create-product-button');
const productsContainer = document.getElementById('products-container');

//______________Form Handling
asynchronousFormSubmission(createProductForm, resetPage);

//______________Create Button Handling
createProductButton.addEventListener('click', () => {
    resetForm(createProductForm, imageContainer);
    createProductForm.action = productsApi;

    // setting the file input as required, because it's not in the update form
    const fileInput = createProductForm.querySelector('input[type=file]');
    if(fileInput) {
        fileInput.setAttribute('required', '');
    }

    popupWindow.classList.remove('hidden');
});

//______________Close Button Handling
popupWindow.querySelector('#close-button').addEventListener('click', () => {
    try {
        resetForm(createProductForm, imageContainer);
    } catch(e) {
        console.error(e);
    }
    popupWindow.classList.add('hidden');
});

//______________Management Buttons (edit / archive / delete) handling
productsContainer.addEventListener('click', function (event) {
    const editButton = event.target.closest('#edit-button');
    const deleteButton = event.target.closest('#delete-button');

    if (editButton) {
        const productId = parseInt(editButton.getAttribute('data-id'));

        fetch(`${productsApi}/${productId}`)
            .then(response => response.json())
            .then(product => {
                product = product[0];
                console.log(product);

                const fileInput = createProductForm.querySelector('input[type=file]');
                if(fileInput) {
                    fileInput.removeAttribute('required');
                }

                injectProductIntoForm(product, createProductForm, productsApi);
                popupWindow.classList.remove('hidden');
            });

    } else if (deleteButton) {
        if (confirm('are you sure you want to delete this product ??')) {
            console.log('admin wants to delete a product :(');

            const productId = parseInt(deleteButton.getAttribute('data-id'));

            const url = `${productsApi}/${productId}`;
            fetch(url, {
                method: 'DELETE',
            })
                .then(response => {
                    if (response.ok) {
                        console.log('product deleted successfully');
                        // window.location.reload();
                        resetPage()
                    }
                })
                .catch((error) => console.error(error));
        }
    }
});

//______________Image Handling
const imageInput = createProductForm.querySelector('input[type=file]');

if(imageInput && imageContainer) {

    imageInput.addEventListener('change', function(event) {
        const image = event.target.files[0];

        if(image) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imageContainer.classList.remove('hidden');
                imageContainer.style.backgroundImage = `url('${e.target.result}')`;
            }
            reader.readAsDataURL(image);
        }
    })
}

// TODO: what to do with create category and manufacturer buttons in create product form ?