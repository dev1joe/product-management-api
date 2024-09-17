import {applyFilters} from "./helperFunctions.js";

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
    card.querySelector('.price').textContent = `$${(product.unitPriceInCents / 100)}`;

    const buttons = card.querySelectorAll('button');
    buttons.forEach(b => {
        b.setAttribute('data-id', product.id);
    });

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
    console.log(url);

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

            console.log(data);

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
