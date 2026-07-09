document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const productForm = document.getElementById('product-form');
    const editForm = document.getElementById('edit-form');
    const tableBody = document.getElementById('products-table-body');
    const tableFoot = document.getElementById('products-table-foot');
    const sumTotalValue = document.getElementById('sum-total-value');
    const alertContainer = document.getElementById('alert-container');
    const editModalElement = document.getElementById('edit-modal');
    const editModal = new bootstrap.Modal(editModalElement);

    const routes = {
        list: '/products/list',
        store: '/products',
        update: (id) => `/products/${id}`,
    };

    const formatCurrency = (value) => Number(value).toFixed(2);

    const showAlert = (message, type = 'success') => {
        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    };

    const escapeHtml = (value) => {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const renderProducts = (products, sumTotal) => {
        if (!products.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">No products submitted yet.</td>
                </tr>
            `;
            tableFoot.style.display = 'none';
            return;
        }

        tableBody.innerHTML = products.map((product) => `
            <tr>
                <td>${escapeHtml(product.product_name)}</td>
                <td>${product.quantity_in_stock}</td>
                <td>${formatCurrency(product.price_per_item)}</td>
                <td>${product.datetime_submitted}</td>
                <td>${formatCurrency(product.total_value)}</td>
                <td>
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary edit-btn"
                        data-id="${product.id}"
                        data-product-name="${escapeHtml(product.product_name)}"
                        data-quantity="${product.quantity_in_stock}"
                        data-price="${product.price_per_item}"
                    >
                        Edit
                    </button>
                </td>
            </tr>
        `).join('');

        sumTotalValue.textContent = formatCurrency(sumTotal);
        tableFoot.style.display = '';

        document.querySelectorAll('.edit-btn').forEach((button) => {
            button.addEventListener('click', () => openEditModal(button));
        });
    };

    const loadProducts = async () => {
        const response = await fetch(routes.list, {
            headers: {
                'Accept': 'application/json',
            },
        });

        if (!response.ok) {
            showAlert('Failed to load products.', 'danger');
            return;
        }

        const data = await response.json();
        renderProducts(data.products, data.sum_total_value);
    };

    const submitProduct = async (event, method, url, payload, successMessage, formToReset = null) => {
        event.preventDefault();

        const response = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        const data = await response.json();

        if (!response.ok) {
            const errorMessage = data.message
                || Object.values(data.errors || {}).flat().join(' ')
                || 'Request failed.';
            showAlert(errorMessage, 'danger');
            return false;
        }

        renderProducts(data.products, data.sum_total_value);
        showAlert(successMessage);

        if (formToReset) {
            formToReset.reset();
        }

        return true;
    };

    const openEditModal = (button) => {
        document.getElementById('edit_id').value = button.dataset.id;
        document.getElementById('edit_product_name').value = button.dataset.productName;
        document.getElementById('edit_quantity_in_stock').value = button.dataset.quantity;
        document.getElementById('edit_price_per_item').value = button.dataset.price;
        editModal.show();
    };

    productForm.addEventListener('submit', (event) => {
        const formData = new FormData(productForm);

        submitProduct(event, 'POST', routes.store, {
            product_name: formData.get('product_name'),
            quantity_in_stock: formData.get('quantity_in_stock'),
            price_per_item: formData.get('price_per_item'),
        }, 'Product saved successfully.', productForm);
    });

    editForm.addEventListener('submit', async (event) => {
        const id = document.getElementById('edit_id').value;
        const success = await submitProduct(event, 'PUT', routes.update(id), {
            product_name: document.getElementById('edit_product_name').value,
            quantity_in_stock: document.getElementById('edit_quantity_in_stock').value,
            price_per_item: document.getElementById('edit_price_per_item').value,
        }, 'Product updated successfully.');

        if (success) {
            editModal.hide();
        }
    });

    loadProducts();
});
