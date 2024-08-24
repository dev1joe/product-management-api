import {fetchEntities} from "./helperFunctions.js";

// category elements
const categorySelector = document.getElementById('category-selector');

const categoryContainer = document.getElementById('create-category-field');

const newCategoryField = categoryContainer.querySelector('input');
const categoryErrorField = categoryContainer.querySelector('div');

const createCategoryButton = document.getElementById('create-category-button');

// manufacturer elements //TODO: complete manufacturer fields like category elements
const manufacturerSelector = document.getElementById('manufacturer-selector');
const newManufacturerField = document.getElementById('new-manufacturer-field');
const createManufacturerButton = document.getElementById('create-manufacturer-button');

//TODO: add fetchManufacturers function

//TODO: continue this function
/**
 * @param {string} name
 */
function createCategory(name) {
    categoryErrorField.innerHTML = '';

    fetch('/api/categories', {
        method: 'POST',
        header: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({'name': name}),
    }).then(response => {
        if(response.ok) {
            console.log('request accepted');

            fetchCategories();
        } else if(response.status === 400) {
            console.log('validation error');

            (response.json())
                .then(errors => {
                    console.log(errors);
                    categoryErrorField.innerHTML = errors['name'];
                })
        }
    })
}

window.onload = function() {
    fetchCategories();
    //TODO: add all functions that you need to run when page loads
}

createCategoryButton.addEventListener('click', () => {
    console.log('submitting new category....');
    console.log(newCategoryField.value);
    createCategory(newCategoryField.value);
})