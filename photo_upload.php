<html>
    <head>
        <title>Test</title>
    </head>
    <body>
        <form enctype="multipart/form-data" action="index.php" method="POST">
            <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
            <input name="userfile" type="file" /><br />
            <textarea name="HTTP_XML_ADDITIONAL_INFO" rows="5" cols="80"></textarea><br />
            <input type="submit" value="Upload File" />
        </form>
    </body>
</html>
