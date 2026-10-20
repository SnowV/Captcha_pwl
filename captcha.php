<?php
session_start();

$alphabet = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
$code = '';

for ($i = 0; $i < 5; $i++) {
    $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
}

$_SESSION['code'] = $code;

$escapedCode = htmlspecialchars($code, ENT_QUOTES | ENT_XML1, 'UTF-8');
$noise = '';

for ($i = 0; $i < 6; $i++) {
    $x1 = random_int(0, 173);
    $y1 = random_int(0, 50);
    $x2 = random_int(0, 173);
    $y2 = random_int(0, 50);
    $noise .= sprintf(
        '<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#dfe6e9" stroke-opacity=".35" stroke-width="1"/>',
        $x1,
        $y1,
        $x2,
        $y2
    );
}

header('Content-Type: image/svg+xml; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

echo '<svg xmlns="http://www.w3.org/2000/svg" width="173" height="50" viewBox="0 0 173 50" role="img" aria-label="Captcha">';
echo '<rect width="173" height="50" rx="4" fill="#1656a5"/>';
echo $noise;
echo '<text x="50%" y="50%" fill="#dfe6e9" font-family="Arial, sans-serif" font-size="25" font-weight="700" letter-spacing="3" text-anchor="middle" dominant-baseline="central">' . $escapedCode . '</text>';
echo '</svg>';
