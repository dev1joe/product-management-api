import {fetchData, fetchHtml, applyFilters} from "./helperFunctions.js";

//______________DEFINITIONS
const categoriesApi = '/api/categories';
const categoryCardApi = '/resources/views/elements/categoryCard.html';
const categoriesContainer = document.getElementById('categories-container');
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

    categories.forEach(c => {
        let card = categoryCard.cloneNode(true);
        card.querySelector('.category-img').setAttribute('src', '/storage/categories/1280x720.svg');
        card.querySelector('.category-name').textContent = c.name;
        card.querySelector('.number').textContent = c.productCount;
        card.querySelector('#edit-button').setAttribute('data-id', c.id);
        card.querySelector('#delete-button').setAttribute('data-id', c.id);

        categoriesContainer.appendChild(card);
    });
}

run();

//______________EVENT LISTENERS
categoriesContainer.addEventListener('click', function(event) {
    const editButton = event.target.closest('#edit-button');
    const deleteButton = event.target.closest('#delete-button');

    if(editButton) {
        //TODO: handle edit button
    } else if(deleteButton) {
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
                    }
                })
                .catch(error => console.log(error));
        }
    }
})

const filtersSortingSelector = document.getElementById('filters-sorting-selector');
filtersSortingSelector.addEventListener('change', function() {
    filters.orderDir = this.value;
    filters.orderBy = this.options[this.selectedIndex].getAttribute('title');
    run();
})