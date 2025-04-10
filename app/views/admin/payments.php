<?php
// Bắt đầu với Admin Layout
$content = ob_get_clean();
require_once VIEW_PATH . '/layouts/admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quản lý thanh toán</h1>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exportModal">
        <i class="fas fa-file-export"></i> Xuất báo cáo
    </button>
</div>

<!-- Thống kê thanh toán -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-primary">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= number_format($totalRevenue, 0, ',', '.') ?> VND</div>
                <p class="stats-label">Tổng doanh thu</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= number_format($completedPayments ?? 0, 0, ',', '.') ?></div>
                <p class="stats-label">Thanh toán thành công</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-warning">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= number_format($pendingPayments ?? 0, 0, ',', '.') ?></div>
                <p class="stats-label">Thanh toán đang xử lý</p>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-widget">
            <div class="stats-icon bg-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-info">
                <div class="stats-value"><?= number_format($failedPayments ?? 0, 0, ',', '.') ?></div>
                <p class="stats-label">Thanh toán thất bại</p>
            </div>
        </div>
    </div>
</div>

<!-- Biểu đồ thống kê -->
<div class="row mb-4">
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
                <canvas id="revenueChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="widget">
            <div class="widget-header">
                <h4 class="widget-title">Phương thức thanh toán</h4>
            </div>
            <div class="widget-body">
                <canvas id="paymentMethodChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bộ lọc và tìm kiếm -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Bộ lọc thanh toán</h5>
    </div>
    <div class="card-body">
        <form action="<?= APP_URL ?>/admin/payments" method="GET">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="user">Người dùng</label>
                        <input type="text" class="form-control" id="user" name="user" placeholder="Tên hoặc email" value="<?= $filters['user'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="status">Trạng thái</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Đang xử lý</option>
                            <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                            <option value="failed" <?= ($filters['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Thất bại</option>
                            <option value="refunded" <?= ($filters['status'] ?? '') === 'refunded' ? 'selected' : '' ?>>Hoàn tiền</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_method">Phương thức</label>
                        <select class="form-control" id="payment_method" name="payment_method">
                            <option value="">Tất cả phương thức</option>
                            <option value="balance" <?= ($filters['payment_method'] ?? '') === 'balance' ? 'selected' : '' ?>>Số dư</option>
                            <option value="momo" <?= ($filters['payment_method'] ?? '') === 'momo' ? 'selected' : '' ?>>MoMo</option>
                            <option value="bank" <?= ($filters['payment_method'] ?? '') === 'bank' ? 'selected' : '' ?>>Chuyển khoản</option>
                            <option value="card" <?= ($filters['payment_method'] ?? '') === 'card' ? 'selected' : '' ?>>Thẻ tín dụng/ghi nợ</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_range">Khoảng thời gian</label>
                        <input type="text" class="form-control date-range-picker" id="date_range" name="date_range" placeholder="Từ ngày - Đến ngày" value="<?= $filters['date_range'] ?? '' ?>">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount_min">Số tiền từ</label>
                        <input type="text" class="form-control currency-input" id="amount_min" name="amount_min" placeholder="VD: 50.000" value="<?= $filters['amount_min'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount_max">Số tiền đến</label>
                        <input type="text" class="form-control currency-input" id="amount_max" name="amount_max" placeholder="VD: 500.000" value="<?= $filters['amount_max'] ?? '' ?>">
                    </div>
                </div>
            </div>
            <div class="text-right">
                <button type="reset" class="btn btn-secondary">Đặt lại</button>
                <button type="submit" class="btn btn-primary">Lọc kết quả</button>
            </div>
        </form>
    </div>
</div>

<!-- Danh sách thanh toán -->
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách thanh toán</h5>
        <div class="input-group" style="width: 300px;">
            <input type="text" id="searchPayment" class="form-control" placeholder="Tìm kiếm nhanh...">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người dùng</th>
                    <th>Phim</th>
                    <th>Số tiền</th>
                    <th>Phương thức</th>
                    <th>Trạng thái</th>
                    <th>Thời gian</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= $payment['id'] ?></td>
                        <td>
                            <a href="<?= APP_URL ?>/admin/users/view/<?= $payment['user_id'] ?>" class="user-link">
                                <?= $payment['username'] ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($payment['movie_id']): ?>
                                <a href="<?= APP_URL ?>/admin/movies/edit/<?= $payment['movie_id'] ?>">
                                    <?= $payment['movie_title'] ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Nạp tiền</span>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($payment['amount'], 0, ',', '.') ?> VND</td>
                        <td>
                            <?php
                            $paymentMethodClass = '';
                            $paymentMethodIcon = '';
                            $paymentMethodText = '';

                            switch ($payment['payment_method']) {
                                case 'balance':
                                    $paymentMethodClass = 'badge-info';
                                    $paymentMethodIcon = 'fa-wallet';
                                    $paymentMethodText = 'Số dư';
                                    break;
                                case 'momo':
                                    $paymentMethodClass = 'badge-danger';
                                    $paymentMethodIcon = 'fa-mobile-alt';
                                    $paymentMethodText = 'MoMo';
                                    break;
                                case 'bank':
                                    $paymentMethodClass = 'badge-primary';
                                    $paymentMethodIcon = 'fa-university';
                                    $paymentMethodText = 'Chuyển khoản';
                                    break;
                                case 'card':
                                    $paymentMethodClass = 'badge-success';
                                    $paymentMethodIcon = 'fa-credit-card';
                                    $paymentMethodText = 'Thẻ tín dụng/ghi nợ';
                                    break;
                                default:
                                    $paymentMethodClass = 'badge-secondary';
                                    $paymentMethodIcon = 'fa-question-circle';
                                    $paymentMethodText = 'Khác';
                            }
                            ?>
                            <span class="badge <?= $paymentMethodClass ?>">
                                <i class="fas <?= $paymentMethodIcon ?>"></i> <?= $paymentMethodText ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusClass = '';
                            $statusText = '';

                            switch ($payment['status']) {
                                case 'pending':
                                    $statusClass = 'badge-warning';
                                    $statusText = 'Đang xử lý';
                                    break;
                                case 'completed':
                                    $statusClass = 'badge-success';
                                    $statusText = 'Hoàn thành';
                                    break;
                                case 'failed':
                                    $statusClass = 'badge-danger';
                                    $statusText = 'Thất bại';
                                    break;
                                case 'refunded':
                                    $statusClass = 'badge-info';
                                    $statusText = 'Hoàn tiền';
                                    break;
                                default:
                                    $statusClass = 'badge-secondary';
                                    $statusText = 'Không xác định';
                            }
                            ?>
                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($payment['created_at'])) ?></td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-info view-payment-btn" data-toggle="modal" data-target="#viewPaymentModal" data-payment-id="<?= $payment['id'] ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <?php if ($payment['status'] === 'completed' && $payment['payment_method'] !== 'balance'): ?>
                                    <button type="button" class="btn btn-sm btn-warning refund-payment-btn" data-toggle="modal" data-target="#refundPaymentModal" data-payment-id="<?= $payment['id'] ?>" data-amount="<?= $payment['amount'] ?>">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if ($payment['status'] === 'pending'): ?>
                                    <a href="<?= APP_URL ?>/admin/payments/complete/<?= $payment['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Xác nhận hoàn thành thanh toán này?')">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="<?= APP_URL ?>/admin/payments/cancel/<?= $payment['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xác nhận hủy thanh toán này?')">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($payments) === 0): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-money-bill-wave empty-state-icon"></i>
                                <p>Không có thanh toán nào.</p>
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
                        <a class="page-link" href="<?= APP_URL ?>/admin/payments?page=<?= $currentPage - 1 ?><?= isset($queryString) ? '&' . $queryString : '' ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/admin/payments?page=<?= $i ?><?= isset($queryString) ? '&' . $queryString : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/admin/payments?page=<?= $currentPage + 1 ?><?= isset($queryString) ? '&' . $queryString : '' ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Xem chi tiết thanh toán Modal -->
