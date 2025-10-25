<?php
// hashtable.php

$bookInfo = [
  "Harry Potter" => ["author" => "J.K. Rowling", "year" => 1997, "genre" => "Fantasy"],
  "The Hobbit" => ["author" => "J.R.R. Tolkien", "year" => 1937, "genre" => "Fantasy"],
  "Sherlock Holmes" => ["author" => "Arthur Conan Doyle", "year" => 1892, "genre" => "Mystery"],
  "Gone Girl" => ["author" => "Gillian Flynn", "year" => 2012, "genre" => "Thriller"],
];

function getBookInfo(string $title, array $bookInfo): ?array {
  return $bookInfo[$title] ?? null;
}

$title = $_GET['book'] ?? '';
if ($title) {
  $info = getBookInfo($title, $bookInfo);
  if ($info) {
    echo "<h2>Book Details</h2>";
    echo "<p><strong>Title:</strong> $title</p>";
    echo "<p><strong>Author:</strong> {$info['author']}</p>";
    echo "<p><strong>Year:</strong> {$info['year']}</p>";
    echo "<p><strong>Genre:</strong> {$info['genre']}</p>";
  } else {
    echo "<p>Book not found.</p>";
  }
} else {
  echo "<p>Use ?book=Harry%20Potter in the URL to test.</p>";
}
?>
