import {fetchCategories, loadProductCards, createChart} from "./helperFunctions.js";

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
const categorySelector = document.getElementById('category-selector');
const sortingSelector = document.getElementById('sorting-selector');

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
 * @param {string} content
 */
function viewCreateButton(content) {
    createButton.textContent = content;
    createButton.classList.remove('hidden');

    createButton.addEventListener('click', () => {
        // show blur effect
        container.classList.add('blurry');

        // show popup window
        fetch('/resources/views/elements/createProductForm.html')
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
        });
    })


    // createButton.querySelector('input[type=submit]').setAttribute('value', content);
    // createButton.setAttribute('action', '/admin/products/create');
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

sidebarProductsButton.addEventListener('click', () => {
    // highlight the clicked button
    updateSidebarButtons(sidebarProductsButton);

    // activate filters
    viewSortSelector(['Recommended', 'Lowest Price', 'Highest Price']);
    viewCategorySelector();
    viewCreateButton('Create Product');

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