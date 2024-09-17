import {
    clearQueryParams,
    fetchData,
    fetchHtml,
    fillEntitiesAsSelectorOptions,
} from "./helperFunctions.js";

import {
    fetchProducts,
} from "./products-helper.js";

//______________DEFINITIONS
const productsApi = '/api/products';
const productCard = await fetchHtml('/resources/views/elements/customerProductCard.html');
const productsContainer = document.getElementById('products-container');

const filtersSortSelector = document.getElementById('filters-sorting-selector');
const filtersCategorySelector = document.getElementById('filters-category-selector');
const showMoreProductsButton = document.getElementById('show-more-button');

let filters = {
    page: 1,
    limit: 10,
    category: null,
    orderBy: null,
    orderDir: null,
}

// TODO: handle other filtering elements (price range selector for example)

//______________HELPER LOGIC
// fetching and caching categories
const categoriesApi = '/api/categories';
const categories = await fetchData(categoriesApi, 'json')

// filling category selector
fillEntitiesAsSelectorOptions(filtersCategorySelector, categories);

// activate category selector
filtersCategorySelector.addEventListener('change', function () {
    console.log('category selector changed, fetching new results.....')
    filters.category = this.value;
    filters.page = 1;
    runPagination();
});

showMoreProductsButton.addEventListener('click', function() {
    filters.page++;
    runPagination();
})

//______________RUN
// sort selector: already populated, make it functional then
filtersSortSelector.addEventListener('change', function () {
    filters.orderBy = this.options[this.selectedIndex].getAttribute('data-name');
    filters.orderDir = this.options[this.selectedIndex].getAttribute('data-dir');
    filters.page = 1;
    runPagination();
});

function runPagination() {
    console.log('running......')

    fetchProducts({
        route: productsApi,
        filters: filters,
        card: productCard,
        container: productsContainer,
        showMoreButton: showMoreProductsButton
    });
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
    // TODO: fetch (best selling / newly added / best offers) instead of fetching all
    resetPage();
}