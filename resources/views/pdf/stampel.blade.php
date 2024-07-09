.
<!DOCTYPE html>
<html>

<head>
    <style>
        /* CSS untuk stempel */
        .stampel {
            position: fixed;
            bottom: 10px;
            right: 10px;
            font-size: 12px;
            color: #555555;
        }
    </style>
</head>

<body>
    <div class="stampel">
        <p>Approved by: {{ Auth::user()->name }}</p>
        <p>Date: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>

</html>
