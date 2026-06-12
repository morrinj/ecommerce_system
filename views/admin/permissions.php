<?php
$pageTitle = 'My Permissions';
require __DIR__ . '/partials/header.php';
$role = $_SESSION['admin_role'] ?? '';
$isSuper = $role === 'superadmin';
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Permission Overview — <strong><?= ucfirst($role) ?></strong></span>
        <span class="badge bg-<?= $isSuper ? 'danger' : ($role === 'admin' ? 'primary' : 'secondary') ?> fs-6"><?= ucfirst($role) ?></span>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th style="width:120px" class="text-center">Your Access</th>
                    <th style="width:120px" class="text-center">Super Admin</th>
                    <th style="width:120px" class="text-center">Manager</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $permissions = [
                    'Dashboard'         => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Products'          => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Categories'        => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Orders'            => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Reviews'           => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Coupons'           => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Reports'           => ['superadmin' => true,  'admin' => true,  'manager' => true],
                    'Customers'         => ['superadmin' => true,  'admin' => false, 'manager' => false],
                    'Admin Management'  => ['superadmin' => true,  'admin' => false, 'manager' => false],
                    'Settings'          => ['superadmin' => true,  'admin' => false, 'manager' => false],
                    'AI Features'       => ['superadmin' => true,  'admin' => false, 'manager' => false],
                ];
                foreach ($permissions as $feature => $roles):
                    $yourAccess = $roles[$role] ?? false;
                ?>
                <tr class="<?= !$yourAccess ? 'table-light' : '' ?>">
                    <td><?= $feature ?></td>
                    <td class="text-center">
                        <?php if ($yourAccess): ?>
                            <i class="bi bi-check-circle-fill text-success fs-5" title="Allowed"></i>
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill text-danger fs-5" title="Restricted"></i>
                        <?php endif; ?>
                    </td>
                    <td class="text-center"><i class="bi bi-check-circle-fill text-success fs-5"></i></td>
                    <td class="text-center">
                        <?php if ($roles['admin']): ?>
                            <i class="bi bi-check-circle-fill text-success fs-5"></i>
                        <?php else: ?>
                            <i class="bi bi-x-circle-fill text-danger fs-5"></i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/partials/footer.php'; ?>
