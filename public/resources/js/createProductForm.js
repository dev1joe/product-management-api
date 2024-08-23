
const categorySelector = document.getElementById('category-selector');
const newCategoryField = document.getElementById('new-category-field');
const manufacturerSelector = document.getElementById('manufacturer-selector');
const newManufacturerField = document.getElementById('new-manufacturer-field');

/**
 * @param {string} route
 * @param {HTMLElement} container
 * @param {string} entityName
 */
function fetchEntities(route, container, entityName) {
    fetch(route)
        .then(response => response.json())
        .then(data => {

            // make sure categories are found
            if(data.length === 0) {
                console.log(`no ${entityName}(s) found`);
                return;
            }

            data.forEach(category => {
                const optionElement = document.createElement('option');
                optionElement.textContent = category.name;
                optionElement.setAttribute('value', category.id);

                container.appendChild(optionElement);
            })
        })
}

function fetchCategories() {
    fetchEntities('/admin/categories', categorySelector, 'category');
}

//TODO: add fetchManufacturers function

//TODO: continue this function
function createCategory() {
    const categoryName = newCategoryField.value;

    fetch('/admin/categories', {
        method: 'POST',
        body: {'name': categoryName},
    })
}

window.onload = function() {
    fetchCategories();
    //TODO: add all functions that you need to run when page loads
}