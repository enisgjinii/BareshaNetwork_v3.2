<?php

// Përfshij konfigurimet dhe lidhjen me bazën e të dhënave të nevojshme këtu
include 'conn-d.php';

// Merr parametrat e përcaktuar nga përdoruesi nga kërkesa GET
$show = isset($_POST['show']) ? $_POST['show'] : 10;
$pg = isset($_POST['pg']) ? $_POST['pg'] : 1;

// Përcakto URL-në bazë për endpointin e API-së
$baseUrl = 'https://bareshamusic.sourceaudio.com/api/contentid/claims';

// Përcakto parametrat e kërkesës tuaj
$params = array(
    'token' => '6636-66f549fbe813b2087a8748f2b8243dbc',
    'show' => $show, // Numri i pretendimeve për t'u shfaqur në çdo faqe (p.sh., 10 pretendime për faqe)
    'pg' => $pg,    // Faqja e të dhënave për pretendimin për të marrë (p.sh., faqja 1)
    // Mund të shtoni më shumë parametra këtu sipas nevojës
);

// Krijo URL-në përfundimtare me parametrat e kërkesës
$url = $baseUrl . '?' . http_build_query($params);

// Bëj një kërkesë GET duke përdorur file_get_contents()
$response = file_get_contents($url);

// Kontrollo nëse përgjigja është e vlefshme
if ($response === false) {
    die('Gabim: Dështoi në marrjen e të dhënave nga API-ja');
}

// Dekodo të dhënat JSON
$data = json_decode($response, true);

// Kthe të dhënat JSON
header('Content-Type: application/json');
echo json_encode(['pretendim' => $data['pretendim']]);
