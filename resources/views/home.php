<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.rawgit.com/naptha/tesseract.js/1.0.10/dist/tesseract.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        #imageContainer {
            position: relative;
            width: fit-content;
        }

        #image {
            user-select: none; /* Prevent image from being selectable */
            pointer-events: none;
        }

        .selectionRegion {
            position: absolute;
            border: 1px dashed red;
            pointer-events: auto;
        }

        .highlighted {
            border-color: blue !important;
        }

        .deleteBtn {
            position: absolute;
            top: -10px;
            right: -10px;
            background-color: white;
            border: 1px solid black;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>

<body>
<div id="imageContainer">
    <img id="image" src="<?php echo asset('images/text.png'); ?>" alt="">
</div>
<button id="addBoxBtn">Add Box</button>
<button id="performOCRBtn">Perform OCR</button>

<div id="output">
    <h3>Selected Text:</h3>
    <div id="selectedText"></div>
</div>
<script>
    $(document).ready(function () {
        var $selectedText = $('#selectedText');
        var selectedRegions = [];

        // Automatically create selection boxes and lay them over the recognized text upon startup
        Tesseract.recognize(
            document.getElementById('image'),
            'eng',
            {logger: m => console.log(m)}
        ).then((data) => {
            // Group words into paragraphs or sentences
            var groups = groupText(data.words);
            groups.forEach(group => {
                var $selectionRegion = $('<div>').addClass('selectionRegion');
                $selectionRegion.css({
                    left: group.x0 + 'px',
                    top: group.y0 + 'px',
                    width: (group.x1 - group.x0) + 'px',
                    height: (group.y1 - group.y0) + 'px'
                });
                $selectionRegion.appendTo('#imageContainer').draggable({
                    containment: "#imageContainer",
                    start: function (event, ui) {
                        $(this).addClass('highlighted').siblings().removeClass('highlighted');
                    }
                }).resizable();

                var $deleteBtn = $('<div>').addClass('deleteBtn').html('&times;');
                $deleteBtn.appendTo($selectionRegion).on('click', function (event) {
                    event.stopPropagation();
                    var index = $selectionRegion.index();
                    $selectionRegion.remove();
                    selectedRegions.splice(index, 1);
                    updateOCR();
                });

                selectedRegions.push($selectionRegion);
            });
        });

        $('#addBoxBtn').on('click', function () {
            var $selectionRegion = $('<div>').addClass('selectionRegion');
            $selectionRegion.appendTo('#imageContainer').draggable({
                containment: "#imageContainer",
                start: function (event, ui) {
                    $(this).addClass('highlighted').siblings().removeClass('highlighted');
                }
            }).resizable();

            var $deleteBtn = $('<div>').addClass('deleteBtn').html('&times;');
            $deleteBtn.appendTo($selectionRegion).on('click', function (event) {
                event.stopPropagation();
                var index = $selectionRegion.index();
                $selectionRegion.remove();
                selectedRegions.splice(index, 1);
                updateOCR();
            });

            selectedRegions.push($selectionRegion);
        });

        $('#performOCRBtn').on('click', function () {
            updateOCR();
        });

        function updateOCR() {
            $selectedText.empty();
            var promises = [];
            selectedRegions.forEach(function ($region) {
                var region = {
                    x: $region.offset().left,
                    y: $region.offset().top,
                    width: $region.width(),
                    height: $region.height()
                };
                promises.push(performOCR(region));
            });

            Promise.all(promises).then(function (texts) {
                texts.forEach(function (text) {
                    $('<p>').text(text).appendTo($selectedText);
                });
            });
        }

        function performOCR(region) {
            return new Promise(function (resolve) {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var image = document.getElementById('image');
                canvas.width = image.width;
                canvas.height = image.height;
                ctx.drawImage(image, 0, 0);

                var imageData = ctx.getImageData(region.x, region.y, region.width, region.height);
                var tempCanvas = document.createElement('canvas');
                tempCanvas.width = region.width;
                tempCanvas.height = region.height;
                var tempCtx = tempCanvas.getContext('2d');
                tempCtx.putImageData(imageData, 0, 0);

                Tesseract.recognize(
                    tempCanvas,
                    'eng',
                    {logger: m => console.log(m)}
                ).then((data) => {
                    resolve(data.text);
                });
            });
        }

        // Function to group words into paragraphs or sentences
        function groupText(words) {
            var groups = [];
            var currentGroup = null;

            words.forEach(word => {
                if (!currentGroup || word.text.includes('\n')) {
                    // Start a new group if a line break is encountered or if there's no current group
                    currentGroup = {
                        words: [word],
                        x0: word.bbox.x0,
                        y0: word.bbox.y0,
                        x1: word.bbox.x1,
                        y1: word.bbox.y1
                    };
                    groups.push(currentGroup);
                } else {
                    // Add word to the current group
                    currentGroup.words.push(word);
                    // Update the bounding box of the current group
                    currentGroup.x0 = Math.min(currentGroup.x0, word.bbox.x0);
                    currentGroup.y0 = Math.min(currentGroup.y0, word.bbox.y0);
                    currentGroup.x1 = Math.max(currentGroup.x1, word.bbox.x1);
                    currentGroup.y1 = Math.max(currentGroup.y1, word.bbox.y1);
                }
            });

            return groups;
        }
    });
</script>
</body>

</html>
