<!DOCTYPE html>
<html>
<head>
    <title>Laravel Image Upload</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Laravel Image Upload</h2>
        <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#imageModal">Add Image</button>
        <table id="imageTable" class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="imageTableBody">
                @foreach($images as $image)
                    <tr id="imageRow{{ $image->id }}">
                        <td>{{ $image->id }}</td>
                        <td><img src="{{ asset('storage/' . $image->file_path) }}" width="100"></td>
                        <td>
                            <button class="btn btn-success" onclick="showUpdateModal({{ $image->id }}, '{{ asset('storage/' . $image->file_path) }}')">Edit</button>
                            <button class="btn btn-danger" onclick="deleteImage({{ $image->id }})">Delete</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">Upload Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="imageForm">
                        <div class="form-group">
                            <label for="image">Choose Image</label>
                            <input type="file" class="form-control" id="image" name="image">
                            <span class="text-danger" id="imageError"></span>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                    <div id="dropzone">
                        <label for="image">Choose Image</label>
                        <form action="#" class="dropzone" id="my-dropzone">
                            {{-- <button type="submit" class="btn btn-primary">Upload</button> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Modal -->
    <div class="modal fade" id="updateImageModal" tabindex="-1" role="dialog" aria-labelledby="updateImageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateImageModalLabel">Update Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateImageForm">
                        <div class="form-group">
                            <label for="updateImage">Choose Image</label>
                            <input type="file" class="form-control" id="updateImage" name="image">
                            <span class="text-danger" id="updateImageError"></span>
                        </div>
                        <input type="hidden" id="updateImageId">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;

        const myDropzone = new Dropzone("#my-dropzone", {
            url: "/images",
            paramName: "file",
            maxFilesize: 2, // MB
            acceptedFiles: "image/*",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            success: function (file, response) {
                console.log('File uploaded successfully:', response);
            },
            error: function (file, response) {
                console.error('Error uploading file:', response);
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#imageTable').DataTable();

            $('#imageForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                $('#imageError').text('');

                axios.post('{{ route('images.store') }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }).then(response => {
                    $('#imageModal').modal('hide');
                    $('#imageTableBody').append(`
                        <tr id="imageRow${response.data.id}">
                            <td>${response.data.id}</td>
                            <td><img src="storage/${response.data.file_path}" width="100"></td>
                            <td>
                                <button class="btn btn-success" onclick="showUpdateModal(${response.data.id}, 'storage/${response.data.file_path}')">Edit</button>
                                <button class="btn btn-danger" onclick="deleteImage(${response.data.id})">Delete</button>
                            </td>
                        </tr>
                    `);
                }).catch(error => {
                    if (error.response && error.response.data && error.response.data.errors) {
                        $('#imageError').text(error.response.data.errors.image[0]);
                    } else {
                        console.error('Upload error', error);
                        $('#imageError').text('Internal Server Error');
                    }
                });
            });

            $('#updateImageForm').submit(function(e) {
                e.preventDefault();
                let formData = new FormData(this);
                let id = $('#updateImageId').val();
                $('#updateImageError').text('');

                axios.post('{{ route('images.store') }}', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-HTTP-Method-Override': 'POST'
                    }
                }).then(response => {
                    $('#updateImageModal').modal('hide');
                    $(`#imageRow${id} img`).attr('src', `storage/${response.data.file_path}`);
                }).catch(error => {
                    if (error.response && error.response.data && error.response.data.errors) {
                        $('#updateImageError').text(error.response.data.errors.image[0]);
                    } else {
                        console.error('Update error', error);
                        $('#updateImageError').text('Internal Server Error');
                    }
                });
            });
        });

        function showUpdateModal(id, imagePath) {
            $('#updateImageId').val(id);
            $('#updateImageModal').modal('show');
        }

        function deleteImage(id) {
            axios.delete(`/images/${id}`, {
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).then(response => {
                $(`#imageRow${id}`).remove();
            }).catch(error => {
                console.error(error);
            });
        }
    </script>
</body>
</html>
