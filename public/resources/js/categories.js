import {fetchData, fetchHtml} from "./helperFunctions.js";

const categoriesApi = '/api/categories';
const categoryCardApi = '/resources/views/elements/categoryCard.html';

//______________HELPER LOGIC

// fetch and cache categories
let categories = await fetchData(categoriesApi, 'json');

// fetch and cache category hard
let categoryCard = await fetchHtml(categoryCardApi);

//______________RUN
document.getElementById('categories-button').classList.add('active');

const container = document.getElementById('categories-container');

categories.forEach(c => {
    let card = categoryCard.cloneNode(true);
    card.querySelector('.category-img').setAttribute('src', '/storage/categories/1280x720.svg');
    card.querySelector('.category-name').textContent = c.name;
    card.querySelector('.number').textContent = c.productCount;

    container.appendChild(card);
});