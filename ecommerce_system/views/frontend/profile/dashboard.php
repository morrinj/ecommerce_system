<?php $pageTitle = 'My Dashboard'; ?>
<?php require __DIR__ . '/../../partials/header.php'; ?>

<div class="container py-4">
    <h3 class="fw-bold mb-4"><i class="bi bi-person-circle me-2"></i>My Dashboard</h3>
    <?php if ($msg = flash('success')): ?>
    <div class="alert alert-success"><?= $msg ?></div>
    <?php endif; ?>
    <div class="row g-4">
        <div class="col-lg-3">
            <div class="list-group border-0 shadow-sm">
                <a href="<?= url('profile') ?>" class="list-group-item list-group-item-action active"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
                <a href="<?= url('orders') ?>" class="list-group-item list-group-item-action"><i class="bi bi-box me-2"></i>My Orders</a>
                <a href="<?= url('wishlist') ?>" class="list-group-item list-group-item-action"><i class="bi bi-heart me-2"></i>Wishlist</a>
                <a href="<?= url('profile/edit') ?>" class="list-group-item list-group-item-action"><i class="bi bi-pencil me-2"></i>Edit Profile</a>
                <a href="<?= url('profile/password') ?>" class="list-group-item list-group-item-action"><i class="bi bi-lock me-2"></i>Change Password</a>
            </div>
        </div>
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-0">Welcome, <?= htmlspecialchars($user['first_name']) ?>!</h5>
                    <p class="text-muted mb-0"><?= htmlspecialchars($user['email']) ?></p>
                </div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-box display-5"></i>
                            <h3 class="fw-bold mt-2"><?= count($recentOrders) ?></h3>
                            <p class="mb-0 opacity-75">Total Orders</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-success text-white">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-heart display-5"></i>
                            <h3 class="fw-bold mt-2">
                                <?php
                                $wishlistModel = new Wishlist();
                                echo count($wishlistModel->getUserWishlist(getCurrentUserId()));
                                ?>
                            </h3>
                            <p class="mb-0 opacity-75">Wishlist Items</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm bg-info text-white">
                        <div class="card-body text-center py-4">
                            <i class="bi bi-tag display-5"></i>
                            <h3 class="fw-bold mt-2">
                                <?php
                                $orderModel = new Order();
                                $allOrders = $orderModel->getByUser(getCurrentUserId());
                                $completed = 0;
                                foreach ($allOrders as $o) {
                                    if ($o['status'] === 'delivered') $completed++;
                                }
                                echo $completed;
                                ?>
                            </h3>
                            <p class="mb-0 opacity-75">Completed</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0">Recent Orders</h6>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentOrders)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted mb-0">No orders yet</p>
                        <a href="<?= url('products') ?>" class="btn btn-primary btn-sm mt-2">Start Shopping</a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $ord): ?>
                                <tr>
                                    <td><small><?= htmlspecialchars($ord['order_number']) ?></small></td>
                                    <td><small><?= date('M j, Y', strtotime($ord['created_at'])) ?></small></td>
                                    <td><small><?= formatPrice($ord['total_amount']) ?></small></td>
                                    <td><?= getStatusBadge($ord['status']) ?></td>
                                    <td><a href="<?= url('order/' . $ord['order_number']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../partials/footer.php'; ?>
