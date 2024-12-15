<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Подключение стилей для таблицы -->
    <style>
         /* Сброс всех отступов */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box; /* Рекомендуется для правильного расчета размеров блоков */
        }

        /* Опционально: установка ширины и высоты для html и body */
        html, body {
            width: 100%;
            height: 100%;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #F5F6F9;
            /* background-image: url("{{ asset('storage/images/background-image-with-a-light-gray-color.jpg') }}"); */
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

        .bg-image {
            background-image: url("{{ asset('storage/images/background-hero-image-with-a-light-gray.jpg') }}");
            background-size: cover; /* Покрывает весь блок */
            background-position: bottom; /* Центрирует изображение */
            background-repeat: no-repeat; /* Отключает повтор */
            color: white; /* Делает текст белым для контраста */
        }
    </style>
</head>
<body>
    <section class="py-5 text-center bg-image">
        <div class="row py-lg-5">
            <div class="col-lg-6 col-md-8 mx-auto">
                <h1 class="fw-light">Laravel News Blog</h1>
                <p class="lead  text-white">All the latest Laravel News posts</p>
            </div>
        </div>
    </section>
    <section class="py-5  container">
    <table class="table">
    <thead>
        <tr class="table-light">
        <th scope="col" class="text-center">
            <a href="?sort_by=publication_date&order={{ request('order') === 'asc' ? 'desc' : 'asc' }}">
            <i class="fa-regular fa-calendar-days"></i>
            Publication Date
                {{ request('sort_by') === 'publication_date' ? (request('order') === 'asc' ? '⬆️' : '⬇️') : '' }}
            </a>
        </th>
        <th scope="col" class="text-center">
            <a href="?sort_by=title&order={{ request('order') === 'asc' ? 'desc' : 'asc' }}">
            <i class="fa-brands fa-blogger"></i>
                Title
                {{ request('sort_by') === 'title' ? (request('order') === 'asc' ? '⬆️' : '⬇️') : '' }}
            </a>
        </th>
        <th scope="col" class="text-center">
            <i class="fa-solid fa-at"></i>    
            Author</th>
        <th scope="col" class="text-center">
            <i class="fa-solid fa-tags"></i>    
            Tags</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($articles as $article)
            <tr>
                <td class="text-center">{{ \Carbon\Carbon::parse($article->publication_date)->format('d.m.Y') }}</td>
                <td><a href="{{ $article->link }}" target="_blank">{{ $article->title }}</a></td>
                <td class="text-center">{{ $article->author }}</td>
                <td class="text-center">{{ $article->tags }}</td>
            </tr>
        @endforeach
    </tbody>
    </table>

    <button id="fetch-updates" type="button" class="btn btn-primary">Update articles</button>
    </section>
    <footer class=" py-5 bg-black">
        <div class="container">
            <p class="float-end mb-1">
            <a href="#">Back to top</a>
            </p>
            <p class="mb-1 text-white">The project was completed as a test task!</p>
        </div>
    </footer>
    <!-- Модальное окно -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="statusModalLabel">Status</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="statusModalBody">
            <!-- Здесь будет сообщение -->
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
    </div>
    
    <script src="https://kit.fontawesome.com/0e34ff38b4.js" crossorigin="anonymous"></script>
    <script>
        document.getElementById('fetch-updates').addEventListener('click', function () {
        fetch('/fetch-updates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
            .then(response => response.json())
            .then(data => {
                const modalBody = document.getElementById('statusModalBody');
                const modal = new bootstrap.Modal(document.getElementById('statusModal'));

                if (data.success) {
                    modalBody.textContent = 'Updates fetched successfully!';
                    modal.show();

                    // Обновляем данные на странице
                    fetchArticles(); // Вызовите функцию, чтобы обновить таблицу
                } else {
                    modalBody.textContent = data.message || 'An error occurred.';
                    modal.show();
                }
            })
            .catch(error => {
                const modalBody = document.getElementById('statusModalBody');
                const modal = new bootstrap.Modal(document.getElementById('statusModal'));

                modalBody.textContent = 'Error fetching updates!';
                console.error(error);
                modal.show();
            });
    });

    // Функция для получения обновленных статей и обновления таблицы
    function fetchArticles() {
        fetch('//articles') // Здесь должен быть путь для получения данных о статьях
            .then(response => response.json())
            .then(data => {
                const tableBody = document.querySelector('table tbody');
                tableBody.innerHTML = ''; // Очищаем старые строки

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
