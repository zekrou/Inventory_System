<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Users</h1>
    </section>

    <section class="content">
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">All System Users</h3>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Name</th>
                            <th>Tenant</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></td>
                                    <td>
                                        <?php if (!empty($user['tenant_names']) && $user['tenant_names'] != 'N/A'): ?>
                                            <?php echo $user['tenant_names']; ?>
                                        <?php else: ?>
                                            <span class="label label-warning">System Admin</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (!empty($user['roles']) && $user['roles'] != 'N/A') {
                                            echo ucfirst($user['roles']);
                                        } else {
                                            echo '<span class="label label-warning">No Role Assigned</span>';
                                        }
                                        ?>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>