<?php

class Note_model extends CI_Model
{
    // CREATE NOTE
    public function create_note($data)
    {
        return $this->db->insert('notes', $data);
    }

    // GET NOTES WITH PAGINATION
    public function get_notes($limit, $offset)
    {
        return $this->db
            ->limit($limit, $offset)
            ->order_by('id', 'DESC')
            ->get('notes')
            ->result();
    }

    // COUNT NOTES
    public function count_notes()
    {
        return $this->db->count_all('notes');
    }

    // GET SINGLE NOTE
    public function get_note($id)
    {
        return $this->db
            ->where('id', $id)
            ->get('notes')
            ->row();
    }

    // UPDATE NOTE
    public function update_note($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update('notes', $data);
    }

    // DELETE NOTE
    public function delete_note($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete('notes');
    }

    // SEMANTIC SEARCH
    public function search_notes($keyword)
    {
        return $this->db
            ->group_start()
            ->like('title', $keyword)
            ->or_like('content', $keyword)
            ->or_like('summary', $keyword)     
            ->group_end()
            ->get('notes')
            ->result();
    }
}