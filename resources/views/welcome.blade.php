<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>File Upload</title>

        @vite(['resources/js/app.js'])

        <style>
            body {
                background-color: #fff;
                font-family: sans-serif;
                padding: 20px;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                border: 1px solid #000;
                padding: 20px;
            }
            .upload-container {
                border: 1px solid #000;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 20px;
            }
            .upload-button {
                background-color: #fff;
                border: 1px solid #000;
                padding: 5px 15px;
                font-size: 16px;
                cursor: pointer;
                box-shadow: 3px 3px 0px #000;
            }
            .table-container {
                border: 1px solid #000;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #000;
                padding: 10px;
                text-align: left;
            }
            thead {
                background-color: #e0e0e0;
            }
            .status-badge {
                display: inline-block;
                padding: 5px 10px;
                border-radius: 5px;
                color: #000;
            }
            .status-completed { background-color: #c8e6c9; }
            .status-processing { background-color: #fff9c4; }
            .status-failed { background-color: #ffcdd2; }
            .status-pending { background-color: #f5f5f5; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="upload-container">
                <span>Select file/Drag and drop</span>
                <form action="/upload" method="POST" enctype="multipart/form-data" id="upload-form" class="d-inline">
                    @csrf
                    <label for="csv_file" class="upload-button">
                        Upload File
                    </label>
                    <input type="file" id="csv_file" name="csv_file" class="d-none" style="display: none;" onchange="document.getElementById('upload-form').submit()">
                </form>
            </div>

            <div class="table-container">
                <table id="uploads-table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>File Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be injected by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <script>
            function timeAgoInWords(dateString) {
                const now = new Date();
                const past = new Date(dateString);
                const seconds = Math.floor((now - past) / 1000);

                let interval = seconds / 31536000;
                if (interval > 1) {
                    return Math.floor(interval) + " years ago";
                }
                interval = seconds / 2592000;
                if (interval > 1) {
                    return Math.floor(interval) + " months ago";
                }
                interval = seconds / 86400;
                if (interval > 1) {
                    return Math.floor(interval) + " days ago";
                }
                interval = seconds / 3600;
                if (interval > 1) {
                    return Math.floor(interval) + " hours ago";
                }
                interval = seconds / 60;
                if (interval > 1) {
                    return Math.floor(interval) + " minutes ago";
                }
                return Math.floor(seconds) + " seconds ago";
            }

            function fetchUploads() {
                fetch('/uploads')
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector('#uploads-table tbody');
                        tableBody.innerHTML = '';
                        if (data.length === 0) {
                            tableBody.innerHTML = `<tr><td colspan="3" style="text-align: center; padding: 20px;">No uploads yet.</td></tr>`;
                        } else {
                            data.forEach(upload => {
                                let statusCell;
                                const status = upload.status.toLowerCase();
                                statusCell = `<span class="status-badge status-${status}">${upload.status}</span>`;

                                const uploadDate = new Date(upload.created_at);
                                const timeString = uploadDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                                const dateString = `${uploadDate.getMonth() + 1}-${uploadDate.getDate()}-${uploadDate.getFullYear().toString().substr(-2)}`;
                                const timeAgo = timeAgoInWords(upload.created_at);

                                const row = `<tr>
                                    <td>${dateString} ${timeString}<br><small>(${timeAgo})</small></td>
                                    <td>${upload.file_name}</td>
                                    <td>${statusCell}</td>
                                </tr>`;
                                tableBody.innerHTML += row;
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching uploads:', error);
                        const tableBody = document.querySelector('#uploads-table tbody');
                        tableBody.innerHTML = `<tr><td colspan="3" style="text-align: center; color: red; padding: 20px;">Error loading data.</td></tr>`;
                    });
            }

            document.addEventListener('DOMContentLoaded', () => {
                fetchUploads();
                setInterval(fetchUploads, 5000);
            });
        </script>
    </body>
</html>