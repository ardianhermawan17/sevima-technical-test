<?php
function dd(...$vars): void
{
    header('Content-Type: application/json');
    echo json_encode($vars, JSON_PRETTY_PRINT);
    exit;
}