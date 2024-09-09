import {
    fetchData,
    fetchHtml,
    applyFilters,
    resetForm,
    injectImageInputInContainer,
    asynchronousFormSubmission,
    findEntityById,
} from "./helperFunctions.js";

//______________DEFINITIONS
const categoriesApi = '/api/categories';
const categoryCardApi = '/resources/views/elements/categoryCard.html';
const categoriesContainer = document.getElementById('categories-container');
const popupWindow = document.getElementById('popup-window');

// fetch and cache category hard
let categoryCard = await fetchHtml(categoryCardApi);
let categories;

let filters = {
    orderBy: null,
    orderDir: null,
}

//______________HELPER LOGIC

//______________RUN
async function run() {
document.getElementById('categories-button').classList.add('active');
    // apply filters
    let url = applyFilters(filters, categoriesApi);

    // fetch and cache categories
    categories = await fetchData(url, 'json');

    // clear previous categories
    categoriesContainer.innerHTML = '';

    // inject category data in the card
    categories.forEach(c => {
        let card = categoryCard.cloneNode(true);

        /** @var {string} imageSource*/
        const imageSource = (c.image)? c.image : '/storage/categories/1280x720.svg';

        card.querySelector('.category-img').setAttribute('src', imageSource);
        card.querySelector('.category-name').textContent = c.name;
        card.querySelector('.number').textContent = c.productCount;
        card.querySelector('#edit-button').setAttribute('data-id', c.id);
        card.querySelector('#delete-button').setAttribute('data-id', c.id);

        categoriesContainer.appendChild(card);
    });
}

const filtersSortingSelector = document.getElementById('filters-sorting-selector');
filtersSortingSelector.addEventListener('change', function() {
    filters.orderDir = this.options[this.selectedIndex].getAttribute('data-dir');
    filters.orderBy = this.options[this.selectedIndex].getAttribute('data-name');
    run();
})
function resetPage() {
    popupWindow.classList.add('hidden');

    filtersSortingSelector.value = 'last-updated';
    const changeEvent = new Event('change');
    filtersSortingSelector.dispatchEvent(changeEvent);
}

resetPage();

//______________EVENT LISTENERS
// fetch and inject "create category" form
// await fetchHtml(
//     '/resources/views/elements/createCategoryForm.html',
//     popupWindow.querySelector('div#popup-content')
// );

const createCategoryForm = popupWindow.querySelector('form#create-category-form');
asynchronousFormSubmission(createCategoryForm, resetPage);
const imageContainer = popupWindow.querySelector('div.image-container');

// create category button
const createCategoryButton = document.getElementById('create-category-button');
createCategoryButton.addEventListener('click', function() {
    createCategoryForm.action = categoriesApi;
    popupWindow.classList.remove('hidden');
});

// close popup window button
popupWindow.querySelector('#close-button').addEventListener('click', () => {
    try {
    resetForm(createCategoryForm, imageContainer);
    } catch(e) {
        console.error(e);
    }
    popupWindow.classList.add('hidden');
});

// create product form: show image if a one is chosen
const imageInput = createCategoryForm.querySelector('input[type=file]');

if(imageInput && imageContainer) {
    injectImageInputInContainer(imageInput, imageContainer);
}

// edit & delete buttons
categoriesContainer.addEventListener('click', function(event) {
    const editButton = event.target.closest('#edit-button');
    const deleteButton = event.target.closest('#delete-button');

    if(editButton) {
        //TODO: handle edit button
        const id = parseInt(editButton.getAttribute('data-id'));
        // console.log(`searching for category ${id}`);

        const currentCategory = findEntityById(id, categories);
        //console.log(currentCategory);
        injectCategoryIntoForm(currentCategory, createCategoryForm, categoriesApi);
        popupWindow.classList.remove('hidden');
    } else if(deleteButton) {
        //TODO: handle delete restrictions (view it in the frontend)
        if(confirm('delete category, are you sure ?')) {
            let id = deleteButton.getAttribute('data-id');
            let url = `${categoriesApi}/${id}`;

            fetch(url, {
                method: 'DELETE',
            })
                .then(response => {
                    console.log(response);

                    if(! response.ok) {
                        console.error('category deletion error');
                        console.error(response.statusText);
                    } else {
                        console.log('category deleted successfully');
                        resetPage();
                    }
                })
                .catch(error => console.log(error));
        }
    }
})

function injectCategoryIntoForm(category, form, categoriesApi) {
    form.querySelector('#name').value = category.name;

    if(category.image) {
        const imagePreview = form.querySelector('#image');

        imagePreview.style.backgroundImage = `url(\'${category.image}\')`;
        imagePreview.classList.remove('hidden');
    }

    form.action = `${categoriesApi}/${category.id}`;
}