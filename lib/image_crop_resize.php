<?php
switch ($extension) {
    case 'png':
        $image = imagecreatefrompng($imagePath);
        break;
    case 'gif':
        $image = imagecreatefromgif($imagePath);
        break;
    case 'svg':
        die("SVG images are not directly supported.");
        break;
    case 'jpg':
        $image = imagecreatefromjpeg($imagePath);
        break;
    case 'jpeg':
        $image = imagecreatefromjpeg($imagePath);
        break;
    default:
        die("Unsupported image format.");
}

// Get the current dimensions of the image
$width = imagesx($image);
$height = imagesy($image);

// Set the new dimensions for the cropped image
$newWidth = $image_width;
$newHeight = $image_height;

$aspectRatio = $width / $height;

// Calculate the aspect ratio of the new dimensions
$newAspectRatio = $newWidth / $newHeight;

// Determine the crop dimensions
if ($newAspectRatio > $aspectRatio) {
    // If the new aspect ratio is wider than the original, crop the sides
    $cropWidth = $height * $newAspectRatio;
    $cropHeight = $height;
} else {
    // If the new aspect ratio is taller than the original, crop the top and bottom
    $cropWidth = $width;
    $cropHeight = $width / $newAspectRatio;
}

// Calculate the crop position to center the crop
$cropX = max(0, ($width - $cropWidth) / 2);
$cropY = max(0, ($height - $cropHeight) / 2);


// Create a new image with the desired dimensions
$newImage = imagecreatetruecolor($newWidth, $newHeight);

// Perform the crop
imagecopyresampled($newImage, $image, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $newWidth, $newHeight);

// imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $newWidth, $newHeight);

// Save the cropped image to a new file based on the original file extension
$newImagePath = $full_path_image;
switch ($extension) {
    case 'png':
        imagepng($newImage, $newImagePath);
        break;
    case 'jpg':
    case 'jpeg':
        imagejpeg($newImage, $newImagePath);
        break;
    case 'gif':
        $image = imagecreatefromgif($imagePath);
        break;   
}

// Free up memory by destroying the images
imagedestroy($image);
imagedestroy($newImage);

?>