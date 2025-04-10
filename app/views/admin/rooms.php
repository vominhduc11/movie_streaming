<?php
// Bắt đầu với Admin Layout
$content = ob_get_clean();
require_once VIEW_PATH . '/layouts/admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quản lý phòng xem phim</h1>
    <a href="<?= APP_URL ?>/admin/rooms/add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tạo phòng mới
    </a>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách phòng</h5>

        <div class="d-flex">
            <div class="input-group mr-2">
                <input type="text" class="form-control" placeholder="Tìm kiếm phòng..." id="searchRoom">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown">
                    <i class="fas fa-filter"></i> Lọc
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item active" href="#">Tất cả</a>
                    <a class="dropdown-item" href="#">Phòng đang mở</a>
                    <a class="dropdown-item" href="#">Phòng đã đóng</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Phòng của tôi</a>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Phim</th>
                    <th>Admin</th>
                    <th>Trạng thái</th>
                    <th>Người xem</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                    <tr>
                        <td><?= $room['id'] ?></td>
                        <td><?= $room['name'] ?></td>
                        <td>
                            <div class="media align-items-center">
                                <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $room['thumbnail'] ?? 'default.jpg' ?>" class="room-movie-thumb mr-2">
                                <div class="media-body">
                                    <?= $room['movie_title'] ?>
                                </div>
                            </div>
                        </td>
                        <td><?= $room['admin_username'] ?></td>
                        <td>
                            <?php if ($room['status'] === 'open'): ?>
                                <span class="badge badge-success">Đang mở</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Đã đóng</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= $room['viewers_count'] ?? 0 ?> người xem</span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($room['created_at'])) ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="<?= APP_URL ?>/admin/rooms/view/<?= $room['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($room['status'] === 'open'): ?>
                                    <a href="<?= APP_URL ?>/admin/rooms/close/<?= $room['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn đóng phòng này?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= APP_URL ?>/admin/rooms/open/<?= $room['id'] ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-play"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= APP_URL ?>/admin/rooms/edit/<?= $room['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= APP_URL ?>/admin/rooms/delete/<?= $room['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa phòng này?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($rooms) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-video empty-state-icon"></i>
                                <p>Không có phòng nào.</p>
                                <a href="<?= APP_URL ?>/admin/rooms/add" class="btn btn-primary btn-sm">Tạo phòng mới</a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/admin/rooms?page=<?= $currentPage - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/admin/rooms?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/admin/rooms?page=<?= $currentPage + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Room Statistics -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Thống kê phòng</h5>
            </div>
            <div class="card-body">
                <canvas id="roomStatsChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Phòng phổ biến</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Xem phim cuối tuần</h6>
                                <small class="text-muted">Avengers: Endgame</small>
                            </div>
                            <span class="badge badge-primary badge-pill">45 người xem</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Marvel Movie Night</h6>
                                <small class="text-muted">Spider-Man: No Way Home</small>
                            </div>
                            <span class="badge badge-primary badge-pill">32 người xem</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Horror Friday</h6>
                                <small class="text-muted">The Conjuring</small>
                            </div>
                            <span class="badge badge-primary badge-pill">28 người xem</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Anime Lovers</h6>
                                <small class="text-muted">Your Name</small>
                            </div>
                            <span class="badge badge-primary badge-pill">25 người xem</span>
                        </div>
                    </div>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">Comedy Night</h6>
                                <small class="text-muted">The Hangover</small>
                            </div>
                            <span class="badge badge-primary badge-pill">21 người xem</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .room-movie-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
    }

    .empty-state {
        padding: 30px;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 15px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchRoom');
        if (searchInput) {
            searchInput.addEventListener('keyup', function(e) {
                const term = this.value.toLowerCase();
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.indexOf(term) > -1) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        // Room Stats Chart
        const roomStatsCtx = document.getElementById('roomStatsChart');
        if (roomStatsCtx) {
            const roomStatsChart = new Chart(roomStatsCtx, {
                type: 'bar',
                data: {
                    labels: ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'],
                    datasets: [{
                            label: 'Phòng đã tạo',
                            data: [5, 8, 6, 9, 12, 15, 10],
                            backgroundColor: 'rgba(63, 81, 181, 0.7)'
                        },
                        {
                            label: 'Số người xem',
                            data: [25, 40, 30, 45, 60, 75, 50],
                            backgroundColor: 'rgba(76, 175, 80, 0.7)'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    });
</script>
<?php
$scripts = ob_get_clean();
?>