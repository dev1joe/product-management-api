import {
    clearQueryParams,
    fetchData,
    fetchHtml,
    fillEntitiesAsSelectorOptions,
} from "./helperFunctions.js";

import {
    displayProducts,
    fetchProducts,
} from "./productsHelper.js";

//______________DEFINITIONS
const productsApi = '/api/products';
const productCard = await fetchHtml('/resources/views/elements/customerProductCard.html');
const productsContainer = document.getElementById('products-container');

const filtersSortSelector = document.getElementById('filters-sorting-selector');
const filtersCategorySelector = document.getElementById('filters-category-selector');
const filtersManufacturerSelector = document.getElementById('manufacturer-selector');

const filtersMinPrice = document.getElementById('filters-min-price');
const filtersMaxPrice = document.getElementById('filters-max-price');

const showMoreProductsButton = document.getElementById('show-more-button');

let filters = {
    page: 1,
    limit: 10,
    category: null,
    manufacturer: null,
    orderBy: null,
    orderDir: null,
}

//______________CACHE
let categories;
let manufacturers;

//______________RUN

//______________Filtering Sorting Selector Component
// it is very important, fetching the main data (products) depend on it.
// that's why it's handled before the other filtering components
// it's already populated, make it functional then
filtersSortSelector.addEventListener('change', function () {
    filters.orderBy = this.options[this.selectedIndex].getAttribute('data-name');
    filters.orderDir = this.options[this.selectedIndex].getAttribute('data-dir');
    filters.page = 1;
    runPagination();
});

async function runPagination() {
    console.log('running......')

    if(filters.page === 1) {
        productsContainer.innerHTML = '';
    }
    try {
        let data = await fetchProducts(productsApi, filters, showMoreProductsButton);

        let fetchedProducts = data['products'];
        let metadata = data['metadata'];

        displayProducts(fetchedProducts, productCard, productsContainer);
    } catch (error) {
        console.error(error);
    }
}

export function resetPage() {
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

// window.onload was not working so I deleted it

const queryParams = new URLSearchParams(window.location.search);

if(queryParams.size > 0) {
    console.log('found query params');
    //  if there is a query: extract query, execute query, remove query from url.
    if(queryParams.has('category')){
        console.log('found category');
        filtersCategorySelector.value = queryParams.get('category');
        const changeEvent = new Event('change');
        filtersCategorySelector.dispatchEvent(changeEvent);
    }

    clearQueryParams();
} else {
    // this will be most used with customer pagination
    // TODO: fetch (best selling / newly added / best offers) instead of fetching all
    resetPage();
}


// TODO: handle other filtering elements (price range selector for example)
//______________ACTIVATE FILTERING

//______________Filters Category Selector Component
// if(filtersCategorySelector) {
//     // activate category selector
//     filtersCategorySelector.addEventListener('change', function () {
//         console.log('category selector changed, fetching new results.....')
//         filters.category = this.value;
//         filters.page = 1;
//         runPagination();
//     });
// } else {
//     console.error('category selector not found!');
// }

//______________Filters Manufacturer Selector Component
if(filtersManufacturerSelector) {
    const manufacturersApi = '/api/manufacturers/names';
    manufacturers = await fetchData(manufacturersApi, 'json')

    fillEntitiesAsSelectorOptions(filtersManufacturerSelector, manufacturers);

    filtersManufacturerSelector.addEventListener('change', function () {
        console.log('manufacturer selector changed, fetching new results.....')
        filters.manufacturer = this.value;
        filters.page = 1;
        runPagination();
    });
} else {
    console.error('manufacturer selector not found!');
}

//______________Show More Button Component
showMoreProductsButton.addEventListener('click', function() {
    filters.page++;
    runPagination();
});

// price filtering
// first I need to fetch metadata, or compute metadata !