<div class="modal fade" id="viewPaymentModal" tabindex="-1" role="dialog" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPaymentModalLabel">Chi tiết thanh toán #<span id="payment-id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">ID thanh toán:</label>
                            <p id="payment-detail-id"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Người dùng:</label>
                            <p id="payment-detail-user"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Phim:</label>
                            <p id="payment-detail-movie"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Số tiền:</label>
                            <p id="payment-detail-amount"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">Phương thức thanh toán:</label>
                            <p id="payment-detail-method"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Mã giao dịch:</label>
                            <p id="payment-detail-transaction"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Trạng thái:</label>
                            <p id="payment-detail-status"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Thời gian tạo:</label>
                            <p id="payment-detail-created"></p>
                        </div>
                        <div class="form-group">
                            <label class="font-weight-bold">Cập nhật gần nhất:</label>
                            <p id="payment-detail-updated"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <a href="#" id="payment-detail-edit" class="btn btn-warning">Chỉnh sửa</a>
                <a href="#" id="payment-detail-invoice" class="btn btn-primary">Xuất hóa đơn</a>
            </div>
        </div>
    </div>
</div>

<!-- Hoàn tiền Modal -->
<div class="modal fade" id="refundPaymentModal" tabindex="-1" role="dialog" aria-labelledby="refundPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundPaymentModalLabel">Hoàn tiền thanh toán #<span id="refund-payment-id"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Chú ý: Hành động này sẽ hoàn tiền cho người dùng và xóa phim khỏi danh sách phim đã mua của họ.
                </p>
                <form id="refundPaymentForm" action="<?= APP_URL ?>/admin/payments/refund" method="POST">
                    <input type="hidden" id="refund-id" name="payment_id">

                    <div class="form-group">
                        <label for="refund-amount">Số tiền hoàn lại</label>
                        <div class="input-group">
                            <input type="text" class="form-control currency-input" id="refund-amount" name="amount" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text">VND</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="refund-reason">Lý do hoàn tiền</label>
                        <select class="form-control" id="refund-reason" name="reason">
                            <option value="Yêu cầu của khách hàng">Yêu cầu của khách hàng</option>
                            <option value="Lỗi kỹ thuật">Lỗi kỹ thuật</option>
                            <option value="Sự cố trong quá trình xem phim">Sự cố trong quá trình xem phim</option>
                            <option value="Chất lượng phim không đạt yêu cầu">Chất lượng phim không đạt yêu cầu</option>
                            <option value="Khác">Khác</option>
                        </select>
                    </div>

                    <div class="form-group" id="other-reason-group" style="display: none;">
                        <label for="other-reason">Lý do khác</label>
                        <textarea class="form-control" id="other-reason" name="other_reason" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="confirm-refund" required>
                            <label class="custom-control-label" for="confirm-refund">
                                Tôi xác nhận rằng thông tin trên là chính xác và tôi muốn hoàn tiền cho giao dịch này.
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" id="submitRefundBtn" class="btn btn-primary">Hoàn tiền</button>
            </div>
        </div>
    </div>
