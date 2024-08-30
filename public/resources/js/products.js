import {
    fetchProducts,
    getProductCard,
    fillCategoriesAsSelectorOptions,
    injectProductIntoForm, resetForm, asynchronousFormSubmission
} from "./products-helper.js";

//______________DEFINITIONS

const productsApi = '/api/products';
const categoriesApi = '/api/categories';
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
let response = await fetch(categoriesApi);
if(! response.ok) {
    console.error('error fetching categories');
} else {
    categories = await response.json();
}
// console.log(categories);

// fill category selector with categories
const filtersCategorySelector = document.getElementById('filters-category-selector');
fillCategoriesAsSelectorOptions(filtersCategorySelector, categories);

// activate create product form
const popupWindow = document.getElementById('popup-window');
fetch('/resources/views/elements/createProductForm.html')
    .then(response => response.text())
    .then(html => {
        popupWindow.querySelector('#popup-content').innerHTML = html;
        popupWindow.querySelector('#close-button').addEventListener('click', () => {
            const form = popupWindow.querySelector('#create-product-form');
            resetForm(form);
            popupWindow.classList.add('hidden');
        })
        //TODO: activate create category button

        fillCategoriesAsSelectorOptions(popupWindow.querySelector('#category-selector'), categories);
    });

// // activate update product form
// const filledPopupWindow = document.getElementById('filled-popup-window');
// fetch('/resources/views/elements/updateProductForm.html')
//     .then(response => response.text())
//     .then(html => {
//        filledPopupWindow.querySelector('#filled-popup-content').innerHTML = html;
//         fillCategoriesAsSelectorOptions(filledPopupWindow.querySelector('#updated-category-selector'), categories);
//     });
//
// // activate close button
// popupWindow.querySelector('#close-button').addEventListener('click', () => {
//     popupWindow.classList.add('hidden');
// });

document.querySelectorAll('.close-button').forEach(button => {
    button.addEventListener('click', () => {
        button.parentElement.classList.add('hidden');
    })
})

//______________RUN

function run() {
    console.log('running......')
    document.getElementById('products-button').classList.add('active');

    fetchProducts({
        route: productsApi,
        filters: filters,
        card: productCard,
        container: productsContainer,
        showMoreButton: showMoreProductsButton
    });
}
run();

//______________EVENT LISTENERS

// handling edit and delete buttons
// will go with the event.target.closest approach for better performance
productsContainer.addEventListener('click', function(event)  {
   const editButton = event.target.closest('#edit-button');
   const deleteButton = event.target.closest('#delete-button');

   if(editButton) {
       //TODO: handle click!
       const productId = parseInt(editButton.getAttribute('data-id'));

       fetch(`${productsApi}/${productId}`)
           .then(response => response.json())
           .then(product => {
               product = product[0];
               console.log(product);

               const form = popupWindow.querySelector('#create-product-form');
               if(! form) {
                   console.error('form not found!');
                   return;
               }

               injectProductIntoForm(product, form, productsApi);
               asynchronousFormSubmission(form);
               popupWindow.classList.remove('hidden');
           });

   } else if(deleteButton) {
       if(confirm('are you sure you want to delete this product ??')) {
           console.log('admin wants to delete a product :(');

           const productId = parseInt(deleteButton.getAttribute('data-id'));

           const url = `${productsApi}/${productId}`;
           fetch(url, {
               method: 'DELETE',
           })
               .then(response =>  {
                   if(response.ok) {
                       console.log('product deleted successfully');
                       window.location.reload(); //TODO: remove reload
                   }
               })
               .catch((error) => console.error(error));
       }
   }
});

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
    filters.page = 1;
    run();
})

// add event listener to create button
const createProductButton = document.getElementById('create-product-button');
createProductButton.addEventListener('click', () => {
    popupWindow.classList.remove('hidden');
    const form = popupWindow.querySelector('#create-product-form');
    form.setAttribute('action', productsApi);
    asynchronousFormSubmission(form);
});