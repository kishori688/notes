<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Notes extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->model('Note_model');

        header('Content-Type: application/json');
    }

    // CREATE NOTE
    public function create()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['title']) ||
            empty($data['content'] || empty($data['Summary']))
        ) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Title and content required'
            ]);

            return;
        }

        $insert = [
            'title' => trim($data['title']),
            'content' => trim($data['content']),
            'Summary' => trim($data['Summary'])
        ];

        $this->Note_model->create_note($insert);

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'message' => 'Note created successfully'
        ]);
    }

    // NOTES LIST WITH PAGINATION
    public function index()
    {
        $page = (int) ($this->input->get('page') ?: 1);

        $limit = (int) ($this->input->get('limit') ?: 10);

        $offset = ($page - 1) * $limit;

        $notes = $this->Note_model
            ->get_notes($limit, $offset);

        $total = $this->Note_model
            ->count_notes();

        echo json_encode([
            'success' => true,
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'data' => $notes
        ]);
    }

    // GET SINGLE NOTE
    public function show($id)
    {
        $note = $this->Note_model->get_note($id);

        if (!$note) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Note not found'
            ]);

            return;
        }

        echo json_encode([
            'success' => true,
            'data' => $note
        ]);
    }

    // UPDATE NOTE
    public function update($id)
    {
        $title = $this->input->post('title');

        $content = $this->input->post('content');
        
        $summary = $this->input->post('summary');     
        $note = $this->Note_model->get_note($id);

        if (!$note) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Note not found'
            ]);

            return;
        }

        $update = [
            'title' => trim($title),
            'content' => trim($content),
            'summary' => trim($summary)
        ];

        $this->Note_model->update_note($id, $update);

        echo json_encode([
            'success' => true,
            'message' => 'Note updated successfully'
        ]);
    }

    // DELETE NOTE
    public function delete($id)
    {
        $note = $this->Note_model->get_note($id);

        if (!$note) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Note not found'
            ]);

            return;
        }

        $this->Note_model->delete_note($id);

        echo json_encode([
            'success' => true,
            'message' => 'Note deleted successfully'
        ]);
    }

    // AI SUMMARY ENDPOINT
    public function summary($id)
    {
        $note = $this->Note_model->get_note($id);

        if (!$note) {

            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Note not found'
            ]);

            return;
        }

        // SIMPLE AI-LIKE SUMMARY
        $summary = substr(strip_tags($note->content), 0, 100);

        $summary .= '...';

        // SAVE SUMMARY
        $this->Note_model->update_note($id, [
            'summary' => $summary
        ]);

        echo json_encode([
            'success' => true,
            'summary' => $summary
        ]);
    }

    // SEMANTIC SEARCH
    public function search()
    {
        $keyword = $this->input->get('keyword');

        if (empty($keyword)) {

            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Keyword required'
            ]);

            return;
        }

        $results = $this->Note_model
            ->search_notes($keyword);

        echo json_encode([
            'success' => true,
            'results' => $results
        ]);
    }
}