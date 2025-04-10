<?php
// Bắt đầu với Admin Layout
$content = ob_get_clean();
require_once VIEW_PATH . '/layouts/admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Quản lý phim</h1>
    <a href="<?= APP_URL ?>/admin/movies/add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm phim mới
    </a>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Danh sách phim</h5>

        <div class="d-flex">
            <div class="input-group mr-2">
                <input type="text" class="form-control" placeholder="Tìm kiếm phim..." id="searchMovie">
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
                    <a class="dropdown-item" href="#">Đang hiển thị</a>
                    <a class="dropdown-item" href="#">Đã ẩn</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">Sắp xếp theo giá</a>
                    <a class="dropdown-item" href="#">Sắp xếp theo lượt xem</a>
                    <a class="dropdown-item" href="#">Sắp xếp theo thời gian</a>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Thể loại</th>
                    <th>Giá</th>
                    <th>Thời lượng</th>
                    <th>Lượt xem</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?= $movie['id'] ?></td>
                        <td>
                            <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" alt="<?= $movie['title'] ?>" class="movie-thumbnail">
                        </td>
                        <td><?= $movie['title'] ?></td>
                        <td><?= $movie['genre'] ?></td>
                        <td><?= number_format($movie['price'], 0, ',', '.') ?> VND</td>
                        <td><?= $movie['duration'] ?> phút</td>
                        <td><?= $movie['views'] ?></td>
                        <td>
                            <?php if ($movie['is_active']): ?>
                                <span class="badge badge-success">Hiển thị</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Đã ẩn</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="<?= APP_URL ?>/admin/movies/edit/<?= $movie['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <?php if ($movie['is_active']): ?>
                                    <a href="<?= APP_URL ?>/admin/movies/delete/<?= $movie['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc chắn muốn ẩn phim này?')">
                                        <i class="fas fa-eye-slash"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= APP_URL ?>/admin/movies/restore/<?= $movie['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc chắn muốn hiển thị lại phim này?')">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>

                                <button type="button" class="btn btn-sm btn-info create-room-btn" data-toggle="modal" data-target="#createRoomModal" data-movie-id="<?= $movie['id'] ?>" data-movie-title="<?= $movie['title'] ?>">
                                    <i class="fas fa-video"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($movies) === 0): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="empty-state">
                                <i class="fas fa-film empty-state-icon"></i>
                                <p>Không có phim nào.</p>
                                <a href="<?= APP_URL ?>/admin/movies/add" class="btn btn-primary btn-sm">Thêm phim mới</a>
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
                        <a class="page-link" href="<?= APP_URL ?>/admin/movies?page=<?= $currentPage - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= APP_URL ?>/admin/movies?page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= APP_URL ?>/admin/movies?page=<?= $currentPage + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<!-- Movie Statistics -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Phim theo thể loại</h5>
            </div>
            <div class="card-body">
                <canvas id="genreChart" height="250"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Top phim xem nhiều</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach (array_slice($movies, 0, 5) as $index => $movie): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="media align-items-center">
                                    <div class="rank-number mr-3"><?= $index + 1 ?></div>
                                    <img src="<?= PUBLIC_PATH ?>/assets/uploads/thumbnails/<?= $movie['thumbnail'] ?>" class="movie-thumbnail mr-3">
                                    <div class="media-body">
                                        <h6 class="mb-0"><?= $movie['title'] ?></h6>
                                        <small class="text-muted"><?= $movie['genre'] ?>, <?= $movie['release_year'] ?></small>
                                    </div>
                                </div>
                                <span class="badge badge-primary badge-pill"><?= $movie['views'] ?> lượt xem</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Room Modal -->
<div class="modal fade" id="createRoomModal" tabindex="-1" role="dialog" aria-labelledby="createRoomModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createRoomModalLabel">Tạo phòng xem phim</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= APP_URL ?>/admin/rooms/add" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="movie_id" id="modal-movie-id">

                    <div class="form-group">
                        <label for="room-name">Tên phòng:</label>
                        <input type="text" class="form-control" id="room-name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label>Phim:</label>
                        <p id="modal-movie-title" class="form-control-plaintext"></p>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="auto-open" name="auto_open">
                            <label class="custom-control-label" for="auto-open">Tự động mở phòng sau khi tạo</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Tạo phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .movie-thumbnail {
        width: 50px;
        height: 50px;
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

    .rank-number {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.8rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchMovie');
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

        // Create Room Modal
        const createRoomBtns = document.querySelectorAll('.create-room-btn');
        if (createRoomBtns.length) {
            createRoomBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const movieId = this.getAttribute('data-movie-id');
                    const movieTitle = this.getAttribute('data-movie-title');

                    document.getElementById('modal-movie-id').value = movieId;
                    document.getElementById('modal-movie-title').textContent = movieTitle;
                    document.getElementById('room-name').value = 'Xem phim: ' + movieTitle;
                });
            });
        }

        // Genre Chart
        const genreCtx = document.getElementById('genreChart');
        if (genreCtx) {
            // Tạo dữ liệu mẫu cho biểu đồ
            const genres = [];
            const genreCounts = [];
            const genreColors = [
                '#3f51b5', '#f44336', '#4caf50', '#ff9800', '#2196f3',
                '#9c27b0', '#e91e63', '#00bcd4', '#795548', '#607d8b'
            ];

            // Thu thập dữ liệu thể loại từ danh sách phim
            const genreMap = {};

            <?php foreach ($movies as $movie): ?>
                if ('<?= $movie['genre'] ?>') {
                    if (genreMap['<?= $movie['genre'] ?>']) {
                        genreMap['<?= $movie['genre'] ?>']++;
                    } else {
                        genreMap['<?= $movie['genre'] ?>'] = 1;
                    }
                }
            <?php endforeach; ?>

            // Chuyển đổi dữ liệu sang định dạng cho biểu đồ
            for (const genre in genreMap) {
                genres.push(genre);
                genreCounts.push(genreMap[genre]);
            }

            const genreChart = new Chart(genreCtx, {
                type: 'doughnut',
                data: {
                    labels: genres,
                    datasets: [{
                        data: genreCounts,
                        backgroundColor: genreColors.slice(0, genres.length),
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
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