<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Articles</h1>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>
                <a href="?sortField=publication_date&sortDirection={{ $sortField === 'publication_date' && $sortDirection === 'asc' ? 'desc' : 'asc' }}">
                    Publication Date
                </a>
            </th>
            <th>
                <a href="?sortField=title&sortDirection={{ $sortField === 'title' && $sortDirection === 'asc' ? 'desc' : 'asc' }}">
                    Title
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
    </table>
</div>
</body>
</html>
