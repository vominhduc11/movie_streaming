<?php
// Bắt đầu với Admin Layout
$content = ob_get_clean();
require_once VIEW_PATH . '/layouts/admin.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Thêm phim mới</h1>
    <a href="<?= APP_URL ?>/admin/movies" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Thông tin phim</h5>
    </div>
    <div class="card-body">
        <?php if (isset($errors['add'])): ?>
            <div class="alert alert-danger"><?= $errors['add'] ?></div>
        <?php endif; ?>

        <form action="<?= APP_URL ?>/admin/movies/add" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="title">Tiêu đề phim <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" id="title" name="title" value="<?= $title ?? '' ?>" required>
                        <?php if (isset($errors['title'])): ?>
                            <div class="invalid-feedback"><?= $errors['title'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Mô tả phim</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?= $description ?? '' ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="price">Giá (VND) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" id="price" name="price" value="<?= $price ?? '50000' ?>" min="0" step="1000" required>
                                <?php if (isset($errors['price'])): ?>
                                    <div class="invalid-feedback"><?= $errors['price'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="duration">Thời lượng (phút) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?= isset($errors['duration']) ? 'is-invalid' : '' ?>" id="duration" name="duration" value="<?= $duration ?? '90' ?>" min="1" required>
                                <?php if (isset($errors['duration'])): ?>
                                    <div class="invalid-feedback"><?= $errors['duration'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="release_year">Năm phát hành</label>
                                <select class="form-control" id="release_year" name="release_year">
                                    <?php for ($year = date('Y'); $year >= 1990; $year--): ?>
                                        <option value="<?= $year ?>" <?= (isset($release_year) && $release_year == $year) ? 'selected' : '' ?>><?= $year ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="genre">Thể loại</label>
                        <select class="form-control" id="genre" name="genre">
                            <option value="">-- Chọn thể loại --</option>
                            <option value="Hành Động" <?= (isset($genre) && $genre == 'Hành Động') ? 'selected' : '' ?>>Hành Động</option>
                            <option value="Tình Cảm" <?= (isset($genre) && $genre == 'Tình Cảm') ? 'selected' : '' ?>>Tình Cảm</option>
                            <option value="Hài Hước" <?= (isset($genre) && $genre == 'Hài Hước') ? 'selected' : '' ?>>Hài Hước</option>
                            <option value="Kinh Dị" <?= (isset($genre) && $genre == 'Kinh Dị') ? 'selected' : '' ?>>Kinh Dị</option>
                            <option value="Viễn Tưởng" <?= (isset($genre) && $genre == 'Viễn Tưởng') ? 'selected' : '' ?>>Viễn Tưởng</option>
                            <option value="Hoạt Hình" <?= (isset($genre) && $genre == 'Hoạt Hình') ? 'selected' : '' ?>>Hoạt Hình</option>
                            <option value="Chiến Tranh" <?= (isset($genre) && $genre == 'Chiến Tranh') ? 'selected' : '' ?>>Chiến Tranh</option>
                            <option value="Tâm Lý" <?= (isset($genre) && $genre == 'Tâm Lý') ? 'selected' : '' ?>>Tâm Lý</option>
                            <option value="Hình Sự" <?= (isset($genre) && $genre == 'Hình Sự') ? 'selected' : '' ?>>Hình Sự</option>
                            <option value="Phiêu Lưu" <?= (isset($genre) && $genre == 'Phiêu Lưu') ? 'selected' : '' ?>>Phiêu Lưu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="video">File phim <span class="text-danger">*</span></label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input <?= isset($errors['video']) ? 'is-invalid' : '' ?>" id="video" name="video" accept="video/*" required>
                            <label class="custom-file-label" for="video">Chọn file</label>
                            <?php if (isset($errors['video'])): ?>
                                <div class="invalid-feedback"><?= $errors['video'] ?></div>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted">Chấp nhận các định dạng MP4, WebM, Ogg. Kích thước tối đa 500MB.</small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="thumbnail">Ảnh thumbnail <span class="text-danger">*</span></label>
                        <div class="thumbnail-preview mb-3">
                            <img id="thumbnail-preview-img" src="<?= PUBLIC_PATH ?>/assets/img/thumbnail-placeholder.jpg" class="img-fluid rounded">
                        </div>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input <?= isset($errors['thumbnail']) ? 'is-invalid' : '' ?>" id="thumbnail" name="thumbnail" accept="image/*" required>
                            <label class="custom-file-label" for="thumbnail">Chọn ảnh</label>
                            <?php if (isset($errors['thumbnail'])): ?>
                                <div class="invalid-feedback"><?= $errors['thumbnail'] ?></div>
                            <?php endif; ?>
                        </div>
                        <small class="form-text text-muted">Chấp nhận các định dạng JPEG, PNG, GIF. Kích thước tối đa 2MB. Tỷ lệ khuyến nghị 2:3.</small>
                    </div>

                    <div class="form-group">
                        <label>Trạng thái</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">Hiển thị phim</label>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <div class="form-group mb-0">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu phim
                </button>
                <a href="<?= APP_URL ?>/admin/movies" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<style>
    .thumbnail-preview {
        width: 100%;
        height: 300px;
        border-radius: 4px;
        overflow: hidden;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .thumbnail-preview img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File input labels
        document.querySelectorAll('.custom-file-input').forEach(input => {
            input.addEventListener('change', function(e) {
                const fileName = this.files[0].name;
                const label = this.nextElementSibling;
                label.textContent = fileName;

                // Preview thumbnail
                if (this.id === 'thumbnail') {
                    const preview = document.getElementById('thumbnail-preview-img');
                    const file = this.files[0];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            });
        });

        // Form validation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Validate title
            const title = document.getElementById('title');
            if (!title.value.trim()) {
                title.classList.add('is-invalid');
                isValid = false;
            } else {
                title.classList.remove('is-invalid');
            }

            // Validate price
            const price = document.getElementById('price');
            if (!price.value || price.value <= 0) {
                price.classList.add('is-invalid');
                isValid = false;
            } else {
                price.classList.remove('is-invalid');
            }

            // Validate duration
            const duration = document.getElementById('duration');
            if (!duration.value || duration.value <= 0) {
                duration.classList.add('is-invalid');
                isValid = false;
            } else {
                duration.classList.remove('is-invalid');
            }

            // Validate video
            const video = document.getElementById('video');
            if (!video.files || video.files.length === 0) {
                video.classList.add('is-invalid');
                isValid = false;
            } else {
                video.classList.remove('is-invalid');
            }

            // Validate thumbnail
            const thumbnail = document.getElementById('thumbnail');
            if (!thumbnail.files || thumbnail.files.length === 0) {
                thumbnail.classList.add('is-invalid');
                isValid = false;
            } else {
                thumbnail.classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
            }
        });
    });
</script>
<?php
$scripts = ob_get_clean();
?>