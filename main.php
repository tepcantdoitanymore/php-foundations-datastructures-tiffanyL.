<?php
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

$bookInfo = [
  "Harry Potter"               => ["author" => "J.K. Rowling",       "year" => 1997, "genre" => "Fantasy"],
  "The Hobbit"                 => ["author" => "J.R.R. Tolkien",     "year" => 1937, "genre" => "Fantasy"],
  "Sherlock Holmes"            => ["author" => "Arthur Conan Doyle", "year" => 1892, "genre" => "Mystery"],
  "Gone Girl"                  => ["author" => "Gillian Flynn",      "year" => 2012, "genre" => "Thriller"],
  "A Brief History of Time"    => ["author" => "Stephen Hawking",    "year" => 1988, "genre" => "Science"],
  "The Selfish Gene"           => ["author" => "Richard Dawkins",    "year" => 1976, "genre" => "Science"],
  "Steve Jobs"                 => ["author" => "Walter Isaacson",    "year" => 2011, "genre" => "Biography"],
  "Becoming"                   => ["author" => "Michelle Obama",     "year" => 2018, "genre" => "Biography"],
  "Milk and Honey"             => ["author" => "Rupi Kaur",          "year" => 2014, "genre" => "Poetry"],
  "The Sun and Her Flowers"    => ["author" => "Rupi Kaur",          "year" => 2017, "genre" => "Poetry"],
];

function getBookInfo(string $title, array $bookInfo): ?array {
  return $bookInfo[$title] ?? null;
}

function collectTitles(array $node, array &$out) {
  foreach ($node as $key => $val) {
    if (is_array($val)) {
      if (!empty($val) && array_keys($val) === range(0, count($val) - 1)) {
        foreach ($val as $title) $out[] = $title;
      } else {
        collectTitles($val, $out);
      }
    }
  }
}

function displayLibrary(array $library, int $depth = 0) {
  echo '<ul class="tree">';
  foreach ($library as $key => $val) {
    if (is_array($val)) {

        $id = 'node_' . md5($key . '|' . $depth . '|' . count($val));
      echo '<li class="branch">';
      echo '<button class="toggle" data-target="#' . $id . '" aria-expanded="true">[-]</button>';
      echo '<span class="label cat">' . htmlspecialchars($key) . '</span>';
      echo '<div id="' . $id . '" class="children open">';
      if (!empty($val) && array_keys($val) === range(0, count($val) - 1)) {
        echo '<ul class="leaf-books">';
        foreach ($val as $book) {
          $link = '?book=' . urlencode($book);
          echo '<li class="book"><a class="chip" href="' . $link . '">' . htmlspecialchars($book) . '</a></li>';
        }
        echo '</ul>';
      } else {
        displayLibrary($val, $depth + 1);
      }
      echo '</div>';
      echo '</li>';
    } else {
      $link = '?book=' . urlencode($val);
      echo '<li class="book"><a class="chip" href="' . $link . '">' . htmlspecialchars($val) . '</a></li>';
    }
  }
  echo '</ul>';
}

class Node {
  public string $data;
  public ?Node $left;
  public ?Node $right;
  public function __construct(string $data) {
    $this->data  = $data;
    $this->left  = null;
    $this->right = null;
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
    if ($cmp < 0)      $node->left  = $this->insertRec($node->left,  $data);
    elseif ($cmp > 0)  $node->right = $this->insertRec($node->right, $data);
    return $node;
  }

