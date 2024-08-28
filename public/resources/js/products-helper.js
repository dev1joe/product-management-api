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
    console.log(card);

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