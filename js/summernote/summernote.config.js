$(document).ready(function () {
    $('#summernote').summernote({
        height: 300,
        minHeight: null,
        lang: 'ko-KR',
        disableDragAndDrop: true,
        shortcuts: false,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture']],
            // ['insert', ['link', 'picture', 'video']],
            // ['view', ['fullscreen', 'codeview', 'help']]
        ],
        callbacks: {
            onImageUpload: function (image) {
                uploadImage(image[0], this);
            }
        }
    });
    $('.note-frame').find('.modal-body').addClass('col-md-10 col-md-offset-1');

    function uploadImage(image, elem) {
        var data = new FormData();
        data.append("image", image);
        $.ajax({
            data: data,
            type: 'POST',
            url: '/app/saveImage.php',
            cache: false,
            contentType: false,
            processData: false,
            success: function (info) {
                $(elem).summernote('insertImage', info);
            },
            error: function (jqxhr, status, error) {
                console.log(error);
            }
        });
    }
});
