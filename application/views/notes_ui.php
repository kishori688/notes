<!DOCTYPE html>
<html>
<head>
    <title>Notes CRUD App</title>

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">

<div class="container mt-5">

    <h2 class="mb-4 text-center">Notes CRUD Application</h2>

    <!-- CREATE NOTE -->

    <div class="card mb-4">
        <div class="card-body">

            <h4>Create Note</h4>

            <input type="text"
                   id="title"
                   class="form-control mb-2"
                   placeholder="Enter title">

            <textarea id="content"
                      class="form-control mb-2"
                      placeholder="Enter content"></textarea>
            <textarea id="summary"
                      class="form-control mb-2"
                      placeholder="Enter summary"></textarea>
            
            <button class="btn btn-primary"
                    onclick="createNote()">
                Create Note
            </button>

        </div>
    </div>

    <!-- SEARCH -->

    <div class="card mb-4">
        <div class="card-body">

            <h4>Semantic Search</h4>

            <input type="text"
                   id="searchKeyword"
                   class="form-control mb-2"
                   placeholder="Search notes">

            <button class="btn btn-success"
                    onclick="searchNotes()">
                Search
            </button>

        </div>
    </div>

    <!-- NOTES LIST -->

    <div class="card">
        <div class="card-body">

            <h4>Notes List</h4>

            <div id="notesList"></div>

            <!-- PAGINATION -->

            <div class="mt-3">
                <button class="btn btn-secondary"
                        onclick="prevPage()">
                    Previous
                </button>

                <span id="pageNumber" class="mx-3">1</span>

                <button class="btn btn-secondary"
                        onclick="nextPage()">
                    Next
                </button>
            </div>

        </div>
    </div>

</div>

<script>

let currentPage = 1;
let limit = 5;

// LOAD NOTES
function loadNotes(page = 1)
{
    currentPage = page;

    $('#pageNumber').text(page);

    $.ajax({
        url:
        'http://localhost/notes-api/index.php/api/notes?page='
        + page +
        '&limit=' + limit,

        method: 'GET',

        success: function(response)
        {
            let html = '';

            response.data.forEach(note => {

                html += `
                    <div class="card mt-3">
                        <div class="card-body">

                            <h5>${note.title}</h5>

                            <p>${note.content}</p>
                                        
                            <p>${note.summary}</p>            

                            <small>
                                ${note.created_at}
                            </small>

                            <br><br>

                            <button class="btn btn-warning btn-sm"
                                    onclick="showUpdatePrompt(${note.id})">
                                Update
                            </button>

                            <button class="btn btn-danger btn-sm"
                                    onclick="deleteNote(${note.id})">
                                Delete
                            </button>

                            <button class="btn btn-info btn-sm"
                                    onclick="generateSummary(${note.id})">
                                AI Summary
                            </button>

                        </div>
                    </div>
                `;
            });

            $('#notesList').html(html);
        }
    });
}

// CREATE NOTE
function createNote()
{
    let title = $('#title').val();

    let content = $('#content').val();
    
    let summary = $('#summary').val();

    $.ajax({
        url:
        'http://localhost/notes-api/index.php/api/notes/create',

        method: 'POST',

        contentType: 'application/json',

        data: JSON.stringify({
            title: title,
            content: content,
            summary: summary
        }),

        success: function(response)
        {
            alert(response.message);

            $('#title').val('');
            $('#content').val('');
            $('#summary').val('');

            loadNotes(currentPage);
        }
    });
}

// DELETE NOTE
function deleteNote(id)
{
    if (!confirm('Delete this note?')) {
        return;
    }

    $.ajax({
        url:
        'http://localhost/notes-api/index.php/api/notes/delete/' + id,

        method: 'GET',

        success: function(response)
        {
            alert(response.message);

            loadNotes(currentPage);
        }
    });
}

// UPDATE NOTE
function showUpdatePrompt(id)
{
    let title = prompt('Enter new title');

    let content = prompt('Enter new content');

    $.ajax({
        url:
        'http://localhost/notes-api/index.php/api/notes/update/' + id,

        method: 'POST',

        data: {
            title: title,
            content: content,
            summary: summary
        },

        success: function(response)
        {
            alert(response.message);

            loadNotes(currentPage);
        }
    });
}

// AI SUMMARY
function generateSummary(id)
{
    $.ajax({
        url:
        'http://localhost/notes-api/index.php/api/notes/'
        + id +
        '/summary',

        method: 'POST',

        success: function(response)
        {
            alert('AI Summary:\n\n' + response.summary);
        }
    });
}

// SEARCH NOTES
function searchNotes()
{
    let keyword = $('#searchKeyword').val();

    $.ajax({
        url:
        'http://localhost/notes-api/index.php/api/notes/search?keyword='
        + keyword,

        method: 'GET',

        success: function(response)
        {
            let html = '';

            response.results.forEach(note => {

                html += `
                    <div class="card mt-3">
                        <div class="card-body">

                            <h5>${note.title}</h5>

                            <p>${note.content}</p>
                                        
                             <p>${note.summary}</p>
                        </div>
                    </div>
                `;
            });

            $('#notesList').html(html);
        }
    });
}

// PAGINATION
function nextPage()
{
    currentPage++;

    loadNotes(currentPage);
}

function prevPage()
{
    if (currentPage > 1)
    {
        currentPage--;

        loadNotes(currentPage);
    }
}

// INITIAL LOAD
loadNotes();

</script>

</body>
</html>