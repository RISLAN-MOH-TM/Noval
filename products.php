<?php
require_once 'includes/product_manager.php';
require_once 'includes/user_manager.php';
require_once 'includes/cart_manager.php';
require_once 'includes/nav_helper.php';

SessionManager::init();

$productManager  = new ProductManager();
$categoryManager = new CategoryManager();
$cartManager     = new CartManager();

$categoryId  = isset($_GET['category']) ? (int)$_GET['category']   : null;
$searchTerm  = isset($_GET['search'])   ? trim($_GET['search'])     : '';

if ($searchTerm)     { $products = $productManager->searchProducts($searchTerm); }
elseif ($categoryId) { $products = $productManager->getProductsByCategory($categoryId); }
else                 { $products = $productManager->getAllProducts(); }

$products      = is_array($products) ? $products : [];
$categories    = $categoryManager->getAllCategories();
$categories    = is_array($categories) ? $categories : [];
$totalProducts = count($productManager->getAllProducts() ?: []);
$userId        = SessionManager::get('user_id');
$cartCount     = $cartManager->getCartCount($userId);

renderHead('Shop Natural Products — Novel Cosmetics');
renderNav('products', $cartCount);
?>

<!-- ══ SHOP HERO ════════════════════════════════════════ -->
<section style="background:linear-gradient(135deg,#1a1209 0%,#2d1e0f 55%,#1a2a18 100%);
                padding:3.5rem 0 2.5rem;position:relative;overflow:hidden;">
  <div style="position:absolute;inset:0;
       background:url('data:image/svg+xml,%3Csvg width=60 height=60 xmlns=http://www.w3.org/2000/svg%3E%3Ccircle cx=30 cy=30 r=1.5 fill=%23fff fill-opacity=.04/%3E%3C/svg%3E');
       pointer-events:none;"></div>
  <div style="position:relative;z-index:1;max-width:1240px;margin:0 auto;padding:0 1.5rem;">

    <!-- Two-column on desktop, stacked on mobile -->
    <div id="shopHeroGrid"
         style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;align-items:end;">

      <!-- Left -->
      <div>
        <span style="display:inline-flex;align-items:center;gap:.5rem;
              background:rgba(255,255,255,.08);backdrop-filter:blur(8px);
              border:1px solid rgba(255,255,255,.14);color:rgba(255,255,255,.8);
              font-size:.72rem;font-weight:700;letter-spacing:.08em;text-transform:uppercase;
              padding:.35rem .9rem;border-radius:8px;margin-bottom:1rem;">
          <span style="width:7px;height:7px;border-radius:50%;background:#7bc47b;flex-shrink:0;"></span>
          100% Natural &amp; Organic
        </span>

        <h1 style="font-family:'New York','Georgia',serif;
                   font-size:clamp(1.8rem,4vw,3rem);
                   color:#fff;font-weight:600;line-height:1.15;
                   margin-bottom:.6rem;letter-spacing:-.03em;">
          Our Collection
        </h1>
        <p style="color:rgba(255,255,255,.6);font-size:.95rem;margin-bottom:0;
                  max-width:380px;line-height:1.6;">
          Handcrafted cosmetics made with pure ingredients — no chemicals, just nature.
        </p>

        <!-- Stats -->
        <div id="shopHeroStats"
             style="display:flex;gap:2rem;flex-wrap:wrap;margin-top:1.5rem;">
          <?php foreach ([[$totalProducts.'+','Products'],[count($categories),'Categories'],['100%','Organic'],['0%','Chemicals']] as $s): ?>
          <div>
            <strong style="display:block;font-size:1.4rem;font-weight:800;color:#fff;line-height:1;">
              <?= $s[0] ?>
            </strong>
            <span style="font-size:.68rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.07em;">
              <?= $s[1] ?>
            </span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Right: search -->
      <div id="shopHeroSearch"
           style="background:rgba(255,255,255,.08);backdrop-filter:blur(12px);
                  border:1px solid rgba(255,255,255,.14);border-radius:16px;padding:1.25rem 1.5rem;">
        <form method="GET" action="/novel/products">
          <div id="shopSearchRow" style="display:flex;gap:.75rem;">
            <input type="search" name="search"
                   value="<?= htmlspecialchars($searchTerm) ?>"
                   placeholder="Search products, ingredients..."
                   style="flex:1;min-width:0;background:rgba(255,255,255,.92);border:none;
                          border-radius:10px;padding:.8rem 1.1rem;font-size:.9rem;
                          font-family:var(--font-sans);color:var(--c-gray-800);outline:none;">
            <button type="submit"
                    style="background:#fff;color:var(--c-sage);border:none;border-radius:10px;
                           padding:.8rem 1.25rem;font-size:.875rem;font-weight:700;
                           cursor:pointer;font-family:var(--font-sans);
                           white-space:nowrap;transition:var(--tr);"
                    onmouseover="this.style.background='#a8d5a8'"
                    onmouseout="this.style.background='#fff'">
              <i class="fas fa-search"></i>
              <span style="display:none;" class="d-sm-inline"> Search</span>
            </button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>

