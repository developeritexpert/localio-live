@extends('admin_layout.master')
@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head d-flex justify-content-between">
        <div class="nk-block-head-content">
            <h4 class="title nk-block-title">
                Manage Category Topics
            </h4>
        </div>
    </div>

    <div class="card card-bordered">
        <div class="card-inner">
            <h5 class="title mb-3">
                <span id="form-title">Add New Topic</span> under: <strong>{{ $topic_data['name'] }}</strong>
            </h5>

            <form id="topic-form" class="form-validate" novalidate>
                @csrf
                <input type="hidden" name="category_id" value="{{ $topic_data['id'] }}">
                <input type="hidden" name="topic_id" id="topic-id" value="">

                <div class="row g-gs">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Topic Title</label>
                            <div class="form-control-wrap">
                                <input type="text" name="title" id="topic-title" class="form-control" placeholder="Enter topic title">
                            </div>
                            <div class="text-danger small error-title"></div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary btn-localio" id="submit-button">
                                <span id="submit-icon">
                                    <em class="icon ni ni-plus"></em>
                                </span>
                                <span id="submit-text">Add Topic</span>
                                <span class="spinner-border spinner-border-sm text-light ms-2 d-none" id="loading-spinner" role="status"></span>
                            </button>

                            
                            <button type="button" id="cancel-edit" class="btn btn-lg btn-secondary d-none ms-2">
                                <em class="icon ni ni-cross"></em> Cancel
                            </button>
                        </div>
                        <div id="topic-success" class="alert alert-success mt-2 d-none">
                            <em class="icon ni ni-check-circle"></em> 
                            <span id="success-message">Topic added successfully!</span>
                        </div>
                        <div id="topic-error" class="alert alert-danger mt-2 d-none">
                            <em class="icon ni ni-cross-circle"></em> 
                            <span id="error-message">Something went wrong!</span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-bordered mt-4 {{ $topic_data->topics->isEmpty() ? 'd-none' : '' }}" id="existing-topics-card">
        <div class="card-inner">
            <h6 class="title mb-3">
                Existing Topics 
                <span class="badge badge-light ms-2" id="topic-count">{{ $topic_data->topics->count() }}</span>
            </h6>
            <ul class="gy-2" id="topic-list">
                @foreach($topic_data->topics as $topic)
                    <li class="d-flex align-items-center justify-content-between border-bottom py-2" data-id="{{ $topic->id }}">
                        <span class="topic-title">{{ $topic->translations->first()?->title ?? 'No title' }}</span>
                        <div class="topic-actions">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary me-2 edit-topic" 
                                    data-id="{{ $topic->id }}"
                                    data-title="{{ $topic->translations->first()?->title ?? '' }}">
                                <em class="icon ni ni-edit"></em> Edit
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-danger remove-topic" 
                                    data-id="{{ $topic->id }}"
                                    data-title="{{ $topic->translations->first()?->title ?? '' }}">
                                <em class="icon ni ni-trash"></em> Remove
                            </button>
                        </div>
                    </li>
                @endforeach
            </ul>
            <div class="text-center py-3 d-none" id="no-topics-message">
                <em class="icon ni ni-inbox text-muted" style="font-size: 2rem;"></em>
                <p class="text-muted mt-2">No topics found</p>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete "<span id="delete-topic-title"></span>"?</p>
                <p class="text-muted small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">
                    <em class="icon ni ni-trash"></em> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let isEditing = false;
    let editingTopicId = null;

    // Handle form submission (Add/Edit)
    $('#topic-form').on('submit', function (e) {
    e.preventDefault();

    const $form = $(this);
    const formData = $form.serialize();
    const url = isEditing ? "{{ route('update-topic-category') }}" : "{{ route('store-topic-category') }}";
    const method ='POST';

    // Show loading inside button
    $('#loading-spinner').removeClass('d-none');
    $('#submit-text').text(isEditing ? 'Updating...' : 'Adding...');
    $('.text-danger').text('');
    $('#topic-success, #topic-error').addClass('d-none');
    $('#submit-button').prop('disabled', true);

    $.ajax({
        url: url,
        method: method,
        data: formData,
        success: function (res) {
            handleSuccess(res, $form); // ✅ passing $form here
        },
        error: function (xhr) {
            handleError(xhr);
        },
        complete: function () {
            $('#loading-spinner').addClass('d-none');
            $('#submit-text').text(isEditing ? 'Update Topic' : 'Add Topic');
            $('#submit-button').prop('disabled', false);
        }
    });
});
    // Handle success response
    function handleSuccess(res, $form) {
    if (isEditing) {
        const $topicItem = $(`li[data-id="${editingTopicId}"]`);
        $topicItem.find('.topic-title').text(res.topic.title);
        $topicItem.find('.edit-topic').attr('data-title', res.topic.title);
        $topicItem.find('.remove-topic').attr('data-title', res.topic.title);

        $('#success-message').text('Topic updated successfully!');
        resetForm();
    } else {
        if (res.topic) {
            $('#existing-topics-card').removeClass('d-none');
            $('#no-topics-message').addClass('d-none');

            const html = `
                <li class="d-flex align-items-center justify-content-between border-bottom py-2" data-id="${res.topic.id}">
                    <span class="topic-title">${res.topic.title}</span>
                    <div class="topic-actions">
                        <button type="button" class="btn btn-sm btn-outline-primary me-2 edit-topic"
                            data-id="${res.topic.id}" data-title="${res.topic.title}">
                            <em class="icon ni ni-edit"></em> Edit
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-topic"
                            data-id="${res.topic.id}" data-title="${res.topic.title}">
                            <em class="icon ni ni-trash"></em> Remove
                        </button>
                    </div>
                </li>`;
            $('#topic-list').append(html);
            updateTopicCount();
        }

        $('#success-message').text('Topic added successfully!');
        $form[0].reset();
    }

    $('#topic-success').removeClass('d-none');
    setTimeout(() => {
        $('#topic-success').addClass('d-none');
    }, 3000);
}

    // Handle error response
    function handleError(xhr) {
        if (xhr.status === 422) {
            const errors = xhr.responseJSON.errors;
            if (errors.title) {
                $('.error-title').text(errors.title[0]);
            }
        } else {
            $('#error-message').text(xhr.responseJSON?.message || 'Something went wrong!');
            $('#topic-error').removeClass('d-none');
            
            // Auto-hide error message after 5 seconds
            setTimeout(() => {
                $('#topic-error').addClass('d-none');
            }, 5000);
        }
    }

    // Handle edit topic
    $(document).on('click', '.edit-topic', function (e) {
        e.preventDefault();
        
        const topicId = $(this).data('id');
        const topicTitle = $(this).data('title');
        
        // Set edit mode
        isEditing = true;
        editingTopicId = topicId;
        
        // Update UI
        $('#topic-id').val(topicId);
        $('#topic-title').val(topicTitle);
        $('#form-title').text('Edit Topic');
        $('#submit-text').text('Update Topic');
        $('#topic-form button[type="submit"] em').removeClass('ni-plus').addClass('ni-edit');
        $('#cancel-edit').removeClass('d-none');
        
        // Scroll to form
        $('html, body').animate({
            scrollTop: $("#topic-form").offset().top - 100
        }, 500);
        
        // Focus on title input
        $('#topic-title').focus();
    });

    // Handle cancel edit
    $('#cancel-edit').on('click', function () {
        resetForm();
    });

    // Handle remove topic
    $(document).on('click', '.remove-topic', function (e) {
        e.preventDefault();
        
        const topicId = $(this).data('id');
        const topicTitle = $(this).data('title');
        
        // Show confirmation modal
        $('#delete-topic-title').text(topicTitle);
        $('#confirm-delete').data('id', topicId);
        $('#deleteModal').modal('show');
    });

    // Handle confirm delete
    $('#confirm-delete').on('click', function () {
        const topicId = $(this).data('id');
        const $deleteBtn = $(this);
        
        // Show loading state on delete button
        $deleteBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Deleting...');
        
        $.ajax({
            url: "{{ route('delete-topic-category') }}",
            method: 'GET',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                topic_id: topicId
            },
            success: function (res) {
                // Remove topic from list with animation
                $(`li[data-id="${topicId}"]`).fadeOut(300, function() {
                    $(this).remove();
                    updateTopicCount();
                    
                    // Show no topics message if list is empty
                    if ($('#topic-list li').length === 0) {
                        $('#existing-topics-card').addClass('d-none');
                        $('#no-topics-message').removeClass('d-none');
                    }
                });
                
                // Show success message
                $('#success-message').text('Topic deleted successfully!');
                $('#topic-success').removeClass('d-none');
                setTimeout(() => {
                    $('#topic-success').addClass('d-none');
                }, 3000);
                
                // Hide modal
                $('#deleteModal').modal('hide');
            },
            error: function (xhr) {
                $('#error-message').text(xhr.responseJSON?.message || 'Failed to delete topic!');
                $('#topic-error').removeClass('d-none');
                setTimeout(() => {
                    $('#topic-error').addClass('d-none');
                }, 5000);
            },
            complete: function() {
                // Reset delete button state
                $deleteBtn.prop('disabled', false).html('<em class="icon ni ni-trash"></em> Delete');
            }
        });
    });

    // Reset form to add mode
    function resetForm() {
        isEditing = false;
        editingTopicId = null;
        
        $('#topic-form')[0].reset();
        $('#topic-id').val('');
        $('#form-title').text('Add New Topic');
        $('#submit-text').text('Add Topic');
        $('#topic-form button[type="submit"] em').removeClass('ni-edit').addClass('ni-plus');
        $('#cancel-edit').addClass('d-none');
        $('.text-danger').text('');
        $('#topic-success, #topic-error').addClass('d-none');
    }

    // Update topic count
    function updateTopicCount() {
        const count = $('#topic-list li').length;
        $('#topic-count').text(count);
    }

    // Clear alerts when typing
    $('#topic-title').on('input', function() {
        $('.error-title').text('');
        $('#topic-success, #topic-error').addClass('d-none');
    });
});
</script>



@endsection