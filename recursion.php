<?php
// recursion.php

$library = [
  "Fiction" => [
    "Fantasy" => ["Harry Potter", "The Hobbit"],
    "Mystery" => ["Sherlock Holmes", "Gone Girl"],
  ],
  "Non-Fiction" => [
    "Science"   => ["A Brief History of Time", "The Selfish Gene"],
    "Biography" => ["Steve Jobs", "Becoming"],
  ],
  "Poetry" => [
    "Modern" => ["Milk and Honey", "The Sun and Her Flowers"],
  ],
];

function displayLibrary(array $library, int $depth = 0) {
  echo str_repeat("&nbsp;", $depth * 4) . "<ul>";
  foreach ($library as $key => $val) {
    if (is_array($val)) {
      echo "<li><strong>" . htmlspecialchars($key) . "</strong></li>";
      displayLibrary($val, $depth + 1);
    } else {
      echo "<li>" . htmlspecialchars($val) . "</li>";
    }
  }
  echo "</ul>";
}

echo "<h2>Recursive Category Display</h2>";
displayLibrary($library);
?>
