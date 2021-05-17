<!DOCTYPE html>

<html>
<title></title>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="movieAPIstyle.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" >


</head>

<body>


<header class="container-fluid">
<div>
  <nav>
      <ul>
        <li class="navbtn"><a href="#">Home</a></li>
        <li class="navbtn"><a href="#">About</a></li>
      </ul>
  </nav>
</div>
<div class="logo-div">
  <img src="JoeIMDB.png" alt="logo" class="logo">
</div>
<form method="get">
    <div class="searchbar"><input type="text" placeholder="Search for films..." class="searchbar--input" name="film" id="film"></div>
</form>
</header>



<?php

#När värdet är på i searchbaren så körs programmet. 
if(isset($_GET['film']))
{
#Tar informationen av det som söktes och lagra det i en variabel 
$film = $_GET["film"];
#Omvandlar varje mellan slag till %20 då APIn behöver det så för att söka på filmer utan att det blir error
$film = str_replace(" ", "%20", $film);

#Lägger variabeln $film i länken för att den ska söka just den filmen
$urlSearch = "https://api.themoviedb.org/3/search/movie?api_key=d7838c293df79b60aac94cda673cc7ad&query=$film";

#Gör en funktion av cURL eftersom jag kallar den flera gånger då jag använder olika länkar
function json($url){
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  $getJSON = curl_exec($ch);

  curl_close($ch);
  return $getJSON;
}

$searchJSON = json($urlSearch);
#Konverterar JSON objekt till PHP objekt
$search = json_decode($searchJSON, true);

#Gör en funktion som tar IDn och lagrar det i en variabel med namnet $id
function get_id($JSON){
#Notera att "results" är en dictionary där en lista ligger i och i den listan finns den en till dictionary ->
#därför skrivs resutls och sen själva positionen av arrayen och sedan "id" för att få informatioen.
$id = $JSON["results"][0]["id"];
return $id;
}
$id = get_id($search);

#Använder IDn för att få mer info av den filmen 
$urlMovie = "https://api.themoviedb.org/3/movie/$id?api_key=d7838c293df79b60aac94cda673cc7ad";
$movieJSON = json($urlMovie);

#Omvandlar från JSON till PHP objekt med variabeln $movie
$movie = json_decode($movieJSON, true);

#Tar ut viss information av den omvandlade variabeln $movie för att sän lägga det på sidan
$titel = $movie["original_title"];
$overview = $movie["overview"];
$rating = $movie["vote_average"];
$rating_count = $movie["vote_count"];
$status = $movie["status"];
$quote = $movie["tagline"];

#Gör en funktion för att ta image 
function image($movie){
#Lägger ihop bas länken tillsammans med $movie["poster_path] på grund av att den ger ut bara end pointen av länken för att få filmen
$image = "http://image.tmdb.org/t/p/w500".$movie["poster_path"];
return $image;
}
$image = image($movie);

#Använder en länk för att ta videon av den filmen
$urlVideos = "https://api.themoviedb.org/3/movie/$id/videos?api_key=d7838c293df79b60aac94cda673cc7ad";
$videosJSON = json($urlVideos);
$videos = json_decode($videosJSON, true);

#Funktion som tar trailern av filmen och lägger den i en variabel
function trailer($video){
$video = "https://www.youtube.com/embed/".$video["results"][0]["key"];
return $video;
}
$movieTrailer = trailer($videos);

#Använder image API för att få en bättre bild för att använda det som bakgrunds bild
$urlImages = "https://api.themoviedb.org/3/movie/$id/images?api_key=d7838c293df79b60aac94cda673cc7ad";
$imagesJSON = json($urlImages);
$images = json_decode($imagesJSON, true);

#Gör en funktion för att få bakgrunds bilden precis samma sätt som den förra funktionen för att få bilden
function background($image){
$background_image = "http://image.tmdb.org/t/p/original".$image["backdrops"][0]["file_path"];
return $background_image;
}
$background_image = background($images);


echo "<div class='container'>";

echo "<div class='box-div'>";
echo "<div class='box-img'style='background-image: url($background_image);'></div>";
echo "<div class='container'>";
echo "<div class='image-div'>";
echo "<img class='image' src='$image' alt='poster'>";
echo "</div>";

echo "<div class='info disableBlur'>";
echo "<h1>$titel</h1>";
echo "<p>Overview: $overview</p>";
echo "<p>Rating: $rating<span class='fa fa-star checked'></span></p>";
echo "<p>Rating Count: $rating_count</p>";
echo "<p>Status: $status</p>";
echo "<p>Quote: '$quote'</p>";
echo "</div>";
echo "</div>";

echo "</div>";


echo "<div class='trailer-div'>";
echo "<iframe allow='fullscreen' class='trailer' src='$movieTrailer'></iframe>";
echo "</div>";


echo "</div>";
}
else{
  echo "<div class='title'>";
  echo "<h1>Welcome to JoeIMDB</h1>";
  echo "</div>";
}
?>
</body>
</html>
