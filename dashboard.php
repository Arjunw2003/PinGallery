
<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/header.php';

$userId = $_SESSION['user_id'];

$stmt = mysqli_prepare($conn, "SELECT * FROM images WHERE user_id = ? ORDER BY created_at DESC");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$images = mysqli_fetch_all($result, MYSQLI_ASSOC);

mysqli_stmt_close($stmt);
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container">

        <a class="navbar-brand fw-bold" href="dashboard.php">Gallery</a>

        <div class="d-flex gap-2">
            <button class="btn btn-success" id="openAddModalBtn">
                + Add New Image
            </button>

            <a href="logout.php" class="btn btn-outline-secondary">
                Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)
            </a>
        </div>

    </div>
</nav>


<!-- Gallery -->
<div class="container pb-5">

    <div id="dashboardAlert" class="alert d-none"></div>

    <div class="masonry-grid" id="galleryGrid">

        <?php foreach ($images as $img): ?>

            <div class="masonry-item" id="pin-<?php echo (int)$img['id']; ?>">

                <div class="card pin-card shadow-sm border-0">

                    <img
                        src="<?php echo htmlspecialchars($img['file_path']); ?>"
                        alt="<?php echo htmlspecialchars($img['title']); ?>"
                        class="view-image"
                        data-id="<?php echo (int)$img['id']; ?>"
                        data-title="<?php echo htmlspecialchars($img['title'], ENT_QUOTES); ?>"
                        data-description="<?php echo htmlspecialchars($img['description'] ?? '', ENT_QUOTES); ?>"
                        style="cursor:pointer;"
                    >

                    <div class="card-body">

                        <h6 class="card-title pin-title">
                            <?php echo htmlspecialchars($img['title']); ?>
                        </h6>

                        <p class="card-text text-muted small pin-desc">
                            <?php echo nl2br(htmlspecialchars($img['description'] ?? '')); ?>
                        </p>

                        <div class="d-flex justify-content-end gap-2">

                            <button type="button"
                                class="btn btn-info btn-sm view-btn"
                                data-image="<?php echo htmlspecialchars($img['file_path'] ?? '', ENT_QUOTES); ?>"
                                data-title="<?php echo htmlspecialchars($img['title'] ?? '', ENT_QUOTES); ?>"
                                data-description="<?php echo htmlspecialchars($img['description'] ?? '', ENT_QUOTES); ?>">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button
                                class="btn btn-sm btn-outline-primary edit-btn"
                                data-id="<?php echo (int)$img['id']; ?>"
                                data-title="<?php echo htmlspecialchars($img['title'], ENT_QUOTES); ?>"
                                data-description="<?php echo htmlspecialchars($img['description'] ?? '', ENT_QUOTES); ?>"
                            >
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-outline-danger delete-btn"
                                data-id="<?php echo (int)$img['id']; ?>"
                            >
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
<div class="modal fade" id="imageModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <form id="imageForm" enctype="multipart/form-data">

                <div class="modal-header">

                    <h5 class="modal-title" id="imageModalLabel">
                        Add New Image
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>

                <div class="modal-body">

                    <div id="modalAlert" class="alert d-none"></div>

                    <input type="hidden" id="imageId" name="image_id">

                    <div class="mb-3">

                        <label class="form-label">Title</label>

                        <input
                            type="text"
                            class="form-control"
                            id="imgTitle"
                            name="title"
                            required
                        >

                    </div>

                    <div class="mb-3">

                        <label class="form-label">Description</label>

                        <textarea
                            class="form-control"
                            id="imgDescription"
                            name="description"
                            rows="3"
                        ></textarea>

                    </div>

                    <div class="mb-3" id="fileInputGroup">

                        <label class="form-label">Upload Image</label>

                        <input
                            type="file"
                            class="form-control"
                            id="imgFile"
                            name="image"
                            accept=".jpg,.jpeg,.png,.gif,.webp"
                        >

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary" id="saveImageBtn">
                        Save Changes
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<!-- View Image Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="viewTitle"></h5>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">

                <img id="viewImage"
                     src=""
                     class="img-fluid rounded"
                     style="max-height:500px;">

                <p id="viewDescription" class="mt-3 text-muted"></p>

            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/includes/footer.php'; ?>


<script>

$(document).ready(function() {

    let mode = 'create';

    const modal = new bootstrap.Modal(document.getElementById('imageModal'));


    // Show Alert
    function showAlert(type, message) {

        $('#dashboardAlert')
            .removeClass('d-none alert-success alert-danger')
            .addClass('alert-' + type)
            .text(message);

    }


    // Open Add Modal
    $('#openAddModalBtn').click(function() {

        mode = 'create';

        $('#imageForm')[0].reset();

        $('#imageId').val('');

        $('#imageModalLabel').text('Add New Image');

        $('#imgFile').prop('required', true);

        $('#fileInputGroup').show();

        $('#modalAlert').addClass('d-none');

        modal.show();

    });


    // Open Edit Modal
    $(document).on('click', '.edit-btn', function() {

        mode = 'edit';

        $('#imageForm')[0].reset();

        $('#imageId').val($(this).data('id'));

        $('#imgTitle').val($(this).data('title'));

        $('#imgDescription').val($(this).data('description'));

        $('#imageModalLabel').text('Edit Image');

        $('#imgFile').prop('required', false);

        $('#fileInputGroup').hide();

        $('#modalAlert').addClass('d-none');

        modal.show();

    });


    // Upload / Update Image
    $('#imageForm').submit(function(e) {

        e.preventDefault();

        let formData = new FormData(this);

        if (mode === 'create') {
            formData.append('action', 'upload');
        } else {
            formData.append('action', 'update');
        }

        let btn = $('#saveImageBtn');

        btn.prop('disabled', true).text('Saving...');

        $.ajax({

            url: 'ajax/imageCrud.php',

            type: 'POST',

            data: formData,

            contentType: false,

            processData: false,

            dataType: 'json',

            success: function(response) {

                if (response.status === 'success') {

                    modal.hide();

                    showAlert('success', response.message);

                    location.reload();

                } else {

                    $('#modalAlert')
                        .removeClass('d-none alert-success')
                        .addClass('alert-danger')
                        .text(response.message);

                }

            },

            error: function() {

                $('#modalAlert')
                    .removeClass('d-none')
                    .addClass('alert-danger')
                    .text('Something went wrong!');

            },

            complete: function() {

                btn.prop('disabled', false).text('Save Changes');

            }

        });

    });


    // Delete Image
    $(document).on('click', '.delete-btn', function() {

        if (!confirm('Are you sure you want to delete this image?')) {
            return;
        }

        let id = $(this).data('id');

        let item = $('#pin-' + id);

        $.ajax({

            url: 'ajax/imageCrud.php',

            type: 'POST',

            data: {
                action: 'delete',
                image_id: id
            },

            dataType: 'json',

            success: function(response) {

                if (response.status === 'success') {

                    showAlert('success', response.message);

                    item.fadeOut(400, function() {
                        $(this).remove();
                    });

                } else {

                    showAlert('danger', response.message);

                }

            },

            error: function() {

                showAlert('danger', 'Failed to delete image.');

            }

        });

    });

});

// View Image
$(document).on('click', '.view-btn', function () {

    let image = $(this).data('image');
    let title = $(this).data('title');
    let description = $(this).data('description');

    $('#viewImage').attr('src', image);
    $('#viewTitle').text(title);
    $('#viewDescription').text(description);

    bootstrap.Modal.getOrCreateInstance(
        document.getElementById('viewModal')
    ).show();
});

</script>
