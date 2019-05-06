const $ = require('jquery');
const SHA512 = require('crypto-js/sha512');
const Papa = require('papaparse');

const parseFile = file => {
    return new Promise((resolve, reject) => {
        Papa.parse(file, {
            delimiter: ',',
            complete(results) {
                if (results.errors.length > 0) return reject(results.errors);
                return resolve(results.data);
            }
        });
    });
};

$('.custom-file-input').on('change',function(){
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
    $(this).removeClass('is-invalid')
});

$('form[name="match_job_form"]').submit(function (e) {
    e.preventDefault();

    const $form = $(this);
    const $inputFile = $('#hash_file');

    if (!$inputFile[0].files[0] || !$inputFile[0].files[0].name.endsWith('.csv')) {
        $inputFile.addClass('is-invalid');
        return
    }

    parseFile($inputFile[0].files[0]).then(result => {

        if (result.length > 50000) {
            $inputFile.addClass('is-invalid');
            return
        }

        const hashData = [];

        for (let i = 0; i < result.length; i++) {
            hashData.push([SHA512(result[i][0]).toString()])
        }

        const hashDataString = Papa.unparse(hashData);

        $('#match_job_form_fileData').val(hashDataString);

        $form.off('submit');
        $form.submit();
    });
});

$('#compare_files').click(function (e) {

    const $originalFile = $('#original_file');
    const $hashedFile = $('#hashed_file');

    if (!$originalFile[0].files[0] || $originalFile[0].files[0].type !== 'text/csv') {
        $originalFile.addClass('is-invalid');
        return
    }

    if (!$hashedFile[0].files[0] || $hashedFile[0].files[0].type !== 'text/csv') {
        $hashedFile.addClass('is-invalid');
        return
    }

    Promise.all([
        parseFile($originalFile[0].files[0]),
        parseFile($hashedFile[0].files[0])
    ]).then(results => {
        const emailsHashes = results[0].map(value => [value[0], SHA512(value[0]).toString()]);

        const emailsInTheList = results[1].reduce((emailsInTheList, current) => {
            const foundValue = emailsHashes.find(emailHash => emailHash[1] === current[0]);
            if (foundValue) emailsInTheList.push([foundValue[0]]);

            return emailsInTheList;
        }, []);

        const csvData = Papa.unparse(emailsInTheList);

        const a = window.document.createElement('a');
        a.href = window.URL.createObjectURL(new Blob([csvData], {type: 'text/csv'}));
        a.download = 'coincidencias.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        $('#modalDecode').modal('hide')
    });

});
