import {fetchCategories} from "./helperFunctions.js";

// pagination settings
let page = 1;
const productPerPage = 10;
const productsContainer = document.getElementById('content');

// buttons
const sidebarProductsButton = document.getElementById('products-button');
const loadMoreProductsButton = document.getElementById('load-more-button');
const createButton = document.getElementById('create-button')

// filters
const categorySelector = document.getElementById('category-selector');
const sortingSelector = document.getElementById('sorting-selector');

/**
 * @param {HTMLElement} activeButton
 */
function updateSidebarButtons(activeButton) {
    document.querySelectorAll('.sidebar-button').forEach(btn => btn.classList.remove('active'));
    activeButton.classList.add('active');
}

/**
 * @param {string} content
 */
function viewCreateButton(content) {
    createButton.querySelector('input[type=submit]').setAttribute('value', content);

    createButton.setAttribute('action', '/admin/products/create');
    createButton.classList.remove('hidden');
}

function viewCategorySelector() {
    categorySelector.classList.remove('hidden');

    fetchCategories({container: categorySelector});
}

/**
 * @param {array} options
 */
function viewSortSelector(options) {
    // inserting options
    options.forEach(option => {
        if (typeof option === 'string') {

            let optionElement = document.createElement('option');
            optionElement.textContent = option;
            optionElement.value = option;

        } else if(option instanceof HTMLOptionElement) {
            sortingSelector.appendChild(option);
        }
    });

    // view the sort selector
    sortingSelector.classList.remove('hidden');
}

/**
 * @param {int} page
 * @param {int} limit
 * @param {HTMLElement} container
 */
function loadProducts(page, limit, container) {
    // show button
    loadMoreProductsButton.classList.remove('hidden')

    // if fetching the first page, then empty the container
    if(page === 1) {
        container.innerHTML = '';
    }

    fetch(`/admin/products?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            //validate that there is data to show
            if (data.length === 0) {
                console.log('no more products to fetch');
                loadMoreProductsButton.classList.add('hidden');
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
                        container.appendChild(productCard);
                    })
                })
        })
}

sidebarProductsButton.addEventListener('click', () => {
    // highlight the clicked button
    updateSidebarButtons(sidebarProductsButton);

    // activate filters
    viewSortSelector(['Recommended', 'Lowest Price', 'Highest Price']);
    viewCategorySelector();
    viewCreateButton('Create Product');

    loadProducts(page, productPerPage, productsContainer);
});

loadMoreProductsButton.addEventListener('click', () => {
    page++;
    loadProducts(page, productPerPage, productsContainer);
})
//TODO: categories button
