jQuery(function ($) {
    (function (e) {
        const GOODOC_FILES = $('#goodoc-files .goodoc-files__single-file');
        let importButton = $('button#goodoc-start-import');

        // Document selection
        GOODOC_FILES.click(function () {

            importButton.prop('disabled', true);

            if ($(this).hasClass('active')) {
                $(this).removeClass('active')
            } else {
                GOODOC_FILES.removeClass('active');
                $(this).addClass('active');
                importButton.prop('disabled', false);
            }
        });

        function getSelectedDocData() {
            let theDocData = {};

            GOODOC_FILES.each(function (index, value) {
                if (value.classList.contains('active')) {
                    theDocData = value.dataset;
                }
            });

            return theDocData;
        }

        // Import AJAX call
        importButton.click(function () {
            let docData = getSelectedDocData();
            let spinner = $('#doc-import-spinner');
            spinner.addClass('is-active');

            $.ajax({
                url: GOODOC_AJAX.url,
                type: 'post',
                data: {
                    action: 'import_google_document',
                    doc_title: docData.docTitle,
                    doc_id: docData.docId,
                    nonce: GOODOC_AJAX.nonce
                },
                success: function (response) {
                    console.log(response);
                    let data = response.data;
                    spinner.removeClass('is-active');
                    let newPostUrl = data.editUrl.replaceAll('&amp;', '&');
                    if (newPostUrl) {
                        window.location.replace(newPostUrl);
                    }
                },
                error: function (response) {
                    spinner.removeClass('is-active');
                    console.log('error', response);
                    alert('An error occurred while importing the document. Please try again later.');
                },
            });
        });


    })()
})