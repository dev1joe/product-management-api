/**
 * @param {string|URL} api
 * @param {string} format
 * @returns {Promise<string|null>}
 */
export async function fetchData(api, format) {
    let response = await fetch(api);

    if(! response.ok) {
        console.error('error fetching data');
        return null;
    } else {
        let data;

        if(format === 'text') {
            data = await response.text();
        } else {
            data = await response.json();
        }

        return data;
    }
}

export function fetchCategories(api = '/api/categories', format = 'json') {
    let categories;

    fetchData(api, format).then(data =>  {
        console.log(data);
        categories =  data;
    });

    console.log(categories);
    return categories;
}

/**
 * @param {string} api
 * @param {HTMLElement} container
 * @returns {Promise<ChildNode>}
 */
export async function fetchHtml(api, container = null) {
    let response = await fetch(api);

    let html = await response.text();
    // console.log(html);

    if(container) {
        container.innerHTML = html;
        return container.firstChild;
    }

    let tmpDiv = document.createElement('div');
    tmpDiv.innerHTML = html;
    return tmpDiv.firstChild;
}

/**
 * @param {object} filters
 * @param {string} route
 */
export function applyFilters(filters, route) {
    const url = new URL(route, window.location.origin);

    // Add all active filters to the URL as query parameters
    for (const key in filters) {
        if (filters[key]) {
            url.searchParams.set(key, filters[key]);
        }
    }

    // console.log(url);
    return url;
}


/**
 * @param {HTMLFormElement} form
 * @param {HTMLElement} imageContainer
 */
export function resetForm(form, imageContainer) {
    form.reset();

    form.setAttribute('action', '');

    const textarea = form.querySelector('textarea');
    if(textarea) {
        textarea.textContent = 'Product Details......';
    }

    imageContainer.removeAttribute('background-image');
    imageContainer.classList.add('hidden');


    const errorFields = form.querySelectorAll('div.invalid-feedback');
    errorFields.forEach(field => {
        field.textContent = '';
    })
}

/**
 * @param {HTMLInputElement} imageInput
 * @param {HTMLElement} container
 */
export function injectImageInputInContainer(imageInput, container) {
    if('hidden' in container.classList) {
        container.classList.remove('hidden');
    }

    imageInput.addEventListener('change', function(event) {
        const image = event.target.files[0];

        if(image) {
            const reader = new FileReader();
            reader.onload = function (e) {
                container.classList.remove('hidden');
                container.style.backgroundImage = `url('${e.target.result}')`;
            }
            reader.readAsDataURL(image);
        }
    });
}

/**
 * @param {HTMLFormElement} form
 * @param {Function} resetPageFunction
 */
export function asynchronousFormSubmission(form, resetPageFunction) {
    console.trace();
    form.addEventListener('submit', async function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        console.log(formData);

        // loop over form data entries
        for(let [key, value] of formData.entries()) {

            // if you find a photo key and there is no files uploaded
            if(key === 'photo') {
                const fileInput = form.querySelector('input[type=file]');

                if(! fileInput.files.length) {
                    // remove it
                    formData.delete('photo');
                }
            }
        }

        const url = this.action;
        const request = new Request(url, {
            method: 'POST',
            body: formData,
        });

        const response = await fetch(request);

        // remove past validation errors
        const validationFields = form.querySelectorAll('div.invalid-feedback');
        validationFields.forEach(field => field.textContent = '');

        if(response.ok) {
            console.log('form submitted successfully!');
            try{
                const messages = await response.json();
                console.log(messages)
            } catch(e){}

            // window.location.reload();
            resetPageFunction()

            const imageContainer = form.parentElement.parentElement.querySelector('div.image-container');
            resetForm(form, imageContainer);
        } else {
            const status = response.status;

            const responseErrors = await response.json();
            console.error(responseErrors);

            // if validation exception display it
            if(status === 400) {
                displayValidationErrors(form, responseErrors);
            }
        }

    })
}

/**
 * @param {HTMLFormElement} form
 * @param {Object|Array} errors
 */
function displayValidationErrors(form, errors) {
    console.log('displaying validation errors......')

    for(const fieldName in errors) {
        console.log(fieldName);
        const field = form.querySelector(`[name=${fieldName}]`);

        if(field) {
            const parent = field.parentElement;
            parent.querySelector('div.invalid-feedback').textContent = errors[fieldName][0];
        } else {
            console.error(`${fieldName} field not found`);
        }
    }
}

/**
 * @param {int} id
 * @param {Array<Object>} array
 * @returns {Object}
 */
export function findEntityById(id, array) {
    return array.find(category => category.id === id);
}

/**
 * @param {HTMLSelectElement} container
 * @param {Array} entities
 */
export function fillEntitiesAsSelectorOptions(container, entities) {
    entities.forEach(e => {
        const optionElement = document.createElement('option');
        optionElement.textContent = e.name;
        optionElement.value = e.id;

        container.appendChild(optionElement);
    });
}

export function clearQueryParams() {
    const url = window.location.origin + window.location.pathname;
    window.history.replaceState({}, document.title, url);
}