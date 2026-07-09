<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .page-header {
            border-bottom: 3px solid #0d6efd;
            padding-bottom: 0.75rem;
            margin-bottom: 1.5rem;
        }

        #alert-container {
            min-height: 3rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="page-header">Product Inventory</h1>

        <div id="alert-container"></div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Add Product</h5>
            </div>
            <div class="card-body">
                <form id="product-form" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="product_name" class="form-label">Product name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="quantity_in_stock" class="form-label">Quantity in stock</label>
                            <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" min="0" step="1" required>
                        </div>
                        <div class="col-md-4">
                            <label for="price_per_item" class="form-label">Price per item</label>
                            <input type="number" class="form-control" id="price_per_item" name="price_per_item" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Submitted Products</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Product name</th>
                                <th>Quantity in stock</th>
                                <th>Price per item</th>
                                <th>Datetime submitted</th>
                                <th>Total value number</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="products-table-body">
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No products submitted yet.</td>
                            </tr>
                        </tbody>
                        <tfoot id="products-table-foot" class="table-group-divider" style="display: none;">
                            <tr class="table-warning fw-bold">
                                <td colspan="4" class="text-end">Sum Total:</td>
                                <td id="sum-total-value">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="edit-modal-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="edit-form">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit-modal-label">Edit Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="edit_id" name="id">
                        <div class="mb-3">
                            <label for="edit_product_name" class="form-label">Product name</label>
                            <input type="text" class="form-control" id="edit_product_name" name="product_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_quantity_in_stock" class="form-label">Quantity in stock</label>
                            <input type="number" class="form-control" id="edit_quantity_in_stock" name="quantity_in_stock" min="0" step="1" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price_per_item" class="form-label">Price per item</label>
                            <input type="number" class="form-control" id="edit_price_per_item" name="price_per_item" min="0" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/products.js') }}"></script>
</body>
</html>
