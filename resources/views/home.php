<!doctype html>
<html lang="en">

<head>
    <?php echo view('partials.header')->render() ?>

    <title>Document</title>
</head>

<body>

<div id="image_container">
    <img id="image" src="<?php echo asset('images/page_1.jpg'); ?>" alt="">
</div>

<button id="add_box_btn">Add Box</button>
<button id="perform_ocr_btn">Perform OCR</button>
<button id="auto_ocr_grouping_btn">Auto OCR Grouping</button>

<div id="output">
    <h3>Selected Text:</h3>
    <div id="selected_text"></div>
</div>

<?php echo view('partials.footer')->render() ?>

</body>

</html>