<!-- ══ SHOP BODY ════════════════════════════════════════ -->
<div style="max-width:1240px;margin:0 auto;padding:2rem 1.5rem 5rem;">
  <div id="shopLayout" style="display:grid;grid-template-columns:220px 1fr;gap:2rem;">

    <!-- ── SIDEBAR ───────────────────────────────────── -->
    <aside id="shopSidebar">

      <!-- Mobile toggle button -->
      <button id="sidebarToggleBtn"
              onclick="toggleSidebar()"
              style="display:none;width:100%;align-items:center;justify-content:space-between;
                     padding:.75rem 1rem;background:var(--c-white);border-radius:12px;
                     border:1.5px solid var(--c-gray-200);cursor:pointer;
                     font-size:.875rem;font-weight:700;color:var(--c-gray-700);
                     font-family:var(--font-sans);margin-bottom:.75rem;">
        <span><i class="fas fa-filter" style="margin-right:.5rem;color:var(--c-sage);"></i>Filter by Category</span>
        <i class="fas fa-chevron-down" id="sidebarChevron" style="transition:transform .25s;font-size:.8rem;color:var(--c-gray-400);"></i>
      </button>

      <!-- Sidebar content -->
      <div id="sidebarInner" style="position:sticky;top:80px;">
        <div style="background:var(--c-white);border-radius:14px;
                    border:1px solid var(--c-gray-200);overflow:hidden;
                    box-shadow:var(--shadow-sm);">

          <div style="background:linear-gradient(135deg,var(--c-sage),var(--c-sage-dark));
                      padding:.9rem 1.2rem;display:flex;align-items:center;gap:.6rem;">
            <i class="fas fa-filter" style="color:rgba(255,255,255,.8);font-size:.85rem;"></i>
            <span style="color:#fff;font-size:.875rem;font-weight:700;">Categories</span>
          </div>

          <div style="padding:.4rem 0;">
            <?php
            $allActive = (!$categoryId && !$searchTerm);
            $baseStyle = 'display:flex;align-items:center;justify-content:space-between;padding:.7rem 1.2rem;text-decoration:none;font-size:.875rem;transition:var(--tr);';
            $activeStyle = $baseStyle . 'color:var(--c-sage);font-weight:700;background:var(--c-sage-light);border-left:3px solid var(--c-sage);';
            $idleStyle   = $baseStyle . 'color:var(--c-gray-700);font-weight:500;border-left:3px solid transparent;';
            ?>
            <a href="/novel/products" style="<?= $allActive ? $activeStyle : $idleStyle ?>">
              <span><i class="fas fa-th" style="margin-right:.5rem;color:var(--c-sage);font-size:.78rem;"></i>All Products</span>
              <span style="background:<?= $allActive?'var(--c-sage)':'var(--c-gray-100)' ?>;color:<?= $allActive?'#fff':'var(--c-gray-600)' ?>;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:20px;">
                <?= $totalProducts ?>
              </span>
            </a>
            <?php foreach ($categories as $cat):
              $catCount = count($productManager->getProductsByCategory($cat['id']) ?: []);
              $isAct    = $categoryId == $cat['id'];
            ?>
            <a href="/novel/products/category/<?= $cat['id'] ?>"
               style="<?= $isAct ? $activeStyle : $idleStyle ?>">
              <span><i class="fas fa-leaf" style="margin-right:.5rem;color:var(--c-sage);font-size:.78rem;"></i><?= htmlspecialchars($cat['name']) ?></span>
              <span style="background:<?= $isAct?'var(--c-sage)':'var(--c-gray-100)' ?>;color:<?= $isAct?'#fff':'var(--c-gray-600)' ?>;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:20px;">
                <?= $catCount ?>
              </span>
            </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </aside>

    <!-- ── MAIN ──────────────────────────────────────── -->
    <main>

      <!-- Toolbar -->
      <div style="display:flex;align-items:center;justify-content:space-between;
                  background:var(--c-white);border-radius:12px;
                  border:1px solid var(--c-gray-200);padding:.75rem 1.2rem;
                  margin-bottom:1.5rem;flex-wrap:wrap;gap:.75rem;box-shadow:var(--shadow-xs);">
        <div style="font-size:.875rem;color:var(--c-gray-600);">
          <?php if ($searchTerm): ?>
            <?= count($products) ?> results for
            <strong style="color:var(--c-gray-800);">"<?= htmlspecialchars($searchTerm) ?>"</strong>
            <a href="/novel/products"
               style="display:inline-flex;align-items:center;gap:.35rem;margin-left:.6rem;
                      background:var(--c-sage-light);color:var(--c-sage);font-size:.72rem;
                      font-weight:700;padding:.25rem .7rem;border-radius:6px;text-decoration:none;">
              Clear <i class="fas fa-times"></i>
            </a>
          <?php elseif ($categoryId):
            $selCat = $categoryManager->getCategoryById($categoryId); ?>
            Showing <strong style="color:var(--c-gray-800);"><?= count($products) ?></strong>
            in <strong style="color:var(--c-gray-800);"><?= htmlspecialchars($selCat['name'] ?? '') ?></strong>
            <a href="/novel/products"
               style="display:inline-flex;align-items:center;gap:.35rem;margin-left:.6rem;
                      background:var(--c-sage-light);color:var(--c-sage);font-size:.72rem;
                      font-weight:700;padding:.25rem .7rem;border-radius:6px;text-decoration:none;">
              Clear <i class="fas fa-times"></i>
            </a>
          <?php else: ?>
            Showing <strong style="color:var(--c-gray-800);"><?= count($products) ?></strong> products
          <?php endif; ?>
        </div>

        <div style="display:flex;gap:.35rem;">
          <button id="btnGrid" onclick="setView('grid')"
                  style="width:34px;height:34px;border-radius:8px;border:1.5px solid var(--c-sage);
                         background:var(--c-sage);color:#fff;cursor:pointer;transition:var(--tr);
                         display:flex;align-items:center;justify-content:center;font-size:.85rem;"
                  title="Grid view"><i class="fas fa-th"></i></button>
          <button id="btnList" onclick="setView('list')"
                  style="width:34px;height:34px;border-radius:8px;border:1.5px solid var(--c-gray-200);
                         background:var(--c-white);color:var(--c-gray-400);cursor:pointer;
                         transition:var(--tr);display:flex;align-items:center;
                         justify-content:center;font-size:.85rem;"
                  title="List view"><i class="fas fa-list"></i></button>
        </div>
      </div>

      <!-- Products grid -->
      <?php if ($products): ?>
      <div id="productGrid"
           style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.25rem;">
        <?php foreach ($products as $product):
          $inStock = $product['stock_quantity'] > 0;
          $imgSrc  = $product['image_url']
              ? 'images/products/' . htmlspecialchars($product['image_url'])
              : 'images/default-product.jpg';
        ?>
        <div class="novel-pcard"
             style="background:var(--c-white);border-radius:14px;overflow:hidden;
                    border:1px solid var(--c-gray-200);display:flex;flex-direction:column;
                    position:relative;transition:transform .28s var(--ease),box-shadow .28s var(--ease),border-color .28s var(--ease);"
             onmouseover="this.style.transform='translateY(-5px)';this.style.boxShadow='0 12px 36px rgba(26,18,9,.1)';this.style.borderColor='var(--c-gray-300)';"
             onmouseout="this.style.transform='';this.style.boxShadow='';this.style.borderColor='var(--c-gray-200)';">

          <!-- Badge -->
          <span style="position:absolute;top:12px;left:12px;z-index:2;
                       background:<?= !$inStock?'var(--c-gray-400)':($product['stock_quantity']<=5?'var(--c-earth)':'var(--c-sage)') ?>;
                       color:#fff;font-size:.62rem;font-weight:700;letter-spacing:.05em;
                       text-transform:uppercase;padding:.25rem .65rem;border-radius:6px;">
            <?= !$inStock?'Sold Out':($product['stock_quantity']<=5?'Low Stock':'Natural') ?>
          </span>

          <!-- Image -->
          <div style="position:relative;overflow:hidden;
                      background:linear-gradient(135deg,var(--c-sage-light),var(--c-earth-light));
                      aspect-ratio:1/1;" class="pcard-img-zone">
            <img src="<?= $imgSrc ?>" alt="<?= htmlspecialchars($product['name']) ?>"
                 loading="lazy" class="pcard-thumb"
                 style="width:100%;height:100%;object-fit:cover;transition:transform .45s var(--ease);">
            <?php if ($inStock): ?>
            <div class="pcard-hover-overlay"
                 style="position:absolute;inset:0;background:rgba(45,30,15,.55);
                        display:flex;align-items:center;justify-content:center;
                        opacity:0;transition:opacity .28s var(--ease);">
              <a href="/novel/product/<?= $product['id'] ?>" class="pcard-qv-btn"
                 style="background:#fff;color:var(--c-sage);border-radius:8px;
                        padding:.5rem 1.3rem;font-weight:700;font-size:.82rem;
                        text-decoration:none;transform:translateY(8px);
                        transition:transform .25s var(--ease);">
                <i class="fas fa-eye me-1"></i> Quick View
              </a>
            </div>
            <?php endif; ?>
          </div>

          <!-- Body -->
          <div style="padding:1.1rem 1.2rem 1.3rem;flex:1;display:flex;flex-direction:column;">
            <div style="font-size:.68rem;font-weight:700;color:var(--c-sage);
                        text-transform:uppercase;letter-spacing:.06em;margin-bottom:.3rem;">
              <i class="fas fa-leaf" style="margin-right:.3rem;"></i>Natural
            </div>
            <div style="font-size:.975rem;font-weight:600;color:var(--c-gray-800);
                        line-height:1.3;margin-bottom:.65rem;letter-spacing:-.01em;
                        display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
              <?= htmlspecialchars($product['name']) ?>
            </div>

            <!-- PRICE — RS same size as number -->
            <div style="display:flex;align-items:baseline;gap:.15rem;margin-bottom:.15rem;">
              <span style="font-size:1.2rem;font-weight:800;color:var(--c-gray-800);letter-spacing:-.02em;">RS</span>
              <span style="font-size:1.2rem;font-weight:800;color:var(--c-gray-800);letter-spacing:-.02em;"><?= number_format($product['price'],2) ?></span>
            </div>

            <div style="font-size:.72rem;font-weight:500;margin-bottom:.9rem;
                        color:<?= !$inStock?'var(--c-clay)':($product['stock_quantity']<=5?'var(--c-earth)':'var(--c-sage)') ?>;">
              <i class="fas <?= !$inStock?'fa-times-circle':($product['stock_quantity']<=5?'fa-exclamation-circle':'fa-check-circle') ?>" style="margin-right:.3rem;"></i>
              <?= !$inStock?'Out of Stock':($product['stock_quantity']<=5?'Only '.$product['stock_quantity'].' left':'In Stock') ?>
            </div>

            <div style="display:flex;gap:.5rem;margin-top:auto;">
              <a href="/novel/product/<?= $product['id'] ?>"
                 style="flex:1;padding:.55rem;border-radius:8px;border:1.5px solid var(--c-gray-300);
                        color:var(--c-gray-700);background:transparent;font-size:.8rem;font-weight:600;
                        text-align:center;text-decoration:none;transition:var(--tr);
                        display:flex;align-items:center;justify-content:center;gap:.35rem;"
                 onmouseover="this.style.background='var(--c-gray-800)';this.style.color='#fff';this.style.borderColor='var(--c-gray-800)';"
                 onmouseout="this.style.background='transparent';this.style.color='var(--c-gray-700)';this.style.borderColor='var(--c-gray-300)';">
                <i class="fas fa-eye"></i> Details
              </a>
              <?php if ($inStock): ?>
              <a href="/novel/product/<?= $product['id'] ?>"
                 style="flex:1;padding:.55rem;border-radius:8px;background:var(--c-sage);
                        color:#fff;border:none;font-size:.8rem;font-weight:600;
                        text-align:center;text-decoration:none;transition:var(--tr);
                        display:flex;align-items:center;justify-content:center;gap:.35rem;"
                 onmouseover="this.style.background='var(--c-sage-dark)';this.style.boxShadow='var(--shadow-sage)';"
                 onmouseout="this.style.background='var(--c-sage)';this.style.boxShadow='';">
                <i class="fas fa-cart-plus"></i> Add
              </a>
              <?php else: ?>
              <button disabled
                      style="flex:1;padding:.55rem;border-radius:8px;background:var(--c-gray-100);
                             color:var(--c-gray-400);border:none;font-size:.8rem;font-weight:600;cursor:not-allowed;">
                <i class="fas fa-ban"></i>
              </button>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <?php else: ?>
      <div style="text-align:center;padding:5rem 2rem;background:var(--c-white);
                  border-radius:14px;border:1px solid var(--c-gray-200);">
        <div style="width:88px;height:88px;border-radius:50%;background:var(--c-sage-light);
                    display:flex;align-items:center;justify-content:center;
                    margin:0 auto 1.5rem;font-size:2.2rem;color:var(--c-sage);">
          <i class="fas fa-search"></i>
        </div>
        <h4 style="font-weight:700;color:var(--c-gray-800);margin-bottom:.5rem;">No products found</h4>
        <p style="color:var(--c-gray-400);margin-bottom:1.5rem;">Try a different search or browse all categories.</p>
        <a href="/novel/products" class="btn-novel btn-novel-primary">View All Products</a>
      </div>
      <?php endif; ?>

    </main>
  </div>
