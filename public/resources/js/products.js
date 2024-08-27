// fetch categories
// cache them (save them in an array for now)
let categories;
let response = await fetch('/api/categories');
if(! response.ok) {
    console.error('error fetching categories');
} else {
    categories = await response.json();
}

console.log(categories);

/**
 * @param {HTMLSelectElement} container
 */
function fillCategoriesAsSelectorOptions(container) {
    categories.forEach(cat => {
        const optionElement = document.createElement('option');
        optionElement.textContent = cat.name;
        optionElement.value = cat.id;

        container.appendChild(optionElement);
    });
}

// populate categories selector
const filtersCategorySelector = document.getElementById('filters-category-selector');
fillCategoriesAsSelectorOptions(filtersCategorySelector);


// fetch the create product form and persist it in the popup window
// also make the form functional
const popupWindow = document.getElementById('popup');
fetch('/resources/views/elements/createProductForm.html')
    .then(response => response.text())
    .then(html => {
        popupWindow.querySelector('#popup-content').innerHTML = html;
        popupWindow.querySelector('#close-button').addEventListener('click', () => {
            popupWindow.classList.add('hidden');
        });
        fillCategoriesAsSelectorOptions(popupWindow.querySelector('#category-selector'));
    });

// add event listener to create button
const createProductButton = document.getElementById('create-product-button');
createProductButton.addEventListener('click', () => {
    popupWindow.classList.remove('hidden');
});

//TODO: continue

// define some variables for pagination

// fetch products and populate content div
const productsContainer = document.getElementById('products-container');

response = await fetch('/resources/views/elements/adminProductCard.twig');
const productCardHtml = await response.text();

const tmpDiv = document.createElement('div');
tmpDiv.innerHTML = productCardHtml;

let productCard = tmpDiv.firstChild;

fetch('/api/products')
    .then(response => response.json())
    .then(data => {
        console.log(data);
        data.forEach(product => {
            // add data to node
            let cardCopy = productCard.cloneNode(true);
            cardCopy.querySelector('.product-img').setAttribute('src', product.photo);
            cardCopy.querySelector('.product-name').textContent = product.name;
            cardCopy.querySelector('button').setAttribute('data-id', product.id)

            // append to the container
            productsContainer.appendChild(cardCopy);
        })
    })
