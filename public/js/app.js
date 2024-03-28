$(document).ready(function () {
    const $selected_text = $('#selected_text');
    const selected_regions = [];

    // auto grouping

    $('#auto_ocr_grouping_btn').on('click', function () {
        const $image = document.getElementById('image');

        // here we start tesseract and ask it to scan the image in english
        // then we use the data to process it

        Tesseract
            .recognize($image, 'eng')
            .then(function ({ data }) {
                // get the grouped texts from tesseract ocr
                let groups = group_text(data.words);

                groups.forEach((group) => {
                    const padding = 5; // adjust this value according to your need

                    // increase the coordinates by padding

                    group.x0 -= padding;
                    group.y0 -= padding;
                    group.x1 += padding;
                    group.y1 += padding;

                    const $selection_region = $('<div>').addClass('selection_region');

                    // set it's placement

                    $selection_region.css({
                        left: group.x0 + 'px',
                        top: group.y0 + 'px',
                        width: (group.x1 - group.x0) + 'px',
                        height: (group.y1 - group.y0) + 'px'
                    });

                    // add to selection region array

                    $selection_region.appendTo('#image_container')
                        .draggable({
                            containment: '#image_container',
                            start: function () {
                                $(this)
                                    .addClass('highlighted')
                                    .siblings()
                                    .removeClass('highlighted');
                            }
                        })
                        .resizable();

                    // create the delete button for the selection region
                    // we can delete the region, so it doesn't have to be processed by the ocr tesseract

                    const $delete_btn = $('<div>')
                        .addClass('delete_btn')
                        .data('selection_region', $selection_region)
                        .html('&times;');

                    // add delete button to selection region

                    $delete_btn
                        .appendTo($selection_region)
                        .on('click', function (event) {
                            event.stopPropagation();

                            const $region = $(this).data('selection_region');

                            // delete selection region

                            $region.remove();
                            selected_regions.splice(selected_regions.indexOf($region), 1);
                        })

                    // add the region to selected regions

                    selected_regions.push($selection_region);
                })
            })
    })

    // add region by clicking on image container

    $('#image_container').on('click', function (event) {
        if ($(this).hasClass('selecting')) {
            const $selection_region = $('<div>').addClass('selection_region');

            // Remove class when selection is complete
            $(this).removeClass('selecting');

            // Calculate position relative to image container
            const containerOffset = $(this).offset();
            const relativeX = event.pageX - containerOffset.left;
            const relativeY = event.pageY - containerOffset.top;

            // Set position of selection region
            $selection_region.css({
                left: relativeX + 'px',
                top: relativeY + 'px'
            });

            // add to selection region array
            $selection_region.appendTo('#image_container')
                .draggable({
                    containment: '#image_container',
                    start: function () {
                        $(this)
                            .addClass('highlighted')
                            .siblings()
                            .removeClass('highlighted');
                    }
                })
                .resizable();

            // create the delete button for the selection region
            // we can delete the region, so it doesn't have to be processed by the ocr tesseract
            const $delete_btn = $('<div>')
                .addClass('delete_btn')
                .data('selection_region', $selection_region)
                .html('&times;');

            // add delete button to selection region
            $delete_btn
                .appendTo($selection_region)
                .on('click', function (event) {
                    event.stopPropagation();

                    const $region = $(this).data('selection_region');

                    // delete selection region
                    $region.remove();
                    selected_regions.splice(selected_regions.indexOf($region), 1);
                });

            // add the region to selected regions
            selected_regions.push($selection_region);
        }
    });

    // toggle selecting class when add box button is clicked

    $('#add_box_btn').on('click', function () {
        $('#image_container').toggleClass('selecting');
    });

    // perform ocr

    $('#perform_ocr_btn').on('click', () => update_ocr());

    // update ocr

    function update_ocr() {
        $selected_text.empty();

        const promises = [];

        selected_regions.forEach(function ($region) {
            const region = {
                x: $region.offset().left,
                y: $region.offset().top,
                width: $region.width(),
                height: $region.height()
            };

            promises.push(sendRegionToBackend(region));
        });

        Promise
            .all(promises)
            .then(function (responses) {
                responses.forEach(function (response) {
                    $('<p>')
                        .text(response.text)
                        .appendTo($selected_text);
                });
            });
    }

    // send region to backend for processing

    function sendRegionToBackend(region) {
        return new Promise(function (resolve, reject) {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            // set width and height of the canvas

            const image = $('#image').get(0);

            canvas.width = image.width;
            canvas.height = image.height;

            ctx.drawImage(image, 0, 0);

            // prepare image

            const temp_canvas = document.createElement('canvas');

            temp_canvas.width = region.width;
            temp_canvas.height = region.height;

            const image_data = ctx.getImageData(region.x, region.y, region.width, region.height);

            temp_canvas
                .getContext('2d')
                .putImageData(image_data, 0, 0);

            // Convert canvas to data URL
            const imageDataUrl = temp_canvas.toDataURL();

            // Send region data to backend
            $.ajax({
                url: '/ocr/process',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ image_data: imageDataUrl }),
                success: function (response) {
                    console.log(response)

                    resolve(response);
                },
                error: function (xhr, status, error) {
                    reject(error);
                }
            });
        });
    }

    // get the grouped paragraphs/texts from the ocr

    function group_text(words) {
        const groups = [];
        let current_group = null;

        words.forEach(word => {
            if (!current_group || word.text.includes('\n')) {

                // start a new group if a line break is encountered or if there's no current group

                current_group = {
                    words: [word],
                    x0: word.bbox.x0,
                    y0: word.bbox.y0,
                    x1: word.bbox.x1,
                    y1: word.bbox.y1
                };

                groups.push(current_group);
            } else {
                // add word to the current group
                current_group.words.push(word);

                // update the bounding box of the current group

                current_group.x0 = Math.min(current_group.x0, word.bbox.x0);
                current_group.y0 = Math.min(current_group.y0, word.bbox.y0);
                current_group.x1 = Math.max(current_group.x1, word.bbox.x1);
                current_group.y1 = Math.max(current_group.y1, word.bbox.y1);
            }
        });

        return groups;
    }
});

