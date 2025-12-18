<?php
/* get site info */
function getSiteInfo()
{
    $data = [
        'site_name'       => '',
        'logo'            => '',
        'favicon'         => '',
        'copyright'       => '',
        'phone'           => '',
        'address'         => '',
        'email'           => '',

        'whatsapp'        => '',
        'skype'           => '',
        'google_plus'     => '',
        'facebook'        => '',
        'twitter'         => '',
        'instagram'       => '',
        'linkedin'        => '',
        'youtube'         => '',

        'meta_title'      => '',
        'meta_keywords'   => '',
        'meta_description'=> '',

        'placeholder'     => '',
    ];

    $siteInfo = \Illuminate\Support\Facades\DB::table('settings')->get();
    if (!empty($siteInfo)) {
        foreach ($siteInfo as $row) {
            $data[$row->meta_key] = $row->meta_value;
        }
    }
    return (object)$data;
}

function strFilter($text = '')
{
    if (!empty($text)) {
        $text = trim($text);
        if (mb_detect_encoding($text) == 'UTF-8') {
            $text = str_replace('_', ' ', $text);
        } else {
            $text = ucwords(str_replace('_', ' ', $text));
        }
        return $text;
    }
    return 'N/A';
}

function strClean($text = '')
{
    if (!empty($text)) {
        $text = trim($text);
        if (mb_detect_encoding($text) == 'UTF-8') {
            $text = str_replace(' ', '', $text);
        } else {
            $text = ucwords(str_replace(' ', '', $text));
        }
        return preg_replace('/[^A-Za-z0-9\-]/', '', $text);
    }
}

function strSlug($text = '')
{
    if (!empty($text)) {
        $text = trim($text);
        if (mb_detect_encoding($text) == 'UTF-8') {
            $text = str_replace(' ', '-', $text);
        } else {
            $text = str_replace(' ', '-', strtolower($text));
        }
        return str_replace('&', 'and', $text);
    }
}

function strLimit($text, $count, $prefix = "")
{
    $text   = str_replace("  ", " ", strip_tags($text));
    $string = explode(" ", $text);
    $stringCount = count($string) - 1;
    $wordCount = $count - 1;
    $trimed = "";
    $count  = ($stringCount > $wordCount) ? $wordCount : $stringCount;
    for ($i = 0; $i <= $count; $i++) {
        $trimed .= $string[$i];
        if ($i < $count) {
            $trimed .= " ";
        }
    }
    if ($stringCount > $wordCount) {
        $trimed .= $prefix;
    }
    $trimed = trim($trimed);
    return $trimed;
}

function uploadFile($sourcePath = null, $uploadPath = '', $prefix = null)
{
    if (!empty($sourcePath) && !empty($uploadPath)) {
        if (!is_dir(public_path($uploadPath))) mkdir(public_path($uploadPath), 0755, true);
        
        $fileInfo = $sourcePath->getClientOriginalName();
        $extension = pathinfo($fileInfo, PATHINFO_EXTENSION);
        $filename = (!empty($prefix) ? $prefix . '-' : '') . date('ymd') . rand(100000, 999999) . '.' . $extension;
        $sourcePath->move(public_path($uploadPath), $filename);
        
        return $uploadPath . '/' . $filename;
    }
    return false;
}

function uploadImage($sourcePath = null, $uploadPath = '', $maxWidth = 0, $maxHeight = 0, $quality = 100, $prefix = null)
{
    if (!empty($sourcePath) && !empty($uploadPath)) {
        if (!is_dir(public_path($uploadPath))) mkdir(public_path($uploadPath), 0755, true);

        $mimeType = $sourcePath->getMimeType();
        list($imgWidth, $imgHeight) = getimagesize($sourcePath);
        $fileName = (!empty($prefix) ? $prefix . '-' : '') . date('ymd') . rand(100000, 999999) . '.webp';
        if ($mimeType == 'image/jpeg') {
            $sourceImage = imagecreatefromjpeg($sourcePath);
        } elseif ($mimeType == 'image/png') {
            $sourceImage = imagecreatefrompng($sourcePath);
        } elseif ($mimeType == 'image/webp') {
            $sourceImage = imagecreatefromwebp($sourcePath);
        } elseif ($mimeType == 'image/gif') {
            $sourceImage = imagecreatefromgif($sourcePath);
        } else {
            return false;
        }
        imagepalettetotruecolor($sourceImage);
        if (!empty($maxWidth) && !empty($maxHeight)) {
            if ($imgWidth > $imgHeight) {
                if ($imgWidth > $maxWidth) {
                    $newWidth  = $maxWidth;
                    $newHeight = ($imgHeight / $imgWidth) * $maxWidth;
                } else {
                    $newWidth  = $imgWidth;
                    $newHeight = $imgHeight;
                }
            } else {
                if ($imgHeight > $maxHeight) {
                    $newHeight = $maxHeight;
                    $newWidth  = ($imgWidth / $imgHeight) * $maxHeight;
                } else {
                    $newWidth  = $imgWidth;
                    $newHeight = $imgHeight;
                }
            }
            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $imgWidth, $imgHeight);
            $sourceImage = $newImage;
            $newImage = imagecreatetruecolor($maxWidth, $maxHeight);
            $x = ($newWidth - $maxWidth) / 2;
            $y = ($newHeight - $maxHeight) / 2;
            imagecopyresampled($newImage, $sourceImage, 0, 0, $x, $y, $maxWidth, $maxHeight, $maxWidth, $maxHeight);
            $sourceImage = $newImage;
        }
        $imageTo = public_path($uploadPath) . '/' . $fileName;
        $quality = (!empty($quality) ? $quality : 100);
        // Convert the image to WebP format
        if ($sourceImage !== false && imagewebp($sourceImage, $imageTo, $quality)) {
            if (is_resource($sourceImage) || $sourceImage instanceof \GdImage) {
                imagedestroy($sourceImage);
            }
            return $uploadPath . '/' . $fileName;
        }
    }
    return false;
}