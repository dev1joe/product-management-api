import {fetchProducts, getProductCard, fillCategoriesAsSelectorOptions} from "./products-helper.js";

//______________DEFINITIONS

const fetchAllProductsRoute = '/api/products';
const fetchAllCategoriesRoute = '/api/categories';
const productCardRoute = '/resources/views/elements/adminProductCard.twig';
const productCard = await getProductCard(productCardRoute);
const productsContainer = document.getElementById('products-container');
const showMoreProductsButton = document.getElementById('show-more-button');

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
let categories;
let response = await fetch(fetchAllCategoriesRoute);
if(! response.ok) {
    console.error('error fetching categories');
} else {
    categories = await response.json();
}
// console.log(categories);

// fill category selector with categories
const filtersCategorySelector = document.getElementById('filters-category-selector');
fillCategoriesAsSelectorOptions(filtersCategorySelector, categories);

// fetch the create product form and inject it in the popup window
// also make the form functional
const popupWindow = document.getElementById('popup');
fetch('/resources/views/elements/createProductForm.html')
    .then(response => response.text())
    .then(html => {
        popupWindow.querySelector('#popup-content').innerHTML = html;
        popupWindow.querySelector('#close-button').addEventListener('click', () => {
            popupWindow.classList.add('hidden');
        });
        fillCategoriesAsSelectorOptions(popupWindow.querySelector('#category-selector'), categories);
    });

//______________RUN

function run() {
    console.log('running......')

    fetchProducts({
        route: fetchAllProductsRoute,
        filters: filters,
        card: productCard,
        container: productsContainer,
        showMoreButton: showMoreProductsButton
    });
}
run();

//______________EVENT LISTENERS

//TODO: handle edit and delete buttons
// querySelectorAll or event.target.closest ??

// add event listener to show-more button
showMoreProductsButton.addEventListener('click', function() {
    filters.page++;
    run();
});

// filter by category functionality
filtersCategorySelector.addEventListener('change', function()  {
    filters.category = this.value;
    filters.page = 1;
    run();
});


// sort selector: already populated, make it functional then
const filtersSortSelector = document.getElementById('filters-sorting-selector');
filtersSortSelector.addEventListener('change', function() {
    filters.orderBy = this.options[this.selectedIndex].getAttribute('title');
    filters.orderDir = this.value;
    run();
})

// add event listener to create button
const createProductButton = document.getElementById('create-product-button');
createProductButton.addEventListener('click', () => {
    popupWindow.classList.remove('hidden');
});