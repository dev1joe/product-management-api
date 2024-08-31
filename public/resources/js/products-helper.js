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
    card.querySelector('.price').textContent = `$${(product.unitPriceCents / 100)}`;

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

    fetch(url)
        .then(response => response.json())
        .then(data => {

            if(data.length === 0) {
                console.log('no more products');
                showMoreButton.classList.add('hidden');
                return;
            }
            showMoreButton.classList.remove('hidden');

            if(filters.page === 1) {
                container.innerHTML = '';
            }
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
 * @param {object} filters
 * @param {string} route
 */
function applyFilters(filters, route) {
    const url = new URL(route, window.location.origin);

    // Add all active filters to the URL as query parameters
    for (const key in filters) {
        if (filters[key]) {
            url.searchParams.set(key, filters[key]);
        }
    }

    // console.log(url);
    return url;
}

/**
 * @param {HTMLSelectElement} container
 * @param {Array} categories
 */
export function fillCategoriesAsSelectorOptions(container, categories) {
    categories.forEach(category => {
        const optionElement = document.createElement('option');
        optionElement.textContent = category.name;
        optionElement.value = category.id;

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
    form.querySelector('#price').value =  product.unitPriceCents / 100;
    form.querySelector('textarea').textContent = product.description;
    form.querySelector('#category-selector').value = product.category.id;
    form.querySelector('#manufacturer-selector').value = 'Apple';

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
            window.location.reload(); //TODO: remove reload
        } else {
            const errors = await response.json();
            console.error(errors);
            displayValidationErrors(form, errors);
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
            parent.querySelector('div.invalid-feedback').textContent = errors[fieldName];
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
