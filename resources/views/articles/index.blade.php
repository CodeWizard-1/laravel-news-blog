<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
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
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        a {
            color: #007bff;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Articles</h1>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Tags</th>
                <th>Publication Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles as $article)
                <tr>
                    <td><a href="{{ $article->link }}" target="_blank">{{ $article->title }}</a></td>
                    <td>{{ $article->author }}</td>
                    <td>{{ $article->tags }}</td>
                    <td>{{ \Carbon\Carbon::parse($article->publication_date)->format('d.m.Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>