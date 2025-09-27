<?php
class Category {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    // Get all categories, ordered by sort_order
    public function getAllCategories(){
        $this->db->query('SELECT * FROM categories ORDER BY parent_id ASC, sort_order ASC, name ASC');
        return $this->db->resultSet();
    }

    // Add new category
    public function addCategory($data){
        $this->db->query('INSERT INTO categories (name, parent_id, description, sort_order) VALUES (:name, :parent_id, :description, :sort_order)');
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':parent_id', $data['parent_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':sort_order', $data['sort_order']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getCategoryById($id){
        $this->db->query('SELECT * FROM categories WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Update category
    public function updateCategory($data){
        $this->db->query('UPDATE categories SET name = :name, parent_id = :parent_id, description = :description, sort_order = :sort_order WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':parent_id', $data['parent_id']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':sort_order', $data['sort_order']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    // Delete category
    public function deleteCategory($id){
        $this->db->query('DELETE FROM categories WHERE id = :id');
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
?>