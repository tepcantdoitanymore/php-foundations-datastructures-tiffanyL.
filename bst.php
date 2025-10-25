<?php
// bst.php

class Node {
  public string $data;
  public ?Node $left;
  public ?Node $right;

  public function __construct(string $data) {
    $this->data = $data;
    $this->left = $this->right = null;
  }
}

class BinarySearchTree {
  public ?Node $root = null;

  public function insert(string $data): void {
    $this->root = $this->insertRec($this->root, $data);
  }

  private function insertRec(?Node $node, string $data): Node {
    if ($node === null) return new Node($data);
    $cmp = strcasecmp($data, $node->data);
    if ($cmp < 0) $node->left = $this->insertRec($node->left, $data);
    elseif ($cmp > 0) $node->right = $this->insertRec($node->right, $data);
    return $node;
  }

  public function search(string $data): bool {
    return $this->searchRec($this->root, $data);
  }

  private function searchRec(?Node $node, string $data): bool {
    if ($node === null) return false;
    $cmp = strcasecmp($data, $node->data);
    if ($cmp === 0) return true;
    return $cmp < 0 ? $this->searchRec($node->left, $data)
                    : $this->searchRec($node->right, $data);
  }

  public function inorder(): array {
    $out = [];
    $this->inorderRec($this->root, $out);
    return $out;
  }

  private function inorderRec(?Node $node, array &$out): void {
    if ($node === null) return;
    $this->inorderRec($node->left, $out);
    $out[] = $node->data;
    $this->inorderRec($node->right, $out);
  }
}

// Example usage
$bst = new BinarySearchTree();
$books = ["Harry Potter", "Becoming", "Gone Girl", "The Hobbit", "A Brief History of Time"];
foreach ($books as $book) $bst->insert($book);

echo "<h2>BST Inorder Traversal (Alphabetical)</h2><ul>";
foreach ($bst->inorder() as $title) {
  echo "<li>$title</li>";
}
echo "</ul>";

$q = $_GET['q'] ?? '';
if ($q) {
  echo $bst->search($q)
    ? "<p><strong>$q</strong> found in BST.</p>"
    : "<p><strong>$q</strong> not found.</p>";
} else {
  echo "<p>Try ?q=The%20Hobbit in the URL.</p>";
}
?>
