// category fields
const categorySelector = document.getElementById('category-selector');
// const newCategoryField = document.getElementById('new-category-field');

const categoryContainer = document.getElementById('create-category-field');
const newCategoryField = categoryContainer.querySelector('input');
const categoryErrorField = categoryContainer.querySelector('div');

const createCategoryButton = document.getElementById('create-category-button');

const manufacturerSelector = document.getElementById('manufacturer-selector');
const newManufacturerField = document.getElementById('new-manufacturer-field');
const createManufacturerButton = document.getElementById('create-manufacturer-button');

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

            // reset container
            const tmpOption = document.createElement('option');
            tmpOption.textContent = `-- select ${entityName} --`;
            tmpOption.setAttribute('selected', '');
            tmpOption.setAttribute('disabled', '');

            container.innerHTML = '';
            container.appendChild(tmpOption);

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