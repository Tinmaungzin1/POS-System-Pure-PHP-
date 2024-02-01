<?php

function convertDateFormatYMD($inputDate) {
    // Parse the input date using DateTime
    $dateTime = new DateTime($inputDate);
  
    // Format the date in the desired format
    $formattedDate = $dateTime->format("Y-m-d");
  
    return $formattedDate;
}
function convertDataFormat($inputDate){
    $dateTime = new DateTime($inputDate);
    $formattedDate = $dateTime->format("d F Y");

    return $formattedDate;
}
function convertDateFormatDMY($inputDate) {
    $dateTime = new DateTime($inputDate);
    $formattedDate = $dateTime->format("m/d/Y");
    return $formattedDate;
}
function FormatHis($inputDate) {
    $dateTime = new DateTime($inputDate);
    $formattedDate = $dateTime->format("H:i:s");
    return $formattedDate;
}
function FormatDMY($inputDate) {
    $dateTime = new DateTime($inputDate);
    $formattedDate = $dateTime->format("Ymd");
    return $formattedDate;
}
function FormatHi($inputDate) {
    $dateTime = new DateTime($inputDate);
    $formattedDate = $dateTime->format("H:i");
    return $formattedDate;
}