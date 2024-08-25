const categorySelector = document.getElementById('category-selector');

/**
 * @param {string} route
 * @param {HTMLElement} container
 * @param {string} entityName
 */
export function fetchEntities(route, container, entityName) {
    console.log('fetching entities....')
    fetch(route)
        .then(response => response.json())
        .then(data => {

            // make sure categories are found
            if (data.length === 0) {
                console.log(`no ${entityName}(s) found`);
                return;
            }

            // reset container
            const tmpOption = document.createElement('option');
            tmpOption.textContent = `-- select ${entityName} --`;
            tmpOption.setAttribute('selected', '');
            tmpOption.setAttribute('disabled', '');

            container.innerHTML = '';
            container.appendChild(tmpOption);

            data.forEach(entity => {
                const optionElement = document.createElement('option');
                optionElement.textContent = entity.name;
                optionElement.setAttribute('value', entity.id);

                container.appendChild(optionElement);
            })
        })
        .catch(error => {
            console.error('error fetching entities');
            console.error(error);
        });
}

/**
 * @param {string} route
 * @param {HTMLElement} container
 * @param {string} entityName
 */
export function fetchCategories({
                                    route = '/admin/categories',
                                    container = categorySelector,
                                    entityName = 'category'
                                } = {}) {
    console.log('fetching categories....')
    fetchEntities(route, container, entityName);
}


/**
 * @param {int} page
 * @param {int} limit
 * @param {HTMLElement} container
 * @param {HTMLElement} loadMoreButton
 */
export function loadProductCards(page, limit, container, loadMoreButton) {
    // show button
    loadMoreButton.classList.remove('hidden')

    // if fetching the first page, then empty the container
    if (page === 1) {
        container.innerHTML = '';
    }

    const newContainer = document.createElement('div');
    newContainer.style.display = 'grid';
    newContainer.style.gridTemplateColumns = 'repeat(5, 1fr)';
    newContainer.style.gap = '10px';

    fetch(`/admin/products?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            //validate that there is data to show
            if (data.length === 0) {
                console.log('no more products to fetch');
                loadMoreButton.classList.add('hidden');
                return;
            }

            fetch('/resources/views/elements/adminProductCard.twig')
                .then(response => response.text())
                .then(productCardHtml => {
                    data.forEach(product => {
                        // convert html to node
                        const tmpDiv = document.createElement('div');
                        tmpDiv.innerHTML = productCardHtml;
                        const productCard = tmpDiv.firstChild;

                        // add data to node
                        productCard.querySelector('.product-img').setAttribute('src', product.photo);
                        productCard.querySelector('.product-name').textContent = product.name;
                        productCard.querySelector('button').setAttribute('data-id', product.id)

                        // append to the container
                        newContainer.appendChild(productCard);
                    })
                })
        })

    container.appendChild(newContainer);
}

/**
 * @param {string} name
 * @param {HTMLElement} errorField
 */
export function createCategory(name, errorField) {
    errorField.innerHTML = '';

    fetch('/api/categories', {
        method: 'POST',
        header: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({'name': name}),
    }).then(response => {
        if (response.ok) {
            console.log('request accepted');

            fetchCategories();
        } else if (response.status === 400) {
            console.log('validation error');

            (response.json())
                .then(errors => {
                    console.log(errors);
                    errorField.innerHTML = errors['name'];
                })
        }
    })
}

//TODO: add fetchManufacturers function

/**
 * @param {HTMLElement} canvas
 * @param {string} type
 */
export function createChart(type) {
    const widget = document.createElement('div');
    const chartElement = document.createElement('canvas');

    new Chart(chartElement, {
        type: type,
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    return chartElement;
}