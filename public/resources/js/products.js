import {
    fetchProducts,
    injectProductIntoForm,
} from "./products-helper.js";

import {
    fetchData,
    fetchHtml,
    resetForm,
    asynchronousFormSubmission,
    fillEntitiesAsSelectorOptions
} from "./helperFunctions.js";

//______________DEFINITIONS

const productsApi = '/api/products';
const categoriesApi = '/api/categories';
const manufacturersApi = '/api/manufacturers/names';
const productCard = await fetchHtml('/resources/views/elements/adminProductCard.html');
const productsContainer = document.getElementById('products-container');
const showMoreProductsButton = document.getElementById('show-more-button');
const filtersSortSelector = document.getElementById('filters-sorting-selector');

let filters = {
    page: 1,
    limit: 10,
    category: null,
    orderBy: null,
    orderDir: null,
}

//______________HELPER LOGIC

// fetch categories
// cache them (save them in an array for now)
let categories = await fetchData(categoriesApi, 'json');
let manufacturers = await fetchData(manufacturersApi, 'json');

// fill category selector with categories
const filtersCategorySelector = document.getElementById('filters-category-selector');
fillEntitiesAsSelectorOptions(filtersCategorySelector, categories);

// put create product form in the DOM
const popupWindow = document.getElementById('popup-window');

//TODO: in createProductForm: make the first child a container not a heading tag
// or add a query parameter to the fetchHtml function so that the function returns the result of the query

// await fetchHtml(
//     '/resources/views/elements/createProductForm.html',
//     popupWindow.querySelector('#popup-content')
// );

const createProductForm = popupWindow.querySelector('form#create-product-form');
const imageContainer = popupWindow.querySelector('div.image-container');
asynchronousFormSubmission(createProductForm, resetPage);
// popupWindow.querySelector('#popup-content').appendChild(createProductForm);

popupWindow.querySelector('#close-button').addEventListener('click', () => {
    try {
        resetForm(createProductForm, imageContainer);
    } catch(e) {
        console.error(e);
    }
    popupWindow.classList.add('hidden');
});

// activate create product form
let categorySelector = createProductForm.querySelector('#category-selector');
fillEntitiesAsSelectorOptions(categorySelector, categories);

let manufacturerSelector = createProductForm.querySelector('#manufacturer-selector');
fillEntitiesAsSelectorOptions(manufacturerSelector, manufacturers);

//TODO: activate create category button

//______________RUN
// sort selector: already populated, make it functional then
filtersSortSelector.addEventListener('change', function () {
    filters.orderBy = this.options[this.selectedIndex].getAttribute('data-name');
    filters.orderDir = this.options[this.selectedIndex].getAttribute('data-dir');
    filters.page = 1;
    run();
})

export function run() {
    console.log('running......')
    document.getElementById('products-button').classList.add('active');

    fetchProducts({
        route: productsApi,
        filters: filters,
        card: productCard,
        container: productsContainer,
        showMoreButton: showMoreProductsButton
    });
}

export function resetPage() {
    resetForm(createProductForm, imageContainer);
    popupWindow.classList.add('hidden');

    // when `fetchProducts` function finds that the page is 1,
    // it clears the container first
    filters = filters = {
        page: 1,
        limit: 10,
        category: null,
        orderBy: null,
        orderDir: null,
    };

    // modify sorting to bring last updated first
    // change in sorting triggers the run function
    filtersSortSelector.value = 'last-updated';
    const changeEvent = new Event('change');
    filtersSortSelector.dispatchEvent(changeEvent);
}

resetPage();

//______________OTHER EVENT LISTENERS

// handling edit and delete buttons
// will go with event.target.closest approach for better performance
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

// add event listener to show-more button
showMoreProductsButton.addEventListener('click', function () {
    filters.page++;
    run();
});

// filter by category functionality
filtersCategorySelector.addEventListener('change', function () {
    filters.category = this.value;
    filters.page = 1;
    run();
});


// add event listener to create button
const createProductButton = document.getElementById('create-product-button');
createProductButton.addEventListener('click', () => {
    resetForm(createProductForm, imageContainer);
    createProductForm.action = productsApi;
    createProductForm.enctype = 'multipart/form-data'; // just making sure

    const fileInput = createProductForm.querySelector('input[type=file]');
    if(fileInput) {
        fileInput.setAttribute('required', '');
    }

    popupWindow.classList.remove('hidden');
});

// add event listener to create category button
const createCategoryButton = createProductForm.querySelector('#create-category-button');
createCategoryButton.addEventListener('click', async function()  {
    /** @type {HTMLInputElement} */
    const field = document.getElementById('new-category-field');
    console.log(field.value);

    const errorField = field.parentElement.querySelector('div.invalid-feedback');

    const categoryName = field.value;
    errorField.innerHTML = '';

    fetch(categoriesApi, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({'name': categoryName}),
    })
        .then(async response =>   {
            console.log(response);
            if (response.ok) {
                console.log('request accepted');
                field.value = '';

                categories = await fetchData(categoriesApi, 'json');
                fillEntitiesAsSelectorOptions(categorySelector, categories);
            } else if (response.status === 400) {
                console.log('validation error');

                (response.json())
                    .then(errors => {
                        console.log(errors);
                        errorField.innerHTML = errors['name'];
                    })
            }
        })

})

// create product form: show image if a one is chosen
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
