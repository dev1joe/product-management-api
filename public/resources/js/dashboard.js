import {fetchCategories, loadProductCards} from "./helperFunctions.js";
import {createChart} from "./chart.js";

// getting container elements
const container = document.getElementById('container');
const popup = document.getElementById('popup');

// pagination settings
let page = 1;
const productPerPage = 10;
const contentsContainer = document.getElementById('content');

// buttons
const sidebarDashboardButton = document.getElementById('dashboard-button');
const sidebarProductsButton = document.getElementById('products-button');
const loadMoreProductsButton = document.getElementById('load-more-button');
const createButton = document.getElementById('create-button')

// filters
const categorySelector = document.getElementById('filters-category-selector');
const sortingSelector = document.getElementById('filters-sorting-selector');

function newFiltersObject() {
    return {
        'page': page,
        'category': null,
        'orderBy': null,
    }
}

let filters = newFiltersObject();


function applyFilters(url, filtersObject) {
    const urlObject = new URL(url);

    for(const key in filtersObject) {
        if(filtersObject[key]) {
            urlObject.searchParams.set(key, filtersObject[key]);
        }
    }
}

/**
 * @param {HTMLElement} activeButton
 */
function updateSidebarButtons(activeButton) {
    document.querySelectorAll('.sidebar-button').forEach(btn => btn.classList.remove('active'));
    activeButton.classList.add('active');
}

function toggleBlur() {
    //TODO: try to pass the element instance through providing the html element by declaring the onclick attribute
}

/**
 * @param {HTMLElement} button
 */
function refresh(button) {
    // reset page var to 1
    page = 1;

    // figure out what are the entities loaded and load them again
    // pass a function to call and expect data from
    // or maybe click the button to reload data
    button.click();
}

/**
 * @param {string} content
 * @param {string} route to fetch the form that will appear when the button is clicked
 * @param {HTMLElement} button the button that will be responsible for refreshing the page
 */
function viewCreateButton(content, route, button) {
    createButton.textContent = content;
    createButton.classList.remove('hidden');

    createButton.addEventListener('click', () => {
        // show blur effect
        container.classList.add('blurry');

        // show the popup window
        fetch(route)
            .then(response => response.text())
            .then(html => {
                popup.querySelector('div.popup-content').innerHTML = html;
                fetchCategories({container: popup.querySelector('#category-selector')})
            })
            .catch(error => {
                console.error(error);
            })

        popup.classList.remove('hidden');

        // activate popup button functionality
        popup.querySelector('button.close-button').addEventListener('click', () => {
            container.classList.remove('blurry');
            popup.classList.add('hidden');
            refresh(button);
        });
    })


    // createButton.querySelector('input[type=submit]').setAttribute('value', content);
    // createButton.setAttribute('action', '/admin/products/create');
}

function viewCategorySelector() {
    categorySelector.classList.remove('hidden');

    fetchCategories({container: categorySelector});

    categorySelector.addEventListener('change', () => {
        filters.category = categorySelector.value;
        applyFilters('')
    })
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

sidebarProductsButton.addEventListener('click', () => {
    // highlight the clicked button
    updateSidebarButtons(sidebarProductsButton);

    // activate filters
    viewSortSelector(['Recommended', 'Lowest Price', 'Highest Price']);
    viewCategorySelector();
    viewCreateButton('Create Product', '/resources/views/elements/createProductForm.html', sidebarProductsButton);

    loadProductCards(page, productPerPage, contentsContainer, loadMoreProductsButton);
});

loadMoreProductsButton.addEventListener('click', () => {
    page++;
    loadProductCards(page, productPerPage, contentsContainer, loadMoreProductsButton);
});

// charts: fixed, data: fixed, container: dynamic
// modify container to flex, set direction to column, and justify space between
// append the two charts as children to the container.
// and that's it
/**
 * @param {HTMLElement} container
 */
function loadAnalytics(container) {
    // getting the container ready
    const newContainer = document.createElement('div');
    newContainer.style.display = 'grid';
    newContainer.style.gridTemplateColumns = 'repeat(2, 1fr)';
    newContainer.style.gridColumnGap = '20px';
    newContainer.style.gridAutoFlow = 'row';
    newContainer.style.gridAutoRows = 'fit-content';
    newContainer.style.gridColumn = '1 / span all'
    newContainer.style.gridRow = '1 / span all'
    newContainer.style.height = '100%';
    // container.style.justifyContent = 'space-between';

    // appending charts
    newContainer.appendChild(createChart('bar'));
    newContainer.appendChild(createChart('line'));

    // append new container
    container.appendChild(newContainer);
}

sidebarDashboardButton.addEventListener('click', () => {
    // highlight the clicked button
    updateSidebarButtons(sidebarDashboardButton);

    loadAnalytics(contentsContainer);
});

//TODO: categories button and it's associated functions

window.onload = () => {
    sidebarDashboardButton.click();
}