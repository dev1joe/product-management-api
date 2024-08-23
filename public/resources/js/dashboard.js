let page = 1;
const productPerPage = 10;
const productsContainer = document.getElementById('content');

const sidebarProductsButton = document.getElementById('products-button');
const loadMoreProductsButton = document.getElementById('load-more-button');

/**
 * @param {HTMLElement} activeButton
 */
function updateSidebarButtons(activeButton) {
    document.querySelectorAll('.sidebar-button').forEach(btn => btn.classList.remove('active'));
    activeButton.classList.add('active');
}

/**
 * @param {HTMLElement} buttonId
 * @param {int} page
 * @param {int} limit
 * @param {HTMLElement} container
 */
function loadProducts(buttonId, page, limit, container) {
    // highlight the clicked button
    updateSidebarButtons(buttonId);

    // show button
    loadMoreProductsButton.classList.remove('hidden')

    // if fetching the first page, then empty the container
    if(page === 1) {
        container.innerHTML = '';
    }

    fetch(`/admin/products?page=${page}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            //validate that there is data to show
            if (data.length === 0) {
                console.log('no more products to fetch');
                loadMoreProductsButton.classList.add('hidden');
                return;
            }

            fetch('/resources/views/elements/adminProductCard.twig')
                .then(response => response.text())
                .then(productCardHtml => {
                    data.forEach(product => {
                        // convert html to node
                        const tmpDiv = document.createElement('div');
                        tmpDiv.innerHTML = productCardHtml;
                        const productCard = tmpDiv.firstChild;

                        // add data to node
                        productCard.querySelector('.product-img').setAttribute('src', product.photo);
                        productCard.querySelector('.product-name').textContent = product.name;
                        productCard.querySelector('button').setAttribute('data-id', product.id)

                        // append to the container
                        container.appendChild(productCard);
                    })
                })
        })
}

sidebarProductsButton.addEventListener('click', () => {
    loadProducts(sidebarProductsButton, page, productPerPage, productsContainer);
});

loadMoreProductsButton.addEventListener('click', () => {
    page++;
    loadProducts(sidebarProductsButton, page, productPerPage, productsContainer);
})
//TODO: categories button
