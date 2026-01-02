<?php
$page_title = 'Inventory Management - Elite Salon';
require_once '../includes/auth.php';
require_once '../includes/db.php';

require_role('admin');

// Handle inventory operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $item_name = sanitize_input($_POST['item_name']);
        $category = sanitize_input($_POST['category']);
        $quantity = (int)$_POST['quantity'];
        $unit_price = (float)$_POST['unit_price'];
        $supplier = sanitize_input($_POST['supplier']);
        $reorder_level = (int)$_POST['reorder_level'];
        
        $status = $quantity == 0 ? 'out_of_stock' : ($quantity <= $reorder_level ? 'low_stock' : 'in_stock');
        
        $query = "INSERT INTO inventory (item_name, category, quantity, unit_price, supplier, reorder_level, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssidsss", $item_name, $category, $quantity, $unit_price, $supplier, $reorder_level, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $_SESSION['success'] = 'Item added successfully!';
        header("Location: /elite-salon/admin/inventory.php");
        exit();
    } elseif ($_POST['action'] === 'update') {
        $item_id = (int)$_POST['item_id'];
        $quantity = (int)$_POST['quantity'];
        $unit_price = (float)$_POST['unit_price'];
        $reorder_level = (int)$_POST['reorder_level'];
        
        $status = $quantity == 0 ? 'out_of_stock' : ($quantity <= $reorder_level ? 'low_stock' : 'in_stock');
        
        $query = "UPDATE inventory SET quantity = ?, unit_price = ?, reorder_level = ?, status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "idisi", $quantity, $unit_price, $reorder_level, $status, $item_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $_SESSION['success'] = 'Item updated successfully!';
        header("Location: /elite-salon/admin/inventory.php");
        exit();
    } elseif ($_POST['action'] === 'delete') {
        $item_id = (int)$_POST['item_id'];
        mysqli_query($conn, "DELETE FROM inventory WHERE id = $item_id");
        
        $_SESSION['success'] = 'Item deleted successfully!';
        header("Location: /elite-salon/admin/inventory.php");
        exit();
    }
}

// Fetch inventory items
$inventory = mysqli_query($conn, "SELECT * FROM inventory ORDER BY item_name");

require_once '../includes/header.php';
require_once '../includes/dashboard-nav.php';
?>

<div class="dashboard-container">
    <div class="container-fluid">
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-box-seam"></i> Inventory Management</h1>
                    <p>Manage salon products and supplies</p>
                </div>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="bi bi-plus-circle"></i> Add Item
                </button>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="content-card">
            <h4><i class="bi bi-list"></i> All Inventory Items</h4>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total Value</th>
                            <th>Supplier</th>
                            <th>Reorder Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($inventory) > 0): ?>
                            <?php while ($item = mysqli_fetch_assoc($inventory)): ?>
                                <tr>
                                    <td>#<?php echo $item['id']; ?></td>
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category'] ?? 'N/A'); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>$<?php echo number_format($item['unit_price'], 2); ?></td>
                                    <td>$<?php echo number_format($item['quantity'] * $item['unit_price'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['supplier'] ?? 'N/A'); ?></td>
                                    <td><?php echo $item['reorder_level']; ?></td>
                                    <td>
                                        <?php
                                        $status_class = ['in_stock' => 'success', 'low_stock' => 'warning', 'out_of_stock' => 'danger'];
                                        ?>
                                        <span class="badge bg-<?php echo $status_class[$item['status']]; ?>">
                                            <?php echo ucwords(str_replace('_', ' ', $item['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" 
                                                data-bs-target="#editModal<?php echo $item['id']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?php echo $item['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        
                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="editModal<?php echo $item['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Update Item</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="update">
                                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Quantity</label>
                                                                <input type="number" name="quantity" class="form-control" value="<?php echo $item['quantity']; ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Unit Price</label>
                                                                <input type="number" step="0.01" name="unit_price" class="form-control" value="<?php echo $item['unit_price']; ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label class="form-label">Reorder Level</label>
                                                                <input type="number" name="reorder_level" class="form-control" value="<?php echo $item['reorder_level']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="submit" class="btn btn-primary">Update</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="deleteModal<?php echo $item['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">Confirm Delete</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                                            <p>Are you sure you want to delete this item?</p>
                                                            <p><strong><?php echo htmlspecialchars($item['item_name']); ?></strong></p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center">No items found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="item_name" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Quantity *</label>
                        <input type="number" name="quantity" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit Price *</label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <input type="text" name="supplier" class="form-control">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reorder Level *</label>
                        <input type="number" name="reorder_level" class="form-control" value="10" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
