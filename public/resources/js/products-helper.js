import {applyFilters} from "./helperFunctions.js";
import {resetPage, run} from "./products.js";

/**
 * @param {string} route
 */
export async function getProductCard(route) {
    let response = await fetch(route);
    const productCardHtml = await response.text();

    const tmpDiv = document.createElement('div');
    tmpDiv.innerHTML = productCardHtml;

    return tmpDiv.firstChild;
}

/**
 * @param {HTMLElement} card
 * @param {object} product
 */
function fillProductCard(card, product) {
    card.querySelector('.product-img').setAttribute('src', product.photo);
    card.querySelector('.product-name').textContent = product.name;
    card.querySelector('button').setAttribute('data-id', product.id)
    card.querySelector('.price').textContent = `$${(product.unitPriceInCents / 100)}`;

    // handling edit button
    const editButton = card.querySelector('#edit-button');
    editButton.setAttribute('data-id', product.id);

    // handling delete button
    const deleteButton = card.querySelector('#delete-button');
    deleteButton.setAttribute('data-id', product.id);

    return card;
}

/**
 * @param {RequestInfo | URL} url used to fetch data
 * @param {Object} filters
 * @param {Node} card product front-end
 * @param {HTMLElement} container to show data in
 * @param {HTMLButtonElement} showMoreButton let the function control the button's visibility
 */
export function fetchProducts({route = '/api/products', filters, card, container, showMoreButton} = {}) {
    const url = applyFilters(filters, route);
    // console.log(url);

    fetch(url)
        .then(response => response.json())
        .then(data => {

            if(filters.page === 1) {
                container.innerHTML = '';
            }

            if(data.length === 0) {
                console.log('no more products');
                showMoreButton.classList.add('hidden');
                return;
            }
            showMoreButton.classList.remove('hidden');

            // console.log(data);

            data.forEach(product => {
                // add data to node
                let cardCopy = card.cloneNode(true);
                fillProductCard(cardCopy, product);

                // append to the container
                container.appendChild(cardCopy);
            })
        })
        .catch((error) => {
            console.error(error);
        });
}


/**
 * @param {HTMLSelectElement} container
 * @param {Array} entities
 */
export function fillEntitiesAsSelectorOptions(container, entities) {
    entities.forEach(e => {
        const optionElement = document.createElement('option');
        optionElement.textContent = e.name;
        optionElement.value = e.id;

        container.appendChild(optionElement);
    });
}

/**
 * @param {Object} product
 * @param {HTMLElement} form
 * @param {string} productUpdateRoute
 */
export function injectProductIntoForm(product, form, productUpdateRoute) {
    form.querySelector('#name').value = product.name;
    form.querySelector('#price').value =  product.unitPriceInCents / 100;
    form.querySelector('textarea').textContent = product.description;

    if(product.manufacturer) {
        form.querySelector('#manufacturer-selector').value = product.manufacturer.id;
    }

    if(product.category) {
        form.querySelector('#category-selector').value = product.category.id;
    }

    // product image preview
    if(product.photo) {
        const imagePreview = form.parentElement.querySelector('div.image-container');

        if(imagePreview) {
            imagePreview.classList.remove('hidden');
            imagePreview.style.backgroundImage = `url(\'${product.photo}\')`;
        }
    }

    // form behavior handling
    form.setAttribute('action', `${productUpdateRoute}/${product.id}`);

    // photo field is not required any more
    //TODO: add image preview section in product form + better handling
    form.querySelector('#photo').removeAttribute('required');
}

/**
 * @param {HTMLFormElement} form
 */
export function asynchronousFormSubmission(form) {
    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        console.log(formData);

        // loop over form data entries
            // if you find a photo key and there is no files uploaded
                // remove it

        for(let [key, value] of formData.entries()) {
            if(key === 'photo') {
                const fileInput = form.querySelector('input[type=file]');

                if(! fileInput.files.length) {
                    formData.delete('photo');
                }
            }
        }

        const url = this.action;
        const request = new Request(url, {
            method: 'POST',
            body: formData,
        });

        const response = await fetch(request);

        // remove past validation errors
        const validationFields = form.querySelectorAll('div.invalid-feedback');
        validationFields.forEach(field => field.textContent = '');

        if(response.ok) {
            console.log('form submitted successfully!');
            try{
                const messages = await response.json();
                console.log(messages)
            } catch(e){}

            // window.location.reload();
            resetPage()
        } else {
            const status = response.status;

            const responseErrors = await response.json();
            console.error(responseErrors);

            // if validation exception display it
            if(status === 400) {
                displayValidationErrors(form, responseErrors);
            }
        }

    })
}

/**
 * @param {HTMLFormElement} form
 */
export function resetForm(form) {
    form.reset();

    form.setAttribute('action', '');

    const textarea = form.querySelector('textarea');
    if(textarea) {
        textarea.textContent = 'Product Details......';
    }

    const imagePreview = form.parentElement.querySelector('div.image-container');
    if(imagePreview) {
        imagePreview.removeAttribute('background-image');
        imagePreview.classList.add('hidden');
    }

    const errorFields = form.querySelectorAll('div.invalid-feedback');
    errorFields.forEach(field => {
        field.textContent = '';
    })
}

/**
 * @param {HTMLFormElement} form
 * @param {Object|Array} errors
 */
function displayValidationErrors(form, errors) {
    console.log('displaying validation errors......')

    for(const fieldName in errors) {
        console.log(fieldName);
        const field = form.querySelector(`[name=${fieldName}]`);

        if(field) {
            const parent = field.parentElement;
            parent.querySelector('div.invalid-feedback').textContent = errors[fieldName][0];
        } else {
            console.error(`${fieldName} field not found`);
        }
    }
}


/**
 * @param {HTMLInputElement} field
 * @param {HTMLElement} errorField
 * @param {string} categoriesApi
 */
// export function createCategory(field, errorField, categoriesApi ) {
//     const name = field.value;
//
//     errorField.innerHTML = '';
//
//     fetch(categoriesApi, {
//         method: 'POST',
//         header: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({'name': name}),
//     }).then(response => {
//         if (response.ok) {
//             console.log('request accepted');
//             field.value = '';
//
//             fetchData(categoriesApi, 'json')
//         } else if (response.status === 400) {
//             console.log('validation error');
//
//             (response.json())
//                 .then(errors => {
//                     console.log(errors);
//                     errorField.innerHTML = errors['name'];
//                 })
//         }
//     })
// }

//TODO: add fetchManufacturers function
