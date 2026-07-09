<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Product Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #06b6d4;
            --surface: rgba(255, 255, 255, 0.92);
            --text-muted: #64748b;
            --shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
            --shadow-hover: 0 20px 50px rgba(15, 23, 42, 0.12);
            --radius: 1rem;
            --transition: 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #eef2ff 0%, #f0fdfa 50%, #faf5ff 100%);
            min-height: 100vh;
            color: #1e293b;
        }

        .page-wrapper {
            max-width: 1100px;
            margin: 0 auto;
            padding: 2.5rem 1.25rem 3rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeSlideDown 0.6s ease-out;
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2.25rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 1.05rem;
            margin: 0;
        }

        .glass-card {
            background: var(--surface);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: box-shadow var(--transition), transform var(--transition);
            overflow: hidden;
            animation: fadeSlideUp 0.6s ease-out backwards;
        }

        .glass-card:hover {
            box-shadow: var(--shadow-hover);
        }

        .glass-card.form-card {
            animation-delay: 0.1s;
            margin-bottom: 1.75rem;
        }

        .glass-card.table-card {
            animation-delay: 0.2s;
        }

        .card-header-custom {
            padding: 1.1rem 1.5rem;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .card-header-custom .icon-badge {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .card-header-custom.form .icon-badge {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
        }

        .card-header-custom.table .icon-badge {
            background: linear-gradient(135deg, #0ea5e9, #06b6d4);
            color: #fff;
        }

        .card-header-custom h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.05rem;
        }

        .card-body-custom {
            padding: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            font-size: 0.875rem;
            color: #475569;
            margin-bottom: 0.4rem;
        }

        .form-control {
            border: 1.5px solid #e2e8f0;
            border-radius: 0.6rem;
            padding: 0.65rem 0.9rem;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            border-radius: 0.6rem;
            padding: 0.65rem 1.5rem;
            font-weight: 600;
            transition: transform var(--transition), box-shadow var(--transition);
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .products-table {
            margin: 0;
        }

        .products-table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            padding: 0.9rem 1rem;
            white-space: nowrap;
        }

        .products-table tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            transition: background var(--transition);
        }

        .products-table tbody tr {
            animation: rowFadeIn 0.35s ease-out backwards;
        }

        .products-table tbody tr:hover td {
            background: #f8fafc;
        }

        .products-table tbody tr.row-removing {
            animation: rowFadeOut 0.35s ease-in forwards;
        }

        .product-name {
            font-weight: 600;
            color: #1e293b;
        }

        .badge-qty {
            background: #eef2ff;
            color: var(--primary-dark);
            font-weight: 600;
            padding: 0.3rem 0.65rem;
            border-radius: 2rem;
            font-size: 0.85rem;
        }

        .price-cell, .total-cell {
            font-variant-numeric: tabular-nums;
            font-weight: 500;
        }

        .total-cell {
            color: var(--primary-dark);
            font-weight: 600;
        }

        .datetime-cell {
            font-size: 0.875rem;
            color: var(--text-muted);
        }

        .action-btns {
            display: flex;
            gap: 0.4rem;
            flex-wrap: nowrap;
        }

        .btn-action {
            width: 2.1rem;
            height: 2.1rem;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            border: 1.5px solid transparent;
            transition: all var(--transition);
            font-size: 0.9rem;
        }

        .btn-edit {
            background: #eef2ff;
            color: var(--primary);
            border-color: #c7d2fe;
        }

        .btn-edit:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .btn-delete {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-delete:hover {
            background: #e11d48;
            color: #fff;
            border-color: #e11d48;
            transform: translateY(-1px);
        }

        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 2.5rem;
            opacity: 0.4;
            display: block;
            margin-bottom: 0.75rem;
        }

        .sum-row td {
            background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
            font-weight: 700;
            font-size: 0.95rem;
            border-top: 2px solid #fbbf24 !important;
            padding: 1rem !important;
        }

        .sum-label {
            text-align: right;
            color: #92400e;
        }

        .sum-value {
            color: #b45309;
            font-variant-numeric: tabular-nums;
            font-size: 1.05rem;
        }

        #alert-container {
            position: fixed;
            top: 1.25rem;
            right: 1.25rem;
            z-index: 1080;
            max-width: 380px;
            width: calc(100% - 2.5rem);
        }

        #alert-container .alert {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.12);
            animation: toastIn 0.4s ease-out;
            font-size: 0.9rem;
        }

        .modal-content {
            border: none;
            border-radius: var(--radius);
            box-shadow: 0 25px 60px rgba(15, 23, 42, 0.18);
            overflow: hidden;
        }

        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
        }

        .modal-header.edit-header {
            background: linear-gradient(135deg, #eef2ff, #f0fdfa);
        }

        .modal-header.delete-header {
            background: linear-gradient(135deg, #fff1f2, #fef2f2);
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1rem 1.5rem;
        }

        .btn-modal-save {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border: none;
            font-weight: 600;
            border-radius: 0.5rem;
        }

        .btn-modal-delete {
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            border: none;
            font-weight: 600;
            border-radius: 0.5rem;
        }

        .btn-modal-delete:hover {
            background: linear-gradient(135deg, #e11d48, #be123c);
        }

        .product-count-badge {
            margin-left: auto;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.7rem;
            border-radius: 2rem;
        }

        @keyframes fadeSlideDown {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes rowFadeIn {
            from { opacity: 0; transform: translateX(-8px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes rowFadeOut {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(12px); }
        }

        @keyframes toastIn {
            from { opacity: 0; transform: translateX(24px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 1.75rem;
            }

            .action-btns {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div id="alert-container"></div>

    <div class="page-wrapper">
        <header class="page-header">
            <h1>Product Inventory</h1>
            <p>Track stock, prices, and total value in real time</p>
        </header>

        <div class="glass-card form-card">
            <div class="card-header-custom form">
                <div class="icon-badge"><i class="bi bi-plus-lg"></i></div>
                <h5>Add Product</h5>
            </div>
            <div class="card-body-custom">
                <form id="product-form" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="product_name" class="form-label">Product name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" placeholder="e.g. Wireless Mouse" required>
                        </div>
                        <div class="col-md-4">
                            <label for="quantity_in_stock" class="form-label">Quantity in stock</label>
                            <input type="number" class="form-control" id="quantity_in_stock" name="quantity_in_stock" min="0" step="1" placeholder="0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="price_per_item" class="form-label">Price per item</label>
                            <input type="number" class="form-control" id="price_per_item" name="price_per_item" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="bi bi-check2-circle me-1"></i> Submit Product
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="glass-card table-card">
            <div class="card-header-custom table">
                <div class="icon-badge"><i class="bi bi-table"></i></div>
                <h5>Submitted Products</h5>
                <span class="product-count-badge" id="product-count">0 items</span>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table products-table mb-0">
                        <thead>
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
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        No products submitted yet.
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot id="products-table-foot" style="display: none;">
                            <tr class="sum-row">
                                <td colspan="4" class="sum-label">Sum Total</td>
                                <td class="sum-value" id="sum-total-value">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="edit-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="edit-form">
                    <div class="modal-header edit-header">
                        <h5 class="modal-title" id="edit-modal-label">
                            <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Product
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
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
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-modal-save">
                            <i class="bi bi-check-lg me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="delete-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header delete-header">
                    <h5 class="modal-title" id="delete-modal-label">
                        <i class="bi bi-trash3 me-2 text-danger"></i>Delete Product
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="mb-1">Are you sure you want to delete</p>
                    <p class="fw-semibold mb-0" id="delete-product-name"></p>
                    <p class="text-muted small mt-2 mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger btn-modal-delete" id="confirm-delete-btn">
                        <i class="bi bi-trash3 me-1"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/products.js') }}"></script>
</body>
</html>
