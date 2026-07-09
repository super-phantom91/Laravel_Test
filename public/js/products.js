document.addEventListener('DOMContentLoaded', () => {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const productForm = document.getElementById('product-form');
    const editForm = document.getElementById('edit-form');
    const tableBody = document.getElementById('products-table-body');
    const tableFoot = document.getElementById('products-table-foot');
    const sumTotalValue = document.getElementById('sum-total-value');
    const productCount = document.getElementById('product-count');
    const alertContainer = document.getElementById('alert-container');
    const editModalElement = document.getElementById('edit-modal');
    const deleteModalElement = document.getElementById('delete-modal');
    const deleteProductName = document.getElementById('delete-product-name');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    const editModal = new bootstrap.Modal(editModalElement);
    const deleteModal = new bootstrap.Modal(deleteModalElement);

    let pendingDeleteId = null;
    let alertTimeout = null;

    const routes = {
        list: '/products/list',
        store: '/products',
        update: (id) => `/products/${id}`,
        destroy: (id) => `/products/${id}`,
    };

    const formatCurrency = (value) => Number(value).toFixed(2);

    const showAlert = (message, type = 'success') => {
        if (alertTimeout) {
            clearTimeout(alertTimeout);
        }

        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';

        alertContainer.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                <i class="bi ${icon}"></i>
                <span>${message}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        alertTimeout = setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 4000);
    };

    const escapeHtml = (value) => {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const updateProductCount = (count) => {
        productCount.textContent = `${count} item${count === 1 ? '' : 's'}`;
    };

    const renderProducts = (products, sumTotal) => {
        updateProductCount(products.length);

        if (!products.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            No products submitted yet.
                        </div>
                    </td>
                </tr>
            `;
            tableFoot.style.display = 'none';
            return;
        }

        tableBody.innerHTML = products.map((product, index) => `
            <tr style="animation-delay: ${index * 0.04}s">
                <td><span class="product-name">${escapeHtml(product.product_name)}</span></td>
                <td><span class="badge-qty">${product.quantity_in_stock}</span></td>
                <td class="price-cell">$${formatCurrency(product.price_per_item)}</td>
                <td class="datetime-cell">${product.datetime_submitted}</td>
                <td class="total-cell">$${formatCurrency(product.total_value)}</td>
                <td>
                    <div class="action-btns">
                        <button
                            type="button"
                            class="btn btn-action btn-edit edit-btn"
                            title="Edit"
                            data-id="${product.id}"
                            data-product-name="${escapeHtml(product.product_name)}"
                            data-quantity="${product.quantity_in_stock}"
                            data-price="${product.price_per_item}"
                        >
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-action btn-delete delete-btn"
                            title="Delete"
                            data-id="${product.id}"
                            data-product-name="${escapeHtml(product.product_name)}"
                        >
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        sumTotalValue.textContent = `$${formatCurrency(sumTotal)}`;
        tableFoot.style.display = '';

        document.querySelectorAll('.edit-btn').forEach((button) => {
            button.addEventListener('click', () => openEditModal(button));
        });

        document.querySelectorAll('.delete-btn').forEach((button) => {
            button.addEventListener('click', () => openDeleteModal(button));
        });
    };

    const loadProducts = async () => {
        const response = await fetch(routes.list, {
            headers: { 'Accept': 'application/json' },
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

    const openDeleteModal = (button) => {
        pendingDeleteId = button.dataset.id;
        deleteProductName.textContent = button.dataset.productName;
        deleteModal.show();
    };

    const deleteProduct = async () => {
        if (!pendingDeleteId) {
            return;
        }

        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Deleting...';

        const response = await fetch(routes.destroy(pendingDeleteId), {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        const data = await response.json();

        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.innerHTML = '<i class="bi bi-trash3 me-1"></i> Delete';

        if (!response.ok) {
            showAlert(data.message || 'Delete failed.', 'danger');
            return;
        }

        deleteModal.hide();
        renderProducts(data.products, data.sum_total_value);
        showAlert('Product deleted successfully.');
        pendingDeleteId = null;
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

    confirmDeleteBtn.addEventListener('click', deleteProduct);

    deleteModalElement.addEventListener('hidden.bs.modal', () => {
        pendingDeleteId = null;
    });

    loadProducts();
});
