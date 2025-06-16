<?php
$path_prefix = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You Found It!</title>
    <style>
        body, html { margin: 0; padding: 0; height: 100%; overflow: hidden; background-color: #000; display: flex; justify-content: center; align-items: center; }
        video { max-width: 100vw; max-height: 100vh; width: auto; height: auto; object-fit: contain; }
        .fallback-message { color: #fff; text-align: center; font-size: 1.2em; padding: 20px; }
    </style>
</head>
<body>
    <video id="secretPlayer" controls autoplay muted playsinline poster="<?php echo $path_prefix; ?>emman.jpg">
        <source src="<?php echo $path_prefix; ?>sec-rick.mp4" type="video/mp4">
        <div class="fallback-message">
            <p>Oops! Your browser doesn't support this video format, or the video is missing.</p>
            <p>But hey, you clicked the link! 😉 Did you expect Rick Astley?</p>
        </div>
    </video>
    <script>
        const video = document.getElementById('secretPlayer');
        video.play().catch(error => { console.warn("Autoplay was prevented:", error); });
    </script>
</body>
</html>