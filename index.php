<?php
// Connection 
$conn = new mysqli('localhost', 'root', '', 'rssfeed');
if($conn->connect_error){
    die('Connection failed: ' . $conn->connect_error);
}

$url = null;
$feed = null;

$rss = [];

if(isset($_POST['submit'])){
    $url = $_POST['url'];
    $description = getDescription($url);
    $title = getTitle($url);
    if($stmt = $conn->prepare("INSERT INTO posts (title, description, url) VALUES (?, ?, ?)")){
        $stmt->bind_param('sss', $title, $description, $url);
        $stmt->execute();
        $stmt->close();
    }
}

$sql = "SELECT * FROM posts";

if($result = $conn->query($sql)){
    if(mysqli_num_rows($result) > 0){
        while($row = $result->fetch_assoc()){
            $feed .= "<div class='container border border-primary p-5 mt-5'>";
            $feed .= "<h5 style='padding: 0; margin: 0;'>".$row['title']."</h5><br>";
            $feed .= "<h6 style='padding: 0; margin: 0;'>".$row['description']."</h6><br>";
            $feed .= "<h6 style='padding: 0; margin: 0;'>".$row['url']."</h6>";
            $feed .= '</div>';
        }
    }
}

function getDescription($url) {
    $tags = get_meta_tags($url);
    return @($tags['description'] ? $tags['description'] : "NULL");
}

function getTitle($url){
    if (preg_match('/<title>(.+)<\/title>/',file_get_contents($url),$matches) && isset($matches[1])){
   $title = $matches[1];
    }
    return $title ? $title : 'NULL';
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <title>RSS Feed</title>
    <style>
    .border{
        border: 2px solid blue !important;
        border-radius: 25px !important;
    }
    </style>
</head>
<body>

<div class="container pt-5">
<div class="form-group">
<form method="post">
<label for='url'>Enter URL: </label>
<input type='text' name='url' class='form-control'>
<input type='submit' class='btn btn-primary mt-3' value='Add URL' name='submit'>
</form>
</div>
</div>

<div class="container" style='padding-top: 15px'>
<div class="container">
<?php
echo $feed;
?>
</div>
</div>
    
</body>
</html>