  public function search(string $data): bool {
    return $this->searchRec($this->root, $data);
  }
  private function searchRec(?Node $node, string $data): bool {
    if ($node === null) return false;
    $cmp = strcasecmp($data, $node->data);
    if ($cmp === 0) return true;
    if ($cmp < 0) return $this->searchRec($node->left, $data);
    return $this->searchRec($node->right, $data);
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

$titles = [];
collectTitles($library, $titles);
$bst = new BinarySearchTree();
foreach ($titles as $t) $bst->insert($t);

$selectedBook = isset($_GET['book']) ? trim($_GET['book']) : '';
$searchQuery  = isset($_GET['q'])    ? trim($_GET['q'])    : '';

$searchHit   = null;
$searchTried = false;
if ($searchQuery !== '') {
  $searchTried = true;
  $searchHit   = $bst->search($searchQuery);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Digital Library Organizer</title>
<style>

  :root{
    --cream:      #fff7ef;  
    --paper:      #fffdf9;   
    --ink:        #3f2a33;   
    --muted:      #7c5f69;   
    --pink:       #f7b6c8;   
    --pink-100:   #ffe6ef;  
    --pink-200:   #ffd5e1; 
    --lavender:   #cdb4ff;   
    --shadow:     0 12px 30px rgba(247,182,200,0.28);
    --ring:       0 0 0 3px rgba(205,180,255,0.35);
    --border:     #f2c6d3;
  }

  * { box-sizing: border-box; }
  html, body {
    margin: 0;
    background:
      radial-gradient(900px 400px at 10% -10%, #fff1ea, transparent),
      radial-gradient(900px 400px at 90% -10%, #ffeef6, transparent),
      var(--cream);
    color: var(--ink);
    font: 15px/1.5 ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Apple Color Emoji", "Segoe UI Emoji";
  }

  .app {
    display: grid;
    grid-template-columns: 330px 1fr;
    gap: 18px;
    padding: 18px;
    min-height: 100vh;
  }
  @media (max-width: 980px) { .app { grid-template-columns: 1fr; } }

  .card {
    background: linear-gradient(180deg, var(--paper), #fffaf6);
    border: 2px solid var(--pink-200);
    border-radius: 16px;
    padding: 16px;
    box-shadow: var(--shadow);
  }

  .header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 8px;
  }
    
  h1 {
    font-size: 18px; margin: 0; letter-spacing: .2px; color: #4b3340;
  }
  .muted { color: var(--muted); font-size: 13px; }

  .tree { list-style: none; padding-left: 0; margin: 0; }
  .tree .branch { margin: 6px 0; }
  .toggle{
    background: #fff;
    color: #4b3340;
    border: 2px solid var(--lavender);
    border-radius: 9px;
    padding: 2px 8px;
    cursor: pointer;
    margin-right: 8px;
    font-weight: 700;
    box-shadow: 0 2px 0 rgba(205,180,255,.45);
  }
  .toggle:focus-visible { outline: none; box-shadow: var(--ring); }
  .label.cat{
    font-weight: 700; color: #4b3340;
    border-left: 4px solid var(--pink);
    padding-left: 8px;
  }
  .children { margin-left: 28px; display: none; }
  .children.open { display: block; }

  .leaf-books{
    list-style: none; padding-left: 0; margin: 6px 0 10px 0;
    display: grid; grid-template-columns: repeat(2,minmax(120px,1fr)); gap: 6px 10px;
  }
  @media (max-width: 480px){ .leaf-books{ grid-template-columns: 1fr; } }

  .chip{
    display: inline-block;
    text-decoration: none;
    color: #4b3340;
    background: #fff;
    border: 1px solid var(--lavender);
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 600;
    box-shadow: 0 2px 0 rgba(205,180,255,.35);
    transition: transform .05s ease-in-out, background-color .15s ease;
  }
  .chip:hover { transform: translateY(-1px); background: #fff7ff; }
  .chip:focus-visible { outline: none; box-shadow: var(--ring); }

  .grid { display: grid; gap: 18px; grid-template-columns: 1.1fr 1fr; }
  @media (max-width: 1180px){ .grid{ grid-template-columns: 1fr; } }

  .search-row{ display:flex; gap:10px; margin-top: 8px; }
  .input{
    flex: 1;
    background: #fffaf8;
    border: 2px solid var(--pink-200);
    border-radius: 12px;
    padding: 10px 12px;
    color: #4b3340;
  }
  .input::placeholder{ color:#9c7b86; }
  .input:focus-visible{ outline:none; box-shadow: var(--ring); }

  .btn{
    background: var(--pink);
    color: #3a2730;
    border: none;
    border-radius: 12px;
    padding: 10px 14px;
    cursor: pointer;
    font-weight: 800;
    box-shadow: 0 4px 0 rgba(247,182,200,.35);
  }
  .btn:hover{ filter: brightness(.98); }
  .btn:focus-visible{ outline:none; box-shadow: var(--ring); }

  .pill{
    display:inline-block;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 12px;
    border: 1px dashed var(--pink-200);
    background: var(--pink-100);
    color: #5a3f49;
  }

  .stat{
    display:inline-flex; align-items:center; gap:8px;
    padding:8px 12px; border-radius:10px;
    background:#fff7fb; border: 1px solid var(--pink-200);
    color:#4b3340;
  }
  .ok  { color:#3b7b51; font-weight:700; }
  .bad { color:#a23d58; font-weight:700; }

  .a2z a{
    color:#5a3f49; text-decoration:none;
    border-bottom: 2px solid transparent;
  }
  .a2z a:hover{ border-color: var(--lavender); }

  .list{ margin: 10px 0 0 0; padding-left: 18px; }
  .list li{ margin: 4px 0; }

  .note{ font-size: 13px; color: var(--muted); margin-top: 6px; }

  .ribbon{
    height: 10px;
    background:
      radial-gradient(circle at 8px 12px, var(--pink-100) 10px, transparent 11px) repeat-x left/20px 20px;
    margin: 6px -16px 16px -16px;
  }
</style>
</head>
<body>
  <div class="app">
    <!-- Recursive  -->
    <aside class="card">
      <div class="header">
        <div>
          <h1>Digital Library Organizer</h1>
          <div class="muted">Browse categories • Click a title to view details</div>
        </div>
      </div>
      <div class="ribbon" aria-hidden="true"></div>
      <?php displayLibrary($library); ?>
      <div class="note">Tip: The square button toggles each category. <strong>[-]</strong> means open, <strong>[+]</strong> means closed.</div>
    </aside>

    <main class="grid">
      <section class="card">
        <div class="header"><h1>Quick Search</h1></div>
        <form class="search-row" method="get">
          <input class="input" type="text" name="q" placeholder="Search by exact title (e.g., The Hobbit)" value="<?php echo htmlspecialchars($searchQuery); ?>" />
          <button class="btn" type="submit">Search</button>
        </form>

        <?php if ($searchTried): ?>
          <div style="margin-top:12px">
            <?php if ($searchHit): ?>
              <div class="stat"><span class="ok">Found</span> “<?php echo htmlspecialchars($searchQuery); ?>”. <a href="?book=<?php echo urlencode($searchQuery); ?>" class="a2z" style="margin-left:8px">View details</a></div>
            <?php else: ?>
              <div class="stat"><span class="bad">Not found</span> “<?php echo htmlspecialchars($searchQuery); ?>”.</div>
            <?php endif; ?>
          </div>
        <?php endif; ?>

        <div class="note">Powered by a Binary Search Tree (BST) for fast, case-insensitive lookups.</div>
      </section>

      <section class="card">
        <div class="header"><h1>Book Details</h1></div>
        <?php
          if ($selectedBook !== '') {
            $info = getBookInfo($selectedBook, $bookInfo);
            if ($info) {
              echo '<div class="pill">Title</div><div style="margin:6px 0 12px 0"><strong>' . htmlspecialchars($selectedBook) . '</strong></div>';
              echo '<div class="pill">Author</div><div style="margin:6px 0 12px 0">' . htmlspecialchars($info['author']) . '</div>';
              echo '<div class="pill">Year</div><div style="margin:6px 0 12px 0">' . htmlspecialchars((string)$info['year']) . '</div>';
              echo '<div class="pill">Genre</div><div style="margin:6px 0 12px 0">' . htmlspecialchars($info['genre']) . '</div>';
            } else {
              echo '<div class="stat"><span class="bad">Missing</span> Book not found in metadata.</div>';
            }
          } else {
            echo '<div class="note">Select a book on the left or use the search to show details here.</div>';
          }
        ?>
      </section>

      <section class="card" style="grid-column: 1 / -1">
        <div class="header"><h1>Titles A to Z (Inorder Traversal)</h1></div>
        <ol class="list a2z">
          <?php foreach ($bst->inorder() as $title): ?>
            <li><a href="?book=<?php echo urlencode($title); ?>"><?php echo htmlspecialchars($title); ?></a></li>
          <?php endforeach; ?>
        </ol>
        <div class="note">Alphabetical order comes from the BST’s inorder traversal.</div>
      </section>
    </main>
  </div>

<script>
  document.querySelectorAll('.toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.querySelector(btn.dataset.target);
      const isOpen = target.classList.toggle('open');
      btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
      btn.textContent = isOpen ? '-' : '+';
    });
  });
</script>
</body>
</html>
