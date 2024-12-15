<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Подключение стилей для таблицы -->
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #f2f2f2;
        }
        table th a {
            color: #007bff;
            text-decoration: none;
        }
        table th a:hover {
            text-decoration: underline;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Articles</h1>

    <!-- <table>
        <thead>
            <tr>
                <th>
                    <a href="?sort_by=date&order={{ request('order') === 'asc' ? 'desc' : 'asc' }}">
                        Publication Date
                        {{ request('sort_by') === 'date' ? (request('order') === 'asc' ? '⬆️' : '⬇️') : '' }}
                    </a>
                </th>
                <th>
                    <a href="?sort_by=title&order={{ request('order') === 'asc' ? 'desc' : 'asc' }}">
                        Title
                        {{ request('sort_by') === 'title' ? (request('order') === 'asc' ? '⬆️' : '⬇️') : '' }}
                    </a>
                </th>
                <th>Author</th>
                <th>Tags</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($article->publication_date)->format('d.m.Y') }}</td>
                    <td><a href="{{ $article->link }}" target="_blank">{{ $article->title }}</a></td>
                    <td>{{ $article->author }}</td>
                    <td>{{ $article->tags }}</td>
                </tr>
            @endforeach
        </tbody>
    </table> -->

    <table class="table">
    <thead>
        <tr>
        <th scope="col">
            <a href="?sort_by=publication_date&order={{ request('order') === 'asc' ? 'desc' : 'asc' }}">
                Publication Date
                {{ request('sort_by') === 'publication_date' ? (request('order') === 'asc' ? '⬆️' : '⬇️') : '' }}
            </a>
        </th>
        <th scope="col">
            <a href="?sort_by=title&order={{ request('order') === 'asc' ? 'desc' : 'asc' }}">
                Title
                {{ request('sort_by') === 'title' ? (request('order') === 'asc' ? '⬆️' : '⬇️') : '' }}
            </a>
        </th>
        <th scope="col">Author</th>
        <th scope="col">Tags</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
            <tr>
                <td>{{ \Carbon\Carbon::parse($article->publication_date)->format('d.m.Y') }}</td>
                <td><a href="{{ $article->link }}" target="_blank">{{ $article->title }}</a></td>
                <td>{{ $article->author }}</td>
                <td>{{ $article->tags }}</td>
            </tr>
        @endforeach
    </tbody>
    </table>

    <!-- <button id="fetch-updates" style="margin-top: 20px;">Fetch Updates from Source</button> -->
    <button id="fetch-updates" type="button" class="btn btn-primary">Update</button>
    <script>
        document.getElementById('fetch-updates').addEventListener('click', function() {
            fetch('/fetch-updates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Updates fetched successfully!');
                    // Обновляем данные на странице
                    fetchArticles(); // Вызовите функцию, чтобы обновить таблицу
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                alert('Error fetching updates!');
                console.error(error);
            });
        });

        // Функция для получения обновленных статей и обновления таблицы
        function fetchArticles() {
            fetch('//articles')  // Здесь должен быть путь для получения данных о статьях
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('table tbody');
                tableBody.innerHTML = '';  // Очищаем старые строки

                data.articles.forEach(article => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${article.publication_date}</td>
                        <td><a href="${article.link}" target="_blank">${article.title}</a></td>
                        <td>${article.author}</td>
                        <td>${article.tags}</td>
                    `;
                    tableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error('Error fetching articles:', error);
            });
        }

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