</div>

<!-- Xuất báo cáo Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Xuất báo cáo thanh toán</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportForm" action="<?= APP_URL ?>/admin/payments/export" method="POST">
                    <div class="form-group">
                        <label for="export-format">Định dạng</label>
                        <select class="form-control" id="export-format" name="format">
                            <option value="excel">Excel (.xlsx)</option>
                            <option value="csv">CSV (.csv)</option>
                            <option value="pdf">PDF (.pdf)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="export-date-range">Khoảng thời gian</label>
                        <input type="text" class="form-control date-range-picker" id="export-date-range" name="date_range" placeholder="Từ ngày - Đến ngày">
                    </div>

                    <div class="form-group">
                        <label for="export-status">Trạng thái</label>
                        <select class="form-control" id="export-status" name="status">
                            <option value="">Tất cả trạng thái</option>
                            <option value="pending">Đang xử lý</option>
                            <option value="completed">Hoàn thành</option>
                            <option value="failed">Thất bại</option>
                            <option value="refunded">Hoàn tiền</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="export-payment-method">Phương thức thanh toán</label>
                        <select class="form-control" id="export-payment-method" name="payment_method">
                            <option value="">Tất cả phương thức</option>
                            <option value="balance">Số dư</option>
                            <option value="momo">MoMo</option>
                            <option value="bank">Chuyển khoản</option>
                            <option value="card">Thẻ tín dụng/ghi nợ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="export-include-user-info" name="include_user_info" value="1" checked>
                            <label class="custom-control-label" for="export-include-user-info">
                                Bao gồm thông tin người dùng
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" id="submitExportBtn" class="btn btn-primary">Xuất báo cáo</button>
            </div>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .empty-state {
        padding: 30px;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 15px;
    }

    .user-link {
        color: #495057;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .user-link:hover {
        color: var(--primary-color);
        text-decoration: none;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Chart.js - Revenue Chart
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

        // Chart.js - Payment Method Chart
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const paymentMethodChart = new Chart(paymentMethodCtx, {
            type: 'doughnut',
            data: {
                labels: ['Số dư', 'MoMo', 'Chuyển khoản', 'Thẻ tín dụng'],
                datasets: [{
                    data: [55, 25, 15, 5],
                    backgroundColor: ['#3f51b5', '#f44336', '#4caf50', '#ff9800'],
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

        // Search functionality
        const searchInput = document.getElementById('searchPayment');
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

        // Date Range Picker
        if (typeof $.fn.daterangepicker !== 'undefined') {
            $('.date-range-picker').daterangepicker({
                opens: 'left',
                autoUpdateInput: false,
                locale: {
                    format: 'DD/MM/YYYY',
                    applyLabel: 'Áp dụng',
                    cancelLabel: 'Hủy',
                    fromLabel: 'Từ',
                    toLabel: 'Đến',
                    customRangeLabel: 'Tùy chọn',
                    daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                    monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                    firstDay: 1
                }
            });

            $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
            });

            $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
        }

        // Format currency inputs
        const currencyInputs = document.querySelectorAll('.currency-input');
        currencyInputs.forEach(input => {
            input.addEventListener('input', function(e) {
                // Remove non-digit characters
                let value = this.value.replace(/\D/g, '');

                // Format with thousand separator
                if (value.length > 0) {
                    value = parseInt(value).toLocaleString('vi-VN');
                }

                // Update input value
                this.value = value;
            });
        });

        // View Payment Details
        const viewPaymentButtons = document.querySelectorAll('.view-payment-btn');
        viewPaymentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');

                // In a real application, you would fetch this data via AJAX
                // This is a demo implementation
                const paymentData = {
                    id: paymentId,
                    user: 'Nguyễn Văn A',
                    movie: 'Avengers: Endgame',
                    amount: '150.000 VND',
                    method: 'MoMo',
                    transaction: 'MOMO123456789',
                    status: 'Hoàn thành',
                    created: '01/05/2023 15:30',
                    updated: '01/05/2023 15:32'
                };

                // Populate modal with data
                document.getElementById('payment-id').textContent = paymentData.id;
                document.getElementById('payment-detail-id').textContent = paymentData.id;
                document.getElementById('payment-detail-user').textContent = paymentData.user;
                document.getElementById('payment-detail-movie').textContent = paymentData.movie;
                document.getElementById('payment-detail-amount').textContent = paymentData.amount;
                document.getElementById('payment-detail-method').textContent = paymentData.method;
                document.getElementById('payment-detail-transaction').textContent = paymentData.transaction;
                document.getElementById('payment-detail-status').textContent = paymentData.status;
                document.getElementById('payment-detail-created').textContent = paymentData.created;
                document.getElementById('payment-detail-updated').textContent = paymentData.updated;

                // Update links
                document.getElementById('payment-detail-edit').href = '<?= APP_URL ?>/admin/payments/edit/' + paymentData.id;
                document.getElementById('payment-detail-invoice').href = '<?= APP_URL ?>/admin/payments/invoice/' + paymentData.id;
            });
        });

        // Refund Payment Modal
        const refundPaymentButtons = document.querySelectorAll('.refund-payment-btn');
        refundPaymentButtons.forEach(button => {
            button.addEventListener('click', function() {
                const paymentId = this.getAttribute('data-payment-id');
                const amount = this.getAttribute('data-amount');

                document.getElementById('refund-payment-id').textContent = paymentId;
                document.getElementById('refund-id').value = paymentId;
                document.getElementById('refund-amount').value = parseFloat(amount).toLocaleString('vi-VN');
            });
        });

        // Show/hide other reason field
        const refundReason = document.getElementById('refund-reason');
        if (refundReason) {
            refundReason.addEventListener('change', function() {
                const otherReasonGroup = document.getElementById('other-reason-group');
                if (this.value === 'Khác') {
                    otherReasonGroup.style.display = 'block';
                } else {
                    otherReasonGroup.style.display = 'none';
                }
            });
        }

        // Submit refund form
        const submitRefundBtn = document.getElementById('submitRefundBtn');
        if (submitRefundBtn) {
            submitRefundBtn.addEventListener('click', function() {
                const confirmCheckbox = document.getElementById('confirm-refund');
                if (!confirmCheckbox.checked) {
                    alert('Vui lòng xác nhận thông tin hoàn tiền');
                    return;
                }

                const refundForm = document.getElementById('refundPaymentForm');
                refundForm.submit();
            });
        }

        // Submit export form
        const submitExportBtn = document.getElementById('submitExportBtn');
        if (submitExportBtn) {
            submitExportBtn.addEventListener('click', function() {
                const exportForm = document.getElementById('exportForm');
                exportForm.submit();
            });
        }
    });
</script>
<?php
$scripts = ob_get_clean();
?>