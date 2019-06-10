<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Todo_Model extends CI_Model
{

    public function create(array $post)
    {
        $data = [
            'todo' => $post['todo'],
        ];
        $this->db->insert('todo', $data);
        return $this->db->insert_id();
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete('todo');
    }

    public function update(array $post, $id)
    {
        $data = [
            'todo' => $post['todo'],
        ];
        return $this->db->where('id', $id)->update('todo', $data);
    }

    public function get($id) {
        return $this->db->get_where('todo', ['id' => $id])->row();
    }

    public function all()
    {
        return $this->db->get('todo')->result();
    }

}




