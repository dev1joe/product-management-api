import {fetchData} from "./helperFunctions.js";
import {createCategory, fillEntitiesAsSelectorOptions} from "./productsHelper.js";

// category elements
const categorySelector = document.getElementById('filters-category-selector');

const categoryContainer = document.getElementById('create-category-field');

const newCategoryField = categoryContainer.querySelector('input');
const categoryErrorField = categoryContainer.querySelector('div');

const createCategoryButton = document.getElementById('create-category-button');

// manufacturer elements //TODO: complete manufacturer fields like category elements
const manufacturerSelector = document.getElementById('manufacturer-selector');
const newManufacturerField = document.getElementById('new-manufacturer-field');
const createManufacturerButton = document.getElementById('create-manufacturer-button');

createCategoryButton.addEventListener('click', () => {
    console.log('submitting new category....');
    console.log(newCategoryField.value);
    createCategory(newCategoryField, categoryErrorField);
})

let categories = await fetchData('/api/categories', 'json');
fillEntitiesAsSelectorOptions(categorySelector, categories)