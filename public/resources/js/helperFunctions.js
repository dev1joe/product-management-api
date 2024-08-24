/**
 * @param {string} route
 * @param {HTMLElement} container
 * @param {string} entityName
 */
export function fetchEntities(route, container, entityName) {
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

/**
 * @param {string} route
 * @param {HTMLElement} container
 * @param {string} entityName
 */
export function fetchCategories({route = '/admin/categories', container = categorySelector, entityName = 'category'} = {}) {
    fetchEntities(route, container, entityName);
}