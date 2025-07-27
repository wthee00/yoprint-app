<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>YoPrint</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        <style>
            /*! modern-normalize v1.1.0 | MIT License | https://github.com/sindresorhus/modern-normalize */*,::after,::before{box-sizing:border-box}:root{-moz-tab-size:4;tab-size:4}html{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}body{font-family:system-ui,-apple-system,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji'}hr{height:0;color:inherit}abbr[title]{text-decoration:underline dotted}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace,SFMono-Regular,Consolas,'Liberation Mono',Menlo,monospace;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-size:100%;line-height:1.15;margin:0}button,select{text-transform:none}[type=button],[type=reset],[type=submit],button{-webkit-appearance:button}::-moz-focus-inner{border-style:none;padding:0}:-moz-focusring{outline:1px dotted ButtonText}:-moz-ui-invalid{box-shadow:none}legend{padding:0}progress{vertical-align:baseline}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Upload CSV</h1>
            <form action="/upload" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="csv_file" required>
                <button type="submit">Upload</button>
            </form>

            <h2>Recent Uploads</h2>
            <table id="uploads-table">
                <thead>
                    <tr>
                        <th>File Name</th>
                        <th>Upload Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <script>
            function fetchUploads() {
                fetch('/uploads')
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.querySelector('#uploads-table tbody');
                        tableBody.innerHTML = '';
                        data.forEach(upload => {
                            const row = `<tr>
                                <td>${upload.file_name}</td>
                                <td>${upload.created_at}</td>
                                <td>${upload.status}</td>
                            </tr>`;
                            tableBody.innerHTML += row;
                        });
                    });
            }

            setInterval(fetchUploads, 5000);
            fetchUploads();
        </script>
    </body>
</html>