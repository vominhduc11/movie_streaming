<?php
// Bắt đầu với Admin Layout
$content = ob_get_clean();
require_once VIEW_PATH . '/layouts/admin.php';
?>

<div class="row">
    <div class="col-lg-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-primary">
                <i class="fas fa-film"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= $totalMovies ?></div>
                <p class="stats-label">Tổng số phim</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= $totalUsers ?></div>
                <p class="stats-label">Người dùng</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-warning">
                <i class="fas fa-video"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= $openRooms ?>/<?= $totalRooms ?></div>
                <p class="stats-label">Phòng đang mở</p>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-danger">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= number_format($totalRevenue, 0, ',', '.') ?> VND</div>
                <p class="stats-label">Tổng doanh thu</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-8">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title">Doanh thu theo thời gian</h4>
                <div class="widget-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" data-period="day">Ngày</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="week">Tuần</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="month">Tháng</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-period="year">Năm</button>
                    </div>
                </div>
            </div>
            <div class="widget-body">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title">Phim phổ biến</h4>
                <div class="widget-tools">
                    <button type="button" class="btn btn-sm btn-outline-primary">Xem tất cả</button>
                </div>
            </div>
            <div class="widget-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Phim</th>
                                <th>Lượt xem</th>
                                <th>Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popularMovies as $movie): ?>
                                <tr>
                                    <td>
                                        <div class="movie-item">
                                            <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>" class="movie-thumbnail">
                                            <span class="movie-title"><?= $movie['title'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= $movie['views'] ?></td>
                                    <td><?= number_format($movie['price'] * ($movie['views'] / 10), 0, ',', '.') ?> VND</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-lg-6">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title">Thanh toán gần đây</h4>
                <div class="widget-tools">
                    <button type="button" class="btn btn-sm btn-outline-primary">Xem tất cả</button>
                </div>
            </div>
            <div class="widget-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Người dùng</th>
                                <th>Phim</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentPayments as $payment): ?>
                                <tr>
                                    <td>#<?= $payment['id'] ?></td>
                                    <td><?= $payment['username'] ?></td>
                                    <td><?= $payment['movie_title'] ?></td>
                                    <td><?= number_format($payment['amount'], 0, ',', '.') ?> VND</td>
                                    <td>
                                        <?php if ($payment['status'] === 'completed'): ?>
                                            <span class="badge badge-success">Hoàn thành</span>
                                        <?php elseif ($payment['status'] === 'pending'): ?>
                                            <span class="badge badge-warning">Đang xử lý</span>
                                        <?php elseif ($payment['status'] === 'failed'): ?>
                                            <span class="badge badge-danger">Thất bại</span>
                                        <?php elseif ($payment['status'] === 'refunded'): ?>
                                            <span class="badge badge-info">Hoàn tiền</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="row">
            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title">Phân bố người dùng</h4>
                    </div>
                    <div class="widget-body">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="widget">
                    <div class="widget-header">
                        <h4 class="widget-title">Phân bố thể loại</h4>
                    </div>
                    <div class="widget-body">
                        <canvas id="genreChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="widget mt-4">
            <div class="widget-header">
                <h4 class="widget-title">Phòng đang mở</h4>
                <div class="widget-tools">
                    <button type="button" class="btn btn-sm btn-outline-primary">Tạo phòng mới</button>
                </div>
            </div>
            <div class="widget-body p-0">
                <ul class="room-list">
                    <?php foreach (array_slice($openRooms, 0, 5) as $room): ?>
                        <li class="room-item">
                            <div class="room-info">
                                <h5 class="room-name"><?= $room['name'] ?></h5>
                                <p class="room-movie">Phim: <?= $room['movie_title'] ?></p>
                                <p class="room-admin">Admin: <?= $room['admin_username'] ?></p>
                            </div>
                            <div class="room-actions">
                                <a href="<?= APP_URL ?>/admin/rooms/view/<?= $room['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                                <a href="<?= APP_URL ?>/admin/rooms/close/<?= $room['id'] ?>" class="btn btn-sm btn-danger">
                                    <i class="fas fa-times"></i> Đóng
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    /* Additional Dashboard Styles */
    .movie-item {
        display: flex;
        align-items: center;
    }

    .movie-thumbnail {
        width: 40px;
        height: 40px;
        border-radius: 4px;
        object-fit: cover;
        margin-right: 10px;
    }

    .movie-title {
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
    }

    .room-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .room-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }

    .room-item:last-child {
        border-bottom: none;
    }

    .room-info h5 {
        margin-bottom: 5px;
    }

    .room-info p {
        margin-bottom: 3px;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .room-actions {
        display: flex;
        gap: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart.js Configuration
        Chart.defaults.font.family = "'Roboto', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6c757d';

        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
                datasets: [{
                    label: 'Doanh thu (VND)',
                    data: [1500000, 2500000, 1800000, 3000000, 2200000, 3200000, 3800000, 4500000, 4000000, 5000000, 4800000, 5500000],
                    borderColor: '#3f51b5',
                    backgroundColor: 'rgba(63, 81, 181, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return value.toLocaleString('vi-VN') + ' VND';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000) + 'M';
                                }
                                return value / 1000 + 'K';
                            }
                        }
                    }
                }
            }
        });

        // User Distribution Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        const userChart = new Chart(userCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mới', 'Thường xuyên', 'VIP'],
                datasets: [{
                    data: [35, 45, 20],
                    backgroundColor: ['#4caf50', '#ff9800', '#3f51b5'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                },
                cutout: '70%'
            }
        });

        // Genre Distribution Chart
        const genreCtx = document.getElementById('genreChart').getContext('2d');
        const genreChart = new Chart(genreCtx, {
            type: 'pie',
            data: {
                labels: ['Hành động', 'Tình cảm', 'Hài hước', 'Kinh dị', 'Khoa học'],
                datasets: [{
                    data: [30, 20, 25, 15, 10],
                    backgroundColor: ['#3f51b5', '#f44336', '#4caf50', '#ff9800', '#2196f3'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });

        // Period buttons for Revenue Chart
        const periodButtons = document.querySelectorAll('.widget-tools .btn-group button');
        periodButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                periodButtons.forEach(btn => btn.classList.remove('active'));

                // Add active class to clicked button
                this.classList.add('active');

                // Update chart data based on selected period
                const period = this.getAttribute('data-period');
                updateRevenueChart(period);
            });
        });

        // Function to update Revenue Chart based on selected period
        function updateRevenueChart(period) {
            let labels, data;

            switch (period) {
                case 'day':
                    labels = ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '23:59'];
                    data = [300000, 150000, 450000, 600000, 750000, 900000, 450000];
                    break;
                case 'week':
                    labels = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'];
                    data = [500000, 700000, 600000, 800000, 1200000, 1500000, 900000];
                    break;
                case 'month':
                    labels = ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'];
                    data = [2500000, 3200000, 2800000, 3500000];
                    break;
                case 'year':
                    labels = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
                    data = [1500000, 2500000, 1800000, 3000000, 2200000, 3200000, 3800000, 4500000, 4000000, 5000000, 4800000, 5500000];
                    break;
                default:
                    return;
            }

            revenueChart.data.labels = labels;
            revenueChart.data.datasets[0].data = data;
            revenueChart.update();
        }
    });
</script>
<?php
$scripts = ob_get_clean();
?>