</div>

<?php renderFooter(); ?>

<style>
.novel-pcard:hover .pcard-thumb        { transform: scale(1.06); }
.novel-pcard:hover .pcard-hover-overlay { opacity: 1 !important; }
.novel-pcard:hover .pcard-qv-btn        { transform: translateY(0) !important; }

/* List mode */
.list-mode .novel-pcard { flex-direction: row; }
.list-mode .novel-pcard .pcard-img-zone { width:180px;flex-shrink:0;aspect-ratio:unset; }
.list-mode .novel-pcard .pcard-hover-overlay { display:none; }

/* Responsive grid */
@media (max-width:1024px) { #productGrid { grid-template-columns:repeat(2,1fr) !important; } }
@media (max-width:768px)  {
  #shopLayout    { grid-template-columns:1fr !important; }
  #productGrid   { grid-template-columns:repeat(2,1fr) !important; gap:1rem !important; }
  .list-mode .novel-pcard { flex-direction:column; }
  .list-mode .novel-pcard .pcard-img-zone { width:100%;aspect-ratio:1/1; }
}
@media (max-width:480px)  { #productGrid { grid-template-columns:1fr !important; } }
</style>

<script>
function setView(mode) {
  var grid = document.getElementById('productGrid');
  var btnG = document.getElementById('btnGrid');
  var btnL = document.getElementById('btnList');
  if (mode === 'grid') {
    grid.classList.remove('list-mode');
    grid.style.gridTemplateColumns = window.innerWidth <= 768 ? 'repeat(2,1fr)' : 'repeat(3,1fr)';
    btnG.style.cssText += 'background:var(--c-sage);border-color:var(--c-sage);color:#fff;';
    btnL.style.cssText += 'background:var(--c-white);border-color:var(--c-gray-200);color:var(--c-gray-400);';
  } else {
    grid.classList.add('list-mode');
    grid.style.gridTemplateColumns = '1fr';
    btnL.style.cssText += 'background:var(--c-sage);border-color:var(--c-sage);color:#fff;';
    btnG.style.cssText += 'background:var(--c-white);border-color:var(--c-gray-200);color:var(--c-gray-400);';
  }
}

function toggleSidebar() {
  var inner    = document.getElementById('sidebarInner');
  var chevron  = document.getElementById('sidebarChevron');
  var isOpen   = inner.classList.contains('open');
  inner.classList.toggle('open', !isOpen);
  chevron.style.transform = isOpen ? '' : 'rotate(180deg)';
}
</script>
