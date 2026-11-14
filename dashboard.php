<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/header.php';

$userId = (int)$_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT id, title, description, file_path, created_at FROM images WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$images = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">PinGallery</a>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-success" id="openAddModalBtn">+ Add New Image</button>
            <a href="logout.php" class="btn btn-outline-secondary">Logout (<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>)</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div id="dashboardAlert" class="alert d-none" role="alert"></div>

    <div class="masonry-grid" id="galleryGrid">
        <?php foreach ($images as $img): ?>
            <?php
                $safeId = (int)$img['id'];
                $safeTitle = htmlspecialchars($img['title'], ENT_QUOTES, 'UTF-8');
                $safeRawDesc = htmlspecialchars($img['description'] ?? '', ENT_QUOTES, 'UTF-8');
                $safeHtmlDesc = nl2br($safeRawDesc);
                $safePath = htmlspecialchars($img['file_path'], ENT_QUOTES, 'UTF-8');
            ?>
            <div class="masonry-item" id="pin-<?php echo $safeId; ?>">
                <div class="card pin-card shadow-sm border-0">
                    <img src="<?php echo $safePath; ?>" alt="<?php echo $safeTitle; ?>">
                    <div class="card-body">
                        <h6 class="card-title mb-1 pin-title"><?php echo $safeTitle; ?></h6>
                        <p class="card-text text-muted small pin-desc"><?php echo $safeHtmlDesc; ?></p>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary edit-btn"
                                    data-id="<?php echo $safeId; ?>"
                                    data-title="<?php echo $safeTitle; ?>"
                                    data-description="<?php echo $safeRawDesc; ?>">
                                Edit
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger delete-btn"
                                    data-id="<?php echo $safeId; ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="imageForm" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Add New Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalAlert" class="alert d-none" role="alert"></div>
                    <input type="hidden" id="imageId" name="image_id" value="">

                    <div class="mb-3">
                        <label for="imgTitle" class="form-label">Title</label>
                        <input type="text" class="form-control" id="imgTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="imgDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="imgDescription" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3" id="fileInputGroup">
                        <label for="imgFile" class="form-label">Upload Image (JPG, PNG, GIF)</label>
                        <input type="file" class="form-control" id="imgFile" name="image" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveImageBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
$(function() {
    const modalEl = document.getElementById('imageModal');
    const bsModal = new bootstrap.Modal(modalEl);
    let mode = 'create'; // 'create' or 'edit'

    function showDashAlert(type, msg) {
        $('#dashboardAlert')
            .removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type)
            .text(msg);
    }

    function buildPinCard(img) {
        return `
            <div class="masonry-item" id="pin-${img.id}">
                <div class="card pin-card shadow-sm border-0">
                    <img src="${img.file_path}" alt="${img.title}">
                    <div class="card-body">
                        <h6 class="card-title mb-1 pin-title">${img.title}</h6>
                        <p class="card-text text-muted small pin-desc">${img.description}</p>
                        <div class="d-flex justify-content-end gap-2 mt-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-primary edit-btn"
                                    data-id="${img.id}"
                                    data-title="${img.title}"
                                    data-description="${img.raw_description}">
                                Edit
                            </button>
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger delete-btn"
                                    data-id="${img.id}">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Open Create Modal
    $('#openAddModalBtn').on('click', function() {
        mode = 'create';
        $('#imageModalLabel').text('Add New Image');
        $('#imageForm')[0].reset();
        $('#imageId').val('');
        $('#imgFile').prop('required', true);
        $('#fileInputGroup').show();
        $('#modalAlert').addClass('d-none');
        bsModal.show();
    });

    // Open Edit Modal
    $(document).on('click', '.edit-btn', function() {
        mode = 'edit';
        $('#imageModalLabel').text('Edit Image');
        $('#imageForm')[0].reset();
        $('#imageId').val($(this).data('id'));
        $('#imgTitle').val($(this).data('title'));
        $('#imgDescription').val($(this).data('description'));
        $('#imgFile').prop('required', false);
        $('#fileInputGroup').hide();
        $('#modalAlert').addClass('d-none');
        bsModal.show();
    });

    // Create or Update via AJAX
    $('#imageForm').on('submit', function(e) {
        e.preventDefault();
        const url = mode === 'create' ? 'ajax/image_create.php' : 'ajax/image_update.php';
        const formData = new FormData(this);
        const $btn = $('#saveImageBtn');

        $btn.prop('disabled', true).text('Saving...');
        $('#modalAlert').addClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    bsModal.hide();
                    showDashAlert('success', res.message);

                    if (mode === 'create') {
                        $('#galleryGrid').prepend(buildPinCard(res.image));
                    } else {
                        const $card = $('#pin-' + res.image.id);
                        $card.find('.pin-title').text(res.image.title);
                        $card.find('.pin-desc').html(res.image.description);
                        const $editBtn = $card.find('.edit-btn');
                        $editBtn.data('title', res.image.title);
                        $editBtn.data('description', res.image.raw_description);
                    }
                } else {
                    $('#modalAlert')
                        .removeClass('d-none alert-success')
                        .addClass('alert-danger')
                        .text(res.message);
                }
            },
            error: function() {
                $('#modalAlert')
                    .removeClass('d-none alert-success')
                    .addClass('alert-danger')
                    .text('An error occurred while saving.');
            },
            complete: function() {
                $btn.prop('disabled', false).text('Save Changes');
            }
        });
    });

    // Delete via AJAX with confirmation and fadeOut
    $(document).on('click', '.delete-btn', function() {
        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        const id = $(this).data('id');
        const $item = $('#pin-' + id);

        $.ajax({
            url: 'ajax/image_delete.php',
            type: 'POST',
            data: { image_id: id },
            dataType: 'json',
            success: function(res) {
                if (res.status === 'success') {
                    showDashAlert('success', res.message);
                    $item.fadeOut(400, function() {
                        $(this).remove();
                    });
                } else {
                    showDashAlert('danger', res.message);
                }
            },
            error: function() {
                showDashAlert('danger', 'Failed to delete image.');
            }
        });
    });
});
</script>