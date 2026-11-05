<?php
// Product management functions
require_once 'db_config.php';

class ProductManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getConnection();
    }
    
    // Get all products
    public function getAllProducts($limit = null, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.is_active = 1 
                    ORDER BY p.created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return false;
        }
    }
    
    // Get product by ID
    public function getProductById($id) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.id = :id AND p.is_active = 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching product: " . $e->getMessage());
            return false;
        }
    }
    
    // Get products by category
    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.category_id = :category_id AND p.is_active = 1 
                    ORDER BY p.created_at DESC";
            
            if ($limit) {
                $sql .= " LIMIT :limit OFFSET :offset";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching products by category: " . $e->getMessage());
            return false;
        }
    }
    
    // Create new product
    public function createProduct($name, $description, $price, $categoryId, $image_url = null, $stock_quantity = 0) {
        try {
            $sql = "INSERT INTO products (name, description, price, category_id, image_url, stock_quantity) 
                    VALUES (:name, :description, :price, :category_id, :image_url, :stock_quantity)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':stock_quantity', $stock_quantity);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error creating product: " . $e->getMessage());
            return false;
        }
    }
    
    // Update product
    public function updateProduct($id, $name, $description, $price, $categoryId, $image_url = null, $stock_quantity = 0, $is_active = 1) {
        try {
            $sql = "UPDATE products SET name = :name, description = :description, price = :price, 
                    category_id = :category_id, image_url = :image_url, stock_quantity = :stock_quantity, 
                    is_active = :is_active, updated_at = CURRENT_TIMESTAMP 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':stock_quantity', $stock_quantity);
            $stmt->bindParam(':is_active', $is_active, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }
    
    // Get product image filename only (used before deletion)
    public function getProductImage($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT image_url FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['image_url'] : null;
        } catch(PDOException $e) {
            error_log("Error fetching product image: " . $e->getMessage());
            return null;
        }
    }

    // Delete product — hard delete (permanently removes the row)
    public function deleteProduct($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }
    
    // Search products
    public function searchProducts($searchTerm) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p 
                    LEFT JOIN categories c ON p.category_id = c.id 
                    WHERE p.name LIKE :search OR p.description LIKE :search AND p.is_active = 1";
            $searchTerm = '%' . $searchTerm . '%';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':search', $searchTerm);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error searching products: " . $e->getMessage());
            return false;
        }
    }
}

// Category management functions
class CategoryManager {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getConnection();
    }
    
    // Get all categories
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categories ORDER BY name";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return false;
        }
    }
    
    // Get category by ID
    public function getCategoryById($id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching category: " . $e->getMessage());
            return false;
        }
    }
    
    // Create new category
    public function createCategory($name, $description) {
        try {
            $sql = "INSERT INTO categories (name, description) VALUES (:name, :description)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            
            if ($stmt->execute()) {
                return $this->pdo->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error creating category: " . $e->getMessage());
            return false;
        }
    }
    
    // Update category
    public function updateCategory($id, $name, $description) {
        try {
            $sql = "UPDATE categories SET name = :name, description = :description WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating category: " . $e->getMessage());
            return false;
        }
    }
    
    // Delete category
    public function deleteCategory($id) {
        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error deleting category: " . $e->getMessage());
            return false;
        }
    }
}
?>